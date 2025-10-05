# Logging System Setup Guide

## Quick Setup

The logging system is now fully integrated into your admin panel. Here's how to access and use it:

### 🚀 **Access the Logging System**

1. **Login to Admin Panel**
   ```
   https://yourdomain.com/admin
   ```

2. **Navigate to System Logs**
   - Look for **"System Logs"** in the left sidebar menu
   - Click to expand the submenu

3. **Available Log Sections**:
   - **Logs Dashboard** - Overview and statistics
   - **Custom Transactions** - Wallet operations
   - **Webhook Logs** - API monitoring
   - **System Logs** - Laravel application logs
   - **User Activities** - User behavior tracking

### 📊 **Logs Dashboard** (`/admin/logs`)

**What you'll see**:
- Real-time statistics cards
- Quick access to all log types
- Recent activity summary
- Quick action buttons

**Key Features**:
- Auto-refreshing statistics (every 30 seconds)
- Export functionality
- Clear old logs option

### 💰 **Custom Transactions** (`/admin/logs/custom-transactions`)

**Purpose**: Monitor all wallet operations (deposits, withdrawals, transfers)

**How to use**:
1. **Apply Filters**:
   - Select specific users
   - Filter by transaction type
   - Set date ranges
   - Filter by amount ranges

2. **View Details**:
   - Click the eye icon to see full transaction details
   - View balance changes
   - Check transaction metadata

3. **Export Data**:
   - Use "Export CSV" button to download filtered results
   - Perfect for reporting and analysis

### 🔗 **Webhook Logs** (`/admin/logs/webhook-logs`)

**Purpose**: Monitor API calls from gaming providers

**How to use**:
1. **Filter by Status**:
   - Success: All successful API calls
   - Failure: Failed API calls that need attention
   - Partial: Partially successful calls

2. **Debug Issues**:
   - Click on any log entry to see full details
   - View request and response data
   - Analyze error messages

3. **Monitor Performance**:
   - Check success rates
   - Identify problematic requests
   - Track API response times

### 📝 **System Logs** (`/admin/logs/system-logs`)

**Purpose**: Browse Laravel application logs

**How to use**:
1. **Filter by Level**:
   - ERROR: Critical issues that need immediate attention
   - WARNING: Potential problems
   - INFO: General information
   - DEBUG: Detailed debugging information

2. **Search Logs**:
   - Use search box to find specific log entries
   - Search by error messages, user names, etc.

3. **Copy Logs**:
   - Click copy button to copy log entries
   - Useful for sharing with developers

### 👥 **User Activities** (`/admin/logs/user-activities`)

**Purpose**: Track user behavior and activities

**How to use**:
1. **Filter Users**:
   - Filter by user type (Player, Agent, Master, Owner)
   - Search by username, name, or email

2. **View User Cards**:
   - See user information and current balance
   - View recent transactions
   - Check last activity time

3. **Navigate to Details**:
   - Click "View All" to see complete transaction history
   - Click on user card to go to user profile

## 🎯 **Common Use Cases**

### **Daily Monitoring**
1. Start with **Logs Dashboard** for overview
2. Check **Custom Transactions** for today's activity
3. Review **Webhook Logs** for any failures
4. Monitor **System Logs** for errors

### **Troubleshooting Issues**
1. Check **System Logs** for error messages
2. Review **Webhook Logs** for API failures
3. Analyze **Custom Transactions** for balance issues
4. Use **User Activities** to track specific user problems

### **Reporting & Analysis**
1. Use filters to narrow down data
2. Export **Custom Transactions** to CSV
3. Analyze success rates in **Webhook Logs**
4. Generate user activity reports

### **Performance Monitoring**
1. Monitor real-time statistics
2. Check webhook success rates
3. Track transaction volumes
4. Identify system bottlenecks

## 🔧 **Quick Actions**

### **Export Data**
- **Custom Transactions**: Click "Export CSV" button
- **Webhook Logs**: Copy individual log entries
- **System Logs**: Copy specific log entries

### **Clear Old Data**
- Go to **Logs Dashboard**
- Click "Clear Old Logs" button
- Confirm to remove logs older than 30 days

### **Refresh Data**
- **Auto-refresh**: Statistics refresh every 30 seconds
- **Manual refresh**: Click refresh button or reload page
- **Real-time updates**: System logs auto-refresh

## 📱 **Mobile Responsive**

The logging system is fully responsive and works on:
- Desktop computers
- Tablets
- Mobile phones

All features are accessible on mobile devices with touch-friendly interfaces.

## 🚨 **Important Notes**

### **Data Retention**
- Logs are kept for 30 days by default
- Use "Clear Old Logs" to free up space
- Export important data before clearing

### **Performance**
- Large datasets may take time to load
- Use filters to narrow down results
- Pagination limits results to 50 items per page

### **Security**
- Only admin users can access logs
- Sensitive data is protected
- All actions are logged for audit

## 🆘 **Getting Help**

### **If Something's Not Working**:
1. Check browser console for errors
2. Verify you're logged in as admin
3. Check Laravel logs for server errors
4. Contact development team

### **For New Features**:
- Request new features through development team
- Provide specific use cases
- Include screenshots if possible

## 🎉 **You're All Set!**

The logging system is now ready to use. Start with the **Logs Dashboard** to get familiar with the interface, then explore each section based on your monitoring needs.

**Happy Monitoring!** 🚀

---

**Quick Links**:
- [Main Documentation](ADMIN_LOGGING_SYSTEM_DOCUMENTATION.md)
- [Webhook Documentation](WEBHOOK_CUSTOM_WALLET_DOCUMENTATION.md)
- [Custom Wallet Guide](CUSTOM_WALLET_IMPLEMENTATION_GUIDE.md)
