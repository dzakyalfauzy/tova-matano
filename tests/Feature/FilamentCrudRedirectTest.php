<?php

use App\Filament\Concerns\RedirectsToResourceIndex;
use App\Filament\Resources\Activities\ActivityResource;
use App\Filament\Resources\BlogPosts\BlogPostResource;
use App\Filament\Resources\DonationChannels\DonationChannelResource;
use App\Filament\Resources\Founders\FounderResource;
use App\Filament\Resources\GalleryItems\GalleryItemResource;
use App\Filament\Resources\HeroSlides\HeroSlideResource;
use App\Filament\Resources\Members\MemberResource;
use App\Filament\Resources\OrgCategories\OrgCategoryResource;
use App\Filament\Resources\OrgPeople\OrgPersonResource;
use App\Filament\Resources\Partnerships\PartnershipResource;
use App\Filament\Resources\Programs\ProgramResource;
use App\Filament\Resources\RunningTexts\Pages\CreateRunningText;
use App\Filament\Resources\RunningTexts\RunningTextResource;
use App\Filament\Resources\Volunteers\Pages\CreateVolunteer;
use App\Filament\Resources\Volunteers\Pages\EditVolunteer;
use App\Filament\Resources\Volunteers\Pages\ListVolunteers;
use App\Filament\Resources\Volunteers\VolunteerResource;
use App\Filament\Resources\WaterQualityRecords\WaterQualityRecordResource;
use App\Filament\Resources\WaterSamplingPoints\WaterSamplingPointResource;
use App\Models\User;
use App\Models\Volunteer;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
    Filament::setCurrentPanel('admin');
});

it('applies the shared redirect trait to every create and edit page', function () {
    $resources = [
        ActivityResource::class,
        BlogPostResource::class,
        DonationChannelResource::class,
        FounderResource::class,
        GalleryItemResource::class,
        HeroSlideResource::class,
        MemberResource::class,
        OrgCategoryResource::class,
        OrgPersonResource::class,
        PartnershipResource::class,
        ProgramResource::class,
        RunningTextResource::class,
        VolunteerResource::class,
        WaterQualityRecordResource::class,
        WaterSamplingPointResource::class,
    ];

    $checked = 0;

    foreach ($resources as $resource) {
        foreach ($resource::getPages() as $name => $registration) {
            if (! in_array($name, ['create', 'edit'], true)) {
                continue;
            }

            expect(class_uses_recursive($registration->getPage()))
                ->toContain(RedirectsToResourceIndex::class);

            $checked++;
        }
    }

    expect($checked)->toBe(30);
});

it('redirects to the resource index after creating a record', function () {
    Livewire::test(CreateVolunteer::class)
        ->fillForm([
            'full_name' => 'Relawan Baru',
            'email' => 'baru@example.com',
            'phone' => '081255555555',
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect(VolunteerResource::getUrl('index'));
});

it('redirects to the previous filtered listing after creating a record', function () {
    $listUrl = VolunteerResource::getUrl('index', [
        'tableFilters' => ['status' => ['value' => 'APPROVED']],
        'page' => 2,
    ]);

    Livewire::test(CreateVolunteer::class)
        ->set('previousUrl', $listUrl)
        ->fillForm([
            'full_name' => 'Relawan Terfilter',
            'email' => 'terfilter@example.com',
            'phone' => '081266666666',
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertRedirect($listUrl);
});

it('redirects to the previous filtered listing after editing a record', function () {
    $volunteer = Volunteer::create([
        'full_name' => 'Relawan Edit',
        'email' => 'edit@example.com',
        'phone' => '081277777777',
        'submitted_at' => now(),
    ]);

    $listUrl = VolunteerResource::getUrl('index', [
        'tableFilters' => ['status' => ['value' => 'PENDING']],
        'page' => 3,
    ]);

    Livewire::test(EditVolunteer::class, ['record' => $volunteer->getRouteKey()])
        ->set('previousUrl', $listUrl)
        ->fillForm(['full_name' => 'Relawan Edit Diperbarui'])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect($listUrl);

    expect($volunteer->fresh()->full_name)->toBe('Relawan Edit Diperbarui');
});

it('falls back to the resource index for an external previous url', function () {
    $volunteer = Volunteer::create([
        'full_name' => 'Relawan Aman',
        'email' => 'aman@example.com',
        'phone' => '081288888888',
        'submitted_at' => now(),
    ]);

    Livewire::test(EditVolunteer::class, ['record' => $volunteer->getRouteKey()])
        ->set('previousUrl', 'https://evil.example/admin/volunteers')
        ->fillForm(['full_name' => 'Relawan Aman Diperbarui'])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertRedirect(VolunteerResource::getUrl('index'));
});

it('falls back to the resource index for a previous url from another resource', function () {
    $volunteer = Volunteer::create([
        'full_name' => 'Relawan Lain',
        'email' => 'lain@example.com',
        'phone' => '081299999999',
        'submitted_at' => now(),
    ]);

    Livewire::test(EditVolunteer::class, ['record' => $volunteer->getRouteKey()])
        ->set('previousUrl', RunningTextResource::getUrl('index'))
        ->fillForm(['full_name' => 'Relawan Lain Diperbarui'])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertRedirect(VolunteerResource::getUrl('index'));
});

it('stays on the listing page after deleting a record', function () {
    $volunteer = Volunteer::create([
        'full_name' => 'Relawan Hapus',
        'email' => 'hapus@example.com',
        'phone' => '081200001111',
        'submitted_at' => now(),
    ]);

    Livewire::test(ListVolunteers::class)
        ->callTableAction('delete', $volunteer)
        ->assertNotified()
        ->assertOk();

    $this->assertDatabaseMissing('volunteers', ['id' => $volunteer->id]);
});

it('stays on the listing page after bulk deleting records', function () {
    $first = Volunteer::create([
        'full_name' => 'Relawan Hapus Satu',
        'email' => 'hapus1@example.com',
        'phone' => '081200002222',
        'submitted_at' => now(),
    ]);

    $second = Volunteer::create([
        'full_name' => 'Relawan Hapus Dua',
        'email' => 'hapus2@example.com',
        'phone' => '081200003333',
        'submitted_at' => now(),
    ]);

    Livewire::test(ListVolunteers::class)
        ->callTableBulkAction('delete', [$first, $second])
        ->assertNotified()
        ->assertOk();

    $this->assertDatabaseMissing('volunteers', ['id' => $first->id]);
    $this->assertDatabaseMissing('volunteers', ['id' => $second->id]);
});

it('applies the redirect behavior to another representative resource', function () {
    Livewire::test(CreateRunningText::class)
        ->fillForm([
            'text' => 'Selamat datang di TMCC',
            'is_active' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertRedirect(RunningTextResource::getUrl('index'));
});
