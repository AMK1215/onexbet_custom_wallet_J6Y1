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
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
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
                    <button type="button" class="btn btn-sm btn-warning ml-2" id="bulkDeleteBtn" disabled>
                        <i class="fas fa-trash"></i> Delete Selected
                    </button>
                    <a href="{{ route('admin.logs.deleted-transactions') }}" class="btn btn-sm btn-secondary ml-2">
                        <i class="fas fa-history"></i> View Deleted
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
                                    <th>Meta</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="form-check-input transaction-checkbox" 
                                                   value="{{ $transaction->id }}" data-amount="{{ $transaction->amount }}"
                                                   data-type="{{ $transaction->type }}" data-user="{{ $transaction->user->user_name ?? 'N/A' }}">
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle"></i> Confirm Soft Delete
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle"></i>
                    <strong>Important:</strong> This will soft delete the selected transactions. 
                    They will be hidden from the main view but can be restored later. 
                    <strong>User balances will NOT be affected.</strong>
                </div>
                
                <div id="selectedTransactionsInfo">
                    <!-- Selected transactions will be displayed here -->
                </div>
                
                <div class="form-group">
                    <label for="deleteReason" class="font-weight-bold">Reason for deletion <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="deleteReason" rows="3" 
                              placeholder="Please provide a reason for deleting these transactions..." required></textarea>
                    <small class="form-text text-muted">This reason will be logged for audit purposes.</small>
                </div>
                
                <div id="balanceWarnings" class="alert alert-danger" style="display: none;">
                    <h6><i class="fas fa-exclamation-triangle"></i> Balance Discrepancy Warnings:</h6>
                    <div id="warningList"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" id="confirmDeleteBtn">
                    <i class="fas fa-trash"></i> Soft Delete Transactions
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

    // Auto-submit form on filter change
    $('select[name="type"], select[name="transaction_name"]').on('change', function() {
        $(this).closest('form').submit();
    });

    // Checkbox functionality
    $('#selectAll').on('change', function() {
        $('.transaction-checkbox').prop('checked', this.checked);
        updateDeleteButton();
    });

    $('.transaction-checkbox').on('change', function() {
        updateDeleteButton();
        updateSelectAllCheckbox();
    });

    function updateDeleteButton() {
        const checkedBoxes = $('.transaction-checkbox:checked');
        $('#bulkDeleteBtn').prop('disabled', checkedBoxes.length === 0);
    }

    function updateSelectAllCheckbox() {
        const totalCheckboxes = $('.transaction-checkbox').length;
        const checkedCheckboxes = $('.transaction-checkbox:checked').length;
        $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
    }

    // Bulk delete functionality
    $('#bulkDeleteBtn').on('click', function() {
        const selectedTransactions = $('.transaction-checkbox:checked');
        if (selectedTransactions.length === 0) {
            alert('Please select at least one transaction to delete.');
            return;
        }

        // Display selected transactions info
        let infoHtml = '<h6>Selected Transactions:</h6><ul class="list-unstyled">';
        selectedTransactions.each(function() {
            const $this = $(this);
            infoHtml += `<li>
                <strong>ID:</strong> ${$this.val()}, 
                <strong>User:</strong> ${$this.data('user')}, 
                <strong>Amount:</strong> ${$this.data('amount')}, 
                <strong>Type:</strong> ${$this.data('type')}
            </li>`;
        });
        infoHtml += '</ul>';
        $('#selectedTransactionsInfo').html(infoHtml);

        // Clear previous warnings and reason
        $('#balanceWarnings').hide();
        $('#deleteReason').val('');
        
        $('#deleteModal').modal('show');
    });

    // Confirm delete
    $('#confirmDeleteBtn').on('click', function() {
        const reason = $('#deleteReason').val().trim();
        if (!reason) {
            alert('Please provide a reason for deletion.');
            $('#deleteReason').focus();
            return;
        }

        const selectedIds = $('.transaction-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedIds.length === 0) {
            alert('No transactions selected.');
            return;
        }

        // Show loading state
        const $btn = $(this);
        const originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Deleting...');

        // Send AJAX request
        $.ajax({
            url: '{{ route("admin.logs.soft-delete-transactions") }}',
            method: 'POST',
            data: {
                transaction_ids: selectedIds,
                reason: reason,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    toastr.success(response.message);
                    
                    // Hide modal
                    $('#deleteModal').modal('hide');
                    
                    // Show balance warnings if any
                    if (response.balance_warnings && response.balance_warnings.length > 0) {
                        let warningHtml = '<ul class="mb-0">';
                        response.balance_warnings.forEach(function(warning) {
                            warningHtml += `<li>
                                Transaction ID ${warning.transaction_id} (${warning.user}): 
                                Current: ${warning.current_balance}, Expected: ${warning.expected_balance}, 
                                Difference: ${warning.difference}
                            </li>`;
                        });
                        warningHtml += '</ul>';
                        $('#warningList').html(warningHtml);
                        $('#balanceWarnings').show();
                    }
                    
                    // Reload page after short delay
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while deleting transactions.';
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
    });

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
