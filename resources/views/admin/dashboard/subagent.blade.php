@extends('layouts.master')

@section('title', 'SubAgent Dashboard')

@section('style')
<style>
    .stats-card {
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
        color: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    .stats-card:hover {
        transform: translateY(-5px);
    }
    .stats-card h3 {
        margin: 0;
        font-size: 2.5rem;
        font-weight: bold;
    }
    .stats-card p {
        margin: 5px 0 0 0;
        opacity: 0.9;
        font-size: 1.1rem;
    }
    .stats-card small {
        opacity: 0.8;
    }
    .chart-container {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .recent-activity {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .activity-item {
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .activity-item:last-child {
        border-bottom: none;
    }
    .badge-custom {
        font-size: 0.8rem;
        padding: 4px 8px;
    }
</style>
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-user-tag text-warning"></i> SubAgent Dashboard
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item active">SubAgent Dashboard</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Welcome Message -->
        <div class="alert alert-warning">
            <i class="fas fa-info-circle"></i>
            <strong>Welcome, {{ auth()->user()->user_name }}!</strong> 
            You are viewing the SubAgent Dashboard with access to your players.
        </div>

        <!-- User Statistics -->
        <div class="row">
            <div class="col-lg-6 col-md-6">
                <div class="stats-card">
                    <h3>{{ number_format($stats['total_players']) }}</h3>
                    <p>Direct Players</p>
                    <small>Players under you</small>
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="stats-card" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                    <h3>{{ number_format($stats['total_balance'], 2) }}</h3>
                    <p>Player Balance</p>
                    <small>Total balance of your players</small>
                </div>
            </div>
        </div>

        <!-- Financial Statistics -->
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="stats-card" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                    <h3>{{ number_format($stats['total_deposits_today'], 2) }}</h3>
                    <p>Deposits Today</p>
                    <small>Player deposits</small>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="stats-card" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
                    <h3>{{ number_format($stats['total_withdrawals_today'], 2) }}</h3>
                    <p>Withdrawals Today</p>
                    <small>Player withdrawals</small>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="stats-card" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
                    <h3>{{ number_format($stats['total_deposits_today'] - $stats['total_withdrawals_today'], 2) }}</h3>
                    <p>Net Flow Today</p>
                    <small>Deposits - Withdrawals</small>
                </div>
            </div>
        </div>

        <!-- Game Statistics -->
        <div class="row">
            <div class="col-lg-6 col-md-6">
                <div class="stats-card" style="background: linear-gradient(135deg, #6f42c1 0%, #5a32a3 100%);">
                    <h3>{{ number_format($stats['total_bets_today']) }}</h3>
                    <p>Player Bets Today</p>
                    <small>Total bets placed by players</small>
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="stats-card" style="background: linear-gradient(135deg, #fd7e14 0%, #e8650e 100%);">
                    <h3>{{ number_format($stats['total_bet_amount_today'], 2) }}</h3>
                    <p>Player Bet Amount</p>
                    <small>Total amount wagered</small>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row">
            <div class="col-lg-6">
                <div class="chart-container">
                    <h5><i class="fas fa-chart-line"></i> Daily Player Transactions (Last 30 Days)</h5>
                    <canvas id="transactionChart" height="300"></canvas>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="chart-container">
                    <h5><i class="fas fa-chart-bar"></i> Daily Player Bets (Last 30 Days)</h5>
                    <canvas id="betChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Activity Row -->
        <div class="row">
            <div class="col-lg-6">
                <div class="recent-activity">
                    <h5><i class="fas fa-history"></i> Recent Player Transactions</h5>
                    @forelse($stats['recent_transactions'] as $transaction)
                        <div class="activity-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $transaction->user->user_name ?? 'N/A' }}</strong>
                                    @if($transaction->targetUser)
                                        <i class="fas fa-arrow-right mx-2"></i>
                                        <strong>{{ $transaction->targetUser->user_name }}</strong>
                                    @endif
                                    <br>
                                    <small class="text-muted">{{ $transaction->transaction_name }}</small>
                                </div>
                                <div class="text-right">
                                    <span class="badge badge-{{ $transaction->type === 'deposit' ? 'success' : ($transaction->type === 'withdraw' ? 'danger' : 'info') }} badge-custom">
                                        {{ ucfirst($transaction->type) }}
                                    </span>
                                    <br>
                                    <strong>{{ number_format($transaction->amount, 2) }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $transaction->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center">No recent transactions</p>
                    @endforelse
                </div>
            </div>
            <div class="col-lg-6">
                <div class="recent-activity">
                    <h5><i class="fas fa-users"></i> Recent Players</h5>
                    @forelse($stats['recent_players'] as $player)
                        <div class="activity-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $player->user_name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $player->name ?? 'N/A' }}</small>
                                </div>
                                <div class="text-right">
                                    <span class="badge badge-info badge-custom">Player</span>
                                    <br>
                                    <strong>{{ number_format($player->balance, 2) }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $player->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center">No players yet</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-bolt"></i> Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <a href="{{ route('admin.player.index') }}" class="btn btn-warning btn-block">
                                    <i class="fas fa-user"></i> Manage Players
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="{{ route('admin.logs.index') }}" class="btn btn-info btn-block">
                                    <i class="fas fa-clipboard-list"></i> View Logs
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="{{ route('admin.profile_index', auth()->user()->id) }}" class="btn btn-success btn-block">
                                    <i class="fas fa-user-edit"></i> Edit Profile
                                </a>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Transaction Chart
    const transactionCtx = document.getElementById('transactionChart').getContext('2d');
    const transactionData = @json($stats['daily_transactions']);
    
    new Chart(transactionCtx, {
        type: 'line',
        data: {
            labels: transactionData.map(item => item.date),
            datasets: [
                {
                    label: 'Deposits',
                    data: transactionData.map(item => parseFloat(item.deposits)),
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Withdrawals',
                    data: transactionData.map(item => parseFloat(item.withdrawals)),
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Bet Chart
    const betCtx = document.getElementById('betChart').getContext('2d');
    const betData = @json($stats['daily_bets']);
    
    new Chart(betCtx, {
        type: 'bar',
        data: {
            labels: betData.map(item => item.date),
            datasets: [
                {
                    label: 'Bet Amount',
                    data: betData.map(item => parseFloat(item.bet_amount)),
                    backgroundColor: 'rgba(255, 193, 7, 0.8)',
                    borderColor: '#ffc107',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endsection
