import { Link } from '@inertiajs/react';
import { ChevronRight, Droplets, Leaf } from 'lucide-react';

export default function AboutSnippet({ homeStats }) {
    const orgMetrics = [
        {
            number: homeStats?.relawan_aktif ?? '—',
            label: 'Relawan Aktif',
            sublabel: 'Terdaftar & Terverifikasi',
        },
        {
            number: homeStats?.program_terlaksana ?? '—',
            label: 'Program Terlaksana',
            sublabel: 'Aksi Nyata Berkelanjutan',
        },
        {
            number: homeStats?.sampah_terangkut ?? '—',
            label: 'Sampah Terangkut',
            sublabel: 'Pembersihan Area Danau',
        },
    ];

    const waterParameters = [
        {
            label: 'pH',
            value: homeStats?.rata_rata_ph ?? '—',
            unit: 'pH',
            desc: 'Derajat Keasaman',
        },
        {
            label: 'Kejernihan',
            value: homeStats?.rata_rata_kejernihan ?? '—',
            unit: 'm',
            desc: 'Kecerahan Secchi',
        },
        {
            label: 'TSS',
            value: homeStats?.rata_rata_tss ?? '—',
            unit: 'mg/L',
            desc: 'Padatan Tersuspensi',
        },
        {
            label: 'TDS',
            value: homeStats?.rata_rata_tds ?? '—',
            unit: 'mg/L',
            desc: 'Padatan Terlarut',
        },
        {
            label: 'DO',
            value: homeStats?.rata_rata_do ?? '—',
            unit: 'mg/L',
            desc: 'Oksigen Terlarut',
        },
        {
            label: 'Suhu',
            value: homeStats?.rata_rata_suhu ?? '—',
            unit: '°C',
            desc: 'Suhu Air Permukaan',
        },
    ];

    return (
        <section className="bg-brand-paper py-16 md:py-24">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                {/* 2-column layout */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">

                    {/* Kolom Kiri – Gambar */}
                    <div className="w-full">
                        <div className="relative rounded-3xl overflow-hidden aspect-[4/3] bg-brand-navy/10 shadow-lg">
                            <img
                                src="/image/logo_tova_matano.png"
                                alt="Kegiatan Tova Matano Care Center"
                                className="w-full h-full object-contain bg-white p-10"
                            />
                            {/* decorative accent */}
                            <div className="absolute -bottom-4 -right-4 w-24 h-24 rounded-full bg-brand-teal/20 blur-2xl pointer-events-none" />
                        </div>
                    </div>

                    {/* Kolom Kanan – Konten */}
                    <div className="flex flex-col justify-center">
                        {/* Small label */}
                        <div className="inline-flex items-center gap-2 mb-4">
                            <Leaf className="h-4 w-4 text-brand-teal" />
                            <span className="text-brand-teal text-sm font-bold uppercase tracking-widest">
                                Tentang Kami
                            </span>
                        </div>

                        {/* Heading */}
                        <h2 className="text-3xl md:text-4xl font-heading font-bold text-brand-navy mb-5 leading-tight">
                            Berdiri untuk pemulihan<br className="hidden md:block" /> Danau Matano
                        </h2>

                        {/* Description */}
                        <p className="text-gray-600 text-base md:text-lg leading-relaxed mb-8">
                            Tova Matano Care Center adalah organisasi peduli lingkungan yang berfokus pada pemulihan dan pelestarian Danau Matano dari ancaman degradasi lingkungan, melalui edukasi dan aksi lapangan dengan pelibatan masyarakat sekitar dan kolaborasi dengan para pecinta lingkungan dan pemangku kepentingan.
                        </p>

                        {/* Button */}
                        <Link
                            href="/tentang"
                            className="inline-flex items-center gap-2 self-start px-6 py-3 bg-brand-teal text-white font-bold rounded-xl hover:bg-brand-navy transition-colors shadow-sm"
                        >
                            Selengkapnya Tentang Kami
                            <ChevronRight className="h-5 w-5" />
                        </Link>
                    </div>
                </div>

                {/* Statistics Container */}
                <div className="mt-14 bg-brand-navy rounded-3xl p-6 sm:p-8 md:p-10 text-white shadow-xl">
                    {/* Kelompok 1 — Metrik Organisasi */}
                    <div className="grid grid-cols-1 sm:grid-cols-3 gap-6 sm:gap-4 md:divide-x md:divide-white/15 pb-8 sm:pb-10 border-b border-white/15">
                        {orgMetrics.map((item, index) => (
                            <div
                                key={index}
                                className="flex flex-col items-center text-center px-4"
                            >
                                <span className="text-3xl md:text-5xl font-heading font-bold text-brand-gold mb-1.5">
                                    {item.number}
                                </span>
                                <span className="text-base font-semibold text-white">
                                    {item.label}
                                </span>
                                <span className="text-xs text-gray-300 mt-0.5">
                                    {item.sublabel}
                                </span>
                            </div>
                        ))}
                    </div>

                    {/* Kelompok 2 — Parameter Kualitas Air Danau Matano */}
                    <div className="pt-8 sm:pt-10">
                        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
                            <div>
                                <div className="inline-flex items-center gap-2 text-brand-teal text-xs font-bold uppercase tracking-wider mb-1">
                                    <Droplets className="h-4 w-4 text-brand-gold" />
                                    <span>Pemantauan Berkala</span>
                                </div>
                                <h3 className="text-xl sm:text-2xl font-heading font-bold text-white">
                                    Parameter Kualitas Air Danau Matano
                                </h3>
                                <p className="text-xs text-gray-300 mt-0.5">
                                    Rata-rata hasil pemantauan terpadu di titik sampling Danau Matano
                                </p>
                            </div>
                            <Link
                                href="/kualitas-air"
                                className="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-brand-gold hover:text-white transition-colors self-start sm:self-auto"
                            >
                                Lihat Data Lengkap
                                <ChevronRight className="h-4 w-4" />
                            </Link>
                        </div>

                        {/* 6 Parameter Cards: Mobile 2 cols, Tablet 3 cols, Desktop 6 cols */}
                        <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
                            {waterParameters.map((param, index) => (
                                <div
                                    key={index}
                                    className="bg-white/5 border border-white/10 rounded-2xl p-4 flex flex-col justify-between hover:bg-white/10 transition-colors"
                                >
                                    <div>
                                        <div className="flex items-baseline justify-between gap-1">
                                            <span className="text-xs font-bold uppercase tracking-wider text-brand-gold">
                                                {param.label}
                                            </span>
                                            <span className="text-[11px] text-gray-400 font-medium">
                                                {param.unit}
                                            </span>
                                        </div>
                                        <p className="text-2xl sm:text-3xl font-heading font-bold text-white mt-2 mb-1">
                                            {param.value}
                                        </p>
                                    </div>
                                    <p className="text-[11px] text-gray-300 mt-1 pt-2 border-t border-white/10 truncate">
                                        {param.desc}
                                    </p>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>

            </div>
        </section>
    );
}
