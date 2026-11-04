<?php

use App\Filament\Resources\WaterQualityRecords\Pages\CreateWaterQualityRecord;
use App\Filament\Resources\WaterQualityRecords\Pages\EditWaterQualityRecord;
use App\Filament\Resources\WaterQualityRecords\Pages\ListWaterQualityRecords;
use App\Models\User;
use App\Models\WaterQualityRecord;
use App\Models\WaterSamplingPoint;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Schema;
use Inertia\Testing\AssertableInertia as Assert;
use Livewire\Livewire;

if (! function_exists('wqSamplingPoint')) {
    function wqSamplingPoint(string $name = 'Titik Uji'): WaterSamplingPoint
    {
        return WaterSamplingPoint::create([
            'name' => $name,
            'coordinates' => '-2.4500, 121.3400',
            'is_active' => true,
        ]);
    }
}

if (! function_exists('wqRecord')) {
    /**
     * @param  array<string, mixed>  $attributes
     */
    function wqRecord(array $attributes = []): WaterQualityRecord
    {
        return WaterQualityRecord::create(array_merge([
            'recorded_at' => now(),
            'ph' => 7.2,
        ], $attributes));
    }
}

it('adds nullable monitoring columns and keeps legacy records intact', function () {
    expect(Schema::hasColumns('water_quality_records', ['tss', 'tds', 'dissolved_oxygen', 'temperature']))->toBeTrue();

    $legacy = WaterQualityRecord::create([
        'recorded_at' => now()->subYear(),
        'ph' => 7.1,
        'clarity_cm' => 420,
    ])->fresh();

    expect($legacy->ph)->toEqual(7.1)
        ->and($legacy->clarity_cm)->toEqual(420.0)
        ->and($legacy->tss)->toBeNull()
        ->and($legacy->tds)->toBeNull()
        ->and($legacy->dissolved_oxygen)->toBeNull()
        ->and($legacy->temperature)->toBeNull();
});

it('renders the dashboard with six parameter datasets and averages that ignore nulls', function () {
    $point = wqSamplingPoint();

    wqRecord([
        'water_sampling_point_id' => $point->id,
        'recorded_at' => now()->subDay(),
        'tss' => 10,
        'tds' => 100,
        'dissolved_oxygen' => 6,
        'temperature' => 28,
        'clarity_cm' => 400,
        'ph' => 7.2,
    ]);

    wqRecord([
        'water_sampling_point_id' => $point->id,
        'recorded_at' => now(),
        'tss' => 20,
        'tds' => null,
        'dissolved_oxygen' => null,
        'temperature' => null,
        'clarity_cm' => 500,
        'ph' => 7.4,
    ]);

    $response = $this->get('/kualitas-air');

    $response->assertOk()->assertInertia(fn (Assert $page) => $page
        ->component('WaterQuality')
        ->has('records', 2)
        ->has('summary', 6)
        ->has('waterQualityConfig.parameters', 6)
        ->has('perPageOptions', 4)
        ->has('paginatedRecords.data', 2)
        ->where('filters.per_page', 10)
        ->where('waterQualityConfig.parameters.tss.unit', 'mg/L')
        ->where('waterQualityConfig.parameters.ph.threshold_type', 'range')
        ->where('waterQualityConfig.parameters.clarity_cm.threshold_type', 'none')
    );

    $props = $response->inertiaProps();

    expect($props['summary']['tss']['average'])->toEqual(15.0)
        ->and($props['summary']['tss']['count'])->toBe(2)
        ->and($props['summary']['tds']['average'])->toEqual(100.0)
        ->and($props['summary']['tds']['count'])->toBe(1)
        ->and($props['summary']['dissolved_oxygen']['average'])->toEqual(6.0)
        ->and($props['summary']['dissolved_oxygen']['count'])->toBe(1)
        ->and($props['summary']['ph']['average'])->toEqual(7.3)
        ->and($props['summary']['clarity_cm']['average'])->toEqual(450.0)
        ->and(array_keys($props['summary']))->toBe(['tss', 'ph', 'tds', 'dissolved_oxygen', 'temperature', 'clarity_cm']);
});

