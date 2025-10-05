@extends('layouts.master')

@section('title', 'Transaction Details')

@section('style')
<style>
    .transaction-type {
        font-weight: bold;
        font-size: 1.2em;
    }
    .transaction-type.deposit {
        color: #28a745;
    }
    .transaction-type.withdraw {
        color: #dc3545;
    }
    .transaction-type.transfer {
        color: #007bff;
    }
    .amount-display {
        font-size: 1.5em;
        font-weight: bold;
    }
    .amount-positive {
        color: #28a745;
    }
    .amount-negative {
        color: #dc3545;
    }
    .meta-json {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 15px;
        font-family: 'Courier New', monospace;
        font-size: 0.9em;
        white-space: pre-wrap;
        word-break: break-all;
    }
    .balance-change {
        font-size: 1.1em;
        font-weight: bold;
    }
</style>
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Transaction Details</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.logs.index') }}">System Logs</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.logs.custom-transactions') }}">Custom Transactions</a></li>
                    <li class="breadcrumb-item active">Transaction #{{ $transaction->id }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Transaction Overview -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i> Transaction Overview
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Transaction ID:</strong></td>
                                        <td>{{ $transaction->id }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>UUID:</strong></td>
                                        <td><code>{{ $transaction->uuid }}</code></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Type:</strong></td>
                                        <td>
                                            <span class="transaction-type {{ $transaction->type }}">
                                                {{ ucfirst($transaction->type) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Transaction Name:</strong></td>
                                        <td>
                                            <span class="badge badge-secondary">{{ $transaction->transaction_name }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Amount:</strong></td>
                                        <td>
                                            <span class="amount-display {{ $transaction->type === 'deposit' ? 'amount-positive' : 'amount-negative' }}">
                                                {{ $transaction->type === 'deposit' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Created At:</strong></td>
                                        <td>{{ $transaction->created_at->format('M d, Y H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Updated At:</strong></td>
                                        <td>{{ $transaction->updated_at->format('M d, Y H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Confirmed:</strong></td>
                                        <td>
                                            @if($transaction->confirmed)
                                                <span class="badge badge-success">Yes</span>
                                            @else
                                                <span class="badge badge-warning">No</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Balance Changes -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-balance-scale"></i> Balance Changes
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info">
                                        <i class="fas fa-arrow-left"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Old Balance</span>
                                        <span class="info-box-number">{{ number_format($transaction->old_balance, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning">
                                        <i class="fas fa-exchange-alt"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Amount</span>
                                        <span class="info-box-number {{ $transaction->type === 'deposit' ? 'text-success' : 'text-danger' }}">
                                            {{ $transaction->type === 'deposit' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success">
                                        <i class="fas fa-arrow-right"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">New Balance</span>
                                        <span class="info-box-number">{{ number_format($transaction->new_balance, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <span class="balance-change">
                                Balance {{ $transaction->type === 'deposit' ? 'increased' : 'decreased' }} by 
                                {{ number_format(abs($transaction->new_balance - $transaction->old_balance), 2) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Metadata -->
                @if($transaction->meta)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-tags"></i> Transaction Metadata
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="meta-json">{{ json_encode($transaction->meta, JSON_PRETTY_PRINT) }}</div>
                    </div>
                </div>
                @endif
            </div>

            <!-- User Information -->
            <div class="col-md-4">
                <!-- Primary User -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user"></i> Primary User
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($transaction->user)
                            <div class="text-center">
                                <div class="mb-3">
                                    <i class="fas fa-user-circle fa-3x text-primary"></i>
                                </div>
                                <h5>{{ $transaction->user->name }}</h5>
                                <p class="text-muted">{{ $transaction->user->user_name }}</p>
                                <span class="badge badge-primary">{{ $transaction->user->type }}</span>
                                <hr>
                                <div class="row">
                                    <div class="col-6">
                                        <strong>User ID:</strong><br>
                                        <small>{{ $transaction->user->id }}</small>
                                    </div>
                                    <div class="col-6">
                                        <strong>Current Balance:</strong><br>
                                        <small>{{ number_format($transaction->user->balance, 2) }}</small>
                                    </div>
                                </div>
                                <hr>
                                <a href="{{ route('admin.player.show', $transaction->user->id) }}" 
                                   class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye"></i> View User
                                </a>
                            </div>
                        @else
                            <div class="text-center text-muted">
                                <i class="fas fa-user-slash fa-3x mb-3"></i>
                                <p>User not found or deleted</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Target User (if applicable) -->
                @if($transaction->targetUser)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-friends"></i> Target User
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <div class="mb-3">
                                <i class="fas fa-user-circle fa-3x text-success"></i>
                            </div>
                            <h5>{{ $transaction->targetUser->name }}</h5>
                            <p class="text-muted">{{ $transaction->targetUser->user_name }}</p>
                            <span class="badge badge-success">{{ $transaction->targetUser->type }}</span>
                            <hr>
                            <div class="row">
                                <div class="col-6">
                                    <strong>User ID:</strong><br>
                                    <small>{{ $transaction->targetUser->id }}</small>
                                </div>
                                <div class="col-6">
                                    <strong>Current Balance:</strong><br>
                                    <small>{{ number_format($transaction->targetUser->balance, 2) }}</small>
                                </div>
                            </div>
                            <hr>
                            <a href="{{ route('admin.player.show', $transaction->targetUser->id) }}" 
                               class="btn btn-success btn-sm">
                                <i class="fas fa-eye"></i> View User
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-tools"></i> Quick Actions
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.logs.custom-transactions') }}" 
                               class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Transactions
                            </a>
                            @if($transaction->user)
                                <a href="{{ route('admin.logs.custom-transactions', ['user_id' => $transaction->user->id]) }}" 
                                   class="btn btn-info">
                                    <i class="fas fa-list"></i> User's Transactions
                                </a>
                            @endif
                            <button class="btn btn-warning" onclick="copyTransactionData()">
                                <i class="fas fa-copy"></i> Copy Transaction Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
<script>
function copyTransactionData() {
    const transactionData = {
        id: {{ $transaction->id }},
        uuid: '{{ $transaction->uuid }}',
        type: '{{ $transaction->type }}',
        transaction_name: '{{ $transaction->transaction_name }}',
        amount: {{ $transaction->amount }},
        old_balance: {{ $transaction->old_balance }},
        new_balance: {{ $transaction->new_balance }},
        user: {
            id: {{ $transaction->user->id ?? 'null' }},
            user_name: '{{ $transaction->user->user_name ?? 'N/A' }}',
            type: '{{ $transaction->user->type ?? 'N/A' }}'
        },
        target_user: {
            id: {{ $transaction->targetUser->id ?? 'null' }},
            user_name: '{{ $transaction->targetUser->user_name ?? 'N/A' }}',
            type: '{{ $transaction->targetUser->type ?? 'N/A' }}'
        },
        meta: @json($transaction->meta),
        created_at: '{{ $transaction->created_at }}',
        confirmed: {{ $transaction->confirmed ? 'true' : 'false' }}
    };

    navigator.clipboard.writeText(JSON.stringify(transactionData, null, 2)).then(function() {
        toastr.success('Transaction data copied to clipboard');
    }, function(err) {
        console.error('Could not copy text: ', err);
        toastr.error('Failed to copy to clipboard');
    });
}
</script>
@endsection
