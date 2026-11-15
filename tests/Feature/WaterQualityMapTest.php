<?php

use App\Models\SiteSettings;
use App\Models\WaterQualityRecord;
use App\Models\WaterSamplingPoint;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    SiteSettings::create([
        'water_quality_map_image_url' => 'settings/water-quality/test-map.webp',
        'water_quality_map_image_credit' => 'Dokumentasi TMCC',
    ]);
});

function waterQualityPageSource(): string
{
    return file_get_contents(resource_path('js/Pages/WaterQuality.jsx'));
}

function mapSamplingPoint(string $name = 'Titik Peta'): WaterSamplingPoint
{
    return WaterSamplingPoint::create([
        'name' => $name,
        'coordinates' => '-2.4500, 121.3400',
        'is_active' => true,
    ]);
}

it('displays map section when water_quality_map_image_url is available', function () {
    $response = $this->get('/kualitas-air');
    $props = $response->inertiaProps();

    expect($props['settings']['water_quality_map_image_url'])->toBe('settings/water-quality/test-map.webp');
    expect($props['settings']['water_quality_map_image_credit'])->toBe('Dokumentasi TMCC');

    $source = waterQualityPageSource();

    expect($source)->toContain('settings?.water_quality_map_image_url &&')
        ->and($source)->toContain('/storage/${settings?.water_quality_map_image_url}')
        ->and($source)->toContain('alt="Peta titik sampling kualitas air Danau Matano"');

    $response->assertInertia(fn (Assert $page) => $page
        ->component('WaterQuality')
        ->where('settings.water_quality_map_image_url', 'settings/water-quality/test-map.webp')
        ->where('settings.water_quality_map_image_credit', 'Dokumentasi TMCC')
    );
});

it('does not display map section when water_quality_map_image_url is null', function () {
    $settings = SiteSettings::firstOrFail();
    $settings->update([
        'water_quality_map_image_url' => null,
        'water_quality_map_image_credit' => null,
    ]);

    $response = $this->get('/kualitas-air');
    $props = $response->inertiaProps();

    expect($props['settings']['water_quality_map_image_url'])->toBeNull();

    $source = waterQualityPageSource();

    expect($source)->toContain('{settings?.water_quality_map_image_url && (');

    $response->assertInertia(fn (Assert $page) => $page
        ->component('WaterQuality')
        ->where('settings.water_quality_map_image_url', null)
    );
});

it('displays map credit when water_quality_map_image_credit is available', function () {
    $response = $this->get('/kualitas-air');
    $props = $response->inertiaProps();

    expect($props['settings']['water_quality_map_image_credit'])->toBe('Dokumentasi TMCC');

    $source = waterQualityPageSource();

    expect($source)->toContain('{settings?.water_quality_map_image_credit && (');

    $response->assertInertia(fn (Assert $page) => $page
        ->component('WaterQuality')
        ->where('settings.water_quality_map_image_credit', 'Dokumentasi TMCC')
    );
});

it('does not display map credit when water_quality_map_image_credit is null', function () {
    $settings = SiteSettings::firstOrFail();
    $settings->update([
        'water_quality_map_image_url' => 'settings/water-quality/test-map.webp',
        'water_quality_map_image_credit' => null,
    ]);

    $response = $this->get('/kualitas-air');
    $props = $response->inertiaProps();

    expect($props['settings']['water_quality_map_image_credit'])->toBeNull();

    $source = waterQualityPageSource();

    expect($source)->toContain('{settings?.water_quality_map_image_credit && (');

    $response->assertInertia(fn (Assert $page) => $page
        ->component('WaterQuality')
        ->where('settings.water_quality_map_image_credit', null)
    );
});

it('renders the page when the settings prop is null', function () {
    SiteSettings::query()->delete();

    $response = $this->get('/kualitas-air');

    $response->assertOk()->assertInertia(fn (Assert $page) => $page
        ->component('WaterQuality')
        ->where('settings', null)
    );

    $source = waterQualityPageSource();

    expect($source)->toContain('settings?.water_quality_map_image_url &&')
        ->and($source)->toContain('/storage/${settings?.water_quality_map_image_url}')
        ->and($source)->toContain('settings?.water_quality_map_image_credit &&');
});

it('existing six parameters and charts still render correctly with map section', function () {
    $point = mapSamplingPoint();

    WaterQualityRecord::create([
        'water_sampling_point_id' => $point->id,
        'recorded_at' => now()->subDay(),
        'tss' => 10,
        'tds' => 100,
        'dissolved_oxygen' => 6,
        'temperature' => 28,
        'clarity_cm' => 400,
        'ph' => 7.2,
    ]);

    $response = $this->get('/kualitas-air');
    $props = $response->inertiaProps();

    expect($props['summary'])->toHaveCount(6);
    expect($props['waterQualityConfig']['parameters'])->toHaveCount(6);

    $response->assertInertia(fn (Assert $page) => $page
        ->component('WaterQuality')
        ->has('summary', 6)
        ->has('waterQualityConfig.parameters', 6)
    );
});

it('filter and pagination still work correctly with map section', function () {
    $point = mapSamplingPoint();

    foreach (range(1, 25) as $index) {
        WaterQualityRecord::create([
            'water_sampling_point_id' => $point->id,
            'recorded_at' => now()->subDays($index),
            'tss' => $index,
            'ph' => 7.2,
        ]);
    }

    $response = $this->get('/kualitas-air?sampling_point='.$point->id.'&range=month');
    $props = $response->inertiaProps();

    expect($props['paginatedRecords']['total'])->toBe(25)
        ->and($props['paginatedRecords']['data'])->toHaveCount(10)
        ->and($props['filters']['sampling_point'])->toBe($point->id);

    $response->assertInertia(fn (Assert $page) => $page
        ->component('WaterQuality')
        ->where('paginatedRecords.total', 25)
        ->where('filters.sampling_point', $point->id)
    );
});

it('does not use leaflet openstreetmap maps api gps coordinates or seeded coordinates', function () {
    $source = waterQualityPageSource();

    foreach ([
        'leaflet',
        'openstreetmap',
        'google.maps',
        'maps.googleapis',
        'tilelayer',
        'latitude',
        'longitude',
        'coordinates',
    ] as $forbidden) {
        expect(str_contains(strtolower($source), $forbidden))->toBeFalse();
    }
});
