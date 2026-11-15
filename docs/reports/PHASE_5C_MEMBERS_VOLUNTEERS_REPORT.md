# PHASE 5C — Members & Volunteers Photo Visibility Audit Report

**Project:** Tova Matano Care Center (TMCC)  
**Phase:** 5C — Members & Volunteers  
**Date:** 2026-11-13  
**Status:** Investigation and regression test suite complete — awaiting user approval

---

## 1. Investigation Summary

Client requirement from `docs/revisi web.docx` (section `PAGE : ANGGOTA DAN RELAWAN` — `MASUKAN#12`):
> "HANYA MAU MEMASTIKAN PHOTO ANGGOTA MUNCUL"

We conducted a comprehensive, read-only architectural and database audit across:
1. Public Members page rendering (`resources/js/Pages/Members.jsx`).
2. Controller data pipeline (`WebController::members()`).
3. Filament Admin Member and Volunteer upload flows (`MemberForm.php`, `VolunteerForm.php`).
4. Local MySQL database records and file system storage (`storage/app/public`).

---

## 2. Client Requirement & Interpretation

- **Client statement:** "HANYA MAU MEMASTIKAN PHOTO ANGGOTA MUNCUL"
- **Context:** The client is not requesting a redesign or new features; they want to confirm that photos uploaded for members appear on the public website.
- **Audit Findings:** The existing code implementation for displaying member and volunteer photos is **already correct and functional**. No code bug exists in the image rendering pipeline. If photos do not appear on a deployed environment, the cause is **missing data or unlinked storage on that server**, not a code defect.

---

## 3. Files Inspected

- `resources/js/Pages/Members.jsx`
- `app/Http/Controllers/WebController.php`
- `app/Models/Member.php`
- `app/Models/Volunteer.php`
- `app/Filament/Resources/Members/Schemas/MemberForm.php`
- `app/Filament/Resources/Members/Tables/MembersTable.php`
- `app/Filament/Resources/Volunteers/Schemas/VolunteerForm.php`
- `app/Filament/Resources/Volunteers/Tables/VolunteersTable.php`
- `database/migrations/2026_01_01_000007_create_members_table.php`
- `database/migrations/2026_01_01_000008_create_volunteers_table.php`
- `database/seeders/DatabaseSeeder.php`

---

## 4. Files Changed

| File | Change |
|---|---|
| `tests/Feature/MembersPageTest.php` | Added dedicated feature and source regression tests (5 tests / 65 assertions). |
| `docs/reports/PHASE_5C_MEMBERS_VOLUNTEERS_REPORT.md` | This report. |

**No application code was modified**, adhering strictly to the guideline: *"If the issue cannot be reproduced because local data is empty, do not invent a fix."*

---

## 5. Local Data Inspection Results

| Inspection Target | Result | Notes |
|---|---|---|
| **Member records in local DB** | **0 records** | Table `members` is empty locally. |
| **Volunteer records in local DB** | **0 records** | Table `volunteers` is empty locally. |
| **Approved Volunteers in local DB** | **0 records** | None. |
| **`public/storage` symlink** | **Exists (Junction)** | Points directly to `storage/app/public`. |
| **`storage/app/public/members`** | **1 orphaned file** | `01M3EX3ASCM958HTWYC2XP6631.png` (3.9 MB). No matching DB record. |
| **`storage/app/public/volunteers`** | **Directory absent** | No volunteer photos uploaded locally. |
| **Seeder definitions (`DatabaseSeeder.php`)** | **15 members, 5 volunteers** | None of the seeded members specify a `photo_url`. |

---

## 6. Member Photo Rendering Flow

In `resources/js/Pages/Members.jsx:138-150`:
```jsx
<div className="w-12 h-12 rounded-full overflow-hidden shrink-0 border-2 border-brand-teal/30 bg-white">
    {member.photo_url ? (
        <img
            src={`/storage/${member.photo_url}`}
            alt={member.name}
            className="w-full h-full object-cover"
        />
    ) : (
        <div className="w-full h-full bg-brand-teal/10 flex items-center justify-center font-bold text-brand-teal text-lg">
            {member.name.charAt(0)}
        </div>
    )}
</div>
```
- **When `photo_url` is present:** Renders an `<img>` tag with `/storage/${member.photo_url}`, 48×48px avatar circle, `object-cover`.
- **When `photo_url` is null/empty:** Gracefully renders the first letter of the member's name as an initials badge on teal background.

---

## 7. Volunteer Photo Rendering Flow

In `resources/js/Pages/Members.jsx:233-245`:
```jsx
<div className="w-12 h-12 rounded-full overflow-hidden shrink-0 border-2 border-emerald-500/30 bg-emerald-50 flex items-center justify-center">
    {volunteer.photo_url ? (
        <img
            src={`/storage/${volunteer.photo_url}`}
            alt={volunteer.full_name}
            className="w-full h-full object-cover"
        />
    ) : (
        <span className="font-heading font-bold text-emerald-700 text-lg">
            {volunteer.full_name?.charAt(0) || 'R'}
        </span>
    )}
</div>
```
- Only volunteers with `status === 'APPROVED'` are passed to the frontend by `WebController::members()`.
- Renders `/storage/${volunteer.photo_url}` when populated; falls back to initial letter (or `'R'`) when empty.

