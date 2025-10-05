@extends('layouts.master')

@section('title', 'View Archived Transactions')

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
    .archive-badge {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
    }
</style>
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Archived Transactions</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.logs.index') }}">System Logs</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.transaction-archive.index') }}">Transaction Archive</a></li>
                    <li class="breadcrumb-item active">View Archived</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Info Alert -->
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <strong>Archived Transactions:</strong> These transactions have been moved from the main table to preserve system performance while maintaining complete data integrity.
        </div>

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
                <form method="GET" action="{{ route('admin.transaction-archive.view-archived') }}">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Transaction Type</label>
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
                                <label>Batch ID</label>
                                <select name="batch_id" class="form-control">
                                    <option value="">All Batches</option>
                                    @foreach($batchIds as $batchId)
                                        <option value="{{ $batchId }}" {{ request('batch_id') == $batchId ? 'selected' : '' }}>
                                            {{ $batchId }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Archived From</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Archived To</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Apply
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Archived Transactions Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-archive"></i> Archived Transactions ({{ $transactions->total() }} total)
                </h3>
                <div class="card-tools">
                    <span class="badge badge-info">{{ $transactions->count() }} shown</span>
                    <a href="{{ route('admin.transaction-archive.index') }}" class="btn btn-sm btn-secondary ml-2">
                        <i class="fas fa-arrow-left"></i> Back to Archive Management
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($transactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Original ID</th>
                                    <th>User ID</th>
                                    <th>Target User ID</th>
                                    <th>Amount</th>
                                    <th>Type</th>
                                    <th>Transaction Name</th>
                                    <th>Old Balance</th>
                                    <th>New Balance</th>
                                    <th>Original Date</th>
                                    <th>Archived At</th>
                                    <th>Batch ID</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                    <tr>
                                        <td>
                                            <strong>{{ $transaction->original_id ?? 'N/A' }}</strong>
                                        </td>
                                        <td>
                                            <strong>{{ $transaction->user_id ?? 'N/A' }}</strong>
                                        </td>
                                        <td>
                                            @if($transaction->target_user_id)
                                                <strong>{{ $transaction->target_user_id }}</strong>
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
                                            <small>{{ \Carbon\Carbon::parse($transaction->created_at)->format('M d, Y') }}</small>
                                            <br><small class="text-muted">{{ \Carbon\Carbon::parse($transaction->created_at)->format('H:i:s') }}</small>
                                        </td>
                                        <td>
                                            <small>{{ \Carbon\Carbon::parse($transaction->archived_at)->format('M d, Y') }}</small>
                                            <br><small class="text-muted">{{ \Carbon\Carbon::parse($transaction->archived_at)->format('H:i:s') }}</small>
                                        </td>
                                        <td>
                                            <span class="archive-badge">{{ $transaction->archive_batch_id }}</span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info batch-details-btn" 
                                                    data-batch-id="{{ $transaction->archive_batch_id }}" 
                                                    title="View Batch Details">
                                                <i class="fas fa-info"></i>
                                            </button>
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
                        <i class="fas fa-archive fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No archived transactions found</h5>
                        <p class="text-muted">Try adjusting your filters or check back later.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Batch Details Modal -->
<div class="modal fade" id="batchDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-info"></i> Batch Details
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="batchDetailsContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" id="restoreBatchBtn" style="display: none;">
                    <i class="fas fa-undo"></i> Restore Batch
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Restore Confirmation Modal -->
<div class="modal fade" id="restoreModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i> Confirm Batch Restore
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Warning:</strong> This will restore all transactions in this batch back to the main table. 
                    This operation should only be used in emergency situations.
                </div>
                
                <div class="form-group">
                    <label for="restoreReason" class="font-weight-bold">Reason for Restore <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="restoreReason" rows="3" 
                              placeholder="Please provide a detailed reason for restoring this batch..." required></textarea>
                </div>
                
                <div id="restoreBatchInfo">
                    <!-- Batch info will be displayed here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" id="confirmRestoreBtn">
                    <i class="fas fa-undo"></i> Restore Batch
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
let currentBatchId = null;

$(document).ready(function() {
    // Batch details functionality
    $('.batch-details-btn').on('click', function() {
        const batchId = $(this).data('batch-id');
        currentBatchId = batchId;
        
        $.ajax({
            url: `{{ route("admin.transaction-archive.batch-details", ":batchId") }}`.replace(':batchId', batchId),
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    let content = `
                        <h6>Batch Information:</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Batch ID:</strong></td><td><code>${data.batch_info.batch_id}</code></td></tr>
                            <tr><td><strong>Total Transactions:</strong></td><td>${data.batch_info.total_transactions}</td></tr>
                            <tr><td><strong>Archived At:</strong></td><td>${new Date(data.batch_info.archived_at).toLocaleString()}</td></tr>
                            <tr><td><strong>Date Range:</strong></td><td>${new Date(data.batch_info.oldest_transaction).toLocaleDateString()} - ${new Date(data.batch_info.newest_transaction).toLocaleDateString()}</td></tr>
                            <tr><td><strong>Total Amount:</strong></td><td>${data.batch_info.total_amount}</td></tr>
                        </table>
                        
                        <h6>Transaction Types:</h6>
                        <ul>
                    `;
                    
                    Object.entries(data.batch_info.types).forEach(function([type, count]) {
                        content += `<li>${type}: ${count} transactions</li>`;
                    });
                    
                    content += `</ul>`;
                    
                    $('#batchDetailsContent').html(content);
                    $('#restoreBatchBtn').show();
                    $('#batchDetailsModal').modal('show');
                }
            },
            error: function(xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || 'Failed to load batch details'));
            }
        });
    });

    // Show restore modal
    $('#restoreBatchBtn').on('click', function() {
        $('#batchDetailsModal').modal('hide');
        $('#restoreBatchInfo').html(`
            <strong>Batch ID:</strong> ${currentBatchId}<br>
            <strong>This will restore all transactions in this batch back to the main table.</strong>
        `);
        $('#restoreReason').val('');
        $('#restoreModal').modal('show');
    });

    // Confirm restore
    $('#confirmRestoreBtn').on('click', function() {
        const reason = $('#restoreReason').val().trim();
        if (!reason) {
            alert('Please provide a reason for restoration.');
            $('#restoreReason').focus();
            return;
        }

        if (!confirm('Are you absolutely sure you want to restore this batch? This operation cannot be undone.')) {
            return;
        }

        const $btn = $(this);
        const originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Restoring...');

        $.ajax({
            url: '{{ route("admin.transaction-archive.restore") }}',
            method: 'POST',
            data: {
                batch_id: currentBatchId,
                reason: reason,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert('Success: ' + response.message);
                    $('#restoreModal').modal('hide');
                    location.reload(); // Refresh to show updated data
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || 'Restore operation failed'));
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>
@endsection
