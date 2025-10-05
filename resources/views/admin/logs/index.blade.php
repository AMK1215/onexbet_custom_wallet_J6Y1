@extends('layouts.master')

@section('title', 'System Logs Dashboard')

@section('style')
<style>
    .log-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .log-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .stats-card.success {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    .stats-card.warning {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }
    .stats-card.danger {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
    }
    .log-type-icon {
        font-size: 2rem;
        margin-bottom: 1rem;
    }
</style>
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">System Logs Dashboard</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
                    <li class="breadcrumb-item active">System Logs</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-2 col-6">
                <div class="small-box stats-card success">
                    <div class="inner">
                        <h3 id="total-transactions">-</h3>
                        <p>Total Transactions</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="small-box stats-card">
                    <div class="inner">
                        <h3 id="today-transactions">-</h3>
                        <p>Today's Transactions</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="small-box stats-card warning">
                    <div class="inner">
                        <h3 id="webhook-logs-today">-</h3>
                        <p>Webhook Logs Today</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-webhook"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="small-box stats-card danger">
                    <div class="inner">
                        <h3 id="failed-webhooks">-</h3>
                        <p>Failed Webhooks</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="small-box stats-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="inner">
                        <h3 id="user-activities-today">-</h3>
                        <p>User Activities Today</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-clock"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="small-box stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <div class="inner">
                        <h3 id="unique-users-today">-</h3>
                        <p>Unique Users Today</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Log Type Cards -->
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card log-card" onclick="window.location.href='{{ route('admin.logs.custom-transactions') }}'">
                    <div class="card-body text-center">
                        <div class="log-type-icon text-primary">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <h5 class="card-title">Custom Wallet Transactions</h5>
                        <p class="card-text">View all custom wallet transactions including deposits, withdrawals, and transfers.</p>
                        <div class="mt-3">
                            <span class="badge badge-primary">Real-time</span>
                            <span class="badge badge-success">Filtered</span>
                            <span class="badge badge-warning">Soft Delete</span>
                        </div>
                        <div class="mt-2">
                            <a href="{{ route('admin.logs.deleted-transactions') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-history"></i> View Deleted
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card log-card" onclick="window.location.href='{{ route('admin.logs.webhook-logs') }}'">
                    <div class="card-body text-center">
                        <div class="log-type-icon text-info">
                            <i class="fas fa-webhook"></i>
                        </div>
                        <h5 class="card-title">Webhook API Logs</h5>
                        <p class="card-text">Monitor webhook API calls, responses, and error logs from gaming providers.</p>
                        <div class="mt-3">
                            <span class="badge badge-info">API Logs</span>
                            <span class="badge badge-warning">Debug</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card log-card" onclick="window.location.href='{{ route('admin.logs.system-logs') }}'">
                    <div class="card-body text-center">
                        <div class="log-type-icon text-warning">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <h5 class="card-title">System Logs</h5>
                        <p class="card-text">Browse Laravel application logs with filtering by level and search terms.</p>
                        <div class="mt-3">
                            <span class="badge badge-warning">Laravel</span>
                            <span class="badge badge-secondary">Debug</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card log-card" onclick="window.location.href='{{ route('admin.logs.user-activities') }}'">
                    <div class="card-body text-center">
                        <div class="log-type-icon text-success">
                            <i class="fas fa-user-activity"></i>
                        </div>
                        <h5 class="card-title">User Activities</h5>
                        <p class="card-text">Track user activities, recent transactions, and account changes.</p>
                        <div class="mt-3">
                            <span class="badge badge-success">Users</span>
                            <span class="badge badge-primary">Activity</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Quick Actions</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <button class="btn btn-primary btn-block" onclick="refreshStats()">
                                    <i class="fas fa-sync-alt"></i> Refresh Statistics
                                </button>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('admin.logs.export-transactions') }}" class="btn btn-success btn-block">
                                    <i class="fas fa-download"></i> Export Transactions
                                </a>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-warning btn-block" onclick="clearOldLogs()">
                                    <i class="fas fa-trash"></i> Clear Old Logs
                                </button>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-info btn-block" onclick="window.open('{{ route('admin.logs.system-logs') }}', '_blank')">
                                    <i class="fas fa-external-link-alt"></i> View System Logs
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Recent Activity</h3>
                    </div>
                    <div class="card-body">
                        <div id="recent-activity">
                            <div class="text-center">
                                <i class="fas fa-spinner fa-spin"></i> Loading recent activity...
                            </div>
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
$(document).ready(function() {
    loadStats();
    loadRecentActivity();
    
    // Auto-refresh stats every 30 seconds
    setInterval(loadStats, 30000);
});

function loadStats() {
    $.get('{{ route("admin.logs.stats") }}')
        .done(function(data) {
            $('#total-transactions').text(data.total_transactions.toLocaleString());
            $('#today-transactions').text(data.today_transactions.toLocaleString());
            $('#webhook-logs-today').text(data.webhook_logs_today.toLocaleString());
            $('#failed-webhooks').text(data.failed_webhooks.toLocaleString());
            
            // New user activity statistics
            if (data.user_activities_today !== undefined) {
                $('#user-activities-today').text(data.user_activities_today.toLocaleString());
            }
            if (data.unique_users_today !== undefined) {
                $('#unique-users-today').text(data.unique_users_today.toLocaleString());
            }
        })
        .fail(function() {
            console.error('Failed to load statistics');
        });
}

function loadRecentActivity() {
    $.get('{{ route("admin.logs.custom-transactions") }}?limit=5')
        .done(function(data) {
            // This would need to be implemented as an API endpoint
            $('#recent-activity').html('<p class="text-muted">Recent activity will be displayed here</p>');
        })
        .fail(function() {
            $('#recent-activity').html('<p class="text-danger">Failed to load recent activity</p>');
        });
}

function refreshStats() {
    loadStats();
    toastr.success('Statistics refreshed');
}

function clearOldLogs() {
    if (confirm('Are you sure you want to clear logs older than 30 days? This action cannot be undone.')) {
        $.post('{{ route("admin.logs.clear-old") }}', {
            days: 30,
            _token: '{{ csrf_token() }}'
        })
        .done(function(data) {
            toastr.success(data.message);
            loadStats();
        })
        .fail(function() {
            toastr.error('Failed to clear old logs');
        });
    }
}
</script>
@endsection