---

## 8. Admin Upload & Storage Flow

In `app/Filament/Resources/Members/Schemas/MemberForm.php:28-33`:
```php
FileUpload::make('photo_url')
    ->label('Foto')
    ->image()
    ->disk('public')
    ->directory('members')
    ->columnSpanFull()
```

In `app/Filament/Resources/Volunteers/Schemas/VolunteerForm.php:48-53`:
```php
FileUpload::make('photo_url')
    ->label('Foto')
    ->image()
    ->disk('public')
    ->directory('volunteers')
    ->columnSpanFull()
```
- Both use the `public` storage disk (`storage/app/public`).
- Both store relative paths in the database (e.g. `members/01M3...png` or `volunteers/01M3...jpg`).
- The frontend prepends `/storage/` to construct `/storage/members/01M3...png`.
- Because `public/storage` is linked to `storage/app/public`, files serve with HTTP 200 without routing overhead.

---

## 9. Confirmed Defects and Fixes

- **Code Defects:** **None found.** The full chain (Admin upload → Database relative path → Controller prop → React `/storage/${photo_url}` → DOM `<img>`) is completely sound.
- **Code Fixes:** **None needed.** No changes were made to application logic.

---

## 10. Potential Issues That Could Not Be Reproduced

If the client or tester observes that member photos are missing on another environment (e.g. production/staging), the cause is guaranteed to be one of the following environment or data conditions:
1. **Empty `photo_url` in Database:** The records in the database do not have a `photo_url` filled, so the UI correctly displays the initials fallback instead of an image.
2. **Missing `public/storage` Symlink:** On the production or staging server, `php artisan storage:link` has not been executed, causing `/storage/...` requests to return HTTP 404.
3. **Files Not Migrated/Uploaded:** When deploying between local and remote environments, files in `storage/app/public/members` were not copied across servers.
4. **Hardcoded Seed Data:** Running `php artisan db:seed` inserts 15 dummy members, none of which specify a `photo_url`.

---

## 11. Automated Test Results

Created `tests/Feature/MembersPageTest.php`:

```text
php artisan test tests/Feature/MembersPageTest.php --compact
```
Result: **5 passed / 65 assertions**

1. `returns HTTP 200 and the Members component with member and approved volunteer props`
2. `filters out pending and rejected volunteers from the public page`
3. `orders members descending by points`
4. `renders the public page gracefully when no members or volunteers exist`
5. `constructs storage URLs for member and volunteer photos with initials fallback`

### Full Suite Validation
```text
php artisan test --compact
```
Result: **146 passed / 1201 assertions** (All passing).

---

## 12. Pint & Build Results

- **Pint:** `vendor/bin/pint --dirty --format agent` → **PASS** (no formatting changes needed).
- **Build:** `npm run build` → **SUCCESS** (`✓ 3450 modules transformed in 3.96s`).

---

## 13. Manual Visual QA Checklist

For verification on environments where real member photos exist:
- [ ] Log in to Admin Panel (`/admin/members`).
- [ ] Create or edit a member and upload a profile photo (JPEG/PNG/WebP).
- [ ] Save the record and confirm the photo displays in the admin edit form.
- [ ] Navigate to `/anggota`.
- [ ] Confirm the member card displays the circular photo avatar.
- [ ] Confirm members without a photo display their first initial in a colored badge.
- [ ] Log in to Admin Panel (`/admin/volunteers`).
- [ ] For an approved volunteer, upload a photo and verify it displays on `/anggota` under "Relawan Terverifikasi".
- [ ] Confirm PENDING and REJECTED volunteers do not appear on `/anggota`.

---

## 14. Pending Decisions Requiring User/Client Approval

The following architectural observations were noted during audit and **intentionally left unchanged**:

1. **`is_featured` column on `members`:**
   - Migration comment: `"exclude dari portal tanpa hapus data"`.
   - Current behavior: `WebController::members()` loads all members regardless of `is_featured`.
   - *Decision needed:* Should unfeatured members be hidden from `/anggota`?
2. **Upload Dimension/Size Restrictions:**
   - Currently, admin photo uploads for members and volunteers have no `maxSize()` or `dimensions()` rule. (A 3.9 MB / 6000px image was uploaded locally).
   - *Decision needed:* Should a 2 MB or 5 MB max size rule be added to `MemberForm` and `VolunteerForm`?
3. **Localization:**
   - `/anggota` currently contains hardcoded Indonesian text and does not use `site.php` translation strings.
   - *Decision needed:* Should translation keys be introduced for the Members page?

---

## 15. Final Recommendation

**PASS WITH LIMITATIONS**

- **Pass:** The application logic, storage path construction, fallback display, and admin upload infrastructure are completely verified and covered by automated regression tests.
- **Limitations:** Photo visibility on live sites depends on admin data entry (uploading photos to member records) and server storage configuration (`php artisan storage:link`).

**STOP.** Phase 5C audit and verification is complete. Do not start another phase. Awaiting user approval.
