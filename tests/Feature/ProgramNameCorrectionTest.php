<?php

use App\Models\Activity;
use App\Models\Program;
use App\Models\SiteSettings;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->actingAs(User::factory()->create());

    SiteSettings::create([]);

    $this->programs = collect([
        Program::create(['slug' => 'tova-water-patrol', 'name' => 'Tova Water Patrol', 'description' => 'Desc 1', 'order' => 1]),
        Program::create(['slug' => 'eco-education', 'name' => 'Eco Education', 'description' => 'Desc 2', 'order' => 2]),
        Program::create(['slug' => 'clean-lake-action', 'name' => 'Clean Lake Action', 'description' => 'Desc 3', 'order' => 3]),
        Program::create(['slug' => 'biodiversity-watch', 'name' => 'Tova Campaign & Event', 'description' => 'Desc 4', 'order' => 4]),
    ]);

    $this->activity = Activity::create([
        'program_id' => $this->programs[3]->id,
        'title' => 'Survei Populasi Ikan Butini',
        'slug' => 'survei-populasi-ikan-butini-2026',
        'cover_image_url' => '/image/logo_tova_matano.png',
        'content' => '<p>Content</p>',
        'published_at' => now()->subDay(),
        'is_published' => true,
    ]);
});

it('displays the fourth program as Tova Campaign & Event on the programs page', function () {
    $this->get('/program')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Programs')
            ->has('programs', 4)
            ->where('programs.3.name', 'Tova Campaign & Event')
            ->where('programs.3.slug', 'biodiversity-watch')
        );
});

it('preserves all three other program names unchanged', function () {
    $this->get('/program')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('programs.0.name', 'Tova Water Patrol')
            ->where('programs.1.name', 'Eco Education')
            ->where('programs.2.name', 'Clean Lake Action')
        );
});

it('resolves the program category page using the existing slug', function () {
    $this->get('/program/biodiversity-watch')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('ProgramCategory')
            ->where('program.name', 'Tova Campaign & Event')
            ->where('program.slug', 'biodiversity-watch')
        );
});

it('resolves the activity detail page under the existing program slug', function () {
    $this->get('/program/biodiversity-watch/survei-populasi-ikan-butini-2026')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('ProgramDetail')
            ->where('program.name', 'Tova Campaign & Event')
            ->where('program.slug', 'biodiversity-watch')
            ->where('activity.slug', 'survei-populasi-ikan-butini-2026')
            ->where('activity.title', 'Survei Populasi Ikan Butini')
        );
});

it('keeps the program ID and slug unchanged after the name update', function () {
    $program = Program::where('slug', 'biodiversity-watch')->firstOrFail();

    expect($program->name)->toBe('Tova Campaign & Event')
        ->and($program->slug)->toBe('biodiversity-watch')
        ->and($program->id)->toBe($this->programs[3]->id);
});

it('maintains the activity relationship with the renamed program', function () {
    $program = Program::where('slug', 'biodiversity-watch')->firstOrFail();
    $activities = $program->activities;

    expect($activities)->toHaveCount(1)
        ->and($activities->first()->slug)->toBe('survei-populasi-ikan-butini-2026');
});

it('uses the correct program name in the seeder for fresh environments', function () {
    $seederPath = base_path('database/seeders/DatabaseSeeder.php');
    $seederContent = file_get_contents($seederPath);

    expect($seederContent)->toContain("'name' => 'Tova Campaign & Event'")
        ->and($seederContent)->toContain("'slug' => 'biodiversity-watch'")
        ->and($seederContent)->not->toContain("'name' => 'Biodiversity Watch'");
});
