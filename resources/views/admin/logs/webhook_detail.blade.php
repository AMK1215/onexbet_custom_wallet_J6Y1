@extends('layouts.master')

@section('title', 'Webhook Log Details')

@section('style')
<style>
    .json-display {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 15px;
        font-family: 'Courier New', monospace;
        font-size: 0.9em;
        white-space: pre-wrap;
        word-break: break-all;
        max-height: 400px;
        overflow-y: auto;
    }
    .status-badge {
        font-size: 1.1em;
        padding: 8px 16px;
    }
    .status-success {
        background-color: #28a745;
        color: white;
    }
    .status-failure {
        background-color: #dc3545;
        color: white;
    }
    .status-partial {
        background-color: #ffc107;
        color: black;
    }
    .log-type {
        font-weight: bold;
        font-size: 1.2em;
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
</style>
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Webhook Log Details</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.logs.index') }}">System Logs</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.logs.webhook-logs') }}">Webhook Logs</a></li>
                    <li class="breadcrumb-item active">Log #{{ $log->id }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Log Overview -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i> Webhook Log Overview
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Log ID:</strong></td>
                                        <td>{{ $log->id }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Type:</strong></td>
                                        <td>
                                            <span class="log-type {{ $log->type }}">
                                                {{ ucfirst($log->type) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            @if($log->status === 'success')
                                                <span class="badge status-badge status-success">
                                                    <i class="fas fa-check-circle"></i> Success
                                                </span>
                                            @elseif($log->status === 'failure')
                                                <span class="badge status-badge status-failure">
                                                    <i class="fas fa-times-circle"></i> Failure
                                                </span>
                                            @else
                                                <span class="badge status-badge status-partial">
                                                    <i class="fas fa-exclamation-triangle"></i> {{ ucfirst($log->status) }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Created At:</strong></td>
                                        <td>{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Updated At:</strong></td>
                                        <td>{{ $log->updated_at->format('M d, Y H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Duration:</strong></td>
                                        <td>
                                            @if($log->created_at && $log->updated_at)
                                                {{ $log->created_at->diffInMilliseconds($log->updated_at) }}ms
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Request Data -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-arrow-right"></i> Request Data
                        </h3>
                        <div class="card-tools">
                            <button class="btn btn-tool" onclick="copyToClipboard('{{ addslashes(json_encode($log->batch_request, JSON_PRETTY_PRINT)) }}')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="json-display">{{ json_encode($log->batch_request, JSON_PRETTY_PRINT) }}</div>
                    </div>
                </div>

                <!-- Response Data -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-arrow-left"></i> Response Data
                        </h3>
                        <div class="card-tools">
                            <button class="btn btn-tool" onclick="copyToClipboard('{{ addslashes(json_encode($log->response_data, JSON_PRETTY_PRINT)) }}')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="json-display">{{ json_encode($log->response_data, JSON_PRETTY_PRINT) }}</div>
                    </div>
                </div>
            </div>

            <!-- Analysis & Actions -->
            <div class="col-md-4">
                <!-- Request Analysis -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line"></i> Request Analysis
                        </h3>
                    </div>
                    <div class="card-body">
                        @php
                            $batchRequests = $log->batch_request['batch_requests'] ?? [];
                            $totalRequests = count($batchRequests);
                            $successCount = 0;
                            $errorCount = 0;
                            
                            if (isset($log->response_data['data'])) {
                                foreach ($log->response_data['data'] as $response) {
                                    if (isset($response['code']) && $response['code'] === 0) {
                                        $successCount++;
                                    } else {
                                        $errorCount++;
                                    }
                                }
                            }
                        @endphp
                        
                        <div class="row">
                            <div class="col-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info">
                                        <i class="fas fa-list"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Requests</span>
                                        <span class="info-box-number">{{ $totalRequests }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success">
                                        <i class="fas fa-check"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Successful</span>
                                        <span class="info-box-number">{{ $successCount }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        @if($errorCount > 0)
                        <div class="row">
                            <div class="col-12">
                                <div class="info-box">
                                    <span class="info-box-icon bg-danger">
                                        <i class="fas fa-times"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Failed</span>
                                        <span class="info-box-number">{{ $errorCount }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <div class="progress mb-3">
                            <div class="progress-bar bg-success" style="width: {{ $totalRequests > 0 ? ($successCount / $totalRequests) * 100 : 0 }}%"></div>
                            <div class="progress-bar bg-danger" style="width: {{ $totalRequests > 0 ? ($errorCount / $totalRequests) * 100 : 0 }}%"></div>
                        </div>
                        
                        <small class="text-muted">
                            Success Rate: {{ $totalRequests > 0 ? round(($successCount / $totalRequests) * 100, 1) : 0 }}%
                        </small>
                    </div>
                </div>

                <!-- Request Details -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info"></i> Request Details
                        </h3>
                    </div>
                    <div class="card-body">
                        @if(isset($log->batch_request['operator_code']))
                            <p><strong>Operator Code:</strong><br>
                            <code>{{ $log->batch_request['operator_code'] }}</code></p>
                        @endif
                        
                        @if(isset($log->batch_request['currency']))
                            <p><strong>Currency:</strong><br>
                            <span class="badge badge-info">{{ $log->batch_request['currency'] }}</span></p>
                        @endif
                        
                        @if(isset($log->batch_request['request_time']))
                            <p><strong>Request Time:</strong><br>
                            <small>{{ date('M d, Y H:i:s', $log->batch_request['request_time']) }}</small></p>
                        @endif
                        
                        @if(isset($log->batch_request['sign']))
                            <p><strong>Signature:</strong><br>
                            <code style="font-size: 0.8em;">{{ $log->batch_request['sign'] }}</code></p>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-tools"></i> Quick Actions
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.logs.webhook-logs') }}" 
                               class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Webhook Logs
                            </a>
                            
                            <button class="btn btn-info" onclick="copyFullLog()">
                                <i class="fas fa-copy"></i> Copy Full Log
                            </button>
                            
                            <button class="btn btn-warning" onclick="testWebhook()">
                                <i class="fas fa-play"></i> Test Similar Request
                            </button>
                            
                            @if($log->status !== 'success')
                                <button class="btn btn-danger" onclick="retryWebhook()">
                                    <i class="fas fa-redo"></i> Retry Request
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Error Analysis -->
                @if($log->status !== 'success' && isset($log->response_data['data']))
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-exclamation-triangle"></i> Error Analysis
                        </h3>
                    </div>
                    <div class="card-body">
                        @foreach($log->response_data['data'] as $index => $response)
                            @if(isset($response['code']) && $response['code'] !== 0)
                                <div class="alert alert-danger">
                                    <strong>Request #{{ $index + 1 }}:</strong><br>
                                    <strong>Code:</strong> {{ $response['code'] }}<br>
                                    <strong>Message:</strong> {{ $response['message'] ?? 'No message' }}
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        toastr.success('Data copied to clipboard');
    }, function(err) {
        console.error('Could not copy text: ', err);
        toastr.error('Failed to copy to clipboard');
    });
}

function copyFullLog() {
    const logData = {
        id: {{ $log->id }},
        type: '{{ $log->type }}',
        status: '{{ $log->status }}',
        created_at: '{{ $log->created_at }}',
        batch_request: @json($log->batch_request),
        response_data: @json($log->response_data)
    };
    
    copyToClipboard(JSON.stringify(logData, null, 2));
}

function testWebhook() {
    if (confirm('This will create a test request similar to this webhook. Continue?')) {
        // This would need to be implemented as an API endpoint
        toastr.info('Test webhook functionality would be implemented here');
    }
}

function retryWebhook() {
    if (confirm('This will retry the failed webhook request. Continue?')) {
        // This would need to be implemented as an API endpoint
        toastr.info('Retry webhook functionality would be implemented here');
    }
}
</script>
@endsection
