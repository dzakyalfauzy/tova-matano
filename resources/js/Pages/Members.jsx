import PublicLayout from '@/Layouts/PublicLayout';
import { Head } from '@inertiajs/react';
import { Trophy, Medal } from 'lucide-react';

export default function Members({ members, settings }) {
    const getRankIcon = (index) => {
        if (index === 0) return <Trophy className="h-6 w-6 text-yellow-500" />;
        if (index === 1) return <Medal className="h-6 w-6 text-gray-400" />;
        if (index === 2) return <Medal className="h-6 w-6 text-amber-600" />;
        return <span className="text-sm font-bold text-gray-400 w-6 text-center">#{index + 1}</span>;
    };

    const getRankBg = (index) => {
        if (index === 0) return 'bg-yellow-50 border-yellow-200 ring-2 ring-yellow-300';
        if (index === 1) return 'bg-gray-50 border-gray-200 ring-1 ring-gray-300';
        if (index === 2) return 'bg-amber-50 border-amber-200 ring-1 ring-amber-300';
        return 'bg-white border-gray-100';
    };

    return (
        <PublicLayout settings={settings}>
            <Head title="Anggota TMCC" />

            {/* Page Header */}
            <div className="bg-brand-navy py-16 md:py-24 text-center border-b-4 border-brand-teal">
                <div className="max-w-4xl mx-auto px-4">
                    <h1 className="text-4xl md:text-5xl font-heading font-bold text-brand-gold mb-4">Anggota TMCC</h1>
                    <p className="text-xl text-gray-200">
                        Daftar anggota aktif terurut berdasarkan poin keaktifan berkontribusi.
                    </p>
                    {members && (
                        <div className="mt-6 inline-block px-6 py-2 bg-brand-teal/20 rounded-full">
                            <span className="text-brand-teal font-semibold">{members.length} Anggota Terdaftar</span>
                        </div>
                    )}
                </div>
            </div>

            {/* Full Rankings List */}
            <div className="bg-white py-12">
                <div className="max-w-3xl mx-auto px-4">
                    {members && members.length > 0 ? (
                        <div className="space-y-3">
                            {members.map((member, index) => (
                                <div
                                    key={member.id}
                                    className={`flex items-center gap-4 p-4 rounded-xl border transition-shadow hover:shadow-md ${getRankBg(index)}`}
                                >
                                    {/* Rank */}
                                    <div className="w-8 flex justify-center shrink-0">
                                        {getRankIcon(index)}
                                    </div>

                                    {/* Avatar */}
                                    <div className="w-12 h-12 rounded-full overflow-hidden shrink-0 border-2 border-brand-teal/30">
                                        {member.photo_url ? (
                                            <img src={`/storage/${member.photo_url}`} alt={member.name} className="w-full h-full object-cover" />
                                        ) : (
                                            <div className="w-full h-full bg-brand-teal/10 flex items-center justify-center font-bold text-brand-teal">
                                                {member.name.charAt(0)}
                                            </div>
                                        )}
                                    </div>

                                    {/* Info */}
                                    <div className="flex-grow min-w-0">
                                        <p className="font-bold text-brand-navy truncate">{member.name}</p>
                                        {member.divisi_name && (
                                            <p className="text-xs text-gray-500 uppercase tracking-wide">{member.divisi_name}</p>
                                        )}
                                        {member.message && (
                                            <p className="text-xs text-gray-400 italic mt-1 truncate">"{member.message}"</p>
                                        )}
                                    </div>

                                    {/* Points Badge */}
                                    <div className="shrink-0 text-right">
                                        <span className={`inline-block px-3 py-1 rounded-full text-sm font-bold ${
                                            index === 0 ? 'bg-yellow-100 text-yellow-700' :
                                            index === 1 ? 'bg-gray-100 text-gray-600' :
                                            index === 2 ? 'bg-amber-100 text-amber-700' :
                                            'bg-brand-teal/10 text-brand-teal'
                                        }`}>
                                            {member.points} poin
                                        </span>
                                    </div>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <div className="text-center py-20 text-gray-400">
                            <Trophy className="mx-auto h-16 w-16 text-gray-200 mb-4" />
                            <p className="text-lg font-medium">Belum ada data anggota.</p>
                        </div>
                    )}
                </div>
            </div>
        </PublicLayout>
    );
}
