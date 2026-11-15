# Phase 3B-4 — Public About Controller & Legal Data Gating

**Status:** Implemented and verified locally. Awaiting approval.
**Scope:** `app/Http/Controllers/WebController.php` + focused tests + this report. `About.jsx`, `ManageSiteSettings.php`, `SiteSettings.php`, migrations, seeds, localization files, and Phase 1/2 files were not changed.

---

## 1. Files Changed

| Status | File |
| --- | --- |
| Modified | `app/Http/Controllers/WebController.php` |
| Added | `tests/Feature/AboutPageTest.php` |
| Added | `docs/reports/PHASE_3B_4_CONTROLLER_REPORT.md` |

Pre-existing working-tree changes from Phase 2B and Phase 3B-1/2/3 were preserved.

No migration was created or run. No database records were changed.

---

## 2. Inertia Props Structure (`GET /tentang`)

```jsonc
{
  "founders": [ /* unchanged: Founder::orderBy('order')->get() */ ],

  "settings": {
    // Existing fields preserved (images, contact, stats, social, etc.)
    // `about_content` is REMOVED from serialization (see §3).
    // `about_legal_verified` and `about_lake_image_credit` remain available.
    "about_hero_image_url": "settings/...",
    "about_lake_image_url": "settings/...",
    "about_legal_verified": false,
    "about_lake_image_credit": "..."
  },

  "aboutContent": {
    "hero":      { "eyebrow": "...", "title": "...", "subtitle": "..." },
    "lake":      { "title": "...", "body": "P1\n\nP2" },
    "profile":   { "title": "...", "body": "P1\n\nP2" },
    "potential": { "title": "...", "body": "P1\n\nP2" },
    "legal":     { "title": "...", "items": "", "verified": false },
    "vision":    "...",
    "mission":   "M1\nM2\nM3\nM4",
    "goal":      "T1\nT2\nT3\nT4",
    "founders":  { "title": "...", "narrative": "..." }
  },

  "aboutLakeImageCredit": "..." // separate prop, nullable
}
```

- The structure exactly matches the approved architecture (`PHASE_3_1_ABOUT_CONTENT_ARCHITECTURE.md` §2) and the model output from Phase 3B-2.
- `aboutContent` contains **resolved localized strings**, not the raw `{id, en}` blob.
- `settings` keeps every legacy field needed by `About.jsx` and `PublicLayout`/`Footer`; only `about_content` is withheld.

---

## 3. Legal Gating Implementation

### 3.1 Where it happens

Server-side, in `WebController::gateAboutLegalContent()`, **before** `Inertia::render()` builds the response. The frontend never receives unverified legal content, so it cannot be exposed by CSS or client-side conditions.

### 3.2 Conditions

Legal items are shared only when **all** are true:

1. `settings.about_legal_verified === true` (strict boolean).
2. Resolved `legal.items` is a **string** (valid model structure).
3. `trim($items) !== ''` (non-empty after normalization).

Otherwise:

```php
$aboutContent['legal']['items'] = '';
$aboutContent['legal']['verified'] = false;
```

Result:

| `about_legal_verified` | `legal.items` | Sent to browser? | `legal.verified` prop |
| --- | --- | --- | --- |
| `false` | filled | **No** (`''`) | `false` |
| `false` | empty | No (`''`) | `false` |
| `true` | empty/whitespace | No (`''`) | `false` |
| `true` | valid non-empty | **Yes** | `true` |

`legal.verified` is set by the controller to the **actual exposure result**, not merely the stored flag.

### 3.3 No raw blob leak

`WebController::settings()` now returns `SiteSettings::first()?->makeHidden(['about_content'])`. This:

- Removes the raw `about_content` JSON from the `settings` prop on **every** public page, not only About.
- Does not alter DB data, model accessors, or admin editing.
- Leaves `founders`, image URLs, contact fields, and all other legacy props intact.

No top-level `about_content` prop is sent. No legal fallback or placeholder is generated.

---

## 4. Proof That Unverified Legal Data Does Not Leak

`tests/Feature/AboutPageTest.php`:

- Stores a unique sentinel `AHU-SECRET-001` in `about_content.legal.items` with `about_legal_verified = false`.
- Asserts `aboutContent.legal.items === ''` and `aboutContent.legal.verified === false`.
- Asserts the **entire raw HTTP response body** does not contain `AHU-SECRET-001`:

