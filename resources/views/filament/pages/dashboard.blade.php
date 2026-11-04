<x-filament-panels::page>
    <div class="tmcc-dash">
        <section class="tmcc-welcome">
            <div class="tmcc-welcome-copy">
                <span class="tmcc-eyebrow">Pusat kendali</span>
                <h2>Selamat datang, {{ auth()->user()?->name ?? 'Admin' }}</h2>
                <p>Kelola konten, relawan, dan pemantauan kualitas air Danau Matano dari satu tempat.</p>
            </div>

            <div class="tmcc-welcome-side">
                <span class="tmcc-date">
                    <x-filament::icon :icon="\Filament\Support\Icons\Heroicon::OutlinedCalendarDays" />
                    {{ now()->translatedFormat('l, j F Y') }}
                </span>

                @if (isset($quickActions[0]))
                    <a href="{{ $quickActions[0]['url'] }}" class="tmcc-button tmcc-button--primary">
                        <x-filament::icon :icon="$quickActions[0]['icon']" />
                        {{ $quickActions[0]['label'] }}
                    </a>
                @endif
            </div>
        </section>

        <section class="tmcc-kpi-grid" aria-label="Ringkasan data">
            @foreach ($stats as $stat)
                @if (isset($stat['url']))
                    <a href="{{ $stat['url'] }}" class="tmcc-kpi {{ isset($stat['tone']) ? 'tmcc-kpi--'.$stat['tone'] : '' }}">
                @else
                    <div class="tmcc-kpi {{ isset($stat['tone']) ? 'tmcc-kpi--'.$stat['tone'] : '' }}">
                @endif
                        <span class="tmcc-kpi-icon">
                            <x-filament::icon :icon="$stat['icon']" />
                        </span>
                        <span class="tmcc-kpi-copy">
                            <span class="tmcc-kpi-label">{{ $stat['label'] }}</span>
                            <strong class="tmcc-kpi-value">{{ number_format($stat['value'], 0, ',', '.') }}</strong>
                            <span class="tmcc-kpi-hint">{{ $stat['hint'] }}</span>
                        </span>
                @if (isset($stat['url']))
                    </a>
                @else
                    </div>
                @endif
            @endforeach
        </section>

        <div class="tmcc-columns">
            <div class="tmcc-column-main">
                <section class="tmcc-card">
                    <header class="tmcc-card-header">
                        <div>
                            <h3>Relawan Terbaru</h3>
                            <p>Pendaftaran yang masuk paling akhir.</p>
                        </div>
                        <a href="{{ \App\Filament\Resources\Volunteers\VolunteerResource::getUrl('index') }}" class="tmcc-link">Lihat semua</a>
                    </header>

                    <div class="tmcc-list">
                        @forelse ($latestVolunteers as $volunteer)
                            <div class="tmcc-list-row">
                                <span class="tmcc-avatar">{{ strtoupper(substr($volunteer->full_name ?? 'R', 0, 1)) }}</span>
                                <span class="tmcc-list-copy">
                                    <strong>{{ $volunteer->full_name }}</strong>
                                    <small>{{ $volunteer->email }}</small>
                                </span>
                                <span class="tmcc-list-meta">
                                    <span class="tmcc-badge tmcc-badge--{{ match ($volunteer->status) { 'APPROVED' => 'success', 'REJECTED' => 'danger', default => 'warning' } }}">
                                        {{ match ($volunteer->status) { 'APPROVED' => 'Disetujui', 'REJECTED' => 'Ditolak', default => 'Menunggu' } }}
                                    </span>
                                    <small>{{ $volunteer->submitted_at?->translatedFormat('d M Y') ?? '—' }}</small>
                                </span>
                            </div>
                        @empty
                            <p class="tmcc-empty">Belum ada pendaftaran relawan.</p>
                        @endforelse
                    </div>
                </section>

                <section class="tmcc-card">
                    <header class="tmcc-card-header">
                        <div>
                            <h3>Artikel Terbaru</h3>
                            <p>Publikasi blog dan rilis pers.</p>
                        </div>
                        <a href="{{ \App\Filament\Resources\BlogPosts\BlogPostResource::getUrl('index') }}" class="tmcc-link">Lihat semua</a>
                    </header>

                    <div class="tmcc-list">
                        @forelse ($latestBlogPosts as $post)
                            <div class="tmcc-list-row">
                                <span class="tmcc-avatar tmcc-avatar--paper">
                                    <x-filament::icon :icon="\Filament\Support\Icons\Heroicon::OutlinedNewspaper" />
                                </span>
                                <span class="tmcc-list-copy">
                                    <strong>{{ $post->title }}</strong>
                                    <small>{{ $post->category ? str_replace('_', ' ', $post->category) : 'Tanpa kategori' }}</small>
                                </span>
                                <span class="tmcc-list-meta">
                                    <span class="tmcc-badge tmcc-badge--{{ $post->is_published ? 'success' : 'muted' }}">
                                        {{ $post->is_published ? 'Terbit' : 'Draf' }}
                                    </span>
                                    <small>{{ $post->published_at?->translatedFormat('d M Y') ?? '—' }}</small>
                                </span>
                            </div>
                        @empty
                            <p class="tmcc-empty">Belum ada artikel.</p>
                        @endforelse
                    </div>
                </section>

                <section class="tmcc-card">
                    <header class="tmcc-card-header">
                        <div>
                            <h3>Pemantauan Kualitas Air Terbaru</h3>
                            <p>Lima pengukuran terakhir dari seluruh titik sampling.</p>
                        </div>
                        <a href="{{ \App\Filament\Resources\WaterQualityRecords\WaterQualityRecordResource::getUrl('index') }}" class="tmcc-link">Lihat semua</a>
                    </header>

                    <div class="tmcc-table-wrap">
                        <table class="tmcc-table">
                            <thead>
                                <tr>
                                    <th>Waktu</th>
                                    <th>Titik sampling</th>
                                    <th>pH</th>
                                    <th>DO (mg/L)</th>
                                    <th>Kejernihan (cm)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($latestWaterQuality as $record)
                                    <tr>
                                        <td>{{ $record->recorded_at?->translatedFormat('d M Y H:i') ?? '—' }}</td>
                                        <td>{{ $record->samplingPoint?->name ?? '—' }}</td>
                                        <td>{{ $record->ph !== null ? number_format($record->ph, 2, ',', '.') : '—' }}</td>
                                        <td>{{ $record->dissolved_oxygen !== null ? number_format($record->dissolved_oxygen, 2, ',', '.') : '—' }}</td>
                                        <td>{{ $record->clarity_cm !== null ? number_format($record->clarity_cm, 0, ',', '.') : '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="tmcc-empty">Belum ada data kualitas air.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>

            <aside class="tmcc-column-side">
                <section class="tmcc-card">
                    <header class="tmcc-card-header">
                        <div>
                            <h3>Aksi Cepat</h3>
                            <p>Mulai pekerjaan yang sering dilakukan.</p>
                        </div>
                    </header>

                    <div class="tmcc-actions">
                        @foreach ($quickActions as $action)
                            <a href="{{ $action['url'] }}" class="tmcc-action">
                                <span class="tmcc-action-icon">
                                    <x-filament::icon :icon="$action['icon']" />
                                </span>
                                <span>
                                    <strong>{{ $action['label'] }}</strong>
                                    <small>{{ $action['description'] }}</small>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </section>

                <section class="tmcc-card">
                    <header class="tmcc-card-header">
                        <div>
                            <h3>Perlu Perhatian</h3>
                            <p>Item yang menunggu tindakan admin.</p>
                        </div>
                    </header>

                    <div class="tmcc-attention">
                        @forelse ($attentionItems as $item)
                            <a href="{{ $item['url'] }}" class="tmcc-attention-item">
                                <span class="tmcc-dot tmcc-dot--{{ $item['tone'] }}"></span>
                                <span>{{ $item['label'] }}</span>
                                <x-filament::icon :icon="\Filament\Support\Icons\Heroicon::OutlinedArrowRight" />
                            </a>
                        @empty
                            <p class="tmcc-empty">Tidak ada item yang perlu ditindaklanjuti.</p>
                        @endforelse
                    </div>
                </section>

                <p class="tmcc-dash-note">
                    Angka pada dasbor dihitung langsung dari database dan diperbarui setiap halaman dimuat.
                </p>
            </aside>
        </div>
    </div>
</x-filament-panels::page>
