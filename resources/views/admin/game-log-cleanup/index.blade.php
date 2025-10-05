@extends('layouts.master')

@section('title', 'Game Log Cleanup Management')

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
    .cleanup-card {
        border: 1px solid #dee2e6;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        background: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .btn-cleanup {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        border: none;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        font-weight: bold;
    }
    .btn-cleanup:hover {
        background: linear-gradient(135deg, #c82333 0%, #a71e2a 100%);
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
    .alert-cleanup {
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
        background: linear-gradient(90deg, #dc3545, #c82333);
        transition: width 0.3s ease;
    }
</style>
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Game Log Cleanup Management</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.logs.index') }}">System Logs</a></li>
                    <li class="breadcrumb-item active">Game Log Cleanup</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Warning Alert -->
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Important:</strong> This tool deletes game logs from the place_bets table permanently. 
            This operation cannot be undone. Use with caution and always preview before deleting.
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <h3 id="total-bets">{{ number_format($stats['total_bets'] ?? 0) }}</h3>
                    <p>Total Game Logs</p>
                    <small>In place_bets table</small>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <h3 id="bets-15-days">{{ number_format($stats['bets_older_than_15_days'] ?? 0) }}</h3>
                    <p>Logs Older Than 15 Days</p>
                    <small>Can be cleaned up</small>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <h3 id="bets-30-days">{{ number_format($stats['bets_older_than_30_days'] ?? 0) }}</h3>
                    <p>Logs Older Than 30 Days</p>
                    <small>Very old logs</small>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stats-card">
                    <h3 id="table-size">{{ $stats['table_size'] ?? 0 }} MB</h3>
                    <p>Table Size</p>
                    <small>Current size</small>
                </div>
            </div>
        </div>

        <!-- Cleanup Management -->
        <div class="row">
            <div class="col-lg-8">
                <div class="cleanup-card">
                    <h4><i class="fas fa-trash-alt"></i> Manual Cleanup Operations</h4>
                    <p class="text-muted">Delete old game logs with custom parameters</p>
                    
                    <form id="cleanupForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="cleanupDays">Delete Game Logs Older Than (Days)</label>
                                    <select class="form-control" id="cleanupDays" name="days">
                                        <option value="7">7 Days</option>
                                        <option value="15" selected>15 Days</option>
                                        <option value="30">30 Days</option>
                                        <option value="60">60 Days</option>
                                        <option value="90">90 Days</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="cleanupReason">Reason for Cleanup</label>
                                    <input type="text" class="form-control" id="cleanupReason" name="reason" 
                                           placeholder="Enter reason for cleanup (optional)">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-info mr-2" id="previewBtn">
                                    <i class="fas fa-eye"></i> Preview Cleanup
                                </button>
                                <button type="button" class="btn-cleanup" id="cleanupBtn">
                                    <i class="fas fa-trash-alt"></i> Delete Game Logs
                                </button>
                                <button type="button" class="btn-optimize ml-2" id="optimizeBtn">
                                    <i class="fas fa-tools"></i> Optimize Table
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Preview Results -->
                    <div id="previewResults" class="mt-3" style="display: none;">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-eye"></i> Cleanup Preview Results:</h6>
                            <div id="previewContent"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="cleanup-card">
                    <h4><i class="fas fa-clock"></i> Automatic Cleanup</h4>
                    <p class="text-muted">System automatically cleans up game logs daily</p>
                    
                    <div class="mb-3">
                        <strong>Next Cleanup:</strong><br>
                        <small class="text-muted">Daily at 3:00 AM</small>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Cleanup Policy:</strong><br>
                        <small class="text-muted">Game logs older than 15 days</small>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Status:</strong><br>
                        <span class="badge badge-success">Active</span>
                    </div>

                    <div class="mb-3">
                        <strong>Table Info:</strong><br>
                        <small class="text-muted">Oldest: {{ $stats['oldest_bet'] ?? 'N/A' }}</small><br>
                        <small class="text-muted">Newest: {{ $stats['newest_bet'] ?? 'N/A' }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Cleanups -->
        <div class="row">
            <div class="col-12">
                <div class="cleanup-card">
                    <h4><i class="fas fa-history"></i> Recent Cleanup Operations</h4>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Deleted Count</th>
                                    <th>Days Old</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentCleanups as $cleanup)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($cleanup['timestamp'])->format('M d, Y H:i:s') }}</td>
                                        <td>{{ number_format($cleanup['deleted_count']) }}</td>
                                        <td>{{ $cleanup['days_old'] }} days</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">
                                            No recent cleanup operations found
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

<!-- Cleanup Progress Modal -->
<div class="modal fade" id="cleanupProgressModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-trash-alt"></i> Cleanup in Progress
                </h5>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <div class="spinner-border text-danger mb-3" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p id="cleanupProgressText">Processing cleanup operation...</p>
                    <div class="progress-bar">
                        <div class="progress-fill" id="cleanupProgressBar" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    // Preview functionality
    $('#previewBtn').on('click', function() {
        const days = $('#cleanupDays').val();
        
        $.ajax({
            url: '{{ route("admin.game-log-cleanup.preview") }}',
            method: 'POST',
            data: {
                days: days,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    let content = `
                        <strong>Cleanup Preview:</strong><br>
                        • Days: ${data.days_old}<br>
                        • Cutoff Date: ${new Date(data.cutoff_date).toLocaleString()}<br>
                        • Game Logs to Delete: <strong>${data.count_to_delete}</strong><br>
                    `;
                    
                    if (data.count_to_delete > 0) {
                        content += `<br><strong>Sample Game Logs:</strong><br>`;
                        data.sample_bets.forEach(function(bet) {
                            content += `• ID: ${bet.id}, User: ${bet.user?.user_name || 'N/A'}, Game: ${bet.game_name || 'N/A'}, Amount: ${bet.amount || 0}<br>`;
                        });
                    }
                    
                    $('#previewContent').html(content);
                    $('#previewResults').show();
                }
            },
            error: function(xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || 'Failed to preview cleanup'));
            }
        });
    });

    // Cleanup functionality
    $('#cleanupBtn').on('click', function() {
        if (!confirm('⚠️ WARNING: This will permanently delete game logs from the database. This operation cannot be undone!\n\nAre you sure you want to proceed?')) {
            return;
        }

        const days = $('#cleanupDays').val();
        const reason = $('#cleanupReason').val() || 'Manual cleanup by admin';

        $('#cleanupProgressModal').modal('show');
        $('#cleanupProgressText').text('Starting cleanup operation...');
        $('#cleanupProgressBar').css('width', '10%');

        $.ajax({
            url: '{{ route("admin.game-log-cleanup.cleanup") }}',
            method: 'POST',
            data: {
                days: days,
                reason: reason,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#cleanupProgressBar').css('width', '100%');
                $('#cleanupProgressText').text('Cleanup completed successfully!');
                
                setTimeout(function() {
                    $('#cleanupProgressModal').modal('hide');
                    if (response.success) {
                        alert('Success: ' + response.message);
                        location.reload(); // Refresh to show updated stats
                    } else {
                        alert('Error: ' + response.message);
                    }
                }, 2000);
            },
            error: function(xhr) {
                $('#cleanupProgressModal').modal('hide');
                alert('Error: ' + (xhr.responseJSON?.message || 'Cleanup operation failed'));
            }
        });
    });

    // Optimize functionality
    $('#optimizeBtn').on('click', function() {
        if (!confirm('Are you sure you want to optimize the place_bets table?')) {
            return;
        }

        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Optimizing...');

        $.ajax({
            url: '{{ route("admin.game-log-cleanup.optimize") }}',
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

    // Auto-refresh stats every 30 seconds
    setInterval(function() {
        $.ajax({
            url: '{{ route("admin.game-log-cleanup.stats") }}',
            method: 'GET',
            success: function(response) {
                $('#total-bets').text(response.total_bets.toLocaleString());
                $('#bets-15-days').text(response.bets_older_than_15_days.toLocaleString());
                $('#bets-30-days').text(response.bets_older_than_30_days.toLocaleString());
                $('#table-size').text(response.table_size + ' MB');
            }
        });
    }, 30000);
});
</script>
@endsection
