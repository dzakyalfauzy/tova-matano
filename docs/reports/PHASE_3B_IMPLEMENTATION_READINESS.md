# PHASE 3B — IMPLEMENTATION READINESS

**Proyek:** Tova Matano Care Center (TMCC)  
**Dokumen:** Kesiapan Implementasi Phase 3B (Tentang TMCC)  
**Tanggal:** 11 November 2026  
**Status:** READINESS REVIEW — belum ada kode/migration yang dijalankan  
**Lingkup:** **Hanya halaman Tentang TMCC.** Halaman lain = fase terpisah (lihat `TMCC_MASTER_REVISION_ROADMAP.md`).

---

## 1. DESAIN VERIFIKASI LEGAL (PUBLICATION SAFETY)

### 1.1 Prinsip
Data legal **tidak boleh** tampil publik sebelum diverifikasi. Visibilitas dikendalikan oleh **dua syarat** (keduanya harus benar):
1. `site_settings.about_legal_verified === true`
2. `about_content.legal.items` tidak kosong

### 1.2 Skema kolom
| Kolom | Tipe | Null? | Default | Keterangan |
|---|---|---|---|---|
| `about_legal_verified` | `boolean` (`TINYINT(1)`) | **NOT NULL** | **FALSE (0)** | Hanya admin berwenang dapat mengubah |

### 1.3 Perilaku frontend & controller
- **Admin** menyiapkan item legal di Filament (disimpan di `about_content.legal.items`).
- **Default migration**: `about_legal_verified = 0` → Legalitas **tersembunyi** di semua lingkungan/fresh deploy.
- **Controller** (`WebController::about()`): jika `about_legal_verified !== true`, **kosongkan** `legal.items` dari props agar data legal tak terkirim ke browser:
  ```php
  if (! $settings?->about_legal_verified) {
      $aboutContent['legal']['items'] = ['id' => '', 'en' => ''];
  }
  ```
- **Frontend** (`About.jsx`): render seksi Legalitas **hanya jika** `settings.about_legal_verified === true` **dan** `legalItems.length > 0`. Tidak ada default/fallback legal.
- **Wajib dihapus:** nomor legal placeholder/hardcoded lama di `About.jsx` (SK `AHU-0012345…`, NPWP `12.345.678.9…`, `123/Kesbangpol…`). **Tidak boleh** menjadi fallback.

### 1.4 Matriks tampilan
| `about_legal_verified` | `legal.items` | Tampil? | Data dikirim ke browser? |
|---|---|---|---|
| `false` | terisi | **Tidak** | **Tidak** (dikosongkan controller) |
| `false` | kosong | **Tidak** | Tidak |
| `true` | kosong | **Tidak** | (kosong) |
| `true` | terisi | **Ya** | Ya |

### 1.5 Hak akses
- Toggle `about_legal_verified` ditempatkan di panel admin (`ManageSiteSettings`), dilindungi oleh mekanisme otentikasi Filament yang sudah ada. Tidak ada jalur publik untuk mengubahnya.

---

## 2. TEMUAN KOMPATIBILITAS FILAMENT 5

### 2.1 Versi terpasang (terverifikasi)
| Komponen | Versi |
|---|---|
| Filament | **v5.8.4** (`composer show filament/filament`, `composer.lock`) |
| Laravel | **13.33.0** |
| PHP | **8.4.26** |

### 2.2 Komponen yang tersedia (diverifikasi ada di `vendor/`)
- `Filament\Forms\Components\Textarea` → **ADA** (`vendor/filament/forms/src/Components/Textarea.php`)
- `Filament\Schemas\Components\Tabs` → **ADA**
- `Filament\Schemas\Components\Section` → **ADA** (sudah dipakai `ManageSiteSettings`)
- `Filament\Forms\Components\Repeater` → **ADA**

### 2.3 Nested JSON state binding (terverifikasi)
- Filament memakai helper Laravel `data_get`/`data_set` (terbukti di `vendor/filament/schemas/src/Components/Concerns/HasState.php:49,52,661,965` dan `Utilities/Get.php:42`, `Utilities/Set.php:36`).
- `data_get`/`data_set` mendukung **kedalaman path bertitik sembarang** → `about_content.hero.title.id` (4 level) aman.
- **Bukti langsung di codebase:** `ManageSiteSettings` sudah memakai pola ini untuk `fixed_text.id` dan `fixed_text.en` (kolom JSON `fixed_text` di-cast `array` di `SiteSettings`). Ini mengonfirmasi binding & hidrasi JSON bersarang berfungsi pada versi terpasang.

