<?php

namespace App\Http\Controllers\Api\Player;

use App\Enums\TransactionName;
use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\Admin\TransferLog;
use App\Models\DepositRequest;
use App\Models\ExchangeTransactionLog;
use App\Models\User;
use App\Models\WithDrawRequest;
use App\Services\WalletService;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    use HttpResponses;

    public function index(Request $request)
    {
        $type = $request->get('type');

        [$from, $to] = match ($type) {
            'yesterday' => [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()],
            'this_week' => [now()->startOfWeek(), now()->endOfWeek()],
            'last_week' => [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()],
            default => [now()->startOfDay(), now()],
        };

        $transactions = TransferLog::with('fromUser', 'toUser')->where('from_user_id', Auth::id())->orWhere('to_user_id', Auth::id())->whereBetween('created_at', [$from, $to])
            ->orderBy('id', 'DESC')
            ->paginate();

        return $this->success(TransactionResource::collection($transactions));
    }

    // Note: MainToGame and GameToMain methods removed since main_balance column has been removed
    // Now there's only one balance column, so no need for balance transfers between columns

    public function exchangeTransactionLog()
    {
        $transactions = ExchangeTransactionLog::with('user')->where('user_id', Auth::id())
            ->orderBy('id', 'DESC')
            ->paginate();

        return $this->success($transactions);
    }

    public function depositRequestLog()
    {
        $transactions = DepositRequest::with('user')->where('user_id', Auth::id())
            ->orderBy('id', 'DESC')
            ->paginate();

        return $this->success($transactions);
    }

    public function withDrawRequestLog()
    {
        $transactions = WithDrawRequest::with('user')->where('user_id', Auth::id())
            ->orderBy('id', 'DESC')
            ->paginate();

        return $this->success($transactions);
    }
}
