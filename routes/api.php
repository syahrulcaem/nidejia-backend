<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ListingController; // Ensure this class exists in the specified namespace
use App\Http\Controllers\API\TransactionController; // Ensure this class exists in the specified namespace

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return response()->json([
        'success' => true,
        'meqssage' => 'User authenticated',
        'data' => $request->user(),
    ]);
});

Route::resource('listing', ListingController::class)->only(['index', 'show']); // Ensure the ListingController class exists in the specified namespace
Route::post('transaction/is-available', [TransactionController::class, 'isAvailable'])->middleware(['auth:sanctum']);
require __DIR__ . '/auth.php';
