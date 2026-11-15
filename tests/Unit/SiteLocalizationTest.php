<?php

beforeEach(function () {
    $this->projectRoot = dirname(__DIR__, 2);
});

it('keeps indonesian and english site translation keys in sync', function () {
    $id = require $this->projectRoot.'/lang/id/site.php';
    $en = require $this->projectRoot.'/lang/en/site.php';

    expect(array_keys($en))->toEqual(array_keys($id));
});

it('provides every translation key used by the about page in both languages', function () {
    $source = file_get_contents($this->projectRoot.'/resources/js/Pages/About.jsx');

    preg_match_all('/\bt\.([A-Za-z0-9_]+)/', $source, $matches);

    $keys = array_values(array_unique($matches[1]));

    expect($keys)->not->toBeEmpty();

    $id = require $this->projectRoot.'/lang/id/site.php';
    $en = require $this->projectRoot.'/lang/en/site.php';

    foreach ($keys as $key) {
        expect($id)->toHaveKey($key)
            ->and($en)->toHaveKey($key);
    }
});

it('removes hardcoded legal placeholders from the about page', function () {
    $source = file_get_contents($this->projectRoot.'/resources/js/Pages/About.jsx');

    expect($source)
        ->not->toContain('AHU-0012345')
        ->not->toContain('12.345.678.9')
        ->not->toContain('123/Kesbangpol')
        ->not->toContain('Nomor belum tersedia');
});

it('reads all nine about sections from inertia props', function () {
    $source = file_get_contents($this->projectRoot.'/resources/js/Pages/About.jsx');

    foreach (['hero', 'lake', 'profile', 'potential', 'legal', 'vision', 'mission', 'goal', 'founders'] as $section) {
        expect($source)->toContain("aboutContent?.{$section}");
    }
});
