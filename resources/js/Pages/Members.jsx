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

            {/* Top 3 Podium */}
            {members && members.length >= 3 && (
                <div className="bg-brand-paper py-12 border-b border-gray-200">
                    <div className="max-w-3xl mx-auto px-4">
                        <h2 className="text-center text-xl font-heading font-bold text-brand-navy mb-8 uppercase tracking-wider">
                            Anggota Paling Aktif
                        </h2>
                        <div className="flex items-end justify-center gap-4">
                            {/* Rank 2 */}
                            <div className="flex flex-col items-center flex-1 max-w-[160px]">
                                <div className="w-20 h-20 rounded-full overflow-hidden border-4 border-gray-300 mb-3">
                                    {members[1].photo_url ? (
                                        <img src={`/storage/${members[1].photo_url}`} alt={members[1].name} className="w-full h-full object-cover" />
                                    ) : (
                                        <div className="w-full h-full bg-gray-200 flex items-center justify-center text-2xl font-bold text-gray-500">
                                            {members[1].name.charAt(0)}
                                        </div>
                                    )}
                                </div>
                                <p className="font-bold text-center text-sm text-brand-navy">{members[1].name}</p>
                                <span className="text-xs text-gray-500 mb-2">{members[1].points} poin</span>
                                <div className="w-full bg-gray-400 rounded-t-lg h-20 flex items-center justify-center">
                                    <Medal className="h-8 w-8 text-white" />
                                </div>
                            </div>

                            {/* Rank 1 */}
                            <div className="flex flex-col items-center flex-1 max-w-[180px]">
                                <Trophy className="h-8 w-8 text-yellow-500 mb-2" />
                                <div className="w-24 h-24 rounded-full overflow-hidden border-4 border-yellow-400 mb-3">
                                    {members[0].photo_url ? (
                                        <img src={`/storage/${members[0].photo_url}`} alt={members[0].name} className="w-full h-full object-cover" />
                                    ) : (
                                        <div className="w-full h-full bg-yellow-100 flex items-center justify-center text-3xl font-bold text-yellow-600">
                                            {members[0].name.charAt(0)}
                                        </div>
                                    )}
                                </div>
                                <p className="font-bold text-center text-brand-navy">{members[0].name}</p>
                                <span className="text-sm text-brand-gold font-bold mb-2">{members[0].points} poin</span>
                                <div className="w-full bg-brand-gold rounded-t-lg h-32 flex items-center justify-center">
                                    <Trophy className="h-10 w-10 text-white" />
                                </div>
                            </div>

                            {/* Rank 3 */}
                            <div className="flex flex-col items-center flex-1 max-w-[160px]">
                                <div className="w-20 h-20 rounded-full overflow-hidden border-4 border-amber-400 mb-3">
                                    {members[2].photo_url ? (
                                        <img src={`/storage/${members[2].photo_url}`} alt={members[2].name} className="w-full h-full object-cover" />
                                    ) : (
                                        <div className="w-full h-full bg-amber-100 flex items-center justify-center text-2xl font-bold text-amber-600">
                                            {members[2].name.charAt(0)}
                                        </div>
                                    )}
                                </div>
                                <p className="font-bold text-center text-sm text-brand-navy">{members[2].name}</p>
                                <span className="text-xs text-gray-500 mb-2">{members[2].points} poin</span>
                                <div className="w-full bg-amber-600 rounded-t-lg h-14 flex items-center justify-center">
                                    <Medal className="h-7 w-7 text-white" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            )}

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
