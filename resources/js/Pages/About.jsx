import PublicLayout from '@/Layouts/PublicLayout';
import { Head, Link } from '@inertiajs/react';
import { ChevronRight } from 'lucide-react';

export default function About({ founders, settings }) {
    return (
        <PublicLayout settings={settings}>
            <Head title="Tentang TMCC" />

            {/* Page Header */}
            <div className="relative bg-brand-navy py-16 md:py-32 text-center border-b-4 border-brand-teal overflow-hidden">
                <div className="absolute inset-0 z-0">
                    <img 
                        src={settings?.about_hero_image_url ? `/storage/${settings.about_hero_image_url}` : "/image/logo_tova_matano.png"} 
                        alt="Background TMCC" 
                        className={`w-full h-full object-cover ${settings?.about_hero_image_url ? 'opacity-30' : 'opacity-10'}`}
                    />
                    <div className="absolute inset-0 bg-brand-navy/80 mix-blend-multiply"></div>
                </div>
                <div className="max-w-4xl mx-auto px-4 relative z-10">
                    <h1 className="text-4xl md:text-6xl font-heading font-bold text-brand-gold mb-6 drop-shadow-md">Tentang Kami</h1>
                    <p className="text-xl md:text-2xl text-gray-200 drop-shadow">
                        Membangun masa depan yang berkelanjutan untuk Danau Matano, satu tindakan pada satu waktu.
                    </p>
                </div>
            </div>

            {/* Latar Belakang Danau Matano */}
            <div className="py-16 md:py-24 bg-white">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                        <div className="order-2 lg:order-1 relative">
                            <div className="absolute inset-0 bg-brand-teal rounded-2xl transform -translate-x-4 translate-y-4"></div>
                            <img 
                                src="/image/danau-matano.jpg" 
                                alt="Danau Matano" 
                                onError={(e) => { e.target.src = '/image/logo_tova_matano.png'; e.target.className = 'relative rounded-2xl shadow-xl w-full h-auto object-contain bg-white p-8 aspect-[4/3] z-10' }}
                                className="relative rounded-2xl shadow-xl w-full h-auto object-cover aspect-[4/3] z-10"
                            />
                        </div>
                        <div className="order-1 lg:order-2">
                            <h2 className="text-3xl font-heading font-bold text-brand-navy mb-6">Latar Belakang Danau Matano</h2>
                            <div className="prose prose-lg text-gray-600 prose-a:text-brand-teal">
                                <p>
                                    Danau Matano merupakan danau tektonik purba terdalam di Indonesia dan Asia Tenggara, serta terdalam ke-8 di dunia. Danau ini terletak di Kabupaten Luwu Timur, Sulawesi Selatan, dengan kedalaman maksimal mencapai sekitar 590 meter.
                                </p>
                                <p>
                                    Sebagai "Danau Purba", Matano memiliki keanekaragaman hayati endemik yang luar biasa, termasuk ikan-ikan unik dan biota air tawar yang tidak ditemukan di tempat lain di dunia. Kualitas airnya yang jernih dan ekosistem yang rapuh membuatnya sangat penting untuk dilestarikan.
                                </p>
                                <p>
                                    Ancaman terhadap kelestarian Danau Matano terus meningkat seiring dengan aktivitas manusia, mulai dari pencemaran, limbah domestik, hingga dampak perubahan iklim. Oleh karena itu, langkah nyata untuk menjaga dan memulihkan kondisi danau ini menjadi sangat krusial.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Profil & Sejarah */}
            <div className="py-16 md:py-24 bg-brand-paper">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                        <div>
                            <h2 className="text-3xl font-heading font-bold text-brand-navy mb-6">Profil & Sejarah</h2>
                            <div className="prose prose-lg text-gray-600 prose-a:text-brand-teal">
                                <p>
                                    <strong>Tova Matano Care Center (TMCC)</strong> lahir dari kepedulian mendalam terhadap kelestarian Danau Matano, jantung kehidupan masyarakat Luwu Timur. Berawal dari gerakan komunitas kecil pada tahun 2018 yang rutin melakukan aksi bersih-bersih tepi danau, gerakan ini berevolusi menjadi lembaga resmi yang berkomitmen pada restorasi dan edukasi lingkungan jangka panjang.
                                </p>
                                <p>
                                    Kata "Tova" diambil dari bahasa lokal yang berarti rumah atau tempat bernaung. Kami percaya bahwa Danau Matano bukan sekadar genangan air, melainkan rumah bagi keanekaragaman hayati endemik dan sumber penghidupan yang harus dijaga untuk generasi mendatang.
                                </p>
                                <h3 className="font-heading text-xl text-brand-navy mt-8 mb-4">Legalitas</h3>
                                <ul className="list-disc pl-5 space-y-2">
                                    <li><strong>SK Kemenkumham:</strong> AHU-0012345.AH.01.07.TAHUN 2020</li>
                                    <li><strong>NPWP:</strong> 12.345.678.9-001.000</li>
                                    <li><strong>Tanda Daftar Organisasi Kemasyarakatan:</strong> Nomor 123/Kesbangpol/LT/2021</li>
                                </ul>
                            </div>
                        </div>
                        <div className="relative">
                            <div className="absolute inset-0 bg-brand-gold rounded-2xl transform translate-x-4 translate-y-4"></div>
                            <img 
                                src="/image/logo_tova_matano.png" 
                                alt="Logo Tova Matano Care Center" 
                                className="relative rounded-2xl shadow-xl w-full h-auto object-contain bg-white p-8 aspect-[4/3] z-10"
                            />
                        </div>
                    </div>
                </div>
            </div>

            {/* Visi, Misi, Tujuan */}
            <div className="py-16 md:py-24 bg-white">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="text-center mb-16">
                        <h2 className="text-3xl font-heading font-bold text-brand-navy mb-4">Visi, Misi & Tujuan</h2>
                        <div className="w-24 h-1 bg-brand-teal mx-auto"></div>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                        {/* Visi */}
                        <div className="bg-white p-8 rounded-xl shadow-sm border-t-4 border-brand-teal text-center">
                            <div className="w-16 h-16 bg-brand-teal/10 rounded-full flex items-center justify-center mx-auto mb-6">
                                <span className="text-2xl font-bold text-brand-teal">V</span>
                            </div>
                            <h3 className="text-xl font-bold text-brand-navy mb-4">Visi</h3>
                            <p className="text-gray-600">
                                Menjadi pusat unggulan dalam pelestarian ekosistem perairan darat tropis dan inspirasi global untuk penyelamatan danau-danau tektonik.
                            </p>
                        </div>

                        {/* Misi */}
                        <div className="bg-white p-8 rounded-xl shadow-sm border-t-4 border-brand-gold text-center transform md:-translate-y-4">
                            <div className="w-16 h-16 bg-brand-gold/10 rounded-full flex items-center justify-center mx-auto mb-6">
                                <span className="text-2xl font-bold text-brand-gold">M</span>
                            </div>
                            <h3 className="text-xl font-bold text-brand-navy mb-4">Misi</h3>
                            <ul className="text-gray-600 text-left space-y-3">
                                <li className="flex items-start">
                                    <ChevronRight className="h-5 w-5 text-brand-gold shrink-0 mt-0.5" />
                                    <span>Melakukan penelitian dan pemantauan kualitas air secara berkala.</span>
                                </li>
                                <li className="flex items-start">
                                    <ChevronRight className="h-5 w-5 text-brand-gold shrink-0 mt-0.5" />
                                    <span>Mengedukasi masyarakat pesisir tentang pentingnya sanitasi dan pengelolaan limbah.</span>
                                </li>
                                <li className="flex items-start">
                                    <ChevronRight className="h-5 w-5 text-brand-gold shrink-0 mt-0.5" />
                                    <span>Mengembangkan ekowisata berkelanjutan yang memberdayakan ekonomi lokal.</span>
                                </li>
                            </ul>
                        </div>

                        {/* Tujuan */}
                        <div className="bg-white p-8 rounded-xl shadow-sm border-t-4 border-brand-navy text-center">
                            <div className="w-16 h-16 bg-brand-navy/10 rounded-full flex items-center justify-center mx-auto mb-6">
                                <span className="text-2xl font-bold text-brand-navy">T</span>
                            </div>
                            <h3 className="text-xl font-bold text-brand-navy mb-4">Tujuan</h3>
                            <p className="text-gray-600">
                                Mewujudkan Danau Matano yang bersih, sehat, dan lestari selambatnya pada tahun 2035 melalui kolaborasi pentahelix (Pemerintah, Akademisi, Komunitas, Bisnis, Media).
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {/* Founders Section */}
            <div className="py-16 md:py-24 bg-brand-paper">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="text-center mb-16">
                        <h2 className="text-3xl font-heading font-bold text-brand-navy mb-4">Dewan Pendiri</h2>
                        <p className="text-gray-600 max-w-2xl mx-auto">Merekalah yang meletakkan batu pertama perjuangan pelestarian Danau Matano.</p>
                    </div>

                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                        {founders && founders.length > 0 ? (
                            founders.map(founder => (
                                <div key={founder.id} className="group">
                                    <div className="relative overflow-hidden rounded-xl aspect-[3/4] mb-4 bg-gray-100">
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
                                        <div className="absolute inset-0 bg-gradient-to-t from-brand-navy/90 via-brand-navy/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-6">
                                            <p className="text-white text-sm font-medium">Berdedikasi sejak awal mula TMCC berdiri.</p>
                                        </div>
                                    </div>
                                    <h3 className="text-xl font-bold text-brand-navy">{founder.name}</h3>
                                    <p className="text-brand-teal font-medium mb-1">{founder.role}</p>
                                    {founder.org_name && (
                                        <p className="text-sm text-gray-500">{founder.org_name}</p>
                                    )}
                                </div>
                            ))
                        ) : (
                            <div className="col-span-full text-center py-12 text-gray-500 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                                Data pendiri belum ditambahkan.
                            </div>
                        )}
                    </div>
                </div>
            </div>

            {/* CTA Section */}
            <div className="bg-brand-navy py-16 relative overflow-hidden">
                <div className="absolute top-0 left-0 w-full h-1 bg-brand-gold"></div>
                <div className="max-w-4xl mx-auto px-4 text-center relative z-10">
                    <h2 className="text-3xl font-heading font-bold text-white mb-6">Mari Berkarya Bersama Kami</h2>
                    <p className="text-gray-300 mb-8 text-lg">
                        Pelestarian lingkungan bukanlah tugas satu orang atau satu kelompok. Ini adalah tanggung jawab kita bersama. 
                        Jadilah bagian dari solusi nyata.
                    </p>
                    <div className="flex flex-col sm:flex-row justify-center gap-4">
                        <Link href="/gabung-donasi" className="px-8 py-3 bg-brand-gold text-brand-navy font-bold rounded hover:bg-amber-400 transition-colors">
                            Ayo Bergabung
                        </Link>
                        <Link href="/kontak" className="px-8 py-3 border border-gray-400 text-white font-bold rounded hover:border-white hover:bg-white/10 transition-colors">
                            Hubungi Kami
                        </Link>
                    </div>
                </div>
            </div>

        </PublicLayout>
    );
}