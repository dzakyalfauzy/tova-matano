import { Link } from '@inertiajs/react';
import { ChevronRight, Leaf } from 'lucide-react';

export default function AboutSnippet({ homeStats }) {
    const stats = [
        { number: homeStats?.relawan_aktif ?? '—',      label: 'Relawan Aktif' },
        { number: homeStats?.program_terlaksana ?? '—', label: 'Program Terlaksana' },
        { number: homeStats?.sampah_terangkut ?? '—',   label: 'Sampah Terangkut' },
        { number: homeStats?.rata_rata_ph ?? '—',       label: 'Rata-rata pH Air' },
        { number: homeStats?.rata_rata_kejernihan ?? '—', label: 'Rata-rata Kejernihan (m)' },
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
                            Tova Matano Care Center adalah organisasi peduli lingkungan yang berfokus pada
                            pemulihan dan pelestarian Danau Matano dari ancaman pencemaran, melalui edukasi,
                            aksi lapangan, dan kolaborasi bersama masyarakat sekitar.
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

                {/* Statistics Bar */}
                <div className="mt-14 bg-brand-navy rounded-3xl px-6 py-8 md:px-10 md:py-10">
                    <div className="grid grid-cols-2 md:grid-cols-5 gap-6 md:gap-0 md:divide-x md:divide-white/20">
                        {stats.map((stat, index) => (
                            <div
                                key={index}
                                className="flex flex-col items-center text-center px-4 py-2"
                            >
                                <span className="text-3xl md:text-4xl font-heading font-bold text-brand-teal mb-1">
                                    {stat.number}
                                </span>
                                <span className="text-sm text-gray-300 font-medium">
                                    {stat.label}
                                </span>
                            </div>
                        ))}
                    </div>
                </div>

            </div>
        </section>
    );
}
