<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Singleton ?" selalu akses lewat SiteSettings::first() atau ::firstOrCreate([], [...])
class SiteSettings extends Model
{
    protected $table = 'site_settings';

    protected $fillable = [
        'fixed_text',
        'about_content', 'about_lake_image_credit', 'about_legal_verified',
        'address', 'phone', 'whatsapp', 'email',
        'instagram_url', 'facebook_url', 'tiktok_url', 'twitter_url',
        'about_hero_image_url', 'about_lake_image_url', 'org_hero_image_url', 'org_chart_image_url',
        'water_quality_map_image_url', 'water_quality_map_image_credit',
        'blog_hero_image_url',
        'partnership_hero_image_url', 'program_hero_image_url', 'contact_hero_image_url',
        'stat_sampah_terangkut', 'stat_sekolah_teredukasi',
    ];

    protected $casts = [
        'fixed_text' => 'array',
        'about_content' => 'array',
        'about_legal_verified' => 'boolean',
    ];

    public function getFixedText(?string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();
        $translations = is_array($this->fixed_text) ? $this->fixed_text : [];

        $text = $translations[$locale] ?? null;

        if ((! is_string($text) || trim($text) === '') && $locale !== 'id') {
            $text = $translations['id'] ?? null;
        }

        if (is_string($text) && trim($text) !== '') {
            return trim($text);
        }

        return null;
    }

    public function getHeroFixedTextAttribute(): ?string
    {
        return $this->getFixedText();
    }

    /**
     * Resolve the About page content for a locale.
     *
     * Each field is stored as a localized structure ({id, en}) inside the
     * `about_content` JSON column. Values fall back from the requested locale
     * to Indonesian, then to the approved default content.
     *
     * Legal note: this method NEVER fabricates legal numbers. The `legal.items`
     * default is intentionally empty; public exposure gating is handled in the
     * controller (Phase 3B-4) using the `legal.verified` flag, which reflects
     * only the stored `about_legal_verified` boolean.
     *
     * Reading this method never mutates the stored `about_content` value.
     *
     * @return array<string, mixed>
     */
    public function getAboutContent(?string $locale = null): array
    {
        $locale = $locale ?: app()->getLocale();
        $stored = is_array($this->about_content) ? $this->about_content : [];
        $defaults = $this->defaultAboutContent();

        return [
            'hero' => [
                'eyebrow' => $this->resolveAboutField(data_get($stored, 'hero.eyebrow'), $defaults['hero']['eyebrow'], $locale),
                'title' => $this->resolveAboutField(data_get($stored, 'hero.title'), $defaults['hero']['title'], $locale),
                'subtitle' => $this->resolveAboutField(data_get($stored, 'hero.subtitle'), $defaults['hero']['subtitle'], $locale),
            ],
            'lake' => [
                'title' => $this->resolveAboutField(data_get($stored, 'lake.title'), $defaults['lake']['title'], $locale),
                'body' => $this->resolveAboutField(data_get($stored, 'lake.body'), $defaults['lake']['body'], $locale),
            ],
            'profile' => [
                'title' => $this->resolveAboutField(data_get($stored, 'profile.title'), $defaults['profile']['title'], $locale),
                'body' => $this->resolveAboutField(data_get($stored, 'profile.body'), $defaults['profile']['body'], $locale),
            ],
            'potential' => [
                'title' => $this->resolveAboutField(data_get($stored, 'potential.title'), $defaults['potential']['title'], $locale),
                'body' => $this->resolveAboutField(data_get($stored, 'potential.body'), $defaults['potential']['body'], $locale),
            ],
            'legal' => [
                'title' => $this->resolveAboutField(data_get($stored, 'legal.title'), $defaults['legal']['title'], $locale),
                'items' => $this->resolveAboutField(data_get($stored, 'legal.items'), '', $locale),
                'verified' => $this->about_legal_verified === true,
            ],
            'vision' => $this->resolveAboutField(data_get($stored, 'vision'), $defaults['vision'], $locale),
            'mission' => $this->resolveAboutField(data_get($stored, 'mission'), $defaults['mission'], $locale),
            'goal' => $this->resolveAboutField(data_get($stored, 'goal'), $defaults['goal'], $locale),
            'founders' => [
                'title' => $this->resolveAboutField(data_get($stored, 'founders.title'), $defaults['founders']['title'], $locale),
                'narrative' => $this->resolveAboutField(data_get($stored, 'founders.narrative'), $defaults['founders']['narrative'], $locale),
            ],
        ];
    }

