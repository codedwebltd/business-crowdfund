<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Access Denied</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 60px 40px;
            text-align: center;
            max-width: 600px;
            width: 100%;
        }

        .error-code {
            font-size: 120px;
            font-weight: 900;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 20px;
        }

        .error-title {
            font-size: 32px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 16px;
        }

        .error-message {
            font-size: 18px;
            color: #718096;
            margin-bottom: 32px;
            line-height: 1.6;
        }

        .lock-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 30px;
            opacity: 0.8;
        }

        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 14px 32px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: transform 0.2s, box-shadow 0.2s;
            margin: 8px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }

        .hint {
            margin-top: 30px;
            padding: 20px;
            background: #f7fafc;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }

        .hint-title {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
        }

        .hint-text {
            color: #718096;
            font-size: 14px;
            line-height: 1.6;
        }

        .ip-info {
            margin-top: 20px;
            font-size: 14px;
            color: #a0aec0;
        }
    </style>
</head>
<body>
    <div class="container">
        <svg class="lock-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke="#667eea" />
        </svg>

        <div class="error-code">403</div>
        <h1 class="error-title">Access Denied</h1>
        <p class="error-message">
            {{ $exception->getMessage() ?: 'Your IP address is not authorized to access this area.' }}
        </p>

        <a href="{{ url('/') }}" class="btn">Go to Homepage</a>

        <div class="hint">
            <div class="hint-title">Administrator Access</div>
            <div class="hint-text">
                If you are the system administrator and your IP has changed, you can use the recovery token to regain access. Contact your technical team for assistance.
            </div>
        </div>

        <div class="ip-info">
            Your IP: {{ request()->ip() }}
        </div>
    </div>
</body>
</html>
