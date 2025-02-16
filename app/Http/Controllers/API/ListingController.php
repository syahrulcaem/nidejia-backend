<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Models\Listing;

class ListingController extends Controller
{
    public function index(): JsonResponse
    {
        $listings = Listing::withCount('transaction')->orderBy('transaction_count', 'desc')->paginate();

        return response()->json([
            'success' => true,
            'message' => 'Listings loaded successfully',
            'data' => $listings
        ]);
    }

    public function show(Listing $listing): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Listing loaded successfully',
            'data' => $listing
        ]);
    }
}