it('paginates the measurement history by ten records while keeping the chart dataset complete', function () {
    $point = wqSamplingPoint();

    foreach (range(1, 12) as $index) {
        wqRecord([
            'water_sampling_point_id' => $point->id,
            'recorded_at' => now()->subDays($index),
            'tss' => $index,
        ]);
    }

    $response = $this->get('/kualitas-air');
    $props = $response->inertiaProps();

    expect($props['paginatedRecords']['data'])->toHaveCount(10)
        ->and($props['paginatedRecords']['total'])->toBe(12)
        ->and($props['paginatedRecords']['per_page'])->toBe(10)
        ->and($props['paginatedRecords']['last_page'])->toBe(2)
        ->and($props['records'])->toHaveCount(12)
        ->and($props['paginatedRecords']['from'])->toBe(1)
        ->and($props['paginatedRecords']['to'])->toBe(10);

    $secondPage = $this->get('/kualitas-air?page=2')->inertiaProps();

    expect($secondPage['paginatedRecords']['current_page'])->toBe(2)
        ->and($secondPage['paginatedRecords']['data'])->toHaveCount(2)
        ->and($secondPage['records'])->toHaveCount(12);
});

it('supports page size selection and resets pagination independently of filters', function () {
    $point = wqSamplingPoint();

    foreach (range(1, 12) as $index) {
        wqRecord([
            'water_sampling_point_id' => $point->id,
            'recorded_at' => now()->subDays($index),
        ]);
    }

    $props = $this->get('/kualitas-air?per_page=15')->inertiaProps();

    expect($props['filters']['per_page'])->toBe(15)
        ->and($props['paginatedRecords']['per_page'])->toBe(15)
        ->and($props['paginatedRecords']['data'])->toHaveCount(12);

    $invalid = $this->get('/kualitas-air?per_page=999')->inertiaProps();

    expect($invalid['paginatedRecords']['per_page'])->toBe(10);
});

it('keeps the sampling point filter when navigating pages', function () {
    $pointA = wqSamplingPoint('Titik A');
    $pointB = wqSamplingPoint('Titik B');

    foreach (range(1, 11) as $index) {
        wqRecord([
            'water_sampling_point_id' => $pointA->id,
            'recorded_at' => now()->subDays($index),
        ]);
    }

    foreach (range(1, 3) as $index) {
        wqRecord([
            'water_sampling_point_id' => $pointB->id,
            'recorded_at' => now()->subDays($index),
        ]);
    }

    $firstPage = $this->get('/kualitas-air?sampling_point='.$pointA->id.'&range=all&page=2')->inertiaProps();

    expect($firstPage['filters']['sampling_point'])->toBe($pointA->id)
        ->and($firstPage['paginatedRecords']['total'])->toBe(11)
        ->and($firstPage['paginatedRecords']['current_page'])->toBe(2)
        ->and($firstPage['paginatedRecords']['data'])->toHaveCount(1)
        ->and($firstPage['paginatedRecords']['data'][0]['water_sampling_point_id'])->toBe($pointA->id)
        ->and($firstPage['paginatedRecords']['prev_page_url'])->toContain('sampling_point='.$pointA->id);
});

it('applies the date range filter to charts, summary, and history', function () {
    $point = wqSamplingPoint();

    wqRecord([
        'water_sampling_point_id' => $point->id,
        'recorded_at' => now()->subDays(10),
        'tss' => 40,
        'ph' => 7.0,
    ]);

    wqRecord([
        'water_sampling_point_id' => $point->id,
        'recorded_at' => now(),
        'tss' => 10,
        'ph' => 7.5,
    ]);

    $lastWeek = $this->get('/kualitas-air?range=week')->inertiaProps();

    expect($lastWeek['records'])->toHaveCount(1)
        ->and($lastWeek['paginatedRecords']['total'])->toBe(1)
        ->and($lastWeek['summary']['tss']['average'])->toEqual(10.0)
        ->and($lastWeek['filters']['range'])->toBe('week');

    $allTime = $this->get('/kualitas-air?range=all')->inertiaProps();

    expect($allTime['records'])->toHaveCount(2)
        ->and($allTime['summary']['tss']['average'])->toEqual(25.0);
});

