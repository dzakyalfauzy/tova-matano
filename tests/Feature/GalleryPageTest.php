<?php

use App\Models\GalleryItem;
use Inertia\Testing\AssertableInertia as Assert;

function galleryPageSource(): string
{
    return file_get_contents(resource_path('js/Pages/Gallery.jsx'));
}

function galleryItemForTest(string $type, string $url, ?string $caption = null): GalleryItem
{
    return GalleryItem::create([
        'type' => $type,
        'url' => $url,
        'caption' => $caption,
    ]);
}

it('returns the gallery page with paginated items and captions', function () {
    galleryItemForTest('PHOTO', 'gallery/first.jpg', 'Kegiatan pertama');
    galleryItemForTest('PHOTO', 'gallery/second.jpg', 'Kegiatan kedua');

    $response = $this->get('/galeri');

    $response->assertOk()->assertInertia(fn (Assert $page) => $page
        ->component('Gallery')
        ->has('items.data', 2)
        ->where('items.total', 2)
        ->where('items.per_page', 12)
    );

    $captions = collect($response->inertiaProps()['items']['data'])->pluck('caption');

    expect($captions)->toContain('Kegiatan pertama')
        ->and($captions)->toContain('Kegiatan kedua');
});

it('paginates gallery items twelve per page', function () {
    foreach (range(1, 13) as $index) {
        galleryItemForTest('PHOTO', "gallery/{$index}.jpg", "Kegiatan {$index}");
    }

    $firstPage = $this->get('/galeri');
    $firstPageProps = $firstPage->inertiaProps();

    expect($firstPageProps['items']['data'])->toHaveCount(12)
        ->and($firstPageProps['items']['total'])->toBe(13)
        ->and($firstPageProps['items']['current_page'])->toBe(1)
        ->and($firstPageProps['items']['last_page'])->toBe(2);

    $secondPage = $this->get('/galeri?page=2');
    $secondPageProps = $secondPage->inertiaProps();

    $secondPage->assertOk()->assertInertia(fn (Assert $page) => $page
        ->component('Gallery')
        ->has('items.data', 1)
        ->where('items.current_page', 2)
    );

    expect($secondPageProps['items']['data'])->toHaveCount(1);
});

it('renders the gallery empty state when no items exist', function () {
    $response = $this->get('/galeri');

    $response->assertOk()->assertInertia(fn (Assert $page) => $page
        ->component('Gallery')
        ->has('items.data', 0)
        ->where('items.total', 0)
    );

    expect(galleryPageSource())->toContain('Belum ada item galeri.');
});

it('passes photo and video item data through the page props', function () {
    galleryItemForTest('PHOTO', 'gallery/photo.jpg', 'Foto dokumentasi');
    galleryItemForTest('VIDEO', 'https://example.com/video', 'Video dokumentasi');

    $response = $this->get('/galeri');
    $items = collect($response->inertiaProps()['items']['data'])->keyBy('type');

    $response->assertOk()->assertInertia(fn (Assert $page) => $page
        ->component('Gallery')
        ->has('items.data', 2)
    );

    expect($items['PHOTO']['url'])->toBe('gallery/photo.jpg')
        ->and($items['PHOTO']['caption'])->toBe('Foto dokumentasi')
        ->and($items['VIDEO']['url'])->toBe('https://example.com/video')
        ->and($items['VIDEO']['caption'])->toBe('Video dokumentasi');
});

it('keeps captions visible and lazy-loads gallery thumbnails', function () {
    $source = galleryPageSource();

    expect($source)->toContain('<figcaption')
        ->and($source)->toContain('loading="lazy"')
        ->and($source)->not->toContain('group-hover:opacity-100');
});

it('imports router for gallery pagination', function () {
    $source = galleryPageSource();

    expect($source)->toContain("import { Head, router } from '@inertiajs/react';")
        ->and($source)->toContain('router.visit(link.url)');
});
