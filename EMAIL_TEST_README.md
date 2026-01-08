# Email Service Testing - Ngumi Network

## Overview
This document explains how to test the email service functionality in the Ngumi Network application.

## SMTP Configuration
The application is configured to use the following SMTP settings:

```
MAIL_DRIVER=smtp
MAIL_HOST=mail.nguminetwork.co.ke
MAIL_PORT=587
MAIL_USERNAME=noreply@nguminetwork.co.ke
MAIL_PASSWORD=Tronex29@
MAIL_ENCRYPTION=tls
```

**Note:** The email service uses port 587 with TLS encryption, not port 465 with SSL.

## Testing Methods

### 1. Web Interface (Recommended)
Access the test page at: `http://your-domain/test-email`

**Features:**
- ✅ Visual web interface
- ✅ Real-time feedback
- ✅ Plain text and HTML email testing
- ✅ Error handling and debugging info
- ✅ Current mail configuration display

### 2. API Endpoints

#### Plain Text Email Test
```bash
POST /api/test-email
Content-Type: application/json

{
  "email": "test@example.com",
  "subject": "Test Subject (Optional)",
  "message": "Test message content (Optional)"
}
```

#### HTML Email Test
```bash
POST /api/test-email-html
Content-Type: application/json

{
  "email": "test@example.com"
}
```

## Testing Steps

1. **Access Test Page**: Visit `http://your-domain/test-email`
2. **Enter Email**: Provide a valid email address to receive the test
3. **Choose Test Type**:
   - **Plain Text**: Basic text email
   - **HTML**: Formatted HTML email with styling
4. **Send Test**: Click "Send Test Email"
5. **Check Results**:
   - ✅ Green success message = Email sent successfully
   - ❌ Red error message = Configuration or sending issue

## Expected Results

### Success Response
```json
{
  "success": true,
  "message": "Test email sent successfully!",
  "data": {
    "recipient": "test@example.com",
    "subject": "Test Email from Ngumi Network",
    "timestamp": "2026-01-07T12:00:00.000000Z",
    "mail_config": {
      "driver": "smtp",
      "host": "mail.topi.co.ke",
      "port": 465,
      "encryption": "ssl",
      "from_address": "noreply@nguminetwork.co.ke",
      "from_name": "Ngumi Network"
    }
  }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Failed to send test email",
  "error": "Connection timeout or authentication failure"
}
```

## Troubleshooting

### Common Issues

1. **Connection Timeout**
   - Check if SMTP server is accessible
   - Verify firewall settings
   - Confirm port 465 is open

2. **Authentication Failed**
   - Verify username/password
   - Check if account allows SMTP
   - Confirm credentials are correct

3. **SSL/TLS Issues**
   - Ensure SSL encryption is supported
   - Check certificate validity
   - Try different port (587 for TLS, 465 for SSL)

### Debug Information

The test page provides detailed debug information including:
- Current mail configuration
- SMTP server details
- Error messages with stack traces
- Connection status

## Integration Testing

### Sparring Request Notifications
The email service is used for:
- ✅ New spar request notifications
- ✅ Request acceptance notifications
- ✅ Request rejection notifications
- ✅ Request cancellation notifications

### Registration Verification
- ✅ Email verification for new user registrations

## Security Notes

- Test emails are sent using the configured SMTP credentials
- No sensitive data is included in test emails
- API endpoints may require authentication in production
- Test functionality should be disabled in production environments

## Support

If email testing fails:
1. Check SMTP server logs
2. Verify network connectivity
3. Confirm SMTP credentials
4. Test with different email providers
5. Check spam/junk folders

---

**Ngumi Network - Email Service Test Documentation**
