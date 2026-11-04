import PublicLayout from '@/Layouts/PublicLayout';
import { Head, router } from '@inertiajs/react';
import { useMemo } from 'react';
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

const STATUS_META = {
    normal: { label: 'Normal', badge: 'bg-green-100 text-green-700', dot: '#16A34A' },
    warning: { label: 'Perhatian', badge: 'bg-amber-100 text-amber-800', dot: '#D97706' },
    abnormal: { label: 'Tidak Normal', badge: 'bg-red-100 text-red-700', dot: '#DC2626' },
    no_data: { label: 'Tidak Ada Data', badge: 'bg-gray-100 text-gray-600', dot: '#9CA3AF' },
    unassessed: { label: 'Belum Dinilai', badge: 'bg-slate-100 text-slate-600', dot: '#64748B' },
};

const DASH = '#DC2626';
const WARNING = '#D97706';
const GRID = '#e5e7eb';

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

function evaluateStatus(parameter, value) {
    if (value === null || value === undefined || value === '') return 'no_data';
    const num = Number(value);
    if (Number.isNaN(num)) return 'no_data';

    const { threshold_type: type, min, max, warning_min: warningMin, warning_max: warningMax } = parameter;

    if (type === 'maximum') {
        if (max !== null && num > max) return 'abnormal';
        if (warningMax !== null && num > warningMax) return 'warning';
        return 'normal';
    }

    if (type === 'minimum') {
        if (min !== null && num < min) return 'abnormal';
        if (warningMin !== null && num < warningMin) return 'warning';
        return 'normal';
    }

    if (type === 'range') {
        if ((min !== null && num < min) || (max !== null && num > max)) return 'abnormal';
        if ((warningMin !== null && num < warningMin) || (warningMax !== null && num > warningMax)) return 'warning';
        return 'normal';
    }

    if (type === 'deviation') {
        if (parameter.baseline === null || parameter.max_deviation === null) return 'unassessed';
        const deviation = Math.abs(num - Number(parameter.baseline));
        if (deviation > parameter.max_deviation) return 'abnormal';
        if (deviation > parameter.max_deviation * 0.8) return 'warning';
        return 'normal';
    }

    return 'unassessed';
}

function StatusBadge({ status, small = false }) {
    const meta = STATUS_META[status] || STATUS_META.unassessed;

    return (
        <span className={`inline-flex items-center gap-1.5 rounded-full font-bold ${meta.badge} ${small ? 'px-2 py-0.5 text-[10px]' : 'px-2.5 py-1 text-xs'}`}>
            <span className="h-1.5 w-1.5 rounded-full" style={{ backgroundColor: meta.dot }} />
            {meta.label}
        </span>
    );
}

function ParameterTooltip({ active, payload, parameter }) {
    if (!active || !payload || payload.length === 0) return null;
    const point = payload[0].payload;
    const status = evaluateStatus(parameter, point.value);

    return (
        <div className="rounded-xl border border-gray-100 bg-white px-3 py-2 shadow-lg">
            <p className="text-xs font-semibold text-gray-500">{point.dateLabel}</p>
            <p className="text-sm font-bold text-brand-navy">
                {formatValue(point.value, parameter.decimals)} <span className="font-normal text-gray-500">{parameter.unit}</span>
            </p>
            <div className="mt-1">
                <StatusBadge status={status} small />
            </div>
        </div>
    );
}

function ParameterDot({ cx, cy, payload, parameter }) {
    if (cx === null || cy === null || cx === undefined || cy === undefined) return null;
    const status = evaluateStatus(parameter, payload?.value);
    const meta = STATUS_META[status] || STATUS_META.unassessed;
    const fill = status === 'normal' ? parameter.color : meta.dot;

    return <circle cx={cx} cy={cy} r={3.2} fill={fill} stroke="#fff" strokeWidth={1.2} />;
}

function getDomain(parameter, data) {
    const values = data.map((row) => row.value);
    const candidates = [
        ...values,
        parameter.min,
        parameter.max,
        parameter.warning_min,
        parameter.warning_max,
    ].filter((value) => typeof value === 'number' && !Number.isNaN(value));

    if (candidates.length === 0) return [0, 1];

    const lowest = Math.min(...candidates);
    const highest = Math.max(...candidates);
    const span = highest - lowest || Math.max(Math.abs(highest) * 0.2, 1);
    const padding = span * 0.12;

    return [lowest - padding, highest + padding];
}

