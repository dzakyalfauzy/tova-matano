<?php

use App\Filament\Pages\ManageSiteSettings;
use App\Models\SiteSettings;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Livewire\Livewire;

beforeEach(function () {
    Storage::fake('public');
    $this->actingAs(User::factory()->create());
    Filament::setCurrentPanel('admin');
});

function pageSource(string $page): string
{
    return file_get_contents(resource_path("js/Pages/{$page}.jsx"));
}

it('adds the three nullable page hero columns to site settings', function () {
    foreach (['partnership_hero_image_url', 'program_hero_image_url', 'contact_hero_image_url'] as $column) {
        expect(Schema::hasColumn('site_settings', $column))->toBeTrue();
    }

    $settings = SiteSettings::create([]);

    expect($settings->partnership_hero_image_url)->toBeNull()
        ->and($settings->program_hero_image_url)->toBeNull()
        ->and($settings->contact_hero_image_url)->toBeNull();
});

it('defines a reversible migration for the page hero columns', function () {
    $migration = file_get_contents(base_path('database/migrations/2026_11_14_110000_add_page_hero_images_to_site_settings_table.php'));

    expect($migration)->toContain('partnership_hero_image_url')
        ->and($migration)->toContain('program_hero_image_url')
        ->and($migration)->toContain('contact_hero_image_url')
        ->and($migration)->toContain('dropColumn')
        ->and($migration)->toContain('public function up(): void')
        ->and($migration)->toContain('public function down(): void');
});

it('accepts a valid :field image into its dedicated directory', function (string $field, string $directory, string $extension) {
    Livewire::test(ManageSiteSettings::class)
        ->fillForm([
            $field => UploadedFile::fake()->image("hero.{$extension}", 1600, 800),
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $settings = SiteSettings::query()->firstOrFail();

    expect($settings->{$field})->toStartWith($directory.'/');
    Storage::disk('public')->assertExists($settings->{$field});
})->with([
    'partnership jpg' => ['partnership_hero_image_url', 'settings/partnership', 'jpg'],
    'partnership webp' => ['partnership_hero_image_url', 'settings/partnership', 'webp'],
    'program png' => ['program_hero_image_url', 'settings/program', 'png'],
    'program jpg' => ['program_hero_image_url', 'settings/program', 'jpg'],
    'contact webp' => ['contact_hero_image_url', 'settings/contact', 'webp'],
    'contact png' => ['contact_hero_image_url', 'settings/contact', 'png'],
]);

it('rejects unsupported file types for page hero uploads', function (string $field) {
    Livewire::test(ManageSiteSettings::class)
        ->fillForm([
            $field => UploadedFile::fake()->create('hero.pdf', 200, 'application/pdf'),
        ])
        ->call('save')
        ->assertHasFormErrors([$field]);

    expect(SiteSettings::query()->first()?->{$field})->toBeNull();
})->with([
    'partnership' => ['partnership_hero_image_url'],
    'program' => ['program_hero_image_url'],
    'contact' => ['contact_hero_image_url'],
]);

it('rejects page hero images larger than 5 MB', function (string $field) {
    Livewire::test(ManageSiteSettings::class)
        ->fillForm([
            $field => UploadedFile::fake()->image('huge.jpg')->size(6000),
        ])
        ->call('save')
        ->assertHasFormErrors([$field]);

    expect(SiteSettings::query()->first()?->{$field})->toBeNull();
})->with([
    'partnership' => ['partnership_hero_image_url'],
    'program' => ['program_hero_image_url'],
    'contact' => ['contact_hero_image_url'],
]);

it('keeps the three hero settings independent when uploading one image', function () {
    $settings = SiteSettings::create([
        'partnership_hero_image_url' => null,
        'program_hero_image_url' => 'settings/program/old.jpg',
        'contact_hero_image_url' => 'settings/contact/old.jpg',
    ]);

    Storage::disk('public')->put('settings/program/old.jpg', 'old');
    Storage::disk('public')->put('settings/contact/old.jpg', 'old');

    Livewire::test(ManageSiteSettings::class)
        ->fillForm([
            'partnership_hero_image_url' => UploadedFile::fake()->image('new-partnership.jpg'),
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $settings->refresh();

    expect($settings->partnership_hero_image_url)->toStartWith('settings/partnership/')
        ->and($settings->program_hero_image_url)->toBe('settings/program/old.jpg')
        ->and($settings->contact_hero_image_url)->toBe('settings/contact/old.jpg');
});

it('removes one hero image without affecting the other two settings', function () {
    $settings = SiteSettings::create([
        'partnership_hero_image_url' => 'settings/partnership/old.jpg',
        'program_hero_image_url' => 'settings/program/old.jpg',
        'contact_hero_image_url' => 'settings/contact/old.jpg',
    ]);

    Storage::disk('public')->put('settings/partnership/old.jpg', 'old');
    Storage::disk('public')->put('settings/program/old.jpg', 'old');
    Storage::disk('public')->put('settings/contact/old.jpg', 'old');

    Livewire::test(ManageSiteSettings::class)
        ->fillForm(['program_hero_image_url' => null])
        ->call('save')
        ->assertHasNoFormErrors();

    $settings->refresh();

    expect($settings->program_hero_image_url)->toBeNull()
        ->and($settings->partnership_hero_image_url)->toBe('settings/partnership/old.jpg')
        ->and($settings->contact_hero_image_url)->toBe('settings/contact/old.jpg');
});

it('passes the partnership hero image to the public partnership page', function () {
    SiteSettings::create([
        'partnership_hero_image_url' => 'settings/partnership/hero.jpg',
        'program_hero_image_url' => 'settings/program/hero.jpg',
        'contact_hero_image_url' => 'settings/contact/hero.jpg',
    ]);

    $this->get('/partnership')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Partnerships')
            ->where('settings.partnership_hero_image_url', 'settings/partnership/hero.jpg')
        );
});

it('passes the program hero image to the public program page', function () {
    SiteSettings::create([
        'partnership_hero_image_url' => 'settings/partnership/hero.jpg',
        'program_hero_image_url' => 'settings/program/hero.jpg',
        'contact_hero_image_url' => 'settings/contact/hero.jpg',
    ]);

    $this->get('/program')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Programs')
            ->where('settings.program_hero_image_url', 'settings/program/hero.jpg')
        );
});

it('passes the contact hero image to the public contact page', function () {
    SiteSettings::create([
        'partnership_hero_image_url' => 'settings/partnership/hero.jpg',
        'program_hero_image_url' => 'settings/program/hero.jpg',
        'contact_hero_image_url' => 'settings/contact/hero.jpg',
    ]);

    $this->get('/kontak')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Contact')
            ->where('settings.contact_hero_image_url', 'settings/contact/hero.jpg')
        );
});

it('renders all three public hero pages safely without a settings record', function () {
    foreach (['/partnership' => 'Partnerships', '/program' => 'Programs', '/kontak' => 'Contact'] as $url => $component) {
        $response = $this->get($url);

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component($component));

        $props = $response->inertiaProps();
        expect(array_key_exists('settings', $props))->toBeTrue()
            ->and($props['settings'])->toBeNull();
    }
});

