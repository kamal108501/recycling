<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use League\OAuth2\Server\Exception\OAuthServerException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // 🧩 OAuth / Passport authentication failure
        $exceptions->renderable(function (OAuthServerException $e, $request) {
            return response()->json([
                'responseStatus'  => false,
                'responseCode'    => 401,
                'responseMessage' => $e->getMessage(),
            ], 401);
        });

        // 🧩 Authentication (no or invalid token)
        $exceptions->renderable(function (AuthenticationException|UnauthorizedHttpException $e, $request) {
            return response()->json([
                'responseStatus'  => false,
                'responseCode'    => 401,
                'responseMessage' => 'Unauthorized or invalid access token.',
            ], 401);
        });

        // 🧩 Validation errors
        $exceptions->renderable(function (ValidationException $e, $request) {
            return response()->json([
                'responseStatus'  => false,
                'responseCode'    => 422,
                'responseMessage' => 'Validation failed.',
                'errors'          => $e->errors(),
            ], 422);
        });

        // 🧩 404 errors
        $exceptions->renderable(function (NotFoundHttpException $e, $request) {
            return response()->json([
                'responseStatus'  => false,
                'responseCode'    => 404,
                'responseMessage' => 'Resource not found.',
            ], 404);
        });

        // 🧩 Fallback for all other unhandled exceptions
        $exceptions->renderable(function (Throwable $e, $request) {
            return response()->json([
                'responseStatus'  => false,
                'responseCode'    => 500,
                'responseMessage' => $e->getMessage(),
            ], 500);
        });
    })
    ->create();
