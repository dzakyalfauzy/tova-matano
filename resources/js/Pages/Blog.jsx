import PublicLayout from '@/Layouts/PublicLayout';
import { Head, Link, router } from '@inertiajs/react';
import { Calendar, Tag } from 'lucide-react';

const CATEGORIES = [
    { value: '', label: 'Semua' },
    { value: 'EDUKASI', label: 'Edukasi' },
    { value: 'RILIS_PERS', label: 'Rilis Pers' },
    { value: 'BERITA', label: 'Berita' },
    { value: 'KEBIJAKAN', label: 'Kebijakan' },
    { value: 'BASELINE_STUDY', label: 'Baseline Study' },
];

const CATEGORY_COLORS = {
    EDUKASI: 'bg-green-100 text-green-700',
    RILIS_PERS: 'bg-blue-100 text-blue-700',
    BERITA: 'bg-orange-100 text-orange-700',
    KEBIJAKAN: 'bg-purple-100 text-purple-700',
    BASELINE_STUDY: 'bg-brand-gold/20 text-brand-navy border border-brand-gold/30',
};

export default function Blog({ posts, filters, settings }) {
    const activeCategory = filters?.category || '';

    const filterByCategory = (cat) => {
        router.get('/blog', cat ? { category: cat } : {}, { preserveState: true });
    };

    return (
        <PublicLayout settings={settings}>
            <Head title="Tova Blog" />

            {/* Header */}
            <div className="bg-brand-navy py-16 md:py-24 text-center border-b-4 border-brand-teal">
                <div className="max-w-4xl mx-auto px-4">
                    <h1 className="text-4xl md:text-5xl font-heading font-bold text-brand-gold mb-4">Tova Blog</h1>
                    <p className="text-xl text-gray-200">Edukasi, rilis pers, dan kabar terbaru dari lapangan.</p>
                </div>
            </div>

            <div className="bg-brand-paper py-12">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                    {/* Category Filter */}
                    <div className="flex flex-wrap gap-2 justify-center mb-10">
                        {CATEGORIES.map(cat => (
                            <button
                                key={cat.value}
                                onClick={() => filterByCategory(cat.value)}
                                className={`px-5 py-2 rounded-full text-sm font-semibold transition-colors ${
                                    activeCategory === cat.value
                                        ? 'bg-brand-teal text-white shadow-md'
                                        : 'bg-white text-gray-600 hover:bg-brand-teal/10 border border-gray-200'
                                }`}
                            >
                                {cat.label}
                            </button>
                        ))}
                    </div>

                    {/* Grid Posts */}
                    {posts?.data && posts.data.length > 0 ? (
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            {posts.data.map(post => (
                                <Link
                                    key={post.id}
                                    href={`/blog/${post.slug}`}
                                    className="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all border border-gray-100 flex flex-col"
                                >
                                    {/* Cover */}
                                    <div className="h-48 bg-gray-100 overflow-hidden relative">
                                        {post.cover_image_url ? (
                                            <img
                                                src={`/storage/${post.cover_image_url}`}
                                                alt={post.title}
                                                className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                            />
                                        ) : (
                                            <div className="w-full h-full flex items-center justify-center bg-brand-teal/5">
                                                <Tag className="h-12 w-12 text-brand-teal/20" />
                                            </div>
                                        )}
                                        {post.category && (
                                            <span className={`absolute top-3 left-3 px-2.5 py-1 text-xs font-bold rounded-full ${CATEGORY_COLORS[post.category] || 'bg-gray-100 text-gray-600'}`}>
                                                {CATEGORIES.find(c => c.value === post.category)?.label || post.category}
                                            </span>
                                        )}
                                    </div>

                                    {/* Content */}
                                    <div className="p-6 flex flex-col flex-grow">
                                        <h2 className="font-heading font-bold text-brand-navy text-lg mb-3 line-clamp-2 group-hover:text-brand-teal transition-colors">
                                            {post.title}
                                        </h2>
                                        <div className="flex items-center gap-2 text-gray-400 text-xs mt-auto pt-4 border-t border-gray-50">
                                            <Calendar className="h-3.5 w-3.5" />
                                            <span>
                                                {new Date(post.published_at).toLocaleDateString('id-ID', {
                                                    day: 'numeric', month: 'long', year: 'numeric'
                                                })}
                                            </span>
                                        </div>
                                    </div>
                                </Link>
                            ))}
                        </div>
                    ) : (
                        <div className="text-center py-20 bg-white rounded-2xl border border-dashed border-gray-200">
                            <p className="text-gray-400 text-lg">Belum ada artikel yang dipublikasikan.</p>
                        </div>
                    )}

                    {/* Pagination */}
                    {posts?.links && posts.links.length > 3 && (
                        <div className="flex justify-center gap-2 mt-12">
                            {posts.links.map((link, i) => (
                                <button
                                    key={i}
                                    disabled={!link.url}
                                    onClick={() => link.url && router.visit(link.url)}
                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                    className={`px-4 py-2 rounded-lg text-sm font-medium transition-colors ${
                                        link.active
                                            ? 'bg-brand-teal text-white'
                                            : link.url
                                                ? 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200'
                                                : 'bg-gray-50 text-gray-300 cursor-not-allowed'
                                    }`}
                                />
                            ))}
                        </div>
                    )}
                </div>
            </div>
        </PublicLayout>
    );
}
