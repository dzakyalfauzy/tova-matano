<?php

namespace App\Filament\Resources\BlogPosts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class BlogPostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_image_url')
                    ->label('Gambar Sampul'),
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable(),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable(),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge(),
                TextColumn::make('published_at')
                    ->label('Tanggal Terbit')
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('is_published')
                    ->label('Terbit')
                    ->boolean(),
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
            ->defaultSort('published_at', 'desc')
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options([
                        'EDUKASI' => 'Edukasi',
                        'RILIS_PERS' => 'Rilis Pers',
                        'BERITA' => 'Berita',
                        'KEBIJAKAN' => 'Kebijakan',
                        'BASELINE_STUDY' => 'Baseline Study',
                    ]),
                TernaryFilter::make('is_published')
                    ->label('Status Publikasi')
                    ->trueLabel('Terbit')
                    ->falseLabel('Draf'),
            ])
            ->recordActions([
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
