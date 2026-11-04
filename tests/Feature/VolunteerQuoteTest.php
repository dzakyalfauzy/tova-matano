<?php

use App\Filament\Resources\Volunteers\Pages\EditVolunteer;
use App\Filament\Resources\Volunteers\Pages\ListVolunteers;
use App\Models\User;
use App\Models\Volunteer;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

beforeEach(function () {
    Mail::fake();

    $this->actingAs(User::factory()->create());
    Filament::setCurrentPanel('admin');
});

function submitVolunteer(TestCase $testCase, array $overrides = [])
{
    return $testCase->from('/gabung-donasi')->post(route('volunteer.submit'), array_merge([
        'full_name' => 'Relawan Uji',
        'email' => 'relawan@example.com',
        'phone' => '081234567890',
        'address' => 'Sorowako',
        'motivation' => 'Ingin membantu pelestarian Danau Matano.',
    ], $overrides));
}

it('registers a volunteer without a quote and keeps quote consent false', function () {
    submitVolunteer($this)
        ->assertRedirect('/gabung-donasi')
        ->assertSessionHas('registration_code');

    $volunteer = Volunteer::where('email', 'relawan@example.com')->firstOrFail();

    expect($volunteer->quote)->toBeNull()
        ->and($volunteer->quote_consent)->toBeFalse()
        ->and($volunteer->registration_code)->not->toBeEmpty()
        ->and($volunteer->status)->toBe('PENDING');
});

it('registers a volunteer with a quote and explicit publication consent', function () {
    $quote = 'Danau Matano adalah warisan yang harus kita jaga bersama.';

    submitVolunteer($this, [
        'quote' => $quote,
        'quote_consent' => '1',
    ])->assertRedirect('/gabung-donasi');

    $volunteer = Volunteer::where('email', 'relawan@example.com')->firstOrFail();

    expect($volunteer->quote)->toBe($quote)
        ->and($volunteer->quote_consent)->toBeTrue();
});

it('rejects a quote when publication consent is not given', function () {
    submitVolunteer($this, [
        'quote' => 'Kutipan tanpa persetujuan.',
        'quote_consent' => '0',
    ])->assertSessionHasErrors('quote_consent');

    expect(Volunteer::count())->toBe(0);
});

it('rejects a quote longer than 300 characters', function () {
    submitVolunteer($this, [
        'quote' => str_repeat('a', 301),
        'quote_consent' => '1',
    ])->assertSessionHasErrors('quote');

    expect(Volunteer::count())->toBe(0);
});

it('keeps existing volunteer records valid after the quote migration', function () {
    $volunteer = Volunteer::create([
        'full_name' => 'Relawan Lama',
        'email' => 'lama@example.com',
        'phone' => '081200000000',
        'status' => 'APPROVED',
        'submitted_at' => now()->subYear(),
    ])->fresh();

    expect($volunteer->quote)->toBeNull()
        ->and($volunteer->quote_consent)->toBeFalse()
        ->and($volunteer->registration_code)->not->toBeEmpty()
        ->and($volunteer->isApproved())->toBeTrue();
});

it('lets an admin view and edit a volunteer quote and consent', function () {
    $volunteer = Volunteer::create([
        'full_name' => 'Relawan Admin',
        'email' => 'admin-edit@example.com',
        'phone' => '081211111111',
        'quote' => 'Kutipan awal.',
        'quote_consent' => true,
        'submitted_at' => now(),
    ]);

    Livewire::test(EditVolunteer::class, ['record' => $volunteer->getRouteKey()])
        ->assertFormSet([
            'quote' => 'Kutipan awal.',
            'quote_consent' => true,
        ])
        ->fillForm([
            'quote' => 'Kutipan yang diperbarui oleh admin.',
            'quote_consent' => true,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $volunteer->refresh();

    expect($volunteer->quote)->toBe('Kutipan yang diperbarui oleh admin.')
        ->and($volunteer->quote_consent)->toBeTrue();
});

it('formats the volunteer table name and quote with the exact slash format', function () {
    $volunteer = Volunteer::create([
        'full_name' => 'Dzaky Al Fauzy',
        'email' => 'dzaky@example.com',
        'phone' => '081222222222',
        'quote' => 'Danau Matano adalah warisan yang harus kita jaga bersama.',
        'quote_consent' => true,
        'submitted_at' => now(),
    ]);

    Livewire::test(ListVolunteers::class)
        ->assertSee('/Dzaky Al Fauzy + /Danau Matano adalah warisan yang harus kita jaga bersama.')
        ->assertSee('Disetujui untuk publikasi');
});

it('offers the copy action only for volunteers with a quote', function () {
    $withQuote = Volunteer::create([
        'full_name' => 'Punya Kutipan',
        'email' => 'punya@example.com',
        'phone' => '081255555555',
        'quote' => 'Kutipan untuk disalin.',
        'quote_consent' => true,
        'submitted_at' => now(),
    ]);

    $withoutQuote = Volunteer::create([
        'full_name' => 'Tanpa Kutipan Aksi',
        'email' => 'tanpaaksi@example.com',
        'phone' => '081266666666',
        'submitted_at' => now(),
    ]);

    Livewire::test(ListVolunteers::class)
        ->assertTableActionVisible('copy_quote', $withQuote)
        ->assertTableActionHidden('copy_quote', $withoutQuote);
});

it('filters volunteers by quote publication consent', function () {
    $approved = Volunteer::create([
        'full_name' => 'Sudah Setuju',
        'email' => 'setuju@example.com',
        'phone' => '081277777777',
        'quote' => 'Kutipan yang disetujui.',
        'quote_consent' => true,
        'submitted_at' => now(),
    ]);

    $unapproved = Volunteer::create([
        'full_name' => 'Belum Setuju Filter',
        'email' => 'belumsetuju@example.com',
        'phone' => '081288888888',
        'quote' => 'Kutipan tanpa izin.',
        'quote_consent' => false,
        'submitted_at' => now(),
    ]);

    $blank = Volunteer::create([
        'full_name' => 'Tanpa Kutipan Filter',
        'email' => 'tanpafilter@example.com',
        'phone' => '081299999999',
        'submitted_at' => now(),
    ]);

    Livewire::test(ListVolunteers::class)
        ->filterTable('quote_consent', 'approved')
        ->assertCanSeeTableRecords([$approved])
        ->assertCanNotSeeTableRecords([$unapproved, $blank]);

    Livewire::test(ListVolunteers::class)
        ->filterTable('quote_consent', 'unapproved')
        ->assertCanSeeTableRecords([$unapproved, $blank])
        ->assertCanNotSeeTableRecords([$approved]);
});

it('shows a clear indicator for volunteers without a quote', function () {
    Volunteer::create([
        'full_name' => 'Tanpa Kutipan',
        'email' => 'noquote@example.com',
        'phone' => '081233333333',
        'submitted_at' => now(),
    ]);

    Livewire::test(ListVolunteers::class)
        ->assertSee('Belum ada kutipan')
        ->assertDontSee('/Tanpa Kutipan + /');
});

it('marks a submitted quote without consent as not authorized for publication', function () {
    Volunteer::create([
        'full_name' => 'Belum Setuju',
        'email' => 'unapproved@example.com',
        'phone' => '081244444444',
        'quote' => 'Kutipan pribadi yang belum disetujui.',
        'quote_consent' => false,
        'submitted_at' => now(),
    ]);

    Livewire::test(ListVolunteers::class)
        ->assertSee('Belum disetujui');

    $volunteer = Volunteer::where('email', 'unapproved@example.com')->firstOrFail();

    expect($volunteer->quote_consent)->toBeFalse();
});
