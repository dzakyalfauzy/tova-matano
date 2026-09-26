import { Link, usePage } from '@inertiajs/react';
import { Menu, X } from 'lucide-react';
import { useState } from 'react';

export default function Navigation() {
    const { url } = usePage();
    const [isOpen, setIsOpen] = useState(false);

    const navLinks = [
        { name: 'Beranda', href: '/' },
        { name: 'Tentang TMCC', href: '/tentang' },
        { name: 'Struktur Organisasi', href: '/struktur-organisasi' },
        { name: 'Program', href: '/program' },
        { name: 'Blog', href: '/blog' },
        { name: 'Kontak', href: '/kontak' },
    ];

    const isActive = (path) => {
        if (path === '/') {
            return url === '/';
        }
        return url.startsWith(path);
    };

    return (
        <nav className="bg-brand-navy/95 backdrop-blur-sm text-white sticky top-0 z-50 shadow-md">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="flex justify-between h-20 items-center">
                    {/* Logo Area */}
                    <div className="flex-shrink-0 flex items-center">
                        <Link href="/" className="flex items-center gap-3">
                            <img 
                                src="/image/logo_tova_matano.png" 
                                alt="Logo Tova Matano Care Center" 
                                className="h-10 w-auto"
                            />
                            <span className="font-heading font-bold text-2xl tracking-wide text-brand-paper hover:text-brand-gold transition-colors hidden sm:block">
                                TMCC
                            </span>
                        </Link>
                    </div>

                    {/* Desktop Menu */}
                    <div className="hidden md:flex items-center space-x-6">
                        {navLinks.map((link) => (
                            <Link
                                key={link.name}
                                href={link.href}
                                className={`text-sm font-medium transition-colors hover:text-brand-gold pb-1 border-b-2 ${
                                    isActive(link.href)
                                        ? 'border-brand-gold text-brand-gold'
                                        : 'border-transparent text-gray-200'
                                }`}
                            >
                                {link.name}
                            </Link>
                        ))}
                    </div>

                    {/* CTA Buttons */}
                    <div className="hidden md:flex items-center space-x-4">
                        <Link
                            href="/gabung-donasi"
                            className="px-4 py-2 text-sm font-medium border border-brand-gold text-brand-gold rounded hover:bg-brand-gold hover:text-brand-navy transition-colors"
                        >
                            Donasi
                        </Link>
                        <Link
                            href="/gabung-donasi"
                            className="px-4 py-2 text-sm font-medium bg-brand-gold text-brand-navy rounded hover:bg-amber-400 transition-colors shadow-sm"
                        >
                            Ayo Bergabung
                        </Link>
                    </div>

                    {/* Mobile menu button */}
                    <div className="md:hidden flex items-center">
                        <button
                            onClick={() => setIsOpen(!isOpen)}
                            className="text-gray-200 hover:text-white focus:outline-none"
                        >
                            {isOpen ? <X className="h-6 w-6" /> : <Menu className="h-6 w-6" />}
                        </button>
                    </div>
                </div>
            </div>

            {/* Mobile Menu */}
            {isOpen && (
                <div className="md:hidden bg-brand-navy/95 backdrop-blur-md border-t border-slate-700">
                    <div className="px-2 pt-2 pb-4 space-y-1 sm:px-3">
                        {navLinks.map((link) => (
                            <Link
                                key={link.name}
                                href={link.href}
                                onClick={() => setIsOpen(false)}
                                className={`block px-3 py-2 rounded-md text-base font-medium ${
                                    isActive(link.href)
                                        ? 'bg-brand-teal text-white'
                                        : 'text-gray-300 hover:bg-slate-700 hover:text-white'
                                }`}
                            >
                                {link.name}
                            </Link>
                        ))}
                        <div className="mt-4 pt-4 border-t border-slate-700 flex flex-col space-y-3 px-3">
                            <Link
                                href="/gabung-donasi"
                                className="w-full text-center px-4 py-2 border border-brand-gold text-brand-gold rounded hover:bg-brand-gold hover:text-brand-navy transition-colors"
                            >
                                Donasi
                            </Link>
                            <Link
                                href="/gabung-donasi"
                                className="w-full text-center px-4 py-2 bg-brand-gold text-brand-navy rounded hover:bg-amber-400 transition-colors"
                            >
                                Ayo Bergabung
                            </Link>
                        </div>
                    </div>
                </div>
            )}
        </nav>
    );
}