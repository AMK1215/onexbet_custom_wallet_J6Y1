<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\WithdrawResource;
use App\Models\WithDrawRequest;
use App\Services\CustomWalletService;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class WithDrawRequestController extends Controller
{
    use HttpResponses;

    public function FinicalWithdraw(Request $request)
    {
        $request->validate([
            'account_name' => ['required', 'string'],
            'amount' => ['required', 'integer', 'min: 10000'],
            'account_number' => ['required', 'regex:/^[0-9]+$/'],
            'payment_type_id' => ['required', 'integer'],
            'password' => ['required']
        ]);

        $player = Auth::user();
        
        // Use custom wallet system for balance checking
        if ($request->amount > $player->balance) {
            return $this->error('', 'Insufficient Balance!', 401);
        }

        // Verify password
        if (!Hash::check($request->password, $player->password)) {
            return $this->error('', 'Your password is wrong!', 401);
        }

        try {
            $withdraw = WithDrawRequest::create([
                'user_id' => $player->id,
                'agent_id' => $player->agent_id,
                'amount' => $request->amount,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
                'payment_type_id' => $request->payment_type_id,
            ]);

            // Log the withdrawal request for audit purposes
            Log::info('Withdrawal request created', [
                'user_id' => $player->id,
                'amount' => $request->amount,
                'withdraw_id' => $withdraw->id,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
                'payment_type_id' => $request->payment_type_id,
            ]);

            return $this->success($withdraw, 'Withdraw Request Success');
        } catch (Exception $e) {
            Log::error('Withdrawal request failed', [
                'user_id' => $player->id,
                'amount' => $request->amount,
                'error' => $e->getMessage()
            ]);
            
            return $this->error('', 'Withdrawal request failed. Please try again.', 500);
        }
    }

    public function log()
    {
        $withdraw = WithDrawRequest::where('user_id', Auth::id())->get();

        return $this->success(WithdrawResource::collection($withdraw));
    }

    public function withdrawTest(Request $request)
    {
        $request->validate([
            'account_name' => ['required', 'string'],
            'amount' => ['required', 'integer', 'min: 10000'],
            'account_number' => ['required', 'regex:/^[0-9]+$/'],
            'payment_type_id' => ['required', 'integer'],
            'password' => ['required']
        ]);

        $player = Auth::user();
        
        // Use custom wallet system for balance checking
        if ($request->amount > $player->balance) {
            return $this->error('', 'Insufficient Balance', 401);
        }
        
        // Verify password
        if (!Hash::check($request->password, $player->password)) {
            return $this->error('', 'လျို့ဝှက်နံပါတ်ကိုက်ညီမှု မရှိပါ။', 401);
        }

        try {
            $withdraw = WithDrawRequest::create([
                'user_id' => $player->id,
                'agent_id' => $player->agent_id,
                'amount' => $request->amount,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
                'payment_type_id' => $request->payment_type_id,
            ]);

            // Log the test withdrawal request
            Log::info('Test withdrawal request created', [
                'user_id' => $player->id,
                'amount' => $request->amount,
                'withdraw_id' => $withdraw->id,
            ]);

            return $this->success($withdraw, 'Withdraw Request Success');
        } catch (Exception $e) {
            Log::error('Test withdrawal request failed', [
                'user_id' => $player->id,
                'amount' => $request->amount,
                'error' => $e->getMessage()
            ]);
            
            return $this->error('', 'Test withdrawal request failed. Please try again.', 500);
        }
    }
}
