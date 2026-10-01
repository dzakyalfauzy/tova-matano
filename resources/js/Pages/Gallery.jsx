import PublicLayout from '@/Layouts/PublicLayout';
import { Head } from '@inertiajs/react';
import { Image, Play } from 'lucide-react';
import { useState } from 'react';

export default function Gallery({ items, settings }) {
    const [selected, setSelected] = useState(null);

    return (
        <PublicLayout settings={settings}>
            <Head title="Galeri & Media" />

            {/* Header */}
            <div className="bg-brand-navy py-16 md:py-24 text-center border-b-4 border-brand-teal">
                <div className="max-w-4xl mx-auto px-4">
                    <h1 className="text-4xl md:text-5xl font-heading font-bold text-brand-gold mb-4">Galeri & Media</h1>
                    <p className="text-xl text-gray-200">Dokumentasi aksi dan kegiatan TMCC di lapangan.</p>
                </div>
            </div>

            <div className="bg-brand-paper py-12">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    {items?.data && items.data.length > 0 ? (
                        <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            {items.data.map(item => (
                                <button
                                    key={item.id}
                                    onClick={() => setSelected(item)}
                                    className="group relative aspect-square rounded-xl overflow-hidden bg-gray-100 shadow-sm hover:shadow-lg transition-all"
                                >
                                    {item.type === 'PHOTO' ? (
                                        <img
                                            src={`/storage/${item.url}`}
                                            alt={item.caption || 'Galeri TMCC'}
                                            className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                        />
                                    ) : (
                                        <div className="w-full h-full bg-brand-navy/80 flex flex-col items-center justify-center">
                                            <Play className="h-12 w-12 text-white mb-2" />
                                            <span className="text-white text-xs">Video</span>
                                        </div>
                                    )}
                                    <div className="absolute inset-0 bg-brand-navy/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-3">
                                        {item.caption && (
                                            <p className="text-white text-xs font-medium line-clamp-2 text-left">{item.caption}</p>
                                        )}
                                    </div>
                                    <div className="absolute top-2 right-2">
                                        {item.type === 'PHOTO' ? (
                                            <Image className="h-4 w-4 text-white drop-shadow" />
                                        ) : (
                                            <Play className="h-4 w-4 text-white drop-shadow" />
                                        )}
                                    </div>
                                </button>
                            ))}
                        </div>
                    ) : (
                        <div className="text-center py-20 bg-white rounded-2xl border border-dashed border-gray-200">
                            <Image className="mx-auto h-16 w-16 text-gray-200 mb-4" />
                            <p className="text-gray-400 text-lg">Belum ada item galeri.</p>
                        </div>
                    )}

                    {/* Pagination */}
                    {items?.links && items.links.length > 3 && (
                        <div className="flex justify-center gap-2 mt-12">
                            {items.links.map((link, i) => (
                                <button
                                    key={i}
                                    disabled={!link.url}
                                    onClick={() => link.url && router.visit(link.url)}
                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                    className={`px-4 py-2 rounded-lg text-sm font-medium transition-colors ${
                                        link.active ? 'bg-brand-teal text-white'
                                        : link.url ? 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200'
                                        : 'bg-gray-50 text-gray-300 cursor-not-allowed'
                                    }`}
                                />
                            ))}
                        </div>
                    )}
                </div>
            </div>

            {/* Lightbox Modal */}
            {selected && (
                <div
                    className="fixed inset-0 bg-black/90 z-50 flex items-center justify-center p-4"
                    onClick={() => setSelected(null)}
                >
                    <div className="max-w-4xl w-full" onClick={e => e.stopPropagation()}>
                        {selected.type === 'PHOTO' ? (
                            <img
                                src={`/storage/${selected.url}`}
                                alt={selected.caption || ''}
                                className="w-full h-auto max-h-[80vh] object-contain rounded-xl"
                            />
                        ) : (
                            <div className="aspect-video rounded-xl overflow-hidden bg-black">
                                <a
                                    href={selected.url}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="flex items-center justify-center w-full h-full text-white gap-3"
                                >
                                    <Play className="h-12 w-12" />
                                    <span className="text-lg font-medium">Buka Video</span>
                                </a>
                            </div>
                        )}
                        {selected.caption && (
                            <p className="text-white text-center mt-4 text-sm opacity-80">{selected.caption}</p>
                        )}
                        <button
                            onClick={() => setSelected(null)}
                            className="absolute top-4 right-4 text-white text-3xl leading-none hover:text-gray-300"
                        >
                            &times;
                        </button>
                    </div>
                </div>
            )}
        </PublicLayout>
    );
}
