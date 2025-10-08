# Email Logs

This directory contains email logs for development purposes.

## Files:
- `email_replies.log` - Contains all email replies sent from the admin panel

## Production Setup:
For production use, configure proper SMTP settings in your hosting environment or use a service like:
- SendGrid
- Mailgun
- Amazon SES
- Gmail SMTP (for testing)

## XAMPP Email Configuration:
To enable email in XAMPP for testing:

1. Edit `php.ini` file in your XAMPP installation
2. Configure SMTP settings:
   ```
   [mail function]
   SMTP = smtp.gmail.com
   smtp_port = 587
   sendmail_from = your-email@gmail.com
   ```

3. Or use a library like PHPMailer for more reliable email sending.

## Current Behavior:
- In development mode, emails are logged to `email_replies.log`
- The reply is still saved to the database
- Admin receives confirmation that reply was processed