<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests; // Sebelum Laravel 10, ada juga Bus\DispatchesJobs
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests; // Sebelum Laravel 10, ada juga DispatchesJobs

    /**
     * Contoh: Properti atau method yang bisa diakses oleh semua controller turunan.
     *
     * Misalkan Anda ingin semua controller memiliki akses mudah ke pengaturan aplikasi tertentu.
     * Namun, ini lebih baik dilakukan melalui service, helper, atau config.
     *
     * public function __construct()
     * {
     *     // Contoh: Sharing data ke semua view yang dirender oleh controller turunan
     *     // (Biasanya lebih baik dilakukan melalui View Composer jika datanya spesifik untuk view tertentu)
     *     //
     *     // $sharedData = ['appName' => config('app.name')];
     *     // view()->share('sharedData', $sharedData);
     *
     *     // Contoh: Menerapkan middleware ke semua method di controller turunan
     *     // (Ini juga bisa dilakukan per controller atau per group di routes/web.php)
     *     //
     *     // $this->middleware('auth');
     * }
     */

    /**
     * Contoh: Helper method untuk respons JSON yang konsisten.
     *
     * Jika Anda banyak membuat API, Anda bisa menambahkan helper di sini.
     *
     * protected function sendSuccessResponse($data, $message = 'Success', $statusCode = 200)
     * {
     *     return response()->json([
     *         'success' => true,
     *         'message' => $message,
     *         'data'    => $data,
     *     ], $statusCode);
     * }
     *
     * protected function sendErrorResponse($message, $errors = [], $statusCode = 400)
     * {
     *     return response()->json([
     *         'success' => false,
     *         'message' => $message,
     *         'errors'  => $errors,
     *     ], $statusCode);
     * }
     */
}