@extends('layouts.master')

@section('title', 'User Activities')

@section('style')
<style>
    .user-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .user-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .user-type {
        font-weight: bold;
    }
    .user-type.Player {
        color: #28a745;
    }
    .user-type.Agent {
        color: #007bff;
    }
    .user-type.Master {
        color: #6f42c1;
    }
    .user-type.Owner {
        color: #dc3545;
    }
    .balance-display {
        font-size: 1.2em;
        font-weight: bold;
        color: #28a745;
    }
    .transaction-item {
        border-left: 3px solid #dee2e6;
        padding: 8px 12px;
        margin-bottom: 8px;
        background: #f8f9fa;
        border-radius: 0 4px 4px 0;
    }
    .transaction-item.deposit {
        border-left-color: #28a745;
    }
    .transaction-item.withdraw {
        border-left-color: #dc3545;
    }
    .transaction-item.transfer {
        border-left-color: #007bff;
    }
    .transaction-amount {
        font-weight: bold;
    }
    .transaction-amount.positive {
        color: #28a745;
    }
    .transaction-amount.negative {
        color: #dc3545;
    }
</style>
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">User Activities</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.logs.index') }}">System Logs</a></li>
                    <li class="breadcrumb-item active">User Activities</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <!-- Filters -->
        <div class="card mb-4">
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
                <form method="GET" action="{{ route('admin.logs.user-activities') }}">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>User Type</label>
                                <select name="user_type" class="form-control">
                                    <option value="">All Types</option>
                                    <option value="Player" {{ request('user_type') == 'Player' ? 'selected' : '' }}>Player</option>
                                    <option value="Agent" {{ request('user_type') == 'Agent' ? 'selected' : '' }}>Agent</option>
                                    <option value="Master" {{ request('user_type') == 'Master' ? 'selected' : '' }}>Master</option>
                                    <option value="Owner" {{ request('user_type') == 'Owner' ? 'selected' : '' }}>Owner</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Search</label>
                                <input type="text" name="search" class="form-control" 
                                       value="{{ request('search') }}" 
                                       placeholder="Search by username, name, or email...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Users List -->
        <div class="row">
            @forelse($users as $user)
                <div class="col-lg-6 col-xl-4 mb-4">
                    <div class="card user-card" onclick="window.location.href='{{ route('admin.player.show', $user->id) }}'">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0">{{ $user->name }}</h5>
                                    <small class="text-muted">{{ $user->user_name }}</small>
                                </div>
                                <div class="text-right">
                                    <span class="user-type {{ $user->type }}">{{ $user->type }}</span>
                                    <br>
                                    <span class="balance-display">{{ number_format($user->balance, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-6">
                                    <small class="text-muted">User ID</small><br>
                                    <strong>{{ $user->id }}</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Email</small><br>
                                    <strong>{{ $user->email ?? 'N/A' }}</strong>
                                </div>
                            </div>
                            
                            @if($user->customTransactions && $user->customTransactions->count() > 0)
                                <div class="mb-3">
                                    <small class="text-muted">Recent Transactions</small>
                                    <div class="mt-2">
                                        @foreach($user->customTransactions->take(3) as $transaction)
                                            <div class="transaction-item {{ $transaction->type }}">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong>{{ ucfirst($transaction->type) }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $transaction->transaction_name }}</small>
                                                    </div>
                                                    <div class="text-right">
                                                        <span class="transaction-amount {{ $transaction->type === 'deposit' ? 'positive' : 'negative' }}">
                                                            {{ $transaction->type === 'deposit' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }}
                                                        </span>
                                                        <br>
                                                        <small class="text-muted">{{ $transaction->created_at->format('M d, H:i') }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        
                                        @if($user->customTransactions->count() > 3)
                                            <div class="text-center mt-2">
                                                <small class="text-muted">
                                                    +{{ $user->customTransactions->count() - 3 }} more transactions
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <div class="text-center text-muted py-3">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <p class="mb-0">No recent transactions</p>
                                </div>
                            @endif
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    Last Activity: {{ $user->updated_at->diffForHumans() }}
                                </small>
                                <a href="{{ route('admin.logs.custom-transactions', ['user_id' => $user->id]) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-list"></i> View All
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No users found</h5>
                            <p class="text-muted">Try adjusting your search criteria or check back later.</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    {{ $users->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</section>
@endsection

@section('script')
<script>
$(document).ready(function() {
    // Auto-submit form on filter change
    $('select[name="user_type"]').on('change', function() {
        $(this).closest('form').submit();
    });
});
</script>
@endsection