```php
expect($response->getContent())->not->toContain('AHU-SECRET-001');
```

- Separately asserts the raw blob key is absent:

```php
$page->missing('about_content')->missing('settings.about_content');
expect($response->getContent())->not->toContain('about_content');
```

These assertions confirm the data is removed at the server boundary, not hidden at render time.

---

## 5. Locale Fallback

- `about()` uses `app()->getLocale()` (the value set by the existing `SetLocale` middleware / app config).
- `SiteSettings::getAboutContent($locale)` preserves the approved resolution order:
  1. requested locale (e.g. `en`) if non-empty,
  2. Indonesian (`id`) if the requested value is empty,
  3. approved Indonesian default content from the model.
- No automatic translation is performed.

Tests:

- `uses english content when it is available`
- `falls back to indonesian content for the english locale when translations are empty`

---

## 6. Lake Image Credit

- Sent as its own prop: `aboutLakeImageCredit` (`string|null`).
- Also remains available on `settings.about_lake_image_credit`; it is **not** merged into `aboutContent`.
- Image URLs (`settings.about_hero_image_url`, `settings.about_lake_image_url`) are unchanged.
- Test: `shares the lake image credit as a separate prop` (also asserts `aboutContent.legal.image_credit` is missing).

---

## 7. Existing Behavior / Regression Coverage

- Kept `founders` and `settings` props; `About.jsx` currently depends on `founders` and `settings.about_hero_image_url` / `settings.about_lake_image_url`. Both are preserved.
- `WebController::home()` statistics, member queries, founder queries, and all other actions were not altered.
- Legacy prop test: `keeps the legacy about props available` (founders + hero/lake image URLs).
- Full suite verifies no regressions.

---

## 8. Test Results

| Command | Result |
| --- | --- |
| `vendor/bin/pint app/Http/Controllers/WebController.php tests/Feature/AboutPageTest.php --format agent` | passed (style fixes applied) |
| `php artisan test --compact tests/Feature/AboutPageTest.php` | **9 passed, 147 assertions** |
| `php artisan test --compact` | **93 passed, 614 assertions** |

New test count: 9 (previous suite was 84).

---

## 9. Git Status

```
 M app/Filament/Pages/ManageSiteSettings.php      (Phase 3B-3, pre-existing this phase)
 M app/Http/Controllers/WebController.php         (Phase 3B-4 + Phase 2B pre-existing)
 M app/Models/SiteSettings.php                    (Phase 3B-2)
 M resources/js/Components/Public/AboutSnippet.jsx (Phase 2B)
 M resources/js/Pages/Home.jsx                    (Phase 2B)
 M tests/Unit/SiteSettingsTest.php                (Phase 3B-2)
?? database/migrations/2026_11_11_143330_add_about_content_to_site_settings_table.php (Phase 3B-1)
?? docs/reports/                                  (reports)
?? "docs/revisi web.docx"
?? tests/Feature/AboutPageTest.php                (Phase 3B-4)
?? tests/Feature/ManageSiteSettingsTest.php       (Phase 3B-3)
?? tests/Feature/SiteSettingsAboutContentTest.php (Phase 3B-2)
```

No commit, push, stash, deletion, or deploy was performed.

---

## 10. Unresolved Issues / Notes

1. **Granular legal-verification permission** remains unavailable (documented in Phase 3B-3). Only admin-panel access exists; a future roles/permissions phase is recommended.
2. **EN content** is still awaiting client translation; behavior is ID fallback as designed.
3. **Final legal numbers** are still awaiting client verification. Even when entered in Filament, they remain hidden until `about_legal_verified` is explicitly enabled.
4. **`About.jsx` not updated in this phase** (as required). It still renders hardcoded copy and placeholder legal numbers, but those are static frontend text, not data from the server. Phase 3B-5 will remove them and consume `aboutContent`, `aboutLakeImageCredit`, and the gated legal section.
5. `settings.about_legal_verified` is still exposed (boolean, non-sensitive). Phase 3B-5 may use it together with the (already gated) `aboutContent.legal.items`.

**STOP — Phase 3B-4 complete.** Awaiting Project Owner approval before Phase 3B-5 (`About.jsx`).
