<?php

use Bupple\Engine\Facades\Engine;

test('bupple engine facade is registered', function () {
    expect(class_exists(Engine::class))->toBeTrue();
});

test('bupple engine service provider is registered', function () {
    $providers = app()->getLoadedProviders();
    expect($providers)->toHaveKey('Bupple\Engine\Providers\BuppleEngineServiceProvider');
});
