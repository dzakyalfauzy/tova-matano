<?php

use App\Filament\Resources\Members\Pages\CreateMember;
use App\Filament\Resources\Members\Pages\EditMember;
use App\Filament\Resources\Members\Pages\ListMembers;
use App\Models\Member;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
    Filament::setCurrentPanel('admin');
});

function memberForSorting(string $name, int $points): Member
{
    return Member::create([
        'name' => $name,
        'points' => $points,
        'joined_at' => now(),
    ]);
}

it('sorts the NAMA column from A to Z', function () {
    $aminah = memberForSorting('Aminah', 100);
    $budi = memberForSorting('Budi', 200);
    $zulkifli = memberForSorting('Zulkifli', 300);

    Livewire::test(ListMembers::class)
        ->sortTable('name', 'asc')
        ->assertCanSeeTableRecords([$aminah, $budi, $zulkifli], inOrder: true);
});

it('sorts the NAMA column from Z to A', function () {
    $aminah = memberForSorting('Aminah', 100);
    $budi = memberForSorting('Budi', 200);
    $zulkifli = memberForSorting('Zulkifli', 300);

    Livewire::test(ListMembers::class)
        ->sortTable('name', 'desc')
        ->assertCanSeeTableRecords([$zulkifli, $budi, $aminah], inOrder: true);
});

it('keeps POINT descending as the default sort', function () {
    $aminah = memberForSorting('Aminah', 100);
    $budi = memberForSorting('Budi', 200);
    $zulkifli = memberForSorting('Zulkifli', 300);

    Livewire::test(ListMembers::class)
        ->assertCanSeeTableRecords([$zulkifli, $budi, $aminah], inOrder: true);
});

it('searches members by name', function () {
    $aminah = memberForSorting('Aminah', 100);
    $budi = memberForSorting('Budi', 200);
    $zulkifli = memberForSorting('Zulkifli', 300);

    Livewire::test(ListMembers::class)
        ->searchTable('Aminah')
        ->assertCanSeeTableRecords([$aminah])
        ->assertCanNotSeeTableRecords([$budi, $zulkifli]);
});

it('combines name search with name sorting', function () {
    $ahmad = memberForSorting('Ahmad', 10);
    $amir = memberForSorting('Amir', 20);
    $budi = memberForSorting('Budi', 30);
    $zaki = memberForSorting('Zaki', 40);

    Livewire::test(ListMembers::class)
        ->searchTable('a')
        ->sortTable('name', 'desc')
        ->assertCanSeeTableRecords([$zaki, $amir, $ahmad], inOrder: true)
        ->assertCanNotSeeTableRecords([$budi]);
});

it('paginates while name sorting is active', function () {
    $members = [];

    foreach (range('A', 'L') as $index => $suffix) {
        $members[$suffix] = memberForSorting("Anggota {$suffix}", $index);
    }

    $pageOne = collect(['L', 'K', 'J', 'I', 'H', 'G', 'F', 'E', 'D', 'C'])
        ->map(fn (string $suffix) => $members[$suffix])
        ->all();

    Livewire::test(ListMembers::class)
        ->sortTable('name', 'desc')
        ->assertCanSeeTableRecords($pageOne, inOrder: true)
        ->assertCanNotSeeTableRecords([$members['B'], $members['A']])
        ->call('gotoPage', 2)
        ->assertCanSeeTableRecords([$members['B'], $members['A']], inOrder: true);
});

it('keeps member create and edit pages functional', function () {
    Livewire::test(CreateMember::class)
        ->fillForm([
            'name' => 'Anggota Baru',
            'points' => 10,
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified();

    $member = Member::where('name', 'Anggota Baru')->firstOrFail();

    Livewire::test(EditMember::class, ['record' => $member->getRouteKey()])
        ->fillForm(['name' => 'Anggota Diperbarui'])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified();

    expect($member->fresh()->name)->toBe('Anggota Diperbarui');
});

it('keeps member edit and delete table actions available', function () {
    $member = memberForSorting('Anggota Aksi', 5);

    Livewire::test(ListMembers::class)
        ->assertTableActionVisible('edit', $member)
        ->assertTableActionVisible('delete', $member)
        ->callTableAction('delete', $member)
        ->assertNotified();

    expect(Member::find($member->id))->toBeNull();
});
