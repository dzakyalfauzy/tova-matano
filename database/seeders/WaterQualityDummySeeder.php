<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\WaterQualityRecord;
use App\Models\WaterSamplingPoint;
use Illuminate\Database\Seeder;

/**
 * Data DUMMY untuk pengecekan tampilan dashboard kualitas air.
 *
 * JANGAN dijalankan di produksi. Semua catatan diberi awalan "DUMMY" pada
 * kolom notes agar mudah dihapus dan tidak tertukar dengan data lapangan.
 * Seeder ini tidak mengubah record historis yang sudah ada.
 */
class WaterQualityDummySeeder extends Seeder
{
    private const NOTE_PREFIX = 'DUMMY';

    public function run(): void
    {
        $points = WaterSamplingPoint::orderBy('id')->get();

        if ($points->isEmpty()) {
            $this->command?->error('Tidak ada titik sampling. Jalankan DatabaseSeeder terlebih dahulu.');

            return;
        }

        // Hanya hapus baris dummy dari eksekusi sebelumnya.
        WaterQualityRecord::where('notes', 'like', self::NOTE_PREFIX.'%')->delete();

        $recordedBy = User::query()->value('id');
        $daysAgo = [84, 70, 56, 42, 35, 28, 21, 14, 7, 1];

        $baselines = [
            ['tss' => 38.0, 'ph' => 7.00, 'tds' => 165.0, 'do' => 5.30, 'temp' => 28.6, 'clarity' => 360],
            ['tss' => 12.0, 'ph' => 7.30, 'tds' => 120.0, 'do' => 6.80, 'temp' => 28.2, 'clarity' => 500],
            ['tss' => 22.0, 'ph' => 7.10, 'tds' => 150.0, 'do' => 5.90, 'temp' => 28.9, 'clarity' => 420],
            ['tss' => 16.0, 'ph' => 7.20, 'tds' => 135.0, 'do' => 6.40, 'temp' => 28.4, 'clarity' => 470],
            ['tss' => 28.0, 'ph' => 7.00, 'tds' => 155.0, 'do' => 5.60, 'temp' => 28.7, 'clarity' => 410],
            ['tss' => 9.0, 'ph' => 7.40, 'tds' => 110.0, 'do' => 7.00, 'temp' => 28.0, 'clarity' => 520],
        ];

        $notes = [
            self::NOTE_PREFIX.' — simulasi tren, bukan hasil lapangan.',
            self::NOTE_PREFIX.' — skenario cuaca cerah.',
            self::NOTE_PREFIX.' — skenario setelah hujan.',
            self::NOTE_PREFIX.' — skenario sedimentasi tinggi.',
        ];

        foreach ($daysAgo as $dayIndex => $days) {
            foreach ($points as $pointIndex => $point) {
                $base = $baselines[$pointIndex % count($baselines)];

                $tss = round(max(2, $base['tss'] + ((($dayIndex * 3 + $pointIndex) % 5) - 2) * 2.5), 1);
                $ph = round($base['ph'] + ((($dayIndex + $pointIndex) % 5) - 2) * 0.05, 2);
                $tds = round($base['tds'] + ((($dayIndex * 2 + $pointIndex) % 5) - 2) * 4, 1);
                $do = round(max(0.5, $base['do'] + ((($dayIndex + $pointIndex * 2) % 5) - 2) * 0.12), 2);
                $temperature = round($base['temp'] + ((($dayIndex * 2 + $pointIndex) % 5) - 2) * 0.15, 1);
                $clarity = round(max(50, $base['clarity'] + ((($dayIndex * 2 + $pointIndex) % 5) - 2) * 8), 0);

                // Skenario sengaja untuk menguji status warning/abnormal.
                if ($pointIndex === 0 && $dayIndex === 2) {
                    $tss = 58.4;
                    $clarity = 280;
                }

                if ($pointIndex === 1 && $dayIndex === 5) {
                    $tss = 45.2;
                }

                if ($pointIndex === 2 && $dayIndex === 4) {
                    $ph = 5.8;
                }

                if ($pointIndex === 3 && $dayIndex === 3) {
                    $ph = 8.7;
                }

                if ($pointIndex === 4 && $dayIndex === 6) {
                    $do = 3.6;
                }

                if ($pointIndex === 5 && $dayIndex === 7) {
                    $do = 4.6;
                }

                WaterQualityRecord::create([
                    'recorded_at' => now()->subDays($days)->setTime(8 + ($pointIndex % 6), 0),
                    'tss' => $tss,
                    'ph' => $ph,
                    'tds' => $tds,
                    'dissolved_oxygen' => $do,
                    'temperature' => $temperature,
                    'clarity_cm' => $clarity,
                    'notes' => $notes[($dayIndex + $pointIndex) % count($notes)],
                    'recorded_by' => $recordedBy,
                    'water_sampling_point_id' => $point->id,
                ]);
            }
        }

        $this->command?->info('Data dummy kualitas air dibuat: '.(count($daysAgo) * $points->count()).' record (notes berawalan "DUMMY").');
    }
}
