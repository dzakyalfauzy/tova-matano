<?php

namespace App\Filament\Pages;

use App\Models\SiteSettings;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ManageSiteSettings extends Page
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected string $view = 'filament.pages.manage-site-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = SiteSettings::firstOrCreate([]);
        $data = $settings->toArray();
        if (! isset($data['fixed_text']) || ! is_array($data['fixed_text'])) {
            $data['fixed_text'] = ['id' => '', 'en' => ''];
        }
        $this->form->fill($data);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Teks Tetap Hero Beranda')
                    ->description('Teks statis yang tampil di bagian atas slider pada halaman Beranda (tidak ikut berganti saat slide bergeser). Kosongkan jika tidak ingin menampilkan teks tetap.')
                    ->schema([
                        TextInput::make('fixed_text.id')
                            ->label('Teks Tetap (Bahasa Indonesia)')
                            ->placeholder('Contoh: Tova Matano Care Center'),
                        TextInput::make('fixed_text.en')
                            ->label('Fixed Text (English)')
                            ->placeholder('Example: Tova Matano Care Center'),
                    ])->columns(2),
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
                        FileUpload::make('about_lake_image_url')
                            ->label('Foto Danau Matano (Section Latar Belakang)')
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
