<?php

use App\Models\SiteSettings;

test('getAboutContent uses the current application locale when none is provided', function () {
    app()->setLocale('en');

    $settings = new SiteSettings([
        'about_content' => [
            'vision' => ['id' => 'Visi ID', 'en' => 'Vision EN'],
        ],
    ]);

    expect($settings->getAboutContent()['vision'])->toBe('Vision EN');

    app()->setLocale('id');

    expect((new SiteSettings)->getAboutContent()['hero']['title'])->toBe('ADVOKASI LINGKUNGAN LOKAL');
});
