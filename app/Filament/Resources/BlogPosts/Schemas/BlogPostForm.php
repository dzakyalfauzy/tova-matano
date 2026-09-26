<?php

namespace App\Filament\Resources\BlogPosts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;

class BlogPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                Select::make('category')
                    ->options([
                        'EDUKASI' => 'Edukasi',
                        'RILIS_PERS' => 'Rilis Pers',
                        'BERITA' => 'Berita',
                        'KEBIJAKAN' => 'Kebijakan',
                    ])
                    ->required(),
                FileUpload::make('cover_image_url')
                    ->image()
                    ->disk('public')
                    ->directory('blog-posts'),
                RichEditor::make('content')
                    ->required()
                    ->columnSpanFull(),
                DateTimePicker::make('published_at')
                    ->default(now())
                    ->required(),
                Toggle::make('is_published')
                    ->default(true)
                    ->required(),
            ]);
    }
}