function ParameterChart({ parameterKey, parameter, records }) {
    const data = useMemo(
        () =>
            records
                .filter((record) => record[parameterKey] !== null && record[parameterKey] !== undefined && record[parameterKey] !== '')
                .map((record) => ({
                    timestamp: new Date(record.recorded_at).getTime(),
                    dateLabel: formatDate(record.recorded_at),
                    value: Number(record[parameterKey]),
                }))
                .filter((row) => !Number.isNaN(row.timestamp) && !Number.isNaN(row.value))
                .sort((a, b) => a.timestamp - b.timestamp),
        [records, parameterKey],
    );

    const domain = getDomain(parameter, data);
    const hasRegulatoryLine = parameter.min !== null || parameter.max !== null;
    const hasWarningLine = parameter.warning_min !== null || parameter.warning_max !== null;

    return (
        <section className="bg-white rounded-2xl shadow-sm p-5 md:p-6">
            <header className="mb-4">
                <div className="flex items-baseline justify-between gap-3">
                    <h3 className="text-lg font-heading font-bold text-brand-navy">
                        {parameter.label} <span className="text-sm font-sans font-normal text-gray-400">({parameter.unit})</span>
                    </h3>
                    <span className="text-[11px] font-semibold uppercase tracking-wider text-gray-400">{parameter.full_name}</span>
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
                            tickFormatter={(value) => new Date(value).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })}
                            tick={{ fontSize: 11, fill: '#6B7280' }}
                            minTickGap={20}
                        />
                        <YAxis
                            domain={domain}
                            tick={{ fontSize: 11, fill: '#6B7280' }}
                            width={52}
                            tickFormatter={(value) => formatValue(value, parameter.decimals)}
                        />

                        {parameter.threshold_type === 'maximum' && parameter.warning_max !== null && parameter.max !== null && (
                            <ReferenceArea y1={parameter.warning_max} y2={parameter.max} fill="#FDE68A" fillOpacity={0.35} />
                        )}
                        {parameter.threshold_type === 'maximum' && parameter.max !== null && (
                            <ReferenceArea y1={parameter.max} y2={domain[1]} fill="#FECACA" fillOpacity={0.35} />
                        )}
                        {parameter.threshold_type === 'minimum' && parameter.warning_min !== null && parameter.min !== null && (
                            <ReferenceArea y1={parameter.min} y2={parameter.warning_min} fill="#FDE68A" fillOpacity={0.35} />
                        )}
                        {parameter.threshold_type === 'minimum' && parameter.min !== null && (
                            <ReferenceArea y1={domain[0]} y2={parameter.min} fill="#FECACA" fillOpacity={0.35} />
                        )}
                        {parameter.threshold_type === 'range' && parameter.min !== null && parameter.max !== null && (
                            <>
                                {parameter.warning_min !== null && (
                                    <ReferenceArea y1={parameter.min} y2={parameter.warning_min} fill="#FDE68A" fillOpacity={0.35} />
                                )}
                                {parameter.warning_max !== null && (
                                    <ReferenceArea y1={parameter.warning_max} y2={parameter.max} fill="#FDE68A" fillOpacity={0.35} />
                                )}
                                <ReferenceArea y1={domain[0]} y2={parameter.min} fill="#FECACA" fillOpacity={0.3} />
                                <ReferenceArea y1={parameter.max} y2={domain[1]} fill="#FECACA" fillOpacity={0.3} />
                            </>
                        )}

                        {parameter.min !== null && (
                            <ReferenceLine y={parameter.min} stroke={DASH} strokeDasharray="6 4" strokeWidth={1.5} />
                        )}
                        {parameter.max !== null && (
                            <ReferenceLine y={parameter.max} stroke={DASH} strokeDasharray="6 4" strokeWidth={1.5} />
                        )}
                        {parameter.warning_min !== null && (
                            <ReferenceLine y={parameter.warning_min} stroke={WARNING} strokeDasharray="3 3" strokeWidth={1.5} />
                        )}
                        {parameter.warning_max !== null && (
                            <ReferenceLine y={parameter.warning_max} stroke={WARNING} strokeDasharray="3 3" strokeWidth={1.5} />
                        )}

                        <Tooltip content={<ParameterTooltip parameter={parameter} />} />
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

            <div className="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1 text-[11px] text-gray-500">
                <span className="inline-flex items-center gap-1.5">
                    <span className="inline-block h-0.5 w-4" style={{ backgroundColor: parameter.color }} />
                    Data pengukuran
                </span>
                {hasWarningLine && (
                    <span className="inline-flex items-center gap-1.5" title="Ambang operasional internal TMCC, bukan baku mutu resmi.">
                        <span className="inline-block h-0.5 w-4" style={{ backgroundColor: WARNING }} />
                        Ambang perhatian (operasional)
                    </span>
                )}
                {hasRegulatoryLine && (
                    <span className="inline-flex items-center gap-1.5" title={parameter.source}>
                        <span className="inline-block h-0.5 w-4" style={{ backgroundColor: DASH }} />
                        Baku mutu (perlu verifikasi)
                    </span>
                )}
                {!hasWarningLine && !hasRegulatoryLine && (
                    <span title={parameter.source}>Belum ada ambang numerik yang dapat diverifikasi untuk parameter ini.</span>
                )}
            </div>
        </section>
    );
}

