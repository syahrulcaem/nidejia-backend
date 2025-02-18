<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\Store;
use Illuminate\Http\Request;
use App\Models\Listing;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Exceptions\HttpResponseException;

class TransactionController extends Controller
{

    public function index()
    {
        $transactions = Transaction::whereUserId(auth()->id())->paginate();

        return response()->json([
            'success' => true,
            'message' => 'Transaction List',
            'data' => $transactions,
        ]);
    }
    private function _fullyBookedChecker(Store $request)
    {
        $listing = Listing::find($request->listing_id);
        $runningTransactionCount = Transaction::whereListingId($listing->id)
            ->whereNot('status', 'canceled')
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [
                    $request->start_date,
                    $request->end_date
                ])->orWhereBetween('end_date', [
                    $request->start_date,
                    $request->end_date
                ])->orWhere(function ($subQuery) use ($request) {
                    $subQuery->where('start_date', '<', $request->start_date)
                        ->where('end_date', '>', $request->end_date);
                });
            })->count();

        if ($runningTransactionCount >= $listing->max_person) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Listing Fully Booked',
                ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            );
        }

        return true;
    }

    function isAvailable(Store $request)
    {
        $this->_fullyBookedChecker($request);

        return response()->json([
            'success' => true,
            'message' => 'Listing Available',
        ]);
    }

    public function store(Store $request)
    {
        $this->_fullyBookedChecker($request);

        $transaction = Transaction::create([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'listing_id' => $request->listing_id,
            'user_id' => auth()->id(),
        ]);

        $transaction->Listing;

        return response()->json([
            'success' => true,
            'message' => 'Transaction Created',
            'data' => $transaction,
        ]);
    }

    public function Show(Transaction $transaction): JsonResponse
    {
        if ($transaction->user_id !== auth()->id()) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], JsonResponse::HTTP_UNAUTHORIZED)
            );
        }

        $transaction->Listing;

        return response()->json([
            'success' => true,
            'message' => 'Transaction Details',
            'data' => $transaction,
        ]);
    }
}
