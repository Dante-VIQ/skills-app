<?php
use Illuminate\Support\Str;

$teamDenomination = 'Team';

return [
    'denomination_singular' => $teamDenomination,
    'denomination_plural' => Str::plural($teamDenomination),
    'denomination_plural_lower' => strtolower(str_replace(' ', '_', Str::plural($teamDenomination))),
    'denomination_singular_lower' => strtolower(str_replace(' ', '_', $teamDenomination)),    
    // Role definitions
    'roles' => [
        'owner' => 'Owner',
        'member' => 'Member',
    ],
    
    'info' => [
        'name' => env('MAIL_FROM_NAME', config('app.name')),
        'address' => env('MAIL_FROM_ADDRESS', "mazchord[@]gmail[.]com"),
        'codeberg' => env('CODEBERG_HANDLE', "mnobre"),
        'linkedin' => env('LINKEDIN_HANDLE', "mnobre"),
        'github' => env('GITHUB_HANDLE', "m-nobre"),
    ],
];