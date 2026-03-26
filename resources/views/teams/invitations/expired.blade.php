{{-- resources/views/team-invitations/expired.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invitation Expired - {{ config('app.name') }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .message-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 480px;
            padding: 48px 32px;
            text-align: center;
        }
        
        .icon {
            font-size: 64px;
            margin-bottom: 24px;
        }
        
        h1 {
            color: #4a5568;
            font-size: 28px;
            margin-bottom: 16px;
        }
        
        p {
            color: #718096;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 24px;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 16px;
        }
        
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
    </style>
</head>
<body>
    <div class="message-card">
        <div class="icon">⏰</div>
        <h1>Invitation Expired</h1>
        <p>
            This invitation is no longer valid. Please contact the {{ strtolower($teamDenomination['singular'] ?? 'team') }} owner to request a new invitation.
        </p>
        <a href="{{ route('teams.index') }}" class="btn">
            Go to Dashboard
        </a>
    </div>
</body>
</html>