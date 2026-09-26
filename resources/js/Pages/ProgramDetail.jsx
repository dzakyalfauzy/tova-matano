import PublicLayout from '@/Layouts/PublicLayout';
import { Head, Link } from '@inertiajs/react';
import { ArrowLeft, Calendar, Youtube, Instagram } from 'lucide-react';

export default function ProgramDetail({ activity, settings }) {
    const embedYoutube = (url) => {
        if (!url) return null;
        const match = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
        return match ? `https://www.youtube.com/embed/${match[1]}` : null;
    };

    return (
        <PublicLayout settings={settings}>
            <Head title={activity.title} />

            <div className="bg-brand-paper min-h-screen">
                {/* Cover Image */}
                {activity.cover_image_url && (
                    <div className="w-full h-64 md:h-96 overflow-hidden relative bg-brand-navy">
                        <img
                            src={`/storage/${activity.cover_image_url}`}
                            alt={activity.title}
                            className="w-full h-full object-cover opacity-80"
                        />
                        <div className="absolute inset-0 bg-gradient-to-t from-brand-navy via-brand-navy/30 to-transparent"></div>
                    </div>
                )}

                <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                    {/* Back */}
                    <Link href="/program" className="inline-flex items-center gap-2 text-brand-teal hover:text-brand-navy transition-colors mb-8 text-sm font-medium">
                        <ArrowLeft className="h-4 w-4" />
                        Kembali ke Program
                    </Link>

                    {/* Title & Meta */}
                    <div className="bg-white rounded-2xl shadow-sm p-8 mb-8">
                        <h1 className="text-3xl md:text-4xl font-heading font-bold text-brand-navy mb-4">
                            {activity.title}
                        </h1>
                        <div className="flex items-center gap-2 text-gray-400 text-sm mb-6">
                            <Calendar className="h-4 w-4" />
                            <span>
                                {new Date(activity.published_at).toLocaleDateString('id-ID', {
                                    weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
                                })}
                            </span>
                        </div>
                        <hr className="border-gray-100 mb-6" />

                        {/* Rich Content */}
                        <div
                            className="prose prose-lg max-w-none prose-headings:font-heading prose-headings:text-brand-navy prose-a:text-brand-teal prose-img:rounded-xl"
                            dangerouslySetInnerHTML={{ __html: activity.content }}
                        />
                    </div>

                    {/* YouTube Embed */}
                    {activity.youtube_url && embedYoutube(activity.youtube_url) && (
                        <div className="bg-white rounded-2xl shadow-sm p-6 mb-8">
                            <div className="flex items-center gap-2 mb-4">
                                <Youtube className="h-5 w-5 text-red-500" />
                                <h3 className="font-bold text-brand-navy">Video Dokumentasi</h3>
                            </div>
                            <div className="aspect-video rounded-xl overflow-hidden">
                                <iframe
                                    src={embedYoutube(activity.youtube_url)}
                                    className="w-full h-full"
                                    allowFullScreen
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                />
                            </div>
                        </div>
                    )}

                    {/* Instagram Link */}
                    {activity.instagram_url && (
                        <div className="bg-white rounded-2xl shadow-sm p-6">
                            <a
                                href={activity.instagram_url}
                                target="_blank"
                                rel="noopener noreferrer"
                                className="flex items-center gap-3 text-brand-teal hover:text-brand-navy transition-colors font-medium"
                            >
                                <Instagram className="h-5 w-5" />
                                Lihat di Instagram
                            </a>
                        </div>
                    )}
                </div>
            </div>
        </PublicLayout>
    );
}