### 2.4 JSON cast & hidrasi (terverifikasi)
- `SiteSettings` mendefinisikan `protected $casts = ['fixed_text' => 'array'];` dan pola ini dapat direplikasi untuk `about_content`.
- `ManageSiteSettings::mount()` melakukan `$settings->toArray()` lalu `$this->form->fill($data)`; `toArray()` mengembalikan JSON yang sudah didekode menjadi array, dan `fill()` mengisi state bersarang. Pola ini teruji pada `fixed_text`.

### 2.5 NULL/default handling (rencana, mengikuti pola existing)
- Saat kolom NULL, `mount()` menormalkan struktur default (analog `if (! isset($data['fixed_text']) || ! is_array($data['fixed_text'])) { $data['fixed_text'] = ['id'=>'','en'=>'']; }`).
- Untuk `about_content`, normalisasi akan mengisi struktur default `{hero,lake,profile,potential,legal,vision,mission,goal,founders}` dengan `{id:'',en:''}` agar form tidak error.

### 2.6 Simpan & muat ulang
- `save()` existing: `$settings->update($this->form->getState())` → state bersarang ditulis ke kolom JSON (cast array). Terbukti untuk `fixed_text`.
- **Verifikasi manual wajib** saat implementasi: simpan nested → reload halaman admin → nilai tetap ada.

### 2.7 Kesimpulan kompatibilitas
**TextInput + Textarea + Section/Tabs** **kompatibel** dengan Filament 5.8.4 untuk state JSON bersarang. Tidak ada asumsi tanpa dasar; didukung bukti kode terpasang.

---

## 3. SKEMA MIGRATION (FINAL, ADITIF)

```php
// database/migrations/xxxx_xx_xx_add_about_content_to_site_settings_table.php
Schema::table('site_settings', function (Blueprint $table) {
    $table->json('about_content')->nullable()->after('fixed_text');
    $table->string('about_lake_image_credit')->nullable()->after('about_lake_image_url');
    $table->boolean('about_legal_verified')->default(false)->after('about_content');
});
```

- **3 kolom baru, semuanya aditif.**
- `about_legal_verified`: `boolean NOT NULL DEFAULT FALSE`.
- Tidak ada perubahan/penghapusan kolom lama (`fixed_text`, `about_hero_image_url`, `about_lake_image_url`, `stat_*`, kontak, dll).
- `down()`: `dropColumn(['about_content','about_lake_image_credit','about_legal_verified'])` — hanya untuk rollback dev.

> **Catatan urutan `after()`:** `about_lake_image_url` sudah ada (migration `2026_10_06_063557_add_lake_image_to_site_settings_table.php`), `fixed_text` ada (`2026_10_21_101845`), sehingga `after()` valid saat migration baru dijalankan.

---

## 4. REVIEW KEAMANAN MIGRATION + CHECKLIST BACKUP

### 4.1 Jaminan
- ✅ Record `site_settings` lama **tidak disentuh** (hanya kolom baru ditambahkan).
- ✅ Kolom baru **nullable / default aman** (`about_content` NULL, `about_lake_image_credit` NULL, `about_legal_verified` 0).
- ✅ **Tidak ada operasi destruktif** (no drop/rename/alter tipe).
- ✅ **Tidak ada data migration wajib** (frontend default menutup tampilan; admin mengisi belakangan).
- ✅ `fixed_text`, URL gambar, statistik, kontak **tetap utuh**.

### 4.2 Checklist uji lokal & backup
1. **Backup DB** sebelum migrate: `mysqldump -u USER -p DBNAME > backup_pre_3b.sql` (atau tool setara).
2. Catat `SELECT COUNT(*) FROM site_settings;` (harus tetap setelah migrate).
3. Jalankan `php artisan migrate` (lokal/staging dulu).
4. Verifikasi kolom: `SHOW COLUMNS FROM site_settings;` memuat 3 kolom baru.
5. Verifikasi data lama utuh: `SELECT fixed_text, about_hero_image_url, about_lake_image_url FROM site_settings;` tidak berubah.
6. Boot test: `php artisan about` tanpa error.
7. `php artisan test --compact` (68 test existing tetap hijau).
8. `npm run build` sukses.
9. Smoke test `GET /tentang` → 200, Legalitas tersembunyi (flag default false).
10. Uji Filament: isi nested JSON → simpan → reload → nilai persist; toggle `about_legal_verified` → section legal tampil/hilang.
11. Baru setelah lolos: jalankan di produksi **dengan backup**.

> **Larangan:** jangan jalankan `down()` di produksi sebelum backup; jangan `migrate:fresh`/`db:wipe`.

---

## 5. DEPENDENSI

