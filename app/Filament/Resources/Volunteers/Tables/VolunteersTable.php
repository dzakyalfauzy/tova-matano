<?php

namespace App\Filament\Resources\Volunteers\Tables;

use App\Models\Volunteer;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Js;

class VolunteersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('registration_code')
                    ->label('Kode Pendaftaran')
                    ->searchable(),
                ImageColumn::make('photo_url')
                    ->label('Foto'),
                TextColumn::make('full_name')
                    ->label('Nama Lengkap')
                    ->searchable(),
                TextColumn::make('name_and_quote')
                    ->label('Nama + Kutipan')
                    ->state(function (Volunteer $record): ?string {
                        if (blank($record->quote)) {
                            return null;
                        }

                        return "/{$record->full_name} + /{$record->quote}";
                    })
                    ->placeholder('Belum ada kutipan')
                    ->copyable(fn (Volunteer $record): bool => filled($record->quote))
                    ->copyableState(fn (Volunteer $record): ?string => filled($record->quote) ? "/{$record->full_name} + /{$record->quote}" : null)
                    ->copyMessage('Format Nama + Kutipan berhasil disalin!')
                    ->tooltip(fn (Volunteer $record): ?string => filled($record->quote) ? "/{$record->full_name} + /{$record->quote}" : null)
                    ->wrap()
                    ->searchable(query: function ($query, string $search) {
                        $query->where(function ($q) use ($search) {
                            $q->where('full_name', 'like', "%{$search}%")
                                ->orWhere('quote', 'like', "%{$search}%");
                        });
                    }),
                TextColumn::make('quote_consent')
                    ->label('Izin Publikasi')
                    ->badge()
                    ->formatStateUsing(function (bool $state, Volunteer $record): string {
                        if (blank($record->quote)) {
                            return 'Belum ada kutipan';
                        }

                        return $state ? 'Disetujui untuk publikasi' : 'Belum disetujui';
                    })
                    ->color(function (bool $state, Volunteer $record): string {
                        if (blank($record->quote)) {
                            return 'gray';
                        }

                        return $state ? 'success' : 'warning';
                    }),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'PENDING' => 'warning',
                        'APPROVED' => 'success',
                        'REJECTED' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('submitted_at')
                    ->label('Dikirim Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('submitted_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'PENDING' => 'Menunggu',
                        'APPROVED' => 'Disetujui',
                        'REJECTED' => 'Ditolak',
                    ]),
                SelectFilter::make('quote_consent')
                    ->label('Izin Publikasi Kutipan')
                    ->options([
                        'approved' => 'Disetujui untuk publikasi',
                        'unapproved' => 'Belum disetujui / Belum ada kutipan',
                    ])
                    ->query(function ($query, array $data) {
                        if (($data['value'] ?? null) === 'approved') {
                            $query->whereNotNull('quote')->where('quote', '!=', '')->where('quote_consent', true);
                        } elseif (($data['value'] ?? null) === 'unapproved') {
                            $query->where(function ($q) {
                                $q->whereNull('quote')->orWhere('quote', '')->orWhere('quote_consent', false);
                            });
                        }
                    }),
            ])
            ->recordActions([
                Action::make('copy_quote')
                    ->label('Salin Kutipan')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('info')
                    ->visible(fn (Volunteer $record): bool => filled($record->quote))
                    ->alpineClickHandler(fn (Volunteer $record): string => 'window.navigator.clipboard.writeText('.Js::from("/{$record->full_name} + /{$record->quote}").'); new FilamentNotification().title('.Js::from("Tersalin: /{$record->full_name} + /{$record->quote}").').success().send();'
                    ),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
