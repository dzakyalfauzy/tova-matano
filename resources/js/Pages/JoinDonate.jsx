import PublicLayout from '@/Layouts/PublicLayout';
import { Head, useForm } from '@inertiajs/react';
import { useState } from 'react';
import { CheckCircle, CreditCard, User, Mail, Phone, MapPin, FileText } from 'lucide-react';

export default function JoinDonate({ donationChannels, settings }) {
    const [registrationCode, setRegistrationCode] = useState(null);

    const { data, setData, post, processing, errors, reset } = useForm({
        full_name: '',
        email: '',
        phone: '',
        address: '',
        motivation: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/gabung-donasi/daftar', {
            onSuccess: (page) => {
                setRegistrationCode(page.props.flash?.registration_code);
                reset();
            },
        });
    };

    return (
        <PublicLayout settings={settings}>
            <Head title="Gabung & Donasi" />

            {/* Header */}
            <div className="bg-brand-navy py-16 md:py-24 text-center border-b-4 border-brand-teal">
                <div className="max-w-4xl mx-auto px-4">
                    <h1 className="text-4xl md:text-5xl font-heading font-bold text-brand-gold mb-4">Gabung & Donasi</h1>
                    <p className="text-xl text-gray-200">Jadilah bagian dari gerakan nyata pelestarian Danau Matano.</p>
                </div>
            </div>

            <div className="bg-brand-paper py-12">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-12">

                        {/* Volunteer Registration Form */}
                        <div>
                            <div className="flex items-center gap-3 mb-6">
                                <div className="w-10 h-10 rounded-full bg-brand-teal/10 flex items-center justify-center">
                                    <User className="h-5 w-5 text-brand-teal" />
                                </div>
                                <h2 className="text-2xl font-heading font-bold text-brand-navy">Daftar Relawan</h2>
                            </div>

                            {/* Success State */}
                            {registrationCode ? (
                                <div className="bg-green-50 border border-green-200 rounded-2xl p-8 text-center">
                                    <CheckCircle className="h-16 w-16 text-green-500 mx-auto mb-4" />
                                    <h3 className="text-xl font-bold text-green-800 mb-2">Pendaftaran Berhasil!</h3>
                                    <p className="text-green-700 mb-4">Kode pendaftaran Anda adalah:</p>
                                    <div className="bg-white border-2 border-green-300 rounded-xl p-4 mb-4">
                                        <p className="text-3xl font-mono font-bold text-green-700 tracking-widest">{registrationCode}</p>
                                    </div>
                                    <p className="text-sm text-green-600">
                                        Simpan kode ini. Anda akan menerima email konfirmasi segera. Tim kami akan memproses pendaftaran Anda.
                                    </p>
                                    <button
                                        onClick={() => setRegistrationCode(null)}
                                        className="mt-6 px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm"
                                    >
                                        Daftar Lagi
                                    </button>
                                </div>
                            ) : (
                                <form onSubmit={handleSubmit} className="bg-white rounded-2xl shadow-sm p-8 space-y-5">
                                    {/* Full Name */}
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1.5">
                                            <User className="inline h-4 w-4 mr-1.5 text-brand-teal" />
                                            Nama Lengkap <span className="text-red-500">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            value={data.full_name}
                                            onChange={e => setData('full_name', e.target.value)}
                                            className="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-teal/30 focus:border-brand-teal outline-none transition"
                                            placeholder="Nama lengkap sesuai KTP"
                                        />
                                        {errors.full_name && <p className="text-red-500 text-xs mt-1">{errors.full_name}</p>}
                                    </div>

                                    {/* Email */}
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1.5">
                                            <Mail className="inline h-4 w-4 mr-1.5 text-brand-teal" />
                                            Email <span className="text-red-500">*</span>
                                        </label>
                                        <input
                                            type="email"
                                            value={data.email}
                                            onChange={e => setData('email', e.target.value)}
                                            className="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-teal/30 focus:border-brand-teal outline-none transition"
                                            placeholder="email@contoh.com"
                                        />
                                        {errors.email && <p className="text-red-500 text-xs mt-1">{errors.email}</p>}
                                    </div>

                                    {/* Phone */}
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1.5">
                                            <Phone className="inline h-4 w-4 mr-1.5 text-brand-teal" />
                                            No. Telepon <span className="text-red-500">*</span>
                                        </label>
                                        <input
                                            type="tel"
                                            value={data.phone}
                                            onChange={e => setData('phone', e.target.value)}
                                            className="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-teal/30 focus:border-brand-teal outline-none transition"
                                            placeholder="08xxxxxxxxxx"
                                        />
                                        {errors.phone && <p className="text-red-500 text-xs mt-1">{errors.phone}</p>}
                                    </div>

                                    {/* Address */}
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1.5">
                                            <MapPin className="inline h-4 w-4 mr-1.5 text-brand-teal" />
                                            Alamat
                                        </label>
                                        <input
                                            type="text"
                                            value={data.address}
                                            onChange={e => setData('address', e.target.value)}
                                            className="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-teal/30 focus:border-brand-teal outline-none transition"
                                            placeholder="Kota, Provinsi"
                                        />
                                    </div>

                                    {/* Motivation */}
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1.5">
                                            <FileText className="inline h-4 w-4 mr-1.5 text-brand-teal" />
                                            Motivasi Bergabung
                                        </label>
                                        <textarea
                                            value={data.motivation}
                                            onChange={e => setData('motivation', e.target.value)}
                                            rows={4}
                                            className="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-teal/30 focus:border-brand-teal outline-none transition resize-none"
                                            placeholder="Ceritakan motivasi Anda bergabung dengan TMCC..."
                                        />
                                    </div>

                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="w-full py-3 bg-brand-gold text-brand-navy font-bold rounded-xl hover:bg-amber-400 transition-colors disabled:opacity-60"
                                    >
                                        {processing ? 'Mendaftar...' : 'Kirim Pendaftaran'}
                                    </button>
                                </form>
                            )}
                        </div>

                        {/* Donation Info */}
                        <div>
                            <div className="flex items-center gap-3 mb-6">
                                <div className="w-10 h-10 rounded-full bg-brand-gold/10 flex items-center justify-center">
                                    <CreditCard className="h-5 w-5 text-brand-gold" />
                                </div>
                                <h2 className="text-2xl font-heading font-bold text-brand-navy">Info Donasi</h2>
                            </div>

                            <div className="bg-white rounded-2xl shadow-sm p-8 mb-6">
                                <p className="text-gray-600 mb-6 leading-relaxed">
                                    Donasi Anda akan digunakan untuk mendukung program-program pelestarian Danau Matano, termasuk pembelian alat uji kualitas air, kegiatan edukasi masyarakat, dan aksi bersih-bersih berkala.
                                </p>

                                {donationChannels && donationChannels.length > 0 ? (
                                    <div className="space-y-4">
                                        {donationChannels.map(channel => (
                                            <div key={channel.id} className="border border-gray-100 rounded-xl p-5 hover:border-brand-gold/50 transition-colors">
                                                <div className="flex items-center justify-between mb-3">
                                                    <span className="font-bold text-brand-navy text-lg">{channel.bank_name}</span>
                                                    <span className="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">Aktif</span>
                                                </div>
                                                <div className="bg-gray-50 rounded-lg p-3">
                                                    <p className="text-xs text-gray-500 mb-1">Nomor Rekening</p>
                                                    <p className="text-xl font-mono font-bold text-brand-navy tracking-wider">{channel.account_no}</p>
                                                </div>
                                                <p className="text-sm text-gray-600 mt-3">
                                                    a.n. <strong>{channel.account_name}</strong>
                                                </p>
                                            </div>
                                        ))}
                                    </div>
                                ) : (
                                    <div className="text-center py-8 text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                                        Info rekening donasi sedang diperbarui.
                                    </div>
                                )}
                            </div>

                            <div className="bg-brand-navy/5 border border-brand-navy/10 rounded-xl p-5">
                                <p className="text-sm text-gray-600">
                                    <strong className="text-brand-navy">Catatan:</strong> Setelah transfer, harap konfirmasi via WhatsApp ke{' '}
                                    {settings?.whatsapp ? (
                                        <a href={`https://wa.me/${settings.whatsapp}`} className="text-brand-teal font-medium hover:underline">
                                            {settings.whatsapp}
                                        </a>
                                    ) : 'nomor WhatsApp kami'} dengan menyertakan bukti transfer dan nama pengirim.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}
