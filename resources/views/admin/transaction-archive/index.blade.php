@extends('layouts.master')

@section('title', 'Transaction Archive Management')

@section('style')
<style>
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .stats-card h3 {
        margin: 0;
        font-size: 2rem;
        font-weight: bold;
    }
    .stats-card p {
        margin: 5px 0 0 0;
        opacity: 0.9;
    }
    .archive-card {
        border: 1px solid #dee2e6;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        background: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .btn-archive {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        font-weight: bold;
    }
    .btn-archive:hover {
        background: linear-gradient(135deg, #218838 0%, #1aa085 100%);
        color: white;
    }
    .btn-optimize {
        background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);
        border: none;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        font-weight: bold;
    }
    .btn-optimize:hover {
        background: linear-gradient(135deg, #0056b3 0%, #520dc2 100%);
        color: white;
    }
    .alert-archive {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 20px;
    }
    .progress-bar {
        height: 20px;
        background: #e9ecef;
        border-radius: 10px;
        overflow: hidden;
        margin: 10px 0;
    }
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #28a745, #20c997);
        transition: width 0.3s ease;
    }
</style>
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Transaction Archive Management</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.logs.index') }}">System Logs</a></li>
                    <li class="breadcrumb-item active">Transaction Archive</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Info Alert -->
        <div class="alert-archive">
            <i class="fas fa-info-circle"></i>
            <strong>Safe Transaction Archiving:</strong> This system safely moves old transactions to an archive table while preserving all data integrity. 
            This is much safer than truncating tables and maintains complete audit trails.
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <h3 id="main-table-count">{{ number_format($stats['main_table_count'] ?? 0) }}</h3>
                    <p>Active Transactions</p>
                    <small>In main table</small>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <h3 id="archive-table-count">{{ number_format($stats['archive_table_count'] ?? 0) }}</h3>
                    <p>Archived Transactions</p>
                    <small>In archive table</small>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <h3 id="main-table-size">{{ $stats['main_table_size'] ?? 0 }} MB</h3>
                    <p>Main Table Size</p>
                    <small>Current size</small>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <h3 id="archive-table-size">{{ $stats['archive_table_size'] ?? 0 }} MB</h3>
                    <p>Archive Table Size</p>
                    <small>Total archived</small>
                </div>
            </div>
        </div>

        <!-- Archive Management -->
        <div class="row">
            <div class="col-lg-8">
                <div class="archive-card">
                    <h4><i class="fas fa-archive"></i> Manual Archive Operations</h4>
                    <p class="text-muted">Perform manual archive operations with custom parameters</p>
                    
                    <form id="archiveForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="archiveMonths">Archive Transactions Older Than (Months)</label>
                                    <select class="form-control" id="archiveMonths" name="months">
                                        <option value="6">6 Months</option>
                                        <option value="12" selected>12 Months</option>
                                        <option value="18">18 Months</option>
                                        <option value="24">24 Months</option>
                                        <option value="36">36 Months</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="archiveReason">Reason for Archive</label>
                                    <input type="text" class="form-control" id="archiveReason" name="reason" 
                                           placeholder="Enter reason for archiving (optional)">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-info mr-2" id="dryRunBtn">
                                    <i class="fas fa-eye"></i> Preview Archive
                                </button>
                                <button type="button" class="btn-archive" id="archiveBtn">
                                    <i class="fas fa-archive"></i> Archive Transactions
                                </button>
                                <button type="button" class="btn-optimize ml-2" id="optimizeBtn">
                                    <i class="fas fa-tools"></i> Optimize Table
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Dry Run Results -->
                    <div id="dryRunResults" class="mt-3" style="display: none;">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-eye"></i> Archive Preview Results:</h6>
                            <div id="dryRunContent"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="archive-card">
                    <h4><i class="fas fa-clock"></i> Automatic Archiving</h4>
                    <p class="text-muted">System automatically archives transactions monthly</p>
                    
                    <div class="mb-3">
                        <strong>Next Archive:</strong><br>
                        <small class="text-muted">1st of each month at 2:00 AM</small>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Archive Policy:</strong><br>
                        <small class="text-muted">Transactions older than 12 months</small>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Status:</strong><br>
                        <span class="badge badge-success">Active</span>
                    </div>

                    <a href="{{ route('admin.transaction-archive.view-archived') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-history"></i> View Archived Data
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Archive Batches -->
        <div class="row">
            <div class="col-12">
                <div class="archive-card">
                    <h4><i class="fas fa-history"></i> Recent Archive Batches</h4>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Batch ID</th>
                                    <th>Archived At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentBatches as $batch)
                                    <tr>
                                        <td><code>{{ $batch->archive_batch_id }}</code></td>
                                        <td>{{ \Carbon\Carbon::parse($batch->archived_at)->format('M d, Y H:i:s') }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-info batch-details-btn" 
                                                    data-batch-id="{{ $batch->archive_batch_id }}">
                                                <i class="fas fa-info"></i> Details
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">
                                            No archive batches found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Archive Progress Modal -->
<div class="modal fade" id="archiveProgressModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-archive"></i> Archive in Progress
                </h5>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p id="archiveProgressText">Processing archive operation...</p>
                    <div class="progress-bar">
                        <div class="progress-fill" id="archiveProgressBar" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    // Dry run functionality
    $('#dryRunBtn').on('click', function() {
        const months = $('#archiveMonths').val();
        
        $.ajax({
            url: '{{ route("admin.transaction-archive.dry-run") }}',
            method: 'POST',
            data: {
                months: months,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    let content = `
                        <strong>Archive Preview:</strong><br>
                        • Months: ${data.months}<br>
                        • Cutoff Date: ${new Date(data.cutoff_date).toLocaleString()}<br>
                        • Transactions to Archive: <strong>${data.count_to_archive}</strong><br>
                    `;
                    
                    if (data.count_to_archive > 0) {
                        content += `<br><strong>Sample Transactions:</strong><br>`;
                        data.sample_transactions.forEach(function(tx) {
                            content += `• ID: ${tx.id}, User: ${tx.user?.user_name || 'N/A'}, Amount: ${tx.amount}, Type: ${tx.type}<br>`;
                        });
                    }
                    
                    $('#dryRunContent').html(content);
                    $('#dryRunResults').show();
                }
            },
            error: function(xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || 'Failed to preview archive'));
            }
        });
    });

    // Archive functionality
    $('#archiveBtn').on('click', function() {
        if (!confirm('Are you sure you want to archive old transactions? This operation cannot be undone easily.')) {
            return;
        }

        const months = $('#archiveMonths').val();
        const reason = $('#archiveReason').val() || 'Manual archive by admin';

        $('#archiveProgressModal').modal('show');
        $('#archiveProgressText').text('Starting archive operation...');
        $('#archiveProgressBar').css('width', '10%');

        $.ajax({
            url: '{{ route("admin.transaction-archive.archive") }}',
            method: 'POST',
            data: {
                months: months,
                reason: reason,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#archiveProgressBar').css('width', '100%');
                $('#archiveProgressText').text('Archive completed successfully!');
                
                setTimeout(function() {
                    $('#archiveProgressModal').modal('hide');
                    if (response.success) {
                        alert('Success: ' + response.message);
                        location.reload(); // Refresh to show updated stats
                    } else {
                        alert('Error: ' + response.message);
                    }
                }, 2000);
            },
            error: function(xhr) {
                $('#archiveProgressModal').modal('hide');
                alert('Error: ' + (xhr.responseJSON?.message || 'Archive operation failed'));
            }
        });
    });

    // Optimize functionality
    $('#optimizeBtn').on('click', function() {
        if (!confirm('Are you sure you want to optimize the transactions table?')) {
            return;
        }

        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Optimizing...');

        $.ajax({
            url: '{{ route("admin.transaction-archive.optimize") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert('Success: ' + response.message);
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || 'Optimization failed'));
            },
            complete: function() {
                $('#optimizeBtn').prop('disabled', false).html('<i class="fas fa-tools"></i> Optimize Table');
            }
        });
    });

    // Batch details functionality
    $('.batch-details-btn').on('click', function() {
        const batchId = $(this).data('batch-id');
        
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
                    $('#batchDetailsModal').modal('show');
                }
            },
            error: function(xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || 'Failed to load batch details'));
            }
        });
    });

    // Auto-refresh stats every 30 seconds
    setInterval(function() {
        $.ajax({
            url: '{{ route("admin.transaction-archive.stats") }}',
            method: 'GET',
            success: function(response) {
                $('#main-table-count').text(response.main_table.total_transactions.toLocaleString());
                $('#archive-table-count').text(response.archive_table.total_transactions.toLocaleString());
                $('#main-table-size').text(response.main_table_size + ' MB');
                $('#archive-table-size').text(response.archive_table_size + ' MB');
            }
        });
    }, 30000);
});
</script>
@endsection
