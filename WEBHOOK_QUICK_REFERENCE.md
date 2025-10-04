# Webhook API Quick Reference - Custom Wallet

## 🚀 Quick Start

### Base URL
```
https://yourdomain.com/api/v1/api/seamless/
```

### Authentication
```php
$signature = md5($operator_code . $request_time . $action . $secret_key);
```

## 📋 API Endpoints

### 1. Get Balance
**URL:** `/balance`  
**Method:** `POST`

```json
{
    "batch_requests": [
        {"member_account": "PLAYER0101", "product_code": 1006}
    ],
    "currency": "IDR",
    "operator_code": "W2B1", 
    "request_time": 1759489243,
    "sign": "your_signature"
}
```

### 2. Deposit
**URL:** `/deposit`  
**Method:** `POST`

```json
{
    "batch_requests": [
        {
            "member_account": "PLAYER0101",
            "product_code": 1006,
            "transactions": [
                {
                    "action": "SETTLED",
                    "amount": "10",
                    "id": "unique_transaction_id",
                    "wager_code": "wager_123"
                }
            ]
        }
    ],
    "currency": "IDR",
    "operator_code": "W2B1",
    "request_time": 1759489243,
    "sign": "your_signature"
}
```

### 3. Withdraw
**URL:** `/withdraw`  
**Method:** `POST`

```json
{
    "batch_requests": [
        {
            "member_account": "PLAYER0101",
            "product_code": 1006,
            "transactions": [
                {
                    "action": "BET",
                    "amount": "-10",
                    "id": "unique_transaction_id",
                    "wager_code": "wager_123"
                }
            ]
        }
    ],
    "currency": "IDR",
    "operator_code": "W2B1",
    "request_time": 1759489243,
    "sign": "your_signature"
}
```

## 💰 Currency Support

### Regular Currencies (2 decimals)
- IDR, MMK, VND, USD, EUR, etc.

### Special Currencies (4 decimals, ÷1000)
- IDR2, KRW2, MMK2, VND2, LAK2, KHR2

**Example:**
- Raw balance: 50000
- IDR display: 50000.00
- IDR2 display: 50.0000

## ✅ Success Response Format

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
        }
    ]
}
```

## ❌ Error Codes

| Code | Message | Description |
|------|---------|-------------|
| 0 | Success | Operation successful |
| 999 | Invalid Currency | Currency not supported |
| 1000 | Member not found | User doesn't exist |
| 1001 | Insufficient balance | Not enough funds |
| 1004 | Incorrect Signature | Invalid signature |
| 1005 | Duplicate transaction | Transaction already processed |

## 🔧 Testing

### Test User Setup
```php
User::create([
    'user_name' => 'PLAYER0101',
    'balance' => 50000.00,
    'type' => 'Player'
]);
```

### Generate Test Signature
```php
$signature = md5('W2B1' . time() . 'getbalance' . 'your_secret_key');
```

## 🐛 Common Issues

### "Balance is incorrect"
- ✅ **Fixed:** Webhook controllers now refresh user model after balance updates

### "Insufficient balance"
- Check member's current balance
- Verify transaction amount

### "Invalid signature"
- Verify signature generation formula
- Check operator_code, request_time, action, secret_key

### "Member not found"
- Ensure member exists in database
- Check member_account spelling

## 📊 Response Examples

### Get Balance Success
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
        }
    ]
}
```

### Deposit Success
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

### Withdraw Success
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

## 🎯 Key Features

- ✅ **High Performance:** Direct database operations
- ✅ **Real-time Updates:** Immediate balance reflection
- ✅ **Atomic Transactions:** All operations are safe
- ✅ **Comprehensive Logging:** Full audit trail
- ✅ **Currency Support:** Multiple currencies with proper conversion
- ✅ **Error Handling:** Detailed error codes and messages

## 📞 Support

- **Logs:** `storage/logs/laravel.log`
- **Debug:** Enable debug logging in webhook controllers
- **Test:** Use provided test scripts for validation

---

**Custom Wallet System - Production Ready** 🚀
