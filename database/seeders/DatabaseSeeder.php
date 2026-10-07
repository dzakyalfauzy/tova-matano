<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\BlogPost;
use App\Models\DonationChannel;
use App\Models\Founder;
use App\Models\GalleryItem;
use App\Models\HeroSlide;
use App\Models\Member;
use App\Models\OrgCategory;
use App\Models\Partnership;
use App\Models\Program;
use App\Models\RunningText;
use App\Models\SiteSettings;
use App\Models\User;
use App\Models\Volunteer;
use App\Models\WaterQualityRecord;
use App\Models\WaterSamplingPoint;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@tovamatano.org'],
            ['name' => 'Admin TMCC', 'password' => bcrypt('password123')]
        );

        $this->seedSiteSettings();
        $this->seedHeroSlides();
        $this->seedRunningTexts();
        $this->seedFounders();
        $programs = $this->seedPrograms();
        $this->seedActivities($programs);
        $this->seedOrgStructure();
        $this->seedDonationChannels();
        $this->seedBlogPosts();
        $this->seedGallery();
        $this->seedMembers();
        $this->seedVolunteers();
        $this->seedPartnerships();
        $samplingPoints = $this->seedWaterSamplingPoints();
        $this->seedWaterQuality($admin->id, $samplingPoints);
    }

    private function seedSiteSettings(): void
    {
        SiteSettings::firstOrCreate([], [
            'address' => 'Jl. Poros Sorowako No. 12, Malili, Luwu Timur, Sulawesi Selatan 92984',
            'phone' => '+62 451 123456',
            'whatsapp' => '+6281234567890',
            'email' => 'info@tovamatano.org',
            'instagram_url' => 'https://instagram.com/tovamatano',
            'facebook_url' => 'https://facebook.com/tovamatano',
            'tiktok_url' => 'https://tiktok.com/@tovamatano',
            'twitter_url' => 'https://twitter.com/tovamatano',
            'stat_sampah_terangkut' => '2,4 Ton',
            'stat_sekolah_teredukasi' => '12',
        ]);
    }

    private function seedHeroSlides(): void
    {
        if (HeroSlide::count() > 0) {
            return;
        }

        $slides = [
            [
                'image_url' => '/image/logo_tova_matano.png',
                'cta_url' => '/program',
                'order' => 1,
                'is_active' => true,
                'id_title' => 'Jaga Danau Matano, Jaga Kehidupan',
                'id_subtitle' => 'Bergabunglah bersama kami dalam misi pelestarian danau tektonik terdalam di Indonesia.',
                'id_cta' => 'Lihat Program Kami',
                'en_title' => 'Protect Lake Matano, Protect Life',
                'en_subtitle' => 'Join us in the mission to preserve the deepest tectonic lake in Indonesia.',
                'en_cta' => 'See Our Programs',
            ],
            [
                'image_url' => '/image/logo_tova_matano.png',
                'cta_url' => '/gabung-donasi',
                'order' => 2,
                'is_active' => true,
                'id_title' => 'Bersama Kita Bisa Membuat Perubahan',
                'id_subtitle' => 'Donasi dan partisipasi Anda adalah kunci keberlanjutan ekosistem Danau Matano.',
                'id_cta' => 'Donasi Sekarang',
                'en_title' => 'Together We Can Make a Difference',
                'en_subtitle' => 'Your donation and participation are key to sustaining Lake Matano\'s ecosystem.',
                'en_cta' => 'Donate Now',
            ],
            [
                'image_url' => '/image/logo_tova_matano.png',
                'cta_url' => '/kualitas-air',
                'order' => 3,
                'is_active' => true,
                'id_title' => 'Data Nyata, Aksi Nyata',
                'id_subtitle' => 'Pemantauan kualitas air Danau Matano secara berkala untuk masa depan yang lebih baik.',
                'id_cta' => 'Lihat Data Kualitas Air',
                'en_title' => 'Real Data, Real Action',
                'en_subtitle' => 'Periodic monitoring of Lake Matano water quality for a better future.',
                'en_cta' => 'View Water Quality Data',
            ],
        ];

        foreach ($slides as $s) {
            $slide = HeroSlide::create([
                'image_url' => $s['image_url'],
                'cta_url' => $s['cta_url'],
                'order' => $s['order'],
                'is_active' => $s['is_active'],
            ]);

            $slide->translateOrNew('id')->title = $s['id_title'];
            $slide->translateOrNew('id')->subtitle = $s['id_subtitle'];
            $slide->translateOrNew('id')->cta_label = $s['id_cta'];
            $slide->translateOrNew('en')->title = $s['en_title'];
            $slide->translateOrNew('en')->subtitle = $s['en_subtitle'];
            $slide->translateOrNew('en')->cta_label = $s['en_cta'];
            $slide->save();
        }
    }

    private function seedRunningTexts(): void
    {
        if (RunningText::count() > 0) {
            return;
        }

        $texts = [
            ['text' => '🌊 Danau Matano — Danau Tektonik Terdalam di Indonesia', 'order' => 1],
            ['text' => '🐟 Rumah bagi 30+ spesies ikan endemik yang tidak ditemukan di tempat lain di dunia', 'order' => 2],
            ['text' => '💧 Mari bersama jaga kejernihan dan ekosistem Danau Matano untuk generasi mendatang', 'order' => 3],
            ['text' => '🌿 TMCC — Tova Matano Care Center, Bergerak Nyata untuk Alam', 'order' => 4],
            ['text' => '📊 Data kualitas air diperbarui setiap bulan dari 6 titik pengambilan sampel', 'order' => 5],
            ['text' => '🤝 Bergabunglah bersama 200+ relawan aktif TMCC di Luwu Timur', 'order' => 6],
        ];

        foreach ($texts as $t) {
            RunningText::create($t);
        }
    }

    private function seedFounders(): void
    {
        if (Founder::count() > 0) {
            return;
        }

        $founders = [
            ['name' => 'Dr. Ir. Tova Matano, M.Sc.', 'role' => 'Direktur Utama TMCC', 'org_name' => null, 'order' => 1],
            ['name' => 'Dr. Bambang Suharto', 'role' => 'Direktur Riset & Konservasi', 'org_name' => 'Universitas Hasanuddin', 'order' => 2],
            ['name' => 'Rina Kusuma, S.H.', 'role' => 'Direktur Eksekutif', 'org_name' => null, 'order' => 3],
            ['name' => 'Prof. Dr. Anwar Sanusi', 'role' => 'Ketua Dewan Pembina', 'org_name' => 'Universitas Indonesia', 'order' => 4],
        ];

        foreach ($founders as $f) {
            Founder::create($f);
        }
    }

    private function seedPrograms(): array
    {
        $programs = [];
        $data = [
            [
                'slug' => 'tova-water-patrol',
                'name' => 'Tova Water Patrol',
                'description' => 'Program pemantauan kualitas air Danau Matano secara berkala oleh relawan terlatih. Data yang terkumpul menjadi dasar advokasi kebijakan perlindungan danau.',
                'order' => 1,
            ],
            [
                'slug' => 'eco-education',
                'name' => 'Eco Education',
                'description' => 'Program edukasi lingkungan hidup bagi siswa SD/SMP di sekitar Danau Matano. Materi mencakup ekosistem danau, daur air, dan aksi konservasi sederhana.',
                'order' => 2,
            ],
            [
                'slug' => 'clean-lake-action',
                'name' => 'Clean Lake Action',
                'description' => 'Aksi bersih-bersih Danau Matano dan area resapan air. Melibatkan relawan masyarakat, pelajar, dan pemerintah daerah secara gotong royong.',
                'order' => 3,
            ],
            [
                'slug' => 'biodiversity-watch',
                'name' => 'Biodiversity Watch',
                'description' => 'Pemantauan keanekaragaman hayati endemik Danau Matano termasuk ikan, moluska, dan tumbuhan air. Berkontribusi pada data konservasi nasional.',
                'order' => 4,
            ],
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
        $p4 = $programs['biodiversity-watch'] ?? Program::where('slug', 'biodiversity-watch')->first();

        $activities = [
            // Tova Water Patrol
            [
                'program_id' => $p1?->id,
                'title' => 'Pemantauan Kualitas Air Bulanan - September 2026',
                'slug' => 'pemantauan-kualitas-air-september-2026',
                'cover_image_url' => '/image/logo_tova_matano.png',
                'content' => '<h3>Hasil Pemantauan</h3><p>Tim Water Patrol melakukan pengukuran di 6 titik sampling Danau Matano pada 15 September 2026.</p><h3>Temuan Utama</h3><ul><li>pH rata-rata: 7.2 (normal)</li><li>Kejernihan rata-rata: 445 cm (baik)</li><li>Indikasi peningkatan sedimentasi di area muara sungai</li><li>Suhu permukaan air: 28°C (normal musim kering)</li></ul><p>Secara keseluruhan kondisi danau masih dalam batas normal. Tim merekomendasikan peningkatan frekuensi pemantauan di titik muara.</p>',
                'published_at' => now()->subDays(5),
                'is_published' => true,
            ],
            [
                'program_id' => $p1?->id,
                'title' => 'Pemantauan Kualitas Air Bulanan - Agustus 2026',
                'slug' => 'pemantauan-kualitas-air-agustus-2026',
                'cover_image_url' => '/image/logo_tova_matano.png',
                'content' => '<h3>Laporan Agustus 2026</h3><p>Pengukuran bulan Agustus menunjukkan kondisi yang stabil dengan sedikit penurunan kejernihan akibat musim kering berkepanjangan.</p><ul><li>pH: 7.1</li><li>Kejernihan: 430 cm</li><li>Suhu: 29°C</li></ul>',
                'published_at' => now()->subDays(35),
                'is_published' => true,
            ],
            [
                'program_id' => $p1?->id,
                'title' => 'Pelatihan Relawan Water Patrol Batch 5',
                'slug' => 'pelatihan-relawan-water-patrol-batch-5',
                'cover_image_url' => '/image/logo_tova_matano.png',
                'content' => '<h3>Pelatihan Teknis Pengukuran Kualitas Air</h3><p>TMCC menyelenggarakan pelatihan intensif selama 3 hari bagi 25 relawan baru Water Patrol. Materi meliputi teknik pengambilan sampel, penggunaan alat ukur pH dan turbiditas, serta protokol keselamatan di perairan.</p>',
                'published_at' => now()->subDays(50),
                'is_published' => true,
            ],
            // Eco Education
            [
                'program_id' => $p2?->id,
                'title' => 'Kelas Lingkungan di SDN 01 Sorowako',
                'slug' => 'kelas-lingkungan-sdn01-sorowako',
                'cover_image_url' => '/image/logo_tova_matano.png',
                'content' => '<h3>Edukasi untuk Generasi Muda</h3><p>Tim Eco Education mengadakan kelas interaktif bagi 45 siswa kelas 4 dan 5 SDN 01 Sorowako.</p><ul><li>Pengenalan ekosistem Danau Matano</li><li>Peran ikan endemik dalam rantai makanan</li><li>Cara sederhana menjaga kebersihan danau</li></ul>',
                'published_at' => now()->subDays(12),
                'is_published' => true,
            ],
            [
                'program_id' => $p2?->id,
                'title' => 'Workshop Guru: Integrasi Materi Lingkungan Danau ke Kurikulum',
                'slug' => 'workshop-guru-integrasi-materi-lingkungan',
                'cover_image_url' => '/image/logo_tova_matano.png',
                'content' => '<h3>Meningkatkan Kapasitas Guru</h3><p>Workshop ini dihadiri oleh 30 guru dari 8 sekolah di Kecamatan Nuha dan Malili. Tujuannya adalah membantu guru mengintegrasikan materi pelestarian Danau Matano ke dalam pembelajaran IPA dan IPS.</p>',
                'published_at' => now()->subDays(45),
                'is_published' => true,
            ],
            // Clean Lake Action
            [
                'program_id' => $p3?->id,
                'title' => 'Aksi Bersih Danau Matano - Hari Sungai Nasional',
                'slug' => 'aksi-bersih-danau-hari-sungai-nasional',
                'cover_image_url' => '/image/logo_tova_matano.png',
                'content' => '<h3>Gotong Royong Bersihkan Danau</h3><p>120 relawan berpartisipasi dalam aksi bersih-bersih di sepanjang tepian Danau Matano.</p><h3>Hasil</h3><ul><li>0.8 ton sampah plastik terkumpul</li><li>3 titik hotspot pembuangan sampah liar teridentifikasi</li><li>Pemasangan 10 papan larangan buang sampah</li></ul>',
                'published_at' => now()->subDays(20),
                'is_published' => true,
            ],
            [
                'program_id' => $p3?->id,
                'title' => 'Pemasangan Jaring Sampah di Muara Sungai Petea',
                'slug' => 'pemasangan-jaring-sampah-muara-petea',
                'cover_image_url' => '/image/logo_tova_matano.png',
                'content' => '<h3>Inovasi Penangkapan Sampah</h3><p>TMCC bekerja sama dengan Dinas Lingkungan Hidup Luwu Timur memasang 3 unit jaring sampah di muara Sungai Petea. Sistem ini berhasil mengurangi masuknya sampah ke danau hingga 60% dalam uji coba pertama.</p>',
                'published_at' => now()->subDays(60),
                'is_published' => true,
            ],
            // Biodiversity Watch
            [
                'program_id' => $p4?->id,
                'title' => 'Survei Populasi Ikan Butini di Danau Matano',
                'slug' => 'survei-populasi-ikan-butini-2026',
                'cover_image_url' => '/image/logo_tova_matano.png',
                'content' => '<h3>Ikan Butini: Endemik yang Terancam</h3><p>Tim Biodiversity Watch bersama peneliti dari LIPI melakukan survei populasi ikan Butini (Glossogobius matanensis), spesies endemik Danau Matano yang kian langka.</p><h3>Temuan</h3><ul><li>Estimasi populasi: 2.400 - 3.100 individu (turun 15% dari 2024)</li><li>Habitat utama: kedalaman 15-40 meter</li><li>Ancaman terbesar: penurunan kejernihan air dan perubahan suhu</li></ul>',
                'published_at' => now()->subDays(18),
                'is_published' => true,
            ],
            [
                'program_id' => $p4?->id,
                'title' => 'Dokumentasi Flora Air Danau Matano 2026',
                'slug' => 'dokumentasi-flora-air-danau-matano-2026',
                'cover_image_url' => '/image/logo_tova_matano.png',
                'content' => '<h3>Kekayaan Tumbuhan Air Danau Matano</h3><p>Tim relawan mengidentifikasi 47 spesies tumbuhan air di Danau Matano, 12 di antaranya merupakan spesies endemik yang belum pernah tercatat sebelumnya di database nasional KLHK.</p>',
                'published_at' => now()->subDays(55),
                'is_published' => true,
            ],
        ];

        foreach ($activities as $a) {
            Activity::firstOrCreate(['slug' => $a['slug']], $a);
        }
    }

    private function seedOrgStructure(): void
    {
        if (OrgCategory::count() > 0) {
            return;
        }

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
            ['cat' => 'Penasehat', 'name' => 'Prof. Dr. Arifin Tasrif', 'role_title' => 'Guru Besar Limnologi — UNHAS', 'order' => 1],
            ['cat' => 'Penasehat', 'name' => 'Dr. Sri Wahyuni', 'role_title' => 'Peneliti Senior — BRIN', 'order' => 2],
            ['cat' => 'Penasehat', 'name' => 'Ir. Hendra Gunawan', 'role_title' => 'Ahli Kebijakan Lingkungan — KLHK', 'order' => 3],
            ['cat' => 'Dewan Pakar', 'name' => 'Dr. Hendra Kurniawan', 'role_title' => 'Ahli Ekologi Perairan', 'order' => 1],
            ['cat' => 'Dewan Pakar', 'name' => 'Dr. Mayang Sari, M.Si.', 'role_title' => 'Ahli Konservasi Biodiversitas', 'order' => 2],
            ['cat' => 'Dewan Pakar', 'name' => 'Dr. Fauzan Alamsyah', 'role_title' => 'Ahli Hidrologi Danau', 'order' => 3],
            ['cat' => 'Badan Eksekutif', 'name' => 'Rina Kusuma, S.H.', 'role_title' => 'Sekretaris Jenderal', 'order' => 1],
            ['cat' => 'Badan Eksekutif', 'name' => 'Ahmad Fadli, S.E., M.M.', 'role_title' => 'Bendahara Umum', 'order' => 2],
            ['cat' => 'Badan Eksekutif', 'name' => 'Dewi Lestari, S.Pi.', 'role_title' => 'Kepala Divisi Riset & Pemantauan', 'order' => 3],
            ['cat' => 'Pengurus Inti', 'name' => 'Budi Santoso', 'role_title' => 'Koordinator Lapangan', 'order' => 1],
            ['cat' => 'Pengurus Inti', 'name' => 'Siti Rahmawati, S.Pd.', 'role_title' => 'Koordinator Relawan', 'order' => 2],
            ['cat' => 'Pengurus Inti', 'name' => 'Muhammad Iqbal', 'role_title' => 'Koordinator Media & Komunikasi', 'order' => 3],
            ['cat' => 'Divisi', 'name' => 'Yusuf Maulana', 'role_title' => 'Kepala Divisi', 'divisi_name' => 'Divisi Riset & Pemantauan', 'order' => 1],
            ['cat' => 'Divisi', 'name' => 'Nina Aprilia', 'role_title' => 'Kepala Divisi', 'divisi_name' => 'Divisi Edukasi & Sosialisasi', 'order' => 2],
            ['cat' => 'Divisi', 'name' => 'Rico Pratama', 'role_title' => 'Kepala Divisi', 'divisi_name' => 'Divisi Aksi Lapangan', 'order' => 3],
            ['cat' => 'Divisi', 'name' => 'Layla Santika, S.Kom.', 'role_title' => 'Kepala Divisi', 'divisi_name' => 'Divisi Digital & Media', 'order' => 4],
        ];

        foreach ($categories as $catData) {
            $cat = OrgCategory::create($catData);
            foreach ($orgPeople as $p) {
                if ($p['cat'] === $catData['name']) {
                    $cat->people()->create([
                        'name' => $p['name'],
                        'role_title' => $p['role_title'],
                        'divisi_name' => $p['divisi_name'] ?? null,
                        'order' => $p['order'],
                    ]);
                }
            }
        }
    }

    private function seedDonationChannels(): void
    {
        if (DonationChannel::count() > 0) {
            return;
        }

        $channels = [
            ['bank_name' => 'Bank Mandiri', 'account_no' => '170-00-1922419-9', 'account_name' => 'Yayasan TMCC'],
            ['bank_name' => 'Bank BRI', 'account_no' => '4567-01-0234567-8', 'account_name' => 'Yayasan TMCC'],
            ['bank_name' => 'Bank BCA', 'account_no' => '8901-234-567', 'account_name' => 'Yayasan TMCC'],
            ['bank_name' => 'Bank BSI', 'account_no' => '7121-0023-456', 'account_name' => 'Yayasan TMCC'],
        ];

        foreach ($channels as $ch) {
            DonationChannel::create($ch);
        }
    }

    private function seedBlogPosts(): void
    {
        if (BlogPost::count() > 0) {
            return;
        }

        $posts = [
            // EDUKASI
            [
                'title' => 'Mengapa Danau Matano Perlu Dilindungi?',
                'slug' => 'mengapa-danau-matano-perlu-dilindungi',
                'category' => 'EDUKASI',
                'cover_image_url' => '/image/logo_tova_matano.png',
                'content' => '<h2>Permata Tersembunyi di Sulawesi</h2><p>Danau Matano adalah danau tektonik tertua di Asia Tenggara, dengan kedalaman mencapai 590 meter. Danau ini menjadi rumah bagi lebih dari 30 spesies ikan endemik yang tidak ditemukan di tempat lain di dunia.</p><h3>Ancaman yang Dihadapi</h3><ul><li>Pencemaran limbah pertambangan nikel</li><li>Sedimentasi akibat deforestasi di daerah tangkapan air</li><li>Eksploitasi berlebihan sumber daya ikan oleh nelayan</li><li>Perubahan iklim yang mempengaruhi suhu dan debit air</li></ul><p>Oleh karena itu, upaya pelestarian yang terstruktur dan berkelanjutan sangat diperlukan.</p>',
                'published_at' => now()->subDays(7),
                'is_published' => true,
            ],
            [
                'title' => 'Mengenal Ikan Endemik Danau Matano',
                'slug' => 'mengenal-ikan-endemik-danau-matano',
                'category' => 'EDUKASI',
                'cover_image_url' => '/image/logo_tova_matano.png',
                'content' => '<h2>Keanekaragaman Hayati yang Unik</h2><p>Danau Matano menjadi habitat bagi sejumlah spesies ikan yang hanya dapat ditemukan di danau ini. Beberapa di antaranya adalah Butini (Glossogobius matanensis), Wowon (Telmatherina prognatha), dan Opudi (Telmatherina antoniae).</p><p>Keunikan spesies-spesies ini menjadi alasan penting mengapa Danau Matano perlu mendapat perlindungan khusus sebagai kawasan konservasi.</p>',
                'published_at' => now()->subDays(30),
                'is_published' => true,
            ],
            // RILIS_PERS
            [
                'title' => 'TMCC Raih Penghargaan Lingkungan Tingkat Nasional',
                'slug' => 'tmcc-raih-penghargaan-lingkungan',
                'category' => 'RILIS_PERS',
                'cover_image_url' => '/image/logo_tova_matano.png',
                'content' => '<h2>Penghargaan Kalpataru 2026</h2><p>Tova Matano Care Center (TMCC) menerima penghargaan Kalpataru kategori Pengabdian di bidang konservasi sumber daya air pada acara Hari Lingkungan Hidup Sedunia 2026 yang diselenggarakan oleh Kementerian Lingkungan Hidup dan Kehutanan.</p><p>Penghargaan ini diserahkan langsung oleh Menteri LHK kepada Direktur Utama TMCC, Dr. Ir. Tova Matano, M.Sc., di Jakarta pada 5 Juni 2026.</p>',
                'published_at' => now()->subDays(15),
                'is_published' => true,
            ],
            [
                'title' => 'TMCC Tandatangani MoU dengan BRIN untuk Riset Danau Matano',
                'slug' => 'tmcc-mou-brin-riset-danau-matano',
                'category' => 'RILIS_PERS',
                'cover_image_url' => '/image/logo_tova_matano.png',
                'content' => '<h2>Kolaborasi Riset Nasional</h2><p>TMCC dan Badan Riset dan Inovasi Nasional (BRIN) resmi menandatangani Memorandum of Understanding (MoU) untuk kolaborasi riset terpadu Danau Matano selama 3 tahun ke depan. Kerja sama ini mencakup pemantauan biodiversitas, studi kualitas air, dan pengembangan model prediksi ekosistem danau.</p>',
                'published_at' => now()->subDays(40),
                'is_published' => true,
            ],
            // BERITA
            [
                'title' => '200 Relawan Bergabung di Aksi Bersih Danau Matano 2026',
                'slug' => '200-relawan-aksi-bersih-danau-2026',
                'category' => 'BERITA',
                'cover_image_url' => '/image/logo_tova_matano.png',
                'content' => '<h2>Antusiasme Luar Biasa dari Masyarakat</h2><p>Aksi Bersih Danau Matano 2026 berhasil menggerakkan lebih dari 200 relawan dari berbagai elemen masyarakat — mulai dari pelajar, mahasiswa, karyawan PT Vale, hingga tokoh masyarakat setempat. Total sampah yang berhasil dikumpulkan mencapai 1,2 ton dalam satu hari kegiatan.</p>',
                'published_at' => now()->subDays(10),
                'is_published' => true,
            ],
            // KEBIJAKAN
            [
                'title' => 'Revisi UU Cipta Kerja: Dampak terhadap Perlindungan Danau',
                'slug' => 'revisi-uu-cipta-kerja-dampak-danau',
                'category' => 'KEBIJAKAN',
                'cover_image_url' => '/image/logo_tova_matano.png',
                'content' => '<h2>Analisis Kebijakan</h2><p>Revisi Undang-Undang Cipta Kerja membawa perubahan signifikan dalam tata kelola lingkungan hidup di Indonesia, termasuk mekanisme perizinan untuk kegiatan di sekitar kawasan danau.</p><h3>Poin Penting</h3><ul><li>Perubahan mekanisme AMDAL yang lebih ringkas</li><li>Dampak terhadap zona penyangga dan sempadan danau</li><li>Potensi risiko terhadap ekosistem Danau Matano</li></ul>',
                'published_at' => now()->subDays(25),
                'is_published' => true,
            ],
            // BASELINE_STUDY
            [
                'title' => 'Baseline Study Kualitas Air Danau Matano 2024',
                'slug' => 'baseline-study-kualitas-air-danau-matano-2024',
                'category' => 'BASELINE_STUDY',
                'cover_image_url' => '/image/logo_tova_matano.png',
                'content' => '<h2>Ringkasan Eksekutif</h2><p>Studi dasar ini merupakan pemetaan komprehensif kondisi kualitas air Danau Matano yang dilakukan selama 12 bulan (Januari–Desember 2024) di 6 titik pengambilan sampel. Hasil studi ini menjadi acuan untuk program pemantauan dan intervensi lingkungan TMCC pada 2025–2027.</p><h3>Parameter yang Diukur</h3><ul><li>pH air (target: 6.5 – 7.5)</li><li>Kejernihan / Kecerahan Secchi (target: >400 cm)</li><li>Suhu air permukaan dan kedalaman</li><li>Konsentrasi dissolved oxygen (DO)</li><li>Kadar nitrat dan fosfat</li></ul>',
                'published_at' => now()->subDays(180),
                'is_published' => true,
            ],
            [
                'title' => 'Studi Keanekaragaman Hayati Danau Matano: Laporan Awal 2025',
                'slug' => 'studi-keanekaragaman-hayati-danau-matano-2025',
                'category' => 'BASELINE_STUDY',
                'cover_image_url' => '/image/logo_tova_matano.png',
                'content' => '<h2>Pendahuluan</h2><p>Laporan awal ini menyajikan temuan survei biodiversitas Danau Matano yang dilaksanakan pada Maret–Juni 2025, mencakup inventarisasi ikan, moluska, tumbuhan air, dan plankton sebagai indikator kesehatan ekosistem danau.</p><h3>Temuan Awal</h3><ul><li>Teridentifikasi 34 spesies ikan, 28 di antaranya endemik</li><li>9 spesies moluska endemik terdata</li><li>47 spesies tumbuhan air, 12 spesies baru bagi database nasional</li></ul>',
                'published_at' => now()->subDays(120),
                'is_published' => true,
            ],
            [
                'title' => 'Pemetaan Titik Rawan Sedimentasi Danau Matano',
                'slug' => 'pemetaan-titik-rawan-sedimentasi-danau-matano',
                'category' => 'BASELINE_STUDY',
                'cover_image_url' => '/image/logo_tova_matano.png',
                'content' => '<h2>Metodologi</h2><p>Pemetaan dilakukan menggunakan kombinasi survei batimetri, pengambilan sampel sedimen, dan analisis citra satelit Sentinel-2 untuk mengidentifikasi area-area dengan tingkat sedimentasi tinggi di Danau Matano dan sekitarnya.</p><h3>Hasil Pemetaan</h3><ul><li>Teridentifikasi 5 titik rawan sedimentasi di sekitar muara sungai</li><li>Area delta Sungai Petea mengalami akumulasi sedimen tertinggi</li><li>Rekomendasi: reboisasi buffer zone di 3 sub-DAS prioritas</li></ul>',
                'published_at' => now()->subDays(90),
                'is_published' => true,
            ],
        ];

        foreach ($posts as $p) {
            BlogPost::create($p);
        }
    }

    private function seedGallery(): void
    {
        if (GalleryItem::count() > 0) {
            return;
        }

        $items = [
            ['type' => 'PHOTO', 'url' => '/image/logo_tova_matano.png', 'caption' => 'Suasana kegiatan Water Patrol di Danau Matano'],
            ['type' => 'PHOTO', 'url' => '/image/logo_tova_matano.png', 'caption' => 'Eco Education di SDN 01 Sorowako'],
            ['type' => 'PHOTO', 'url' => '/image/logo_tova_matano.png', 'caption' => 'Aksi bersih tepian Danau Matano'],
            ['type' => 'PHOTO', 'url' => '/image/logo_tova_matano.png', 'caption' => 'Tim relawan TMCC siap bertugas'],
            ['type' => 'PHOTO', 'url' => '/image/logo_tova_matano.png', 'caption' => 'Pengukuran kualitas air dengan peralatan modern'],
            ['type' => 'PHOTO', 'url' => '/image/logo_tova_matano.png', 'caption' => 'Keanekaragaman hayati endemik Danau Matano'],
            ['type' => 'PHOTO', 'url' => '/image/logo_tova_matano.png', 'caption' => 'Workshop konservasi bersama komunitas lokal'],
            ['type' => 'PHOTO', 'url' => '/image/logo_tova_matano.png', 'caption' => 'Pemandangan Danau Matano dari ketinggian'],
            ['type' => 'PHOTO', 'url' => '/image/logo_tova_matano.png', 'caption' => 'Penghargaan Kalpataru 2026 untuk TMCC'],
            ['type' => 'PHOTO', 'url' => '/image/logo_tova_matano.png', 'caption' => 'Survei biodiversitas bersama tim BRIN'],
            ['type' => 'VIDEO', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'caption' => 'Profil TMCC — Menjaga Danau Matano'],
            ['type' => 'VIDEO', 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'caption' => 'Aksi Bersih Danau Matano 2026'],
        ];

        foreach ($items as $item) {
            GalleryItem::create($item);
        }
    }

    private function seedMembers(): void
    {
        if (Member::count() > 0) {
            return;
        }

        $members = [
            ['name' => 'Ahmad Rizki Pratama', 'divisi_name' => 'Divisi Riset & Pemantauan', 'points' => 980, 'message' => 'Danau Matano adalah warisan alam yang harus kita jaga bersama untuk anak cucu kita.', 'joined_at' => now()->subMonths(24)],
            ['name' => 'Sari Dewi Puspita', 'divisi_name' => 'Divisi Edukasi & Sosialisasi', 'points' => 875, 'message' => 'Setiap tetes air yang jernih adalah hasil perjuangan kita bersama.', 'joined_at' => now()->subMonths(20)],
            ['name' => 'Budi Setiawan', 'divisi_name' => 'Divisi Aksi Lapangan', 'points' => 820, 'message' => 'Kita semua punya tanggung jawab untuk menjaga bumi yang kita pijak.', 'joined_at' => now()->subMonths(18)],
            ['name' => 'Layla Fitri Handayani', 'divisi_name' => 'Divisi Digital & Media', 'points' => 760, 'message' => 'Melalui media sosial, kita bisa menyebarkan kesadaran lingkungan ke seluruh Indonesia.', 'joined_at' => now()->subMonths(16)],
            ['name' => 'Rahmat Hidayat', 'divisi_name' => 'Divisi Riset & Pemantauan', 'points' => 715, 'message' => 'Data adalah senjata kita. Semakin banyak data, semakin kuat advokasi kita.', 'joined_at' => now()->subMonths(15)],
            ['name' => 'Nisa Amalia Putri', 'divisi_name' => 'Divisi Edukasi & Sosialisasi', 'points' => 680, 'message' => 'Generasi muda adalah harapan bagi kelestarian Danau Matano.', 'joined_at' => now()->subMonths(14)],
            ['name' => 'Hendra Saputra', 'divisi_name' => 'Divisi Aksi Lapangan', 'points' => 640, 'message' => 'Setiap aksi kecil, jika dilakukan bersama-sama, akan menghasilkan perubahan besar.', 'joined_at' => now()->subMonths(12)],
            ['name' => 'Dewi Anggraeni', 'divisi_name' => 'Divisi Riset & Pemantauan', 'points' => 590, 'message' => 'Saya bangga menjadi bagian dari keluarga TMCC yang peduli lingkungan.', 'joined_at' => now()->subMonths(11)],
            ['name' => 'Fauzan Al-Hakim', 'divisi_name' => 'Divisi Aksi Lapangan', 'points' => 540, 'message' => 'Danau Matano sudah memberikan banyak bagi kita, saatnya kita membalasnya.', 'joined_at' => now()->subMonths(10)],
            ['name' => 'Rina Marlina', 'divisi_name' => 'Divisi Edukasi & Sosialisasi', 'points' => 490, 'message' => 'Menanam pohon hari ini adalah investasi terbaik untuk masa depan danau kita.', 'joined_at' => now()->subMonths(9)],
            ['name' => 'Yanto Kurniawan', 'divisi_name' => 'Divisi Digital & Media', 'points' => 445, 'message' => 'Satu foto yang tepat bisa menggerakkan ribuan orang untuk peduli.', 'joined_at' => now()->subMonths(8)],
            ['name' => 'Putri Rahayu', 'divisi_name' => 'Divisi Riset & Pemantauan', 'points' => 400, 'message' => 'Setiap pengukuran yang saya lakukan adalah kontribusi nyata bagi kelestarian danau.', 'joined_at' => now()->subMonths(7)],
            ['name' => 'Arif Wibowo', 'divisi_name' => 'Divisi Aksi Lapangan', 'points' => 355, 'message' => 'Tangan kita yang membersihkan danau hari ini akan diingat oleh anak cucu kita.', 'joined_at' => now()->subMonths(6)],
            ['name' => 'Maya Sari Indah', 'divisi_name' => 'Divisi Edukasi & Sosialisasi', 'points' => 310, 'message' => 'Mengajarkan anak-anak tentang lingkungan adalah investasi jangka panjang terbaik.', 'joined_at' => now()->subMonths(5)],
            ['name' => 'Rizal Faisal', 'divisi_name' => 'Divisi Digital & Media', 'points' => 265, 'message' => 'Bergabung di TMCC membuat saya sadar betapa pentingnya menjaga alam.', 'joined_at' => now()->subMonths(4)],
        ];

        foreach ($members as $m) {
            Member::create($m);
        }
    }

    private function seedVolunteers(): void
    {
        if (Volunteer::count() > 0) {
            return;
        }

        $volunteers = [
            [
                'full_name' => 'Joko Susilo Prawiro',
                'email' => 'joko.susilo@gmail.com',
                'phone' => '081234567001',
                'address' => 'Sorowako, Luwu Timur',
                'motivation' => 'Saya ingin berkontribusi nyata dalam pelestarian Danau Matano yang selama ini menjadi sumber kehidupan masyarakat sekitar.',
                'status' => 'APPROVED',
                'submitted_at' => now()->subDays(60),
            ],
            [
                'full_name' => 'Tuti Alawiyah',
                'email' => 'tuti.ala@yahoo.com',
                'phone' => '081234567002',
                'address' => 'Malili, Luwu Timur',
                'motivation' => 'Sebagai warga lokal, saya merasa punya tanggung jawab moral untuk turut menjaga kelestarian danau ini.',
                'status' => 'APPROVED',
                'submitted_at' => now()->subDays(45),
            ],
            [
                'full_name' => 'Bambang Prasetyo',
                'email' => 'bambang.p@outlook.com',
                'phone' => '081234567003',
                'address' => 'Makassar, Sulawesi Selatan',
                'motivation' => 'Mahasiswa Teknik Lingkungan Unhas yang ingin mengaplikasikan ilmu dalam praktik nyata pelestarian danau.',
                'status' => 'PENDING',
                'submitted_at' => now()->subDays(5),
            ],
            [
                'full_name' => 'Indah Permata Sari',
                'email' => 'indah.permata@gmail.com',
                'phone' => '081234567004',
                'address' => 'Kendari, Sulawesi Tenggara',
                'motivation' => 'Peneliti muda yang tertarik dengan studi ekosistem danau purba. Ingin berkontribusi dalam pengumpulan data biodiversitas.',
                'status' => 'PENDING',
                'submitted_at' => now()->subDays(2),
            ],
            [
                'full_name' => 'Wahyu Hidayatullah',
                'email' => 'wahyu.h@gmail.com',
                'phone' => '081234567005',
                'address' => 'Sorowako, Luwu Timur',
                'motivation' => 'Karyawan PT Vale yang ingin berkontribusi di luar jam kerja untuk program pelestarian lingkungan.',
                'status' => 'REJECTED',
                'submitted_at' => now()->subDays(30),
            ],
        ];

        foreach ($volunteers as $v) {
            Volunteer::create(array_merge($v, ['submitted_at' => $v['submitted_at']]));
        }
    }

    private function seedPartnerships(): void
    {
        if (Partnership::count() > 0) {
            return;
        }

        $partnerships = [
            // Partner
            ['name' => 'PT Vale Indonesia Tbk', 'website_url' => 'https://vale.com', 'description' => 'Mitra strategis dalam program pemulihan ekosistem dan tanggung jawab sosial perusahaan di kawasan Danau Matano.', 'type' => 'partner', 'order' => 1],
            ['name' => 'BRIN — Badan Riset dan Inovasi Nasional', 'website_url' => 'https://brin.go.id', 'description' => 'Kolaborasi riset terpadu untuk pemantauan biodiversitas dan kualitas air Danau Matano.', 'type' => 'partner', 'order' => 2],
            ['name' => 'Universitas Hasanuddin', 'website_url' => 'https://unhas.ac.id', 'description' => 'Mitra akademis dalam penelitian ekosistem perairan dan pengembangan kapasitas relawan TMCC.', 'type' => 'partner', 'order' => 3],
            ['name' => 'Pemkab Luwu Timur', 'website_url' => 'https://luwutimurkab.go.id', 'description' => 'Dukungan pemerintah daerah dalam regulasi perlindungan Danau Matano dan koordinasi program konservasi.', 'type' => 'partner', 'order' => 4],
            ['name' => 'KLHK — Kementerian Lingkungan Hidup', 'website_url' => 'https://menlhk.go.id', 'description' => 'Dukungan kebijakan dan regulasi untuk program konservasi danau tingkat nasional.', 'type' => 'partner', 'order' => 5],
            // Sponsor
            ['name' => 'Yayasan Kehati', 'website_url' => 'https://kehati.or.id', 'description' => 'Dukungan pendanaan program biodiversitas dan konservasi spesies endemik Danau Matano.', 'type' => 'sponsor', 'order' => 1],
            ['name' => 'GEF — Global Environment Facility', 'website_url' => 'https://thegef.org', 'description' => 'Pendanaan hibah internasional untuk program pemantauan ekosistem dan penguatan kapasitas organisasi.', 'type' => 'sponsor', 'order' => 2],
            ['name' => 'Bank Mandiri Peduli', 'website_url' => 'https://bankmandiri.co.id', 'description' => 'Program CSR Bank Mandiri mendukung kegiatan edukasi lingkungan dan aksi bersih danau.', 'type' => 'sponsor', 'order' => 3],
            // Media
            ['name' => 'Kompas.id', 'website_url' => 'https://kompas.id', 'description' => 'Mitra media nasional untuk liputan dan publikasi isu-isu lingkungan Danau Matano.', 'type' => 'media', 'order' => 1],
            ['name' => 'Mongabay Indonesia', 'website_url' => 'https://mongabay.co.id', 'description' => 'Platform media lingkungan yang aktif meliput kegiatan konservasi TMCC.', 'type' => 'media', 'order' => 2],
            ['name' => 'RRI Makassar', 'website_url' => 'https://rri.co.id', 'description' => 'Mitra siaran radio regional untuk penyebaran informasi program TMCC ke masyarakat luas.', 'type' => 'media', 'order' => 3],
        ];

        foreach ($partnerships as $p) {
            Partnership::create($p);
        }
    }

    /**
     * @return array<int, WaterSamplingPoint>
     */
    private function seedWaterSamplingPoints(): array
    {
        if (WaterSamplingPoint::count() > 0) {
            return WaterSamplingPoint::all()->all();
        }

        $points = [
            ['name' => 'Titik A — Muara Sungai Petea', 'description' => 'Titik pengambilan di muara Sungai Petea, area dengan risiko sedimentasi tinggi.', 'coordinates' => '-2.4321, 121.3210', 'is_active' => true],
            ['name' => 'Titik B — Tengah Danau (Utara)', 'description' => 'Titik referensi di tengah bagian utara danau, representasi kondisi open water.', 'coordinates' => '-2.4500, 121.3400', 'is_active' => true],
            ['name' => 'Titik C — Dermaga Sorowako', 'description' => 'Titik dekat area dermaga Sorowako, potensi dampak aktivitas manusia lebih tinggi.', 'coordinates' => '-2.5100, 121.3500', 'is_active' => true],
            ['name' => 'Titik D — Teluk Timur', 'description' => 'Titik di sisi timur danau, dekat area hutan lindung dengan kondisi relatif terjaga.', 'coordinates' => '-2.4800, 121.3900', 'is_active' => true],
            ['name' => 'Titik E — Muara Sungai Larona', 'description' => 'Titik pengambilan di area keluaran menuju Sungai Larona, mempengaruhi debit aliran.', 'coordinates' => '-2.5300, 121.3100', 'is_active' => true],
            ['name' => 'Titik F — Teluk Barat (Zona Konservasi)', 'description' => 'Titik di zona konservasi ketat bagian barat, habitat utama ikan Butini.', 'coordinates' => '-2.4600, 121.2900', 'is_active' => true],
        ];

        $created = [];
        foreach ($points as $p) {
            $created[] = WaterSamplingPoint::create($p);
        }

        return $created;
    }

    /**
     * @param  array<int, WaterSamplingPoint>  $samplingPoints
     */
    private function seedWaterQuality(int $userId, array $samplingPoints): void
    {
        if (WaterQualityRecord::count() > 0) {
            // Update existing records to link sampling points if they have none
            WaterQualityRecord::whereNull('water_sampling_point_id')->each(function ($record) use ($samplingPoints) {
                $record->update(['water_sampling_point_id' => $samplingPoints[array_rand($samplingPoints)]->id]);
            });

            return;
        }

        // Generate rich dummy data across 6 points over 4 months
        $records = [];

        // Data per titik per bulan (realistic lake values)
        $baseData = [
            0 => ['ph' => 7.1, 'clarity' => 420], // Muara Petea — lebih rendah karena sedimentasi
            1 => ['ph' => 7.3, 'clarity' => 480], // Tengah Danau — kondisi terbaik
            2 => ['ph' => 7.0, 'clarity' => 410], // Dermaga Sorowako — aktivitas manusia
            3 => ['ph' => 7.2, 'clarity' => 460], // Teluk Timur — terjaga
            4 => ['ph' => 7.1, 'clarity' => 430], // Muara Larona
            5 => ['ph' => 7.4, 'clarity' => 490], // Zona Konservasi — terbaik
        ];

        $monthsAgo = [90, 75, 60, 45, 30, 20, 15, 10, 5, 2];
        $notePool = [
            'Kondisi normal, cuaca cerah saat pengambilan sampel.',
            'Hujan lebat 2 hari sebelumnya, sedikit penurunan kejernihan.',
            'Kondisi sangat baik, musim kering mendukung kejernihan tinggi.',
            null,
            'Terdeteksi sedikit peningkatan sedimentasi di area muara.',
            'Suhu air permukaan lebih tinggi dari rata-rata musiman.',
            null,
            'Pengukuran dilakukan pagi hari, kondisi optimal.',
            null,
            'Indikasi aktivitas perahu nelayan meningkat.',
        ];

        foreach ($monthsAgo as $idx => $daysAgo) {
            foreach ($samplingPoints as $ptIdx => $point) {
                $base = $baseData[$ptIdx];
                // Slight variation per measurement
                $phVariation = (($idx + $ptIdx) % 3 - 1) * 0.1;
                $clarityVariation = (($idx * 2 + $ptIdx) % 5 - 2) * 10;

                $records[] = [
                    'recorded_at' => now()->subDays($daysAgo + $ptIdx), // stagger by 1 day per point
                    'ph' => round($base['ph'] + $phVariation, 1),
                    'clarity_cm' => $base['clarity'] + $clarityVariation,
                    'notes' => $notePool[($idx + $ptIdx) % count($notePool)],
                    'recorded_by' => $userId,
                    'water_sampling_point_id' => $point->id,
                ];
            }
        }

        foreach ($records as $r) {
            WaterQualityRecord::create($r);
        }
    }
}
