import PublicLayout from '@/Layouts/PublicLayout';
import { Head, router } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import {
    CartesianGrid,
    Line,
    LineChart,
    ReferenceArea,
    ReferenceLine,
    ResponsiveContainer,
    Tooltip,
    XAxis,
    YAxis,
} from 'recharts';

const GRID = '#e5e7eb';
const OPERATIONAL_ALERT_COLOR = '#E11D48';
const REGULATORY_COLOR = '#DC2626';

const RESEARCH_BAND_COLORS = [
    { fill: '#1E7F8C', stroke: '#1E7F8C', opacity: 0.16 }, // Teal
    { fill: '#D97706', stroke: '#D97706', opacity: 0.14 }, // Amber
    { fill: '#2563EB', stroke: '#2563EB', opacity: 0.14 }, // Blue
];

function formatValue(value, decimals = 1) {
    if (value === null || value === undefined || value === '') return '-';
    const num = Number(value);
    if (Number.isNaN(num)) return '-';

    return num.toLocaleString('id-ID', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals,
    });
}

function formatDate(value) {
    if (!value) return '-';

    return new Date(value).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
}

function formatDateTime(value) {
    if (!value) return '-';

    return new Date(value).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function ParameterTooltip({ active, payload, parameterKey, parameter, researchBands = [] }) {
    if (!active || !payload || payload.length === 0) return null;
    const point = payload[0].payload;
    const val = Number(point.value);

    // Matching research observations
    const matchingStudies = researchBands.filter((b) => val >= b.min && val <= b.max);

    return (
        <div className="max-w-xs rounded-xl border border-gray-200 bg-white p-3 shadow-xl text-left">
            <p className="text-[11px] font-semibold text-gray-500">{point.dateTimeLabel || point.dateLabel}</p>
            {point.samplingPointName && (
                <p className="text-xs font-medium text-brand-navy mb-1">{point.samplingPointName}</p>
            )}
            <p className="text-base font-bold text-brand-navy">
                {formatValue(point.value, parameter.decimals)}{' '}
                <span className="text-xs font-normal text-gray-500">{parameter.unit}</span>
            </p>

            {matchingStudies.length > 0 ? (
                <div className="mt-2 border-t border-gray-100 pt-1.5 text-[11px] text-gray-600">
                    <p className="font-semibold text-emerald-700">Dalam rentang referensi:</p>
                    <ul className="mt-0.5 space-y-0.5 list-disc list-inside text-[10px]">
                        {matchingStudies.map((s) => (
                            <li key={s.id}>
                                {s.authors.split(',')[0]} ({s.min}–{s.max} {s.unit})
                            </li>
                        ))}
                    </ul>
                </div>
            ) : researchBands.length > 0 ? (
                <div className="mt-2 border-t border-gray-100 pt-1.5 text-[11px] text-gray-500">
                    <p className="italic">Di luar rentang referensi riset ({researchBands.map((s) => `${s.min}–${s.max} ${s.unit}`).join(', ')}).</p>
                </div>
            ) : null}
        </div>
    );
}

function ParameterDot({ cx, cy, parameter }) {
    if (cx === null || cy === null || cx === undefined || cy === undefined) return null;
    return <circle cx={cx} cy={cy} r={3.5} fill={parameter.color} stroke="#fff" strokeWidth={1.5} />;
}

function getDomain(parameter, data, bands = []) {
    const values = data.map((row) => row.value);
    const candidates = [...values];

    bands.forEach((b) => {
        if (typeof b.min === 'number') candidates.push(b.min);
        if (typeof b.max === 'number') candidates.push(b.max);
    });

    if (parameter.operational_alert?.enabled) {
        if (typeof parameter.operational_alert.min === 'number') candidates.push(parameter.operational_alert.min);
        if (typeof parameter.operational_alert.max === 'number') candidates.push(parameter.operational_alert.max);
    }

    const filtered = candidates.filter((val) => typeof val === 'number' && !Number.isNaN(val));
    if (filtered.length === 0) return [0, 1];

    const lowest = Math.min(...filtered);
    const highest = Math.max(...filtered);
    const span = highest - lowest || Math.max(Math.abs(highest) * 0.2, 1);
    const padding = span * 0.15;

    return [Math.max(0, lowest - padding), highest + padding];
}

function ResearchReferencesList({ references = [] }) {
    const [isOpen, setIsOpen] = useState(false);

    if (!references || references.length === 0) {
        return (
            <div className="mt-3 text-xs text-gray-400 italic">
                Belum ada rujukan literatur ilmiah Danau Matano yang terdaftar untuk parameter ini.
            </div>
        );
    }

    return (
        <div className="mt-3 border-t border-gray-100 pt-2 text-xs">
            <button
                type="button"
                onClick={() => setIsOpen(!isOpen)}
                className="flex items-center justify-between w-full font-semibold text-brand-teal hover:text-brand-navy transition-colors py-1 text-left"
            >
                <span className="flex items-center gap-1.5">
                    <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    Lihat Basis Referensi Penelitian ({references.length} Studi)
                </span>
                <span className="text-gray-400">{isOpen ? '▲ Sembunyikan' : '▼ Buka'}</span>
            </button>

            {isOpen && (
                <div className="mt-2 space-y-2.5 max-h-72 overflow-y-auto pr-1">
                    {references.map((ref) => {
                        const isVerified = ref.verification_status === 'verified';
                        const isRemoteSensing = ref.research_type === 'remote_sensing';

                        return (
                            <div
                                key={ref.source_id || ref.id}
                                className={`p-2.5 rounded-lg border text-left text-xs ${
                                    isVerified
                                        ? 'bg-emerald-50/50 border-emerald-200'
                                        : 'bg-gray-50 border-gray-200'
                                }`}
                            >
                                <div className="flex items-start justify-between gap-2">
                                    <h4 className="font-bold text-brand-navy">
                                        {ref.title}{' '}
                                        {ref.publication_year && <span className="font-normal text-gray-500">({ref.publication_year})</span>}
                                    </h4>
                                    <span
                                        className={`shrink-0 px-2 py-0.5 rounded text-[10px] font-bold ${
                                            isVerified
                                                ? 'bg-emerald-100 text-emerald-800'
                                                : 'bg-amber-100 text-amber-800'
                                        }`}
                                    >
                                        {isVerified ? 'Verified' : 'Provisional'}
                                    </span>
                                </div>
                                <p className="text-gray-600 mt-0.5">
                                    <span className="font-medium">Peneliti:</span> {ref.authors} &middot;{' '}
                                    <span className="font-medium">Institusi:</span> {ref.journal_or_institution || '-'}
                                </p>
                                <div className="mt-1.5 flex flex-wrap gap-x-4 gap-y-1 text-gray-700 bg-white/70 p-1.5 rounded border border-gray-100">
                                    <div>
                                        <span className="font-medium">Rentang:</span>{' '}
                                        <span className="font-bold text-brand-navy">
                                            {ref.reported_minimum} – {ref.reported_maximum} {ref.unit}
                                        </span>
                                    </div>
                                    <div>
                                        <span className="font-medium">Metode:</span> {ref.measurement_method || 'Tidak spesifik'}
                                    </div>
                                    <div>
                                        <span className="font-medium">Kedalaman:</span> {ref.sampling_depth || 'Tidak spesifik'}
                                    </div>
                                </div>
                                {isRemoteSensing && (
                                    <p className="mt-1.5 text-[11px] text-amber-700 bg-amber-50 p-1.5 rounded border border-amber-200">
                                        <strong>Catatan Metodologi:</strong> Estimasi berbasis citra satelit (remote sensing). Satuan mg/m³ berbeda dengan mg/L laboratorium dan tidak dikomparasikan langsung.
                                    </p>
                                )}
                                {ref.interpretation_notes && !isRemoteSensing && (
                                    <p className="mt-1.5 text-[11px] text-gray-500 italic">
                                        {ref.interpretation_notes}
                                    </p>
                                )}
                            </div>
                        );
                    })}
                </div>
            )}
        </div>
    );
}

function ParameterChart({ parameterKey, parameter, records }) {
    const data = useMemo(
        () =>
            records
                .filter((record) => record[parameterKey] !== null && record[parameterKey] !== undefined && record[parameterKey] !== '')
                .map((record) => ({
                    timestamp: new Date(record.recorded_at).getTime(),
                    dateLabel: formatDate(record.recorded_at),
                    dateTimeLabel: formatDateTime(record.recorded_at),
                    samplingPointName: record.sampling_point?.name,
                    value: Number(record[parameterKey]),
                }))
                .filter((row) => !Number.isNaN(row.timestamp) && !Number.isNaN(row.value))
                .sort((a, b) => a.timestamp - b.timestamp),
        [records, parameterKey],
    );

    // Extract all valid, direct research observation ranges for this parameter matching its unit
    const researchBands = useMemo(() => {
        const refs = parameter.research_references || [];
        return refs
            .filter((ref) => {
                // Do not compare remote sensing satellite estimates directly on the direct-measurement chart
                if (ref.research_type === 'remote_sensing') return false;
                // Units must match the parameter unit
                if (String(ref.unit).toLowerCase() !== String(parameter.unit).toLowerCase()) return false;
                const min = Number(ref.reported_minimum);
                const max = Number(ref.reported_maximum);
                return !Number.isNaN(min) && !Number.isNaN(max) && min !== null && max !== null;
            })
            .map((ref, idx) => ({
                id: ref.source_id || `band-${idx}`,
                authors: ref.authors,
                title: ref.title,
                year: ref.publication_year,
                min: Number(ref.reported_minimum),
                max: Number(ref.reported_maximum),
                unit: ref.unit,
                colorScheme: RESEARCH_BAND_COLORS[idx % RESEARCH_BAND_COLORS.length],
            }));
    }, [parameter.research_references, parameter.unit]);

    const domain = getDomain(parameter, data, researchBands);
    const opAlert = parameter.operational_alert;
    const regStd = parameter.regulatory_standard;

    const hasResearchBand = researchBands.length > 0;
    const hasOperationalAlert = opAlert?.enabled && (opAlert.min !== null || opAlert.max !== null);
    const hasVerifiedRegulatory = regStd?.enabled && regStd?.verified && (regStd.min !== null || regStd.max !== null);

    return (
        <section className="bg-white rounded-2xl shadow-sm p-5 md:p-6 flex flex-col justify-between border border-gray-100">
            <div>
                <header className="mb-4">
                    <div className="flex items-baseline justify-between gap-3">
                        <h3 className="text-lg font-heading font-bold text-brand-navy">
                            {parameter.label}{' '}
                            <span className="text-sm font-sans font-normal text-gray-400">({parameter.unit})</span>
                        </h3>
                        <span className="text-[11px] font-semibold uppercase tracking-wider text-gray-400">
                            {parameter.full_name}
                        </span>
                    </div>
                    <p className="mt-1 text-xs leading-relaxed text-gray-500">{parameter.description}</p>
                </header>

                {data.length === 0 ? (
                    <div className="flex h-[220px] items-center justify-center rounded-xl border border-dashed border-gray-200 bg-gray-50 px-4 text-center text-sm text-gray-500">
                        Belum ada pengukuran {parameter.label} pada filter yang dipilih.
                    </div>
                ) : (
                    <ResponsiveContainer width="100%" height={240}>
                        <LineChart data={data} margin={{ top: 8, right: 16, left: -8, bottom: 0 }}>
                            <CartesianGrid strokeDasharray="3 3" stroke={GRID} />
                            <XAxis
                                dataKey="timestamp"
                                type="number"
                                scale="time"
                                domain={['dataMin', 'dataMax']}
                                tickFormatter={(value) =>
                                    new Date(value).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })
                                }
                                tick={{ fontSize: 11, fill: '#6B7280' }}
                                minTickGap={20}
                            />
                            <YAxis
                                domain={domain}
                                tick={{ fontSize: 11, fill: '#6B7280' }}
                                width={52}
                                tickFormatter={(value) => formatValue(value, parameter.decimals)}
                            />

                            {/* Research Reference Bands (all compatible direct research studies) */}
                            {researchBands.map((band) => (
                                <ReferenceArea
                                    key={band.id}
                                    y1={band.min}
                                    y2={band.max}
                                    fill={band.colorScheme.fill}
                                    fillOpacity={band.colorScheme.opacity}
                                />
                            ))}

                            {/* Operational Alert lines (if explicitly configured) */}
                            {hasOperationalAlert && opAlert.min !== null && (
                                <ReferenceLine
                                    y={opAlert.min}
                                    stroke={OPERATIONAL_ALERT_COLOR}
                                    strokeDasharray="4 4"
                                    strokeWidth={1.5}
                                />
                            )}
                            {hasOperationalAlert && opAlert.max !== null && (
                                <ReferenceLine
                                    y={opAlert.max}
                                    stroke={OPERATIONAL_ALERT_COLOR}
                                    strokeDasharray="4 4"
                                    strokeWidth={1.5}
                                />
                            )}

                            {/* Regulatory Standard line (only if verified) */}
                            {hasVerifiedRegulatory && regStd.min !== null && (
                                <ReferenceLine
                                    y={regStd.min}
                                    stroke={REGULATORY_COLOR}
                                    strokeDasharray="6 4"
                                    strokeWidth={1.5}
                                />
                            )}
                            {hasVerifiedRegulatory && regStd.max !== null && (
                                <ReferenceLine
                                    y={regStd.max}
                                    stroke={REGULATORY_COLOR}
                                    strokeDasharray="6 4"
                                    strokeWidth={1.5}
                                />
                            )}

                            <Tooltip
                                content={
                                    <ParameterTooltip
                                        parameterKey={parameterKey}
                                        parameter={parameter}
                                        researchBands={researchBands}
                                    />
                                }
                            />
                            <Line
                                type="monotone"
                                dataKey="value"
                                stroke={parameter.color}
                                strokeWidth={2}
                                connectNulls={false}
                                isAnimationActive={false}
                                dot={<ParameterDot parameter={parameter} />}
                                activeDot={{ r: 5 }}
                            />
                        </LineChart>
                    </ResponsiveContainer>
                )}

                {/* Scientifically Transparent Chart Legend */}
                <div className="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1.5 text-[11px] text-gray-500">
                    <span className="inline-flex items-center gap-1.5 font-medium">
                        <span className="inline-block h-0.5 w-4" style={{ backgroundColor: parameter.color }} />
                        Data Pengukuran
                    </span>

                    {researchBands.map((band) => (
                        <span
                            key={band.id}
                            className="inline-flex items-center gap-1.5 font-medium text-gray-700"
                            title={`Rentang observasi studi: ${band.authors} (${band.min}–${band.max} ${band.unit})`}
                        >
                            <span
                                className="inline-block h-3 w-4 rounded-sm border"
                                style={{
                                    backgroundColor: band.colorScheme.fill,
                                    borderColor: band.colorScheme.stroke,
                                    opacity: 0.8,
                                }}
                            />
                            Rentang Riset ({band.authors.split(',')[0]} {band.year ? `'${String(band.year).slice(-2)}` : ''}: {band.min}–{band.max} {band.unit})
                        </span>
                    ))}

                    {hasOperationalAlert && (
                        <span
                            className="inline-flex items-center gap-1.5 text-rose-600 font-medium"
                            title={opAlert.justification || 'Ambang operasional internal'}
                        >
                            <span
                                className="inline-block h-0.5 w-4"
                                style={{ backgroundColor: OPERATIONAL_ALERT_COLOR }}
                            />
                            Ambang Operasional
                        </span>
                    )}

                    {hasVerifiedRegulatory && (
                        <span
                            className="inline-flex items-center gap-1.5 text-red-600 font-medium"
                            title={regStd.standard_name || 'Baku mutu regulasi resmi'}
                        >
                            <span className="inline-block h-0.5 w-4" style={{ backgroundColor: REGULATORY_COLOR }} />
                            Baku Mutu Regulasi
                        </span>
                    )}

                    {!hasResearchBand && !hasOperationalAlert && !hasVerifiedRegulatory && (
                        <span className="text-gray-400 italic">
                            Belum ada rentang referensi terverifikasi untuk perbandingan otomatis.
                        </span>
                    )}
                </div>
            </div>

            {/* Compact Expandable Research Sources */}
            <ResearchReferencesList references={parameter.research_references} />
        </section>
    );
}

