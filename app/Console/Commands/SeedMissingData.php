<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Models\BlogPost;
use App\Models\Member;
use App\Models\Program;
use App\Models\User;
use App\Models\WaterQualityRecord;
use App\Models\WaterSamplingPoint;
use Illuminate\Console\Command;

class SeedMissingData extends Command
{
    protected $signature = 'tmcc:seed-missing';

    protected $description = 'Seed missing dummy data for TMCC demo';

    public function handle(): void
    {
        $this->seedBaselineBlogPosts();
        $this->seedMoreBlogPosts();
        $this->seedMoreActivities();
        $this->seedMoreWaterRecords();

        $this->info('Done. Summary:');
        $this->table(['Model', 'Count'], [
            ['BlogPost', BlogPost::count()],
            ['BlogPost BASELINE_STUDY', BlogPost::where('category', 'BASELINE_STUDY')->count()],
            ['Activity', Activity::count()],
            ['WaterQualityRecord', WaterQualityRecord::count()],
            ['WaterSamplingPoint', WaterSamplingPoint::count()],
            ['Member', Member::count()],
        ]);
    }

    private function seedBaselineBlogPosts(): void
    {
        $posts = [
            [
                'title' => 'Baseline Study Kualitas Air Danau Matano 2024',
                'slug' => 'baseline-study-kualitas-air-2024',
                'category' => 'BASELINE_STUDY',
                'cover_image_url' => '/image/logo_tova_matano.png',
                'content' => '<h2>Ringkasan Eksekutif</h2><p>Studi dasar komprehensif kondisi kualitas air Danau Matano selama 12 bulan (Januari–Desember 2024) di 6 titik pengambilan sampel. Hasil studi ini menjadi acuan program pemantauan TMCC 2025–2027.</p><h3>Parameter yang Diukur</h3><ul><li>pH air (target: 6.5 – 7.5)</li><li>Kejernihan Secchi (target: &gt;400 cm)</li><li>Dissolved oxygen (DO)</li><li>Kadar nitrat dan fosfat</li></ul><p>Secara umum, kondisi kualitas air Danau Matano masih dalam batas normal ekosistem danau purba, namun terdapat indikasi tekanan antropogenik yang perlu dipantau secara berkelanjutan.</p>',
                'published_at' => now()->subDays(180),
                'is_published' => true,
            ],
            [
                'title' => 'Studi Keanekaragaman Hayati Danau Matano — Laporan Awal 2025',
                'slug' => 'studi-keanekaragaman-hayati-2025',
                'category' => 'BASELINE_STUDY',
                'cover_image_url' => '/image/logo_tova_matano.png',
                'content' => '<h2>Pendahuluan</h2><p>Laporan awal survei biodiversitas Danau Matano yang dilaksanakan pada Maret–Juni 2025, mencakup inventarisasi ikan, moluska, tumbuhan air, dan plankton sebagai indikator kesehatan ekosistem danau.</p><h3>Temuan Awal</h3><ul><li>34 spesies ikan teridentifikasi, 28 di antaranya endemik</li><li>9 spesies moluska endemik terdata</li><li>47 spesies tumbuhan air, 12 spesies baru bagi database nasional KLHK</li></ul>',
                'published_at' => now()->subDays(120),
                'is_published' => true,
            ],
            [
                'title' => 'Pemetaan Titik Rawan Sedimentasi Danau Matano',
                'slug' => 'pemetaan-sedimentasi-danau-matano',
                'category' => 'BASELINE_STUDY',
                'cover_image_url' => '/image/logo_tova_matano.png',
                'content' => '<h2>Metodologi</h2><p>Pemetaan dilakukan menggunakan kombinasi survei batimetri, pengambilan sampel sedimen, dan analisis citra satelit Sentinel-2 untuk mengidentifikasi area-area dengan tingkat sedimentasi tinggi di Danau Matano dan sekitarnya.</p><h3>Hasil Pemetaan</h3><ul><li>5 titik rawan sedimentasi di sekitar muara sungai</li><li>Area delta Sungai Petea mengalami akumulasi sedimen tertinggi</li><li>Rekomendasi: reboisasi buffer zone di 3 sub-DAS prioritas</li></ul>',
                'published_at' => now()->subDays(90),
                'is_published' => true,
            ],
        ];

        foreach ($posts as $post) {
            BlogPost::firstOrCreate(['slug' => $post['slug']], $post);
        }

        $this->line('Baseline Study posts seeded.');
    }

