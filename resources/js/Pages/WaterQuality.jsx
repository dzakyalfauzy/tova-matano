import PublicLayout from '@/Layouts/PublicLayout';
import { Head } from '@inertiajs/react';
import { useState, useMemo } from 'react';
import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer } from 'recharts';

export default function WaterQuality({ records, samplingPoints, settings }) {
    const [selectedPoint, setSelectedPoint] = useState('all');
    const [selectedTimeRange, setSelectedTimeRange] = useState('all');

    // Filter data based on selections
    const filteredRecords = useMemo(() => {
        let filtered = [...records];
        
        // Filter by sampling point
        if (selectedPoint !== 'all') {
            filtered = filtered.filter(r => r.water_sampling_point_id === parseInt(selectedPoint));
        }

        // Filter by time range
        if (selectedTimeRange !== 'all') {
            const now = new Date();
            const pastDate = new Date();
            
            if (selectedTimeRange === 'week') {
                pastDate.setDate(now.getDate() - 7);
            } else if (selectedTimeRange === 'month') {
                pastDate.setMonth(now.getMonth() - 1);
            } else if (selectedTimeRange === 'year') {
                pastDate.setFullYear(now.getFullYear() - 1);
            }
            
            filtered = filtered.filter(r => new Date(r.recorded_at) >= pastDate);
        }
        
        return filtered.sort((a, b) => new Date(a.recorded_at) - new Date(b.recorded_at));
    }, [records, selectedPoint, selectedTimeRange]);

    // Calculate Averages
    const averages = useMemo(() => {
        if (filteredRecords.length === 0) return { ph: null, clarity: null };
        
        const sumPh = filteredRecords.reduce((sum, r) => sum + r.ph, 0);
        
        const clarityRecords = filteredRecords.filter(r => r.clarity_cm !== null);
        const sumClarity = clarityRecords.reduce((sum, r) => sum + r.clarity_cm, 0);
        
        return {
            ph: (sumPh / filteredRecords.length).toFixed(2),
            clarity: clarityRecords.length > 0 ? (sumClarity / clarityRecords.length).toFixed(1) : null
        };
    }, [filteredRecords]);

    const chartData = filteredRecords.map(r => ({
        date: new Date(r.recorded_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }),
        pH: r.ph,
        'Kejernihan (cm)': r.clarity_cm,
    }));

    return (
        <PublicLayout settings={settings}>
            <Head title="Kualitas Air Danau Matano | TMCC" />

            <div className="bg-brand-navy py-16 md:py-24 text-center border-b-4 border-brand-teal">
                <div className="max-w-4xl mx-auto px-4">
                    <h1 className="text-4xl md:text-5xl font-heading font-bold text-brand-gold mb-4">Kualitas Air Danau Matano</h1>
                    <p className="text-xl text-gray-200">Data pemantauan berkala oleh Tova Water Patrol</p>
                </div>
            </div>

            <div className="bg-brand-paper py-12">
                <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

                    {/* Filters */}
                    <div className="bg-white rounded-2xl shadow-sm p-6 flex flex-col md:flex-row gap-4 items-center justify-between">
                        <div className="w-full md:w-auto flex flex-col sm:flex-row gap-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Titik Pengambilan Sample</label>
                                <select 
                                    value={selectedPoint}
                                    onChange={(e) => setSelectedPoint(e.target.value)}
                                    className="border-gray-300 rounded-lg shadow-sm focus:border-brand-teal focus:ring-brand-teal w-full min-w-[200px]"
                                >
                                    <option value="all">Semua Titik Sample</option>
                                    {samplingPoints && samplingPoints.map(point => (
                                        <option key={point.id} value={point.id}>{point.name}</option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Rentang Waktu</label>
                                <select 
                                    value={selectedTimeRange}
                                    onChange={(e) => setSelectedTimeRange(e.target.value)}
                                    className="border-gray-300 rounded-lg shadow-sm focus:border-brand-teal focus:ring-brand-teal w-full min-w-[150px]"
                                >
                                    <option value="all">Semua Waktu</option>
                                    <option value="week">1 Minggu Terakhir</option>
                                    <option value="month">1 Bulan Terakhir</option>
                                    <option value="year">1 Tahun Terakhir</option>
                                </select>
                            </div>
                        </div>
                        <div className="text-sm text-gray-500 font-medium">
                            Menampilkan {filteredRecords.length} data
                        </div>
                    </div>

                    {/* Summary Cards */}
                    {filteredRecords.length > 0 && (
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div className="bg-white rounded-2xl shadow-sm p-6 text-center border-t-4 border-brand-teal">
                                <p className="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">Rata-Rata pH</p>
                                <p className="text-5xl font-heading font-bold text-brand-navy">{averages.ph}</p>
                            </div>
                            <div className="bg-white rounded-2xl shadow-sm p-6 text-center border-t-4 border-brand-gold">
                                <p className="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">Rata-Rata Kejernihan</p>
                                <p className="text-5xl font-heading font-bold text-brand-navy">{averages.clarity ? (averages.clarity / 100).toFixed(2) : '-'} <span className="text-2xl text-gray-400">m</span></p>
                            </div>
                        </div>
                    )}

                    {/* Chart */}
                    {chartData.length > 0 ? (
                        <div className="bg-white rounded-2xl shadow-sm p-6 md:p-8">
                            <h2 className="text-xl font-heading font-bold text-brand-navy mb-6">Grafik pH & Kejernihan</h2>
                            <ResponsiveContainer width="100%" height={400}>
                                <LineChart data={chartData}>
                                    <CartesianGrid strokeDasharray="3 3" stroke="#e5e7eb" />
                                    <XAxis dataKey="date" tick={{ fontSize: 12 }} />
                                    <YAxis yAxisId="ph" domain={[6, 8]} tick={{ fontSize: 12 }} />
                                    <YAxis yAxisId="clarity" orientation="right" domain={[0, 500]} tick={{ fontSize: 12 }} />
                                    <Tooltip
                                        contentStyle={{ borderRadius: '12px', border: 'none', boxShadow: '0 4px 12px rgba(0,0,0,0.1)' }}
                                    />
                                    <Legend />
                                    <Line yAxisId="ph" type="monotone" dataKey="pH" stroke="#1E7F8C" strokeWidth={2} dot={{ r: 4 }} activeDot={{ r: 6 }} />
                                    <Line yAxisId="clarity" type="monotone" dataKey="Kejernihan (cm)" stroke="#E3A23A" strokeWidth={2} dot={{ r: 4 }} activeDot={{ r: 6 }} />
                                </LineChart>
                            </ResponsiveContainer>
                        </div>
                    ) : (
                        <div className="bg-white rounded-2xl shadow-sm p-10 text-center">
                            <p className="text-gray-500 text-lg">Belum ada data kualitas air yang tersedia untuk filter yang dipilih.</p>
                        </div>
                    )}

                    {/* Data Table */}
                    {filteredRecords.length > 0 && (
                        <div className="bg-white rounded-2xl shadow-sm overflow-hidden">
                            <div className="px-6 py-4 border-b border-gray-100">
                                <h2 className="text-xl font-heading font-bold text-brand-navy">Riwayat Pengukuran</h2>
                            </div>
                            <div className="overflow-x-auto">
                                <table className="w-full text-sm">
                                    <thead className="bg-gray-50 text-left">
                                        <tr>
                                            <th className="px-6 py-3 font-semibold text-gray-600">Tanggal</th>
                                            <th className="px-6 py-3 font-semibold text-gray-600">Titik Sample</th>
                                            <th className="px-6 py-3 font-semibold text-gray-600">pH</th>
                                            <th className="px-6 py-3 font-semibold text-gray-600">Kejernihan</th>
                                            <th className="px-6 py-3 font-semibold text-gray-600">Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-gray-100">
                                        {filteredRecords.slice().reverse().map((r) => (
                                            <tr key={r.id} className="hover:bg-gray-50 transition-colors">
                                                <td className="px-6 py-3 text-gray-700 whitespace-nowrap">
                                                    {new Date(r.recorded_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}
                                                </td>
                                                <td className="px-6 py-3 text-gray-700 whitespace-nowrap">
                                                    {r.sampling_point ? r.sampling_point.name : '-'}
                                                </td>
                                                <td className="px-6 py-3">
                                                    <span className={`inline-block px-2.5 py-0.5 rounded-full text-xs font-bold ${
                                                        r.ph >= 7 && r.ph <= 7.5 ? 'bg-green-100 text-green-700' :
                                                        r.ph >= 6.5 ? 'bg-yellow-100 text-yellow-700' :
                                                        'bg-red-100 text-red-700'
                                                    }`}>
                                                        {r.ph}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-3 text-gray-700 whitespace-nowrap">
                                                    {r.clarity_cm ? `${(r.clarity_cm / 100).toFixed(1)} m` : '-'}
                                                </td>
                                                <td className="px-6 py-3 text-gray-500 max-w-xs truncate">
                                                    {r.notes || '-'}
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    )}

                </div>
            </div>
        </PublicLayout>
    );
}