export default function WaterQuality({
    records,
    paginatedRecords,
    samplingPoints,
    summary,
    kpis,
    waterQualityConfig,
    filters,
    perPageOptions,
    settings,
}) {
    const parameters = Object.entries(summary);
    const lastPage = paginatedRecords.last_page;
    const currentPage = paginatedRecords.current_page;

    const pageNumbers = useMemo(() => {
        const windowSize = 5;
        let start = Math.max(1, currentPage - 2);
        const end = Math.min(lastPage, start + windowSize - 1);
        start = Math.max(1, end - windowSize + 1);

        return Array.from({ length: Math.max(end - start + 1, 0) }, (_, index) => start + index);
    }, [currentPage, lastPage]);

    const visit = (params) => {
        router.get('/kualitas-air', params, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    };

    const changeFilters = (next) => {
        visit({
            sampling_point: next.sampling_point ?? filters.sampling_point,
            range: next.range ?? filters.range,
            per_page: filters.per_page,
        });
    };

    const changePerPage = (value) => {
        visit({
            sampling_point: filters.sampling_point,
            range: filters.range,
            per_page: value,
        });
    };

    const goToPage = (page) => {
        visit({
            sampling_point: filters.sampling_point,
            range: filters.range,
            per_page: filters.per_page,
            page,
        });
    };

    return (
        <PublicLayout settings={settings}>
            <Head title="Penilaian Kualitas Air Berbasis Riset Danau Matano | TMCC" />

            <div className="bg-brand-navy py-16 md:py-24 text-center border-b-4 border-brand-teal">
                <div className="max-w-4xl mx-auto px-4">
                    <h1 className="text-3xl md:text-5xl font-heading font-bold text-brand-gold mb-3">
                        Kualitas Air Danau Matano
                    </h1>
                    <p className="text-lg md:text-xl text-teal-100 font-medium tracking-wide mb-3">
                        RESEARCH-BASED WATER QUALITY ASSESSMENT – LAKE MATANO
                    </p>
                    <p className="text-sm md:text-base text-gray-300 max-w-2xl mx-auto leading-relaxed">
                        Sistem penilaian transparan berbasis studi ilmiah Danau Matano, membedakan referensi penelitian,
                        ambang operasional, dan baku mutu regulasi.
                    </p>
                </div>
            </div>

            <div className="bg-brand-paper py-12">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

                    {/* Explanatory Note */}
                    <div className="rounded-2xl border-l-4 border-brand-teal bg-white p-5 shadow-sm text-sm text-gray-700 leading-relaxed">
                        <div className="flex items-start gap-3">
                            <span className="p-2 bg-teal-50 text-brand-teal rounded-lg shrink-0">
                                <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                            <div>
                                <h2 className="font-heading font-bold text-brand-navy text-base mb-1">
                                    Kerangka Penilaian Ilmiah Kualitas Air Danau Matano
                                </h2>
                                <p className="text-gray-600 text-xs sm:text-sm">
                                    {waterQualityConfig?.explanatory_note ||
                                        'Penilaian kualitas air pada dashboard ini mengacu pada hasil penelitian yang dipublikasikan mengenai Danau Matano. Rentang hasil penelitian merupakan referensi kondisi yang pernah diamati, bukan otomatis baku mutu regulasi atau batas keamanan ekologis. Interpretasi juga mempertimbangkan lokasi, kedalaman, waktu, dan metode pengukuran.'}
                                </p>
                            </div>
                        </div>
                    </div>

                    {/* Filters */}
                    <div className="bg-white rounded-2xl shadow-sm p-6 flex flex-col md:flex-row gap-4 items-center justify-between border border-gray-100">
                        <div className="w-full md:w-auto flex flex-col sm:flex-row gap-4">
                            <div>
                                <label htmlFor="sampling-point" className="block text-sm font-medium text-gray-700 mb-1">
                                    Titik Pengambilan Sample
                                </label>
                                <select
                                    id="sampling-point"
                                    value={String(filters.sampling_point)}
                                    onChange={(e) => changeFilters({ sampling_point: e.target.value })}
                                    className="border-gray-300 rounded-lg shadow-sm focus:border-brand-teal focus:ring-brand-teal w-full min-w-[200px]"
                                >
                                    <option value="all">Semua Titik Sample</option>
                                    {samplingPoints &&
                                        samplingPoints.map((point) => (
                                            <option key={point.id} value={String(point.id)}>
                                                {point.name}
                                            </option>
                                        ))}
                                </select>
                            </div>
                            <div>
                                <label htmlFor="time-range" className="block text-sm font-medium text-gray-700 mb-1">
                                    Rentang Waktu
                                </label>
                                <select
                                    id="time-range"
                                    value={filters.range}
                                    onChange={(e) => changeFilters({ range: e.target.value })}
                                    className="border-gray-300 rounded-lg shadow-sm focus:border-brand-teal focus:ring-brand-teal w-full min-w-[150px]"
                                >
                                    <option value="all">Semua Waktu</option>
                                    <option value="week">1 Minggu Terakhir</option>
                                    <option value="month">1 Bulan Terakhir</option>
                                    <option value="year">1 Tahun Terakhir</option>
                                </select>
                            </div>
                        </div>
                        <div className="text-sm text-gray-500 font-medium">
                            {paginatedRecords.total} pengukuran pada filter ini
                        </div>
                    </div>

                    {/* Summary Section */}
                    <div>
                        <div className="mb-4 flex flex-col gap-1 sm:flex-row sm:items-baseline sm:justify-between">
                            <h2 className="text-2xl font-heading font-bold text-brand-navy">Ringkasan Parameter</h2>
                            <p className="text-xs text-gray-500">
                                Rata-rata dan jumlah pengukuran kualitas air Danau Matano pada filter aktif.
                            </p>
                        </div>

                        {/* Parameter Summary Cards */}
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                            {parameters.map(([key, item]) => (
                                <div
                                    key={key}
                                    className="bg-white rounded-2xl shadow-sm p-6 border-t-4 border border-gray-100 flex flex-col justify-between"
                                    style={{ borderTopColor: item.color }}
                                >
                                    <div>
                                        <p className="text-xs font-bold uppercase tracking-wider text-gray-500">
                                            {item.label}
                                        </p>
                                        <p className="text-xs text-gray-400 mt-0.5">{item.full_name}</p>

                                        <p className="mt-4 text-3xl md:text-4xl font-heading font-bold text-brand-navy">
                                            {formatValue(item.average, item.decimals)}
                                            <span className="ml-1.5 text-base font-sans font-normal text-gray-400">
                                                {item.unit}
                                            </span>
                                        </p>
                                    </div>

                                    <div className="mt-4 pt-3 border-t border-gray-100">
                                        <p className="text-xs text-gray-500">
                                            Rata-rata &middot; {item.count ?? 0} pengukuran
                                        </p>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* Six individual parameter charts */}
                    <div>
                        <div className="mb-4">
                            <h2 className="text-2xl font-heading font-bold text-brand-navy">
                                Tren Parameter Kualitas Air
                            </h2>
                            <p className="text-xs text-gray-500 mt-1">
                                Grafik tren individual dengan rentang referensi riset Danau Matano per studi.
                            </p>
                        </div>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {parameters.map(([key, item]) => (
                                <ParameterChart
                                    key={key}
                                    parameterKey={key}
                                    parameter={item}
                                    records={records}
                                />
                            ))}
                        </div>
                    </div>

                    {/* Measurement History */}
                    <div className="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
                        <div className="flex flex-col gap-3 px-6 py-4 border-b border-gray-100 sm:flex-row sm:items-center sm:justify-between">
                            <h2 className="text-xl font-heading font-bold text-brand-navy">Riwayat Pengukuran</h2>
                            <div className="flex items-center gap-2 text-sm text-gray-600">
                                <label htmlFor="per-page">Baris</label>
                                <select
                                    id="per-page"
                                    value={String(filters.per_page)}
                                    onChange={(e) => changePerPage(e.target.value)}
                                    className="border-gray-300 rounded-lg py-1.5 pl-2 pr-8 text-sm focus:border-brand-teal focus:ring-brand-teal"
                                >
                                    {perPageOptions.map((option) => (
                                        <option key={option} value={String(option)}>
                                            {option}
                                        </option>
                                    ))}
                                </select>
                            </div>
                        </div>

                        {paginatedRecords.total === 0 ? (
                            <div className="px-6 py-12 text-center text-gray-500">
                                Belum ada data kualitas air yang tersedia untuk filter yang dipilih.
                            </div>
                        ) : (
                            <>
                                <div className="overflow-x-auto">
                                    <table className="w-full text-sm">
                                        <thead className="bg-gray-50 text-left">
                                            <tr>
                                                <th className="px-6 py-3 font-semibold text-gray-600 whitespace-nowrap">
                                                    Tanggal
                                                </th>
                                                <th className="px-6 py-3 font-semibold text-gray-600 whitespace-nowrap">
                                                    Titik Sample
                                                </th>
                                                <th className="px-6 py-3 font-semibold text-gray-600 whitespace-nowrap">
                                                    TSS (mg/L)
                                                </th>
                                                <th className="px-6 py-3 font-semibold text-gray-600 whitespace-nowrap">
                                                    pH
                                                </th>
                                                <th className="px-6 py-3 font-semibold text-gray-600 whitespace-nowrap">
                                                    TDS (mg/L)
                                                </th>
                                                <th className="px-6 py-3 font-semibold text-gray-600 whitespace-nowrap">
                                                    DO (mg/L)
                                                </th>
                                                <th className="px-6 py-3 font-semibold text-gray-600 whitespace-nowrap">
                                                    Suhu (°C)
                                                </th>
                                                <th className="px-6 py-3 font-semibold text-gray-600 whitespace-nowrap">
                                                    Kejernihan (cm)
                                                </th>
                                                <th className="px-6 py-3 font-semibold text-gray-600">Catatan</th>
                                            </tr>
                                        </thead>
                                        <tbody className="divide-y divide-gray-100">
                                            {paginatedRecords.data.map((record) => (
                                                <tr key={record.id} className="hover:bg-gray-50 transition-colors">
                                                    <td className="px-6 py-3 text-gray-700 whitespace-nowrap">
                                                        {formatDate(record.recorded_at)}
                                                    </td>
                                                    <td className="px-6 py-3 text-gray-700 whitespace-nowrap">
                                                        {record.sampling_point ? record.sampling_point.name : '-'}
                                                    </td>
                                                    <td className="px-6 py-3 text-gray-700 whitespace-nowrap">
                                                        {formatValue(record.tss, 1)}
                                                    </td>
                                                    <td className="px-6 py-3 text-gray-700 whitespace-nowrap">
                                                        {formatValue(record.ph, 2)}
                                                    </td>
                                                    <td className="px-6 py-3 text-gray-700 whitespace-nowrap">
                                                        {formatValue(record.tds, 1)}
                                                    </td>
                                                    <td className="px-6 py-3 text-gray-700 whitespace-nowrap">
                                                        {formatValue(record.dissolved_oxygen, 2)}
                                                    </td>
                                                    <td className="px-6 py-3 text-gray-700 whitespace-nowrap">
                                                        {formatValue(record.temperature, 1)}
                                                    </td>
                                                    <td className="px-6 py-3 text-gray-700 whitespace-nowrap">
                                                        {formatValue(record.clarity_cm, 0)}
                                                    </td>
                                                    <td
                                                        className="px-6 py-3 text-gray-500 max-w-xs truncate"
                                                        title={record.notes || ''}
                                                    >
                                                        {record.notes || '-'}
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>

                                <div className="flex flex-col gap-3 border-t border-gray-100 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                                    <p className="text-sm text-gray-500">
                                        Menampilkan {paginatedRecords.from ?? 0}–{paginatedRecords.to ?? 0} dari{' '}
                                        {paginatedRecords.total} pengukuran
                                        <span className="hidden sm:inline">
                                            {' '}
                                            &middot; Halaman {currentPage} dari {lastPage}
                                        </span>
                                    </p>
                                    <nav className="flex items-center gap-1" aria-label="Navigasi halaman riwayat pengukuran">
                                        <button
                                            type="button"
                                            onClick={() => goToPage(currentPage - 1)}
                                            disabled={currentPage <= 1}
                                            className="rounded-lg border border-gray-200 px-3 py-1.5 text-sm font-semibold text-gray-600 transition-colors hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40"
                                        >
                                            Sebelumnya
                                        </button>
                                        {pageNumbers.map((page) => (
                                            <button
                                                key={page}
                                                type="button"
                                                onClick={() => goToPage(page)}
                                                aria-current={page === currentPage ? 'page' : undefined}
                                                className={`min-w-[2rem] rounded-lg border px-2 py-1.5 text-sm font-semibold transition-colors ${
                                                    page === currentPage
                                                        ? 'border-brand-teal bg-brand-teal text-white'
                                                        : 'border-gray-200 text-gray-600 hover:bg-gray-50'
                                                }`}
                                            >
                                                {page}
                                            </button>
                                        ))}
                                        <button
                                            type="button"
                                            onClick={() => goToPage(currentPage + 1)}
                                            disabled={currentPage >= lastPage}
                                            className="rounded-lg border border-gray-200 px-3 py-1.5 text-sm font-semibold text-gray-600 transition-colors hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40"
                                        >
                                            Berikutnya
                                        </button>
                                    </nav>
                                </div>
                            </>
                        )}
                    </div>

                </div>
            </div>
        </PublicLayout>
    );
}