    private function seedMoreBlogPosts(): void
    {
        $posts = [
            [
                'title' => 'Mengenal Ikan Endemik Danau Matano',
                'slug' => 'mengenal-ikan-endemik-danau-matano',
                'category' => 'EDUKASI',
                'cover_image_url' => '/image/logo_tova_matano.png',
                'content' => '<h2>Keanekaragaman Hayati yang Unik</h2><p>Danau Matano menjadi habitat bagi sejumlah spesies ikan yang hanya dapat ditemukan di danau ini. Beberapa di antaranya adalah Butini (<em>Glossogobius matanensis</em>), Wowon (<em>Telmatherina prognatha</em>), dan Opudi (<em>Telmatherina antoniae</em>).</p>',
                'published_at' => now()->subDays(30),
                'is_published' => true,
            ],
            [
                'title' => 'TMCC Tandatangani MoU dengan BRIN untuk Riset Danau Matano',
                'slug' => 'tmcc-mou-brin-riset-danau-matano',
                'category' => 'RILIS_PERS',
                'cover_image_url' => '/image/logo_tova_matano.png',
                'content' => '<h2>Kolaborasi Riset Nasional</h2><p>TMCC dan Badan Riset dan Inovasi Nasional (BRIN) resmi menandatangani MoU untuk kolaborasi riset terpadu Danau Matano selama 3 tahun ke depan, mencakup pemantauan biodiversitas, studi kualitas air, dan model prediksi ekosistem.</p>',
                'published_at' => now()->subDays(40),
                'is_published' => true,
            ],
            [
                'title' => '200 Relawan Bergabung di Aksi Bersih Danau Matano 2026',
                'slug' => '200-relawan-aksi-bersih-danau-2026',
                'category' => 'BERITA',
                'cover_image_url' => '/image/logo_tova_matano.png',
                'content' => '<h2>Antusiasme Luar Biasa dari Masyarakat</h2><p>Aksi Bersih Danau Matano 2026 berhasil menggerakkan lebih dari 200 relawan dari berbagai elemen masyarakat. Total sampah yang berhasil dikumpulkan mencapai 1,2 ton dalam satu hari kegiatan.</p>',
                'published_at' => now()->subDays(10),
                'is_published' => true,
            ],
        ];

        foreach ($posts as $post) {
            BlogPost::firstOrCreate(['slug' => $post['slug']], $post);
        }

        $this->line('Additional blog posts seeded.');
    }

    private function seedMoreActivities(): void
    {
        $p1 = Program::where('slug', 'tova-water-patrol')->first();
        $p2 = Program::where('slug', 'eco-education')->first();
        $p3 = Program::where('slug', 'clean-lake-action')->first();
        $p4 = Program::where('slug', 'biodiversity-watch')->first();

        $activities = [
            [
                'program_id' => $p1?->id,
                'title' => 'Pemantauan Kualitas Air Bulanan - Agustus 2026',
                'slug' => 'pemantauan-kualitas-air-agustus-2026',
                'cover_image_url' => '/image/logo_tova_matano.png',
                'content' => '<h3>Laporan Agustus 2026</h3><p>Pengukuran bulan Agustus menunjukkan kondisi yang stabil dengan sedikit penurunan kejernihan akibat musim kering berkepanjangan. pH rata-rata: 7.1, Kejernihan: 430 cm.</p>',
                'published_at' => now()->subDays(35),
                'is_published' => true,
            ],
            [
                'program_id' => $p1?->id,
                'title' => 'Pelatihan Relawan Water Patrol Batch 5',
                'slug' => 'pelatihan-relawan-water-patrol-batch-5',
                'cover_image_url' => '/image/logo_tova_matano.png',
                'content' => '<h3>Pelatihan Teknis Pengukuran Kualitas Air</h3><p>TMCC menyelenggarakan pelatihan intensif 3 hari bagi 25 relawan baru Water Patrol. Materi: teknik pengambilan sampel, penggunaan alat ukur pH dan turbiditas, serta protokol keselamatan di perairan.</p>',
                'published_at' => now()->subDays(50),
                'is_published' => true,
            ],
            [
                'program_id' => $p2?->id,
                'title' => 'Workshop Guru: Integrasi Materi Lingkungan ke Kurikulum',
                'slug' => 'workshop-guru-integrasi-materi-lingkungan',
                'cover_image_url' => '/image/logo_tova_matano.png',
                'content' => '<h3>Meningkatkan Kapasitas Guru</h3><p>Workshop dihadiri 30 guru dari 8 sekolah di Kecamatan Nuha dan Malili. Tujuan: membantu guru mengintegrasikan materi pelestarian Danau Matano ke dalam pembelajaran IPA dan IPS.</p>',
                'published_at' => now()->subDays(45),
                'is_published' => true,
            ],
            [
                'program_id' => $p3?->id,
                'title' => 'Pemasangan Jaring Sampah di Muara Sungai Petea',
                'slug' => 'pemasangan-jaring-sampah-muara-petea',
                'cover_image_url' => '/image/logo_tova_matano.png',
                'content' => '<h3>Inovasi Penangkapan Sampah</h3><p>TMCC bekerja sama dengan Dinas Lingkungan Hidup Luwu Timur memasang 3 unit jaring sampah di muara Sungai Petea. Sistem ini mengurangi masuknya sampah ke danau hingga 60% dalam uji coba pertama.</p>',
                'published_at' => now()->subDays(60),
                'is_published' => true,
            ],
            [
                'program_id' => $p4?->id,
                'title' => 'Survei Populasi Ikan Butini di Danau Matano',
                'slug' => 'survei-populasi-ikan-butini-2026',
                'cover_image_url' => '/image/logo_tova_matano.png',
                'content' => '<h3>Ikan Butini: Endemik yang Terancam</h3><p>Tim Biodiversity Watch bersama peneliti BRIN melakukan survei populasi ikan Butini. Estimasi populasi: 2.400–3.100 individu (turun 15% dari 2024). Ancaman terbesar: penurunan kejernihan air.</p>',
                'published_at' => now()->subDays(18),
                'is_published' => true,
            ],
            [
                'program_id' => $p4?->id,
                'title' => 'Dokumentasi Flora Air Danau Matano 2026',
                'slug' => 'dokumentasi-flora-air-danau-matano-2026',
                'cover_image_url' => '/image/logo_tova_matano.png',
                'content' => '<h3>Kekayaan Tumbuhan Air Danau Matano</h3><p>Tim relawan mengidentifikasi 47 spesies tumbuhan air di Danau Matano, 12 di antaranya merupakan spesies endemik yang belum pernah tercatat di database nasional KLHK.</p>',
                'published_at' => now()->subDays(55),
                'is_published' => true,
            ],
        ];

        foreach ($activities as $activity) {
            Activity::firstOrCreate(['slug' => $activity['slug']], $activity);
        }

        $this->line('Additional activities seeded.');
    }

