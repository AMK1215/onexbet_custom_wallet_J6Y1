<?php

namespace App\Services;

use App\Models\User;
use App\Models\CustomTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PlayerManagementService
{
    /**
     * Safely deactivate a player without affecting other players' balances
     */
    public function deactivatePlayer(User $player, string $reason = ''): bool
    {
        try {
            DB::transaction(function () use ($player, $reason) {
                // 1. Check for active transactions with other players
                $activeTransfers = CustomTransaction::where(function($query) use ($player) {
                    $query->where('user_id', $player->id)
                          ->orWhere('target_user_id', $player->id);
                })->whereNull('deleted_at')->count();

                if ($activeTransfers > 0) {
                    throw new \Exception("Cannot deactivate player with {$activeTransfers} active transactions. Please resolve all pending transactions first.");
                }

                // 2. Check for pending deposits/withdrawals
                $pendingDeposits = \App\Models\DepositRequest::where('user_id', $player->id)
                    ->where('status', 'pending')->count();
                
                $pendingWithdrawals = \App\Models\WithDrawRequest::where('user_id', $player->id)
                    ->where('status', 'pending')->count();

                if ($pendingDeposits > 0 || $pendingWithdrawals > 0) {
                    throw new \Exception("Cannot deactivate player with pending deposits ({$pendingDeposits}) or withdrawals ({$pendingWithdrawals}). Please resolve all pending requests first.");
                }

                // 3. Check for active bets
                $activeBets = \App\Models\TwoDigit\TwoBet::where('user_id', $player->id)
                    ->where('status', 'active')->count();

                if ($activeBets > 0) {
                    throw new \Exception("Cannot deactivate player with {$activeBets} active bets. Please resolve all active bets first.");
                }

                // 4. Safely deactivate player
                $player->update([
                    'status' => 0, // Inactive
                    'deactivated_at' => now(),
                    'deactivation_reason' => $reason
                ]);

                // 5. Log the deactivation
                Log::info('Player safely deactivated', [
                    'player_id' => $player->id,
                    'player_name' => $player->user_name,
                    'reason' => $reason,
                    'deactivated_by' => auth()->id()
                ]);
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Player deactivation failed', [
                'player_id' => $player->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Safely anonymize player data while preserving transaction integrity
     */
    public function anonymizePlayer(User $player, string $reason = ''): bool
    {
        try {
            DB::transaction(function () use ($player, $reason) {
                // 1. Create anonymized identifier
                $anonymizedId = 'ANON_' . $player->id . '_' . time();
                
                // 2. Anonymize personal data
                $player->update([
                    'user_name' => $anonymizedId,
                    'name' => 'Anonymized User',
                    'email' => null,
                    'phone' => null,
                    'profile' => null,
                    'account_name' => null,
                    'account_number' => null,
                    'line_id' => null,
                    'anonymized_at' => now(),
                    'anonymization_reason' => $reason,
                    'status' => 0 // Inactive
                ]);

                // 3. Update transaction records to use anonymized identifier
                CustomTransaction::where('user_id', $player->id)
                    ->update(['user_name_anonymized' => $anonymizedId]);
                
                CustomTransaction::where('target_user_id', $player->id)
                    ->update(['target_user_name_anonymized' => $anonymizedId]);

                // 4. Log the anonymization
                Log::info('Player data anonymized', [
                    'original_player_id' => $player->id,
                    'anonymized_id' => $anonymizedId,
                    'reason' => $reason,
                    'anonymized_by' => auth()->id()
                ]);
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Player anonymization failed', [
                'player_id' => $player->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Archive player data to separate archive tables
     */
    public function archivePlayer(User $player, string $reason = ''): bool
    {
        try {
            DB::transaction(function () use ($player, $reason) {
                // 1. Archive player data
                DB::table('archived_players')->insert([
                    'original_id' => $player->id,
                    'user_name' => $player->user_name,
                    'name' => $player->name,
                    'email' => $player->email,
                    'phone' => $player->phone,
                    'balance' => $player->balance,
                    'agent_id' => $player->agent_id,
                    'type' => $player->type,
                    'status' => $player->status,
                    'created_at' => $player->created_at,
                    'updated_at' => $player->updated_at,
                    'archived_at' => now(),
                    'archive_reason' => $reason,
                    'archived_by' => auth()->id()
                ]);

                // 2. Archive all transactions
                $transactions = CustomTransaction::where('user_id', $player->id)
                    ->orWhere('target_user_id', $player->id)
                    ->get();

                foreach ($transactions as $transaction) {
                    DB::table('archived_custom_transactions')->insert([
                        'original_id' => $transaction->id,
                        'user_id' => $transaction->user_id,
                        'target_user_id' => $transaction->target_user_id,
                        'amount' => $transaction->amount,
                        'type' => $transaction->type,
                        'transaction_name' => $transaction->transaction_name,
                        'old_balance' => $transaction->old_balance,
                        'new_balance' => $transaction->new_balance,
                        'meta' => $transaction->meta,
                        'uuid' => $transaction->uuid,
                        'confirmed' => $transaction->confirmed,
                        'created_at' => $transaction->created_at,
                        'updated_at' => $transaction->updated_at,
                        'archived_at' => now()
                    ]);
                }

                // 3. Soft delete the player (preserve referential integrity)
                $player->delete();

                // 4. Log the archiving
                Log::info('Player archived', [
                    'player_id' => $player->id,
                    'player_name' => $player->user_name,
                    'transactions_archived' => $transactions->count(),
                    'reason' => $reason,
                    'archived_by' => auth()->id()
                ]);
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Player archiving failed', [
                'player_id' => $player->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get player deletion impact analysis
     */
    public function getDeletionImpact(User $player): array
    {
        $impact = [
            'player_id' => $player->id,
            'player_name' => $player->user_name,
            'current_balance' => $player->balance,
            'warnings' => [],
            'affected_players' => [],
            'transaction_counts' => []
        ];

        // Check for active transactions
        $activeTransactions = CustomTransaction::where(function($query) use ($player) {
            $query->where('user_id', $player->id)
                  ->orWhere('target_user_id', $player->id);
        })->whereNull('deleted_at')->count();

        $impact['transaction_counts']['active'] = $activeTransactions;

        if ($activeTransactions > 0) {
            $impact['warnings'][] = "Player has {$activeTransactions} active transactions that would be deleted";
        }

        // Check for affected other players
        $affectedPlayers = CustomTransaction::where(function($query) use ($player) {
            $query->where('user_id', $player->id)
                  ->orWhere('target_user_id', $player->id);
        })->with(['user', 'targetUser'])
        ->whereNull('deleted_at')
        ->get();

        $uniquePlayers = collect();
        foreach ($affectedPlayers as $transaction) {
            if ($transaction->user_id !== $player->id) {
                $uniquePlayers->push($transaction->user);
            }
            if ($transaction->target_user_id !== $player->id) {
                $uniquePlayers->push($transaction->targetUser);
            }
        }

        $impact['affected_players'] = $uniquePlayers->unique('id')->values()->toArray();

        if (count($impact['affected_players']) > 0) {
            $impact['warnings'][] = "Deletion would affect " . count($impact['affected_players']) . " other players";
        }

        // Check for pending requests
        $pendingDeposits = \App\Models\DepositRequest::where('user_id', $player->id)
            ->where('status', 'pending')->count();
        
        $pendingWithdrawals = \App\Models\WithDrawRequest::where('user_id', $player->id)
            ->where('status', 'pending')->count();

        $impact['transaction_counts']['pending_deposits'] = $pendingDeposits;
        $impact['transaction_counts']['pending_withdrawals'] = $pendingWithdrawals;

        if ($pendingDeposits > 0) {
            $impact['warnings'][] = "Player has {$pendingDeposits} pending deposit requests";
        }

        if ($pendingWithdrawals > 0) {
            $impact['warnings'][] = "Player has {$pendingWithdrawals} pending withdrawal requests";
        }

        return $impact;
    }
}
