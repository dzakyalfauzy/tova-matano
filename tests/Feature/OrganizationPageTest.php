<?php

use App\Filament\Resources\OrgCategories\Pages\CreateOrgCategory;
use App\Filament\Resources\OrgCategories\Pages\EditOrgCategory;
use App\Models\OrgCategory;
use App\Models\OrgPerson;
use App\Models\SiteSettings;
use App\Models\User;
use Filament\Facades\Filament;
use Inertia\Testing\AssertableInertia as Assert;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
    Filament::setCurrentPanel('admin');
});

function organizationPageSource(): string
{
    return file_get_contents(resource_path('js/Pages/Organization.jsx'));
}

it('returns HTTP 200 and the Organization component', function () {
    $this->get('/struktur-organisasi')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Organization')
            ->has('categories')
        );
});

it('passes the organization chart image URL from site settings', function () {
    SiteSettings::create(['org_chart_image_url' => 'settings/bagan-organisasi.png']);

    $this->get('/struktur-organisasi')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Organization')
            ->where('settings.org_chart_image_url', 'settings/bagan-organisasi.png')
        );
});

it('handles a missing site settings record and null chart image safely', function () {
    $response = $this->get('/struktur-organisasi');

    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Organization')
            ->has('categories', 0)
        );

    $props = $response->inertiaProps();

    expect(array_key_exists('settings', $props))->toBeTrue()
        ->and($props['settings'])->toBeNull();
});

it('returns organization categories with nested people in their configured order', function () {
    $penasehat = OrgCategory::create([
        'name' => 'Penasehat',
        'description' => 'Pemberi masukan strategis',
        'display_type' => 'ROLE_LIST',
        'order' => 2,
    ]);

    $pelindung = OrgCategory::create([
        'name' => 'Pelindung',
        'description' => 'Pendukung kelembagaan',
        'display_type' => 'ROLE_LIST',
        'order' => 1,
    ]);

    OrgPerson::create([
        'org_category_id' => $pelindung->id,
        'name' => 'Tokoh Kedua',
        'role_title' => 'Wakil Pelindung',
        'order' => 2,
    ]);

    OrgPerson::create([
        'org_category_id' => $pelindung->id,
        'name' => 'Tokoh Pertama',
        'role_title' => 'Pelindung Utama',
        'order' => 1,
    ]);

    OrgPerson::create([
        'org_category_id' => $penasehat->id,
        'name' => 'Penasehat Satu',
        'role_title' => 'Anggota Penasehat',
        'order' => 1,
    ]);

    $response = $this->get('/struktur-organisasi');

    $categories = collect($response->inertiaProps()['categories']);

    expect($categories->pluck('name')->all())->toBe(['Pelindung', 'Penasehat'])
        ->and($categories->first()['people'])->toHaveCount(2)
        ->and(collect($categories->first()['people'])->pluck('name')->all())
        ->toBe(['Tokoh Pertama', 'Tokoh Kedua'])
        ->and(collect($categories->first()['people'])->first()['role_title'])->toBe('Pelindung Utama');
});

it('reflects category changes made through the admin resource on the public page', function () {
    Livewire::test(CreateOrgCategory::class)
        ->fillForm([
            'name' => 'Dewan Penasihat Baru',
            'description' => 'Kategori hasil uji admin',
            'display_type' => 'PERSON_GRID',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $category = OrgCategory::where('name', 'Dewan Penasihat Baru')->firstOrFail();

    $publicCategory = collect($this->get('/struktur-organisasi')->inertiaProps()['categories'])
        ->firstWhere('id', $category->id);

    expect($publicCategory)->not->toBeNull()
        ->and($publicCategory['display_type'])->toBe('PERSON_GRID');

    Livewire::test(EditOrgCategory::class, ['record' => $category->getRouteKey()])
        ->fillForm([
            'name' => 'Dewan Penasihat Diperbarui',
            'description' => 'Kategori hasil uji admin',
            'display_type' => 'PERSON_GRID',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $publicNames = collect($this->get('/struktur-organisasi')->inertiaProps()['categories'])
        ->pluck('name');

    expect($publicNames)->toContain('Dewan Penasihat Diperbarui')
        ->and($publicNames)->not->toContain('Dewan Penasihat Baru');
});

it('renders the uploaded chart image source instead of a hardcoded organization tree', function () {
    $source = organizationPageSource();

    expect($source)->toContain('settings?.org_chart_image_url')
        ->and($source)->toContain('src={`/storage/${chartImageUrl}`}')
        ->and($source)->toContain('object-contain')
        ->and($source)->toContain('loading="lazy"');

    $removedHardcodedNodes = [
        'PELINDUNG',
        'Badan Penelitian dan Pengembangan Organisasi',
        'Dewan Eksekutif Organisasi',
        'Direktur Utama TMCC',
        'Direktur Operasional',
        'Direktur Eksternal Relation',
        'Direktur Keuangan',
        'Divisi Legal dan Advokasi',
        'ANGGOTA DAN SUKARELAWAN',
    ];

    foreach ($removedHardcodedNodes as $node) {
        expect($source)->not->toContain($node);
    }
});

it('includes an accessible, non-dependency lightbox for the chart image', function () {
    $source = organizationPageSource();

    expect($source)->toContain('role="dialog"')
        ->and($source)->toContain('aria-modal="true"')
        ->and($source)->toContain('aria-label="Tutup bagan organisasi"')
        ->and($source)->toContain("event.key === 'Escape'")
        ->and($source)->toContain("document.body.style.overflow = 'hidden'")
        ->and($source)->toContain('max-h-[90vh]');
});

it('keeps the dynamic accordion while showing a neutral message when no chart is uploaded', function () {
    SiteSettings::create(['org_chart_image_url' => null]);

    OrgCategory::create([
        'name' => 'Kategori Dinamis',
        'display_type' => 'ROLE_LIST',
        'order' => 1,
    ]);

    $this->get('/struktur-organisasi')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Organization')
            ->where('settings.org_chart_image_url', null)
            ->has('categories', 1)
            ->where('categories.0.name', 'Kategori Dinamis')
        );

    $source = organizationPageSource();

    expect($source)->toContain('Bagan organisasi belum tersedia')
        ->and($source)->toContain('belum diunggah melalui panel admin');
});
