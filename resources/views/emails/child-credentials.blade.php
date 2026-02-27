<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Child Account Created</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f8;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            color: white;
            font-size: 32px;
            font-weight: 700;
        }
        .content {
            padding: 40px 30px;
        }
        .credentials-box {
            background: #f8f9fa;
            border: 2px dashed #667eea;
            border-radius: 15px;
            padding: 30px;
            margin: 30px 0;
            text-align: center;
        }
        .credential-item {
            margin: 20px 0;
        }
        .credential-label {
            font-size: 14px;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 5px;
        }
        .credential-value {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            background: white;
            padding: 10px 20px;
            border-radius: 8px;
            display: inline-block;
            border: 1px solid #ddd;
        }
        .password-value {
            font-size: 28px;
            font-weight: 800;
            color: #667eea;
            background: white;
            padding: 15px 30px;
            border-radius: 12px;
            border: 2px solid #667eea;
            letter-spacing: 2px;
            font-family: monospace;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 18px;
            margin: 20px 0;
        }
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            background: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to SkillDNA!</h1>
            <p style="color: rgba(255,255,255,0.9);">Your child's learning journey begins here</p>
        </div>

        <div class="content">
            <p>Hello <strong>{{ $parent->first_name }}</strong>,</p>
            
            <p>Great news! You've successfully created a SkillDNA account for <strong>{{ $child->full_name }}</strong>.</p>

            <div class="credentials-box">
                <h3 style="margin-top: 0;">🔐 Login Credentials</h3>
                
                <div class="credential-item">
                    <div class="credential-label">Username</div>
                    <div class="credential-value">{{ $child->username }}</div>
                </div>

                <div class="credential-item">
                    <div class="credential-label">Temporary Password</div>
                    <div class="password-value">{{ $temporaryPassword }}</div>
                </div>

                <a href="{{ $loginUrl }}" class="button">
                    Go to Kids' Login
                </a>
            </div>

            <div class="warning">
                <strong>⚠️ Important:</strong> Please ensure your child changes their password after first login.
            </div>

            <h4>What's Next?</h4>
            <ul>
                <li>Your child logs in with the credentials above</li>
                <li>They'll complete a fun onboarding process</li>
                <li>You can track progress from your dashboard</li>
            </ul>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} SkillDNA. All rights reserved.</p>
        </div>
    </div>
</body>
</html>