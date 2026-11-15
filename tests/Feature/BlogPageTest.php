<?php

use App\Filament\Pages\ManageSiteSettings;
use App\Filament\Resources\BlogPosts\Pages\CreateBlogPost;
use App\Filament\Resources\BlogPosts\Pages\ListBlogPosts;
use App\Models\BlogPost;
use App\Models\SiteSettings;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
    Filament::setCurrentPanel('admin');
});

function blogPageSource(): string
{
    return file_get_contents(resource_path('js/Pages/Blog.jsx'));
}

function blogDetailSource(): string
{
    return file_get_contents(resource_path('js/Pages/BlogDetail.jsx'));
}

it('returns HTTP 200 and the Blog component on /blog', function () {
    $this->get('/blog')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Blog')
            ->has('posts')
            ->has('filters')
            ->has('baselineStudyPosts')
        );
});

it('renders the updated blog subtitle in Indonesian and English', function () {
    app()->setLocale('id');
    expect(__('site.blog_subtitle'))->toBe('Berita dan Cerita');

    app()->setLocale('en');
    expect(__('site.blog_subtitle'))->toBe('News and Stories');

    // Title remains Tova Blog
    expect(__('site.blog_title'))->toBe('Tova Blog');
});

it('passes the blog hero background image from site settings to the page', function () {
    SiteSettings::create(['blog_hero_image_url' => 'settings/blog/hero.jpg']);

    $this->get('/blog')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Blog')
            ->where('settings.blog_hero_image_url', 'settings/blog/hero.jpg')
        );
});

it('handles missing site settings safely on the public blog page', function () {
    $response = $this->get('/blog');

    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Blog')
        );

    $props = $response->inertiaProps();
    expect(array_key_exists('settings', $props))->toBeTrue()
        ->and($props['settings'])->toBeNull();
});

it('renders the blog index safely when no background image is set', function () {
    SiteSettings::create(['blog_hero_image_url' => null]);

    $this->get('/blog')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Blog')
            ->where('settings.blog_hero_image_url', null)
        );

    $source = blogPageSource();
    expect($source)->toContain('settings?.blog_hero_image_url')
        ->and($source)->toContain('/storage/${settings.blog_hero_image_url}')
        ->and($source)->toContain('bg-brand-navy');
});

it('displays published CERITA articles in the standard blog feed', function () {
    BlogPost::create([
        'title' => 'Cerita dari Pesisir Matano',
        'slug' => 'cerita-dari-pesisir-matano',
        'category' => 'CERITA',
        'content' => '<p>Kisah masyarakat pesisir Danau Matano.</p>',
        'is_published' => true,
        'published_at' => now(),
    ]);

    $response = $this->get('/blog');
    $response->assertOk();

    $posts = collect($response->inertiaProps()['posts']['data']);
    expect($posts->pluck('slug'))->toContain('cerita-dari-pesisir-matano')
        ->and($posts->firstWhere('slug', 'cerita-dari-pesisir-matano')['category'])->toBe('CERITA');
});

it('filters articles by the CERITA category', function () {
    BlogPost::create([
        'title' => 'Cerita Satu',
        'slug' => 'cerita-satu',
        'category' => 'CERITA',
        'content' => '<p>Konten</p>',
        'is_published' => true,
        'published_at' => now(),
    ]);

    BlogPost::create([
        'title' => 'Berita Satu',
        'slug' => 'berita-satu',
        'category' => 'BERITA',
        'content' => '<p>Konten</p>',
        'is_published' => true,
        'published_at' => now(),
    ]);

    $response = $this->get('/blog?category=CERITA');
    $response->assertOk();

    $posts = collect($response->inertiaProps()['posts']['data']);
    expect($posts->pluck('slug'))->toContain('cerita-satu')
        ->and($posts->pluck('slug'))->not->toContain('berita-satu')
        ->and($response->inertiaProps()['filters']['category'])->toBe('CERITA');
});

it('preserves existing category filters on the blog index', function () {
    BlogPost::create([
        'title' => 'Edukasi Lingkungan',
        'slug' => 'edukasi-lingkungan',
        'category' => 'EDUKASI',
        'content' => '<p>Materi</p>',
        'is_published' => true,
        'published_at' => now(),
    ]);

    BlogPost::create([
        'title' => 'Rilis Pers TMCC',
        'slug' => 'rilis-pers-tmcc',
        'category' => 'RILIS_PERS',
        'content' => '<p>Rilis</p>',
        'is_published' => true,
        'published_at' => now(),
    ]);

    $response = $this->get('/blog?category=EDUKASI');
    $response->assertOk();

    $posts = collect($response->inertiaProps()['posts']['data']);
    expect($posts->pluck('slug'))->toContain('edukasi-lingkungan')
        ->and($posts->pluck('slug'))->not->toContain('rilis-pers-tmcc');
});

it('excludes BASELINE_STUDY posts from the standard feed into their own section', function () {
    BlogPost::create([
        'title' => 'Dokumen Baseline Riset',
        'slug' => 'dokumen-baseline-riset',
        'category' => 'BASELINE_STUDY',
        'content' => '<p>Data</p>',
        'is_published' => true,
        'published_at' => now(),
    ]);

    $response = $this->get('/blog');
    $response->assertOk();

    $standardPosts = collect($response->inertiaProps()['posts']['data']);
    $baselinePosts = collect($response->inertiaProps()['baselineStudyPosts']);

    expect($standardPosts->pluck('slug'))->not->toContain('dokumen-baseline-riset')
        ->and($baselinePosts->pluck('slug'))->toContain('dokumen-baseline-riset');
});

