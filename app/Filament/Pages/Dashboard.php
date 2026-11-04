<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Activities\ActivityResource;
use App\Filament\Resources\BlogPosts\BlogPostResource;
use App\Filament\Resources\Volunteers\VolunteerResource;
use App\Filament\Resources\WaterQualityRecords\WaterQualityRecordResource;
use App\Filament\Resources\WaterSamplingPoints\WaterSamplingPointResource;
use App\Models\Activity;
use App\Models\BlogPost;
use App\Models\Member;
use App\Models\Program;
use App\Models\Volunteer;
use App\Models\WaterQualityRecord;
use App\Models\WaterSamplingPoint;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Dasbor';

    protected static ?string $navigationLabel = 'Dasbor';

    protected static ?int $navigationSort = -100;

    protected string $view = 'filament.pages.dashboard';

    public function getSubheading(): string|Htmlable|null
    {
        return 'Ringkasan operasional Tova Matano Care Center · '.now()->translatedFormat('l, j F Y');
    }

    protected function getViewData(): array
    {
        $pendingVolunteers = Volunteer::query()
            ->where('status', 'PENDING')
            ->count();

        $publishedPosts = BlogPost::query()
            ->where('is_published', true)
            ->count();

        $activeSamplingPoints = WaterSamplingPoint::query()
            ->where('is_active', true)
            ->count();

        $totalSamplingPoints = WaterSamplingPoint::query()->count();

        return [
            'stats' => [
                [
                    'label' => 'Total Anggota',
                    'value' => Member::query()->count(),
                    'hint' => 'Terdaftar di portal keanggotaan',
                    'icon' => Heroicon::OutlinedUsers,
                ],
                [
                    'label' => 'Total Relawan',
                    'value' => Volunteer::query()->count(),
                    'hint' => $pendingVolunteers.' menunggu persetujuan',
                    'icon' => Heroicon::OutlinedUserGroup,
                    'url' => VolunteerResource::getUrl('index'),
                ],
                [
                    'label' => 'Relawan Menunggu',
                    'value' => $pendingVolunteers,
                    'hint' => 'Perlu ditinjau',
                    'icon' => Heroicon::OutlinedClock,
                    'url' => VolunteerResource::getUrl('index'),
                    'tone' => $pendingVolunteers > 0 ? 'warning' : 'success',
                ],
                [
                    'label' => 'Total Program',
                    'value' => Program::query()->count(),
                    'hint' => 'Program lingkungan aktif',
                    'icon' => Heroicon::OutlinedSquares2x2,
                ],
                [
                    'label' => 'Total Kegiatan',
                    'value' => Activity::query()->count(),
                    'hint' => 'Kegiatan terdokumentasi',
                    'icon' => Heroicon::OutlinedCalendarDays,
                    'url' => ActivityResource::getUrl('index'),
                ],
                [
                    'label' => 'Total Artikel',
                    'value' => BlogPost::query()->count(),
                    'hint' => $publishedPosts.' terbit',
                    'icon' => Heroicon::OutlinedNewspaper,
                    'url' => BlogPostResource::getUrl('index'),
                ],
                [
                    'label' => 'Data Kualitas Air',
                    'value' => WaterQualityRecord::query()->count(),
                    'hint' => $activeSamplingPoints.' dari '.$totalSamplingPoints.' titik aktif',
                    'icon' => Heroicon::OutlinedBeaker,
                    'url' => WaterQualityRecordResource::getUrl('index'),
                ],
            ],

            'latestVolunteers' => Volunteer::query()
                ->latest('submitted_at')
                ->limit(5)
                ->get(),

            'latestBlogPosts' => BlogPost::query()
                ->orderByDesc('published_at')
                ->limit(5)
                ->get(),

            'latestWaterQuality' => WaterQualityRecord::query()
                ->with('samplingPoint')
                ->latest('recorded_at')
                ->limit(5)
                ->get(),

            'attentionItems' => collect([
                [
                    'label' => $pendingVolunteers.' pendaftaran relawan menunggu persetujuan',
                    'url' => VolunteerResource::getUrl('index'),
                    'tone' => 'warning',
                ],
                [
                    'label' => (BlogPost::query()->where('is_published', false)->count()).' artikel masih berupa draf',
                    'url' => BlogPostResource::getUrl('index'),
                    'tone' => 'info',
                ],
                [
                    'label' => ($totalSamplingPoints - $activeSamplingPoints).' titik sampling nonaktif',
                    'url' => WaterSamplingPointResource::getUrl('index'),
                    'tone' => 'muted',
                ],
            ])->filter(fn (array $item): bool => ! str_starts_with($item['label'], '0 '))->values(),

            'quickActions' => [
                [
                    'label' => 'Tulis Artikel',
                    'description' => 'Bagikan berita dan edukasi',
                    'icon' => Heroicon::OutlinedNewspaper,
                    'url' => BlogPostResource::getUrl('create'),
                ],
                [
                    'label' => 'Tambah Kegiatan',
                    'description' => 'Dokumentasikan kegiatan terbaru',
                    'icon' => Heroicon::OutlinedCalendarDays,
                    'url' => ActivityResource::getUrl('create'),
                ],
                [
                    'label' => 'Catat Kualitas Air',
                    'description' => 'Input hasil pengukuran',
                    'icon' => Heroicon::OutlinedBeaker,
                    'url' => WaterQualityRecordResource::getUrl('create'),
                ],
                [
                    'label' => 'Tambah Relawan',
                    'description' => 'Daftarkan relawan baru',
                    'icon' => Heroicon::OutlinedUserPlus,
                    'url' => VolunteerResource::getUrl('create'),
                ],
            ],
        ];
    }
}
