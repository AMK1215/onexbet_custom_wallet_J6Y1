# Admin Logging System Documentation

## Overview

The Admin Logging System provides comprehensive monitoring and analysis capabilities for the custom wallet system. It allows administrators to view, filter, and analyze all system activities including custom wallet transactions, webhook API calls, system logs, and user activities.

## Features

### 🎯 **Core Functionality**
- **Real-time Monitoring**: Live statistics and activity tracking
- **Advanced Filtering**: Multiple filter options for all log types
- **Detailed Views**: Comprehensive transaction and log details
- **Export Capabilities**: CSV export for transaction data
- **Search & Analysis**: Powerful search across all log types
- **User Activity Tracking**: Monitor user behavior and transactions

### 📊 **Log Types**

#### 1. **Custom Wallet Transactions**
- **Purpose**: Track all custom wallet operations (deposits, withdrawals, transfers)
- **Features**:
  - Filter by user, type, transaction name, date range, amount range
  - View detailed transaction information
  - Export to CSV
  - Real-time balance tracking
  - Transaction metadata analysis

#### 2. **Webhook API Logs**
- **Purpose**: Monitor webhook API calls from gaming providers
- **Features**:
  - Filter by type (deposit, withdraw, balance), status, date range
  - View request and response data
  - Analyze success/failure rates
  - Debug webhook issues
  - Retry failed requests

#### 3. **System Logs**
- **Purpose**: Browse Laravel application logs
- **Features**:
  - Filter by log level (ERROR, WARNING, INFO, DEBUG)
  - Search by content
  - Real-time log viewing
  - Copy log entries to clipboard
  - Auto-refresh functionality

#### 4. **User Activities**
- **Purpose**: Track user behavior and recent activities
- **Features**:
  - Filter by user type (Player, Agent, Master, Owner)
  - Search by username, name, or email
  - View recent transactions per user
  - Monitor user balance changes
  - Track user activity patterns

## Navigation Structure

### Main Menu: **System Logs**
```
System Logs
├── Logs Dashboard (Overview & Statistics)
├── Custom Transactions (Wallet Operations)
├── Webhook Logs (API Monitoring)
├── System Logs (Laravel Logs)
└── User Activities (User Behavior)
```

## Detailed Views

### 1. **Logs Dashboard** (`/admin/logs`)

**Purpose**: Central hub for all logging activities

**Features**:
- **Statistics Cards**: Real-time metrics
  - Total Transactions
  - Today's Transactions
  - Webhook Logs Today
  - Failed Webhooks
- **Quick Access Cards**: Direct links to each log type
- **Recent Activity**: Latest system activities
- **Quick Actions**: Export, refresh, clear old logs

**Statistics Auto-refresh**: Every 30 seconds

### 2. **Custom Transactions** (`/admin/logs/custom-transactions`)

**Purpose**: Monitor all custom wallet operations

**Filters Available**:
- **User**: Select specific users
- **Type**: Deposit, Withdraw, Transfer
- **Transaction Name**: Specific transaction types
- **Date Range**: From/To dates
- **Amount Range**: Min/Max amounts

**Table Columns**:
- ID, User, Target User, Amount, Type, Transaction Name
- Old Balance, New Balance, Meta, Date, Actions

**Actions**:
- View detailed transaction information
- Export filtered results to CSV
- Navigate to user profiles

### 3. **Webhook Logs** (`/admin/logs/webhook-logs`)

**Purpose**: Monitor webhook API performance

**Filters Available**:
- **Type**: Deposit, Withdraw, Balance
- **Status**: Success, Failure, Partial Success
- **Date Range**: From/To dates

**Table Columns**:
- ID, Type, Status, Request Data, Response Data, Created At, Actions

**Features**:
- **Request/Response Analysis**: JSON data display
- **Success Rate Calculation**: Automatic analysis
- **Error Detection**: Failed request identification
- **Retry Functionality**: Retry failed requests

### 4. **System Logs** (`/admin/logs/system-logs`)

**Purpose**: Browse Laravel application logs

**Filters Available**:
- **Log Level**: All, Error, Warning, Info, Debug
- **Search Term**: Content-based search

**Features**:
- **Color-coded Log Levels**: Visual distinction
- **Copy to Clipboard**: Individual log entries
- **Auto-refresh**: Every 30 seconds
- **Log Statistics**: Count by level
- **Real-time Monitoring**: Live log updates

### 5. **User Activities** (`/admin/logs/user-activities`)

**Purpose**: Track user behavior and activities

**Filters Available**:
- **User Type**: Player, Agent, Master, Owner
- **Search**: Username, name, or email

**User Cards Display**:
- User information (name, username, type)
- Current balance
- Recent transactions (last 3)
- Last activity timestamp
- Quick actions

## API Endpoints

### Routes Structure
```php
Route::prefix('admin/logs')->name('logs.')->group(function () {
    Route::get('/', [LogController::class, 'index'])->name('index');
    Route::get('/custom-transactions', [LogController::class, 'customTransactions'])->name('custom-transactions');
    Route::get('/webhook-logs', [LogController::class, 'webhookLogs'])->name('webhook-logs');
    Route::get('/system-logs', [LogController::class, 'systemLogs'])->name('system-logs');
    Route::get('/user-activities', [LogController::class, 'userActivities'])->name('user-activities');
    Route::get('/transaction/{id}', [LogController::class, 'showTransaction'])->name('transaction-detail');
    Route::get('/webhook/{id}', [LogController::class, 'showWebhookLog'])->name('webhook-detail');
    Route::get('/export-transactions', [LogController::class, 'exportTransactions'])->name('export-transactions');
    Route::get('/stats', [LogController::class, 'getStats'])->name('stats');
    Route::post('/clear-old', [LogController::class, 'clearOldLogs'])->name('clear-old');
});
```

