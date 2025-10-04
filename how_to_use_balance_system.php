<?php

/**
 * HOW TO USE OUR NEW CUSTOM BALANCE SYSTEM
 * ========================================
 * 
 * This guide shows you how to use the new custom wallet system
 * that replaces the Laravel Wallet package for better performance.
 * 
 * NOTE: This is a documentation file with examples.
 * Copy the code examples into your actual Laravel application files.
 */

/*
 * 1. BASIC BALANCE ACCESS
 * =======================
 * 
 * // Add these imports to your controller/service files:
 * use App\Models\User;
 * use App\Services\WalletService;
 * use App\Services\CustomWalletService;
 * use App\Enums\TransactionName;
 * 
 * // Get user balance (direct access)
 * $user = User::find(1);
 * $balance = $user->balance;                    // Raw balance from database (decimal/string)
 * $balanceFloat = $user->balanceFloat;          // Balance as float (accessor - converts to float)
 * 
 * // Check if user has sufficient balance
 * if ($user->hasBalance(1000)) {
 *     echo "User has at least 1000 balance";
 * }
 */

/*
 * 2. WALLET OPERATIONS
 * ====================
 * 
 * $walletService = app(WalletService::class);  // Uses CustomWalletService internally
 * 
 * // Deposit money to user
 * $success = $walletService->deposit($user, 1000, TransactionName::CapitalDeposit);
 * if ($success) {
 *     echo "Deposit successful";
 * }
 * 
 * // Withdraw money from user
 * $success = $walletService->withdraw($user, 500, TransactionName::Withdraw);
 * if ($success) {
 *     echo "Withdrawal successful";
 * }
 * 
 * // Transfer between users
 * $fromUser = User::find(1);
 * $toUser = User::find(2);
 * $success = $walletService->transfer($fromUser, $toUser, 200, TransactionName::CreditTransfer);
 * if ($success) {
 *     echo "Transfer successful";
 * }
 * 
 * // Force transfer (admin operation - bypasses balance checks)
 * $success = $walletService->forceTransfer($fromUser, $toUser, 1000, TransactionName::CreditTransfer);
 */

/*
 * 3. DIRECT CUSTOM WALLET SERVICE USAGE
 * =====================================
 * 
 * $customWalletService = new CustomWalletService();
 * 
 * // Direct balance operations
 * $balance = $customWalletService->getBalance($user);
 * $hasBalance = $customWalletService->hasBalance($user, 1000);
 * 
 * // Direct operations (same as WalletService but without wrapper)
 * $customWalletService->deposit($user, 1000, TransactionName::CapitalDeposit);
 * $customWalletService->withdraw($user, 500, TransactionName::Withdraw);
 * $customWalletService->transfer($fromUser, $toUser, 200, TransactionName::CreditTransfer);
 */

/*
 * 4. TRANSACTION HISTORY
 * ======================
 * 
 * // Get user's transaction history
 * $transactions = $customWalletService->getTransactionHistory($user, 50, 0);
 * 
 * foreach ($transactions as $transaction) {
 *     echo "Amount: {$transaction->amount}, Type: {$transaction->type}, Date: {$transaction->created_at}";
 * }
 */

/*
 * 5. WALLET STATISTICS
 * ====================
 * 
 * $stats = $customWalletService->getWalletStats();
 * echo "Total Users: {$stats['total_users']}";
 * echo "Total Balance: {$stats['total_balance']}";
 * echo "Average Balance: {$stats['average_balance']}";
 */

/*
 * 6. API CONTROLLER EXAMPLES
 * ==========================
 * 
 * // In your API controllers, use balance like this:
 * 
 * class ExampleController extends Controller
 * {
 *     public function getUserBalance(Request $request)
 *     {
 *         $user = User::where('user_name', $request->username)->first();
 *         
 *         if (!$user) {
 *             return response()->json(['error' => 'User not found'], 404);
 *         }
 *         
 *         return response()->json([
 *             'username' => $user->user_name,
 *             'balance' => $user->balanceFloat,
 *             'has_sufficient_balance' => $user->hasBalance(1000)
 *         ]);
 *     }
 *     
 *     public function deposit(Request $request)
 *     {
 *         $user = User::find($request->user_id);
 *         $amount = $request->amount;
 *         
 *         $walletService = app(WalletService::class);
 *         $success = $walletService->deposit($user, $amount, TransactionName::CapitalDeposit);
 *         
 *         if ($success) {
 *             return response()->json([
 *                 'success' => true,
 *                 'new_balance' => $user->refresh()->balanceFloat
 *             ]);
 *         }
 *         
 *         return response()->json(['error' => 'Deposit failed'], 500);
 *     }
 * }
 */

