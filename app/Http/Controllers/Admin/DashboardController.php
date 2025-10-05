<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CustomTransaction;
use App\Models\PlaceBet;
use App\Services\CustomWalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $walletService;

    public function __construct(CustomWalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Display the appropriate dashboard based on user role
     */
    public function index()
    {
        $user = auth()->user();
        $userType = $user->type;

        switch ($userType) {
            case 10: // Owner
                return $this->ownerDashboard();
            case 15: // Master
                return $this->masterDashboard();
            case 20: // Agent
                return $this->agentDashboard();
            case 30: // SubAgent
                return $this->subAgentDashboard();
            default:
                abort(403, 'Access denied');
        }
    }

    /**
     * Owner Dashboard - System-wide overview
     */
    private function ownerDashboard()
    {
        $stats = [
            // User Statistics
            'total_users' => User::count(),
            'total_masters' => User::where('type', 15)->count(),
            'total_agents' => User::where('type', 20)->count(),
            'total_subagents' => User::where('type', 30)->count(),
            'total_players' => User::where('type', 40)->count(),
            
            // Financial Statistics
            'total_balance' => User::sum('balance'),
            'total_deposits_today' => CustomTransaction::where('type', 'deposit')
                ->whereDate('created_at', today())
                ->sum('amount'),
            'total_withdrawals_today' => CustomTransaction::where('type', 'withdraw')
                ->whereDate('created_at', today())
                ->sum('amount'),
            'total_transfers_today' => CustomTransaction::where('type', 'transfer')
                ->whereDate('created_at', today())
                ->sum('amount'),
            
            // Game Statistics
            'total_bets_today' => PlaceBet::whereDate('created_at', today())->count(),
            'total_bet_amount_today' => PlaceBet::whereDate('created_at', today())->sum('amount'),
            
            // Recent Activity
            'recent_transactions' => CustomTransaction::with(['user', 'targetUser'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
            'recent_users' => User::where('type', '!=', 10)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
            
            // Chart Data (Last 30 days)
            'daily_transactions' => $this->getDailyTransactionData(30),
            'daily_bets' => $this->getDailyBetData(30),
            'user_growth' => $this->getUserGrowthData(30)
        ];

        return view('admin.dashboard.owner', compact('stats'));
    }

    /**
     * Master Dashboard - Agent and player management
     */
    private function masterDashboard()
    {
        $user = auth()->user();
        
        $stats = [
            // Agent Statistics
            'total_agents' => User::where('type', 20)
                ->where('agent_id', $user->id)
                ->count(),
            'total_subagents' => User::where('type', 30)
                ->whereHas('agent', function($query) use ($user) {
                    $query->where('agent_id', $user->id);
                })
                ->count(),
            'total_players' => User::where('type', 40)
                ->whereHas('agent', function($query) use ($user) {
                    $query->where('agent_id', $user->id);
                })
                ->count(),
            
            // Financial Statistics
            'total_balance' => User::whereHas('agent', function($query) use ($user) {
                $query->where('agent_id', $user->id);
            })->sum('balance'),
            
            'total_deposits_today' => CustomTransaction::where('type', 'deposit')
                ->whereHas('user', function($query) use ($user) {
                    $query->whereHas('agent', function($q) use ($user) {
                        $q->where('agent_id', $user->id);
                    });
                })
                ->whereDate('created_at', today())
                ->sum('amount'),
            
            'total_withdrawals_today' => CustomTransaction::where('type', 'withdraw')
                ->whereHas('user', function($query) use ($user) {
                    $query->whereHas('agent', function($q) use ($user) {
                        $q->where('agent_id', $user->id);
                    });
                })
                ->whereDate('created_at', today())
                ->sum('amount'),
            
            // Game Statistics
            'total_bets_today' => PlaceBet::whereHas('user', function($query) use ($user) {
                $query->whereHas('agent', function($q) use ($user) {
                    $q->where('agent_id', $user->id);
                });
            })->whereDate('created_at', today())->count(),
            
            'total_bet_amount_today' => PlaceBet::whereHas('user', function($query) use ($user) {
                $query->whereHas('agent', function($q) use ($user) {
                    $q->where('agent_id', $user->id);
                });
            })->whereDate('created_at', today())->sum('amount'),
            
            // Recent Activity
            'recent_transactions' => CustomTransaction::with(['user', 'targetUser'])
                ->whereHas('user', function($query) use ($user) {
                    $query->whereHas('agent', function($q) use ($user) {
                        $q->where('agent_id', $user->id);
                    });
                })
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
            
            'recent_agents' => User::where('type', 20)
                ->where('agent_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
            
            // Chart Data
            'daily_transactions' => $this->getMasterDailyTransactionData($user->id, 30),
            'daily_bets' => $this->getMasterDailyBetData($user->id, 30)
        ];

        return view('admin.dashboard.master', compact('stats'));
    }

    /**
     * Agent Dashboard - SubAgent and player management
     */
    private function agentDashboard()
    {
        $user = auth()->user();
        
        $stats = [
            // SubAgent and Player Statistics
            'total_subagents' => User::where('type', 30)
                ->where('agent_id', $user->id)
                ->count(),
            'total_players' => User::where('type', 40)
                ->where('agent_id', $user->id)
                ->count(),
            
            // Financial Statistics
            'total_balance' => User::where('agent_id', $user->id)->sum('balance'),
            
            'total_deposits_today' => CustomTransaction::where('type', 'deposit')
                ->whereHas('user', function($query) use ($user) {
                    $query->where('agent_id', $user->id);
                })
                ->whereDate('created_at', today())
                ->sum('amount'),
            
            'total_withdrawals_today' => CustomTransaction::where('type', 'withdraw')
                ->whereHas('user', function($query) use ($user) {
                    $query->where('agent_id', $user->id);
                })
                ->whereDate('created_at', today())
                ->sum('amount'),
            
            // Game Statistics
            'total_bets_today' => PlaceBet::whereHas('user', function($query) use ($user) {
                $query->where('agent_id', $user->id);
            })->whereDate('created_at', today())->count(),
            
            'total_bet_amount_today' => PlaceBet::whereHas('user', function($query) use ($user) {
                $query->where('agent_id', $user->id);
            })->whereDate('created_at', today())->sum('amount'),
            
            // Recent Activity
            'recent_transactions' => CustomTransaction::with(['user', 'targetUser'])
                ->whereHas('user', function($query) use ($user) {
                    $query->where('agent_id', $user->id);
                })
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
            
            'recent_players' => User::where('type', 40)
                ->where('agent_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
            
            // Chart Data
            'daily_transactions' => $this->getAgentDailyTransactionData($user->id, 30),
            'daily_bets' => $this->getAgentDailyBetData($user->id, 30)
        ];

        return view('admin.dashboard.agent', compact('stats'));
    }

    /**
     * SubAgent Dashboard - Player management
     */
    private function subAgentDashboard()
    {
        $user = auth()->user();
        
        $stats = [
            // Player Statistics
            'total_players' => User::where('type', 40)
                ->where('agent_id', $user->id)
                ->count(),
            
            // Financial Statistics
            'total_balance' => User::where('agent_id', $user->id)->sum('balance'),
            
            'total_deposits_today' => CustomTransaction::where('type', 'deposit')
                ->whereHas('user', function($query) use ($user) {
                    $query->where('agent_id', $user->id);
                })
                ->whereDate('created_at', today())
                ->sum('amount'),
            
            'total_withdrawals_today' => CustomTransaction::where('type', 'withdraw')
                ->whereHas('user', function($query) use ($user) {
                    $query->where('agent_id', $user->id);
                })
                ->whereDate('created_at', today())
                ->sum('amount'),
            
            // Game Statistics
            'total_bets_today' => PlaceBet::whereHas('user', function($query) use ($user) {
                $query->where('agent_id', $user->id);
            })->whereDate('created_at', today())->count(),
            
            'total_bet_amount_today' => PlaceBet::whereHas('user', function($query) use ($user) {
                $query->where('agent_id', $user->id);
            })->whereDate('created_at', today())->sum('amount'),
            
            // Recent Activity
            'recent_transactions' => CustomTransaction::with(['user', 'targetUser'])
                ->whereHas('user', function($query) use ($user) {
                    $query->where('agent_id', $user->id);
                })
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
            
            'recent_players' => User::where('type', 40)
                ->where('agent_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
            
            // Chart Data
            'daily_transactions' => $this->getSubAgentDailyTransactionData($user->id, 30),
            'daily_bets' => $this->getSubAgentDailyBetData($user->id, 30)
        ];

        return view('admin.dashboard.subagent', compact('stats'));
    }

    /**
     * Get daily transaction data for charts
     */
    private function getDailyTransactionData($days = 30)
    {
        $data = CustomTransaction::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(CASE WHEN type = \'deposit\' THEN amount ELSE 0 END) as deposits'),
                DB::raw('SUM(CASE WHEN type = \'withdraw\' THEN amount ELSE 0 END) as withdrawals'),
                DB::raw('SUM(CASE WHEN type = \'transfer\' THEN amount ELSE 0 END) as transfers')
            )
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $data;
    }

    /**
     * Get daily bet data for charts
     */
    private function getDailyBetData($days = 30)
    {
        $data = PlaceBet::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as bet_count'),
                DB::raw('SUM(amount) as bet_amount')
            )
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $data;
    }

    /**
     * Get user growth data for charts
     */
    private function getUserGrowthData($days = 30)
    {
        $data = User::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as user_count')
            )
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $data;
    }

    /**
     * Get master-specific daily transaction data
     */
    private function getMasterDailyTransactionData($masterId, $days = 30)
    {
        $data = CustomTransaction::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(CASE WHEN type = \'deposit\' THEN amount ELSE 0 END) as deposits'),
                DB::raw('SUM(CASE WHEN type = \'withdraw\' THEN amount ELSE 0 END) as withdrawals')
            )
            ->whereHas('user', function($query) use ($masterId) {
                $query->whereHas('agent', function($q) use ($masterId) {
                    $q->where('agent_id', $masterId);
                });
            })
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $data;
    }

    /**
     * Get master-specific daily bet data
     */
    private function getMasterDailyBetData($masterId, $days = 30)
    {
        $data = PlaceBet::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as bet_count'),
                DB::raw('SUM(amount) as bet_amount')
            )
            ->whereHas('user', function($query) use ($masterId) {
                $query->whereHas('agent', function($q) use ($masterId) {
                    $q->where('agent_id', $masterId);
                });
            })
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $data;
    }

    /**
     * Get agent-specific daily transaction data
     */
    private function getAgentDailyTransactionData($agentId, $days = 30)
    {
        $data = CustomTransaction::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(CASE WHEN type = \'deposit\' THEN amount ELSE 0 END) as deposits'),
                DB::raw('SUM(CASE WHEN type = \'withdraw\' THEN amount ELSE 0 END) as withdrawals')
            )
            ->whereHas('user', function($query) use ($agentId) {
                $query->where('agent_id', $agentId);
            })
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $data;
    }

    /**
     * Get agent-specific daily bet data
     */
    private function getAgentDailyBetData($agentId, $days = 30)
    {
        $data = PlaceBet::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as bet_count'),
                DB::raw('SUM(amount) as bet_amount')
            )
            ->whereHas('user', function($query) use ($agentId) {
                $query->where('agent_id', $agentId);
            })
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $data;
    }

    /**
     * Get subagent-specific daily transaction data
     */
    private function getSubAgentDailyTransactionData($subAgentId, $days = 30)
    {
        $data = CustomTransaction::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(CASE WHEN type = \'deposit\' THEN amount ELSE 0 END) as deposits'),
                DB::raw('SUM(CASE WHEN type = \'withdraw\' THEN amount ELSE 0 END) as withdrawals')
            )
            ->whereHas('user', function($query) use ($subAgentId) {
                $query->where('agent_id', $subAgentId);
            })
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $data;
    }

    /**
     * Get subagent-specific daily bet data
     */
    private function getSubAgentDailyBetData($subAgentId, $days = 30)
    {
        $data = PlaceBet::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as bet_count'),
                DB::raw('SUM(amount) as bet_amount')
            )
            ->whereHas('user', function($query) use ($subAgentId) {
                $query->where('agent_id', $subAgentId);
            })
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $data;
    }
}
