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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>User Type</label>
                                <select name="user_type" class="form-control">
                                    <option value="">All Types</option>
                                    @if(isset($userTypes))
                                        @foreach($userTypes as $type)
                                            <option value="{{ $type }}" {{ request('user_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                        @endforeach
                                    @else
                                        <option value="Player" {{ request('user_type') == 'Player' ? 'selected' : '' }}>Player</option>
                                        <option value="Agent" {{ request('user_type') == 'Agent' ? 'selected' : '' }}>Agent</option>
                                        <option value="Master" {{ request('user_type') == 'Master' ? 'selected' : '' }}>Master</option>
                                        <option value="Owner" {{ request('user_type') == 'Owner' ? 'selected' : '' }}>Owner</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>IP Address</label>
                                <input type="text" name="ip_address" class="form-control" 
                                       value="{{ request('ip_address') }}" 
                                       placeholder="IP Address">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Function Access</label>
                                <input type="text" name="func_access" class="form-control" 
                                       value="{{ request('func_access') }}" 
                                       placeholder="Function">
                            </div>
                        </div>
                        <div class="col-md-3">
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
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Date From</label>
                                <input type="date" name="date_from" class="form-control" 
                                       value="{{ request('date_from') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Date To</label>
                                <input type="date" name="date_to" class="form-control" 
                                       value="{{ request('date_to') }}">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- User Logs Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-clock"></i> User Activity Logs
                </h3>
                <div class="card-tools">
                    <span class="badge badge-info">{{ $userLogs->total() }} entries</span>
                </div>
            </div>
            <div class="card-body">
                @if($userLogs->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Type</th>
                                    <th>IP Address</th>
                                    <th>Function Access</th>
                                    <th>Last Update</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($userLogs as $userLog)
                                    <tr>
                                        <td>{{ $userLog->id }}</td>
                                        <td>
                                            @if($userLog->user)
                                                <div>
                                                    <strong>{{ $userLog->user->name }}</strong><br>
                                                    <small class="text-muted">{{ $userLog->user->user_name }}</small>
                                                </div>
                                            @else
                                                <span class="text-muted">User not found</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($userLog->user)
                                                <span class="user-type {{ $userLog->user->type }}">{{ $userLog->user->type }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <code>{{ $userLog->ip_address }}</code>
                                        </td>
                                        <td>
                                            @if($userLog->func_access)
                                                <span class="badge badge-secondary">{{ $userLog->func_access }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($userLog->lastupdate)
                                                {{ date('Y-m-d H:i:s', $userLog->lastupdate) }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $userLog->created_at->format('Y-m-d H:i:s') }}</td>
                                        <td>
                                            @if($userLog->user)
                                                <a href="{{ route('admin.player.show', $userLog->user->id) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="View User">
                                                    <i class="fas fa-user"></i>
                                                </a>
                                                <a href="{{ route('admin.logs.custom-transactions', ['user_id' => $userLog->user->id]) }}" 
                                                   class="btn btn-sm btn-outline-info" title="View Transactions">
                                                    <i class="fas fa-list"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-user-clock fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No user activity logs found</h5>
                        <p class="text-muted">Try adjusting your search criteria or check back later.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Pagination -->
        @if($userLogs->hasPages())
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    {{ $userLogs->appends(request()->query())->links() }}
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