it('sorts the measurement history newest first', function () {
    $older = wqRecord(['recorded_at' => now()->subDays(3), 'ph' => 6.9]);
    $newer = wqRecord(['recorded_at' => now(), 'ph' => 7.6]);

    $props = $this->get('/kualitas-air')->inertiaProps();

    expect($props['paginatedRecords']['data'][0]['id'])->toBe($newer->id)
        ->and($props['paginatedRecords']['data'][1]['id'])->toBe($older->id);
});

it('handles an empty dataset without producing averages or statuses from zero', function () {
    $props = $this->get('/kualitas-air')->inertiaProps();

    expect($props['records'])->toHaveCount(0)
        ->and($props['paginatedRecords']['total'])->toBe(0)
        ->and($props['paginatedRecords']['data'])->toHaveCount(0)
        ->and($props['summary']['tss']['average'])->toBeNull()
        ->and($props['summary']['tss']['status'])->toBe('no_data')
        ->and($props['summary']['temperature']['status'])->toBe('no_data');
});

it('creates a record with all six parameters from the admin panel', function () {
    $this->actingAs(User::factory()->create());
    Filament::setCurrentPanel('admin');

    $point = wqSamplingPoint();

    Livewire::test(CreateWaterQualityRecord::class)
        ->fillForm([
            'water_sampling_point_id' => $point->id,
            'recorded_at' => now()->toDateTimeString(),
            'tss' => 12.5,
            'ph' => 7.3,
            'tds' => 210.5,
            'dissolved_oxygen' => 6.8,
            'temperature' => 28.4,
            'clarity_cm' => 470,
            'notes' => 'Pengukuran uji.',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('water_quality_records', [
        'water_sampling_point_id' => $point->id,
        'tss' => 12.5,
        'ph' => 7.3,
        'tds' => 210.5,
        'dissolved_oxygen' => 6.8,
        'temperature' => 28.4,
        'clarity_cm' => 470,
    ]);
});

it('rejects invalid numeric values in the admin form', function () {
    $this->actingAs(User::factory()->create());
    Filament::setCurrentPanel('admin');

    $point = wqSamplingPoint();

    Livewire::test(CreateWaterQualityRecord::class)
        ->fillForm([
            'water_sampling_point_id' => $point->id,
            'recorded_at' => now()->toDateTimeString(),
            'tss' => -5,
            'ph' => 20,
            'dissolved_oxygen' => -1,
        ])
        ->call('create')
        ->assertHasFormErrors(['tss', 'ph', 'dissolved_oxygen']);

    $this->assertDatabaseCount('water_quality_records', 0);
});

it('updates monitoring values from the admin panel without losing existing pH and clarity data', function () {
    $this->actingAs(User::factory()->create());
    Filament::setCurrentPanel('admin');

    $point = wqSamplingPoint();
    $record = wqRecord([
        'water_sampling_point_id' => $point->id,
        'ph' => 7.1,
        'clarity_cm' => 430,
    ]);

    Livewire::test(EditWaterQualityRecord::class, ['record' => $record->getRouteKey()])
        ->fillForm([
            'tss' => 18.2,
            'tds' => 320.4,
            'dissolved_oxygen' => 5.9,
            'temperature' => 29.1,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $fresh = $record->fresh();

    expect($fresh->tss)->toEqual(18.2)
        ->and($fresh->tds)->toEqual(320.4)
        ->and($fresh->dissolved_oxygen)->toEqual(5.9)
        ->and($fresh->temperature)->toEqual(29.1)
        ->and($fresh->ph)->toEqual(7.1)
        ->and($fresh->clarity_cm)->toEqual(430.0);
});

it('loads the admin list page with the extended columns and export action', function () {
    $this->actingAs(User::factory()->create());
    Filament::setCurrentPanel('admin');

    wqRecord(['tss' => 15, 'tds' => 200, 'dissolved_oxygen' => 7, 'temperature' => 28]);

    Livewire::test(ListWaterQualityRecords::class)->assertOk();
});
