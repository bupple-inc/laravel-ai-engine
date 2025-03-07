<?php

use Bupple\Engine\Facades\BuppleEngine;

test('bupple engine facade is registered', function () {
    expect(class_exists(BuppleEngine::class))->toBeTrue();
});

test('bupple engine service provider is registered', function () {
    $providers = app()->getLoadedProviders();
    expect($providers)->toHaveKey('Bupple\Engine\Providers\BuppleEngineServiceProvider');
});