### API Endpoints

#### **GET** `/admin/logs/stats`
**Purpose**: Get real-time statistics
**Response**:
```json
{
    "total_transactions": 1250,
    "today_transactions": 45,
    "total_deposits": 50000.00,
    "total_withdrawals": 45000.00,
    "webhook_logs_today": 120,
    "failed_webhooks": 5
}
```

#### **GET** `/admin/logs/export-transactions`
**Purpose**: Export transactions to CSV
**Parameters**: All filter parameters from custom-transactions page
**Response**: CSV file download

#### **POST** `/admin/logs/clear-old`
**Purpose**: Clear old log entries
**Parameters**:
- `days`: Number of days to keep (default: 30)
- `_token`: CSRF token
**Response**:
```json
{
    "message": "Cleared 150 transactions and 25 webhook logs older than 30 days",
    "deleted_transactions": 150,
    "deleted_webhook_logs": 25
}
```

## Database Models

### CustomTransaction Model
**Table**: `custom_transactions`
**Purpose**: Store all custom wallet transactions

**Fields**:
- `id`: Primary key
- `user_id`: Foreign key to users table
- `target_user_id`: Foreign key to users table (for transfers)
- `amount`: Transaction amount
- `type`: Transaction type (deposit, withdraw, transfer)
- `transaction_name`: Specific transaction name
- `old_balance`: Balance before transaction
- `new_balance`: Balance after transaction
- `meta`: JSON metadata
- `uuid`: Unique transaction identifier
- `confirmed`: Transaction confirmation status
- `created_at`, `updated_at`: Timestamps

### TransactionLog Model
**Table**: `transaction_logs`
**Purpose**: Store webhook API logs

**Fields**:
- `id`: Primary key
- `type`: Webhook type (deposit, withdraw, balance)
- `batch_request`: JSON request data
- `response_data`: JSON response data
- `status`: Request status (success, failure, partial_success_or_failure)
- `created_at`, `updated_at`: Timestamps

## Usage Examples

### 1. **Monitoring Daily Transactions**
1. Navigate to **System Logs > Logs Dashboard**
2. View real-time statistics
3. Click **Custom Transactions** for detailed view
4. Filter by today's date range
5. Export results for reporting

### 2. **Debugging Webhook Issues**
1. Navigate to **System Logs > Webhook Logs**
2. Filter by status "failure"
3. Click on failed log entry
4. Analyze request/response data
5. Use retry functionality if needed

### 3. **Analyzing User Behavior**
1. Navigate to **System Logs > User Activities**
2. Filter by user type (e.g., "Player")
3. Search for specific user
4. View recent transactions
5. Click "View All" for complete transaction history

### 4. **System Monitoring**
1. Navigate to **System Logs > System Logs**
2. Filter by log level "ERROR"
3. Search for specific error patterns
4. Copy error details for debugging
5. Monitor real-time updates

## Security & Permissions

### Access Control
- **Authentication Required**: All routes require admin authentication
- **Permission-based**: Can be extended with role-based permissions
- **CSRF Protection**: All POST requests protected
- **Data Sanitization**: All user inputs sanitized

### Data Privacy
- **Sensitive Data**: Balance information visible only to authorized users
- **Audit Trail**: All admin actions logged
- **Data Retention**: Configurable log retention periods

## Performance Considerations

### Optimization Features
- **Pagination**: All lists paginated (50 items per page)
- **Database Indexing**: Optimized queries with proper indexes
- **Caching**: Statistics cached for 30 seconds
- **Lazy Loading**: Relationships loaded on demand

### Scalability
- **Log Rotation**: Automatic old log cleanup
- **Export Limits**: Large exports handled efficiently
- **Real-time Updates**: Optimized for high-frequency updates

## Troubleshooting

### Common Issues

#### 1. **Slow Loading**
- **Cause**: Large number of log entries
- **Solution**: Use filters to narrow results, clear old logs

#### 2. **Missing Logs**
- **Cause**: Log rotation or database issues
- **Solution**: Check system logs, verify database connectivity

#### 3. **Export Failures**
- **Cause**: Large dataset or memory limits
- **Solution**: Use date filters, increase PHP memory limit

#### 4. **Real-time Updates Not Working**
- **Cause**: JavaScript errors or network issues
- **Solution**: Check browser console, verify AJAX endpoints

### Debug Steps
1. Check browser console for JavaScript errors
2. Verify database connectivity
3. Check Laravel logs for server errors
4. Test individual API endpoints
5. Verify file permissions for log files

## Future Enhancements

### Planned Features
- **Real-time Notifications**: WebSocket-based live updates
- **Advanced Analytics**: Charts and graphs for data visualization
- **Automated Alerts**: Email/SMS notifications for critical events
- **API Integration**: REST API for external monitoring tools
- **Custom Dashboards**: Configurable dashboard layouts
- **Report Scheduling**: Automated report generation and delivery

### Integration Possibilities
- **Monitoring Tools**: Integration with external monitoring systems
- **Business Intelligence**: Data export for BI tools
- **Compliance Reporting**: Automated compliance report generation
- **Performance Metrics**: Advanced performance monitoring

## Support & Maintenance

### Regular Maintenance
- **Log Cleanup**: Weekly old log cleanup
- **Database Optimization**: Monthly database maintenance
- **Performance Monitoring**: Regular performance checks
- **Security Updates**: Keep system updated

### Backup Strategy
- **Database Backups**: Regular transaction log backups
- **Log File Backups**: System log file backups
- **Configuration Backups**: System configuration backups

---

**System Status**: Production Ready  
**Last Updated**: October 4, 2025  
**Version**: 1.0  
**Maintainer**: Development Team
