<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class CommonApiController extends Controller
{

    /*
     * @category WEBSITE
     * @author Original Author <ksanghavi@moba.de>
     * @author Another Author <ksanghavi@moba.de>
     * @copyright MOBA
     * @comment  END API REQUEST
     * @date 2025-04-07
     */

    public function endRequest($status, $responseCode, $responseMessage, $data = [], $request = null, $startTime)
    {
        $response['responseStatus'] = $status;
        $response['responseCode'] = $responseCode;
        $response['responseMessage'] = $responseMessage;

        if (!empty($data))
            $response['data'] = $data;

        $executionTime = microtime(true) - $startTime;

        DB::table('api_logs')->insert([
            'api_url' => $request->url(),
            'project' => config('app.name'),
            'method' => $request->method(),
            'request_params' => json_encode($request->all()),
            'response' => json_encode($response),
            'execution_time' => number_format($executionTime, 2),
            'created_by' => $request->userid ?? 0,
            'created_at' => currentDT(),
        ]);

        echo json_encode($response);
        exit;
    }

    /*
     * @category WEBSITE
     * @author Original Author <ksanghavi@moba.de>
     * @author Another Author <ksanghavi@moba.de>
     * @copyright MOBA
     * @comment  CHECK JSON REQUEST
     * @date 2025-04-07
     */

    public function isJsonRequest($request)
    {
        if (!$request->isJson()) {
            //NOT VALID INPUT
            $response['status'] = false;
            $response['responseCode'] = 205;
            $response['responseMessage'] = "Request JSON is not valid.";
            echo json_encode($response);
            die;
        }
    }

    /*
     * @category WEBSITE
     * @author Original Author <ksanghavi@moba.de>
     * @author Another Author <ksanghavi@moba.de>
     * @copyright MOBA
     * @comment  CHECK REQUEST VALIDATION
     * @date 2025-04-07
     */

    public function checkValidation($validator, $request)
    {
        if ($validator->fails()) {
            $startTime = microtime(true);
            $message = $validator->errors()->first();
            $this->endRequest(false, 400, $message, array(), $request, $startTime);
        }
    }

    /*
     * @category WEBSITE
     * @author Original Author <ksanghavi@moba.de>
     * @author Another Author <ksanghavi@moba.de>
     * @copyright MOBA
     * @comment  CHECK USER IS VALIDATE OR NOT
     * @date 2025-04-07
     */

    public function checkvalidateUser($userid, $request)
    {
        $user = auth()->user();

        if ($userid != $user->user_id) {
            $startTime = microtime(true);
            $message = 'No user found.';
            $this->endRequest(false, 205, $message, array(), $request, $startTime);
        }
    }

    /*
     * @category WEBSITE
     * @author Original Author <ksanghavi@moba.de>
     * @author Another Author <ksanghavi@moba.de>
     * @copyright MOBA
     * @comment  CREATE FULL PATH FOR IMAGES
     * @date 2025-04-07
     */

    public static function createFullImagePath($folder_path, $image_name)
    {
        $img_path = '';
        if ($image_name == '')
            return $img_path;

        $siteURL = url('/');
        $filePath = base_path() . '/public/' . $folder_path . '/' . $image_name;

        if (File::exists($filePath)) {
            $img_path = $siteURL . '/' . $folder_path . '/' . $image_name;
        }

        return $img_path;
    }

    /*
     * @category WEBSITE
     * @author Original Author <ksanghavi@moba.de>
     * @author Another Author <ksanghavi@moba.de>
     * @copyright MOBA
     * @comment  CREATE IMAGE FROM BASE64IMAGE
     * @date 2025-04-07
     */

    public function storeBase64Image($base64Image, $folderName = 'logsheets')
    {
        if (strlen($base64Image) < 1)
            return '';

        $imageData = base64_decode($base64Image);
        $size = getImageSizeFromString($imageData);
        $extension = substr($size['mime'], 6);

        $filename = uniqid() . '.' . $extension; // You can choose a different extension if needed

        $folderPath = public_path('share/' . $folderName);

        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0777, true);
        }

        $filePath = $folderPath . '/' . $filename;
        file_put_contents($filePath, $imageData);

        return $filename; // Return the filename if needed for further processing
    }

    /*
     * @category WEBSITE
     * @author Original Author <ksanghavi@moba.de>
     * @author Another Author <ksanghavi@moba.de>
     * @copyright MOBA
     * @comment  INSERT IMAGES TO IMAGE MASTER
     * @date 2025-04-07
     */

    public function insertImagesToMaster($updatedId, $image, $user_id)
    {
        $findRecord = DB::table('image_master')
            ->where('master_tbl_id', $updatedId)
            ->where('is_active', 1)
            ->whereNull('deleted_date')
            ->first();

        $imageData = [
            'master_tbl_id' => $updatedId,
            'image' => (strlen($image) > 0) ? $image : (isset($findRecord) ? $findRecord->image : null),
            'created_by' => $user_id,
            'created_date' => currentDT(),
            'updated_by' => $user_id,
            'updated_date' => currentDT(),
        ];

        $values = array(
            'is_active' => 0,
            'deleted_by' => $user_id,
            'deleted_date' => currentDT(),
        );

        DB::table('image_master')
            ->where('master_tbl_id', $updatedId)
            ->update($values);

        DB::table('image_master')->insert($imageData);
    }

    /*
     * @category WEBSITE
     * @author Original Author <ksanghavi@moba.de>
     * @author Another Author <ksanghavi@moba.de>
     * @copyright MOBA
     * @comment  GET ALL EQUIPMENTS RECORDS
     * @date 2025-04-07
     */

    public static function getUserSiteIds($offset = 0, $limit = 1000, $user_id = '')
    {

        $query = DB::table('site_master as s');
        $query->rightJoin('users_site as us', 's.id', '=', 'us.site_id');
        $query->select('s.id');
        $query->where('us.userid', '=', $user_id);
        $query->orderBy('s.site_name');
        $query->skip($offset)->take($limit);
        $sites = $query->pluck('id')->toArray();

        return $sites;
    }

    /*
     * @category WEBSITE
     * @author Original Author <ksanghavi@moba.de>
     * @author Another Author <ksanghavi@moba.de>
     * @copyright MOBA
     * @comment  GET ALL COMPANIES RECORDS
     * @date 2025-04-10
     */

    public static function getCompanies($company_ids = array())
    {
        $results = DB::table('company_master as cm')
            ->select('cm.id', 'cm.company_name')
            ->where('cm.status', '=', '1')
            ->whereNull('cm.del_date')
            ->whereIn('cm.id', $company_ids)
            ->orderBy('cm.company_name')
            ->get();

        return $results;
    }

    /*
     * @category WEBSITE
     * @author Original Author <ksanghavi@moba.de>
     * @author Another Author <ksanghavi@moba.de>
     * @copyright MOBA
     * @comment  GET ALL SITES RECORDS
     * @date 2025-04-10
     */

    public static function getSites($company_ids = array(), $site_ids = array())
    {
        $results = DB::table('site_master as sm')
            ->rightJoin('company_master as cm', 'sm.company_id', '=', 'cm.company_id')
            ->select('sm.site_id', 'sm.site_name', 'cm.company_id', 'cm.company_name')
            ->where('sm.is_active', '=', '1')
            ->whereNull('sm.deleted_at')
            ->whereIn('cm.company_id', $company_ids)
            ->whereIn('sm.site_id', $site_ids)
            ->get();

        return $results;
    }

    /*
     * @category WEBSITE
     * @author Original Author <ksanghavi@moba.de>
     * @author Another Author <ksanghavi@moba.de>
     * @copyright MOBA
     * @comment  GET ALL SHIFTS RECORDS
     * @date 2025-04-10
     */

    public static function getShifts($company_ids = array())
    {
        $results = DB::table('shift_master as shm')
            ->leftJoin('company_master as cm', 'cm.company_id', '=', 'shm.company_id')
            ->select('shm.shift_id', 'shm.shift_name', 'shm.start_time', 'shm.end_time')
            ->where('shm.is_active', '=', '1')
            ->where('cm.is_active', '=', '1')
            ->whereNull('shm.deleted_at')
            ->orderBy('shm.shift_name')
            ->whereIn('cm.company_id', $company_ids)
            ->get();

        return $results;
    }

    /*
     * @category WEBSITE
     * @author Original Author <ksanghavi@moba.de>
     * @author Another Author <ksanghavi@moba.de>
     * @copyright MOBA
     * @comment  GET ALL EQUIPMENT TYPES RECORDS
     * @date 2025-04-10
     */

    public static function getEquipmentTypes($company_ids = array())
    {
        $equ_type_ids = DB::table('equ_master as em')
            ->distinct()
            ->leftJoin('site_master as sm', 'sm.id', '=', 'em.site_id')
            ->whereIn('sm.company_id', $company_ids)
            ->pluck('em.equ_type')
            ->toArray();

        $results = DB::table('equ_type_master as etm')
            ->select('etm.id', 'etm.equ_type', 'etm.icon')
            ->where('etm.status', '=', '1')
            ->whereIn('etm.id', $equ_type_ids)
            ->orderBy('etm.equ_type')
            ->get();

        return $results;
    }

    /*
     * @category WEBSITE
     * @author Original Author <ksanghavi@moba.de>
     * @author Another Author <ksanghavi@moba.de>
     * @copyright MOBA
     * @comment  GET ALL OBJECT MEASURING POINTS DATA
     * @date 2025-05-28
     */

    public static function getObjMeasuingPts($company_ids = array())
    {
        $results = DB::table('obj_measuring_points as omp')
            ->leftJoin('equ_type_master as etm', 'etm.id', '=', 'omp.equ_type')
            ->select(
                'omp.point_id',
                'etm.id as equ_type_id',
                'etm.equ_type',
                'omp.measuring_position',
                'omp.measuring_units',
            )
            ->where('omp.is_active', '=', '1')
            ->where('etm.is_active', '=', '1')
            ->whereNull('omp.deleted_date')
            ->whereIn('etm.company_id', $company_ids)
            ->orderBy('etm.equ_type')
            ->get();

        return $results;
    }

    /*
     * @category WEBSITE
     * @author Original Author <ksanghavi@moba.de>
     * @author Another Author <ksanghavi@moba.de>
     * @copyright MOBA
     * @comment  GET ALL EQUIPMENTS RECORDS
     * @date 2025-04-10
     */

    public static function getEquipments($site_ids = array())
    {
        $results = DB::table('equ_master as em')
            ->leftJoin('site_master as sm', 'sm.id', '=', 'em.site_id')
            ->leftJoin('equ_type_master as etm', 'etm.id', '=', 'em.equ_type')
            ->select(
                'em.id',
                'em.unitid',
                'em.site_id',
                'sm.site_name',
                'em.equipment',
                'em.number_plate',
                'em.equ_type as equ_type_id',
                'etm.equ_type',
                'etm.icon',
                'em.fdms_equ_desc',
                'em.perform_group',
            )
            ->where('em.status', '=', '1')
            ->whereIn('sm.id', $site_ids)
            ->orderBy('em.equipment')
            ->get();

        return $results;
    }

    /*
     * @category WEBSITE
     * @author Original Author <ksanghavi@moba.de>
     * @author Another Author <ksanghavi@moba.de>
     * @copyright MOBA
     * @comment  GET ALL DEPARTMENTS RECORDS
     * @date 2025-04-10
     */

    public static function getDepartments($company_ids = array())
    {
        $results = DB::table('dept_master as dm')
            ->leftJoin('company_master as cm', 'cm.company_id', '=', 'dm.company_id')
            ->select('dm.dept_id', 'dm.dept_name')
            ->where('cm.is_active', '=', '1')
            ->where('dm.is_active', '=', '1')
            ->whereNull('dm.deleted_at')
            ->whereIn('cm.company_id', $company_ids)
            ->orderBy('dm.dept_name')
            ->get();

        return $results;
    }

    /*
     * @category WEBSITE
     * @author Original Author <ksanghavi@moba.de>
     * @author Another Author <ksanghavi@moba.de>
     * @copyright MOBA
     * @comment  GET ALL ACTIVITIES RECORDS
     * @date 2025-04-10
     */

    public static function getActivities($site_ids = array())
    {
        $results = DB::table('activity_master as am')
            ->leftJoin('site_master as sm', 'sm.site_id', '=', 'am.site_id')
            ->leftJoin('equ_type_master as etm', 'etm.id', '=', 'am.equ_type')
            ->select(
                'am.activity_id',
                'am.activity_name',
                'sm.site_id',
                'sm.site_name',
                DB::raw("GROUP_CONCAT(etm.id) as equ_type_ids"),
                DB::raw("GROUP_CONCAT(etm.equ_type) as equ_types"),
                'am.uom'
            )
            ->where('am.is_active', '=', '1')
            ->where('sm.is_company_active', '=', '1')
            ->whereNull('am.deleted_at')
            ->whereIn('sm.site_id', $site_ids)
            ->groupBy('am.activity_name', 'sm.site_id', 'sm.site_name')
            ->orderBy('am.activity_name')
            ->get();


        return $results;
    }

    /*
     * @category WEBSITE
     * @author Original Author <ksanghavi@moba.de>
     * @author Another Author <ksanghavi@moba.de>
     * @copyright MOBA
     * @comment  GET ALL CHAINAGES RECORDS
     * @date 2025-04-10
     */

    public static function getChainages($site_ids = array())
    {
        $results = DB::table('chainage_master as chm')
            ->leftJoin('site_master as sm', 'sm.site_id', '=', 'chm.site_id')
            ->select('chm.chainage_id', 'chm.chainage_name', 'sm.site_id', 'sm.site_name')
            ->where('chm.is_active', '=', '1')
            ->where('sm.is_company_active', '=', '1')
            ->whereNull('chm.deleted_at')
            ->whereIn('sm.site_id', $site_ids)
            ->orderBy('chm.chainage_name')
            ->get();

        return $results;
    }


    /*
     * @category WEBSITE
     * @author Original Author <ksanghavi@moba.de>
     * @author Another Author <ksanghavi@moba.de>
     * @copyright MOBA
     * @comment  GET ALL OPERATORS RECORDS
     * @date 2025-04-21
     */

    public static function getOperators($site_ids = '')
    {
        $results = DB::table('site_employees as se')
            ->select('se.id', 'se.emp_code', 'se.full_name', 'se.usermobile')
            ->whereIn('se.site_id', $site_ids)
            ->where('status', 1)
            ->where('se.usermobile', '!=', '')
            ->orderBy('se.full_name')
            ->get();

        return $results;
    }

    /*
     * @category WEBSITE
     * @author Original Author <ksanghavi@moba.de>
     * @author Another Author <ksanghavi@moba.de>
     * @copyright MOBA
     * @comment  GET ALL OPERATORS RECORDS
     * @date 2025-04-21
     */

    public static function getLocations($site_ids = '')
    {
        $results = DB::table('trip_locations as tl')
            ->select('tl.location_id', 'tl.location_name', 'tl.site_id')
            ->whereIn('tl.site_id', $site_ids)
            ->where('tl.is_active', 1)
            ->whereNull('tl.deleted_at')
            ->orderBy('tl.location_name')
            ->get();

        return $results;
    }

    /*
     * @category WEBSITE
     * @author Original Author <ksanghavi@moba.de>
     * @author Another Author <ksanghavi@moba.de>
     * @copyright MOBA
     * @comment  GET ALL USED FOR MASTER RECORDS
     * @date 2025-04-24
     */

    public static function getUsedForData()
    {
        $results = DB::table('used_for_master')
            ->select('ufm_id', 'ufm_name')
            ->where('is_active', '=', '1')
            ->whereNull('deleted_at')
            ->orderBy('ufm_name')
            ->get();

        return $results;
    }

    /*
     * @category WEBSITE
     * @author Original Author <ksanghavi@moba.de>
     * @author Another Author <ksanghavi@moba.de>
     * @copyright MOBA
     * @comment  GET ALL SITES RECORDS
     * @date 2021-01-01
     */

    public static function getUserSites($user_id = '', $site_ids = [])
    {

        $query = DB::table('site_master as sm');
        $query->leftJoin('users_site as us', 'sm.id', '=', 'us.site_id');
        $query->select('sm.id', 'sm.site_name', 'sm.company_id', 'sm.site_code');
        $query->where('us.userid', '=', $user_id);
        if (!empty($site_ids))
            $query->whereIn('sm.id', $site_ids);
        $query->where('sm.status', '=', '1');
        $query->whereNull('sm.del_date');
        $query->orderBy('sm.site_name');
        $sites = $query->get();

        return $sites;
    }
}
