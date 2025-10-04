@extends('layouts.master')

@section('title', 'Custom Wallet Transactions')

@section('style')
<style>
    .transaction-type {
        font-weight: bold;
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
    .amount-positive {
        color: #28a745;
        font-weight: bold;
    }
    .amount-negative {
        color: #dc3545;
        font-weight: bold;
    }
    .filter-card {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
    }
    .meta-data {
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Custom Wallet Transactions</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.logs.index') }}">System Logs</a></li>
                    <li class="breadcrumb-item active">Custom Transactions</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Filters -->
        <div class="card filter-card mb-4">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter"></i> Filters
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.logs.custom-transactions') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>User</label>
                                <select name="user_id" class="form-control select2">
                                    <option value="">All Users</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->user_name }} ({{ $user->type }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Type</label>
                                <select name="type" class="form-control">
                                    <option value="">All Types</option>
                                    @foreach($transactionTypes as $type)
                                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                            {{ ucfirst($type) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Transaction Name</label>
                                <select name="transaction_name" class="form-control">
                                    <option value="">All Names</option>
                                    @foreach($transactionNames as $name)
                                        <option value="{{ $name }}" {{ request('transaction_name') == $name ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Date From</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Date To</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Min Amount</label>
                                <input type="number" name="amount_min" class="form-control" value="{{ request('amount_min') }}" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Max Amount</label>
                                <input type="number" name="amount_max" class="form-control" value="{{ request('amount_max') }}" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Apply Filters
                                    </button>
                                    <a href="{{ route('admin.logs.custom-transactions') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Clear Filters
                                    </a>
                                    <a href="{{ route('admin.logs.export-transactions') }}?{{ http_build_query(request()->query()) }}" class="btn btn-success">
                                        <i class="fas fa-download"></i> Export CSV
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i> Transactions ({{ $transactions->total() }} total)
                </h3>
                <div class="card-tools">
                    <span class="badge badge-info">{{ $transactions->count() }} shown</span>
                </div>
            </div>
            <div class="card-body">
                @if($transactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Target User</th>
                                    <th>Amount</th>
                                    <th>Type</th>
                                    <th>Transaction Name</th>
                                    <th>Old Balance</th>
                                    <th>New Balance</th>
                                    <th>Meta</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->id }}</td>
                                        <td>
                                            <strong>{{ $transaction->user->user_name ?? 'N/A' }}</strong>
                                            <br><small class="text-muted">{{ $transaction->user->type ?? 'N/A' }}</small>
                                        </td>
                                        <td>
                                            @if($transaction->targetUser)
                                                <strong>{{ $transaction->targetUser->user_name }}</strong>
                                                <br><small class="text-muted">{{ $transaction->targetUser->type }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="{{ $transaction->type === 'deposit' ? 'amount-positive' : 'amount-negative' }}">
                                                {{ $transaction->type === 'deposit' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="transaction-type {{ $transaction->type }}">
                                                {{ ucfirst($transaction->type) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-secondary">{{ $transaction->transaction_name }}</span>
                                        </td>
                                        <td>{{ number_format($transaction->old_balance, 2) }}</td>
                                        <td>{{ number_format($transaction->new_balance, 2) }}</td>
                                        <td>
                                            @if($transaction->meta)
                                                <div class="meta-data" title="{{ json_encode($transaction->meta, JSON_PRETTY_PRINT) }}">
                                                    {{ json_encode($transaction->meta) }}
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>{{ $transaction->created_at->format('M d, Y') }}</small>
                                            <br><small class="text-muted">{{ $transaction->created_at->format('H:i:s') }}</small>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.logs.transaction-detail', $transaction->id) }}" 
                                               class="btn btn-sm btn-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            Showing {{ $transactions->firstItem() }} to {{ $transactions->lastItem() }} 
                            of {{ $transactions->total() }} results
                        </div>
                        <div>
                            {{ $transactions->appends(request()->query())->links() }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No transactions found</h5>
                        <p class="text-muted">Try adjusting your filters or check back later.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        placeholder: 'Select an option',
        allowClear: true
    });

    // Auto-submit form on filter change
    $('select[name="type"], select[name="transaction_name"]').on('change', function() {
        $(this).closest('form').submit();
    });
});
</script>
@endsection
