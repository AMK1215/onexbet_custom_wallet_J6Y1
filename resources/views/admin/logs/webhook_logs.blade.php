@extends('layouts.master')

@section('title', 'Webhook API Logs')

@section('style')
<style>
    .status-success {
        color: #28a745;
        font-weight: bold;
    }
    .status-failure {
        color: #dc3545;
        font-weight: bold;
    }
    .status-partial {
        color: #ffc107;
        font-weight: bold;
    }
    .log-type {
        font-weight: bold;
    }
    .log-type.deposit {
        color: #28a745;
    }
    .log-type.withdraw {
        color: #dc3545;
    }
    .log-type.balance {
        color: #007bff;
    }
    .request-data {
        max-width: 300px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .response-data {
        max-width: 300px;
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
                <h1 class="m-0">Webhook API Logs</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.logs.index') }}">System Logs</a></li>
                    <li class="breadcrumb-item active">Webhook Logs</li>
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
                <form method="GET" action="{{ route('admin.logs.webhook-logs') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Type</label>
                                <select name="type" class="form-control">
                                    <option value="">All Types</option>
                                    @foreach($logTypes as $type)
                                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                            {{ ucfirst($type) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">All Statuses</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Date From</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Date To</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Apply Filters
                                    </button>
                                    <a href="{{ route('admin.logs.webhook-logs') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Clear Filters
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Webhook Logs Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i> Webhook Logs ({{ $logs->total() }} total)
                </h3>
                <div class="card-tools">
                    <span class="badge badge-info">{{ $logs->count() }} shown</span>
                </div>
            </div>
            <div class="card-body">
                @if($logs->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Request Data</th>
                                    <th>Response Data</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($logs as $log)
                                    <tr>
                                        <td>{{ $log->id }}</td>
                                        <td>
                                            <span class="log-type {{ $log->type }}">
                                                {{ ucfirst($log->type) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($log->status === 'success')
                                                <span class="status-success">
                                                    <i class="fas fa-check-circle"></i> Success
                                                </span>
                                            @elseif($log->status === 'failure')
                                                <span class="status-failure">
                                                    <i class="fas fa-times-circle"></i> Failure
                                                </span>
                                            @else
                                                <span class="status-partial">
                                                    <i class="fas fa-exclamation-triangle"></i> {{ ucfirst($log->status) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="request-data" title="{{ json_encode($log->batch_request, JSON_PRETTY_PRINT) }}">
                                                {{ json_encode($log->batch_request) }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="response-data" title="{{ json_encode($log->response_data, JSON_PRETTY_PRINT) }}">
                                                {{ json_encode($log->response_data) }}
                                            </div>
                                        </td>
                                        <td>
                                            <small>{{ $log->created_at->format('M d, Y') }}</small>
                                            <br><small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.logs.webhook-detail', $log->id) }}" 
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
                            Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} 
                            of {{ $logs->total() }} results
                        </div>
                        <div>
                            {{ $logs->appends(request()->query())->links() }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No webhook logs found</h5>
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
    // Auto-submit form on filter change
    $('select[name="type"], select[name="status"]').on('change', function() {
        $(this).closest('form').submit();
    });
});
</script>
@endsection
