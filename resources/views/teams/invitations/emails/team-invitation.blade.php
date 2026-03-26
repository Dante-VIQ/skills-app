<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark only">
    <title>You're invited to {{ $teamName }}</title>
    
    <!--[if !mso]><!-->
    <style type="text/css">
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
    </style>
    <!--<![endif]-->
    
    <style type="text/css">
        /* Email client reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }
        
        body {
            width: 100% !important;
            min-width: 100%;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            margin: 0;
            padding: 0;
            background-color: #f6f9fc;
            line-height: 1.6;
        }
        
        /* Email wrapper */
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        }
        
        /* Header */
        .email-header {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #d946ef 100%);
            padding: 48px 32px 56px;
            text-align: center;
            position: relative;
        }
        
        .header-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('data:image/svg+xml;utf8,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><path d="M30 0 L60 30 L30 60 L0 30 Z" fill="rgba(255,255,255,0.03)"/></svg>');
            background-size: 30px 30px;
            opacity: 0.4;
        }
        
        .brand-logo {
            font-size: 32px;
            font-weight: 800;
            color: white;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 16px;
            letter-spacing: -0.5px;
            position: relative;
            z-index: 2;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .invitation-badge {
            display:block;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(4px);
            padding: 8px 20px;
            border-radius: 40px;
            color: white;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-top: 8px;
            border: 1px solid rgba(255,255,255,0.3);
        }
        
        /* Content */
        .email-content {
            padding: 48px 32px;
            background: linear-gradient(to bottom, #ffffff, #fafcff);
        }
        
        .greeting {
            font-size: 24px;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 16px;
            line-height: 1.3;
        }
        
        .greeting span {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .intro-text {
            color: #4a5568;
            font-size: 16px;
            margin-bottom: 32px;
            line-height: 1.6;
        }
        
        /* Team card */
        .team-card {
            background: linear-gradient(145deg, #ffffff, #f8faff);
            border: 1px solid #e0e7ff;
            border-radius: 24px;
            padding: 32px;
            margin-bottom: 32px;
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.08);
        }
        
        .team-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            vertical-align: middle;
            margin: auto;
            padding: auto;
            color: white;
            font-size: 32px;
            font-weight: 700;
            box-shadow: 0 8px 16px rgba(99, 102, 241, 0.2);
            transform: rotate(-5deg);
            transition: transform 0.3s ease;
        }
        
        .team-avatar:hover {
            transform: rotate(0deg) scale(1.05);
        }
        
        .team-name {
            text-align: center;
            font-size: 28px;
            font-weight: 800;
            color: #1a202c;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }
        
        .team-meta {
            text-align: center;
            color: #64748b;
            font-size: 15px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
        }
        
        .meta-item {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            background: #f1f5f9;
            border-radius: 20px;
            font-size: 13px;
            color: #475569;
        }
        
        /* Details grid */
        .details-grid {
            background: #f8fafc;
            border-radius: 16px;
            padding: 24px;
            margin-top: 24px;
            border: 1px solid #e2e8f0;
        }
        
        .detail-row {
            display: flex;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-icon {
            width: 36px;
            height: 36px;
            background: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 18px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        
        .detail-label {
            font-weight: 600;
            color: #334155;
            width: 100px;
            font-size: 14px;
        }
        
        .detail-value {
            color: #0f172a;
            font-weight: 500;
            font-size: 15px;
        }
        
        /* Message box */
        .message-box {
            background: linear-gradient(135deg, #fff7ed, #ffedd5);
            border-left: 4px solid #f97316;
            border-radius: 12px;
            padding: 24px;
            margin: 32px 0;
            font-style: italic;
            color: #7c2d12;
            font-size: 16px;
            position: relative;
        }
        
        .message-box::before {
            content: '"';
            position: absolute;
            top: -10px;
            left: 16px;
            font-size: 64px;
            color: #fdba74;
            font-family: Georgia, serif;
            opacity: 0.5;
        }
        
        .message-sender {
            margin-top: 12px;
            font-size: 14px;
            color: #9a3412;
            font-style: normal;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        
        /* CTA Buttons */
        .cta-section {
            text-align: center;
            margin: 40px 0 32px;
        }
        
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white !important;
            text-decoration: none;
            padding: 18px 48px;
            border-radius: 40px;
            font-weight: 600;
            font-size: 17px;
            letter-spacing: 0.5px;
            margin-bottom: 16px;
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
            border: 1px solid rgba(255,255,255,0.2);
            transition: all 0.2s ease;
        }
        
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(99, 102, 241, 0.4);
            background: linear-gradient(135deg, #4f52e0, #7c3aed);
        }
        
        .secondary-link {
            color: #64748b;
            text-decoration: none;
            font-size: 14px;
            border-bottom: 1px dashed #94a3b8;
            padding-bottom: 2px;
        }
        
        .secondary-link:hover {
            color: #475569;
            border-bottom-color: #475569;
        }
        
        /* Expiration notice */
        .expiration-notice {
            background: #fffbeb;
            border: 1px solid #fcd34d;
            border-radius: 12px;
            padding: 18px 20px;
            margin: 32px 0;
            display: flex;
            align-items: center;
            color: #92400e;
        }
        
        .expiration-icon {
            font-size: 24px;
            margin-right: 12px;
        }
        
        .expiration-text {
            font-size: 14px;
            font-weight: 500;
        }
        
        .expiration-date {
            font-weight: 700;
            color: #b45309;
        }
        
        /* Help section */
        .help-section {
            background: #f8fafc;
            border-radius: 16px;
            padding: 28px;
            margin-top: 40px;
            border: 1px solid #e2e8f0;
        }
        
        .help-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
        }
        
        .help-content {
            color: #475569;
            font-size: 14px;
            line-height: 1.7;
        }
        
        .fallback-link {
            background: white;
            padding: 14px;
            border-radius: 10px;
            margin-top: 16px;
            border: 1px solid #e2e8f0;
            word-break: break-all;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            color: #334155;
        }
        
        /* Footer */
        .email-footer {
            background: #0f172a;
            padding: 48px 32px;
            color: #94a3b8;
            text-align: center;
        }
        
        .footer-brand {
            font-size: 24px;
            font-weight: 700;
            color: white;
            text-decoration: none;
            margin-bottom: 16px;
            display: inline-block;
            letter-spacing: -0.5px;
        }
        
        .footer-text {
            font-size: 14px;
            line-height: 1.7;
            margin-bottom: 24px;
            color: #cbd5e1;
        }
        
        .social-links {
            margin: 32px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 24px;
        }
        
        .social-link {
            display: inline-block;
            height: 36px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            line-height: 36px;
            text-align: center;
            color: white;
            text-decoration: none;
            transition: all 0.2s ease;
            padding: 12px;
        }
        
        .social-link:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-2px);
        }
        
        .footer-links {
            margin: 24px 0;
        }
        
        .footer-link {
            color: #94a3b8;
            text-decoration: none;
            font-size: 13px;
            margin: 0 12px;
            transition: color 0.2s ease;
        }
        
        .footer-link:hover {
            color: white;
            text-decoration: underline;
        }
        
        .copyright {
            font-size: 12px;
            color: #64748b;
            margin-top: 32px;
            padding-top: 32px;
            border-top: 1px solid #1e293b;
        }
        
        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #0f172a;
            }
            
            .email-wrapper {
                background-color: #1e293b;
            }
            
            .email-content {
                background: #1e293b;
            }
            
            .greeting {
                color: #f1f5f9;
            }
            
            .greeting span {
                -webkit-text-fill-color: #a78bfa;
            }
            
            .intro-text {
                color: #cbd5e1;
            }
            
            .team-card {
                background: #2d3a4f;
                border-color: #475569;
            }
            
            .team-name {
                color: #f1f5f9;
            }
            
            .team-meta {
                color: #94a3b8;
            }
            
            .meta-item {
                background: #334155;
                color: #e2e8f0;
                margin-bottom: 8px;
                align-self: center;
                padding: 6px;
            }
            
            .details-grid {
                background: #334155;
                border-color: #475569;
            }
            
            .detail-icon {
                background: #1e293b;
                color: #cbd5e1;
            }
            
            .detail-label {
                color: #cbd5e1;
            }
            
            .detail-value {
                color: #f1f5f9;
            }
            
            .help-section {
                background: #2d3a4f;
                border-color: #475569;
            }
            
            .help-title {
                color: #f1f5f9;
            }
            
            .help-content {
                color: #cbd5e1;
            }
            
            .fallback-link {
                background: #1e293b;
                border-color: #475569;
                color: #cbd5e1;
            }
        }
        
        /* Mobile responsive */
        @media screen and (max-width: 600px) {
            .email-header {
                padding: 32px 20px 40px;
            }
            
            .email-content {
                padding: 32px 20px;
            }
            
            .team-card {
                padding: 24px;
            }
            
            .team-name {
                font-size: 24px;
            }
            
            .detail-row {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .detail-icon {
                margin-bottom: 8px;
                align-self: center;
                padding: 6px;
            }
            
            .detail-label {
                width: 100%;
                margin-bottom: 4px;
            }
            
            .cta-button {
                display: block;
                width: 100%;
                padding: 16px 24px;
            }
            
            .social-links {
                gap: 16px;
            }
            
            .footer-link {
                display: block;
                margin: 12px 0;
            }
        }
        
        /* Outlook specific */
        .ExternalClass {
            width: 100%;
        }
        
        .ExternalClass,
        .ExternalClass p,
        .ExternalClass span,
        .ExternalClass font,
        .ExternalClass td,
        .ExternalClass div {
            line-height: 100%;
        }

        /* Dark mode support */
@media (prefers-color-scheme: dark) {
    .codeberg-icon svg,
    .github-icon svg {
        filter: invert(1);
    }
    
    .social-icon:hover {
        background: rgba(255, 255, 255, 0.1) !important;
    }
    
    .linkedin-icon svg path:first-child {
        fill: #0a66c2 !important;
    }
    
    .github-icon svg path {
        fill: #ffffff !important;
    }
}

/* Ensure icons are perfectly centered */
.social-icon {
    padding: 0;
    line-height: 0;
}

.social-icon svg {
    display: block;
}
    </style>

</head>
{{--         Mail::to("maztechdevelopment@gmail.com")->send(new MNobre\LaravelTeams\Mail\TeamInvitationMail($data));
 --}}
<body>

    <div class="email-wrapper">
        <!-- Header -->
        <div class="email-header" style="display: flex; flex-direction: column; gap: 20px;">
            <div class="header-pattern"></div>
            <table style="width: 100%; border-collapse: collapse; border: none;">
                <tr>
                    <td style="text-align: left; padding: 0;">
                        <a href="{{ $appUrl }}" class="brand-logo">{{ config('app.name') }}</a>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: right; padding: 0;">
                        <div class="invitation-badge">
                            {{ strtoupper($teamDenomination['singular'] ?? 'TEAM') }} INVITATION    
                        </div>
                    </td>
                </tr>
            </table>

        </div>
        
        <!-- Content -->
        <div class="email-content">
            <!-- Greeting -->
            <div class="greeting">
                <span>{{ $inviterName }}</span> invited you to join
            </div>
            
            <p class="intro-text">
                You've been invited to collaborate on <strong>{{ $teamName }}</strong>. Join your team members and start working together.
            </p>
            
            <!-- Team Card -->
            <div class="team-card">
                <div class="team-avatar" id="teamAvatar">
                    <span style="text-shadow: 0 2px 4px rgba(0,0,0,0.1); margin: auto; padding: auto; align-items: center; justify-content: center; display: flex;">
                        {{ strtoupper(substr($teamName, 0, 1)) }}
                    </span>
                </div>
                
                <h2 class="team-name">{{ $teamName }}</h2>

                <div class="team-meta" style="display: flex; align-items: center; gap: 16px; padding: 8px 12px; flex-wrap: wrap;">
                    <span class="meta-item" style="display: inline-flex; align-items: center; gap: 6px;">
                        <i>👥</i><span>{{ $memberCount }} {{ $memberCount === 1 ? 'member' : 'members' }}</span> 
                    </span>
                    
                    <span class="meta-item" style="display: inline-flex; align-items: center; gap: 6px;">
                         <i>🏷️</i><span>{{ ucfirst($role ?? 'member') }}</span> 

                    </span>
                    
                    @if($teamDenomination['singular'] ?? false)
                        <span class="meta-item" style="display: inline-flex; align-items: center; gap: 6px;">
                            <i>📋</i><span>{{ $teamDenomination['singular'] }}</span>
                        </span>
                    @endif
                </div>
                
                <!-- Invitation Details -->
                <div class="details-grid">
                    <div class="detail-row">
                        <div class="detail-icon">👤</div>
                        <span class="detail-label">Invited by:</span>
                        <span class="detail-value">{{ $inviterName }}</span>
                    </div>
                    <div class="detail-row">
                        <div class="detail-icon">🎯</div>
                        <span class="detail-label">Your role:</span>
                        <span class="detail-value">{{ ucfirst($role ?? 'member') }}</span>
                    </div>
                    <div class="detail-row">
                        <div class="detail-icon">📅</div>
                        <span class="detail-label">Invited on:</span>
                        <span class="detail-value">{{ $invitedDate ?? now()->format('F j, Y') }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Personal Message -->
            @if(isset($personalMessage) && !empty($personalMessage))
                <div class="message-box">
                    {{ $personalMessage }}
                    <div class="message-sender">
                        — {{ $inviterName }}
                    </div>
                </div>
            @endif
            
            <!-- CTA Section -->
            <div class="cta-section">
                <a href="{{ $acceptUrl }}" class="cta-button">
                    ACCEPT INVITATION
                </a>
                <br>
                <a href="{{ $declineUrl }}" class="secondary-link">
                    or decline this invitation
                </a>
            </div>
            
            <!-- Expiration Notice -->
            @if(isset($expirationDate))
            <div class="expiration-notice">
                <span class="expiration-icon">⏰</span>
                <span class="expiration-text">
                    This invitation will expire on 
                    <span class="expiration-date">{{ $expirationDate }}</span>
                </span>
            </div>
            @endif
            
            <!-- Help Section -->
            <div class="help-section">
                <div class="help-title">
                    <span style="margin-right: 8px;">💡</span>
                    Having trouble?
                </div>
                <div class="help-content">
                    <p style="margin-bottom: 12px;">
                        <strong>What happens after I accept?</strong><br>
                        You'll be added to the {{ strtolower($teamDenomination['singular'] ?? 'team') }} and can immediately access all shared resources and collaborate with team members.
                    </p>
                  
                    <p style="margin-bottom: 12px;">
                        <strong>Don't have an account yet?</strong><br>
                        No problem! Click the accept button above and you'll be guided through creating a new account. Your invitation will be automatically applied.
                    </p>
                    
                    <p style="margin-bottom: 12px;">
                        <strong>Button not working?</strong><br>
                        Copy and paste this link into your browser:
                    </p>
                    <div class="fallback-link">
                        {{ $acceptUrl }}
                    </div>
                </div>
            </div>
        </div>       
        <!-- Footer -->
        <div class="email-footer">
            <div class="footer-content">
                <!-- Footer Links -->
                <div class="footer-links">
                    <a href="{{ $appUrl }}/privacy" class="footer-link">Privacy Policy</a>
                    <span style="color: #334155;">•</span>
                    <a href="{{ $appUrl }}/terms" class="footer-link">Terms of Service</a>
                    <span style="color: #334155;">•</span>
                    <a href="{{ $appUrl }}/support" class="footer-link">Help Center</a>
                </div>       
                <!-- Copyright -->
                <div style="margin-top: 32px; padding-top: 28px; border-top: 1px solid #334155;">
                    <p style="color: #64748b; font-size: 13px; margin-bottom: 10px; font-weight: 500;">
                        © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                    </p>
                    <p style="color: #64748b; font-size: 13px;">
                        {{ $companyAddress ?? '1234 Street Name, City, State 12345' }}
                    </p>
                </div>
                <!-- Logo/Name -->
                <div style="margin-top: 28px; margin-bottom: 6px; border-top: 1px solid #202936; padding-top: 28px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr;">
                        <div class="row inline-flex flex-nowrap items-center justify-center">
                            <div style="margin-bottom:9px;">
                                <span class="text-[8px] text-zinc-500" >Powered by:</span>
                            </div>
                            <div>
                                <a href="{{ $appUrl }}" style="color: #f1f5f9; font-size: 22px; font-weight: 800; text-decoration: none; letter-spacing: -0.5px;">
                                    <img src="https://codeberg.org/mnobre/mnobre/raw/branch/main/media/LT-128.png" 
                                         alt="Laravel Teams"
                                         class="w-full h-full object-cover">
                                </a>
                            </div>
                        </div>
                        <div class="col-span-1 flex items-start justify-center">
<div class="col-span-1 flex items-center justify-center">
    <div style="display: flex; flex-direction: column; align-items: center; text-align: center; gap: 4px;">
        <!-- Title and author section -->
        <div style="display: flex; flex-direction: column; align-items: center;">
            <h2 style="font-weight: 900; margin: 0; line-height: 1.2;">Laravel Teams</h2>
            <div style="display: flex; align-items: center; justify-content: center; gap: 6px; margin-top: 2px;">
                <span style="font-size: 14px; font-weight: 500; color: #64748b;">by</span> 
                <span style="font-size: 20px; font-weight: 600; color: #0284c7;">mnobre</span>
            </div>
        </div>
        
        <!-- Tagline section -->
        <div style="margin-top: 4px;">
            <h3 style="color: #475569; font-size: 16px; font-weight: 400; margin: 0;">
                Collaboration made simple
            </h3>
        </div>
        
        <!-- Social icons with hover effects -->
        <div style="display: flex; align-items: center; justify-content: center; gap: 16px; margin-top: 12px;">
            <!-- Codeberg -->
            <a href="https://codeberg.org/{{ $codebergHandle ?? 'yourcompany' }}" 
               style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 10px; transition: all 0.2s ease; background: transparent;"
               onmouseover="this.style.background='#f1f5f9'; this.style.transform='translateY(-2px)'" 
               onmouseout="this.style.background='transparent'; this.style.transform='translateY(0)'"
               class="social-icon codeberg-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" style="width: 24px; height: 24px; transition: transform 0.2s ease;">
                    <linearGradient id="a-codeberg" x1="42506.715" x2="42624.223" y1="7642.54" y2="7408.031" gradientTransform="matrix(1.4006 0 0 -1.4006 -59281.402 10836.918)" gradientUnits="userSpaceOnUse">
                        <stop offset="0" style="stop-color:#fff;stop-opacity:0"/>
                        <stop offset=".495" style="stop-color:#71c2ff"/>
                        <stop offset="1" style="stop-color:#39aaff"/>
                    </linearGradient>
                    <path d="M260.8 129.9c-1.2 0-2.2.8-2.2 1.7v.3l98.6 369.7c47.7-20.5 88-55.1 115.6-99.1l-210-271.8c-.4-.5-1.2-.8-2-.8" style="opacity:.5;fill:url(#a-codeberg)"/>
                    <path d="M255 10.4C114 11 0 125.4 0 266.4c0 48.1 13.5 95.2 39.1 136l213.4-275.9c1.1-1.4 3.5-1.9 5.5-1.1.6.3 1.1.6 1.5 1.1l213.5 276c25.5-40.8 39.1-87.9 39.1-136 0-141.4-114.6-256-256-256-.4-.1-.7-.1-1.1-.1" style="fill:#2185d0"/>
                </svg>
            </a>
            
            <!-- LinkedIn -->
            <a href="https://linkedin.com/in/{{ $linkedinHandle ?? 'yourcompany' }}" 
               style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 10px; transition: all 0.2s ease; background: transparent;"
               onmouseover="this.style.background='#f1f5f9'; this.style.transform='translateY(-2px)'" 
               onmouseout="this.style.background='transparent'; this.style.transform='translateY(0)'"
               class="social-icon linkedin-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" style="width: 24px; height: 24px; transition: transform 0.2s ease;">
                    <path d="M56.9 512h398.2c31.4 0 56.9-25.5 56.9-56.9V56.9C512 25.5 486.5 0 455.1 0H56.9C25.5 0 0 25.5 0 56.9v398.2C0 486.5 25.5 512 56.9 512" style="fill:#007ebb"/>
                    <path d="M440.9 440.9h-76V311.5c0-35.5-13.5-55.3-41.6-55.3-30.5 0-46.5 20.6-46.5 55.3v129.4h-73.2V194.4h73.2v33.2s22-40.7 74.3-40.7 89.7 31.9 89.7 98v156zM116.3 162.1c-24.9 0-45.2-20.4-45.2-45.5s20.2-45.5 45.2-45.5 45.1 20.4 45.1 45.5-20.2 45.5-45.1 45.5M78.5 440.9h76.4V194.4H78.5z" style="fill:#fff"/>
                </svg>
            </a>
            
            <!-- GitHub -->
            <a href="https://github.com/{{ $githubHandle ?? 'yourcompany' }}" 
               style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 10px; transition: all 0.2s ease; background: transparent;"
               onmouseover="this.style.background='#f1f5f9'; this.style.transform='translateY(-2px)'" 
               onmouseout="this.style.background='transparent'; this.style.transform='translateY(0)'"
               class="social-icon github-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" style="width: 24px; height: 24px; transition: transform 0.2s ease;">
                    <path d="M256 6.3C114.6 6.3 0 120.9 0 262.3c0 113.3 73.3 209 175 242.9 12.8 2.2 17.6-5.4 17.6-12.2 0-6.1-.3-26.2-.3-47.7-64.3 11.8-81-15.7-86.1-30.1-2.9-7.4-15.4-30.1-26.2-36.2-9-4.8-21.8-16.6-.3-17 20.2-.3 34.6 18.6 39.4 26.2 23 38.7 59.8 27.8 74.6 21.1 2.2-16.6 9-27.8 16.3-34.2-57-6.4-116.5-28.5-116.5-126.4 0-27.8 9.9-50.9 26.2-68.8-2.6-6.4-11.5-32.6 2.6-67.8 0 0 21.4-6.7 70.4 26.2 20.5-5.8 42.2-8.6 64-8.6s43.5 2.9 64 8.6c49-33.3 70.4-26.2 70.4-26.2 14.1 35.2 5.1 61.4 2.6 67.8 16.3 17.9 26.2 40.6 26.2 68.8 0 98.2-59.8 120-116.8 126.4 9.3 8 17.3 23.4 17.3 47.4 0 34.2-.3 61.8-.3 70.4 0 6.7 4.8 14.7 17.6 12.2C438.7 471.3 512 375.3 512 262.3c0-141.4-114.6-256-256-256" style="fill:#0a0a0a"/>
                </svg>
            </a>
        </div>
    </div>
</div>

</div>
                    </div>
                </div>            
            </div>
        </div>
    </div>
  
    <!-- Vanilla JS for enhanced interactivity (works in supporting email clients) -->
    <script type="text/javascript">
        (function() {
            'use strict';
            
            // Only run in clients that support JavaScript (webmail, some mobile apps)
            if (window.addEventListener) {
                document.addEventListener('DOMContentLoaded', function() {
                    // Animate team avatar on hover
                    var teamAvatar = document.getElementById('teamAvatar');
                    if (teamAvatar) {
                        teamAvatar.addEventListener('mouseenter', function() {
                            this.style.transform = 'rotate(0deg) scale(1.1)';
                            this.style.transition = 'transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1)';
                        });
                        
                        teamAvatar.addEventListener('mouseleave', function() {
                            this.style.transform = 'rotate(-5deg) scale(1)';
                        });
                    }
                    
                    // Add pulse animation to CTA button
                    var ctaButton = document.querySelector('.cta-button');
                    if (ctaButton) {
                        setInterval(function() {
                            ctaButton.style.boxShadow = '0 12px 28px rgba(99, 102, 241, 0.4)';
                            setTimeout(function() {
                                ctaButton.style.boxShadow = '0 8px 20px rgba(99, 102, 241, 0.3)';
                            }, 500);
                        }, 3000);
                    }
                    
                    // Format dates to local timezone
                    var dateElements = document.querySelectorAll('.invited-date');
                    if (dateElements.length > 0 && '{{ $invitedDate ?? '' }}') {
                        var invitedDate = new Date('{{ $invitedDate ?? now() }}');
                        dateElements.forEach(function(el) {
                            el.textContent = invitedDate.toLocaleDateString('en-US', {
                                weekday: 'long',
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            });
                        });
                    }
                    
                    // Smooth countdown timer for expiration (if within 24h)
                    var expirationElement = document.querySelector('.expiration-date');
                    if (expirationElement && '{{ $expirationDate ?? '' }}') {
                        var expirationDate = new Date('{{ $expirationDate ?? '' }}');
                        var now = new Date();
                        var timeLeft = expirationDate - now;
                        
                        if (timeLeft > 0 && timeLeft < 86400000) { // Less than 24 hours
                            var hoursLeft = Math.floor(timeLeft / 3600000);
                            var minutesLeft = Math.floor((timeLeft % 3600000) / 60000);
                            
                            if (hoursLeft > 0) {
                                expirationElement.innerHTML = hoursLeft + ' hour' + (hoursLeft > 1 ? 's' : '') + ' ' + minutesLeft + ' minute' + (minutesLeft > 1 ? 's' : '');
                            } else {
                                expirationElement.innerHTML = minutesLeft + ' minute' + (minutesLeft > 1 ? 's' : '');
                            }
                        }
                    }
                    
                    // Copy fallback URL to clipboard (with user consent)
                    var fallbackLink = document.querySelector('.fallback-link');
                    if (fallbackLink) {
                        fallbackLink.addEventListener('click', function(e) {
                            var range = document.createRange();
                            range.selectNode(this);
                            window.getSelection().removeAllRanges();
                            window.getSelection().addRange(range);
                            document.execCommand('copy');
                            window.getSelection().removeAllRanges();
                            
                            // Show feedback (if possible)
                            var originalText = this.textContent;
                            this.textContent = '✓ Copied to clipboard!';
                            this.style.background = '#e6f7e6';
                            this.style.color = '#2e7d32';
                            
                            setTimeout(function() {
                                fallbackLink.textContent = originalText;
                                fallbackLink.style.background = '';
                                fallbackLink.style.color = '';
                            }, 2000);
                        });
                    }
                });
                
            }
        })();
    </script>
    
    <!-- No-JS Fallback -->
    <noscript>
        <style>
            .cta-button {
                animation: pulse 2s infinite;
            }
            @keyframes pulse {
                0% { box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3); }
                50% { box-shadow: 0 12px 28px rgba(99, 102, 241, 0.4); }
                100% { box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3); }
            }
        </style>
    </noscript>
</body>
</html>