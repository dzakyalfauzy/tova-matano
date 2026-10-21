<?php

use App\Models\SiteSettings;

test('it returns correct fixed text based on locale parameter', function () {
    $settings = new SiteSettings([
        'fixed_text' => [
            'id' => 'Pelindung Danau Matano',
            'en' => 'Protector of Lake Matano',
        ],
    ]);

    expect($settings->getFixedText('id'))->toBe('Pelindung Danau Matano');
    expect($settings->getFixedText('en'))->toBe('Protector of Lake Matano');
});

test('it falls back to id when current locale translation is empty', function () {
    $settings = new SiteSettings([
        'fixed_text' => [
            'id' => 'Pelindung Danau Matano',
            'en' => '',
        ],
    ]);

    expect($settings->getFixedText('en'))->toBe('Pelindung Danau Matano');
});

test('it returns null when fixed text is completely empty', function () {
    $settings = new SiteSettings([
        'fixed_text' => [
            'id' => '',
            'en' => '',
        ],
    ]);

    expect($settings->getFixedText('id'))->toBeNull();
    expect($settings->getFixedText('en'))->toBeNull();

    $emptySettings = new SiteSettings(['fixed_text' => null]);
    expect($emptySettings->getFixedText('id'))->toBeNull();
});
