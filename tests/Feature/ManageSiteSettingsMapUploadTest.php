<?php

use App\Filament\Pages\ManageSiteSettings;
use App\Models\SiteSettings;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create());
    Filament::setCurrentPanel('admin');
});

function makeSiteSettingsWithMap(string $path = 'settings/water-quality/old-map.webp'): SiteSettings
{
    $settings = SiteSettings::create([
        'phone' => '081100000000',
        'fixed_text' => ['id' => 'Halo', 'en' => 'Hello'],
        'about_content' => [
            'hero' => ['title' => ['id' => 'Judul Lama', 'en' => 'Old Title']],
        ],
        'about_legal_verified' => false,
        'about_lake_image_credit' => 'Kredit Danau',
        'org_chart_image_url' => 'settings/org-chart.png',
        'water_quality_map_image_url' => $path,
        'water_quality_map_image_credit' => 'Kredit Peta Lama',
    ]);

    Storage::disk('public')->put($path, 'old-image');

    return $settings;
}

it('accepts a valid :extension map image', function (string $extension) {
    $file = UploadedFile::fake()->image("station-map.{$extension}", 1200, 800);

    Livewire::test(ManageSiteSettings::class)
        ->fillForm([
            'water_quality_map_image_url' => $file,
            'water_quality_map_image_credit' => 'Dokumentasi TMCC',
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified();

    $settings = SiteSettings::query()->firstOrFail();

    expect($settings->water_quality_map_image_url)->toStartWith('settings/water-quality/');
    expect($settings->water_quality_map_image_credit)->toBe('Dokumentasi TMCC');
    Storage::disk('public')->assertExists($settings->water_quality_map_image_url);
})->with(['jpg', 'png', 'webp']);

it('replaces an existing map image and deletes the old file after commit', function () {
    $settings = makeSiteSettingsWithMap();
    $oldPath = $settings->water_quality_map_image_url;

    Livewire::test(ManageSiteSettings::class)
        ->fillForm([
            'water_quality_map_image_url' => UploadedFile::fake()->image('new-map.png', 1000, 700),
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $settings->refresh();

    expect($settings->water_quality_map_image_url)->not->toBe($oldPath);
    Storage::disk('public')->assertExists($settings->water_quality_map_image_url);
    Storage::disk('public')->assertMissing($oldPath);
});

it('removes the map image and deletes the old file after save', function () {
    $settings = makeSiteSettingsWithMap();
    $oldPath = $settings->water_quality_map_image_url;

    Livewire::test(ManageSiteSettings::class)
        ->fillForm(['water_quality_map_image_url' => null])
        ->call('save')
        ->assertHasNoFormErrors();

    $settings->refresh();

    expect($settings->water_quality_map_image_url)->toBeNull();
    Storage::disk('public')->assertMissing($oldPath);
});

it('does not delete the existing file when a new upload is cancelled', function () {
    $settings = makeSiteSettingsWithMap();
    $oldPath = $settings->water_quality_map_image_url;

    Livewire::test(ManageSiteSettings::class)
        ->fillForm(['water_quality_map_image_url' => UploadedFile::fake()->image('cancelled.png')]);

    $settings->refresh();

    expect($settings->water_quality_map_image_url)->toBe($oldPath);
    Storage::disk('public')->assertExists($oldPath);
    expect(Storage::disk('public')->allFiles('settings/water-quality'))->toBe([$oldPath]);
});

it('does not delete the existing file when removal is cancelled', function () {
    $settings = makeSiteSettingsWithMap();
    $oldPath = $settings->water_quality_map_image_url;

    Livewire::test(ManageSiteSettings::class)
        ->fillForm(['water_quality_map_image_url' => null]);

    $settings->refresh();

    expect($settings->water_quality_map_image_url)->toBe($oldPath);
    Storage::disk('public')->assertExists($oldPath);
});

it('cleans up a newly stored file and preserves the old path when the database update fails', function () {
    $settings = makeSiteSettingsWithMap();
    $oldPath = $settings->water_quality_map_image_url;

    $listener = function (): void {
        throw new RuntimeException('forced database failure');
    };

    Event::listen('eloquent.updating: '.SiteSettings::class, $listener);

    try {
        expect(function (): void {
            Livewire::test(ManageSiteSettings::class)
                ->fillForm([
                    'water_quality_map_image_url' => UploadedFile::fake()->image('will-fail.png'),
                ])
                ->call('save');
        })->toThrow(RuntimeException::class, 'forced database failure');
    } finally {
        Event::forget('eloquent.updating: '.SiteSettings::class);
    }

    $settings->refresh();

    expect($settings->water_quality_map_image_url)->toBe($oldPath);
    Storage::disk('public')->assertExists($oldPath);
    expect(Storage::disk('public')->allFiles('settings/water-quality'))->toBe([$oldPath]);
});

it('rejects :invalidType map uploads', function (string $name, string $mime): void {
    $file = UploadedFile::fake()->create($name, 100, $mime);

    Livewire::test(ManageSiteSettings::class)
        ->fillForm(['water_quality_map_image_url' => $file])
        ->call('save')
        ->assertHasFormErrors(['water_quality_map_image_url']);

    expect(SiteSettings::query()->first()?->water_quality_map_image_url)->toBeNull();
})->with([
    'pdf' => ['map.pdf', 'application/pdf'],
    'svg' => ['map.svg', 'image/svg+xml'],
    'gif' => ['map.gif', 'image/gif'],
]);

it('rejects a map image larger than 5 MB', function () {
    Livewire::test(ManageSiteSettings::class)
        ->fillForm([
            'water_quality_map_image_url' => UploadedFile::fake()->image('too-large.jpg')->size(6000),
        ])
        ->call('save')
        ->assertHasFormErrors(['water_quality_map_image_url']);

    expect(SiteSettings::query()->first()?->water_quality_map_image_url)->toBeNull();
});

it('keeps an unchanged stored path valid', function () {
    $settings = makeSiteSettingsWithMap();
    $oldPath = $settings->water_quality_map_image_url;

    Livewire::test(ManageSiteSettings::class)
        ->fillForm(['water_quality_map_image_credit' => 'Kredit Baru'])
        ->call('save')
        ->assertHasNoFormErrors();

    $settings->refresh();

    expect($settings->water_quality_map_image_url)->toBe($oldPath);
    expect($settings->water_quality_map_image_credit)->toBe('Kredit Baru');
    Storage::disk('public')->assertExists($oldPath);
});

it('rejects a forged map path and does not delete files', function () {
    $settings = makeSiteSettingsWithMap();
    $oldPath = $settings->water_quality_map_image_url;
    $forgedPath = 'settings/water-quality/forged.webp';
    Storage::disk('public')->put($forgedPath, 'forged-image');

    Livewire::test(ManageSiteSettings::class)
        ->fillForm(['water_quality_map_image_url' => [$forgedPath]])
        ->call('save')
        ->assertHasFormErrors(['water_quality_map_image_url']);

    $settings->refresh();

    expect($settings->water_quality_map_image_url)->toBe($oldPath);
    Storage::disk('public')->assertExists($oldPath);
    Storage::disk('public')->assertExists($forgedPath);
});

it('saves and clears the map credit independently', function () {
    $settings = makeSiteSettingsWithMap();

    Livewire::test(ManageSiteSettings::class)
        ->fillForm(['water_quality_map_image_credit' => 'Sumber: TMCC'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($settings->fresh()->water_quality_map_image_credit)->toBe('Sumber: TMCC');

    Livewire::test(ManageSiteSettings::class)
        ->fillForm(['water_quality_map_image_credit' => null])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($settings->fresh()->water_quality_map_image_credit)->toBeNull();
});

it('preserves all other site settings when saving the map', function () {
    $settings = makeSiteSettingsWithMap();
    $existing = $settings->fresh()->only([
        'phone',
        'fixed_text',
        'about_legal_verified',
        'about_lake_image_credit',
    ]);

    Livewire::test(ManageSiteSettings::class)
        ->fillForm([
            'water_quality_map_image_url' => UploadedFile::fake()->image('preserve.png'),
            'water_quality_map_image_credit' => 'Kredit Peta',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $after = $settings->fresh();

    expect($after->only(array_keys($existing)))->toEqual($existing);
    expect($after->water_quality_map_image_credit)->toBe('Kredit Peta');
});