    private function seedMoreWaterRecords(): void
    {
        $samplingPoints = WaterSamplingPoint::all();

        if ($samplingPoints->isEmpty()) {
            $this->warn('No sampling points found, skipping water records.');

            return;
        }

        $adminId = User::first()?->id;

        // Only add more records if we have less than 30
        if (WaterQualityRecord::count() >= 30) {
            $this->line('Water records already sufficient.');

            return;
        }

        $baseData = [
            0 => ['ph' => 7.1, 'clarity' => 420],
            1 => ['ph' => 7.3, 'clarity' => 480],
            2 => ['ph' => 7.0, 'clarity' => 410],
            3 => ['ph' => 7.2, 'clarity' => 460],
            4 => ['ph' => 7.1, 'clarity' => 430],
            5 => ['ph' => 7.4, 'clarity' => 490],
        ];

        $notePool = [
            'Kondisi normal, cuaca cerah saat pengambilan sampel.',
            'Hujan lebat 2 hari sebelumnya, sedikit penurunan kejernihan.',
            'Kondisi sangat baik, musim kering mendukung kejernihan tinggi.',
            null,
            'Terdeteksi sedikit peningkatan sedimentasi di area muara.',
            'Suhu air permukaan lebih tinggi dari rata-rata musiman.',
            null,
            'Pengukuran dilakukan pagi hari, kondisi optimal.',
        ];

        $monthsAgo = [90, 75, 60, 45, 30, 20, 15, 10, 5, 2];

        foreach ($monthsAgo as $idx => $daysAgo) {
            foreach ($samplingPoints as $ptIdx => $point) {
                $base = $baseData[$ptIdx % 6];
                $phVar = (($idx + $ptIdx) % 3 - 1) * 0.1;
                $clarVar = (($idx * 2 + $ptIdx) % 5 - 2) * 10;

                WaterQualityRecord::firstOrCreate(
                    [
                        'water_sampling_point_id' => $point->id,
                        'recorded_at' => now()->subDays($daysAgo + $ptIdx)->toDateString(),
                    ],
                    [
                        'ph' => round($base['ph'] + $phVar, 1),
                        'clarity_cm' => $base['clarity'] + $clarVar,
                        'notes' => $notePool[($idx + $ptIdx) % count($notePool)],
                        'recorded_by' => $adminId,
                    ]
                );
            }
        }

        $this->line('Water quality records seeded.');
    }
}
