# Webhook API Documentation - Custom Wallet System

## Overview

This documentation covers the webhook API endpoints that integrate with our custom wallet system. The custom wallet provides high-performance, direct database operations for gaming platform balance transactions.

## Table of Contents

1. [System Architecture](#system-architecture)
2. [Authentication & Security](#authentication--security)
3. [API Endpoints](#api-endpoints)
4. [Request/Response Formats](#requestresponse-formats)
5. [Currency Handling](#currency-handling)
6. [Error Codes](#error-codes)
7. [Testing](#testing)
8. [Troubleshooting](#troubleshooting)

## System Architecture

### Custom Wallet Components

- **CustomWalletService**: Core service for balance operations
- **CustomTransaction**: Transaction logging model
- **User Model**: Direct balance storage in `users.balance` column
- **Webhook Controllers**: API endpoints for external gaming providers

### Key Features

- **Direct Database Operations**: No ORM overhead for balance updates
- **Row-Level Locking**: Prevents race conditions in concurrent transactions
- **Atomic Transactions**: All operations are wrapped in database transactions
- **Real-time Balance Updates**: Immediate balance reflection after operations
- **Comprehensive Logging**: Full transaction audit trail

## Authentication & Security

### Signature Validation

All webhook requests must include a valid MD5 signature:

```php
$signature = md5($operator_code . $request_time . $action . $secret_key);
```

**Parameters:**
- `operator_code`: Your operator identifier
- `request_time`: Unix timestamp
- `action`: API action (getbalance, deposit, withdraw)
- `secret_key`: Your secret key from configuration

### Security Headers

```http
Content-Type: application/json
User-Agent: YourApp/1.0
```

## API Endpoints

### Base URL
```
https://yourdomain.com/api/v1/api/seamless/
```

### 1. Get Balance

**Endpoint:** `GET/POST /balance`

**Purpose:** Retrieve current balance for one or more members

**Request Format:**
```json
{
    "batch_requests": [
        {
            "member_account": "PLAYER0101",
            "product_code": 1006
        },
        {
            "member_account": "PLAYER0201", 
            "product_code": 1009
        }
    ],
    "currency": "IDR",
    "operator_code": "W2B1",
    "request_time": 1759489243,
    "sign": "cd8e9413dc5df62f57f52913ac60cb2e"
}
```

**Response Format:**
```json
{
    "code": 0,
    "message": "",
    "data": [
        {
            "member_account": "PLAYER0101",
            "product_code": 1006,
            "balance": 50000.0,
            "code": 0,
            "message": "Success"
        },
        {
            "member_account": "PLAYER0201",
            "product_code": 1009,
            "balance": 50000.0,
            "code": 0,
            "message": "Success"
        }
    ]
}
```

### 2. Deposit

**Endpoint:** `POST /deposit`

**Purpose:** Add funds to member account (wins, bonuses, etc.)

**Request Format:**
```json
{
    "batch_requests": [
        {
            "game_type": "SLOT",
            "member_account": "PLAYER0101",
            "product_code": 1006,
            "transactions": [
                {
                    "action": "SETTLED",
                    "amount": "10",
                    "bet_amount": "10",
                    "game_code": "testgame001",
                    "id": "1759584133909_2",
                    "payload": {
                        "amount": 10,
                        "provider_tx_id": "1759584133909",
                        "provider_username": "test_provider_username123",
                        "roundDetails": "Spin",
                        "username": "testusername"
                    },
                    "prize_amount": "10",
                    "round_id": "testRoundID_1759584133909",
                    "settled_at": 1759584133,
                    "tip_amount": "0",
                    "valid_bet_amount": "10",
                    "wager_code": "1759584133909",
                    "wager_status": "SETTLED"
                }
            ]
        }
    ],
    "currency": "IDR",
    "operator_code": "W2B1",
    "request_time": 1759489243,
    "sign": "b6215d7f7afffef0f605c2d2aae195af"
}
```

**Response Format:**
```json
{
    "code": 0,
    "message": "",
    "data": [
        {
            "member_account": "PLAYER0101",
            "product_code": 1006,
            "before_balance": 49990.0,
            "balance": 50000.0,
            "code": 0,
            "message": ""
        }
    ]
}
```

### 3. Withdraw

**Endpoint:** `POST /withdraw`

**Purpose:** Deduct funds from member account (bets, fees, etc.)

**Request Format:**
```json
{
    "batch_requests": [
        {
            "game_type": "SLOT",
            "member_account": "PLAYER0101",
            "product_code": 1006,
            "transactions": [
                {
                    "action": "BET",
                    "amount": "-10",
                    "bet_amount": "10",
                    "game_code": "testgame001",
                    "id": "1759584133909_1",
                    "payload": {
                        "amount": 10,
                        "provider_tx_id": "1759584133909",
                        "provider_username": "test_provider_username123",
                        "roundDetails": "Spin",
                        "username": "testusername"
                    },
                    "prize_amount": "0",
                    "round_id": "testRoundID_1759584133909",
                    "settled_at": 0,
                    "tip_amount": "0",
                    "valid_bet_amount": "10",
                    "wager_code": "1759584133909",
                    "wager_status": "BET"
                }
            ]
        }
    ],
    "currency": "IDR",
    "operator_code": "W2B1",
    "request_time": 1759489243,
    "sign": "07d7d14b31677ef95513b3153a9c1a83"
}
```

**Response Format:**
```json
{
    "code": 0,
    "message": "",
    "data": [
        {
            "member_account": "PLAYER0101",
            "product_code": 1006,
            "before_balance": 50000.0,
            "balance": 49990.0,
            "code": 0,
            "message": "Transaction processed successfully",
            "transaction_id": "1759584133909_1"
        }
    ]
}
```

## Request/Response Formats

### Common Request Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `batch_requests` | Array | Yes | Array of member requests |
| `currency` | String | Yes | Currency code (IDR, IDR2, etc.) |
| `operator_code` | String | Yes | Your operator identifier |
| `request_time` | Integer | Yes | Unix timestamp |
| `sign` | String | Yes | MD5 signature |

### Common Response Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `code` | Integer | Overall response code (0 = success) |
| `message` | String | Overall response message |
| `data` | Array | Array of individual results |

### Individual Result Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `member_account` | String | Member username |
| `product_code` | Integer | Product identifier |
| `balance` | Float | Current balance after operation |
| `before_balance` | Float | Balance before operation (deposit/withdraw only) |
| `code` | Integer | Individual result code |
| `message` | String | Individual result message |
| `transaction_id` | String | Transaction identifier (withdraw only) |

## Currency Handling

### Supported Currencies

**Regular Currencies (2 decimal places):**
- IDR, MMK, VND, INR, MYR, AOA, EUR, PHP, THB, JPY, COP, IRR, CHF, USD, MXN, ETB, CAD, BRL, NGN, KES, KRW, TND, LBP, BDT, CZK

**Special Currencies (4 decimal places, 1:1000 conversion):**
- IDR2, KRW2, MMK2, VND2, LAK2, KHR2

### Currency Conversion Logic

```php
// Special currencies are divided by 1000 for display
if (in_array($currency, ['IDR2', 'KRW2', 'MMK2', 'VND2', 'LAK2', 'KHR2'])) {
    $displayBalance = $rawBalance / 1000;
    $displayBalance = round($displayBalance, 4);
} else {
    $displayBalance = round($rawBalance, 2);
}
```

### Examples

| Currency | Raw Balance | Display Balance | Decimal Places |
|----------|-------------|-----------------|----------------|
| IDR | 50000 | 50000.00 | 2 |
| IDR2 | 50000 | 50.0000 | 4 |
| MMK2 | 1000000 | 1000.0000 | 4 |

## Error Codes

### Success Codes
- `0`: Success

### Error Codes
- `999`: Invalid Currency
- `1000`: Member not found
- `1001`: Insufficient balance
- `1004`: Incorrect Signature
- `1005`: Duplicate transaction
- `1006`: Invalid amount
- `1007`: Transaction failed
- `1008`: Member wallet missing
- `1009`: Unsupported action type

### Error Response Example
```json
{
    "code": 0,
    "message": "",
    "data": [
        {
            "member_account": "PLAYER0101",
            "product_code": 1006,
            "balance": 0.0,
            "code": 1001,
            "message": "Insufficient balance"
        }
    ]
}
```

## Testing

### Test Environment Setup

1. **Configure Test Users:**
   ```php
   // Ensure test users exist with sufficient balance
   User::create([
       'user_name' => 'PLAYER0101',
       'name' => 'Test Player 1',
       'email' => 'player0101@test.com',
       'password' => bcrypt('password'),
       'type' => 'Player',
       'balance' => 50000.00
   ]);
   ```

2. **Generate Test Signatures:**
   ```php
   $signature = md5('W2B1' . time() . 'getbalance' . 'your_secret_key');
   ```

### Test Cases

#### 1. Get Balance Tests
- ✅ Valid request with existing members
- ✅ Invalid currency
- ✅ Invalid signature
- ✅ Non-existent members
- ✅ Special currency conversion (IDR2, MMK2, etc.)

#### 2. Deposit Tests
- ✅ Valid deposit transaction
- ✅ Duplicate transaction handling
- ✅ Invalid member
- ✅ Invalid currency
- ✅ Invalid signature

#### 3. Withdraw Tests
- ✅ Valid withdraw transaction
- ✅ Insufficient balance
- ✅ Invalid action type
- ✅ Invalid member
- ✅ Invalid currency
- ✅ Invalid signature

### Test Script Example

```php
<?php
// test_webhook_api.php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Services\CustomWalletService;
use App\Enums\TransactionName;

// Test user setup
$user = User::firstOrCreate(
    ['user_name' => 'PLAYER0101'],
    ['name' => 'Test Player', 'email' => 'player0101@test.com', 'password' => bcrypt('password'), 'type' => 'Player', 'balance' => 50000.00]
);

$customWalletService = app(CustomWalletService::class);

// Test withdraw
echo "Testing withdraw...\n";
$withdrawSuccess = $customWalletService->withdraw($user, 10.0, TransactionName::Bet, ['test' => 'withdraw']);
$user->refresh();
echo "Withdraw result: " . ($withdrawSuccess ? 'SUCCESS' : 'FAILED') . "\n";
echo "New balance: {$user->balance}\n\n";

// Test deposit
echo "Testing deposit...\n";
$depositSuccess = $customWalletService->deposit($user, 10.0, TransactionName::Win, ['test' => 'deposit']);
$user->refresh();
echo "Deposit result: " . ($depositSuccess ? 'SUCCESS' : 'FAILED') . "\n";
echo "New balance: {$user->balance}\n\n";
```

## Troubleshooting

### Common Issues

#### 1. "Balance is incorrect" Error
**Cause:** Webhook controller not refreshing user model after balance update
**Solution:** Ensure `$user->refresh()` is called after CustomWalletService operations

#### 2. "Insufficient balance" Error
**Cause:** Member doesn't have enough balance for the transaction
**Solution:** Check member's current balance and transaction amount

#### 3. "Invalid signature" Error
**Cause:** Incorrect signature calculation
**Solution:** Verify signature generation matches the expected format

#### 4. "Member not found" Error
**Cause:** Member account doesn't exist in the system
**Solution:** Ensure member is properly created and activated

### Debug Logging

Enable debug logging to trace issues:

```php
// In webhook controllers
Log::debug('Webhook Request', ['request' => $request->all()]);
Log::debug('Balance Update', [
    'user_id' => $user->id,
    'old_balance' => $oldBalance,
    'new_balance' => $newBalance,
    'amount' => $amount
]);
```

### Log Files Location
```
storage/logs/laravel.log
```

### Performance Monitoring

Monitor these metrics:
- Response time for each endpoint
- Database transaction duration
- Balance update success rate
- Error rate by error code

## Integration Guide

### Step 1: Configure Your System
1. Set up your operator code and secret key
2. Configure allowed currencies
3. Set up test users with initial balances

### Step 2: Implement Signature Generation
```php
function generateSignature($operatorCode, $requestTime, $action, $secretKey) {
    return md5($operatorCode . $requestTime . $action . $secretKey);
}
```

### Step 3: Handle Responses
```php
// Always check individual result codes
foreach ($response['data'] as $result) {
    if ($result['code'] === 0) {
        // Success - process the result
        $balance = $result['balance'];
    } else {
        // Error - handle the error
        $errorMessage = $result['message'];
    }
}
```

### Step 4: Implement Retry Logic
```php
// For failed transactions, implement retry logic
if ($result['code'] === 1007) { // Transaction failed
    // Retry the request after a delay
    sleep(1);
    // Retry logic here
}
```

## Support

For technical support or questions about the webhook API:

1. Check the logs in `storage/logs/laravel.log`
2. Verify your signature generation
3. Ensure test users have sufficient balance
4. Contact the development team with specific error codes and request details

---

**Last Updated:** October 4, 2025  
**Version:** 1.0  
**Custom Wallet System:** Active
