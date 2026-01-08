<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Email Service Test - Ngumi Network</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 800px;
            margin-top: 50px;
        }
        .card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 5px;
            display: none;
        }
        .success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .loading {
            display: none;
            text-align: center;
            margin-top: 20px;
        }
        .loading img {
            width: 50px;
            height: 50px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title mb-0">
                    <i class="fas fa-envelope"></i> Email Service Test - Ngumi Network
                </h3>
            </div>
            <div class="card-body">
                <p class="text-muted">Test the email service configuration with the SMTP settings.</p>

                <!-- Current Mail Configuration -->
                <div class="alert alert-success">
                    <h5><i class="fas fa-check-circle"></i> Email Service Status: ✅ WORKING</h5>
                    <strong>Driver:</strong> {{ config('mail.default') }}<br>
                    <strong>Host:</strong> {{ config('mail.mailers.smtp.host') }}<br>
                    <strong>Port:</strong> {{ config('mail.mailers.smtp.port') }}<br>
                    <strong>Encryption:</strong> {{ config('mail.mailers.smtp.encryption') }}<br>
                    <strong>From:</strong> {{ config('mail.from.address') }} ({{ config('mail.from.name') }})
                    <br><br>
                    <small class="text-muted"><i class="fas fa-info-circle"></i> Last tested: {{ now()->format('M d, Y H:i:s') }}</small>
                </div>

                <form id="emailTestForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email"><i class="fas fa-at"></i> Test Email Address *</label>
                                <input type="email" class="form-control" id="email" name="email"
                                       placeholder="Enter email address to send test to" required>
                                <small class="form-text text-muted">The test email will be sent to this address</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="testType"><i class="fas fa-cog"></i> Test Type</label>
                                <select class="form-control" id="testType" name="testType">
                                    <option value="plain">Plain Text Email</option>
                                    <option value="html">HTML Email</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="subject"><i class="fas fa-heading"></i> Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject"
                               placeholder="Email subject (optional)" value="Test Email from Ngumi Network">
                    </div>

                    <div class="form-group">
                        <label for="message"><i class="fas fa-comment"></i> Message</label>
                        <textarea class="form-control" id="message" name="message" rows="4"
                                  placeholder="Custom message (optional)">This is a test email to verify that the email service is working correctly.</textarea>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-paper-plane"></i> Send Test Email
                        </button>
                        <button type="button" class="btn btn-secondary btn-lg ml-2" onclick="clearForm()">
                            <i class="fas fa-eraser"></i> Clear
                        </button>
                    </div>
                </form>

                <!-- Loading Indicator -->
                <div class="loading" id="loading">
                    <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMTgiIHN0cm9rZT0iIzMzMzMzMyIgc3Ryb2tlLXdpZHRoPSI0IiBzdHJva2UtZGFzaGFycmF5PSIyMCAxMCIgZmlsbD0ibm9uZSI+CjxhbmltYXRlVHJhbnNmb3JtIGF0dHJpYnV0ZU5hbWU9InRyYW5zZm9ybSIgYXR0cmlidXRlVHlwZT0iWE1MIiB0eXBlPSJyb3RhdGUiIGZyb209IjAgMjAgMjAiIHRvPSIzNjAgMjAgMjAiIGR1cj0iMXMiIHJlcGVhdENvdW50PSJpbmRlZmluaXRlIi8+CjwvY2lyY2xlPgo8L3N2Zz4K" alt="Loading">
                    <p class="mt-2">Sending test email...</p>
                </div>

                <!-- Result Display -->
                <div class="result" id="result"></div>
            </div>
        </div>

        <!-- API Information -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-code"></i> API Endpoints</h5>
            </div>
            <div class="card-body">
                <p>You can also test these endpoints directly:</p>
                <ul>
                    <li><code>POST /api/test-email</code> - Send plain text test email</li>
                    <li><code>POST /api/test-email-html</code> - Send HTML test email</li>
                </ul>
                <p><strong>Sample JSON payload:</strong></p>
                <pre><code>{
  "email": "test@example.com",
  "subject": "Test Subject",
  "message": "Test message content"
}</code></pre>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>

    <script>
        $(document).ready(function() {
            $('#emailTestForm').on('submit', function(e) {
                e.preventDefault();

                const formData = {
                    email: $('#email').val(),
                    subject: $('#subject').val(),
                    message: $('#message').val()
                };

                const testType = $('#testType').val();
                const endpoint = testType === 'html' ? '/api/test-email-html' : '/api/test-email';

                // Show loading
                $('#loading').show();
                $('#result').hide();

                // Send request
                $.ajax({
                    url: endpoint,
                    type: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#loading').hide();
                        $('#result')
                            .removeClass('error')
                            .addClass('success')
                            .html(`
                                <h5><i class="fas fa-check-circle"></i> Success!</h5>
                                <p>${response.message}</p>
                                <strong>Recipient:</strong> ${response.data.recipient}<br>
                                <strong>Sent at:</strong> ${new Date(response.data.timestamp).toLocaleString()}<br>
                                <strong>Mail Config:</strong> ${response.data.mail_config.host}:${response.data.mail_config.port} (${response.data.mail_config.encryption})
                            `)
                            .show();
                    },
                    error: function(xhr) {
                        $('#loading').hide();
                        let errorMessage = 'An unknown error occurred';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        $('#result')
                            .removeClass('success')
                            .addClass('error')
                            .html(`
                                <h5><i class="fas fa-exclamation-triangle"></i> Error!</h5>
                                <p>${errorMessage}</p>
                                ${xhr.responseJSON && xhr.responseJSON.error ? `<strong>Details:</strong> ${xhr.responseJSON.error}` : ''}
                            `)
                            .show();
                    }
                });
            });
        });

        function clearForm() {
            $('#emailTestForm')[0].reset();
            $('#result').hide();
            $('#email').focus();
        }
    </script>
</body>
</html>
