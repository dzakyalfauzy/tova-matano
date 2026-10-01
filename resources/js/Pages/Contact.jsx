import PublicLayout from '@/Layouts/PublicLayout';
import { Head, useForm } from '@inertiajs/react';
import { MapPin, Phone, Mail, Send, CheckCircle } from 'lucide-react';
import { useState } from 'react';

export default function Contact({ settings }) {
    const [sent, setSent] = useState(false);

    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        email: '',
        message: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/kontak/kirim', {
            onSuccess: () => {
                setSent(true);
                reset();
            },
        });
    };

    return (
        <PublicLayout settings={settings}>
            <Head title="Kontak TMCC" />

            {/* Header */}
            <div className="bg-brand-navy py-16 md:py-24 text-center border-b-4 border-brand-teal">
                <div className="max-w-4xl mx-auto px-4">
                    <h1 className="text-4xl md:text-5xl font-heading font-bold text-brand-gold mb-4">Hubungi Kami</h1>
                    <p className="text-xl text-gray-200">Ada pertanyaan atau ingin berkolaborasi? Kami siap mendengar.</p>
                </div>
            </div>

            <div className="bg-brand-paper py-12">
                <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="grid grid-cols-1 lg:grid-cols-5 gap-12">

                        {/* Contact Info */}
                        <div className="lg:col-span-2 space-y-6">
                            <div>
                                <h2 className="text-2xl font-heading font-bold text-brand-navy mb-6">Informasi Kontak</h2>
                                <div className="space-y-5">
                                    {settings?.address && (
                                        <div className="flex items-start gap-4">
                                            <div className="w-10 h-10 rounded-full bg-brand-teal/10 flex items-center justify-center shrink-0 mt-0.5">
                                                <MapPin className="h-5 w-5 text-brand-teal" />
                                            </div>
                                            <div>
                                                <p className="text-xs font-bold text-brand-teal uppercase tracking-wider mb-1">Alamat</p>
                                                <p className="text-gray-700">{settings.address}</p>
                                            </div>
                                        </div>
                                    )}
                                    {(settings?.phone || settings?.whatsapp) && (
                                        <div className="flex items-start gap-4">
                                            <div className="w-10 h-10 rounded-full bg-brand-teal/10 flex items-center justify-center shrink-0 mt-0.5">
                                                <Phone className="h-5 w-5 text-brand-teal" />
                                            </div>
                                            <div>
                                                <p className="text-xs font-bold text-brand-teal uppercase tracking-wider mb-1">Telepon / WhatsApp</p>
                                                {settings.phone && <p className="text-gray-700">{settings.phone}</p>}
                                                {settings.whatsapp && (
                                                    <a href={`https://wa.me/${settings.whatsapp}`} className="text-brand-teal hover:underline">
                                                        WA: {settings.whatsapp}
                                                    </a>
                                                )}
                                            </div>
                                        </div>
                                    )}
                                    {settings?.email && (
                                        <div className="flex items-start gap-4">
                                            <div className="w-10 h-10 rounded-full bg-brand-teal/10 flex items-center justify-center shrink-0 mt-0.5">
                                                <Mail className="h-5 w-5 text-brand-teal" />
                                            </div>
                                            <div>
                                                <p className="text-xs font-bold text-brand-teal uppercase tracking-wider mb-1">Email</p>
                                                <a href={`mailto:${settings.email}`} className="text-gray-700 hover:text-brand-teal transition-colors">
                                                    {settings.email}
                                                </a>
                                            </div>
                                        </div>
                                    )}
                                </div>
                            </div>

                            {/* Social Media */}
                            <div className="pt-6 border-t border-gray-200">
                                <p className="text-xs font-bold text-brand-teal uppercase tracking-wider mb-4">Media Sosial</p>
                                <div className="flex flex-col gap-2">
                                    {settings?.instagram_url && (
                                        <a href={settings.instagram_url} target="_blank" rel="noopener noreferrer" className="text-gray-600 hover:text-brand-teal transition-colors flex items-center gap-2">
                                            <span className="font-bold text-xs w-6">IG</span> Instagram
                                        </a>
                                    )}
                                    {settings?.facebook_url && (
                                        <a href={settings.facebook_url} target="_blank" rel="noopener noreferrer" className="text-gray-600 hover:text-brand-teal transition-colors flex items-center gap-2">
                                            <span className="font-bold text-xs w-6">FB</span> Facebook
                                        </a>
                                    )}
                                    {settings?.tiktok_url && (
                                        <a href={settings.tiktok_url} target="_blank" rel="noopener noreferrer" className="text-gray-600 hover:text-brand-teal transition-colors flex items-center gap-2">
                                            <span className="font-bold text-xs w-6">TT</span> TikTok
                                        </a>
                                    )}
                                    {settings?.twitter_url && (
                                        <a href={settings.twitter_url} target="_blank" rel="noopener noreferrer" className="text-gray-600 hover:text-brand-teal transition-colors flex items-center gap-2">
                                            <span className="font-bold text-xs w-6">X</span> Twitter / X
                                        </a>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* Contact Form */}
                        <div className="lg:col-span-3">
                            <h2 className="text-2xl font-heading font-bold text-brand-navy mb-6">Kirim Pesan</h2>

                            {sent ? (
                                <div className="bg-green-50 border border-green-200 rounded-2xl p-10 text-center">
                                    <CheckCircle className="h-14 w-14 text-green-500 mx-auto mb-4" />
                                    <h3 className="text-xl font-bold text-green-800 mb-2">Pesan Terkirim!</h3>
                                    <p className="text-green-700">Terima kasih. Tim kami akan membalas pesan Anda secepatnya.</p>
                                    <button
                                        onClick={() => setSent(false)}
                                        className="mt-6 px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm"
                                    >
                                        Kirim Pesan Lagi
                                    </button>
                                </div>
                            ) : (
                                <form onSubmit={handleSubmit} className="bg-white rounded-2xl shadow-sm p-8 space-y-5">
                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 mb-1.5">
                                                Nama <span className="text-red-500">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                value={data.name}
                                                onChange={e => setData('name', e.target.value)}
                                                className="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-teal/30 focus:border-brand-teal outline-none transition"
                                                placeholder="Nama Anda"
                                            />
                                            {errors.name && <p className="text-red-500 text-xs mt-1">{errors.name}</p>}
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 mb-1.5">
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
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1.5">
                                            Pesan <span className="text-red-500">*</span>
                                        </label>
                                        <textarea
                                            value={data.message}
                                            onChange={e => setData('message', e.target.value)}
                                            rows={6}
                                            className="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-teal/30 focus:border-brand-teal outline-none transition resize-none"
                                            placeholder="Tuliskan pesan, pertanyaan, atau ide kolaborasi Anda..."
                                        />
                                        {errors.message && <p className="text-red-500 text-xs mt-1">{errors.message}</p>}
                                    </div>
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="w-full py-3 bg-brand-teal text-white font-bold rounded-xl hover:bg-brand-navy transition-colors disabled:opacity-60 flex items-center justify-center gap-2"
                                    >
                                        <Send className="h-4 w-4" />
                                        {processing ? 'Mengirim...' : 'Kirim Pesan'}
                                    </button>
                                </form>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </PublicLayout>
    );
}
