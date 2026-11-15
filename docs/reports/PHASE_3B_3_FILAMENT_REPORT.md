# Phase 3B-3 — Filament Admin Management for About TMCC

**Status:** Implemented and verified locally. Awaiting approval.
**Scope:** About TMCC content management only (Phase 3B). No public About rendering changes (Phase 3B-4/3B-5), no migrations, no seeders, no DB writes outside tests.

---

## 1. Objective

Add admin management for the new About content in `ManageSiteSettings`:

- Localized (`id` / `en`) About fields bound to the `about_content` JSON column.
- `about_lake_image_credit` as a separate string column.
- `about_legal_verified` toggle, default `false`, never auto-enabled by filling legal data.
- Keep the existing settings form functional and preserve all existing data.
- Do not assume that filling legal items means verification.

---

## 2. Authorization Findings (Requirement #4)

### What exists

- No `spatie/laravel-permission`, no Bouncer, no Filament Shield.
- No `app/Policies` directory and no `Gate::define` calls in providers.
- The only authorization mechanism is `User::canAccessPanel()`:

```php
public function canAccessPanel(Panel $panel): bool
{
    return $panel->getId() === 'admin';
}
```

Every authenticated user in the `admin` panel has identical access. There is no role or per-permission concept to distinguish a "legal verifier" from any other admin.

### Decision

The existing system cannot express a finer-grained permission without adding a dependency or schema change (out of scope, and forbidden without approval). To keep the feature safe and shippable while being explicit about the limitation:

1. Legal verification is restricted to users with **admin panel access** — the strongest authorization signal the project currently has. This is enforced in three places:
   - The whole page is behind Filament's `Authenticate` middleware.
   - The toggle is disabled when `ManageSiteSettings::canManageLegalVerification()` is false.
   - `save()` preserves the stored `about_legal_verified` value when the current user fails that check, so a tampered request cannot flip it.
2. The toggle defaults to `false` and is never enabled by filling legal items.
3. This limitation is reported here. **Recommendation:** add a granular roles/permissions phase before a second admin operator is introduced. Until then, verification is effectively `"any admin-panel user"`.

`canManageLegalVerification()` returns `false` for guests and for any user that is not a `FilamentUser` able to access the current panel.

---

## 3. Implementation

### File changed

`app/Filament/Pages/ManageSiteSettings.php`

### Added sections

| Section | Fields |
| --- | --- |
| Konten Tentang Kami — Hero | `hero.eyebrow`, `hero.title`, `hero.subtitle` (`id`/`en`) |
| Sekilas Danau Matano | `lake.title`, `lake.body` (`id`/`en`) |
| Profil & Sejarah | `profile.title`, `profile.body` (`id`/`en`) |
| Potensi TMCC | `potential.title`, `potential.body` (`id`/`en`) |
| Legalitas | `legal.title`, `legal.items` (`id`/`en`) + `about_legal_verified` toggle |
| Visi, Misi & Tujuan | `vision`, `mission`, `goal` (`id`/`en`) |
| Dewan Pendiri | `founders.title`, `founders.narrative` (`id`/`en`) |

- Sections are collapsible to keep the form manageable.
- Long text uses `Textarea`; mission/goal/legal items use one-item-per-line helper text.
- All localized fields bind to nested paths such as `about_content.hero.title.id`, which Filament resolves via `data_get`/`data_set`.
- `about_lake_image_credit` was added next to the lake image upload in "Tampilan Header & Gambar" and is stored in its own column.

### Mount normalization

`mount()` now runs `normalizeAboutContent()`, which guarantees the form always receives the full `{id, en}` structure even when `about_content` is empty, partial, or contains legacy scalar values. This prevents Livewire state-path errors and makes editing safe for both new and pre-existing rows.

### Save behavior

```php
$settings = SiteSettings::firstOrCreate([]);
$state = $this->form->getState();

if (! static::canManageLegalVerification()) {
    $state['about_legal_verified'] = (bool) $settings->about_legal_verified;
}

$settings->update($state);
```

- Only `about_content`, the credit, and the verification flag are mutated; other settings pass through unchanged.
- No reactive logic enables the toggle when legal items are present.
- Public display gating (`verified === true` AND non-empty items) remains the responsibility of Phase 3B-4; nothing is exposed yet.

---

## 4. Tests

New file: `tests/Feature/ManageSiteSettingsTest.php` (6 tests, 33 assertions).

| Test | Covers |
| --- | --- |
| `persists localized about content and the lake image credit from the admin form` | JSON structure, `id`/`en` pairs, multi-paragraph/newline preservation, credit in separate column, default `verified = false` |
| `does not mark legal content as verified when only legal items are filled` | Filling legal data never auto-enables verification (requirement #5) |
| `lets an authorized panel user mark legal verification explicitly` | Explicit toggle persists `true` |
| `preserves unrelated settings while saving about content` | Existing `phone`, `fixed_text`, and previously stored About fields are preserved; partial JSON is normalized |
| `only allows users with admin panel access to manage legal verification` | `canManageLegalVerification()` is `true` for a panel admin and `false` for a guest (requirement #7, within existing authorization limits) |
| `does not allow guests to open the site settings page` | Page is gated by Filament auth |

Verification commands:

- `vendor/bin/pint app/Filament/Pages/ManageSiteSettings.php tests/Feature/ManageSiteSettingsTest.php --format agent` → passed
- `php artisan test --compact tests/Feature/ManageSiteSettingsTest.php` → 6 passed, 33 assertions
- `php artisan test --compact` → **84 passed, 467 assertions**

---

## 5. Invariants Preserved

- No migrations, seeders, or schema changes.
- No production or `.env` changes.
- No fabricated legal numbers anywhere.
- `about_legal_verified` remains `false` by default and is not derived from legal item content.
- Existing `site_settings` row and all unrelated fields are untouched.
- `docs/revisi web.docx` untouched.

---

## 6. Known Limitation / Open Item

**Granular legal-verification permission is not available.** The project has only panel-level access. Verification is therefore limited to authenticated admin-panel users, with server-side enforcement. A future roles/permissions phase is recommended if more than one admin operator exists or if legal approval must be separated from general content editing.

---

## 7. Next Phase (not started)

- **Phase 3B-4:** public About controller gating — expose `legal` only when `about_legal_verified === true` and `legal.items` is non-empty; otherwise return empty items. EN → ID fallback.
- **Phase 3B-5:** wire `About.jsx` to the new content and add the optional image-credit caption.
- **Phase 3B-6:** translation keys / copy polish.

Awaiting approval before continuing.
