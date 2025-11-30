<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Previews - LiftStation</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Inter', 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #fafafa;
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }

        .header-left h1 {
            color: #18181b;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .header-left p {
            color: #71717a;
            font-size: 14px;
        }

        .select-container {
            position: relative;
        }

        select {
            appearance: none;
            background: white;
            border: 1px solid #e4e4e7;
            border-radius: 8px;
            padding: 10px 40px 10px 16px;
            font-size: 14px;
            color: #18181b;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s;
            min-width: 160px;
        }

        select:hover {
            border-color: #d4d4d8;
        }

        select:focus {
            outline: none;
            border-color: #18181b;
            box-shadow: 0 0 0 3px rgba(24, 24, 27, 0.1);
        }

        .select-container::after {
            content: '▼';
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: #71717a;
            font-size: 10px;
        }

        .email-list {
            background: white;
            border: 1px solid #e4e4e7;
            border-radius: 12px;
            overflow: hidden;
        }

        .email-item {
            border-bottom: 1px solid #f4f4f5;
            transition: background-color 0.2s;
        }

        .email-item:last-child {
            border-bottom: none;
        }

        .email-item:hover {
            background-color: #fafafa;
        }

        .email-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 24px;
            text-decoration: none;
            color: inherit;
        }

        .email-info {
            flex: 1;
        }

        .email-title {
            font-size: 15px;
            font-weight: 500;
            color: #18181b;
            margin-bottom: 4px;
        }

        .email-description {
            font-size: 13px;
            color: #71717a;
            line-height: 1.5;
        }

        .email-arrow {
            color: #a1a1aa;
            font-size: 18px;
            transition: all 0.2s;
        }

        .email-link:hover .email-arrow {
            color: #18181b;
            transform: translateX(4px);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-left">
                <h1>Email Previews</h1>
                <p>Preview all system emails</p>
            </div>
            <div class="select-container">
                <select id="emailTypeSelect" onchange="handleSelectChange(this)">
                    <option value="member" {{ $type === 'member' ? 'selected' : '' }}>Member Emails</option>
                    <option value="owner" {{ $type === 'owner' ? 'selected' : '' }}>Owner Emails</option>
                </select>
            </div>
        </div>

        <div class="email-list">
            @if($type === 'member')
                <div class="email-item">
                    <a href="{{ url('/preview-emails/member/welcome') }}" class="email-link" target="_blank">
                        <div class="email-info">
                            <div class="email-title">Welcome Email</div>
                            <div class="email-description">Sent to new members when they register</div>
                        </div>
                        <div class="email-arrow">→</div>
                    </a>
                </div>

                <div class="email-item">
                    <a href="{{ url('/preview-emails/member/booking-slot-reminder') }}" class="email-link" target="_blank">
                        <div class="email-info">
                            <div class="email-title">Training Session Reminder</div>
                            <div class="email-description">Sent the night before at 9pm to remind members of their upcoming session</div>
                        </div>
                        <div class="email-arrow">→</div>
                    </a>
                </div>
            @else
                <div class="email-item">
                    <a href="{{ url('/preview-emails/owner/new-member') }}" class="email-link" target="_blank">
                        <div class="email-info">
                            <div class="email-title">New Member Notification</div>
                            <div class="email-description">Sent to gym owners when a new member registers</div>
                        </div>
                        <div class="email-arrow">→</div>
                    </a>
                </div>
            @endif
        </div>
    </div>

    <script>
        function handleSelectChange(select) {
            const type = select.value;
            window.location.href = '/preview-emails?type=' + type;
        }
    </script>
</body>
</html>
