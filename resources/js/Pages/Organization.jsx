import PublicLayout from '@/Layouts/PublicLayout';
import { Head } from '@inertiajs/react';
import { Disclosure, Transition } from '@headlessui/react';
import { ChevronDown, Users, UserCircle2 } from 'lucide-react';

export default function Organization({ categories, settings }) {
    // Helper function to render a person card based on display type
    const renderPersonCard = (person, displayType) => {
        if (displayType === 'ROLE_LIST') {
            return (
                <div key={person.id} className="flex items-center gap-4 py-3 border-b border-gray-100 last:border-0 hover:bg-gray-50 transition-colors px-4 rounded-lg">
                    {person.photo_url ? (
                        <img 
                            src={`/storage/${person.photo_url}`} 
                            alt={person.name} 
                            className="w-12 h-12 rounded-full object-cover shadow-sm"
                        />
                    ) : (
                        <div className="w-12 h-12 rounded-full bg-brand-navy/5 flex items-center justify-center text-brand-navy shrink-0">
                            <UserCircle2 size={24} />
                        </div>
                    )}
                    <div className="flex-grow">
                        <p className="font-bold text-brand-navy">{person.name}</p>
                        {person.divisi_name && (
                            <p className="text-xs text-gray-500">{person.divisi_name}</p>
                        )}
                    </div>
                    <div className="text-right shrink-0 max-w-[40%]">
                        <span className="inline-block px-3 py-1 bg-brand-teal/10 text-brand-teal text-xs font-semibold rounded-full">
                            {person.role_title}
                        </span>
                    </div>
                </div>
            );
        }

        // PERSON_GRID display
        return (
            <div key={person.id} className="bg-white border border-gray-100 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow group">
                <div className="aspect-square bg-gray-100 relative overflow-hidden">
                    {person.photo_url ? (
                        <img 
                            src={`/storage/${person.photo_url}`} 
                            alt={person.name} 
                            className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        />
                    ) : (
                        <div className="w-full h-full flex items-center justify-center text-brand-teal/30 bg-brand-teal/5">
                            <Users size={64} strokeWidth={1} />
                        </div>
                    )}
                    <div className="absolute top-3 right-3">
                         <span className="inline-block px-2.5 py-1 bg-white/90 backdrop-blur text-brand-navy text-[10px] font-bold tracking-wider uppercase rounded-full shadow-sm">
                            {person.role_title}
                        </span>
                    </div>
                </div>
                <div className="p-5 text-center">
                    <h4 className="font-bold text-brand-navy text-lg">{person.name}</h4>
                    {person.divisi_name && (
                        <p className="text-sm text-gray-500 mt-1">{person.divisi_name}</p>
                    )}
                </div>
            </div>
        );
    };

    return (
        <PublicLayout settings={settings}>
            <Head title="Struktur Organisasi" />

            {/* Page Header */}
            <div className="relative bg-brand-navy py-16 md:py-32 text-center border-b-4 border-brand-teal overflow-hidden">
                <div className="absolute inset-0 z-0">
                    {settings?.org_hero_image_url ? (
                        <img 
                            src={`/storage/${settings.org_hero_image_url}`} 
                            alt="Background Struktur Organisasi" 
                            className="w-full h-full object-cover opacity-30"
                        />
                    ) : (
                        <img 
                            src="/image/logo_tova_matano.png" 
                            alt="Background Struktur Organisasi" 
                            className="w-full h-full object-cover opacity-10"
                        />
                    )}
                    <div className="absolute inset-0 bg-brand-navy/80 mix-blend-multiply"></div>
                </div>
                <div className="max-w-4xl mx-auto px-4 relative z-10">
                    <h1 className="text-4xl md:text-5xl font-heading font-bold text-brand-gold mb-6 drop-shadow-md">Struktur Organisasi</h1>
                    <p className="text-xl md:text-2xl text-gray-200 drop-shadow">
                        Mengenal lebih dekat para penggerak di balik roda perjuangan Tova Matano Care Center.
                    </p>
                </div>
            </div>

            {/* Bagan Organisasi (Visual Chart) */}
            {settings?.org_chart_image_url && (
                <div className="bg-white py-16 border-b border-gray-200">
                    <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="text-center mb-10">
                            <h2 className="text-3xl font-heading font-bold text-brand-navy mb-4">Bagan Organisasi</h2>
                            <div className="w-24 h-1 bg-brand-teal mx-auto"></div>
                        </div>
                        <div className="bg-brand-paper p-4 md:p-8 rounded-2xl border border-gray-100 shadow-inner flex justify-center">
                            <img 
                                src={`/storage/${settings.org_chart_image_url}`} 
                                alt="Bagan Organisasi TMCC" 
                                className="max-w-full h-auto rounded-xl shadow-sm hover:scale-[1.02] transition-transform duration-500 cursor-zoom-in"
                                onClick={() => window.open(`/storage/${settings.org_chart_image_url}`, '_blank')}
                            />
                        </div>
                    </div>
                </div>
            )}

            {/* Content Area */}
            <div className="py-16 md:py-24 bg-brand-paper">
                <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    
                    {categories && categories.length > 0 ? (
                        <div className="space-y-4">
                            {categories.map((category, index) => (
                                <Disclosure key={category.id} defaultOpen={index === 0}>
                                    {({ open }) => (
                                        <div className={`rounded-xl border ${open ? 'border-brand-teal bg-white shadow-md' : 'border-gray-200 bg-white/60 hover:bg-white hover:border-gray-300'}`}>
                                            <Disclosure.Button className="flex w-full justify-between items-center px-6 py-5 text-left focus:outline-none focus-visible:ring focus-visible:ring-brand-teal/50 rounded-xl">
                                                <div className="flex flex-col">
                                                    <span className={`text-xl font-heading font-bold ${open ? 'text-brand-teal' : 'text-brand-navy'}`}>
                                                        {category.name}
                                                    </span>
                                                    {category.description && (
                                                        <span className="text-sm text-gray-500 mt-1">{category.description}</span>
                                                    )}
                                                </div>
                                                <div className={`shrink-0 ml-4 p-2 rounded-full transition-colors ${open ? 'bg-brand-teal/10 text-brand-teal' : 'bg-gray-100 text-gray-500'}`}>
                                                    <ChevronDown
                                                        className={`h-6 w-6 transition-transform duration-300 ${open ? 'rotate-180 transform' : ''}`}
                                                    />
                                                </div>
                                            </Disclosure.Button>
                                            
                                            <Transition
                                                enter="transition duration-200 ease-out"
                                                enterFrom="transform scale-95 opacity-0 -translate-y-2"
                                                enterTo="transform scale-100 opacity-100 translate-y-0"
                                                leave="transition duration-100 ease-out"
                                                leaveFrom="transform scale-100 opacity-100"
                                                leaveTo="transform scale-95 opacity-0"
                                            >
                                                <Disclosure.Panel className="px-6 pb-6 pt-2">
                                                    {category.people && category.people.length > 0 ? (
                                                        <div className={category.display_type === 'PERSON_GRID' ? "grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 pt-4" : "flex flex-col gap-1 pt-2"}>
                                                            {category.people.map(person => renderPersonCard(person, category.display_type))}
                                                        </div>
                                                    ) : (
                                                        <div className="text-center py-8 text-gray-400 bg-gray-50 rounded-lg border border-dashed border-gray-200">
                                                            Belum ada data pengurus di kategori ini.
                                                        </div>
                                                    )}
                                                </Disclosure.Panel>
                                            </Transition>
                                        </div>
                                    )}
                                </Disclosure>
                            ))}
                        </div>
                    ) : (
                        <div className="text-center py-20 bg-white rounded-2xl border border-gray-200 shadow-sm">
                            <Users className="mx-auto h-16 w-16 text-gray-300 mb-4" />
                            <h3 className="text-xl font-bold text-gray-500">Struktur Belum Tersedia</h3>
                            <p className="text-gray-400 mt-2">Data struktur organisasi sedang dalam tahap penyusunan.</p>
                        </div>
                    )}

                </div>
            </div>
        </PublicLayout>
    );
}