it('preserves pagination and query string parameters on category filter', function () {
    for ($i = 1; $i <= 11; $i++) {
        BlogPost::create([
            'title' => "Cerita {$i}",
            'slug' => "cerita-{$i}",
            'category' => 'CERITA',
            'content' => '<p>Isi cerita</p>',
            'is_published' => true,
            'published_at' => now()->subDays($i),
        ]);
    }

    $response = $this->get('/blog?category=CERITA&page=2');
    $response->assertOk();

    $posts = $response->inertiaProps()['posts'];
    expect($posts['current_page'])->toBe(2)
        ->and($posts['total'])->toBe(11)
        ->and($posts['per_page'])->toBe(9)
        ->and(count($posts['data']))->toBe(2);
});

it('opens a published CERITA article on /blog/{slug}', function () {
    BlogPost::create([
        'title' => 'Cerita Danau Matano',
        'slug' => 'cerita-danau-matano',
        'category' => 'CERITA',
        'content' => '<p>Cerita lengkap tentang keindahan danau.</p>',
        'is_published' => true,
        'published_at' => now(),
    ]);

    $this->get('/blog/cerita-danau-matano')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('BlogDetail')
            ->where('post.slug', 'cerita-danau-matano')
            ->where('post.category', 'CERITA')
        );
});

it('verifies category label and color configuration in BlogDetail and Blog', function () {
    $blogSource = blogPageSource();
    $detailSource = blogDetailSource();

    expect($blogSource)->toContain("{ value: 'CERITA', label: 'Cerita' }")
        ->and($blogSource)->toContain("CERITA: 'bg-amber-100 text-amber-800'")
        ->and($detailSource)->toContain("CERITA: 'Cerita'")
        ->and($detailSource)->toContain("CERITA: 'bg-amber-100 text-amber-800'");
});

it('returns HTTP 404 for draft articles on /blog/{slug}', function () {
    BlogPost::create([
        'title' => 'Artikel Masih Draf',
        'slug' => 'artikel-masih-draf',
        'category' => 'CERITA',
        'content' => '<p>Belum terbit</p>',
        'is_published' => false,
        'published_at' => now(),
    ]);

    $this->get('/blog/artikel-masih-draf')->assertNotFound();
});

it('allows selecting CERITA in the admin BlogPostForm', function () {
    Livewire::test(CreateBlogPost::class)
        ->fillForm([
            'title' => 'Cerita Baru dari Admin',
            'slug' => 'cerita-baru-dari-admin',
            'category' => 'CERITA',
            'content' => '<p>Ditulis oleh admin</p>',
            'is_published' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $post = BlogPost::where('slug', 'cerita-baru-dari-admin')->firstOrFail();
    expect($post->category)->toBe('CERITA');
});

it('includes CERITA in the admin BlogPostsTable category filter', function () {
    BlogPost::create([
        'title' => 'Cerita Tabel',
        'slug' => 'cerita-tabel',
        'category' => 'CERITA',
        'content' => '<p>Konten</p>',
        'is_published' => true,
        'published_at' => now(),
    ]);

    Livewire::test(ListBlogPosts::class)
        ->filterTable('category', 'CERITA')
        ->assertCanSeeTableRecords([BlogPost::where('slug', 'cerita-tabel')->first()]);
});

it('confirms the database schema includes nullable blog_hero_image_url', function () {
    expect(Schema::hasColumn('site_settings', 'blog_hero_image_url'))->toBeTrue();
});

it('saves an uploaded blog hero background image in ManageSiteSettings', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('blog-hero.jpg', 1600, 600);

    Livewire::test(ManageSiteSettings::class)
        ->fillForm([
            'blog_hero_image_url' => $file,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $settings = SiteSettings::query()->firstOrFail();
    expect($settings->blog_hero_image_url)->toStartWith('settings/blog/');
    Storage::disk('public')->assertExists($settings->blog_hero_image_url);
});

it('rejects unsupported file types for the blog hero image', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->create('unsupported.pdf', 200, 'application/pdf');

    Livewire::test(ManageSiteSettings::class)
        ->fillForm([
            'blog_hero_image_url' => $file,
        ])
        ->call('save')
        ->assertHasFormErrors(['blog_hero_image_url']);
});

it('rejects a blog hero image exceeding 5 MB', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('too-large.png')->size(6000);

    Livewire::test(ManageSiteSettings::class)
        ->fillForm([
            'blog_hero_image_url' => $file,
        ])
        ->call('save')
        ->assertHasFormErrors(['blog_hero_image_url']);
});

it('allows removing the blog hero image without breaking other settings', function () {
    Storage::fake('public');

    $settings = SiteSettings::create([
        'phone' => '081234567890',
        'blog_hero_image_url' => 'settings/blog/existing.jpg',
    ]);

    Livewire::test(ManageSiteSettings::class)
        ->fillForm([
            'blog_hero_image_url' => null,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $settings->refresh();
    expect($settings->blog_hero_image_url)->toBeNull()
        ->and($settings->phone)->toBe('081234567890');
});
