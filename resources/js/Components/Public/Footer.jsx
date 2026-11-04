import { Link } from '@inertiajs/react';
import { MessageCircle } from 'lucide-react'; // MessageCircle as fallback for Tiktok if missing in lucide basic

export default function Footer({ settings }) {
    const currentYear = new Date().getFullYear();

    return (
        <footer className="bg-brand-navy text-white pt-12 pb-8 border-t-4 border-brand-teal">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                    {/* Brand & About */}
                    <div className="col-span-1 md:col-span-1">
                        <h3 className="font-heading font-bold text-2xl text-brand-gold mb-4">TMCC</h3>
                        <p className="text-gray-300 text-sm leading-relaxed mb-6">
                            Tova Matano Care Center (TMCC) adalah organisasi peduli lingkungan yang berfokus pada pemulihan dan pelestarian Danau Matano dari ancaman pencemaran.
                        </p>
                        <div className="flex space-x-4">
                            {settings?.instagram_url && (
                                <a href={settings.instagram_url} target="_blank" rel="noopener noreferrer" className="text-gray-400 hover:text-brand-gold transition-colors">
                                    IG
                                </a>
                            )}
                            {settings?.facebook_url && (
                                <a href={settings.facebook_url} target="_blank" rel="noopener noreferrer" className="text-gray-400 hover:text-brand-gold transition-colors">
                                    FB
                                </a>
                            )}
                            {settings?.twitter_url && (
                                <a href={settings.twitter_url} target="_blank" rel="noopener noreferrer" className="text-gray-400 hover:text-brand-gold transition-colors">
                                    X
                                </a>
                            )}
                            {settings?.tiktok_url && (
                                <a href={settings.tiktok_url} target="_blank" rel="noopener noreferrer" className="text-gray-400 hover:text-brand-gold transition-colors">
                                    <MessageCircle className="h-5 w-5" />
                                </a>
                            )}
                        </div>
                    </div>

                    {/* Quick Links */}
                    <div className="col-span-1">
                        <h4 className="font-heading font-semibold text-lg mb-4 text-brand-paper">Tautan Cepat</h4>
                        <ul className="space-y-2 text-sm">
                            <li><Link href="/tentang" className="text-gray-300 hover:text-brand-gold transition-colors">Tentang Kami</Link></li>
                            <li><Link href="/struktur-organisasi" className="text-gray-300 hover:text-brand-gold transition-colors">Struktur Organisasi</Link></li>
                            <li><Link href="/anggota" className="text-gray-300 hover:text-brand-gold transition-colors">Anggota & Relawan</Link></li>
                            <li><Link href="/program" className="text-gray-300 hover:text-brand-gold transition-colors">Program Utama</Link></li>
                            <li><Link href="/galeri" className="text-gray-300 hover:text-brand-gold transition-colors">Galeri Dokumentasi</Link></li>
                            <li><Link href="/blog" className="text-gray-300 hover:text-brand-gold transition-colors">Berita & Edukasi</Link></li>
                            <li><Link href="/kontak" className="text-gray-300 hover:text-brand-gold transition-colors">Hubungi Kami</Link></li>
                        </ul>
                    </div>

                    {/* Contact Info */}
                    <div className="col-span-1">
                        <h4 className="font-heading font-semibold text-lg mb-4 text-brand-paper">Kontak</h4>
                        <address className="not-italic text-sm text-gray-300 space-y-3">
                            <p>
                                {settings?.address || 'Jl. Raya Danau Matano No. 1, Sorowako, Kec. Nuha, Kab. Luwu Timur, Sulawesi Selatan'}
                            </p>
                            {settings?.phone && (
                                <p>
                                    <a href={`tel:${settings.phone}`} className="hover:text-brand-gold transition-colors">
                                        {settings.phone}
                                    </a>
                                </p>
                            )}
                            {settings?.email && (
                                <p>
                                    <a href={`mailto:${settings.email}`} className="hover:text-brand-gold transition-colors">
                                        {settings.email}
                                    </a>
                                </p>
                            )}
                        </address>
                    </div>
                </div>

                <div className="border-t border-slate-700 pt-8 mt-8 text-center text-sm text-gray-400">
                    <p>&copy; {currentYear} Tova Matano Care Center. Hak cipta dilindungi undang-undang.</p>
                </div>
            </div>
        </footer>
    );
}