<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class TestEmailController extends Controller
{
    /**
     * Send a test email to verify email service is working
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendTestEmail(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'subject' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $email = $request->input('email');
        $subject = $request->input('subject', 'Test Email from Ngumi Network');
        $message = $request->input('message', 'This is a test email to verify that the email service is working correctly.');

        try {
            // Send the test email
            Mail::raw($message, function ($mail) use ($email, $subject) {
                $mail->to($email)
                     ->subject($subject);
            });

            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully!',
                'data' => [
                    'recipient' => $email,
                    'subject' => $subject,
                    'timestamp' => now()->toISOString(),
                    'mail_config' => [
                        'driver' => config('mail.default'),
                        'host' => config('mail.mailers.smtp.host'),
                        'port' => config('mail.mailers.smtp.port'),
                        'encryption' => config('mail.mailers.smtp.encryption'),
                        'from_address' => config('mail.from.address'),
                        'from_name' => config('mail.from.name'),
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email',
                'error' => $e->getMessage(),
                'error_details' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]
            ], 500);
        }
    }

    /**
     * Send a comprehensive test email with HTML content
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendHtmlTestEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $email = $request->input('email');

        try {
            // Send HTML test email
            Mail::send([], [], function ($message) use ($email) {
                $message->to($email)
                        ->subject('HTML Test Email - Ngumi Network')
                        ->html('
                            <html>
                            <head>
                                <style>
                                    body { font-family: Arial, sans-serif; }
                                    .header { background-color: #007bff; color: white; padding: 20px; text-align: center; }
                                    .content { padding: 20px; }
                                    .footer { background-color: #f8f9fa; padding: 10px; text-align: center; font-size: 12px; }
                                </style>
                            </head>
                            <body>
                                <div class="header">
                                    <h1>🎯 Ngumi Network</h1>
                                    <p>HTML Email Test</p>
                                </div>
                                <div class="content">
                                    <h2>Test Email Sent Successfully!</h2>
                                    <p>This is a test HTML email to verify that your email service is working correctly.</p>
                                    <p><strong>Sent at:</strong> ' . now()->format('Y-m-d H:i:s') . '</p>
                                    <p><strong>Server:</strong> ' . config('mail.mailers.smtp.host') . ':' . config('mail.mailers.smtp.port') . '</p>
                                    <p>If you received this email, your SMTP configuration is working perfectly! ✅</p>
                                </div>
                                <div class="footer">
                                    <p>Ngumi Network - Connecting Fighters Worldwide</p>
                                </div>
                            </body>
                            </html>
                        ');
            });

            return response()->json([
                'success' => true,
                'message' => 'HTML test email sent successfully!',
                'data' => [
                    'recipient' => $email,
                    'type' => 'HTML',
                    'timestamp' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send HTML test email',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
