<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Bavix\Wallet\Models\Wallet;
use App\Services\WalletService;
use App\Enums\TransactionName;
use Illuminate\Support\Facades\DB;

class TestDBTransactionController extends Controller
{
    public function PurseService(Request $request)
{
    try {
        // Debug: Log the incoming request data
        \Log::info('Request data:', $request->all());
        \Log::info('Request content type:', $request->header('Content-Type'));
        
        // Validate the incoming request data
        $validated = $request->validate([
            'user_name' => 'required|string',
            'balance' => 'required|numeric',
        ]);

        // Retrieve the user by username
        $user = User::where('user_name', $validated['user_name'])->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        // Retrieve the user's wallet
        $wallet = Wallet::where('holder_type', User::class)
            ->where('holder_id', $user->id)
            ->first();

        if (!$wallet) {
            return response()->json([
                'success' => false,
                'message' => 'Wallet not found for the user.',
            ], 404);
        }

        // Perform the deposit using the WalletService
        app(WalletService::class)->deposit($user, $validated['balance'], TransactionName::JackPot);

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Successfully deposited balance.',
            'user_name' => $user->user_name,
            'balance' => $wallet->balance, // Return the updated wallet balance
        ], 200);

    } catch (\Exception $e) {
        // Catch any errors and return a server error response
        return response()->json([
            'success' => false,
            'error' => 'An error occurred: ' . $e->getMessage(),
        ], 500);
    }
}

}
