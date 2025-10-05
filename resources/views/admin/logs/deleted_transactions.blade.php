@extends('layouts.master')

@section('title', 'Deleted Custom Wallet Transactions')

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
    .deleted-info {
        background-color: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 5px;
        padding: 10px;
        margin-bottom: 15px;
    }
</style>
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Deleted Custom Wallet Transactions</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.logs.index') }}">System Logs</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.logs.custom-transactions') }}">Custom Transactions</a></li>
                    <li class="breadcrumb-item active">Deleted Transactions</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Info Alert -->
        <div class="deleted-info">
            <i class="fas fa-info-circle"></i>
            <strong>Note:</strong> These transactions have been soft deleted and are hidden from the main transaction view. 
            They can be restored if needed. <strong>User balances were not affected by the deletion.</strong>
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
                <form method="GET" action="{{ route('admin.logs.deleted-transactions') }}">
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
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Deleted By</label>
                                <select name="deleted_by" class="form-control">
                                    <option value="">All Admins</option>
                                    @foreach($admins as $admin)
                                        <option value="{{ $admin->id }}" {{ request('deleted_by') == $admin->id ? 'selected' : '' }}>
                                            {{ $admin->user_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Deleted From</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Deleted To</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                        </div>
                        <div class="col-md-1">
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

        <!-- Deleted Transactions Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-trash"></i> Deleted Transactions ({{ $transactions->total() }} total)
                </h3>
                <div class="card-tools">
                    <span class="badge badge-info">{{ $transactions->count() }} shown</span>
                    <button type="button" class="btn btn-sm btn-success ml-2" id="bulkRestoreBtn" disabled>
                        <i class="fas fa-undo"></i> Restore Selected
                    </button>
                    <a href="{{ route('admin.logs.custom-transactions') }}" class="btn btn-sm btn-primary ml-2">
                        <i class="fas fa-arrow-left"></i> Back to Active
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($transactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Target User</th>
                                    <th>Amount</th>
                                    <th>Type</th>
                                    <th>Transaction Name</th>
                                    <th>Old Balance</th>
                                    <th>New Balance</th>
                                    <th>Deleted By</th>
                                    <th>Deleted Reason</th>
                                    <th>Deleted At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="form-check-input transaction-checkbox" 
                                                   value="{{ $transaction->id }}">
                                        </td>
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
                                            <strong>{{ $transaction->deletedBy->user_name ?? 'System' }}</strong>
                                            <br><small class="text-muted">{{ $transaction->deleted_at->format('M d, Y H:i') }}</small>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ Str::limit($transaction->deleted_reason, 50) }}</small>
                                        </td>
                                        <td>
                                            <small>{{ $transaction->deleted_at->format('M d, Y') }}</small>
                                            <br><small class="text-muted">{{ $transaction->deleted_at->format('H:i:s') }}</small>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-success restore-btn" 
                                                    data-id="{{ $transaction->id }}" title="Restore Transaction">
                                                <i class="fas fa-undo"></i>
                                            </button>
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
                        <i class="fas fa-trash fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No deleted transactions found</h5>
                        <p class="text-muted">Try adjusting your filters or check back later.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Restore Confirmation Modal -->
<div class="modal fade" id="restoreModal" tabindex="-1" role="dialog" aria-labelledby="restoreModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title" id="restoreModalLabel">
                    <i class="fas fa-undo"></i> Confirm Restore
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Note:</strong> This will restore the selected transactions back to the active transaction list.
                    <strong>User balances will NOT be affected.</strong>
                </div>
                
                <div id="restoreTransactionsInfo">
                    <!-- Selected transactions will be displayed here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmRestoreBtn">
                    <i class="fas fa-undo"></i> Restore Transactions
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        placeholder: 'Select an option',
        allowClear: true
    });

    // Checkbox functionality
    $('#selectAll').on('change', function() {
        $('.transaction-checkbox').prop('checked', this.checked);
        updateRestoreButton();
    });

    $('.transaction-checkbox').on('change', function() {
        updateRestoreButton();
        updateSelectAllCheckbox();
    });

    function updateRestoreButton() {
        const checkedBoxes = $('.transaction-checkbox:checked');
        $('#bulkRestoreBtn').prop('disabled', checkedBoxes.length === 0);
    }

    function updateSelectAllCheckbox() {
        const totalCheckboxes = $('.transaction-checkbox').length;
        const checkedCheckboxes = $('.transaction-checkbox:checked').length;
        $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
    }

    // Bulk restore functionality
    $('#bulkRestoreBtn').on('click', function() {
        const selectedTransactions = $('.transaction-checkbox:checked');
        if (selectedTransactions.length === 0) {
            alert('Please select at least one transaction to restore.');
            return;
        }

        // Display selected transactions info
        let infoHtml = '<h6>Selected Transactions to Restore:</h6><ul class="list-unstyled">';
        selectedTransactions.each(function() {
            const $row = $(this).closest('tr');
            const id = $row.find('td:eq(1)').text();
            const user = $row.find('td:eq(2) strong').text();
            const amount = $row.find('td:eq(4) span').text();
            const type = $row.find('td:eq(5) span').text();
            infoHtml += `<li><strong>ID:</strong> ${id}, <strong>User:</strong> ${user}, <strong>Amount:</strong> ${amount}, <strong>Type:</strong> ${type}</li>`;
        });
        infoHtml += '</ul>';
        $('#restoreTransactionsInfo').html(infoHtml);
        
        $('#restoreModal').modal('show');
    });

    // Individual restore buttons
    $('.restore-btn').on('click', function() {
        const transactionId = $(this).data('id');
        if (confirm('Are you sure you want to restore this transaction?')) {
            restoreTransaction([transactionId]);
        }
    });

    // Confirm bulk restore
    $('#confirmRestoreBtn').on('click', function() {
        const selectedIds = $('.transaction-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedIds.length === 0) {
            alert('No transactions selected.');
            return;
        }

        restoreTransaction(selectedIds);
    });

    function restoreTransaction(transactionIds) {
        // Show loading state
        const $btn = $('#confirmRestoreBtn');
        const originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Restoring...');

        // Send AJAX request
        $.ajax({
            url: '{{ route("admin.logs.restore-transactions") }}',
            method: 'POST',
            data: {
                transaction_ids: transactionIds,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message);
                    } else {
                        alert(response.message);
                    }
                    
                    // Hide modal
                    $('#restoreModal').modal('hide');
                    
                    // Reload page after short delay
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while restoring transactions.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert(errorMessage);
            },
            complete: function() {
                // Reset button state
                $btn.prop('disabled', false).html(originalText);
            }
        });
    }

    // Initialize toastr for notifications
    if (typeof toastr !== 'undefined') {
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right"
        };
    }
});
</script>
@endsection
