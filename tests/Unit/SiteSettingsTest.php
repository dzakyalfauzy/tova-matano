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

test('it returns approved default about content when about_content is null', function () {
    $settings = new SiteSettings(['about_content' => null]);
    $content = $settings->getAboutContent('id');

    expect($content['hero']['eyebrow'])->toBe('Tentang Kami');
    expect($content['hero']['title'])->toBe('ADVOKASI LINGKUNGAN LOKAL');
    expect($content['hero']['subtitle'])->toBe('Membangun masa depan yang berkelanjutan untuk Danau Matano');
    expect($content['lake']['title'])->toBe('Sekilas Danau Matano');
    expect($content['profile']['title'])->toBe('Profil & Sejarah');
    expect($content['potential']['title'])->toBe('Potensi TMCC');
    expect($content['vision'])->toContain('Terwujudnya kelestarian Danau Matano');
    expect(explode("\n", $content['mission']))->toHaveCount(4);
    expect(explode("\n", $content['goal']))->toHaveCount(4);
    expect($content['founders']['title'])->toBe('DEWAN PENDIRI');
});

test('it resolves nested about content for the indonesian locale', function () {
    $settings = new SiteSettings([
        'about_content' => [
            'hero' => [
                'eyebrow' => ['id' => 'Tentang', 'en' => 'About'],
                'title' => ['id' => 'Judul ID', 'en' => 'Title EN'],
                'subtitle' => ['id' => 'Sub ID', 'en' => 'Sub EN'],
            ],
            'lake' => [
                'title' => ['id' => 'Danau', 'en' => 'Lake'],
                'body' => ['id' => 'Tubuh', 'en' => 'Body'],
            ],
        ],
    ]);

    $content = $settings->getAboutContent('id');

    expect($content['hero']['eyebrow'])->toBe('Tentang');
    expect($content['hero']['title'])->toBe('Judul ID');
    expect($content['lake']['title'])->toBe('Danau');
    expect($content['profile']['title'])->toBe('Profil & Sejarah');
});

test('it resolves nested about content for the english locale', function () {
    $settings = new SiteSettings([
        'about_content' => [
            'hero' => [
                'title' => ['id' => 'Judul ID', 'en' => 'Title EN'],
            ],
        ],
    ]);

    expect($settings->getAboutContent('en')['hero']['title'])->toBe('Title EN');
});

test('it falls back to indonesian when english about content is empty', function () {
    $settings = new SiteSettings([
        'about_content' => [
            'hero' => [
                'title' => ['id' => 'Judul ID', 'en' => ''],
            ],
        ],
    ]);

    expect($settings->getAboutContent('en')['hero']['title'])->toBe('Judul ID');
});

test('it handles incomplete nested about structures safely', function () {
    $settings = new SiteSettings([
        'about_content' => [
            'hero' => ['title' => ['id' => 'Judul Khusus']],
            'lake' => 'not-an-array',
        ],
    ]);

    $content = $settings->getAboutContent('id');

    expect($content['hero']['title'])->toBe('Judul Khusus');
    expect($content['hero']['eyebrow'])->toBe('Tentang Kami');
    expect($content['lake']['title'])->toBe('Sekilas Danau Matano');
});

test('it does not fabricate legal data in defaults', function () {
    $content = (new SiteSettings(['about_content' => null]))->getAboutContent('id');

    expect($content['legal']['items'])->toBe('');
    expect($content['legal']['verified'])->toBeFalse();
    expect(str_contains($content['legal']['items'], 'AHU'))->toBeFalse();
    expect(str_contains($content['legal']['items'], 'NPWP'))->toBeFalse();
});

test('it only marks legal verified when the stored boolean is true', function () {
    expect((new SiteSettings(['about_legal_verified' => true]))->getAboutContent('id')['legal']['verified'])->toBeTrue();
    expect((new SiteSettings(['about_legal_verified' => false]))->getAboutContent('id')['legal']['verified'])->toBeFalse();
    expect((new SiteSettings)->getAboutContent('id')['legal']['verified'])->toBeFalse();
});

test('it casts about_legal_verified to boolean', function () {
    expect((new SiteSettings(['about_legal_verified' => 1]))->about_legal_verified)->toBeTrue();
    expect((new SiteSettings(['about_legal_verified' => '1']))->about_legal_verified)->toBeTrue();
    expect((new SiteSettings(['about_legal_verified' => 0]))->about_legal_verified)->toBeFalse();
    expect((new SiteSettings(['about_legal_verified' => '0']))->about_legal_verified)->toBeFalse();
});

test('reading about content does not mutate the stored value', function () {
    $original = ['hero' => ['title' => ['id' => 'Judul', 'en' => 'Title']]];
    $settings = new SiteSettings(['about_content' => $original]);

    $settings->getAboutContent('en');

    expect($settings->about_content)->toBe($original);
});
