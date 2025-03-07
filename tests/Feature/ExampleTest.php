<?php

use Illuminate\Support\Facades\Config;

test('bupple engine config is loaded', function () {
    expect(Config::has('bupple'))->toBeTrue();
});

test('bupple engine can be configured', function () {
    Config::set('bupple.key', 'test-value');
    expect(Config::get('bupple.key'))->toBe('test-value');
});
