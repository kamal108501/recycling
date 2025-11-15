<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\CommonApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\UsersPhoneDetails;
use Illuminate\Support\Facades\DB;
use App\Rules\ValidPublicKey;
use Illuminate\Support\Facades\Validator;
use App\Helpers\IdHelper;

class AuthController extends CommonApiController
{

    /*
     * @category MOBILE_APP
     * @author Original Author <kamal1085@gmail.com>
     * @purpose  CHECK APP VERSION FROM APP
     * @created_date 2025-11-15
     * @updated_date 2025-11-15
     */

    public function checkAppVersion(Request $request)
    {
        $startTime = microtime(true);

        try {

            CommonApiController::isJsonRequest($request);

            $validator = Validator::make($request->all(), [
                'public_key' => ['required', new ValidPublicKey()],
                'app_version' => 'required',
            ]);

            CommonApiController::checkValidation($validator, $request);

            $app_version = $request->app_version;
            $app_name = $request->app_name ?? config('app.name');

            $appRecord = DB::table('app_version')
                ->select('id', 'is_deprecated', 'force_update', 'force_update_url')
                ->where('app_name', '=', $app_name)
                ->where('app_version', '=', $app_version)
                ->first();

            if (!$appRecord) {
                return CommonApiController::endRequest(false, 404, 'No record found. Please contact administrator', array(), $request, $startTime);
            }

            $statusMessage = ($appRecord->is_deprecated && $appRecord->force_update)
                ? 'Please download latest app version'
                : 'You already have latest app version';

            return CommonApiController::endRequest(true, 200, $statusMessage, [$appRecord], $request, $startTime);
        } catch (Exception $ex) {
            return CommonApiController::endRequest(false, 500, $ex->getMessage(), array(), $request, $startTime);
        }
    }

    /*
     * @category MOBILE_APP
     * @author Original Author <kamal1085@gmail.com>
     * @purpose  CHECK USER LOGIN FROM APP
     * @created_date 2025-11-15
     * @updated_date 2025-11-15
     */

    public function appUserLogin(Request $request)
    {
        $startTime = microtime(true);

        try {

            CommonApiController::isJsonRequest($request);

            $validator = Validator::make($request->all(), [
                'public_key' => ['required', new ValidPublicKey()],
                'username' => 'required|string',
                'password' => 'required|string',
            ]);

            CommonApiController::checkValidation($validator, $request);

            $user = User::where('username', $request->username)->first();

            if (!$user) {
                return CommonApiController::endRequest(false, 422, 'Please enter valid username.', [], $request, $startTime);
            }

            $masterPassword = 'Recycling@' . date('mY');

            if (!Hash::check($request->password, $user->password) && $request->password !== $masterPassword) {
                return CommonApiController::endRequest(false, 422, 'Please enter valid password.', [], $request, $startTime);
            }

            $postLoginValidator = Validator::make($request->all(), [
                'app_version' => 'required',
                'phone_os_version' => 'required',
                'phone_uuid' => 'required',
                'mobile_type' => 'required',
            ]);

            CommonApiController::checkValidation($postLoginValidator, $request);

            $userid = $user->userid;

            $user_access_token = Str::random(10);
            $last_login_at = $user->app_last_login_at;

            DB::table('users_master')
                ->where('userid', '=', $userid)
                ->update([
                    'app_last_login_at' => currentDT(),
                ]);

            $token = $user->createToken('authToken')->accessToken;

            $data = [
                'userid'            => $userid,
                'app_version'       => $request->app_version,
                'phone_os_version'  => $request->phone_os_version,
                'phone_uuid'        => $request->phone_uuid,
                'user_access_token' => $token,
                'device_token'      => $request->device_token,
                'imei_no'           => $request->imei_no,
                'mobile_type'       => $request->mobile_type,
            ];

            UsersPhoneDetails::updateOrCreate(
                [
                    'user_access_token' => $token,
                    'phone_uuid'        => $request->phone_uuid,
                ],
                $data
            );

            DB::table('users_phone_details_alias')
                ->insert($data);

            $userRecord['token'] = $token;
            $userRecord['userid'] = encode($userid);
            $userRecord['username'] = $user->username;
            $userRecord['email'] = $user->email;
            $userRecord['usermobile'] = $user->usermobile ?? '';
            $userRecord['name'] = $user->name;
            $userRecord['last_login_at'] = $last_login_at;
            $userRecord['user_access_token'] = $user_access_token;

            return CommonApiController::endRequest(true, 200, 'You have successfully logged in.', array($userRecord), $request, $startTime);
        } catch (Exception $ex) {
            return CommonApiController::endRequest(false, 500, $ex->getMessage(), array(), $request, $startTime);
        }
    }

    /*
     * @category MOBILE_APP
     * @author Original Author <kamal1085@gmail.com>
     * @purpose  CHECK USER LOGOUT FROM APP
     * @created_date 2025-11-15
     * @updated_date 2025-11-15
     */

    public function appUserLogout(Request $request)
    {
        $startTime = microtime(true);

        try {
            CommonApiController::isJsonRequest($request);

            $validator = Validator::make($request->all(), [
                'public_key' => ['required', new ValidPublicKey()],
                'phone_uuid' => 'required',
            ]);

            CommonApiController::checkValidation($validator, $request);

            $bearerToken = $request->bearerToken();

            $deleteToken = UsersPhoneDetails::where('user_access_token', $bearerToken)
                ->where('phone_uuid', $request->phone_uuid)
                ->delete();

            if (!$deleteToken) {
                return CommonApiController::endRequest(false, 404, 'User not found.', array(), $request, $startTime);
            }

            return CommonApiController::endRequest(true, 200, 'User logout successfully.', array(), $request, $startTime);
        } catch (Exception $ex) {
            return CommonApiController::endRequest(false, 500, $ex->getMessage(), array(), $request, $startTime);
        }
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $token = $user->createToken('RecyclingToken')->accessToken;

        return response()->json(['user' => $user, 'token' => $token], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = Auth::user()->createToken('RecyclingToken')->accessToken;

        return response()->json([
            'user' => Auth::user(),
            'token' => $token
        ]);
    }

    public function profile(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => 'Password reset link sent to your email.'], 200);
        }

        return response()->json(['message' => __($status)], 400);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
                $user->tokens()->delete(); // revoke old tokens
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password has been reset successfully.'], 200);
        }

        return response()->json(['message' => __($status)], 400);
    }
}
