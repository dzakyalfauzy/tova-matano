import PublicLayout from '@/Layouts/PublicLayout';
import { Head, Link } from '@inertiajs/react';
import { ChevronRight, Calendar, ArrowLeft } from 'lucide-react';

export default function ProgramCategory({ program, activities, settings }) {
    return (
        <PublicLayout settings={settings}>
            <Head title={`Program: ${program.name}`} />

            {/* Page Header */}
            <div className="bg-brand-navy py-16 text-center border-b-4 border-brand-teal">
                <div className="max-w-4xl mx-auto px-4">
                    <Link href="/program" className="inline-flex items-center text-brand-gold hover:text-white mb-6 transition-colors">
                        <ArrowLeft className="w-4 h-4 mr-2" /> Kembali ke Program
                    </Link>
                    <h1 className="text-4xl md:text-5xl font-heading font-bold text-white mb-4">
                        {program.name}
                    </h1>
                    <p className="text-xl text-gray-300 max-w-3xl mx-auto">
                        {program.description}
                    </p>
                </div>
            </div>

            {/* Activities Grid */}
            <div className="bg-brand-paper py-16">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    {activities.data.length > 0 ? (
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            {activities.data.map((activity) => (
                                <Link 
                                    key={activity.id} 
                                    href={`/program/${program.slug}/${activity.slug}`}
                                    className="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow group flex flex-col"
                                >
                                    <div className="aspect-video relative overflow-hidden bg-gray-100">
                                        {activity.cover_image_url ? (
                                            <img
                                                src={`/storage/${activity.cover_image_url}`}
                                                alt={activity.title}
                                                className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                            />
                                        ) : (
                                            <div className="w-full h-full flex items-center justify-center text-gray-400">
                                                Tidak ada gambar
                                            </div>
                                        )}
                                        <div className="absolute top-4 left-4 bg-brand-gold text-brand-navy text-xs font-bold px-3 py-1 rounded-full shadow-sm">
                                            {program.name}
                                        </div>
                                    </div>
                                    <div className="p-6 flex flex-col flex-grow">
                                        <div className="flex items-center text-sm text-gray-500 mb-3">
                                            <Calendar className="w-4 h-4 mr-2" />
                                            {new Date(activity.published_at).toLocaleDateString('id-ID', {
                                                day: 'numeric',
                                                month: 'long',
                                                year: 'numeric'
                                            })}
                                        </div>
                                        <h3 className="text-xl font-bold text-brand-navy mb-3 group-hover:text-brand-teal transition-colors line-clamp-2">
                                            {activity.title}
                                        </h3>
                                        <p className="text-brand-teal font-semibold text-sm mt-auto flex items-center group-hover:text-brand-navy transition-colors">
                                            Baca Selengkapnya <ChevronRight className="w-4 h-4 ml-1" />
                                        </p>
                                    </div>
                                </Link>
                            ))}
                        </div>
                    ) : (
                        <div className="text-center py-20 bg-white rounded-xl border border-dashed border-gray-200">
                            <p className="text-gray-500 text-lg">Belum ada kegiatan (proker) yang dipublikasikan dalam program ini.</p>
                        </div>
                    )}

                    {/* Pagination */}
                    {activities.links && activities.links.length > 3 && (
                        <div className="mt-12 flex justify-center">
                            <div className="flex space-x-1">
                                {activities.links.map((link, i) => (
                                    <Link
                                        key={i}
                                        href={link.url || '#'}
                                        className={`px-4 py-2 rounded-md border ${
                                            link.active 
                                                ? 'bg-brand-teal text-white border-brand-teal' 
                                                : link.url 
                                                    ? 'bg-white text-gray-700 hover:bg-gray-50 border-gray-300' 
                                                    : 'bg-gray-50 text-gray-400 border-gray-200 cursor-not-allowed'
                                        }`}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                    />
                                ))}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </PublicLayout>
    );
}
