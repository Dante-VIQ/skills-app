{{-- resources/views/team-invitations/accept.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Accept Invitation - {{ config('app.name') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .invitation-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 560px;
            overflow: hidden;
        }
        
        .invitation-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 32px 30px;
            text-align: center;
        }
        
        .invitation-header h1 {
            color: white;
            font-size: 24px;
            margin-bottom: 8px;
        }
        
        .invitation-header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
        }
        
        .team-badge {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: #764ba2;
            font-size: 32px;
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        
        .invitation-content {
            padding: 40px 30px;
        }
        
        .invitation-details {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 30px;
        }
        
        .detail-row {
            display: flex;
            margin-bottom: 12px;
        }
        
        .detail-label {
            font-weight: 600;
            color: #555;
            width: 100px;
        }
        
        .detail-value {
            color: #333;
        }
        
        .message-box {
            background: #fff3e0;
            border-left: 4px solid #ed8936;
            padding: 16px 20px;
            margin-bottom: 30px;
            border-radius: 4px;
            font-style: italic;
            color: #4a5568;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 8px;
        }
        
        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.2s;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn {
            display: inline-block;
            padding: 14px 28px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            width: 100%;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .btn-link {
            background: transparent;
            color: #718096;
            border: 2px solid #e2e8f0;
            width: 100%;
            margin-top: 12px;
        }
        
        .expiration-notice {
            background: #fed7d7;
            border: 1px solid #fc8181;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 20px;
            color: #c53030;
            font-size: 14px;
            display: flex;
            align-items: center;
        }
        
        .invitation-footer {
            background: #f7fafc;
            padding: 20px 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            color: #718096;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="invitation-card">
        <div class="invitation-header">
            <div class="team-badge">
                {{ strtoupper(substr($invitation->team->name, 0, 1)) }}
            </div>
            <h1>You're invited to join</h1>
            <p style="font-size: 20px; font-weight: bold; margin-top: 8px;">{{ $invitation->team->name }}</p>
        </div>
        
        <div class="invitation-content">
            <div class="invitation-details">
                <div class="detail-row">
                    <span class="detail-label">Invited by:</span>
                    <span class="detail-value">{{ $invitation->team->owner->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Your role:</span>
                    <span class="detail-value">{{ ucfirst($invitation->role) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Team size:</span>
                    <span class="detail-value">{{ $invitation->team->users()->count() }} members</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Expires:</span>
                    <span class="detail-value">{{ $invitation->expires_at->format('F j, Y') }}</span>
                </div>
            </div>
            
            @if($invitation->message)
                <div class="message-box">
                    "{{ $invitation->message }}"
                </div>
            @endif
            
            @if($invitation->isExpired())
                <div class="expiration-notice">
                    ⚠️ This invitation has expired. Please ask the team owner to send a new invitation.
                </div>
                <a href="{{ route('teams.index') }}" class="btn btn-primary" style="text-align: center; display: block; background: #718096;">
                    Go to Dashboard
                </a>
            @else
                @if($userExists)
                    <form method="POST" action="{{ route('team.invitations.accept.submit', $invitation->token) }}">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            Accept Invitation
                        </button>
                        <a href="{{ route('team.invitations.decline', $invitation->token) }}" 
                           class="btn btn-link"
                           onclick="return confirm('Are you sure you want to decline this invitation?');">
                            Decline Invitation
                        </a>
                    </form>
                @else
                    <div style="margin-bottom: 20px; padding: 16px; background: #e6f7ff; border: 1px solid #91d5ff; border-radius: 8px;">
                        <p style="color: #0050b3; margin-bottom: 8px;">
                            <strong>✨ No account yet?</strong>
                        </p>
                        <p style="color: #0050b3; font-size: 14px;">
                            Create an account to join {{ $invitation->team->name }}. It's free and takes less than a minute.
                        </p>
                    </div>
                    
                    <form method="POST" action="{{ route('team.invitations.accept.submit', $invitation->token) }}">
                        @csrf
                        
                        <div class="form-group">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   class="form-input" 
                                   value="{{ old('name') }}"
                                   required
                                   autofocus
                                   placeholder="Enter your full name">
                            @error('name')
                                <p style="color: #e53e3e; font-size: 14px; margin-top: 4px;">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" 
                                   id="email" 
                                   class="form-input" 
                                   value="{{ $invitation->email }}"
                                   readonly
                                   disabled
                                   style="background: #f7fafc;">
                        </div>
                        
                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="form-input" 
                                   required
                                   placeholder="Create a password">
                            @error('password')
                                <p style="color: #e53e3e; font-size: 14px; margin-top: 4px;">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   class="form-input" 
                                   required
                                   placeholder="Confirm your password">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            Create Account & Accept Invitation
                        </button>
                        
                        <a href="{{ route('team.invitations.decline', $invitation->token) }}" 
                           class="btn btn-link"
                           onclick="return confirm('Are you sure you want to decline this invitation?');">
                            Decline Invitation
                        </a>
                    </form>
                @endif
            @endif
        </div>
        
        <div class="invitation-footer">
            <p>This invitation was sent to <strong>{{ $invitation->email }}</strong></p>
            <p style="margin-top: 8px;">
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>