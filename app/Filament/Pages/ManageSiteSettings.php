<?php

namespace App\Filament\Pages;

use App\Models\SiteSettings;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Models\Contracts\FilamentUser;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Throwable;

class ManageSiteSettings extends Page
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Pengaturan Situs';

    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?int $navigationSort = 10;

    protected static ?string $title = 'Pengaturan Situs';

    protected string $view = 'filament.pages.manage-site-settings';

    public ?array $data = [];

    private array $pendingMapUploads = [];

    public function mount(): void
    {
        $settings = SiteSettings::firstOrCreate([]);
        $data = $settings->toArray();
        if (! isset($data['fixed_text']) || ! is_array($data['fixed_text'])) {
            $data['fixed_text'] = ['id' => '', 'en' => ''];
        }
        $data['about_content'] = $this->normalizeAboutContent($data['about_content'] ?? []);
        $this->form->fill($data);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->record(SiteSettings::firstOrCreate([]))
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
                        TextInput::make('about_lake_image_credit')
                            ->label('Kredit Foto Danau Matano (Opsional)')
                            ->placeholder('Contoh: Dokumentasi TMCC')
                            ->helperText('Tampil sebagai keterangan kecil pada foto Danau Matano. Kosongkan jika tidak diperlukan.')
                            ->columnSpanFull(),
                        FileUpload::make('org_hero_image_url')
                            ->label('Background Image Halaman Struktur Organisasi')
                            ->image()
                            ->disk('public')
                            ->directory('settings'),
                        FileUpload::make('org_chart_image_url')
                            ->label('Gambar Bagan Organisasi (Opsional)')
                            ->helperText('Gambar bagan organisasi resmi yang tampil di halaman publik Struktur Organisasi. Unggah PNG, JPEG, atau WebP maksimal 5 MB. Kosongkan untuk menyembunyikan bagan.')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(5120)
                            ->disk('public')
                            ->directory('settings')
                            ->visibility('public')
                            ->preventFilePathTampering()
                            ->imagePreviewHeight('240')
                            ->openable()
                            ->downloadable()
                            ->columnSpanFull(),
                        FileUpload::make('blog_hero_image_url')
                            ->label('Gambar Background Hero Blog')
                            ->helperText('Gambar background hero yang tampil di bagian atas halaman publik Berita & Cerita. Unggah JPEG, PNG, atau WebP maksimal 5 MB. Kosongkan untuk menggunakan warna standar.')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(5120)
                            ->disk('public')
                            ->directory('settings/blog')
                            ->visibility('public')
                            ->preventFilePathTampering()
                            ->imagePreviewHeight('240')
                            ->openable()
                            ->downloadable()
                            ->columnSpanFull(),
                        FileUpload::make('partnership_hero_image_url')
                            ->label('Background Hero Partnership')
                            ->helperText('Gambar background hero yang tampil di bagian atas halaman publik Partnership. Unggah JPEG, PNG, atau WebP maksimal 5 MB. Kosongkan untuk menggunakan warna standar.')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(5120)
                            ->disk('public')
                            ->directory('settings/partnership')
                            ->visibility('public')
                            ->preventFilePathTampering()
                            ->imagePreviewHeight('240')
                            ->openable()
                            ->downloadable()
                            ->columnSpanFull(),
                        FileUpload::make('program_hero_image_url')
                            ->label('Background Hero Program')
                            ->helperText('Gambar background hero yang tampil di bagian atas halaman publik Program. Unggah JPEG, PNG, atau WebP maksimal 5 MB. Kosongkan untuk menggunakan warna standar.')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(5120)
                            ->disk('public')
                            ->directory('settings/program')
                            ->visibility('public')
                            ->preventFilePathTampering()
                            ->imagePreviewHeight('240')
                            ->openable()
                            ->downloadable()
                            ->columnSpanFull(),
                        FileUpload::make('contact_hero_image_url')
                            ->label('Background Hero Contact')
                            ->helperText('Gambar background hero yang tampil di bagian atas halaman publik Kontak. Unggah JPEG, PNG, atau WebP maksimal 5 MB. Kosongkan untuk menggunakan warna standar.')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(5120)
                            ->disk('public')
                            ->directory('settings/contact')
                            ->visibility('public')
                            ->preventFilePathTampering()
                            ->imagePreviewHeight('240')
                            ->openable()
                            ->downloadable()
                            ->columnSpanFull(),
                    ]),
                Section::make('Peta Titik Sampling — Halaman Kualitas Air')
                    ->description('Gambar peta yang menampilkan titik/posisi sampling station. Unggah JPEG, PNG, atau WebP maksimal 5 MB dan 6000 × 6000 piksel. Kosongkan gambar untuk menyembunyikan section peta di halaman publik.')
                    ->collapsible()
                    ->schema([
                        FileUpload::make('water_quality_map_image_url')
                            ->label('Gambar Peta Titik Sampling')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(5120)
                            ->rules(['dimensions:max_width=6000,max_height=6000'])
                            ->disk('public')
                            ->directory('settings/water-quality')
                            ->visibility('public')
                            ->preventFilePathTampering()
                            ->saveUploadedFileUsing(function (BaseFileUpload $component, TemporaryUploadedFile $file): ?string {
                                $path = $component->saveUploadedFile($file);

                                if (filled($path)) {
                                    $this->pendingMapUploads[] = $path;
                                }

                                return $path;
                            })
                            ->imagePreviewHeight('240')
                            ->openable()
                            ->downloadable()
                            ->columnSpanFull(),
                        TextInput::make('water_quality_map_image_credit')
                            ->label('Kredit/Sumber Peta (Opsional)')
                            ->maxLength(255)
                            ->helperText('Kredit akan tampil sebagai keterangan kecil di bawah gambar peta pada halaman publik Kualitas Air. Kosongkan jika tidak diperlukan.')
                            ->columnSpanFull(),
                    ]),
                Section::make('Konten Tentang Kami — Hero')
                    ->description('Judul besar, label kecil, dan subjudul pada bagian pembuka halaman Tentang Kami. Isi versi Indonesia; versi Inggris opsional dan otomatis memakai versi Indonesia jika kosong.')
                    ->collapsible()
                    ->schema([
                        TextInput::make('about_content.hero.eyebrow.id')
                            ->label('Label Kecil (Bahasa Indonesia)')
                            ->placeholder('Tentang Kami'),
                        TextInput::make('about_content.hero.eyebrow.en')
                            ->label('Eyebrow (English)')
                            ->placeholder('About Us'),
                        TextInput::make('about_content.hero.title.id')
                            ->label('Judul (Bahasa Indonesia)')
                            ->placeholder('ADVOKASI LINGKUNGAN LOKAL'),
                        TextInput::make('about_content.hero.title.en')
                            ->label('Title (English)')
                            ->placeholder('LOCAL ENVIRONMENTAL ADVOCACY'),
                        Textarea::make('about_content.hero.subtitle.id')
                            ->label('Subjudul (Bahasa Indonesia)')
                            ->rows(2),
                        Textarea::make('about_content.hero.subtitle.en')
                            ->label('Subtitle (English)')
                            ->rows(2),
                    ])->columns(2),
                Section::make('Sekilas Danau Matano')
                    ->description('Judul dan isi bagian latar belakang Danau Matano. Pisahkan paragraf dengan satu baris kosong.')
                    ->collapsible()
                    ->schema([
                        TextInput::make('about_content.lake.title.id')
                            ->label('Judul (Bahasa Indonesia)')
                            ->placeholder('Sekilas Danau Matano'),
                        TextInput::make('about_content.lake.title.en')
                            ->label('Title (English)')
                            ->placeholder('About Lake Matano'),
                        Textarea::make('about_content.lake.body.id')
                            ->label('Isi (Bahasa Indonesia)')
                            ->rows(6),
                        Textarea::make('about_content.lake.body.en')
                            ->label('Body (English)')
                            ->rows(6),
                    ])->columns(2),
                Section::make('Profil & Sejarah')
                    ->description('Judul dan isi bagian profil serta sejarah TMCC. Pisahkan paragraf dengan satu baris kosong.')
                    ->collapsible()
                    ->schema([
                        TextInput::make('about_content.profile.title.id')
                            ->label('Judul (Bahasa Indonesia)')
                            ->placeholder('Profil & Sejarah'),
                        TextInput::make('about_content.profile.title.en')
                            ->label('Title (English)')
                            ->placeholder('Profile & History'),
                        Textarea::make('about_content.profile.body.id')
                            ->label('Isi (Bahasa Indonesia)')
                            ->rows(6),
                        Textarea::make('about_content.profile.body.en')
                            ->label('Body (English)')
                            ->rows(6),
                    ])->columns(2),
                Section::make('Potensi TMCC')
                    ->description('Judul dan isi bagian potensi TMCC. Pisahkan paragraf dengan satu baris kosong.')
                    ->collapsible()
                    ->schema([
                        TextInput::make('about_content.potential.title.id')
                            ->label('Judul (Bahasa Indonesia)')
                            ->placeholder('Potensi TMCC'),
                        TextInput::make('about_content.potential.title.en')
                            ->label('Title (English)')
                            ->placeholder('TMCC Potential'),
                        Textarea::make('about_content.potential.body.id')
                            ->label('Isi (Bahasa Indonesia)')
                            ->rows(6),
                        Textarea::make('about_content.potential.body.en')
                            ->label('Body (English)')
                            ->rows(6),
                    ])->columns(2),
                Section::make('Legalitas')
                    ->description('Data legalitas hanya ditampilkan ke publik setelah status verifikasi diaktifkan. Isi hanya data yang sudah diverifikasi — jangan mengisi nomor atau placeholder yang belum pasti.')
                    ->collapsible()
                    ->schema([
                        TextInput::make('about_content.legal.title.id')
                            ->label('Judul (Bahasa Indonesia)')
                            ->placeholder('Legalitas'),
                        TextInput::make('about_content.legal.title.en')
                            ->label('Title (English)')
                            ->placeholder('Legalitas'),
                        Textarea::make('about_content.legal.items.id')
                            ->label('Daftar Legalitas (Bahasa Indonesia)')
                            ->helperText('Satu item per baris. Jangan mengisi data yang belum diverifikasi.')
                            ->rows(4),
                        Textarea::make('about_content.legal.items.en')
                            ->label('Legal Items (English)')
                            ->helperText('One item per line. Do not enter unverified data.')
                            ->rows(4),
                        Toggle::make('about_legal_verified')
                            ->label('Verifikasi Data Legalitas')
                            ->helperText('Aktifkan HANYA setelah dokumen legal (SK, Akta, NPWP, dan sejenisnya) diverifikasi. Saat aktif, data legalitas dapat ditampilkan ke publik.')
                            ->default(false)
                            ->disabled(fn (): bool => ! static::canManageLegalVerification())
                            ->columnSpanFull(),
                    ])->columns(2),
                Section::make('Visi, Misi & Tujuan')
                    ->description('Visi berupa satu kalimat. Misi dan tujuan diisi satu poin per baris.')
                    ->collapsible()
                    ->schema([
                        Textarea::make('about_content.vision.id')
                            ->label('Visi (Bahasa Indonesia)')
                            ->rows(3),
                        Textarea::make('about_content.vision.en')
                            ->label('Vision (English)')
                            ->rows(3),
                        Textarea::make('about_content.mission.id')
                            ->label('Misi (Bahasa Indonesia)')
                            ->helperText('Satu poin per baris.')
                            ->rows(5),
                        Textarea::make('about_content.mission.en')
                            ->label('Mission (English)')
                            ->helperText('One point per line.')
                            ->rows(5),
                        Textarea::make('about_content.goal.id')
                            ->label('Tujuan (Bahasa Indonesia)')
                            ->helperText('Satu poin per baris.')
                            ->rows(5),
                        Textarea::make('about_content.goal.en')
                            ->label('Goal (English)')
                            ->helperText('One point per line.')
                            ->rows(5),
                    ])->columns(2),
                Section::make('Dewan Pendiri')
                    ->description('Judul dan narasi bagian Dewan Pendiri. Daftar pendiri dikelola melalui menu Pendiri.')
                    ->collapsible()
                    ->schema([
                        TextInput::make('about_content.founders.title.id')
                            ->label('Judul (Bahasa Indonesia)')
                            ->placeholder('DEWAN PENDIRI'),
                        TextInput::make('about_content.founders.title.en')
                            ->label('Title (English)')
                            ->placeholder('FOUNDING BOARD'),
                        Textarea::make('about_content.founders.narrative.id')
                            ->label('Narasi (Bahasa Indonesia)')
                            ->rows(5),
                        Textarea::make('about_content.founders.narrative.en')
                            ->label('Narrative (English)')
                            ->rows(5),
                    ])->columns(2),
                Section::make('Kontak & Media Sosial')
                    ->description('Informasi kontak dan tautan media sosial yang tampil di footer serta halaman kontak.')
                    ->schema([
                        TextInput::make('address')
                            ->label('Alamat'),
                        TextInput::make('phone')
                            ->label('Nomor Telepon')
                            ->tel(),
                        TextInput::make('whatsapp')
                            ->label('Nomor WhatsApp')
                            ->tel(),
                        TextInput::make('email')
                            ->label('Email')
                            ->email(),
                        TextInput::make('instagram_url')
                            ->label('URL Instagram')
                            ->url(),
                        TextInput::make('facebook_url')
                            ->label('URL Facebook')
                            ->url(),
                        TextInput::make('tiktok_url')
                            ->label('URL TikTok')
                            ->url(),
                        TextInput::make('twitter_url')
                            ->label('URL Twitter/X')
                            ->url(),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $settings = SiteSettings::firstOrCreate([]);
        $oldMapPath = $settings->water_quality_map_image_url;
        $this->pendingMapUploads = [];

        try {
            $state = $this->form->getState();

            if ($this->pendingMapUploads !== []) {
                $state['water_quality_map_image_url'] = $this->pendingMapUploads[array_key_last($this->pendingMapUploads)];
            }

            if (! static::canManageLegalVerification()) {
                $state['about_legal_verified'] = (bool) $settings->about_legal_verified;
            }

            DB::transaction(fn () => $settings->update($state));
        } catch (Throwable $exception) {
            foreach (array_unique($this->pendingMapUploads) as $path) {
                if ($path !== $oldMapPath) {
                    rescue(fn () => Storage::disk('public')->delete($path), report: false);
                }
            }

            $this->pendingMapUploads = [];
            $this->data['water_quality_map_image_url'] = filled($oldMapPath) ? [$oldMapPath] : null;

            throw $exception;
        }

        $newMapPath = $settings->water_quality_map_image_url;

        if (filled($oldMapPath) && $oldMapPath !== $newMapPath) {
            rescue(fn () => Storage::disk('public')->delete($oldMapPath), report: false);
        }

        $this->pendingMapUploads = [];

        Notification::make()
            ->success()
            ->title('Pengaturan disimpan')
            ->body('Perubahan pengaturan situs berhasil disimpan.')
            ->send();
    }

    public static function canManageLegalVerification(): bool
    {
        $user = auth()->user();

        if (! $user instanceof FilamentUser) {
            return false;
        }

        $panel = Filament::getCurrentPanel() ?? Filament::getPanel('admin');

        return $panel !== null && $user->canAccessPanel($panel);
    }

    /**
     * Ensure the admin form always receives a complete localized structure,
     * even when `about_content` is empty or partially stored.
     *
     * @return array<string, mixed>
     */
    private function normalizeAboutContent(mixed $content): array
    {
        $content = is_array($content) ? $content : [];

        $field = function (string $path) use ($content): array {
            $value = data_get($content, $path);
            $id = is_array($value) ? ($value['id'] ?? '') : '';
            $en = is_array($value) ? ($value['en'] ?? '') : '';

            return [
                'id' => is_string($id) ? $id : '',
                'en' => is_string($en) ? $en : '',
            ];
        };

        return [
            'hero' => [
                'eyebrow' => $field('hero.eyebrow'),
                'title' => $field('hero.title'),
                'subtitle' => $field('hero.subtitle'),
            ],
            'lake' => [
                'title' => $field('lake.title'),
                'body' => $field('lake.body'),
            ],
            'profile' => [
                'title' => $field('profile.title'),
                'body' => $field('profile.body'),
            ],
            'potential' => [
                'title' => $field('potential.title'),
                'body' => $field('potential.body'),
            ],
            'legal' => [
                'title' => $field('legal.title'),
                'items' => $field('legal.items'),
            ],
            'vision' => $field('vision'),
            'mission' => $field('mission'),
            'goal' => $field('goal'),
            'founders' => [
                'title' => $field('founders.title'),
                'narrative' => $field('founders.narrative'),
            ],
        ];
    }
}
