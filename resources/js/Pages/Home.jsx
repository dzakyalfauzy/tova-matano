import PublicLayout from '@/Layouts/PublicLayout';
import { Head, Link } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import { ChevronLeft, ChevronRight } from 'lucide-react';

export default function Home({ slides, runningTexts, topMembers, programs, settings }) {
    console.log("Slides Data:", slides);
    
    // Simple Slider State
    const [currentSlide, setCurrentSlide] = useState(0);

    useEffect(() => {
        if (!slides || slides.length <= 1) return;
        const timer = setInterval(() => {
            setCurrentSlide((prev) => (prev + 1) % slides.length);
        }, 5000);
        return () => clearInterval(timer);
    }, [slides]);

    const nextSlide = () => {
        setCurrentSlide((prev) => (prev + 1) % slides.length);
    };

    const prevSlide = () => {
        setCurrentSlide((prev) => (prev - 1 + slides.length) % slides.length);
    };

    return (
        <PublicLayout settings={settings}>
            <Head title="Beranda" />

            {/* Hero Slider Area */}
            {slides && slides.length > 0 ? (
                <div className="relative h-[80vh] md:h-[600px] w-full overflow-hidden bg-brand-navy">
                    {slides.map((slide, index) => (
                        <div
                            key={slide.id}
                            className={`absolute inset-0 transition-opacity duration-1000 ease-in-out ${
                                index === currentSlide ? 'opacity-100 z-10' : 'opacity-0 z-0'
                            }`}
                        >
                            <div className="absolute inset-0 bg-black/50 z-10"></div> {/* Overlay */}
                            <img
                                src={`/storage/${slide.image_url}`}
                                alt={slide.title}
                                className="object-cover w-full h-full"
                            />
                            <div className="absolute inset-0 z-20 flex flex-col justify-center items-center text-center px-4">
                                {slide.title && (
                                    <h1 className="text-3xl md:text-5xl lg:text-6xl font-heading font-bold text-white mb-4 drop-shadow-md">
                                        {slide.title}
                                    </h1>
                                )}
                                {slide.subtitle && (
                                    <p className="text-lg md:text-xl text-gray-200 mb-8 max-w-3xl drop-shadow">
                                        {slide.subtitle}
                                    </p>
                                )}
                                {slide.cta_label && slide.cta_url && (
                                    <a
                                        href={slide.cta_url}
                                        className="px-8 py-3 bg-brand-gold text-brand-navy font-bold rounded hover:bg-amber-400 transition-colors shadow-lg"
                                    >
                                        {slide.cta_label}
                                    </a>
                                )}
                            </div>
                        </div>
                    ))}
                    
                    {/* Slider Controls */}
                    {slides.length > 1 && (
                        <>
                            <button
                                onClick={prevSlide}
                                className="absolute left-4 top-1/2 -translate-y-1/2 z-30 p-2 bg-black/30 hover:bg-black/50 text-white rounded-full transition-colors"
                            >
                                <ChevronLeft size={24} />
                            </button>
                            <button
                                onClick={nextSlide}
                                className="absolute right-4 top-1/2 -translate-y-1/2 z-30 p-2 bg-black/30 hover:bg-black/50 text-white rounded-full transition-colors"
                            >
                                <ChevronRight size={24} />
                            </button>
                            
                            {/* Indicators */}
                            <div className="absolute bottom-6 left-1/2 -translate-x-1/2 z-30 flex space-x-2">
                                {slides.map((_, index) => (
                                    <button
                                        key={index}
                                        onClick={() => setCurrentSlide(index)}
                                        className={`w-3 h-3 rounded-full transition-colors ${
                                            index === currentSlide ? 'bg-brand-gold' : 'bg-white/50 hover:bg-white'
                                        }`}
                                    />
                                ))}
                            </div>
                        </>
                    )}
                </div>
            ) : (
                <div className="bg-brand-teal text-white py-20 text-center">
                    <h1 className="text-4xl font-heading font-bold mb-4">Selamat Datang di Tova Matano Care Center</h1>
                    <p className="text-lg opacity-90 max-w-2xl mx-auto">
                        Belum ada hero slide yang aktif.
                    </p>
                </div>
            )}

            {/* Running Text Area */}
            {runningTexts && runningTexts.length > 0 && (
                <div className="bg-brand-navy border-y border-brand-gold/30 text-brand-gold py-3 overflow-hidden flex whitespace-nowrap group">
                    <div className="animate-marquee flex gap-12 pr-12 group-hover:[animation-play-state:paused]">
                        {/* Render teks */}
                        <p className="font-medium text-sm tracking-widest uppercase">
                            {runningTexts.map(text => text.text).join('   ✦   ')}
                        </p>
                        {/* Copy pertama supaya mulus */}
                        <p className="font-medium text-sm tracking-widest uppercase">
                            {runningTexts.map(text => text.text).join('   ✦   ')}
                        </p>
                        {/* Copy kedua supaya super mulus di layar ultra wide */}
                        <p className="font-medium text-sm tracking-widest uppercase">
                            {runningTexts.map(text => text.text).join('   ✦   ')}
                        </p>
                    </div>
                </div>
            )}

            {/* Portal Keanggotaan (Top Members) */}
            <div className="bg-white py-16 border-b border-gray-100">
                <div className="max-w-7xl mx-auto px-4">
                    <div className="text-center mb-12">
                        <h2 className="text-3xl font-heading font-bold text-brand-navy mb-4">Portal Keanggotaan</h2>
                        <p className="text-gray-600 max-w-2xl mx-auto">Kenali pahlawan lingkungan kami yang paling aktif berkontribusi untuk kelestarian Danau Matano.</p>
                    </div>
                    
                    <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                        {topMembers && topMembers.length > 0 ? (
                            topMembers.map(member => (
                                <div key={member.id} className="flex flex-col items-center text-center p-4 bg-brand-paper rounded-lg shadow-sm">
                                    <div className="w-24 h-24 rounded-full overflow-hidden mb-4 border-4 border-brand-teal">
                                        {member.photo_url ? (
                                            <img src={`/storage/${member.photo_url}`} alt={member.name} className="w-full h-full object-cover" />
                                        ) : (
                                            <div className="w-full h-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold text-xl">
                                                {member.name.charAt(0)}
                                            </div>
                                        )}
                                    </div>
                                    <h3 className="font-bold text-brand-navy mb-1">{member.name}</h3>
                                    <span className="inline-block px-2 py-1 bg-brand-gold/20 text-brand-gold text-xs font-bold rounded-full mb-2">
                                        {member.points} Poin
                                    </span>
                                    {member.divisi_name && (
                                        <p className="text-xs text-gray-500 uppercase tracking-wide">{member.divisi_name}</p>
                                    )}
                                </div>
                            ))
                        ) : (
                            <div className="col-span-full text-center text-gray-500">
                                Belum ada data anggota.
                            </div>
                        )}
                    </div>
                    
                    <div className="mt-10 text-center">
                        <Link href="/anggota" className="text-brand-teal font-semibold hover:text-brand-navy transition-colors flex items-center justify-center gap-1">
                            Lihat Semua Anggota <ChevronRight size={16} />
                        </Link>
                    </div>
                </div>
            </div>

            {/* Highlight Program */}
            <div className="bg-brand-paper py-16">
                <div className="max-w-7xl mx-auto px-4">
                    <div className="text-center mb-12">
                        <h2 className="text-3xl font-heading font-bold text-brand-navy mb-4">Program Unggulan</h2>
                        <p className="text-gray-600 max-w-2xl mx-auto">Langkah nyata kami dalam menjaga dan memulihkan ekosistem Danau Matano.</p>
                    </div>
                    
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        {programs && programs.length > 0 ? (
                            programs.map(program => (
                                <Link href={`/program/${program.slug}`} key={program.id} className="group bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow flex flex-col h-full">
                                    <div className="h-48 bg-gray-100 relative overflow-hidden flex items-center justify-center">
                                        {program.icon_url ? (
                                            <img src={`/storage/${program.icon_url}`} alt={program.name} className="object-cover w-full h-full group-hover:scale-105 transition-transform duration-300" />
                                        ) : (
                                            <div className="text-gray-400 font-heading text-xl">TMCC Program</div>
                                        )}
                                        <div className="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                                        <h3 className="absolute bottom-4 left-4 right-4 font-bold text-white text-lg">{program.name}</h3>
                                    </div>
                                    <div className="p-5 flex-grow">
                                        <p className="text-sm text-gray-600 line-clamp-3">{program.description}</p>
                                    </div>
                                    <div className="p-5 pt-0 mt-auto">
                                        <span className="text-brand-teal text-sm font-semibold group-hover:text-brand-gold transition-colors flex items-center">
                                            Pelajari lebih lanjut <ChevronRight size={16} className="ml-1" />
                                        </span>
                                    </div>
                                </Link>
                            ))
                        ) : (
                            <div className="col-span-full text-center text-gray-500 py-8 bg-white rounded-lg border border-dashed border-gray-300">
                                Belum ada program yang dipublikasikan.
                            </div>
                        )}
                    </div>
                </div>
            </div>

        </PublicLayout>
    );
}