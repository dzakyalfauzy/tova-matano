import PublicLayout from '@/Layouts/PublicLayout';
import { Head, Link, usePage } from '@inertiajs/react';
import { ChevronRight } from 'lucide-react';

export default function About({ founders, settings, aboutContent, aboutLakeImageCredit }) {
    const { props: { translations: t } } = usePage();

    const splitParagraphs = (text) => {
        if (! text) return null;
        return text.split(/\n\s*\n/).map((p, i) => (
            <p key={i} className="text-base md:text-lg leading-relaxed text-gray-600 mb-4">
                {p}
            </p>
        ));
    };

    const splitLines = (text) => {
        if (! text) return null;
        return text.split('\n').filter(Boolean).map((line, i) => (
            <li key={i} className="flex items-start space-x-3">
                <ChevronRight className="h-5 w-5 text-brand-gold shrink-0 mt-0.5" />
                <span className="text-gray-600">{line}</span>
            </li>
        ));
    };

    const legalItems = aboutContent?.legal?.items;
    const hasLegalContent = aboutContent?.legal?.verified === true
        && typeof legalItems === 'string'
        && legalItems.trim() !== '';

    return (
        <PublicLayout settings={settings}>
            <Head title={t.about_title} />

            {/* Hero */}
            <div className="relative bg-brand-navy py-16 md:py-32 text-center border-b-4 border-brand-teal overflow-hidden">
                <div className="absolute inset-0 z-0">
                    <img 
                        src={settings?.about_hero_image_url ? `/storage/${settings.about_hero_image_url}` : "/image/logo_tova_matano.png"} 
                        alt={t.about_hero_image_alt} 
                        className={`w-full h-full object-cover ${settings?.about_hero_image_url ? 'opacity-30' : 'opacity-10'}`}
                    />
                    <div className="absolute inset-0 bg-brand-navy/80 mix-blend-multiply"></div>
                </div>
                <div className="max-w-4xl mx-auto px-4 relative z-10">
                    {aboutContent?.hero?.eyebrow && (
                        <p className="text-xs md:text-sm font-semibold uppercase tracking-[0.3em] text-brand-gold/80 mb-4">
                            {aboutContent.hero.eyebrow}
                        </p>
                    )}
                    <h1 className="text-4xl md:text-6xl font-heading font-bold text-brand-gold mb-6 drop-shadow-md">
                        {aboutContent?.hero?.title || t.about_title}
                    </h1>
                    <p className="text-xl md:text-2xl text-gray-200 drop-shadow">
                        {aboutContent?.hero?.subtitle || t.about_subtitle}
                    </p>
                </div>
            </div>

            {/* Sekilas Danau Matano */}
            <div className="py-16 md:py-24 bg-white">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                        <div className="order-1 lg:order-1 relative">
                            <div className="absolute inset-0 bg-brand-teal rounded-2xl transform -translate-x-4 translate-y-4"></div>
                            <img 
                                src={settings?.about_lake_image_url ? `/storage/${settings.about_lake_image_url}` : "/image/danau-matano.jpg"} 
                                alt={t.about_lake_image_alt} 
                                onError={(e) => { e.target.src = '/image/logo_tova_matano.png'; e.target.className = 'relative rounded-2xl shadow-xl w-full h-auto object-contain bg-white p-8 aspect-[4/3] z-10' }}
                                className="relative rounded-2xl shadow-xl w-full h-auto object-cover aspect-[4/3] z-10"
                            />
                            {aboutLakeImageCredit && typeof aboutLakeImageCredit === 'string' && aboutLakeImageCredit.trim() !== '' && (
                                <div className="absolute bottom-2 right-2 text-[10px] text-white/70 opacity-80">
                                    {aboutLakeImageCredit}
                                </div>
                            )}
                        </div>
                        <div className="order-2 lg:order-2">
                            <h2 className="text-3xl font-heading font-bold text-brand-navy mb-6">
                                {aboutContent?.lake?.title || t.lake_background}
                            </h2>
                            {splitParagraphs(aboutContent?.lake?.body)}
                        </div>
                    </div>
                </div>
            </div>

            {/* Profil & Sejarah */}
            <div className="py-16 md:py-24 bg-brand-paper">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                        <div className="order-2 lg:order-1">
                            <h2 className="text-3xl font-heading font-bold text-brand-navy mb-6">
                                {aboutContent?.profile?.title || t.profile_history}
                            </h2>
                            {splitParagraphs(aboutContent?.profile?.body)}
                        </div>
                        <div className="relative w-full max-w-md mx-auto order-1 lg:order-2">
                            <div className="absolute inset-0 bg-brand-gold rounded-2xl transform translate-x-4 translate-y-4"></div>
                            <img 
                                src="/image/logo_tova_matano_full.png" 
                                alt={t.about_logo_alt} 
                                className="relative rounded-2xl shadow-xl w-full h-auto object-contain bg-white p-8 aspect-[4/3] z-10"
                            />
                        </div>
                    </div>
                </div>
            </div>

            {/* Potensi TMCC */}
            <div className="py-16 md:py-24 bg-white">
                <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="text-center mb-10">
                        <h2 className="text-3xl font-heading font-bold text-brand-navy mb-4">
                            {aboutContent?.potential?.title || t.potential}
                        </h2>
                        <div className="w-24 h-1 bg-brand-teal mx-auto"></div>
                    </div>
                    {splitParagraphs(aboutContent?.potential?.body)}
                </div>
            </div>

            {/* Legalitas: server-gated, section hidden entirely unless verified with valid items */}
            {hasLegalContent && (
                <div className="py-16 md:py-24 bg-brand-paper">
                    <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="text-center mb-10">
                            <h2 className="text-3xl font-heading font-bold text-brand-navy mb-4">
                                {aboutContent?.legal?.title || t.legal}
                            </h2>
                            <div className="w-24 h-1 bg-brand-teal mx-auto"></div>
                        </div>
                        <ul className="list-disc pl-5 space-y-2 text-gray-600 leading-relaxed">
                            {legalItems.split('\n').filter(item => item.trim()).map((item, i) => (
                                <li key={i}>{item}</li>
                            ))}
                        </ul>
                    </div>
                </div>
            )}

            {/* Visi, Misi & Tujuan */}
            <div className="py-16 md:py-24 bg-white">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="text-center mb-16">
                        <h2 className="text-3xl font-heading font-bold text-brand-navy mb-4">
                            {t.vision_mission_goal}
                        </h2>
                        <div className="w-24 h-1 bg-brand-teal mx-auto"></div>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-3 gap-8">

                        {/* Visi */}
                        <div className="bg-white p-8 rounded-xl shadow-sm border-t-4 border-brand-teal text-center">
                            <div className="w-16 h-16 bg-brand-teal/10 rounded-full flex items-center justify-center mx-auto mb-6">
                                <span className="text-2xl font-bold text-brand-teal">{t.vision.charAt(0)}</span>
                            </div>
                            <h3 className="text-xl font-bold text-brand-navy mb-4">{t.vision}</h3>
                            <p className="text-gray-600 leading-relaxed">
                                {aboutContent?.vision || ''}
                            </p>
                        </div>

                        {/* Misi */}
                        <div className="bg-white p-8 rounded-xl shadow-sm border-t-4 border-brand-gold text-center transform md:-translate-y-4">
                            <div className="w-16 h-16 bg-brand-gold/10 rounded-full flex items-center justify-center mx-auto mb-6">
                                <span className="text-2xl font-bold text-brand-gold">{t.mission.charAt(0)}</span>
                            </div>
                            <h3 className="text-xl font-bold text-brand-navy mb-4">{t.mission}</h3>
                            <ul className="text-gray-600 text-left space-y-3">
                                {splitLines(aboutContent?.mission)}
                            </ul>
                        </div>

                        {/* Tujuan */}
                        <div className="bg-white p-8 rounded-xl shadow-sm border-t-4 border-brand-navy text-center">
                            <div className="w-16 h-16 bg-brand-navy/10 rounded-full flex items-center justify-center mx-auto mb-6">
                                <span className="text-2xl font-bold text-brand-navy">{t.goal.charAt(0)}</span>
                            </div>
                            <h3 className="text-xl font-bold text-brand-navy mb-4">{t.goal}</h3>
                            <ul className="text-gray-600 text-left space-y-3">
                                {splitLines(aboutContent?.goal)}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {/* Founders Section */}
            <div className="py-16 md:py-24 bg-brand-paper">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="text-center mb-16">
                        <h2 className="text-3xl font-heading font-bold text-brand-navy mb-4">
                            {aboutContent?.founders?.title || t.founders}
                        </h2>
                        <p className="text-gray-600 max-w-2xl mx-auto leading-relaxed">
                            {aboutContent?.founders?.narrative || t.founders_tagline}
                        </p>
                    </div>

                    <div className="flex flex-wrap justify-center gap-8">
                        {founders && founders.length > 0 ? (
                            founders.map(founder => (
                                <div key={founder.id} className="group w-full max-w-[200px] text-center">
                                    <div className="relative overflow-hidden rounded-xl aspect-[3/4] mb-3 bg-gray-100 shadow-sm mx-auto">
                                        {founder.photo_url ? (
                                            <img 
                                                src={`/storage/${founder.photo_url}`} 
                                                alt={founder.name} 
                                                className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                            />
                                        ) : (
                                            <div className="w-full h-full flex items-center justify-center text-4xl text-gray-300 font-heading">
                                                {founder.name.charAt(0)}
                                            </div>
                                        )}
                                        <div className="absolute inset-0 bg-gradient-to-t from-brand-navy/90 via-brand-navy/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
                                            <p className="text-white text-xs font-medium">{t.founder_dedication}</p>
                                        </div>
                                    </div>
                                    <h3 className="text-lg font-bold text-brand-navy">{founder.name}</h3>
                                    <p className="text-brand-teal text-sm font-medium mb-0.5">{founder.role}</p>
                                    {founder.org_name && (
                                        <p className="text-xs text-gray-500">{founder.org_name}</p>
                                    )}
                                </div>
                            ))
                        ) : (
                            <div className="w-full text-center py-12 text-gray-500 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                                {t.founders_empty}
                            </div>
                        )}
                    </div>
                </div>
            </div>

            {/* CTA Section */}
            <div className="bg-brand-navy py-16 relative overflow-hidden">
                <div className="absolute top-0 left-0 w-full h-1 bg-brand-gold"></div>
                <div className="max-w-4xl mx-auto px-4 text-center relative z-10">
                    <h2 className="text-3xl font-heading font-bold text-white mb-6">{t.cta_title}</h2>
                    <p className="text-gray-300 mb-8 text-lg leading-relaxed">
                        {t.cta_body}
                    </p>
                    <div className="flex flex-col sm:flex-row justify-center gap-4">
                        <Link href="/gabung-donasi" className="px-8 py-3 bg-brand-gold text-brand-navy font-bold rounded hover:bg-amber-400 transition-colors">
                            {t.nav_join}
                        </Link>
                        <Link href="/kontak" className="px-8 py-3 border border-gray-400 text-white font-bold rounded hover:border-white hover:bg-white/10 transition-colors">
                            {t.contact_us}
                        </Link>
                    </div>
                </div>
            </div>

        </PublicLayout>
    );
}