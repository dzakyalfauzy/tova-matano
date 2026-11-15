<?php

use App\Models\Founder;
use App\Models\SiteSettings;
use Inertia\Testing\AssertableInertia as Assert;

it('shares the resolved about content on the public about page', function () {
    config(['app.locale' => 'id']);

    SiteSettings::query()->create([
        'about_content' => [
            'hero' => [
                'eyebrow' => ['id' => 'Tentang Kami', 'en' => ''],
                'title' => ['id' => 'Judul Hero', 'en' => ''],
                'subtitle' => ['id' => 'Subjudul Hero', 'en' => ''],
            ],
            'lake' => [
                'title' => ['id' => 'Sekilas Danau', 'en' => ''],
                'body' => ['id' => "Paragraf satu.\n\nParagraf dua.", 'en' => ''],
            ],
            'profile' => [
                'title' => ['id' => 'Profil', 'en' => ''],
                'body' => ['id' => 'Isi profil.', 'en' => ''],
            ],
            'potential' => [
                'title' => ['id' => 'Potensi', 'en' => ''],
                'body' => ['id' => 'Isi potensi.', 'en' => ''],
            ],
            'legal' => [
                'title' => ['id' => 'Legalitas', 'en' => ''],
                'items' => ['id' => 'SK Terverifikasi', 'en' => ''],
            ],
            'vision' => ['id' => 'Visi ID', 'en' => ''],
            'mission' => ['id' => "Misi satu\nMisi dua", 'en' => ''],
            'goal' => ['id' => "Tujuan satu\nTujuan dua", 'en' => ''],
            'founders' => [
                'title' => ['id' => 'Dewan Pendiri', 'en' => ''],
                'narrative' => ['id' => 'Narasi pendiri.', 'en' => ''],
            ],
        ],
    ]);

    $this->get(route('about'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('About')
            ->where('aboutContent.hero.eyebrow', 'Tentang Kami')
            ->where('aboutContent.hero.title', 'Judul Hero')
            ->where('aboutContent.hero.subtitle', 'Subjudul Hero')
            ->where('aboutContent.lake.title', 'Sekilas Danau')
            ->where('aboutContent.lake.body', "Paragraf satu.\n\nParagraf dua.")
            ->where('aboutContent.profile.title', 'Profil')
            ->where('aboutContent.profile.body', 'Isi profil.')
            ->where('aboutContent.potential.title', 'Potensi')
            ->where('aboutContent.potential.body', 'Isi potensi.')
            ->where('aboutContent.legal.title', 'Legalitas')
            ->where('aboutContent.vision', 'Visi ID')
            ->where('aboutContent.mission', "Misi satu\nMisi dua")
            ->where('aboutContent.goal', "Tujuan satu\nTujuan dua")
            ->where('aboutContent.founders.title', 'Dewan Pendiri')
            ->where('aboutContent.founders.narrative', 'Narasi pendiri.')
        );
});

it('withholds legal items and keeps them out of the response when not verified', function () {
    config(['app.locale' => 'id']);

    SiteSettings::query()->create([
        'about_content' => [
            'legal' => [
                'items' => ['id' => 'AHU-SECRET-001', 'en' => ''],
            ],
        ],
        'about_legal_verified' => false,
    ]);

    $response = $this->get(route('about'))->assertOk();

    $response->assertInertia(fn (Assert $page) => $page
        ->where('aboutContent.legal.items', '')
        ->where('aboutContent.legal.verified', false)
    );

    expect($response->getContent())->not->toContain('AHU-SECRET-001');
});

it('withholds legal items when verified but the items are empty', function () {
    config(['app.locale' => 'id']);

    SiteSettings::query()->create([
        'about_content' => [
            'legal' => [
                'items' => ['id' => "   \n\n", 'en' => ''],
            ],
        ],
        'about_legal_verified' => true,
    ]);

    $this->get(route('about'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('aboutContent.legal.items', '')
            ->where('aboutContent.legal.verified', false)
        );
});

it('shares legal items when verification is true and the items are valid', function () {
    config(['app.locale' => 'id']);

    SiteSettings::query()->create([
        'about_content' => [
            'legal' => [
                'items' => ['id' => "SK AHU-01482.AH.02.01.TAHUN 2025\nAkta Notaris Nomor 07", 'en' => ''],
            ],
        ],
        'about_legal_verified' => true,
    ]);

    $this->get(route('about'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('aboutContent.legal.items', "SK AHU-01482.AH.02.01.TAHUN 2025\nAkta Notaris Nomor 07")
            ->where('aboutContent.legal.verified', true)
        );
});

it('uses english content when it is available', function () {
    config(['app.locale' => 'en']);

    SiteSettings::query()->create([
        'about_content' => [
            'hero' => [
                'title' => ['id' => 'Judul Indonesia', 'en' => 'English Title'],
            ],
        ],
    ]);

    $this->get(route('about'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('aboutContent.hero.title', 'English Title')
        );
});

it('falls back to indonesian content for the english locale when translations are empty', function () {
    config(['app.locale' => 'en']);

    SiteSettings::query()->create([
        'about_content' => [
            'hero' => [
                'title' => ['id' => 'Judul Indonesia', 'en' => ''],
            ],
        ],
    ]);

    $this->get(route('about'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('aboutContent.hero.title', 'Judul Indonesia')
        );
});

it('shares the lake image credit as a separate prop', function () {
    config(['app.locale' => 'id']);

    SiteSettings::query()->create([
        'about_lake_image_credit' => 'Dokumentasi TMCC',
    ]);

    $this->get(route('about'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('aboutLakeImageCredit', 'Dokumentasi TMCC')
            ->where('settings.about_lake_image_credit', 'Dokumentasi TMCC')
            ->missing('aboutContent.legal.image_credit')
        );
});

it('keeps the legacy about props available', function () {
    config(['app.locale' => 'id']);

    Founder::query()->create([
        'name' => 'Pendiri Satu',
        'role' => 'Direktur Utama',
        'org_name' => 'TOVA MATANO CARE CENTER',
        'order' => 1,
    ]);

    SiteSettings::query()->create([
        'about_hero_image_url' => 'settings/hero.jpg',
        'about_lake_image_url' => 'settings/lake.jpg',
    ]);

    $this->get(route('about'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('About')
            ->has('founders', 1)
            ->where('founders.0.name', 'Pendiri Satu')
            ->where('settings.about_hero_image_url', 'settings/hero.jpg')
            ->where('settings.about_lake_image_url', 'settings/lake.jpg')
        );
});

it('does not leak the raw about_content blob to public props', function () {
    config(['app.locale' => 'id']);

    SiteSettings::query()->create([
        'about_content' => [
            'hero' => [
                'title' => ['id' => 'Judul Hero', 'en' => ''],
            ],
        ],
        'about_legal_verified' => false,
    ]);

    $response = $this->get(route('about'))->assertOk();

    $response->assertInertia(fn (Assert $page) => $page
        ->missing('about_content')
        ->missing('settings.about_content')
        ->has('aboutContent.hero.title')
    );

    expect($response->getContent())->not->toContain('about_content');
});

it('exposes the about ui translation keys in indonesian and english', function () {
    config(['app.locale' => 'id']);

    $this->get(route('about'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('About')
            ->where('translations.potential', 'Potensi TMCC')
            ->where('translations.legal', 'Legalitas')
            ->where('translations.vision_mission_goal', 'Visi, Misi & Tujuan')
            ->where('translations.founder_dedication', 'Berdedikasi sejak awal mula TMCC berdiri.')
            ->where('translations.cta_title', 'Mari Berkarya Bersama Kami')
            ->where('translations.cta_body', 'Pelestarian lingkungan bukanlah tugas satu orang atau satu kelompok. Ini adalah tanggung jawab kita bersama. Jadilah bagian dari solusi nyata.')
        );

    config(['app.locale' => 'en']);

    $this->get(route('about'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('About')
            ->where('translations.potential', 'TMCC Potential')
            ->where('translations.legal', 'Legal Status')
            ->where('translations.vision_mission_goal', 'Vision, Mission & Goal')
            ->where('translations.founder_dedication', 'Dedicated since TMCC was founded.')
            ->where('translations.cta_title', 'Let Us Work Together')
            ->where('translations.cta_body', 'Environmental preservation is not the task of one person or one group. It is our shared responsibility. Be part of the real solution.')
        );
});

it('keeps admin managed about content separate from ui translation strings', function () {
    config(['app.locale' => 'id']);

    SiteSettings::query()->create([
        'about_content' => [
            'hero' => [
                'title' => ['id' => 'Konten Admin Hero', 'en' => ''],
                'subtitle' => ['id' => 'Subjudul Admin', 'en' => ''],
            ],
        ],
    ]);

    $this->get(route('about'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('aboutContent.hero.title', 'Konten Admin Hero')
            ->where('aboutContent.hero.subtitle', 'Subjudul Admin')
            ->where('translations.about_title', 'Tentang Kami')
            ->where('translations.about_subtitle', 'Membangun masa depan yang berkelanjutan untuk Danau Matano, satu tindakan pada satu waktu.')
        );
});
