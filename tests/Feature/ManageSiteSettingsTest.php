<?php

use App\Filament\Pages\ManageSiteSettings;
use App\Models\SiteSettings;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
    Filament::setCurrentPanel('admin');
});

it('persists localized about content and the lake image credit from the admin form', function () {
    Livewire::test(ManageSiteSettings::class)
        ->fillForm([
            'about_lake_image_credit' => 'Dokumentasi TMCC',
            'about_content.hero.eyebrow.id' => 'Tentang Kami',
            'about_content.hero.eyebrow.en' => 'About Us',
            'about_content.hero.title.id' => 'Judul Indonesia',
            'about_content.hero.title.en' => 'English Title',
            'about_content.hero.subtitle.id' => 'Subjudul Indonesia',
            'about_content.hero.subtitle.en' => 'English Subtitle',
            'about_content.lake.title.id' => 'Sekilas Danau Matano',
            'about_content.lake.body.id' => "Paragraf pertama.\n\nParagraf kedua.",
            'about_content.profile.title.id' => 'Profil & Sejarah',
            'about_content.potential.title.id' => 'Potensi TMCC',
            'about_content.legal.title.id' => 'Legalitas',
            'about_content.legal.items.id' => "SK Kemenkumham\nAkta Notaris",
            'about_content.vision.id' => 'Visi Indonesia',
            'about_content.mission.id' => "Misi pertama\nMisi kedua",
            'about_content.goal.id' => "Tujuan pertama\nTujuan kedua",
            'about_content.founders.title.id' => 'DEWAN PENDIRI',
            'about_content.founders.narrative.id' => 'Narasi pendiri.',
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified();

    $settings = SiteSettings::query()->firstOrFail();

    expect($settings->about_lake_image_credit)->toBe('Dokumentasi TMCC');
    expect($settings->about_content['hero']['eyebrow'])->toEqual(['id' => 'Tentang Kami', 'en' => 'About Us']);
    expect($settings->about_content['hero']['title'])->toEqual(['id' => 'Judul Indonesia', 'en' => 'English Title']);
    expect($settings->about_content['lake']['body']['id'])->toBe("Paragraf pertama.\n\nParagraf kedua.");
    expect($settings->about_content['legal']['items']['id'])->toBe("SK Kemenkumham\nAkta Notaris");
    expect($settings->about_content['vision']['id'])->toBe('Visi Indonesia');
    expect($settings->about_content['mission']['id'])->toBe("Misi pertama\nMisi kedua");
    expect($settings->about_content['founders']['title'])->toEqual(['id' => 'DEWAN PENDIRI', 'en' => '']);
    expect($settings->about_legal_verified)->toBeFalse();
});

it('does not mark legal content as verified when only legal items are filled', function () {
    Livewire::test(ManageSiteSettings::class)
        ->fillForm([
            'about_content.legal.items.id' => "SK Kemenkumham\nAkta Notaris",
            'about_content.legal.items.en' => "Decree\nDeed",
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(SiteSettings::query()->firstOrFail()->about_legal_verified)->toBeFalse();
});

it('lets an authorized panel user mark legal verification explicitly', function () {
    Livewire::test(ManageSiteSettings::class)
        ->fillForm([
            'about_legal_verified' => true,
            'about_content.legal.items.id' => 'SK Kemenkumham',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(SiteSettings::query()->firstOrFail()->about_legal_verified)->toBeTrue();
});

it('preserves unrelated settings while saving about content', function () {
    SiteSettings::query()->create([
        'phone' => '081100000000',
        'fixed_text' => ['id' => 'Halo', 'en' => 'Hello'],
        'about_content' => [
            'hero' => [
                'title' => ['id' => 'Judul Lama', 'en' => 'Old Title'],
            ],
        ],
    ]);

    Livewire::test(ManageSiteSettings::class)
        ->fillForm([
            'about_content.hero.title.id' => 'Judul Baru',
            'about_content.lake.title.id' => 'Danau Baru',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $settings = SiteSettings::query()->firstOrFail();

    expect($settings->phone)->toBe('081100000000');
    expect($settings->fixed_text)->toEqual(['id' => 'Halo', 'en' => 'Hello']);
    expect($settings->about_content['hero']['title'])->toEqual(['id' => 'Judul Baru', 'en' => 'Old Title']);
    expect($settings->about_content['lake']['title']['id'])->toBe('Danau Baru');
});

it('only allows users with admin panel access to manage legal verification', function () {
    expect(ManageSiteSettings::canManageLegalVerification())->toBeTrue();

    auth()->logout();

    expect(ManageSiteSettings::canManageLegalVerification())->toBeFalse();
});

it('does not allow guests to open the site settings page', function () {
    auth()->logout();

    $this->get(ManageSiteSettings::getUrl())
        ->assertRedirect(Filament::getLoginUrl());
});