export default function WaterQuality({
    records,
    paginatedRecords,
    samplingPoints,
    summary,
    waterQualityConfig,
    filters,
    perPageOptions,
    settings,
}) {
    const parameters = Object.entries(waterQualityConfig.parameters);
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
            <Head title="Kualitas Air Danau Matano | TMCC" />

            <div className="bg-brand-navy py-16 md:py-24 text-center border-b-4 border-brand-teal">
                <div className="max-w-4xl mx-auto px-4">
                    <h1 className="text-4xl md:text-5xl font-heading font-bold text-brand-gold mb-4">Kualitas Air Danau Matano</h1>
                    <p className="text-xl text-gray-200">Data pemantauan berkala oleh Tova Water Patrol</p>
                </div>
            </div>

            <div className="bg-brand-paper py-12">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

                    {/* Filters */}
                    <div className="bg-white rounded-2xl shadow-sm p-6 flex flex-col md:flex-row gap-4 items-center justify-between">
                        <div className="w-full md:w-auto flex flex-col sm:flex-row gap-4">
                            <div>
                                <label htmlFor="sampling-point" className="block text-sm font-medium text-gray-700 mb-1">Titik Pengambilan Sample</label>
                                <select
                                    id="sampling-point"
                                    value={String(filters.sampling_point)}
                                    onChange={(e) => changeFilters({ sampling_point: e.target.value })}
                                    className="border-gray-300 rounded-lg shadow-sm focus:border-brand-teal focus:ring-brand-teal w-full min-w-[200px]"
                                >
                                    <option value="all">Semua Titik Sample</option>
                                    {samplingPoints && samplingPoints.map((point) => (
                                        <option key={point.id} value={String(point.id)}>{point.name}</option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label htmlFor="time-range" className="block text-sm font-medium text-gray-700 mb-1">Rentang Waktu</label>
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

                    {/* KPI Cards */}
                    <div>
                        <div className="mb-4 flex flex-col gap-1 sm:flex-row sm:items-baseline sm:justify-between">
                            <h2 className="text-2xl font-heading font-bold text-brand-navy">Ringkasan Parameter</h2>
                            <p className="text-xs text-gray-500">Rata-rata dihitung tanpa menganggap nilai kosong sebagai nol.</p>
                        </div>
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            {parameters.map(([key, parameter]) => {
                                const item = summary[key];

                                return (
                                    <div
                                        key={key}
                                        className="bg-white rounded-2xl shadow-sm p-5 border-t-4"
                                        style={{ borderTopColor: parameter.color }}
                                    >
                                        <div className="flex items-start justify-between gap-3">
                                            <div>
                                                <p className="text-xs font-bold uppercase tracking-wider text-gray-500">{parameter.label}</p>
                                                <p className="text-[11px] text-gray-400">{parameter.full_name}</p>
                                            </div>
                                            <StatusBadge status={item?.status || 'no_data'} small />
                                        </div>
                                        <p className="mt-3 text-3xl font-heading font-bold text-brand-navy">
                                            {formatValue(item?.average, parameter.decimals)}
                                            <span className="ml-1 text-base font-sans font-normal text-gray-400">{parameter.unit}</span>
                                        </p>
                                        <p className="mt-1 text-xs text-gray-500">
                                            Rata-rata &middot; {item?.count ?? 0} pengukuran
                                        </p>
                                    </div>
                                );
                            })}
                        </div>
                        <p className="mt-3 text-[11px] leading-relaxed text-gray-500">
                            {waterQualityConfig.standard.name} &mdash; {waterQualityConfig.standard.class}. {waterQualityConfig.standard.note}
                        </p>
                    </div>

                    {/* Six individual parameter charts */}
                    <div>
                        <h2 className="mb-4 text-2xl font-heading font-bold text-brand-navy">Tren Parameter Kualitas Air</h2>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {parameters.map(([key, parameter]) => (
                                <ParameterChart
                                    key={key}
                                    parameterKey={key}
                                    parameter={parameter}
                                    records={records}
                                />
                            ))}
                        </div>
                    </div>

                    {/* Measurement History */}
                    <div className="bg-white rounded-2xl shadow-sm overflow-hidden">
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
                                        <option key={option} value={String(option)}>{option}</option>
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
                                                <th className="px-6 py-3 font-semibold text-gray-600 whitespace-nowrap">Tanggal</th>
                                                <th className="px-6 py-3 font-semibold text-gray-600 whitespace-nowrap">Titik Sample</th>
                                                <th className="px-6 py-3 font-semibold text-gray-600 whitespace-nowrap">TSS (mg/L)</th>
                                                <th className="px-6 py-3 font-semibold text-gray-600 whitespace-nowrap">pH</th>
                                                <th className="px-6 py-3 font-semibold text-gray-600 whitespace-nowrap">TDS (mg/L)</th>
                                                <th className="px-6 py-3 font-semibold text-gray-600 whitespace-nowrap">DO (mg/L)</th>
                                                <th className="px-6 py-3 font-semibold text-gray-600 whitespace-nowrap">Suhu (°C)</th>
                                                <th className="px-6 py-3 font-semibold text-gray-600 whitespace-nowrap">Kejernihan (cm)</th>
                                                <th className="px-6 py-3 font-semibold text-gray-600">Catatan</th>
                                            </tr>
                                        </thead>
                                        <tbody className="divide-y divide-gray-100">
                                            {paginatedRecords.data.map((record) => {
                                                const phStatus = evaluateStatus(waterQualityConfig.parameters.ph, record.ph);
                                                const phMeta = STATUS_META[phStatus] || STATUS_META.no_data;

                                                return (
                                                    <tr key={record.id} className="hover:bg-gray-50 transition-colors">
                                                        <td className="px-6 py-3 text-gray-700 whitespace-nowrap">{formatDate(record.recorded_at)}</td>
                                                        <td className="px-6 py-3 text-gray-700 whitespace-nowrap">{record.sampling_point ? record.sampling_point.name : '-'}</td>
                                                        <td className="px-6 py-3 text-gray-700 whitespace-nowrap">{formatValue(record.tss, 1)}</td>
                                                        <td className="px-6 py-3 whitespace-nowrap">
                                                            {record.ph === null || record.ph === undefined ? (
                                                                <span className="text-gray-400">-</span>
                                                            ) : (
                                                                <span className={`inline-block px-2.5 py-0.5 rounded-full text-xs font-bold ${phMeta.badge}`}>
                                                                    {formatValue(record.ph, 2)}
                                                                </span>
                                                            )}
                                                        </td>
                                                        <td className="px-6 py-3 text-gray-700 whitespace-nowrap">{formatValue(record.tds, 1)}</td>
                                                        <td className="px-6 py-3 text-gray-700 whitespace-nowrap">{formatValue(record.dissolved_oxygen, 2)}</td>
                                                        <td className="px-6 py-3 text-gray-700 whitespace-nowrap">{formatValue(record.temperature, 1)}</td>
                                                        <td className="px-6 py-3 text-gray-700 whitespace-nowrap">{formatValue(record.clarity_cm, 0)}</td>
                                                        <td className="px-6 py-3 text-gray-500 max-w-xs truncate" title={record.notes || ''}>{record.notes || '-'}</td>
                                                    </tr>
                                                );
                                            })}
                                        </tbody>
                                    </table>
                                </div>

                                <div className="flex flex-col gap-3 border-t border-gray-100 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                                    <p className="text-sm text-gray-500">
                                        Menampilkan {paginatedRecords.from ?? 0}–{paginatedRecords.to ?? 0} dari {paginatedRecords.total} pengukuran
                                        <span className="hidden sm:inline"> &middot; Halaman {currentPage} dari {lastPage}</span>
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
