# PHASE 8B.1.2 — ABOUT PAGE MOBILE IMAGE ORDER REFINEMENT REPORT

**Project:** Tova Matano Care Center (TMCC)  
**Phase:** 8B.1.2 — About page mobile layout refinement  
**Date:** November 14, 2026  
**Status:** COMPLETE — AWAITING MANUAL VISUAL QA  
**Scope:** `resources/js/Pages/About.jsx` only

---

## 1. Sekilas Danau Matano — Before / After

Grid: `grid grid-cols-1 lg:grid-cols-2 gap-16 items-center` (unchanged).

### Before
```jsx
<div className="order-2 lg:order-1 relative">      {/* lake image */}
<div className="order-1 lg:order-2">                {/* heading + text */}
```
| Viewport | Order |
|---|---|
| Mobile | Text → image |
| Desktop | Image left → text right |

### After
```jsx
<div className="order-1 lg:order-1 relative">      {/* lake image */}
<div className="order-2 lg:order-2">                {/* heading + text */}
```
| Viewport | Order |
|---|---|
| Mobile / tablet | **1. Lake Matano image → 2. "Sekilas Danau Matano" heading + text** |
| Desktop (`lg`) | Image left → text right — unchanged |

- Teal offset backdrop, `aspect-[4/3]`, `object-cover`, rounded corners, shadow, and the image-credit caption were all preserved.
- `onError` emblem fallback untouched.

---

## 2. Profil & Sejarah — Preserved

Already correct from Phase 8B.1.1; **not modified in this phase**:
```jsx
<div className="order-2 lg:order-1">                                   {/* heading + text */}
<div className="relative w-full max-w-md mx-auto order-1 lg:order-2">  {/* full logo */}
```
| Viewport | Order |
|---|---|
| Mobile / tablet | **1. Full Tova Matano Care Center logo → 2. "Profil & Sejarah" heading + text** |
| Desktop (`lg`) | Text left → logo right |

- Gold offset backdrop, white panel, `p-8`, `aspect-[4/3]`, `object-contain`, and `max-w-md mx-auto` unchanged.

---

## 3. Desktop Ordering Confirmation

| Section | Desktop order | Verified |
|---|---|---|
| Sekilas Danau Matano | Image left, text right | `order-1 lg:order-1` (image) + `order-2 lg:order-2` (text) |
| Profil & Sejarah | Text left, logo right | `order-2 lg:order-1` (text) + `order-1 lg:order-2` (logo) |

No desktop spacing, sizing, backdrop, or composition was changed.

---

## 4. Files Modified

| File | Change |
|---|---|
| `resources/js/Pages/About.jsx` | Swapped mobile `order` values in the "Sekilas Danau Matano" section only (2 class attributes: `order-2 lg:order-1` → `order-1 lg:order-1`; `order-1 lg:order-2` → `order-2 lg:order-2`). |
| `docs/reports/PHASE_8B1_2_ABOUT_MOBILE_IMAGE_ORDER_REPORT.md` | This report (new). |

`Navigation.jsx` was **not** modified in this phase (approved horizontal lockup preserved). No PHP files changed → Pint not required.

---

## 5. Test Results

**Relevant tests:**
```
php artisan test tests/Feature/AboutPageTest.php --compact
→ 11 passed / 208 assertions
```

**Full suite:**
```
php artisan test --compact
→ 174 passed / 1504 assertions (97.61s)
```
Zero regressions.

---

## 6. Build Results

```
npm run build
→ ✓ built in 5.95s
   About chunk: 9.50 kB
```
**Warnings — all pre-existing, unrelated to this change:**
1. `esbuild` option deprecation from `vite:react-babel` (recommends `oxc`).
2. `optimizeDeps.rollupOptions` deprecation (recommends `rolldownOptions`).
3. `/image/pattern.svg` unresolved at build time (runtime-resolved).
4. Vite plugin timing summary.

---

## 7. Manual Visual QA Checklist

**Mobile 375 px**
- [ ] Sekilas Danau Matano: lake image appears **above** the heading and paragraphs.
- [ ] Profil & Sejarah: full logo appears **above** the heading and paragraphs.
- [ ] Spacing between image and following text is balanced (`gap-16` single column, no overlap with offset backdrops).
- [ ] No horizontal overflow.

**Mobile 393 px**
- [ ] Same ordering as 375 px.
- [ ] Text remains readable; images not clipped.

**Tablet 768 px**
- [ ] Both sections remain single column with image first.
- [ ] Backdrops (teal and gold) aligned without clipping.

**Desktop 1280 px+**
- [ ] Sekilas: image left, text right (unchanged).
- [ ] Profil: text left, logo right (unchanged).
- [ ] Proportions, backdrop offsets, shadows, and rounded corners intact.

**Content regression**
- [ ] Headings/paragraph text unchanged (ID/EN).
- [ ] Image credit caption still displays at bottom-right of the lake image when configured.
- [ ] Failed-image fallback still shows the emblem.

---

## 8. Safety Confirmation

- Only `resources/js/Pages/About.jsx` was edited (plus this report).
- No database, schema, migration, seeder, storage, `.env`, or production changes.
- Both logo assets and all other images remain intact.
- All previous phase work and uncommitted files preserved.
- No commit, push, or deployment.

---

**STOP — awaiting browser QA and approval.**
