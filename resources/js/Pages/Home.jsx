import PublicLayout from '@/Layouts/PublicLayout';
import { Head, Link } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import { ChevronLeft, ChevronRight, Trophy, Medal } from 'lucide-react';
import AboutSnippet from '@/Components/Public/AboutSnippet';

export default function Home({ slides, runningTexts, topMembers, programs, settings, homeStats, fixedText, heroFixedText }) {
    const staticFixedText = fixedText || heroFixedText;
    
    // Simple Slider State
    const [currentSlide, setCurrentSlide] = useState(0);

    useEffect(() => {
        if (!slides || slides.length <= 1) return;
        const timer = setInterval(() => {
            setCurrentSlide((prev) => (prev + 1) % slides.length);
        }, 8000);
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
                <div className="relative aspect-[16/9] md:aspect-auto md:h-[600px] w-full overflow-hidden bg-brand-navy">
                    {staticFixedText && (
                        <div className="absolute top-4 sm:top-6 md:top-12 lg:top-14 left-0 right-0 z-30 pointer-events-none text-center px-4">
                            <h1 className="text-lg sm:text-2xl md:text-4xl lg:text-5xl font-heading font-bold text-white tracking-wide [text-shadow:_0_2px_6px_rgba(0,0,0,0.9),_0_4px_16px_rgba(0,0,0,0.8)]">
                                {staticFixedText}
                            </h1>
                        </div>
                    )}

                    {slides.map((slide, index) => (
                        <div
                            key={slide.id}
                            className={`absolute inset-0 transition-opacity duration-1000 ease-in-out ${
                                index === currentSlide ? 'opacity-100 z-10' : 'opacity-0 z-0'
                            }`}
                        >
                            <img
                                src={`/storage/${slide.image_url}`}
                                alt={slide.title || 'Hero Slide'}
                                className="w-full h-full object-cover md:object-cover object-center"
                            />
                            <div className={`absolute inset-0 z-20 flex flex-col justify-end sm:justify-center items-center text-center px-4 pb-10 sm:pb-0 ${staticFixedText ? 'pt-12 sm:pt-16 md:pt-28' : 'pt-4 sm:pt-8'}`}>
                                {slide.title && (
                                    <h2 className="text-sm sm:text-xl md:text-3xl lg:text-4xl font-heading font-semibold text-white mb-1 sm:mb-3 max-w-4xl [text-shadow:_0_2px_4px_rgba(0,0,0,0.9),_0_4px_12px_rgba(0,0,0,0.7)]">
                                        {slide.title}
                                    </h2>
                                )}
                                {slide.subtitle && (
                                    <p className="text-xs sm:text-base md:text-xl text-white mb-2 sm:mb-6 max-w-3xl [text-shadow:_0_1px_3px_rgba(0,0,0,0.9),_0_2px_8px_rgba(0,0,0,0.7)] line-clamp-2 sm:line-clamp-none">
                                        {slide.subtitle}
                                    </p>
                                )}
                                {slide.cta_label && slide.cta_url && (
                                    <a
                                        href={slide.cta_url}
                                        className="px-4 sm:px-8 py-2 sm:py-3 text-sm sm:text-base bg-brand-gold text-brand-navy font-bold rounded hover:bg-amber-400 transition-colors shadow-lg pointer-events-auto"
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
                                className="absolute left-2 sm:left-4 top-1/2 -translate-y-1/2 z-30 p-1.5 sm:p-2 bg-black/30 hover:bg-black/50 text-white rounded-full transition-colors"
                            >
                                <ChevronLeft className="w-4 h-4 sm:w-6 sm:h-6" />
                            </button>
                            <button
                                onClick={nextSlide}
                                className="absolute right-2 sm:right-4 top-1/2 -translate-y-1/2 z-30 p-1.5 sm:p-2 bg-black/30 hover:bg-black/50 text-white rounded-full transition-colors"
                            >
                                <ChevronRight className="w-4 h-4 sm:w-6 sm:h-6" />
                            </button>
                            
                            {/* Indicators */}
                            <div className="absolute bottom-2 sm:bottom-6 left-1/2 -translate-x-1/2 z-30 flex space-x-1.5 sm:space-x-2">
                                {slides.map((_, index) => (
                                    <button
                                        key={index}
                                        onClick={() => setCurrentSlide(index)}
                                        className={`w-2 h-2 sm:w-3 sm:h-3 rounded-full transition-colors ${
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
                <div className="bg-brand-navy border-y border-brand-gold/30 text-brand-gold py-1.5 sm:py-2 md:py-3 overflow-hidden flex items-center whitespace-nowrap group">
                    <div className="animate-marquee flex gap-6 sm:gap-8 md:gap-12 pr-6 sm:pr-8 md:pr-12 group-hover:[animation-play-state:paused]">
                        <p className="font-bold text-[11px] sm:text-sm md:text-lg tracking-wide md:tracking-widest uppercase">
                            {runningTexts.map(text => text.text).join('   •   ')}
                        </p>
                        <p className="font-bold text-[11px] sm:text-sm md:text-lg tracking-wide md:tracking-widest uppercase">
                            {runningTexts.map(text => text.text).join('   •   ')}
                        </p>
                        <p className="font-bold text-[11px] sm:text-sm md:text-lg tracking-wide md:tracking-widest uppercase">
                            {runningTexts.map(text => text.text).join('   •   ')}
                        </p>
                    </div>
                </div>
            )}

            {/* ===== SECTION: Tentang Kami Snippet ===== */}
            <AboutSnippet homeStats={homeStats} />

            {/* Portal Keanggotaan (Top Members) */}
            <div className="bg-white py-16 border-b border-gray-100">
                <div className="max-w-7xl mx-auto px-4">
                    <div className="text-center mb-12">
                        <h2 className="text-3xl font-heading font-bold text-brand-navy mb-4">Portal Keanggotaan</h2>
                        <p className="text-gray-600 max-w-2xl mx-auto">Kenali pahlawan lingkungan kami yang paling aktif berkontribusi untuk kelestarian Danau Matano.</p>
                    </div>
                    
                    <div className="flex flex-wrap justify-center gap-6 items-end">
                        {topMembers && topMembers.length >= 3 ? (
                            <>
                                {/* Rank 2 */}
                                <div className="relative flex flex-col items-center flex-1 max-w-[200px] text-center p-4">
                                    <div className="w-20 h-20 rounded-full overflow-hidden border-4 border-gray-300 mb-3 bg-white z-10 relative">
                                        {topMembers[1].photo_url ? (
                                            <img src={`/storage/${topMembers[1].photo_url}`} alt={topMembers[1].name} className="w-full h-full object-cover" />
                                        ) : (
                                            <div className="w-full h-full bg-gray-200 flex items-center justify-center text-2xl font-bold text-gray-500">
                                                {topMembers[1].name.charAt(0)}
                                            </div>
                                        )}
                                        <div className="absolute -top-2 -right-2 bg-gray-400 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold border-2 border-white shadow-sm">2</div>
                                    </div>
                                    <h3 className="font-bold text-brand-navy mb-1 text-sm">{topMembers[1].name}</h3>
                                    <span className="text-xs font-bold text-gray-500 mb-2">{topMembers[1].points} poin</span>
                                    <div className="w-full bg-gray-200 rounded-t-xl h-24 flex items-center justify-center shadow-inner relative overflow-hidden">
                                        <div className="absolute inset-0 opacity-10 bg-[url('/image/pattern.svg')]"></div>
                                        <Medal className="h-10 w-10 text-gray-400 drop-shadow" />
                                    </div>
                                </div>

                                {/* Rank 1 */}
                                <div className="relative flex flex-col items-center flex-1 max-w-[220px] text-center p-4">
                                    <Trophy className="h-10 w-10 text-yellow-500 mb-2 animate-bounce" />
                                    <div className="w-28 h-28 rounded-full overflow-hidden border-4 border-yellow-400 mb-3 bg-white z-10 relative shadow-lg">
                                        {topMembers[0].photo_url ? (
                                            <img src={`/storage/${topMembers[0].photo_url}`} alt={topMembers[0].name} className="w-full h-full object-cover" />
                                        ) : (
                                            <div className="w-full h-full bg-yellow-50 flex items-center justify-center text-4xl font-bold text-yellow-600">
                                                {topMembers[0].name.charAt(0)}
                                            </div>
                                        )}
                                        <div className="absolute -top-2 -right-2 bg-yellow-500 text-white w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold border-2 border-white shadow-md">1</div>
                                    </div>
                                    <h3 className="font-bold text-brand-navy mb-1 text-lg">{topMembers[0].name}</h3>
                                    <span className="text-sm font-bold text-brand-gold mb-2">{topMembers[0].points} poin</span>
                                    <div className="w-full bg-gradient-to-b from-brand-gold/80 to-brand-gold rounded-t-xl h-36 flex flex-col items-center justify-center shadow-inner relative overflow-hidden">
                                        <div className="absolute inset-0 opacity-20 bg-[url('/image/pattern.svg')]"></div>
                                        <Trophy className="h-12 w-12 text-white drop-shadow-md" />
                                    </div>
                                </div>

                                {/* Rank 3 */}
                                <div className="relative flex flex-col items-center flex-1 max-w-[200px] text-center p-4">
                                    <div className="w-20 h-20 rounded-full overflow-hidden border-4 border-amber-600 mb-3 bg-white z-10 relative">
                                        {topMembers[2].photo_url ? (
                                            <img src={`/storage/${topMembers[2].photo_url}`} alt={topMembers[2].name} className="w-full h-full object-cover" />
                                        ) : (
                                            <div className="w-full h-full bg-amber-50 flex items-center justify-center text-2xl font-bold text-amber-700">
                                                {topMembers[2].name.charAt(0)}
                                            </div>
                                        )}
                                        <div className="absolute -top-2 -right-2 bg-amber-600 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold border-2 border-white shadow-sm">3</div>
                                    </div>
                                    <h3 className="font-bold text-brand-navy mb-1 text-sm">{topMembers[2].name}</h3>
                                    <span className="text-xs font-bold text-gray-500 mb-2">{topMembers[2].points} poin</span>
                                    <div className="w-full bg-amber-100 rounded-t-xl h-20 flex items-center justify-center shadow-inner relative overflow-hidden">
                                        <div className="absolute inset-0 opacity-10 bg-[url('/image/pattern.svg')]"></div>
                                        <Medal className="h-10 w-10 text-amber-600 drop-shadow" />
                                    </div>
                                </div>
                            </>
                        ) : (
                            <div className="w-full text-center py-8 text-gray-500 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                                Belum ada data anggota yang cukup.
                            </div>
                        )}
                    </div>
                    
                    <div className="mt-12 text-center">
                        <Link href="/anggota" className="inline-flex items-center justify-center px-6 py-3 border-2 border-brand-teal text-brand-teal font-bold rounded-lg hover:bg-brand-teal hover:text-white transition-colors">
                            Lihat Semua Anggota <ChevronRight size={20} className="ml-2" />
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