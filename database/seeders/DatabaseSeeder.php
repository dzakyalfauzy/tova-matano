<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\BlogPost;
use App\Models\DonationChannel;
use App\Models\Founder;
use App\Models\GalleryItem;
use App\Models\OrgCategory;
use App\Models\OrgPerson;
use App\Models\Program;
use App\Models\WaterQualityRecord;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@tovamatano.org'],
            ['name' => 'Admin TMCC', 'password' => bcrypt('password123')]
        );

        $this->seedFounders();
        $programs = $this->seedPrograms();
        $this->seedActivities($programs);
        $this->seedOrgStructure();
        $this->seedDonationChannels();
        $this->seedBlogPosts();
        $this->seedGallery();
        $this->seedWaterQuality($admin->id);
    }

    private function seedFounders(): void
    {
        if (Founder::count() > 0) return;
        $founders = [
            ['name' => 'Dr. Ir. Tova Matano, M.Sc.', 'role' => 'Direktur Utama TMCC', 'org_name' => null, 'order' => 1],
            ['name' => 'Dr. Bambang Suharto', 'role' => 'Direktur Riset & Konservasi', 'org_name' => 'Universitas Hasanuddin', 'order' => 2],
            ['name' => 'Rina Kusuma, S.H.', 'role' => 'Direktur Eksekutif', 'org_name' => null, 'order' => 3],
        ];
        foreach ($founders as $f) Founder::create($f);
    }

    private function seedPrograms(): array
    {
        $programs = [];
        $data = [
            ['slug' => 'tova-water-patrol', 'name' => 'Tova Water Patrol', 'description' => 'Program pemantauan kualitas air Danau Matano secara berkala oleh relawan terlatih. Data yang terkumpul menjadi dasar advokasi kebijakan perlindungan danau.', 'order' => 1],
            ['slug' => 'eco-education', 'name' => 'Eco Education', 'description' => 'Program edukasi lingkungan hidup bagi siswa SD/SMP di sekitar Danau Matano. Materi mencakup ekosistem danau, daur air, dan aksi konservasi sederhana.', 'order' => 2],
            ['slug' => 'clean-lake-action', 'name' => 'Clean Lake Action', 'description' => 'Aksi bersih-bersih Danau Matano dan area resapan air. Melibatkan relawan masyarakat, pelajar, dan pemerintah daerah secara gotong royong.', 'order' => 3],
            ['slug' => 'biodiversity-watch', 'name' => 'Biodiversity Watch', 'description' => 'Pemantauan keanekaragaman hayati endemik Danau Matano termasuk ikan, moluska, dan tumbuhan air. Berkontribusi pada data konservasi nasional.', 'order' => 4],
        ];
        foreach ($data as $d) {
            $programs[$d['slug']] = Program::firstOrCreate(['slug' => $d['slug']], $d);
        }
        return $programs;
    }

    private function seedActivities(array $programs): void
    {
        $p1 = $programs['tova-water-patrol'] ?? Program::where('slug', 'tova-water-patrol')->first();
        $p2 = $programs['eco-education'] ?? Program::where('slug', 'eco-education')->first();
        $p3 = $programs['clean-lake-action'] ?? Program::where('slug', 'clean-lake-action')->first();

        $activities = [
            [
                'program_id' => $p1?->id, 'title' => 'Pemantauan Kualitas Air Bulanan - September 2026',
                'slug' => 'pemantauan-kualitas-air-september-2026', 'cover_image_url' => '/image/logo_tova_matano.png',
                'content' => '<h3>Hasil Pemantauan</h3><p>Tim Water Patrol melakukan pengukuran di 5 titik sampling Danau Matano pada 15 September 2026.</p><h3>Temuan Utama</h3><ul><li>pH rata-rata: 7.2 (normal)</li><li>Kejernihan: 4.5 meter (baik)</li><li>Indikasi peningkatan sedimentasi di area muara</li></ul>',
                'published_at' => now()->subDays(5), 'is_published' => true,
            ],
            [
                'program_id' => $p2?->id, 'title' => 'Kelas Lingkungan di SDN 01 Sorowako',
                'slug' => 'kelas-lingkungan-sdn01-sorowako', 'cover_image_url' => '/image/logo_tova_matano.png',
                'content' => '<h3>Edukasi untuk Generasi Muda</h3><p>Tim Eco Education mengadakan kelas interaktif bagi 45 siswa kelas 4 dan 5 SDN 01 Sorowako.</p><ul><li>Pengenalan ekosistem Danau Matano</li><li>Peran ikan endemik dalam rantai makanan</li><li>Cara sederhana menjaga kebersihan danau</li></ul>',
                'published_at' => now()->subDays(12), 'is_published' => true,
            ],
            [
                'program_id' => $p3?->id, 'title' => 'Aksi Bersih Danau Matano - Hari Sungai Nasional',
                'slug' => 'aksi-bersih-danau-hari-sungai-nasional', 'cover_image_url' => '/image/logo_tova_matano.png',
                'content' => '<h3>Gotong Royong Bersihkan Danau</h3><p>120 relawan berpartisipasi dalam aksi bersih-bersih di sepanjang tepian Danau Matano.</p><h3>Hasil</h3><ul><li>0.8 ton sampah plastik terkumpul</li><li>3 titik hotspot pembuangan sampah liar teridentifikasi</li></ul>',
                'published_at' => now()->subDays(20), 'is_published' => true,
            ],
        ];
        foreach ($activities as $a) {
            Activity::firstOrCreate(['slug' => $a['slug']], $a);
        }
    }

    private function seedOrgStructure(): void
    {
        if (OrgCategory::count() > 0) return;

        $categories = [
            ['name' => 'Pelindung', 'description' => 'Instansi yang memberikan dukungan kelembagaan', 'display_type' => 'ROLE_LIST', 'order' => 1],
            ['name' => 'Penasehat', 'description' => 'Ahli yang memberikan masukan strategis', 'display_type' => 'ROLE_LIST', 'order' => 2],
            ['name' => 'Dewan Pakar', 'description' => 'Tim ahli bidang lingkungan dan konservasi', 'display_type' => 'PERSON_GRID', 'order' => 3],
            ['name' => 'Badan Eksekutif', 'description' => 'Pengelola operasional harian TMCC', 'display_type' => 'PERSON_GRID', 'order' => 4],
            ['name' => 'Pengurus Inti', 'description' => 'Struktur kepengurusan inti organisasi', 'display_type' => 'PERSON_GRID', 'order' => 5],
            ['name' => 'Divisi', 'description' => 'Unit kerja fungsional', 'display_type' => 'PERSON_GRID', 'order' => 6],
        ];

        $orgPeople = [
            ['cat' => 'Pelindung', 'name' => null, 'role_title' => 'Gubernur Sulawesi Selatan', 'order' => 1],
            ['cat' => 'Pelindung', 'name' => null, 'role_title' => 'Bupati Luwu Timur', 'order' => 2],
            ['cat' => 'Penasehat', 'name' => 'Prof. Dr. Arifin', 'role_title' => 'Guru Besar Limnologi', 'order' => 1],
            ['cat' => 'Penasehat', 'name' => 'Dr. Sri Wahyuni', 'role_title' => 'Peneliti BRIN', 'order' => 2],
            ['cat' => 'Dewan Pakar', 'name' => 'Dr. Hendra Kurniawan', 'role_title' => 'Ahli Ekologi Perairan', 'order' => 1],
            ['cat' => 'Dewan Pakar', 'name' => 'Dr. Mayang Sari', 'role_title' => 'Ahli Konservasi', 'order' => 2],
            ['cat' => 'Badan Eksekutif', 'name' => 'Rina Kusuma, S.H.', 'role_title' => 'Sekretaris Jenderal', 'order' => 1],
            ['cat' => 'Badan Eksekutif', 'name' => 'Ahmad Fadli, S.E.', 'role_title' => 'Bendahara Umum', 'order' => 2],
            ['cat' => 'Badan Eksekutif', 'name' => 'Dewi Lestari, S.Pi.', 'role_title' => 'Kepala Divisi Riset', 'order' => 3],
            ['cat' => 'Pengurus Inti', 'name' => 'Budi Santoso', 'role_title' => 'Koordinator Lapangan', 'order' => 1],
            ['cat' => 'Pengurus Inti', 'name' => 'Siti Rahmawati', 'role_title' => 'Koordinator Relawan', 'order' => 2],
            ['cat' => 'Divisi', 'name' => 'Yusuf Maulana', 'role_title' => 'Kepala Divisi', 'divisi_name' => 'Divisi Riset & Pemantauan', 'order' => 1],
            ['cat' => 'Divisi', 'name' => 'Nina Aprilia', 'role_title' => 'Kepala Divisi', 'divisi_name' => 'Divisi Edukasi & Sosialisasi', 'order' => 2],
            ['cat' => 'Divisi', 'name' => 'Rico Pratama', 'role_title' => 'Kepala Divisi', 'divisi_name' => 'Divisi Aksi Lapangan', 'order' => 3],
        ];

        foreach ($categories as $catData) {
            $cat = OrgCategory::create($catData);
            foreach ($orgPeople as $p) {
                if ($p['cat'] === $catData['name']) {
                    $cat->people()->create([
                        'name'       => $p['name'],
                        'role_title' => $p['role_title'],
                        'divisi_name'=> $p['divisi_name'] ?? null,
                        'order'      => $p['order'],
                    ]);
                }
            }
        }
    }

    private function seedDonationChannels(): void
    {
        if (DonationChannel::count() > 0) return;
        $channels = [
            ['bank_name' => 'Bank Mandiri', 'account_no' => '170-00-1922419-9', 'account_name' => 'Yayasan TMCC'],
            ['bank_name' => 'Bank BRI', 'account_no' => '4567-01-0234567-8', 'account_name' => 'Yayasan TMCC'],
            ['bank_name' => 'Bank BCA', 'account_no' => '8901-234-567', 'account_name' => 'Yayasan TMCC'],
        ];
        foreach ($channels as $ch) DonationChannel::create($ch);
    }

    private function seedBlogPosts(): void
    {
        if (BlogPost::count() > 0) return;
        $posts = [
            [
                'title' => 'Mengapa Danau Matano Perlu Dilindungi?', 'slug' => 'mengapa-danau-matano-perlu-dilindungi',
                'category' => 'EDUKASI', 'cover_image_url' => '/image/logo_tova_matano.png',
                'content' => '<h2>Permata Toba di Sulawesi</h2><p>Danau Matano adalah danau tectonic tertua di Asia Tenggara, dengan kedalaman mencapai 590 meter. Danau ini menjadi rumah bagi lebih dari 30 spesies ikan endemik.</p><h3>Ancaman yang Dihadapi</h3><ul><li>Pencemaran limbah pertambangan nikel</li><li>Sedimentasi akibat deforestasi</li><li>Eksploitasi berlebihan oleh nelayan tradisional</li></ul>',
                'published_at' => now()->subDays(7), 'is_published' => true,
            ],
            [
                'title' => 'TMCC Raih Penghargaan Lingkungan Tingkat Nasional', 'slug' => 'tmcc-raih-penghargaan-lingkungan',
                'category' => 'RILIS_PERS', 'cover_image_url' => '/image/logo_tova_matano.png',
                'content' => '<h2>Penghargaan Kalpataru 2026</h2><p>Tova Matano Care Center (TMCC) menerima penghargaan Kalpataru kategori Pengabdian di bidang konservasi sumber daya air pada acara Hari Lingkungan Hidup Sedunia 2026.</p>',
                'published_at' => now()->subDays(15), 'is_published' => true,
            ],
            [
                'title' => 'Revisi UU Cipta Kerja: Dampak terhadap Perlindungan Danau', 'slug' => 'revisi-uu-cipta-kerja-dampak-danau',
                'category' => 'KEBIJAKAN', 'cover_image_url' => '/image/logo_tova_matano.png',
                'content' => '<h2>Analisis Kebijakan</h2><p>Revisi Undang-Undang Cipta Kerja membawa perubahan signifikan dalam tata kelola lingkungan hidup di Indonesia.</p><h3>Poin Penting</h3><ul><li>Perubahan mekanisme AMDAL</li><li>Dampak terhadap zona penyangga danau</li></ul>',
                'published_at' => now()->subDays(25), 'is_published' => true,
            ],
        ];
        foreach ($posts as $p) BlogPost::create($p);
    }

    private function seedGallery(): void
    {
        if (GalleryItem::count() > 0) return;
        $items = [
            ['type' => 'PHOTO', 'url' => '/image/logo_tova_matano.png', 'caption' => 'Suasana kegiatan Water Patrol di Danau Matano'],
            ['type' => 'PHOTO', 'url' => '/image/logo_tova_matano.png', 'caption' => 'Eco Education di SDN 01 Sorowako'],
            ['type' => 'PHOTO', 'url' => '/image/logo_tova_matano.png', 'caption' => 'Aksi bersih tepian Danau Matano'],
            ['type' => 'PHOTO', 'url' => '/image/logo_tova_matano.png', 'caption' => 'Tim relawan TMCC'],
            ['type' => 'PHOTO', 'url' => '/image/logo_tova_matano.png', 'caption' => 'Pengukuran kualitas air'],
            ['type' => 'PHOTO', 'url' => '/image/logo_tova_matano.png', 'caption' => 'Keanekaragaman hayati endemik'],
            ['type' => 'PHOTO', 'url' => '/image/logo_tova_matano.png', 'caption' => 'Workshop konservasi danau'],
            ['type' => 'PHOTO', 'url' => '/image/logo_tova_matano.png', 'caption' => 'Pemandangan Danau Matano dari ketinggian'],
        ];
        foreach ($items as $item) GalleryItem::create($item);
    }

    private function seedWaterQuality(int $userId): void
    {
        if (WaterQualityRecord::count() > 0) return;
        $records = [
            ['ph' => 7.1, 'clarity_cm' => 420, 'days_ago' => 90, 'notes' => 'Kondisi normal, sedikit endapan di muara'],
            ['ph' => 7.2, 'clarity_cm' => 450, 'days_ago' => 75, 'notes' => null],
            ['ph' => 7.0, 'clarity_cm' => 430, 'days_ago' => 60, 'notes' => 'Hujan lebat sehari sebelumnya'],
            ['ph' => 7.3, 'clarity_cm' => 460, 'days_ago' => 45, 'notes' => null],
            ['ph' => 6.9, 'clarity_cm' => 410, 'days_ago' => 30, 'notes' => 'Indikasi sedimentasi di titik 3'],
            ['ph' => 7.1, 'clarity_cm' => 440, 'days_ago' => 25, 'notes' => null],
            ['ph' => 7.2, 'clarity_cm' => 455, 'days_ago' => 20, 'notes' => 'Kondisi sangat baik'],
            ['ph' => 7.0, 'clarity_cm' => 435, 'days_ago' => 15, 'notes' => null],
            ['ph' => 7.1, 'clarity_cm' => 445, 'days_ago' => 10, 'notes' => null],
            ['ph' => 7.2, 'clarity_cm' => 450, 'days_ago' => 5,  'notes' => 'Musim kering, kejernihan meningkat'],
            ['ph' => 7.1, 'clarity_cm' => 448, 'days_ago' => 2,  'notes' => null],
        ];
        foreach ($records as $r) {
            WaterQualityRecord::create([
                'recorded_at' => now()->subDays($r['days_ago']),
                'ph'          => $r['ph'],
                'clarity_cm'  => $r['clarity_cm'],
                'notes'       => $r['notes'],
                'recorded_by' => $userId,
            ]);
        }
    }
}
