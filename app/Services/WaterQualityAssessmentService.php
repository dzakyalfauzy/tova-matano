<?php

namespace App\Services;

use App\Models\WaterQualityResearchReference;
use Illuminate\Support\Collection;

class WaterQualityAssessmentService
{
    /**
     * Cache active references per request.
     *
     * @var Collection<int, WaterQualityResearchReference>|null
     */
    protected ?Collection $cachedReferences = null;

    /**
     * Get active research references (loaded from database with fallback to config).
     *
     * @return Collection<int, WaterQualityResearchReference>
     */
    public function getActiveReferences(): Collection
    {
        if ($this->cachedReferences !== null) {
            return $this->cachedReferences;
        }

        $this->cachedReferences = WaterQualityResearchReference::query()
            ->orderBy('publication_year')
            ->orderBy('id')
            ->get();

        return $this->cachedReferences;
    }

    /**
     * Clear cached references (useful for tests).
     */
    public function clearCache(): void
    {
        $this->cachedReferences = null;
    }

    /**
     * Get verified, comparable research reference for a given parameter.
     * Only verified, active, non-remote-sensing references matching unit and direct measurement are eligible.
     */
    public function getEligibleComparisonReference(string $parameterKey, string $targetUnit = 'mg/L'): ?WaterQualityResearchReference
    {
        return $this->getActiveReferences()
            ->where('parameter', $parameterKey)
            ->where('verification_status', 'verified')
            ->where('is_active_for_comparison', true)
            ->filter(function (WaterQualityResearchReference $ref) use ($targetUnit) {
                // Remote sensing estimates are not directly comparable to laboratory measurements
                if ($ref->research_type === 'remote_sensing') {
                    return false;
                }

                // Units must match directly (or be handled through verified conversion)
                if (strtolower($ref->unit) !== strtolower($targetUnit)) {
                    return false;
                }

                return $ref->reported_minimum !== null && $ref->reported_maximum !== null;
            })
            ->first();
    }

