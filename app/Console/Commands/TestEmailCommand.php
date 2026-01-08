<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email : The email address to send test to} {--type=plain : Type of test email (plain or html)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email to verify email configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $type = $this->option('type');

        $this->info('🚀 Testing Email Configuration...');
        $this->line('');

        // Display current configuration
        $this->info('📧 Current Mail Configuration:');
        $this->line('  Driver: ' . Config::get('mail.default'));
        $this->line('  Host: ' . Config::get('mail.mailers.smtp.host'));
        $this->line('  Port: ' . Config::get('mail.mailers.smtp.port'));
        $this->line('  Encryption: ' . Config::get('mail.mailers.smtp.encryption'));
        $this->line('  From: ' . Config::get('mail.from.address') . ' (' . Config::get('mail.from.name') . ')');
        $this->line('');

        try {
            if ($type === 'html') {
                $this->sendHtmlTestEmail($email);
            } else {
                $this->sendPlainTestEmail($email);
            }

            $this->info('✅ Test email sent successfully!');
            $this->line('📨 Check your inbox for the test email.');
            $this->line('📧 Sent to: ' . $email);
            $this->line('📅 Time: ' . now()->format('Y-m-d H:i:s'));

        } catch (\Exception $e) {
            $this->error('❌ Failed to send test email!');
            $this->error('Error: ' . $e->getMessage());
            $this->line('');
            $this->warn('🔧 Troubleshooting tips:');
            $this->line('  1. Check SMTP server credentials');
            $this->line('  2. Verify network connectivity');
            $this->line('  3. Check firewall settings');
            $this->line('  4. Confirm SMTP server allows connections');

            return 1;
        }

        return 0;
    }

    /**
     * Send a plain text test email
     */
    private function sendPlainTestEmail($email)
    {
        $this->info('📝 Sending plain text test email...');

        Mail::raw('This is a test email from Ngumi Network.

If you received this email, your SMTP configuration is working correctly!

Sent at: ' . now()->format('Y-m-d H:i:s') . '
Server: ' . Config::get('mail.mailers.smtp.host') . ':' . Config::get('mail.mailers.smtp.port'), function ($message) use ($email) {
            $message->to($email)
                    ->subject('Plain Text Test Email - Ngumi Network');
        });
    }

    /**
     * Send an HTML test email
     */
    private function sendHtmlTestEmail($email)
    {
        $this->info('🎨 Sending HTML test email...');

        Mail::send([], [], function ($message) use ($email) {
            $message->to($email)
                    ->subject('HTML Test Email - Ngumi Network')
                    ->html('
                        <html>
                        <head>
                            <style>
                                body { font-family: Arial, sans-serif; margin: 20px; }
                                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
                                .success { color: #28a745; font-size: 24px; font-weight: bold; }
                                .details { background: white; padding: 15px; border-radius: 5px; margin: 15px 0; }
                            </style>
                        </head>
                        <body>
                            <div class="header">
                                <h1>🎯 Ngumi Network</h1>
                                <p>Email Service Test</p>
                            </div>
                            <div class="content">
                                <h2 class="success">✅ HTML Email Test Successful!</h2>
                                <p><strong>Great news!</strong> Your email service is working perfectly with HTML formatting.</p>

                                <div class="details">
                                    <strong>Test Details:</strong><br>
                                    📧 <strong>Sent to:</strong> ' . $email . '<br>
                                    🕐 <strong>Time:</strong> ' . now()->format('Y-m-d H:i:s') . '<br>
                                    🖥️ <strong>Server:</strong> ' . Config::get('mail.mailers.smtp.host') . ':' . Config::get('mail.mailers.smtp.port') . '<br>
                                    🔒 <strong>Encryption:</strong> ' . Config::get('mail.mailers.smtp.encryption') . '
                                </div>

                                <p><em>This confirms that your sparring request notifications and registration verification emails will work correctly!</em></p>
                            </div>
                        </body>
                        </html>
                    ');
        });
    }
}
