<?php

use App\Models\Member;
use App\Models\Volunteer;
use Inertia\Testing\AssertableInertia as Assert;

function membersPageSource(): string
{
    return file_get_contents(resource_path('js/Pages/Members.jsx'));
}

it('returns HTTP 200 and the Members component with member and approved volunteer props', function () {
    $member = Member::create([
        'name' => 'Budi Santoso',
        'points' => 250,
        'photo_url' => 'members/budi.webp',
        'divisi_name' => 'Divisi Lapangan',
        'message' => 'Peduli Danau Matano',
        'joined_at' => now(),
    ]);

    $approvedVolunteer = Volunteer::create([
        'full_name' => 'Siti Nurhaliza',
        'email' => 'siti@example.com',
        'phone' => '081234567890',
        'status' => 'APPROVED',
        'photo_url' => 'volunteers/siti.webp',
        'submitted_at' => now(),
    ]);

    $response = $this->get('/anggota');

    $response->assertOk()->assertInertia(fn (Assert $page) => $page
        ->component('Members')
        ->has('members', 1)
        ->has('volunteers', 1)
        ->where('members.0.name', 'Budi Santoso')
        ->where('members.0.photo_url', 'members/budi.webp')
        ->where('volunteers.0.full_name', 'Siti Nurhaliza')
        ->where('volunteers.0.photo_url', 'volunteers/siti.webp')
    );
});

it('filters out pending and rejected volunteers from the public page', function () {
    $approved = Volunteer::create([
        'full_name' => 'Relawan Disetujui',
        'email' => 'approved@example.com',
        'phone' => '081200000001',
        'status' => 'APPROVED',
        'submitted_at' => now(),
    ]);

    $pending = Volunteer::create([
        'full_name' => 'Relawan Menunggu',
        'email' => 'pending@example.com',
        'phone' => '081200000002',
        'status' => 'PENDING',
        'submitted_at' => now(),
    ]);

    $rejected = Volunteer::create([
        'full_name' => 'Relawan Ditolak',
        'email' => 'rejected@example.com',
        'phone' => '081200000003',
        'status' => 'REJECTED',
        'submitted_at' => now(),
    ]);

    $response = $this->get('/anggota');
    $volunteers = collect($response->inertiaProps()['volunteers']);

    expect($volunteers->pluck('full_name'))->toContain('Relawan Disetujui')
        ->and($volunteers->pluck('full_name'))->not->toContain('Relawan Menunggu')
        ->and($volunteers->pluck('full_name'))->not->toContain('Relawan Ditolak');
});

it('orders members descending by points', function () {
    $low = Member::create(['name' => 'Rendah Poin', 'points' => 10, 'joined_at' => now()]);
    $high = Member::create(['name' => 'Tinggi Poin', 'points' => 1000, 'joined_at' => now()]);

    $response = $this->get('/anggota');
    $members = collect($response->inertiaProps()['members']);

    expect($members->first()['name'])->toBe('Tinggi Poin')
        ->and($members->last()['name'])->toBe('Rendah Poin');
});

it('renders the public page gracefully when no members or volunteers exist', function () {
    $response = $this->get('/anggota');

    $response->assertOk()->assertInertia(fn (Assert $page) => $page
        ->component('Members')
        ->has('members', 0)
        ->has('volunteers', 0)
    );

    $source = membersPageSource();
    expect($source)->toContain('Belum ada data anggota inti.')
        ->and($source)->toContain('Belum ada relawan yang disetujui.');
});

it('constructs storage URLs for member and volunteer photos with initials fallback', function () {
    $source = membersPageSource();

    // Verify member photo branch and fallback
    expect($source)->toContain('member.photo_url ? (')
        ->and($source)->toContain('src={`/storage/${member.photo_url}`}')
        ->and($source)->toContain('member.name.charAt(0)');

    // Verify volunteer photo branch and fallback
    expect($source)->toContain('volunteer.photo_url ? (')
        ->and($source)->toContain('src={`/storage/${volunteer.photo_url}`}')
        ->and($source)->toContain("volunteer.full_name?.charAt(0) || 'R'");
});