    /**
     * Assess a single parameter measurement against the research reference framework.
     *
     * Possible statuses:
     * - no_data: measurement value is null/empty
     * - operational_alert: explicitly configured operational alert triggered
     * - not_assessed: parameter requires local baseline or context that is missing (e.g. Temperature)
     * - insufficient_reference: no verified, compatible research reference available
     * - within_reference: value is within verified research observation range
     * - outside_reference: value is outside verified research observation range
     *
     * @param  array<string, mixed>  $parameterConfig
     * @param  array<string, mixed>  $context  e.g. ['depth' => ..., 'sampling_point_id' => ...]
     * @return array{
     *     status: string,
     *     status_key: string,
     *     label: string,
     *     label_id: string,
     *     badge: string,
     *     dot: string,
     *     message: string,
     *     reference: ?array<string, mixed>,
     *     operational_alert: ?array<string, mixed>,
     *     regulatory_standard: ?array<string, mixed>
     * }
     */
    public function assess(string $parameterKey, ?float $value, array $parameterConfig = [], array $context = []): array
    {
        $statusDefinitions = config('water_quality.assessment_statuses', []);

        if ($value === null) {
            $def = $statusDefinitions['no_data'] ?? [
                'key' => 'no_data',
                'label' => 'No Data',
                'label_id' => 'Tidak Ada Data',
                'badge' => 'bg-gray-100 text-gray-500 border border-gray-200',
                'dot' => '#9CA3AF',
                'description' => 'Belum ada data pengukuran pada filter yang dipilih.',
            ];

            return [
                'status' => 'no_data',
                'status_key' => 'no_data',
                'label' => $def['label'],
                'label_id' => $def['label_id'],
                'badge' => $def['badge'],
                'dot' => $def['dot'],
                'message' => $def['description'],
                'reference' => null,
                'operational_alert' => null,
                'regulatory_standard' => null,
            ];
        }

        // 1. Check Operational Alert if explicitly configured with documented justification
        $operationalAlert = $parameterConfig['operational_alert'] ?? null;
        if (
            is_array($operationalAlert)
            && ($operationalAlert['enabled'] ?? false) === true
            && ! empty($operationalAlert['justification'])
        ) {
            $opMin = $operationalAlert['min'] ?? null;
            $opMax = $operationalAlert['max'] ?? null;

            $isAlert = false;
            if ($opMin !== null && $value < $opMin) {
                $isAlert = true;
            }
            if ($opMax !== null && $value > $opMax) {
                $isAlert = true;
            }

            if ($isAlert) {
                $def = $statusDefinitions['operational_alert'];

                return [
                    'status' => 'operational_alert',
                    'status_key' => 'operational_alert',
                    'label' => $def['label'],
                    'label_id' => $def['label_id'],
                    'badge' => $def['badge'],
                    'dot' => $def['dot'],
                    'message' => 'Nilai pengukuran memicu peringatan operasional internal: '.$operationalAlert['justification'],
                    'reference' => null,
                    'operational_alert' => $operationalAlert,
                    'regulatory_standard' => null,
                ];
            }
        }

        // 2. Check if baseline is required (e.g. Temperature)
        $baselineRequired = $parameterConfig['baseline_required'] ?? false;
        $validatedBaselineExists = $parameterConfig['validated_baseline_exists'] ?? false;

        if ($baselineRequired && ! $validatedBaselineExists) {
            $def = $statusDefinitions['not_assessed'];

            return [
                'status' => 'not_assessed',
                'status_key' => 'not_assessed',
                'label' => $def['label'],
                'label_id' => $def['label_id'],
                'badge' => $def['badge'],
                'dot' => $def['dot'],
                'message' => 'Suhu memerlukan penilaian terhadap baseline musiman dan kedalaman lokal yang tervalidasi. Saat ini baseline belum tersedia.',
                'reference' => null,
                'operational_alert' => null,
                'regulatory_standard' => null,
            ];
        }

        // 3. Find verified, compatible research reference
        $unit = $parameterConfig['unit'] ?? 'mg/L';
        $reference = $this->getEligibleComparisonReference($parameterKey, $unit);

        if (! $reference) {
            $def = $statusDefinitions['insufficient_reference'];

            $message = 'Referensi penelitian ilmiah untuk parameter ini belum terverifikasi penuh atau data observasi hanya bersifat provisional.';
            if ($parameterKey === 'clarity_cm') {
                $message = 'Kecerahan Secchi Danau Matano dipengaruhi dinamika perairan ultra-oligotrofik; tidak diklasifikasikan sebagai tercemar tanpa bukti trofik tervalidasi.';
            }

            return [
                'status' => 'insufficient_reference',
                'status_key' => 'insufficient_reference',
                'label' => $def['label'],
                'label_id' => $def['label_id'],
                'badge' => $def['badge'],
                'dot' => $def['dot'],
                'message' => $message,
                'reference' => null,
                'operational_alert' => null,
                'regulatory_standard' => null,
            ];
        }

        // 4. Compare with verified reference range
        $min = (float) $reference->reported_minimum;
        $max = (float) $reference->reported_maximum;

        $refData = [
            'source_id' => $reference->source_id,
            'title' => $reference->title,
            'authors' => $reference->authors,
            'publication_year' => $reference->publication_year,
            'journal_or_institution' => $reference->journal_or_institution,
            'min' => $min,
            'max' => $max,
            'unit' => $reference->unit,
            'sampling_location' => $reference->sampling_location,
            'sampling_depth' => $reference->sampling_depth,
            'measurement_method' => $reference->measurement_method,
            'research_type' => $reference->research_type,
            'verification_status' => $reference->verification_status,
            'interpretation_notes' => $reference->interpretation_notes,
        ];

        if ($value >= $min && $value <= $max) {
            $def = $statusDefinitions['within_reference'];

            return [
                'status' => 'within_reference',
                'status_key' => 'within_reference',
                'label' => $def['label'],
                'label_id' => $def['label_id'],
                'badge' => $def['badge'],
                'dot' => $def['dot'],
                'message' => "Nilai ({$value} {$unit}) berada dalam rentang referensi penelitian {$reference->authors} ({$min}–{$max} {$unit}).",
                'reference' => $refData,
                'operational_alert' => null,
                'regulatory_standard' => null,
            ];
        }

        $def = $statusDefinitions['outside_reference'];

        return [
            'status' => 'outside_reference',
            'status_key' => 'outside_reference',
            'label' => $def['label'],
            'label_id' => $def['label_id'],
            'badge' => $def['badge'],
            'dot' => $def['dot'],
            'message' => 'Nilai berada di luar rentang yang dilaporkan dalam referensi penelitian. Diperlukan pemeriksaan lebih lanjut terhadap lokasi, kedalaman, waktu, dan metode pengukuran.',
            'reference' => $refData,
            'operational_alert' => null,
            'regulatory_standard' => null,
        ];
    }
}
