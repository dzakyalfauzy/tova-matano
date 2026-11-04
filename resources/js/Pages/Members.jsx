import PublicLayout from '@/Layouts/PublicLayout';
import { Head, Link } from '@inertiajs/react';
import {
    Trophy,
    Medal,
    Users,
    HeartHandshake,
    CheckCircle2,
    Calendar,
    ArrowRight,
    Sparkles,
} from 'lucide-react';
import { useState } from 'react';

export default function Members({ members = [], volunteers = [], settings }) {
    const [activeTab, setActiveTab] = useState('all');

    const getRankIcon = (index) => {
        if (index === 0) return <Trophy className="h-6 w-6 text-yellow-500" />;
        if (index === 1) return <Medal className="h-6 w-6 text-gray-400" />;
        if (index === 2) return <Medal className="h-6 w-6 text-amber-600" />;
        return <span className="text-sm font-bold text-gray-400 w-6 text-center">#{index + 1}</span>;
    };

    const getRankBg = (index) => {
        if (index === 0) return 'bg-yellow-50/70 border-yellow-200 ring-2 ring-yellow-300';
        if (index === 1) return 'bg-gray-50 border-gray-200 ring-1 ring-gray-300';
        if (index === 2) return 'bg-amber-50/70 border-amber-200 ring-1 ring-amber-300';
        return 'bg-white border-gray-100';
    };

    const formatDate = (dateString) => {
        if (!dateString) return null;
        try {
            return new Date(dateString).toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
            });
        } catch {
            return dateString;
        }
    };

    return (
        <PublicLayout settings={settings}>
            <Head title="Anggota & Relawan - Tova Matano Care Center" />

            <div className="bg-brand-navy py-16 md:py-20 text-center border-b-4 border-brand-teal relative overflow-hidden">
                <div className="absolute inset-0 opacity-10 bg-[radial-gradient(#1e7f8c_1px,transparent_1px)] [background-size:16px_16px]" />
                <div className="max-w-4xl mx-auto px-4 relative z-10">
                    <div className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 text-brand-gold text-xs font-semibold uppercase tracking-wider mb-4 border border-white/10">
                        <Users className="w-3.5 h-3.5" />
                        Komunitas Pejuang Lingkungan
                    </div>
                    <h1 className="text-3xl md:text-5xl font-heading font-bold text-brand-paper mb-4">
                        Anggota & Relawan TMCC
                    </h1>
                    <p className="text-base md:text-lg text-gray-200 max-w-2xl mx-auto leading-relaxed">
                        Mengenal para anggota aktif dan relawan terverifikasi yang bersama-sama menggerakkan langkah nyata pelestarian Danau Matano.
                    </p>

                    <div className="mt-8 flex flex-wrap items-center justify-center gap-3">
                        <button
                            type="button"
                            onClick={() => setActiveTab('all')}
                            className={`px-5 py-2 rounded-full text-sm font-semibold transition-all ${
                                activeTab === 'all'
                                    ? 'bg-brand-gold text-brand-navy shadow-md'
                                    : 'bg-white/10 text-white hover:bg-white/20'
                            }`}
                        >
                            Semua ({members.length + volunteers.length})
                        </button>
                        <button
                            type="button"
                            onClick={() => setActiveTab('members')}
                            className={`px-5 py-2 rounded-full text-sm font-semibold transition-all flex items-center gap-2 ${
                                activeTab === 'members'
                                    ? 'bg-brand-gold text-brand-navy shadow-md'
                                    : 'bg-white/10 text-white hover:bg-white/20'
                            }`}
                        >
                            <Trophy className="w-4 h-4" />
                            Anggota Inti ({members.length})
                        </button>
                        <button
                            type="button"
                            onClick={() => setActiveTab('volunteers')}
                            className={`px-5 py-2 rounded-full text-sm font-semibold transition-all flex items-center gap-2 ${
                                activeTab === 'volunteers'
                                    ? 'bg-brand-gold text-brand-navy shadow-md'
                                    : 'bg-white/10 text-white hover:bg-white/20'
                            }`}
                        >
                            <HeartHandshake className="w-4 h-4" />
                            Relawan Terverifikasi ({volunteers.length})
                        </button>
                    </div>
                </div>
            </div>

            <div className="bg-[#FAF8F5] py-12 md:py-16">
                <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">

                    {(activeTab === 'all' || activeTab === 'members') && (
                        <section id="anggota-inti" className="scroll-mt-24">
                            <div className="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8 pb-4 border-b border-gray-200">
                                <div>
                                    <div className="flex items-center gap-2 text-brand-teal text-xs font-bold uppercase tracking-wider mb-1">
                                        <Trophy className="w-4 h-4" />
                                        Sub Bagian 1
                                    </div>
                                    <h2 className="text-2xl md:text-3xl font-heading font-bold text-brand-navy">
                                        Anggota Aktif & Peringkat Kontribusi
                                    </h2>
                                    <p className="text-sm md:text-base text-gray-600 mt-1 max-w-2xl">
                                        Daftar anggota resmi TMCC yang aktif berkontribusi, diurutkan berdasarkan perolehan poin keterlibatan dan dedikasi.
                                    </p>
                                </div>
                                <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-brand-teal/10 text-brand-teal whitespace-nowrap self-start sm:self-auto">
                                    <Sparkles className="w-3.5 h-3.5" />
                                    {members.length} Anggota Terdaftar
                                </span>
                            </div>

                            {members && members.length > 0 ? (
                                <div className="space-y-3">
                                    {members.map((member, index) => (
                                        <div
                                            key={member.id}
                                            className={`flex items-center gap-4 p-4 rounded-xl border transition-all hover:shadow-md ${getRankBg(index)}`}
                                        >
                                            <div className="w-8 flex justify-center shrink-0">
                                                {getRankIcon(index)}
                                            </div>

                                            <div className="w-12 h-12 rounded-full overflow-hidden shrink-0 border-2 border-brand-teal/30 bg-white">
                                                {member.photo_url ? (
                                                    <img
                                                        src={`/storage/${member.photo_url}`}
                                                        alt={member.name}
                                                        className="w-full h-full object-cover"
                                                    />
                                                ) : (
                                                    <div className="w-full h-full bg-brand-teal/10 flex items-center justify-center font-bold text-brand-teal text-lg">
                                                        {member.name.charAt(0)}
                                                    </div>
                                                )}
                                            </div>

                                            <div className="flex-grow min-w-0">
                                                <div className="flex items-center gap-2 flex-wrap">
                                                    <p className="font-bold text-brand-navy truncate text-base">
                                                        {member.name}
                                                    </p>
                                                    {index < 3 && (
                                                        <span className="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded bg-brand-gold/20 text-brand-navy">
                                                            Top Contributor
                                                        </span>
                                                    )}
                                                </div>
                                                {member.divisi_name && (
                                                    <p className="text-xs text-gray-500 uppercase tracking-wide font-medium mt-0.5">
                                                        {member.divisi_name}
                                                    </p>
                                                )}
                                                {member.message && (
                                                    <p className="text-xs text-gray-500 italic mt-1 truncate">
                                                        "{member.message}"
                                                    </p>
                                                )}
                                            </div>

                                            <div className="shrink-0 text-right">
                                                <span
                                                    className={`inline-block px-3 py-1.5 rounded-full text-xs md:text-sm font-bold shadow-sm ${
                                                        index === 0
                                                            ? 'bg-yellow-400 text-brand-navy'
                                                            : index === 1
                                                            ? 'bg-gray-200 text-gray-800'
                                                            : index === 2
                                                            ? 'bg-amber-200 text-amber-900'
                                                            : 'bg-brand-teal/10 text-brand-teal border border-brand-teal/20'
                                                    }`}
                                                >
                                                    {member.points} poin
                                                </span>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div className="text-center py-16 bg-white rounded-2xl border border-dashed border-gray-300">
                                    <Trophy className="mx-auto h-12 w-12 text-gray-300 mb-3" />
                                    <p className="text-base font-semibold text-gray-700">Belum ada data anggota inti.</p>
                                    <p className="text-xs text-gray-500 mt-1">Data anggota akan diperbarui secara berkala.</p>
                                </div>
                            )}
                        </section>
                    )}

                    {(activeTab === 'all' || activeTab === 'volunteers') && (
                        <section id="relawan-terverifikasi" className="scroll-mt-24">
                            <div className="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8 pb-4 border-b border-gray-200">
                                <div>
                                    <div className="flex items-center gap-2 text-emerald-700 text-xs font-bold uppercase tracking-wider mb-1">
                                        <HeartHandshake className="w-4 h-4" />
                                        Sub Bagian 2
                                    </div>
                                    <h2 className="text-2xl md:text-3xl font-heading font-bold text-brand-navy">
                                        Relawan Terverifikasi
                                    </h2>
                                    <p className="text-sm md:text-base text-gray-600 mt-1 max-w-2xl">
                                        Sahabat relawan yang telah mendaftar dan disetujui (approved) oleh admin untuk terjun langsung dalam aksi konservasi Danau Matano.
                                    </p>
                                </div>
                                <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 whitespace-nowrap self-start sm:self-auto">
                                    <CheckCircle2 className="w-3.5 h-3.5" />
                                    {volunteers.length} Relawan Disetujui
                                </span>
                            </div>

                            {volunteers && volunteers.length > 0 ? (
                                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                                    {volunteers.map((volunteer) => (
                                        <div
                                            key={volunteer.id}
                                            className="bg-white rounded-xl border border-gray-200 p-5 shadow-sm hover:shadow-md transition-all flex flex-col justify-between"
                                        >
                                            <div>
                                                <div className="flex items-center gap-3.5 mb-3.5">
                                                    <div className="w-12 h-12 rounded-full overflow-hidden shrink-0 border-2 border-emerald-500/30 bg-emerald-50 flex items-center justify-center">
                                                        {volunteer.photo_url ? (
                                                            <img
                                                                src={`/storage/${volunteer.photo_url}`}
                                                                alt={volunteer.full_name}
                                                                className="w-full h-full object-cover"
                                                            />
                                                        ) : (
                                                            <span className="font-heading font-bold text-emerald-700 text-lg">
                                                                {volunteer.full_name?.charAt(0) || 'R'}
                                                            </span>
                                                        )}
                                                    </div>
                                                    <div className="min-w-0 flex-grow">
                                                        <h3 className="font-bold text-brand-navy text-base truncate">
                                                            {volunteer.full_name}
                                                        </h3>
                                                        <span className="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200 mt-0.5">
                                                            <CheckCircle2 className="w-3 h-3 text-emerald-600" />
                                                            Relawan Aktif
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div className="pt-3 border-t border-gray-100 flex items-center justify-between gap-2 text-xs text-gray-500">
                                                <span className="flex items-center gap-1 text-gray-500">
                                                    <HeartHandshake className="w-3.5 h-3.5 text-emerald-600 shrink-0" />
                                                    Relawan Terverifikasi
                                                </span>
                                                {volunteer.submitted_at && (
                                                    <span className="flex items-center gap-1 text-gray-400 shrink-0">
                                                        <Calendar className="w-3.5 h-3.5" />
                                                        {formatDate(volunteer.submitted_at)}
                                                    </span>
                                                )}
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div className="text-center py-16 bg-white rounded-2xl border border-dashed border-gray-300">
                                    <HeartHandshake className="mx-auto h-12 w-12 text-gray-300 mb-3" />
                                    <p className="text-base font-semibold text-gray-700">Belum ada relawan yang disetujui.</p>
                                    <p className="text-xs text-gray-500 mt-1">Pendaftaran relawan yang diverifikasi admin akan tampil di sini.</p>
                                </div>
                            )}
                        </section>
                    )}

                    <div className="bg-gradient-to-r from-brand-navy to-[#184860] rounded-2xl p-8 md:p-10 text-white shadow-xl flex flex-col md:flex-row items-center justify-between gap-6">
                        <div className="space-y-2 text-center md:text-left max-w-xl">
                            <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-brand-gold/20 text-brand-gold border border-brand-gold/30">
                                <Sparkles className="w-3.5 h-3.5" />
                                Mari Bergabung Bersama Kami
                            </span>
                            <h3 className="text-2xl md:text-3xl font-heading font-bold text-brand-paper">
                                Ingin Berperan Nyata untuk Danau Matano?
                            </h3>
                            <p className="text-sm text-gray-200 leading-relaxed">
                                Daftarkan diri Anda sebagai relawan dan dapatkan kesempatan berkontribusi langsung dalam berbagai program edukasi dan konservasi lingkungan.
                            </p>
                        </div>
                        <div className="shrink-0 flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                            <Link
                                href="/gabung-donasi"
                                className="inline-flex items-center justify-center gap-2 px-6 py-3 bg-brand-gold text-brand-navy font-bold text-sm rounded-xl hover:bg-amber-400 transition-all shadow-md"
                            >
                                Daftar Relawan Sekarang
                                <ArrowRight className="w-4 h-4" />
                            </Link>
                        </div>
                    </div>

                </div>
            </div>
        </PublicLayout>
    );
}
