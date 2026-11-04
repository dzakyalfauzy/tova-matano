<?php

/**
 * Konfigurasi tunggal parameter & ambang batas kualitas air.
 *
 * PENTING — status verifikasi:
 * Nilai baku mutu di bawah ini dirujuk dari PP No. 22 Tahun 2021, Lampiran VI
 * (Baku Mutu Air Nasional) untuk Kelas II. Dokumen resmi belum diverifikasi
 * ulang di dalam repositori ini (`verified` = false). Sebelum dipakai untuk
 * keputusan resmi, konfirmasi kelas peruntukan Danau Matano dan nilai ambang
 * dengan dinas lingkungan hidup setempat.
 *
 * `warning_min` / `warning_max` adalah ambang operasional internal TMCC
 * (bukan baku mutu resmi) untuk memberi peringatan dini.
 *
 * `threshold_type`:
 * - maximum  : nilai tidak boleh melebihi `max`
 * - minimum  : nilai tidak boleh di bawah `min`
 * - range    : nilai harus berada di antara `min` dan `max`
 * - deviation: baku mutu berupa deviasi dari kondisi alamiah, bukan angka absolut
 * - none     : tidak ada baku mutu numerik yang dapat diverifikasi
 */
return [

    'standard' => [
        'name' => 'PP No. 22 Tahun 2021 — Lampiran VI (Baku Mutu Air Nasional)',
        'class' => 'Kelas II',
        'verified' => false,
        'note' => 'Kelas peruntukan Danau Matano serta nilai ambang perlu dikonfirmasi ke instansi lingkungan hidup. Nilai pada konfigurasi ini tidak boleh diklaim sebagai angka resmi sebelum diverifikasi.',
    ],

    'parameters' => [

        'tss' => [
            'label' => 'TSS',
            'full_name' => 'Total Suspended Solids',
            'unit' => 'mg/L',
            'description' => 'Padatan tersuspensi total. Nilai lebih rendah umumnya menunjukkan lebih sedikit partikel tersuspensi di kolom air.',
            'decimals' => 1,
            'color' => '#1E7F8C',
            'threshold_type' => 'maximum',
            'min' => null,
            'max' => 50,
            'warning_min' => null,
            'warning_max' => 40,
            'source' => 'PP No. 22 Tahun 2021, Lampiran VI — Kelas II (baku mutu 50 mg/L)',
            'verified' => false,
        ],

        'ph' => [
            'label' => 'pH',
            'full_name' => 'Potential of Hydrogen',
            'unit' => 'pH',
            'description' => 'Tingkat keasaman/kebasaan air. Nilai di luar rentang 6–9 berada di luar baku mutu dan perlu ditindaklanjuti.',
            'decimals' => 2,
            'color' => '#123A52',
            'threshold_type' => 'range',
            'min' => 6,
            'max' => 9,
            'warning_min' => 6.5,
            'warning_max' => 8.5,
            'source' => 'PP No. 22 Tahun 2021, Lampiran VI — Kelas II (rentang 6–9)',
            'verified' => false,
        ],

        'tds' => [
            'label' => 'TDS',
            'full_name' => 'Total Dissolved Solids',
            'unit' => 'mg/L',
            'description' => 'Padatan terlarut total. Nilai tinggi dapat menandakan pengaruh limpasan atau intrusi air asin.',
            'decimals' => 1,
            'color' => '#2F855A',
            'threshold_type' => 'maximum',
            'min' => null,
            'max' => 1000,
            'warning_min' => null,
            'warning_max' => 800,
            'source' => 'PP No. 22 Tahun 2021, Lampiran VI — Kelas II (baku mutu 1000 mg/L)',
            'verified' => false,
        ],

        'dissolved_oxygen' => [
            'label' => 'DO',
            'full_name' => 'Dissolved Oxygen',
            'unit' => 'mg/L',
            'description' => 'Oksigen terlarut. Nilai di bawah ambang minimum menunjukkan risiko hipoksia bagi biota air.',
            'decimals' => 2,
            'color' => '#2B6CB0',
            'threshold_type' => 'minimum',
            'min' => 4,
            'max' => null,
            'warning_min' => 5,
            'warning_max' => null,
            'source' => 'PP No. 22 Tahun 2021, Lampiran VI — Kelas II (baku mutu minimum 4 mg/L)',
            'verified' => false,
        ],

        'temperature' => [
            'label' => 'Suhu',
            'full_name' => 'Water Temperature',
            'unit' => '°C',
            'description' => 'Suhu air. Baku mutu berupa deviasi maksimum 3 °C dari kondisi alamiah, bukan rentang absolut. Baseline alamiah Danau Matano belum tersedia, sehingga status tidak dinilai otomatis.',
            'decimals' => 1,
            'color' => '#B7791F',
            'threshold_type' => 'deviation',
            'min' => null,
            'max' => null,
            'warning_min' => null,
            'warning_max' => null,
            'max_deviation' => 3,
            'baseline' => null,
            'source' => 'PP No. 22 Tahun 2021, Lampiran VI — Kelas II (deviasi maksimum 3 °C dari kondisi alamiah)',
            'verified' => false,
        ],

        'clarity_cm' => [
            'label' => 'Kejernihan',
            'full_name' => 'Water Clarity / Transparency',
            'unit' => 'cm',
            'description' => 'Kecerahan Secchi (1 m = 100 cm) yang menggambarkan kedalaman cahaya menembus air. PP No. 22 Tahun 2021 tidak menetapkan angka baku mutu kecerahan untuk danau pada tabel baku mutu air nasional; kriteria trofik danau memerlukan verifikasi terhadap regulasi/izin lingkungan setempat.',
            'decimals' => 0,
            'color' => '#E3A23A',
            'threshold_type' => 'none',
            'min' => null,
            'max' => null,
            'warning_min' => null,
            'warning_max' => null,
            'source' => 'Tidak ada baku mutu numerik yang terverifikasi untuk kecerahan danau',
            'verified' => false,
        ],

    ],

];
