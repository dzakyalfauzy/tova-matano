<?php

use App\Models\SiteSettings;

test('it allows null water quality map fields', function () {
    $settings = SiteSettings::create();

    $settings->refresh();

    expect($settings->water_quality_map_image_url)->toBeNull();
    expect($settings->water_quality_map_image_credit)->toBeNull();
});

test('it persists the water quality map path and credit', function () {
    $settings = SiteSettings::create([
        'water_quality_map_image_url' => 'settings/water-quality/station-map.webp',
        'water_quality_map_image_credit' => 'Dokumentasi TMCC',
    ]);

    $settings->refresh();

    expect($settings->water_quality_map_image_url)->toBe('settings/water-quality/station-map.webp');
    expect($settings->water_quality_map_image_credit)->toBe('Dokumentasi TMCC');
});

test('it can clear the water quality map fields back to null', function () {
    $settings = SiteSettings::create([
        'water_quality_map_image_url' => 'settings/water-quality/station-map.webp',
        'water_quality_map_image_credit' => 'Dokumentasi TMCC',
    ]);

    $settings->update([
        'water_quality_map_image_url' => null,
        'water_quality_map_image_credit' => null,
    ]);

    $settings->refresh();

    expect($settings->water_quality_map_image_url)->toBeNull();
    expect($settings->water_quality_map_image_credit)->toBeNull();
});

test('updating the water quality map fields preserves existing settings', function () {
    $settings = SiteSettings::create([
        'phone' => '081100000000',
        'fixed_text' => ['id' => 'Halo', 'en' => 'Hello'],
        'about_content' => [
            'hero' => ['title' => ['id' => 'Judul Lama', 'en' => 'Old Title']],
        ],
        'about_legal_verified' => true,
        'about_lake_image_credit' => 'Dokumentasi Danau',
        'org_chart_image_url' => 'settings/org-chart.png',
    ]);

    $existingFields = [
        'phone',
        'fixed_text',
        'about_content',
        'about_legal_verified',
        'about_lake_image_credit',
        'org_chart_image_url',
    ];

    $before = $settings->fresh()->only($existingFields);

    $settings->update([
        'water_quality_map_image_url' => 'settings/water-quality/station-map.webp',
        'water_quality_map_image_credit' => 'Dokumentasi TMCC',
    ]);

    $after = $settings->fresh();

    expect($after->only($existingFields))->toEqual($before);
    expect($after->water_quality_map_image_url)->toBe('settings/water-quality/station-map.webp');
    expect($after->water_quality_map_image_credit)->toBe('Dokumentasi TMCC');
});
