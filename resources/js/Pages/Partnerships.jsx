import PublicLayout from '@/Layouts/PublicLayout';
import { Head } from '@inertiajs/react';

export default function Partnerships({ partnerships, settings }) {
    // Group partnerships by type
    const groupedPartnerships = partnerships?.reduce((acc, partner) => {
        const type = partner.type || 'partner';
        if (!acc[type]) acc[type] = [];
        acc[type].push(partner);
        return acc;
    }, {});

    const getTypeLabel = (type) => {
        switch (type) {
            case 'sponsor': return 'Sponsor';
            case 'media': return 'Media Partner';
            default: return 'Mitra / Partner';
        }
    };

    return (
        <PublicLayout settings={settings}>
            <Head title="Partnership" />

            {/* Page Header */}
            <div className="bg-brand-navy py-16 md:py-24 text-center border-b-4 border-brand-teal">
                <div className="max-w-4xl mx-auto px-4">
                    <h1 className="text-4xl md:text-5xl font-heading font-bold text-brand-gold mb-4">Partnership</h1>
                    <p className="text-xl text-gray-200">
                        Mitra dan pendukung yang telah berkontribusi bersama Tova Matano Care Center.
                    </p>
                </div>
            </div>

            {/* Partnerships Content */}
            <div className="bg-brand-paper py-16">
                <div className="max-w-7xl mx-auto px-4">
                    
                    {!groupedPartnerships || Object.keys(groupedPartnerships).length === 0 ? (
                        <div className="text-center text-gray-500 py-12 bg-white rounded-lg border border-dashed border-gray-300">
                            Belum ada data partnership.
                        </div>
                    ) : (
                        Object.keys(groupedPartnerships).map((type) => (
                            <div key={type} className="mb-16 last:mb-0">
                                <h2 className="text-3xl font-heading font-bold text-brand-navy mb-8 text-center uppercase tracking-wider">
                                    {getTypeLabel(type)}
                                </h2>
                                
                                <div className="flex flex-wrap justify-center gap-8">
                                    {groupedPartnerships[type].map(partner => (
                                        <div key={partner.id} className="w-48 sm:w-56 bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col items-center hover:shadow-md transition-shadow group">
                                            <a 
                                                href={partner.website_url || '#'} 
                                                target="_blank" 
                                                rel="noreferrer"
                                                className={`w-full flex-grow flex flex-col items-center ${!partner.website_url && 'cursor-default'}`}
                                            >
                                                <div className="h-32 w-full flex items-center justify-center mb-4 p-2 bg-gray-50 rounded-lg group-hover:bg-gray-100 transition-colors">
                                                    {partner.logo_url ? (
                                                        <img 
                                                            src={`/storage/${partner.logo_url}`} 
                                                            alt={partner.name} 
                                                            className="max-h-full max-w-full object-contain filter grayscale group-hover:grayscale-0 transition-all duration-300" 
                                                        />
                                                    ) : (
                                                        <span className="text-gray-400 font-heading font-bold text-xl text-center group-hover:text-brand-teal transition-colors">
                                                            {partner.name}
                                                        </span>
                                                    )}
                                                </div>
                                                <h3 className="font-bold text-brand-navy text-center mb-2">{partner.name}</h3>
                                                {partner.description && (
                                                    <p className="text-sm text-gray-500 text-center line-clamp-3">{partner.description}</p>
                                                )}
                                            </a>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        ))
                    )}
                    
                </div>
            </div>
            
            {/* CTA Section */}
            <div className="bg-brand-teal py-16 text-white text-center">
                <div className="max-w-3xl mx-auto px-4">
                    <h2 className="text-3xl font-heading font-bold mb-4">Tertarik Menjadi Mitra Kami?</h2>
                    <p className="text-lg opacity-90 mb-8">
                        Mari berkolaborasi bersama Tova Matano Care Center untuk menjaga kelestarian lingkungan danau dan alam sekitarnya.
                    </p>
                    <a 
                        href="/kontak" 
                        className="inline-block px-8 py-3 bg-brand-gold text-brand-navy font-bold rounded hover:bg-amber-400 transition-colors shadow-lg"
                    >
                        Hubungi Kami
                    </a>
                </div>
            </div>

        </PublicLayout>
    );
}