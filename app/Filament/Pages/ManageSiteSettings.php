<?php

namespace App\Filament\Pages;

use App\Models\SiteSettings;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;
use BackedEnum;

class ManageSiteSettings extends Page
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected string $view = 'filament.pages.manage-site-settings';
    public ?array $data = [];

    public function mount(): void
    {
        $settings = SiteSettings::firstOrCreate([]);
        $this->form->fill($settings->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Statistik Beranda')
                    ->description('Angka-angka yang tampil di section Tentang Kami pada halaman Beranda. Relawan Aktif & Program Terlaksana dihitung otomatis dari database.')
                    ->schema([
                        TextInput::make('stat_sampah_terangkut')
                            ->label('Sampah Terangkut')
                            ->placeholder('contoh: 2,4T')
                            ->helperText('Isi dengan nilai dan satuan, misal: 2,4 Ton atau 2,4T'),
                        TextInput::make('stat_sekolah_teredukasi')
                            ->label('Sekolah Teredukasi')
                            ->placeholder('contoh: 8')
                            ->helperText('Isi dengan jumlah sekolah yang sudah teredukasi'),
                    ])->columns(2),
                Section::make('Tampilan Header & Gambar')
                    ->schema([
                        FileUpload::make('about_hero_image_url')
                            ->label('Background Image Halaman Tentang Kami')
                            ->image()
                            ->disk('public')
                            ->directory('settings'),
                        FileUpload::make('org_hero_image_url')
                            ->label('Background Image Halaman Struktur Organisasi')
                            ->image()
                            ->disk('public')
                            ->directory('settings'),
                        FileUpload::make('org_chart_image_url')
                            ->label('Gambar Bagan Organisasi (Opsional)')
                            ->image()
                            ->disk('public')
                            ->directory('settings'),
                    ]),
                Section::make('Kontak & Media Sosial')
                    ->schema([
                        TextInput::make('address'),
                        TextInput::make('phone')->tel(),
                        TextInput::make('whatsapp')->tel(),
                        TextInput::make('email')->email(),
                        TextInput::make('instagram_url')->url(),
                        TextInput::make('facebook_url')->url(),
                        TextInput::make('tiktok_url')->url(),
                        TextInput::make('twitter_url')->url(),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $settings = SiteSettings::firstOrCreate([]);
        $settings->update($this->form->getState());

        Notification::make()
            ->success()
            ->title('Settings updated')
            ->send();
    }
}
