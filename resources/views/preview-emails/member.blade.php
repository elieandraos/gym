<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Email Previews - LiftStation</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Inter', 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header h1 {
            color: white;
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
        }

        .email-list {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .email-item {
            border-bottom: 1px solid #e5e7eb;
            transition: background-color 0.2s;
        }

        .email-item:last-child {
            border-bottom: none;
        }

        .email-item:hover {
            background-color: #f9fafb;
        }

        .email-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 24px;
            text-decoration: none;
            color: inherit;
        }

        .email-info {
            flex: 1;
        }

        .email-title {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 4px;
        }

        .email-description {
            font-size: 14px;
            color: #6b7280;
        }

        .email-arrow {
            color: #9ca3af;
            font-size: 20px;
        }

        .email-link:hover .email-arrow {
            color: #667eea;
            transform: translateX(4px);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Member Email Previews</h1>
            <p>Preview all emails sent to members</p>
        </div>

        <div class="email-list">
            <div class="email-item">
                <a href="{{ url('/preview-emails/member/welcome') }}" class="email-link">
                    <div class="email-info">
                        <div class="email-title">Welcome Email</div>
                        <div class="email-description">Sent to new members when they register</div>
                    </div>
                    <div class="email-arrow">→</div>
                </a>
            </div>

            <div class="email-item">
                <a href="{{ url('/preview-emails/member/booking-slot-reminder') }}" class="email-link">
                    <div class="email-info">
                        <div class="email-title">Training Session Reminder</div>
                        <div class="email-description">Sent the night before at 9pm to remind members of their upcoming session</div>
                    </div>
                    <div class="email-arrow">→</div>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
