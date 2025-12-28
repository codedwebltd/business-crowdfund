<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Get referrer info by referral code
Route::get('/referrer/{code}', function ($code) {
    $user = \App\Models\User::where('referral_code', $code)->first();

    if (!$user) {
        return response()->json(['error' => 'Invalid referral code'], 404);
    }

    return response()->json([
        'full_name' => $user->full_name,
        'rank' => $user->rank ?? 'Member',
    ]);
});
