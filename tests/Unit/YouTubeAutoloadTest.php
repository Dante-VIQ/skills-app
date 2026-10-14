<?php

it('autoloads the YouTube service and controller classes', function () {
    expect(class_exists(App\Services\YouTubeService::class))->toBeTrue();
    expect(class_exists(App\Http\Controllers\YouTubeController::class))->toBeTrue();
});
