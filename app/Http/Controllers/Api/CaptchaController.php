<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CaptchaService;
use Illuminate\Http\JsonResponse;

class CaptchaController extends Controller
{
    public function __invoke(CaptchaService $captcha): JsonResponse
    {
        return response()->json($captcha->buat())->header('Cache-Control', 'no-store');
    }
}
