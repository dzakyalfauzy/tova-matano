import PublicLayout from '@/Layouts/PublicLayout';
import { Head, Link } from '@inertiajs/react';
import { ChevronRight, BookOpen } from 'lucide-react';

export default function Programs({ programs, settings }) {
    return (
        <PublicLayout settings={settings}>
            <Head title="Program TMCC" />

            {/* Page Header */}
            <div className="bg-brand-navy py-16 md:py-24 text-center border-b-4 border-brand-teal">
                <div className="max-w-4xl mx-auto px-4">
                    <h1 className="text-4xl md:text-5xl font-heading font-bold text-brand-gold mb-4">Program Kami</h1>
                    <p className="text-xl text-gray-200">
                        Empat pilar aksi nyata TMCC dalam menjaga kelestarian Danau Matano.
                    </p>
                </div>
            </div>

            {/* Programs List */}
            <div className="bg-brand-paper py-16">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    {programs && programs.length > 0 ? (
                        programs.map((program, index) => (
                            <div key={program.id} className={`flex flex-col ${index % 2 === 0 ? 'lg:flex-row' : 'lg:flex-row-reverse'} gap-12 items-center mb-24 last:mb-0`}>
                                {/* Image */}
                                <div className="w-full lg:w-1/2">
                                    <div className="relative rounded-2xl overflow-hidden aspect-video bg-gray-100 shadow-lg">
                                        {program.icon_url ? (
                                            <img
                                                src={`/storage/${program.icon_url}`}
                                                alt={program.name}
                                                className="w-full h-full object-cover"
                                            />
                                        ) : (
                                            <div className="w-full h-full flex items-center justify-center bg-brand-navy/5">
                                                <BookOpen className="h-24 w-24 text-brand-teal/30" strokeWidth={1} />
                                            </div>
                                        )}
                                        <div className="absolute inset-0 bg-gradient-to-t from-brand-navy/60 to-transparent"></div>
                                        <div className="absolute bottom-6 left-6">
                                            <span className="px-3 py-1 bg-brand-gold text-brand-navy text-xs font-bold rounded-full uppercase tracking-wider">
                                                Program #{index + 1}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {/* Content */}
                                <div className="w-full lg:w-1/2">
                                    <h2 className="text-3xl font-heading font-bold text-brand-navy mb-4">{program.name}</h2>
                                    <p className="text-gray-600 leading-relaxed mb-8">{program.description}</p>

                                    {/* Activities Preview */}
                                    {program.activities && program.activities.length > 0 && (
                                        <div className="mb-8">
                                            <h3 className="text-sm font-bold text-brand-teal uppercase tracking-wider mb-4">Kegiatan Terkini</h3>
                                            <div className="space-y-3">
                                                {program.activities.map(activity => (
                                                    <Link
                                                        key={activity.id}
                                                        href={`/program/${program.slug}/${activity.slug}`}
                                                        className="flex items-center gap-3 p-3 bg-white rounded-lg hover:bg-brand-teal/5 transition-colors border border-gray-100 group"
                                                    >
                                                        {activity.cover_image_url && (
                                                            <img
                                                                src={`/storage/${activity.cover_image_url}`}
                                                                alt={activity.title}
                                                                className="w-16 h-12 object-cover rounded shrink-0"
                                                            />
                                                        )}
                                                        <div className="flex-grow min-w-0">
                                                            <p className="font-semibold text-brand-navy text-sm truncate group-hover:text-brand-teal transition-colors">
                                                                {activity.title}
                                                            </p>
                                                            <p className="text-xs text-gray-400">
                                                                {new Date(activity.published_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}
                                                            </p>
                                                        </div>
                                                        <ChevronRight className="h-4 w-4 text-gray-300 group-hover:text-brand-teal shrink-0" />
                                                    </Link>
                                                ))}
                                            </div>
                                        </div>
                                    )}
                                    <Link 
                                        href={`/program/${program.slug}`}
                                        className="inline-flex items-center justify-center px-6 py-3 border-2 border-brand-teal text-brand-teal font-bold rounded-lg hover:bg-brand-teal hover:text-white transition-colors w-full sm:w-auto"
                                    >
                                        Lihat Semua Proker {program.name} <ChevronRight className="ml-2 h-5 w-5" />
                                    </Link>
                                </div>
                            </div>
                        ))
                    ) : (
                        <div className="text-center py-20 text-gray-400">
                            <BookOpen className="mx-auto h-16 w-16 text-gray-200 mb-4" strokeWidth={1} />
                            <p className="text-lg font-medium">Program belum tersedia.</p>
                        </div>
                    )}
                </div>
            </div>
        </PublicLayout>
    );
}
