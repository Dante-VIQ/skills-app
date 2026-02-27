<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tutor Invitation - SkillDNA</title>
</head>
<body>
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #4F46E5;">SkillDNA</h1>
            <p style="color: #6B7280;">Transform Learning, Create Impact</p>
        </div>
        
        <div style="background: #F9FAFB; padding: 30px; border-radius: 8px;">
            <h2 style="color: #111827; margin-top: 0;">You're Invited to Join SkillDNA!</h2>
            
            <p style="color: #374151; line-height: 1.6;">
                Hello {{ $invitation->name ?: 'Future Tutor' }},
            </p>
            
            <p style="color: #374151; line-height: 1.6;">
                You've been invited to join SkillDNA as a tutor. SkillDNA is an educational platform 
                that combines learning with social impact.
            </p>
            
            <div style="background: #FFFFFF; border: 2px dashed #D1D5DB; padding: 20px; border-radius: 6px; margin: 25px 0; text-align: center;">
                <p style="margin: 0 0 10px 0; color: #6B7280; font-size: 14px;">Your Invitation Code:</p>
                <div style="font-size: 24px; font-weight: bold; letter-spacing: 2px; color: #4F46E5;">
                    {{ $invitation->code }}
                </div>
                <p style="margin: 10px 0 0 0; color: #6B7280; font-size: 12px;">
                    Expires: {{ $invitation->expires_at->format('F j, Y') }}
                </p>
            </div>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $url }}" 
                   style="background: #4F46E5; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;">
                    Accept Invitation
                </a>
            </div>
            
            <p style="color: #374151; line-height: 1.6;">
                If the button doesn't work, copy and paste this URL into your browser:
                <br>
                <code style="color: #4F46E5; background: #F3F4F6; padding: 2px 6px; border-radius: 4px;">
                    {{ $url }}
                </code>
            </p>
            
            <hr style="border: none; border-top: 1px solid #E5E7EB; margin: 25px 0;">
            
            <p style="color: #6B7280; font-size: 14px;">
                <strong>Note:</strong> You'll need the invitation code above to complete registration.
                <br>
                If you didn't expect this invitation, please ignore this email.
            </p>
        </div>
        
        <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #E5E7EB; color: #6B7280; font-size: 12px;">
            <p>© {{ date('Y') }} SkillDNA. All rights reserved.</p>
            <p>123 Education Street, Learning City, ED 12345</p>
        </div>
    </div>
</body>
</html>