    /**
     * Resolve a single localized field with locale -> id -> default fallback.
     */
    private function resolveAboutField(mixed $stored, string $fallback, string $locale): string
    {
        if (is_array($stored)) {
            $value = $stored[$locale] ?? null;

            if ((! is_string($value) || trim($value) === '') && $locale !== 'id') {
                $value = $stored['id'] ?? null;
            }

            if (is_string($value) && trim($value) !== '') {
                return $value;
            }
        }

        return $fallback;
    }

    /**
     * Approved default About content (Indonesian), verbatim from the client
     * revision document. Legal `items` is intentionally empty and must not be
     * populated with unverified numbers.
     *
     * @return array<string, mixed>
     */
    private function defaultAboutContent(): array
    {
        return [
            'hero' => [
                'eyebrow' => 'Tentang Kami',
                'title' => 'ADVOKASI LINGKUNGAN LOKAL',
                'subtitle' => 'Membangun masa depan yang berkelanjutan untuk Danau Matano',
            ],
            'lake' => [
                'title' => 'Sekilas Danau Matano',
                'body' => implode("\n\n", [
                    'Danau Matano merupakan danau tektonik purba terdalam di Indonesia dan Asia Tenggara, serta terdalam ke-8 di dunia. Danau ini terletak Kecamatan Nuha, Kabupaten Luwu Timur, Sulawesi Selatan, dengan kedalaman sekitar 590 meter.',
                    'Sebagai "Danau Purba", Matano memiliki keanekaragaman hayati endemik yang luar biasa, termasuk ikan-ikan unik dan biota air tawar yang tidak ditemukan di tempat lain di dunia. Kualitas airnya yang jernih dan ekosistem yang rapuh membuatnya sangat penting untuk dilestarikan.',
                    'Ancaman terhadap kelestarian Danau Matano terus meningkat seiring dengan aktivitas manusia, mulai dari pencemaran, limbah domestik, perkebunan, tata kelola wisata yang lemah hingga dampak penambangan termasuk bertambahnya jumlah penduduk dipicu keberadaan sektor industri ini.  Oleh karena itu, langkah nyata untuk menjaga dan memulihkan kondisi danau ini menjadi sangat krusial.',
                ]),
            ],
            'profile' => [
                'title' => 'Profil & Sejarah',
                'body' => implode("\n\n", [
                    'Tova Matano Care Center (TMCC) lahir dari kepedulian yang mendalam terhadap pentingnya menjaga Danau Matano agar tetap lestari, dengan segala pesona keindahan, keunikan, dan keistimewaannya.',
                    'Dalam bahasa Sorowako, "Tova" atau "Towa" Matano berarti Danau Matano. Sementara itu, care center merupakan wadah bagi masyarakat lokal yang hidup secara turun-temurun di pesisir Danau Matano untuk mendiskusikan dan menyebarkan informasi yang jujur mengenai kondisi aktual Danau Matano, sekaligus menjadi motivator Aksi Gerakan Penyelamatan Danau.',
                    'TMCC adalah tempat bertemunya keindahan dan fakta kritis. Selain menyajikan keindahan pesona Danau Matano, TMCC juga melakukan inventarisasi permasalahan yang dinilai perlu disampaikan dan disuarakan dalam bentuk data dan informasi yang jujur mengenai perubahan kualitas lingkungan yang sesungguhnya sedang terjadi. Hal ini dilakukan untuk menumbuhkan rasa peduli, serta mendorong masyarakat untuk mendukung, bergabung, dan turut serta berbuat sesuatu dalam upaya memperbaiki dan menjaga kelestarian Danau Matano.',
                ]),
            ],
            'potential' => [
                'title' => 'Potensi TMCC',
                'body' => implode("\n\n", [
                    'Sebagai organisasi yang didirikan oleh masyarakat lokal, pegiat TMCC tumbuh dengan "local wisdom" yang kuat sejak masa lalu. Dengan "indigenous knowledge" yang dimilikinya, pegiat TMCC dipastikan berpengalaman, memahami, sekaligus memiliki keterampilan mengenai perilaku dan interaksi manusia secara turun-temurun dengan Danau Matano, dari masa lalu hingga saat ini. TMCC merupakan narasumber primer dan saksi atas setiap perubahan yang terjadi di Danau Matano.',
                    'Kami percaya bahwa Danau Matano bukan sekadar genangan air, melainkan rumah bagi keanekaragaman hayati endemik dan anugerah Allah SWT yang harus dijaga untuk generasi mendatang. Yang tidak kalah penting, sebagai masyarakat yang lahir, hidup, dan besar di pesisir danau ini, Danau Matano merupakan kebanggaan dan bagian dari cerita perjalanan hidup kami. Oleh karena itu, interaksi TMCC dengan Danau Matano lebih dari sekadar menikmati pesonanya, tetapi juga upaya untuk mengukur kehidupan leluhur di masa lalu dan membayangkan bagaimana kami harus mewariskan Danau Matano kepada masa depan.',
                ]),
            ],
            'legal' => [
                'title' => 'Legalitas',
                'items' => '',
            ],
            'vision' => 'Terwujudnya kelestarian Danau Matano sebagai ekosistem yang sehat, berkelanjutan, dan memberi kehidupan bagi generasi kini dan mendatang',
            'mission' => implode("\n", [
                'Menggerakkan dan berperan aktif dalam upaya penyelamatan dan pelestarian Danau Matano.',
                'Mendorong kebijakan dan advokasi lingkungan yang berkeadilan, inklusif, dan berpihak pada keberlanjutan ekosistem Danau Matano.',
                'Meningkatkan kesadaran masyarakat mengenai pentingnya pelestarian Danau Matano.',
                'Membangun jejaring kemitraan dan pemberdayaan komunitas untuk memperkuat gerakan penyelamatan lingkungan.',
            ]),
            'goal' => implode("\n", [
                'Mendukung program pemerintah dan swasta di bidang lingkungan hidup, khususnya pelestarian Danau Matano.',
                'Menyajikan informasi dan data yang akurat mengenai kondisi Danau Matano serta perubahan kualitas lingkungannya.',
                'Melakukan upaya penyelamatan dan pelestarian Danau Matano melalui program revitalisasi, konservasi, rehabilitasi, restorasi dan pengelolaan lingkungan yang berkelanjutan.',
                'Mendorong terbukanya ruang diskusi dan seminar untuk membahas kelestarian Danau Matano, serta aktif melakukan kampanye lingkungan',
            ]),
            'founders' => [
                'title' => 'DEWAN PENDIRI',
                'narrative' => 'TMCC didirikan oleh dua anak pesisir Danau Matano yang menyadari pentingnya ruang bagi masyarakat lokal untuk bergerak secara konsisten, terencana, fokus, dan peduli terhadap kondisi Danau Matano. Kelahiran TMCC dilatarbelakangi oleh kejenuhan karena kerusakan Danau Matano hanya menjadi topik yang menarik untuk dibahas dalam berbagai pertemuan, tanpa diikuti aksi nyata. Selain itu, terdapat rasa bosan dan putus asa karena menunggu kehadiran para pemangku kepentingan untuk benar-benar melakukan aksi dan gerakan penyelamatan di Danau Matano. TMCC juga hadir untuk menjawab kegelisahan atas pemberitaan yang hanya berfokus pada eksposur pesona Danau Matano. Sementara itu, para pendiri menyadari bahwa di danau ini, kerusakan lingkungan sebenarnya sedang terjadi secara terus-menerus dan semakin parah dari waktu ke waktu.',
            ],
        ];
    }
}