it('uses each page specific hero field with a solid navy fallback', function () {
    $pages = [
        'Partnerships' => 'partnership_hero_image_url',
        'Programs' => 'program_hero_image_url',
        'Contact' => 'contact_hero_image_url',
    ];

    foreach ($pages as $page => $field) {
        $source = pageSource($page);

        expect($source)->toContain("settings?.{$field}")
            ->and($source)->toContain("/storage/\${settings.{$field}}")
            ->and($source)->toContain('bg-brand-navy')
            ->and($source)->toContain('object-cover');
    }
});

it('shows the exact client contact title and subtitle', function () {
    $source = pageSource('Contact');

    expect($source)->toContain('Hubungi Kami')
        ->and($source)->toContain('Ada saran dan masukan, pertanyaan atau ingin berkolaborasi? Kami siap mendengar')
        ->and($source)->not->toContain('Ada pertanyaan atau ingin berkolaborasi? Kami siap mendengar.');
});

it('keeps existing contact content and settings intact while adding a hero image', function () {
    SiteSettings::create([
        'address' => 'Jl. Poros Sorowako No. 12',
        'phone' => '+62 451 123456',
        'email' => 'info@tovamatano.org',
        'contact_hero_image_url' => 'settings/contact/hero.jpg',
    ]);

    $this->get('/kontak')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Contact')
            ->where('settings.address', 'Jl. Poros Sorowako No. 12')
            ->where('settings.phone', '+62 451 123456')
            ->where('settings.email', 'info@tovamatano.org')
            ->where('settings.contact_hero_image_url', 'settings/contact/hero.jpg')
        );
});