| # | Dependensi | Mempengaruhi | Blocking? |
|---|---|---|---|
| D1 | Aset **logo bertulisan** "Tova Matano Care Center" | Hero/Profil rendering | Tidak (pakai logo existing sementara) |
| D2 | Keputusan **caption sumber gambar** danau | Sekilas Danau | Tidak |
| D3 | Konfirmasi **foto hero/danau** terunggah | Hero/Sekilas | Tidak |
| D4 | Verifikasi **angka legal** | Legalitas (gated) | Tidak (tetap hidden sampai verified) |
| D5 | Terjemahan **EN** | Semua teks dinamis | Tidak (fallback `id`) |
| D6 | Konfirmasi **hierarki hero** | Hero | Tidak |
| D7 | Backup DB & window deploy | Migration | **Ya (operasional)** |

---

## 6. CHECKLIST CODING PHASE 3B (URUT)

- [ ] **3B-1** Buat migration `add_about_content_to_site_settings_table` (3 kolom aditif). Uji lokal.
- [ ] **3B-2** `SiteSettings`: tambah fillable (`about_content`, `about_lake_image_credit`, `about_legal_verified`), cast (`about_content => array`, `about_legal_verified => boolean`), helper `getAboutContent(?string $locale)`.
- [ ] **3B-3** `ManageSiteSettings`: Section "Konten Tentang Kami" (hero, danau, profil, potensi, legal, visi/misi/tujuan, pendiri) + `TextInput('about_lake_image_credit')` + `Toggle('about_legal_verified')`; normalisasi `mount()` untuk `about_content`.
- [ ] **3B-4** `WebController::about()`: kirim `aboutContent` tervalidasi-lokal; **kosongkan `legal.items` bila belum verified**.
- [ ] **3B-5** `About.jsx`: restrukturisasi (Hero label/H1/subtitle; Sekilas; Profil; **Potensi TMCC baru**; **Legalitas gated**; Visi/Misi 4/Tujuan 4; Dewan Pendiri narasi); default verbatim; `splitParagraphs`/`splitLines`; perkecil logo & foto; caption credit opsional; **hapus placeholder legal**.
- [ ] **3B-6** `lang/id/site.php` & `lang/en/site.php`: label baru (aditif, key lama dipertahankan).
- [ ] **3B-7** Tests (`tests/Feature/AboutPageTest.php`): 200 + props; `about_content` NULL → default; legal hidden saat `verified=false`; legal tampil saat `verified=true` + items; i18n fallback `en→id`.
- [ ] **3B-8** `php artisan test --compact` + `npm run build` + verifikasi visual (mobile/tablet/desktop).

**Tidak diubah:** semua file Phase 1 & 2 (`Navigation.jsx`, `Footer.jsx`, `Partnerships.jsx`, `Programs.jsx`, `Home.jsx`, `AboutSnippet.jsx`, `WebController::home()`).

---

## 7. DEPENDENSI KLIEN YANG MASIH TERTUNDA

1. Logo bertulisan lengkap (aset).
2. Keputusan caption/atribusi gambar danau (perlu/tidak).
3. Konfirmasi foto hero & danau = versi disetujui.
4. Verifikasi final angka legal.
5. Terjemahan EN (opsional; fallback ID).
6. Konfirmasi hierarki hero.
7. (Lintas fase) ejaan program, background Partnership/Program/Berita/Kontak, GPS stasiun air, struktur bagan, kategori Cerita.

---

## 8. APPROVAL CHECKLIST (SEBELUM MULAI CODING)

- [ ] Arsitektur Opsi A (JSON `about_content` + `about_lake_image_credit` + `about_legal_verified`) disetujui.
- [ ] Desain gating legal (§1) disetujui (default FALSE, hidden sampai verified, tidak dikirim ke browser).
- [ ] Kompatibilitas Filament 5.8.4 (§2) diterima.
- [ ] Skema migration aditif (§3) disetujui.
- [ ] Checklist backup & uji (§4) disetujui; backup DB dijadwalkan.
- [ ] Lingkup Phase 3B **hanya Tentang TMCC** disetujui (halaman lain terpisah).
- [ ] File scope (§6) disetujui; Phase 1 & 2 tidak disentuh.
- [ ] Dependensi klien (§7) dipahami (tidak menghambat mulai coding, kecuali D7 operasional).

---

## 9. KESIMPULAN

Phase 3B **siap diimplementasikan** secara teknis: Filament 5.8.4 mendukung nested JSON binding (terbukti), migration aditif aman, dan desain legal gating memenuhi syarat keamanan publikasi. Mulai coding menunggu **persetujuan eksplisit** atas checklist §8.
