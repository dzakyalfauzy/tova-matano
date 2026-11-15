# PHASE 8B.1.1 — LOGO NAVIGATION & ABOUT MOBILE LAYOUT REFINEMENT REPORT

**Project:** Tova Matano Care Center (TMCC)  
**Phase:** 8B.1.1 — UI Refinement (remediation only)  
**Date:** November 14, 2026  
**Status:** COMPLETE — AWAITING MANUAL VISUAL QA  
**Scope:** Navigation logo lockup + About mobile image ordering

---

## 1. Navigation — Before / After

File: `resources/js/Components/Public/Navigation.jsx`

### Before (Phase 8B.1)
```jsx
<Link href="/" className="flex items-center flex-shrink-0">
    <img
        src="/image/logo_tova_matano_full.png"
        alt="Logo Tova Matano Care Center"
        className="h-11 w-auto object-contain sm:h-12 md:h-14"
    />
</Link>
```
Problems observed in manual QA:
- Stacked full logo too small at nav height.
- The asset's opaque white background produced a distracting white rectangle on the navy bar.
- Embedded wordmark unreadable at mobile size.

### After (Phase 8B.1.1)
```jsx
<Link href="/" className="flex items-center gap-2 sm:gap-3 flex-shrink-0">
    <img
        src="/image/logo_tova_matano.png"
        alt="Logo Tova Matano Care Center"
        className="h-9 w-auto object-contain sm:h-10"
    />
    <span className="font-heading font-bold text-sm leading-tight tracking-wide text-brand-paper hover:text-brand-gold transition-colors sm:text-base md:text-lg lg:text-xl">
        Tova Matano Care Center
    </span>
</Link>
```
- Transparent emblem on the left, brand wordmark on the right (horizontal lockup).
- Responsive typography: `text-sm` (mobile) → `sm:text-base` → `md:text-lg` → `lg:text-xl`.
- Responsive gap: `gap-2 sm:gap-3`.
- Emblem height 36 px / 40 px — inside the unchanged `h-16` bar.
- Navigation height, links, language switcher, CTA, sticky behavior, `xl` desktop breakpoint, and mobile menu all unchanged.

---

## 2. About Page — Mobile & Desktop Ordering

File: `resources/js/Pages/About.jsx` — section "Profil & Sejarah" (`grid grid-cols-1 lg:grid-cols-2 gap-16 items-center`).

### Before
| Viewport | Order |
|---|---|
| Mobile | Heading + text → logo |
| Desktop | Text (left) → logo (right) |

### After
```jsx
<div className="order-2 lg:order-1">   {/* heading + text */} </div>
<div className="relative w-full max-w-md mx-auto order-1 lg:order-2">   {/* logo */} </div>
```

| Viewport | Order |
|---|---|
| Mobile / tablet (single column) | **1. Logo → 2. "Profil & Sejarah" heading + text** |
| Desktop (`lg` two columns) | Text (left, `lg:order-1`) → Logo (right, `lg:order-2`) — unchanged |

- DOM structure not duplicated; only CSS `order` used on the existing two grid children.
- Gold offset backdrop, white panel, padding, shadow, `aspect-[4/3]`, and `object-contain` preserved.
- `max-w-md mx-auto` sizing from 8B.1 retained (client's "diperkecil" request).
- No text, localization, or functionality changes.

---

## 3. About Logo Asset

- Still uses `/image/logo_tova_matano_full.png` (unchanged, not overwritten, not deleted).
- No additional white layer, filter, blend mode, or decorative effect was added. The asset's own white background continues to blend naturally with the existing white panel.
- Transparent-variant consideration was **not** attempted; per instructions it would require a separate technical proposal and approval.

---

## 4. Files Modified

| File | Change |
|---|---|
| `resources/js/Components/Public/Navigation.jsx` | Reverted nav to transparent emblem + responsive wordmark lockup. |
| `resources/js/Pages/About.jsx` | Responsive `order` classes so the logo appears above the text on mobile, layout unchanged on desktop. |
| `docs/reports/PHASE_8B1_1_LOGO_MOBILE_REFINEMENT_REPORT.md` | This report (new). |

No PHP files changed → Pint not required/run.
Both logo assets remain intact:
- `public/image/logo_tova_matano.png` (transparent emblem) — used in navigation and fallbacks.
- `public/image/logo_tova_matano_full.png` (stacked lockup) — used on About page only.

---

## 5. Test Results

```
php artisan test --compact
→ 174 passed / 1504 assertions (87.12s)
```
Zero regressions against the Phase 7B baseline (174 tests / 1504 assertions).

---

## 6. Build Results

```
npm run build
→ ✓ built in 6.42s
   About chunk: 9.50 kB
   PublicLayout chunk: 12.87 kB
   app chunk: 349.86 kB
```

**Warnings — all pre-existing, none introduced by this phase:**
1. `esbuild` option deprecation reported by `vite:react-babel` (recommends `oxc`).
2. `optimizeDeps.rollupOptions` deprecation (recommends `rolldownOptions`).
3. `/image/pattern.svg` unresolved at build time (resolved at runtime by design).
4. Vite plugin timing summary.

---

## 7. Remaining Manual Visual QA Checklist

**Navigation**
- [ ] Desktop 1280 px+: emblem + "Tova Matano Care Center" on one line, readable, vertically centered; no white rectangle.
- [ ] Laptop 1024–1279 px: text size `lg:text-xl` fits without colliding with the mobile menu button.
- [ ] Tablet 768 px: `md:text-lg` lockup fits; hamburger/menu behavior unchanged.
- [ ] Mobile 375 px: emblem + `text-sm` wordmark readable, no overflow, bar height unchanged.
- [ ] Long-word wrapping: verify the wordmark does not clip or push the menu button off-screen at 320–375 px.
- [ ] Sticky nav, language switcher, CTA, and all links unchanged.

**About — Profil & Sejarah**
- [ ] Mobile 375 px: logo appears **above** the heading/text.
- [ ] Tablet 768 px: logo above text (single column).
- [ ] Desktop 1280 px+: text left, logo right, gold offset backdrop aligned, proportions unchanged.
- [ ] Logo aspect ratio preserved, no cropping/stretching.

**Regression spot checks**
- [ ] About hero fallback emblem still works when no hero image set.
- [ ] Organization hero fallback emblem unchanged.
- [ ] Lake-image `onError` fallback unchanged.

---

## 8. Safety Confirmation

- No database, schema, migration, seeder, storage, `.env`, or production changes.
- No other pages/components modified.
- All uncommitted work from Phases 1–8B.1 preserved.
- No commit, push, or deployment performed.

---

**STOP — awaiting manual visual QA and approval.**
