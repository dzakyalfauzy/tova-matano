import { Link, router, usePage } from '@inertiajs/react';
import { Globe, Menu, X } from 'lucide-react';
import { useState } from 'react';

export default function Navigation() {
    const { url, props } = usePage();
    const { locale, translations: t } = props;
    const [isOpen, setIsOpen] = useState(false);

    const navLinks = [
        { name: t.nav_home, href: '/' },
        { name: t.nav_about, href: '/tentang' },
        { name: t.nav_organization, href: '/struktur-organisasi' },
        { name: t.nav_members, href: '/anggota' },
        { name: t.nav_partnership, href: '/partnership' },
        { name: t.nav_program, href: '/program' },
        { name: t.nav_water, href: '/kualitas-air' },
        { name: t.nav_gallery, href: '/galeri' },
        { name: t.nav_blog, href: '/blog' },
        { name: t.nav_contact, href: '/kontak' },
    ];

    const isActive = (path) => {
        if (path === '/') return url === '/';
        return url.startsWith(path);
    };

    const switchLocale = (newLocale) => {
        router.post('/locale', { locale: newLocale }, { preserveScroll: true });
    };

    return (
        <nav className="bg-brand-navy text-white sticky top-0 z-50 shadow-lg">
            {/* Top bar: Logo + Language + CTA */}
            <div className="border-b border-white/10">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex items-center justify-between h-16">
                        {/* Logo */}
                        <Link href="/" className="flex items-center gap-3 flex-shrink-0">
                            <img
                                src="/image/logo_tova_matano.png"
                                alt="Logo Tova Matano Care Center"
                                className="h-9 w-auto"
                            />
                            <span className="font-heading font-bold text-xl tracking-wide text-brand-paper hover:text-brand-gold transition-colors hidden sm:block">
                                Tova Matano Care Center
                            </span>
                        </Link>

                        {/* Desktop: Language + CTA */}
                        <div className="hidden md:flex items-center gap-3">
                            {/* Language Switcher */}
                            <div className="flex items-center border border-white/20 rounded-lg overflow-hidden">
                                <button
                                    onClick={() => switchLocale('id')}
                                    title="Bahasa Indonesia"
                                    className={`flex items-center gap-1 px-3 py-1.5 text-xs font-bold transition-colors ${
                                        locale === 'id'
                                            ? 'bg-brand-gold text-brand-navy'
                                            : 'text-gray-300 hover:text-white hover:bg-white/10'
                                    }`}
                                >
                                    <Globe className="h-3 w-3" /> ID
                                </button>
                                <button
                                    onClick={() => switchLocale('en')}
                                    title="English"
                                    className={`px-3 py-1.5 text-xs font-bold transition-colors ${
                                        locale === 'en'
                                            ? 'bg-brand-gold text-brand-navy'
                                            : 'text-gray-300 hover:text-white hover:bg-white/10'
                                    }`}
                                >
                                    EN
                                </button>
                            </div>

                            <Link
                                href="/gabung-donasi"
                                className="px-4 py-2 text-sm font-semibold border border-brand-gold text-brand-gold rounded-lg hover:bg-brand-gold hover:text-brand-navy transition-colors"
                            >
                                {t.nav_donate}
                            </Link>
                            <Link
                                href="/gabung-donasi"
                                className="px-4 py-2 text-sm font-semibold bg-brand-gold text-brand-navy rounded-lg hover:bg-amber-400 transition-colors shadow-sm"
                            >
                                {t.nav_join}
                            </Link>
                        </div>

                        {/* Mobile: Language + Hamburger */}
                        <div className="md:hidden flex items-center gap-2">
                            <div className="flex items-center border border-white/20 rounded-lg overflow-hidden">
                                <button
                                    onClick={() => switchLocale('id')}
                                    className={`px-2.5 py-1 text-xs font-bold transition-colors ${
                                        locale === 'id' ? 'bg-brand-gold text-brand-navy' : 'text-gray-300'
                                    }`}
                                >
                                    ID
                                </button>
                                <button
                                    onClick={() => switchLocale('en')}
                                    className={`px-2.5 py-1 text-xs font-bold transition-colors ${
                                        locale === 'en' ? 'bg-brand-gold text-brand-navy' : 'text-gray-300'
                                    }`}
                                >
                                    EN
                                </button>
                            </div>
                            <button
                                onClick={() => setIsOpen(!isOpen)}
                                className="p-1.5 text-gray-200 hover:text-white focus:outline-none"
                            >
                                {isOpen ? <X className="h-6 w-6" /> : <Menu className="h-6 w-6" />}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {/* Bottom bar: Nav Links (desktop only) */}
            <div className="hidden md:block bg-brand-navy/80 backdrop-blur-sm">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex items-center gap-1">
                        {navLinks.map((link) => (
                            <Link
                                key={link.href}
                                href={link.href}
                                className={`relative px-4 py-3 text-sm font-medium transition-colors whitespace-nowrap ${
                                    isActive(link.href)
                                        ? 'text-brand-gold'
                                        : 'text-gray-300 hover:text-white'
                                }`}
                            >
                                {link.name}
                                {isActive(link.href) && (
                                    <span className="absolute bottom-0 left-0 right-0 h-0.5 bg-brand-gold rounded-t-full" />
                                )}
                            </Link>
                        ))}
                    </div>
                </div>
            </div>

            {/* Mobile Menu */}
            {isOpen && (
                <div className="md:hidden bg-brand-navy border-t border-white/10">
                    <div className="px-3 pt-2 pb-4 space-y-1">
                        {navLinks.map((link) => (
                            <Link
                                key={link.href}
                                href={link.href}
                                onClick={() => setIsOpen(false)}
                                className={`flex items-center px-4 py-2.5 rounded-lg text-sm font-medium transition-colors ${
                                    isActive(link.href)
                                        ? 'bg-brand-teal/20 text-brand-gold border-l-2 border-brand-gold'
                                        : 'text-gray-300 hover:bg-white/10 hover:text-white'
                                }`}
                            >
                                {link.name}
                            </Link>
                        ))}
                        <div className="pt-3 mt-3 border-t border-white/10 flex flex-col gap-2 px-1">
                            <Link
                                href="/gabung-donasi"
                                className="w-full text-center px-4 py-2.5 border border-brand-gold text-brand-gold rounded-lg hover:bg-brand-gold hover:text-brand-navy transition-colors text-sm font-semibold"
                            >
                                {t.nav_donate}
                            </Link>
                            <Link
                                href="/gabung-donasi"
                                className="w-full text-center px-4 py-2.5 bg-brand-gold text-brand-navy rounded-lg hover:bg-amber-400 transition-colors text-sm font-semibold"
                            >
                                {t.nav_join}
                            </Link>
                        </div>
                    </div>
                </div>
            )}
        </nav>
    );
}
