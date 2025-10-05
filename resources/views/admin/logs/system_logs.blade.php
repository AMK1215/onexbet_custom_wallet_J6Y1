@extends('layouts.master')

@section('title', 'System Logs')

@section('style')
<style>
    .log-level {
        font-weight: bold;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 0.8em;
    }
    .log-level.ERROR {
        background-color: #dc3545;
        color: white;
    }
    .log-level.WARNING {
        background-color: #ffc107;
        color: black;
    }
    .log-level.INFO {
        background-color: #17a2b8;
        color: white;
    }
    .log-level.DEBUG {
        background-color: #6c757d;
        color: white;
    }
    .log-content {
        font-family: 'Courier New', monospace;
        font-size: 0.9em;
        white-space: pre-wrap;
        word-break: break-all;
    }
    .log-timestamp {
        color: #6c757d;
        font-size: 0.8em;
    }
    .log-search {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
    }
    .log-entry {
        border-left: 4px solid #dee2e6;
        padding: 10px;
        margin-bottom: 10px;
        background: white;
    }
    .log-entry.ERROR {
        border-left-color: #dc3545;
        background: #fff5f5;
    }
    .log-entry.WARNING {
        border-left-color: #ffc107;
        background: #fffbf0;
    }
    .log-entry.INFO {
        border-left-color: #17a2b8;
        background: #f0f9ff;
    }
    .log-entry.DEBUG {
        border-left-color: #6c757d;
        background: #f8f9fa;
    }
</style>
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">System Logs</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.logs.index') }}">System Logs</a></li>
                    <li class="breadcrumb-item active">Laravel Logs</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <!-- Search and Filter -->
        <div class="card log-search mb-4">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-search"></i> Search & Filter
                </h3>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.logs.system-logs') }}">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Log Level</label>
                                <select name="level" class="form-control">
                                    <option value="all" {{ request('level') == 'all' ? 'selected' : '' }}>All Levels</option>
                                    <option value="error" {{ request('level') == 'error' ? 'selected' : '' }}>Error</option>
                                    <option value="warning" {{ request('level') == 'warning' ? 'selected' : '' }}>Warning</option>
                                    <option value="info" {{ request('level') == 'info' ? 'selected' : '' }}>Info</option>
                                    <option value="debug" {{ request('level') == 'debug' ? 'selected' : '' }}>Debug</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Search Term</label>
                                <input type="text" name="search" class="form-control" 
                                       value="{{ request('search') }}" 
                                       placeholder="Search in log content...">
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

        <!-- Logs Display -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-alt"></i> Laravel Application Logs
                </h3>
                <div class="card-tools">
                    <span class="badge badge-info">{{ count($logs) }} entries shown</span>
                    <button type="button" class="btn btn-tool" onclick="refreshLogs()">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if(count($logs) > 0)
                    <div class="logs-container" style="max-height: 600px; overflow-y: auto;">
                        @foreach($logs as $logEntry)
                            <div class="log-entry {{ $logEntry['level'] }}">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <span class="log-level {{ $logEntry['level'] }}">{{ $logEntry['level'] }}</span>
                                        @if($logEntry['timestamp'])
                                            <span class="log-timestamp ml-2">{{ $logEntry['timestamp'] }}</span>
                                        @endif
                                    </div>
                                    <button class="btn btn-sm btn-outline-secondary" 
                                            onclick="copyToClipboard('{{ addslashes($logEntry['content']) }}')"
                                            title="Copy to clipboard">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                                <div class="log-content">{{ $logEntry['content'] }}</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No log entries found</h5>
                        <p class="text-muted">
                            @if(request('search') || request('level') != 'all')
                                Try adjusting your search criteria or 
                                <a href="{{ route('admin.logs.system-logs') }}">view all logs</a>.
                            @else
                                No log entries available. Check back later or ensure logging is enabled.
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Log Statistics -->
        @if(isset($logStats))
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Errors</span>
                        <span class="info-box-number">{{ $logStats['error'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-warning">
                        <i class="fas fa-exclamation-circle"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Warnings</span>
                        <span class="info-box-number">{{ $logStats['warning'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info">
                        <i class="fas fa-info-circle"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Info</span>
                        <span class="info-box-number">{{ $logStats['info'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-secondary">
                        <i class="fas fa-bug"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Debug</span>
                        <span class="info-box-number">{{ $logStats['debug'] }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</section>
@endsection

@section('script')
<script>
function refreshLogs() {
    window.location.reload();
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        toastr.success('Log entry copied to clipboard');
    }, function(err) {
        console.error('Could not copy text: ', err);
        toastr.error('Failed to copy to clipboard');
    });
}

// Auto-refresh every 30 seconds
setInterval(function() {
    if (document.visibilityState === 'visible') {
        refreshLogs();
    }
}, 30000);
</script>
@endsection
