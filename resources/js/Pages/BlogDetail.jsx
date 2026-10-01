import PublicLayout from '@/Layouts/PublicLayout';
import { Head, Link } from '@inertiajs/react';
import { ArrowLeft, Calendar, Tag, FileText } from 'lucide-react';

const CATEGORY_COLORS = {
    EDUKASI: 'bg-green-100 text-green-700',
    RILIS_PERS: 'bg-blue-100 text-blue-700',
    BERITA: 'bg-orange-100 text-orange-700',
    KEBIJAKAN: 'bg-purple-100 text-purple-700',
    BASELINE_STUDY: 'bg-brand-gold/20 text-brand-navy border border-brand-gold/30',
};

const CATEGORY_LABELS = {
    EDUKASI: 'Edukasi',
    RILIS_PERS: 'Rilis Pers',
    BERITA: 'Berita',
    KEBIJAKAN: 'Kebijakan',
    BASELINE_STUDY: 'Baseline Study',
};

export default function BlogDetail({ post, settings }) {
    return (
        <PublicLayout settings={settings}>
            <Head title={post.title} />

            {/* Cover */}
            {post.cover_image_url && (
                <div className="w-full h-64 md:h-96 overflow-hidden relative bg-brand-navy">
                    <img
                        src={`/storage/${post.cover_image_url}`}
                        alt={post.title}
                        className="w-full h-full object-cover opacity-70"
                    />
                    <div className="absolute inset-0 bg-gradient-to-t from-brand-navy via-brand-navy/40 to-transparent"></div>
                </div>
            )}

            <div className="bg-brand-paper min-h-screen">
                <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                    {/* Back */}
                    <Link href="/blog" className="inline-flex items-center gap-2 text-brand-teal hover:text-brand-navy transition-colors mb-8 text-sm font-medium">
                        <ArrowLeft className="h-4 w-4" />
                        Kembali ke Blog
                    </Link>

                    <div className="bg-white rounded-2xl shadow-sm p-8 mb-8">
                        {/* Category */}
                        {post.category && (
                            <span className={`inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-full mb-4 ${CATEGORY_COLORS[post.category] || 'bg-gray-100 text-gray-600'}`}>
                                <Tag className="h-3 w-3" />
                                {CATEGORY_LABELS[post.category] || post.category}
                            </span>
                        )}

                        {/* Title */}
                        <h1 className="text-3xl md:text-4xl font-heading font-bold text-brand-navy mb-4 leading-tight">
                            {post.title}
                        </h1>

                        {/* Meta */}
                        <div className="flex items-center gap-2 text-gray-400 text-sm mb-8 pb-8 border-b border-gray-100">
                            <Calendar className="h-4 w-4" />
                            <span>
                                {new Date(post.published_at).toLocaleDateString('id-ID', {
                                    weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
                                })}
                            </span>
                        </div>

                        {/* Content */}
                        {post.content && (
                            <div
                                className="prose prose-lg max-w-none prose-headings:font-heading prose-headings:text-brand-navy prose-a:text-brand-teal prose-img:rounded-xl prose-blockquote:border-brand-teal prose-blockquote:text-gray-500 mb-8"
                                dangerouslySetInnerHTML={{ __html: post.content }}
                            />
                        )}

                        {/* PDF Viewer/Downloader */}
                        {post.pdf_url && (
                            <div className="bg-brand-navy/5 rounded-xl border border-brand-teal/20 p-6 flex flex-col sm:flex-row items-center justify-between gap-4 mt-8">
                                <div className="flex items-center gap-4 text-brand-navy">
                                    <div className="p-3 bg-brand-teal/10 rounded-lg">
                                        <FileText className="h-8 w-8 text-brand-teal" />
                                    </div>
                                    <div>
                                        <h3 className="font-bold">Dokumen Lampiran (PDF)</h3>
                                        <p className="text-sm text-gray-500">Klik tombol di samping untuk melihat atau mengunduh dokumen.</p>
                                    </div>
                                </div>
                                <a 
                                    href={`/storage/${post.pdf_url}`} 
                                    target="_blank" 
                                    rel="noreferrer"
                                    className="w-full sm:w-auto px-6 py-2 bg-brand-teal text-white font-bold rounded hover:bg-brand-navy transition-colors text-center shadow-sm"
                                >
                                    Buka Dokumen
                                </a>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}