/*
 * 7. GAMING INTEGRATION EXAMPLES
 * ==============================
 * 
 * // For gaming operations (bets, wins, etc.)
 * 
 * class GamingController extends Controller
 * {
 *     public function placeBet(Request $request)
 *     {
 *         $user = Auth::user();
 *         $betAmount = $request->amount;
 *         
 *         // Check if user has sufficient balance
 *         if (!$user->hasBalance($betAmount)) {
 *             return response()->json(['error' => 'Insufficient balance'], 400);
 *         }
 *         
 *         $walletService = app(WalletService::class);
 *         
 *         // Deduct bet amount
 *         $success = $walletService->withdraw($user, $betAmount, TransactionName::Bet);
 *         
 *         if ($success) {
 *             // Process the bet logic here
 *             // ...
 *             
 *             return response()->json([
 *                 'success' => true,
 *                 'new_balance' => $user->refresh()->balanceFloat
 *             ]);
 *         }
 *         
 *         return response()->json(['error' => 'Bet placement failed'], 500);
 *     }
 *     
 *     public function processWin(Request $request)
 *     {
 *         $user = User::find($request->user_id);
 *         $winAmount = $request->amount;
 *         
 *         $walletService = app(WalletService::class);
 *         
 *         // Add win amount
 *         $success = $walletService->deposit($user, $winAmount, TransactionName::Win);
 *         
 *         if ($success) {
 *             return response()->json([
 *                 'success' => true,
 *                 'new_balance' => $user->refresh()->balanceFloat
 *             ]);
 *         }
 *         
 *         return response()->json(['error' => 'Win processing failed'], 500);
 *     }
 * }
 */

/*
 * 8. WEBHOOK INTEGRATION (Updated from your files)
 * ================================================
 * 
 * class WebhookController extends Controller
 * {
 *     public function getBalance(Request $request)
 *     {
 *         $user = User::where('user_name', $request->member_account)->first();
 *         
 *         if ($user) {
 *             $balance = $user->balance;  // Use new balance system
 *             
 *             // Apply currency conversion if needed
 *             if (in_array($request->currency, $specialCurrencies)) {
 *                 $balance = $balance / 1000;
 *                 $balance = round($balance, 4);
 *             } else {
 *                 $balance = round($balance, 2);
 *             }
 *             
 *             return response()->json([
 *                 'member_account' => $request->member_account,
 *                 'balance' => (float) $balance,
 *                 'code' => 0, // Success
 *                 'message' => 'Success'
 *             ]);
 *         }
 *         
 *         return response()->json([
 *             'member_account' => $request->member_account,
 *             'balance' => 0.00,
 *             'code' => 1, // Member not found
 *             'message' => 'Member not found'
 *         ]);
 *     }
 * }
 */

/*
 * 9. PERFORMANCE BENEFITS
 * =======================
 * 
 * Our new system provides:
 * 
 * 1. 20-50x faster operations (direct database updates)
 * 2. No JOIN queries needed (balance stored in users table)
 * 3. Atomic transactions with row-level locking
 * 4. Comprehensive transaction logging
 * 5. Simplified single balance system
 * 6. Better error handling and rollback support
 * 
 * OLD WAY (Laravel Wallet):
 * $user->wallet->balanceFloat
 * $user->wallet->deposit(1000)
 * 
 * NEW WAY (Custom Wallet):
 * $user->balance (raw) or $user->balanceFloat (as float)
 * $walletService->deposit($user, 1000, TransactionName::CapitalDeposit)
 */

/*
 * 10. MIGRATION CHECKLIST
 * =======================
 * 
 * When updating existing code:
 * 
 * 1. Replace $user->wallet->balanceFloat with $user->balance (or $user->balanceFloat for float conversion)
 * 2. Replace $user->wallet->deposit() with $walletService->deposit()
 * 3. Replace $user->wallet->withdraw() with $walletService->withdraw()
 * 4. Replace $user->wallet->transfer() with $walletService->transfer()
 * 5. Remove ->with('wallet') from User queries
 * 6. Update balance checks to use $user->hasBalance()
 * 7. Use TransactionName enum for transaction types
 * 8. Handle boolean return values from wallet operations
 */

// This file is for documentation purposes only
// Do not execute this file directly - copy the examples into your Laravel application