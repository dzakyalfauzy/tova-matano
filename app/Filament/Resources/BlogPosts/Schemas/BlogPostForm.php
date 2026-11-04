<?php

namespace App\Filament\Resources\BlogPosts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BlogPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Artikel')
                    ->description('Data utama artikel yang akan dipublikasikan.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul')
                            ->helperText('Slug dibuat otomatis dari judul saat data baru dibuat.')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->helperText('Alamat unik artikel pada URL.')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Select::make('category')
                            ->label('Kategori')
                            ->options([
                                'EDUKASI' => 'Edukasi',
                                'RILIS_PERS' => 'Rilis Pers',
                                'BERITA' => 'Berita',
                                'KEBIJAKAN' => 'Kebijakan',
                                'BASELINE_STUDY' => 'Baseline Study',
                            ])
                            ->required(),
                        DateTimePicker::make('published_at')
                            ->label('Tanggal Publikasi')
                            ->default(now())
                            ->required(),
                        Toggle::make('is_published')
                            ->label('Status Terbit')
                            ->helperText('Aktifkan agar artikel tampil di situs.')
                            ->default(true)
                            ->required(),
                    ]),

                Section::make('Konten & Lampiran')
                    ->description('Gambar sampul, lampiran PDF, dan isi tulisan.')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('cover_image_url')
                            ->label('Gambar Sampul')
                            ->image()
                            ->disk('public')
                            ->directory('blog-posts')
                            ->columnSpanFull(),
                        FileUpload::make('pdf_url')
                            ->label('Upload File PDF (Opsional)')
                            ->acceptedFileTypes(['application/pdf'])
                            ->disk('public')
                            ->directory('blog-pdfs')
                            ->columnSpanFull(),
                        RichEditor::make('content')
                            ->label('Konten Tulisan (Kosongkan jika hanya ingin menampilkan PDF)')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
