# Public Frontend Audit (Phase 1)

## Shared include map

- Public pages include `public/header.php` and `public/footer.php`.
- `public/header.php` loads:
  - `config/settings.php`
  - `config/system.php`
  - `config/requires.php`
  - then calls `html__header()` and `html__show_style_header('public', ...)`.
- `public/footer.php` calls:
  - `html__show_style_footer('public')`
  - `html__footer()`
- Shared HTML shell + CSS includes are generated in `tagsets/html_stuff.php`.
- Public navigation is generated from:
  - `html__get_public_menu()` in `tagsets/html_stuff.php`
  - rendered by `html__build_menu(...)`
  - injected into style templates:
    - `style/orsee/html_header.php` (`#navigation#`)
    - `style/orsee2/html_header.php` (`#navigation_horizontal#`)

## Public PHP pages

- `public/index.php`
- `public/participant_create.php`
- `public/participant_login.php`
- `public/participant_reset_pw.php`
- `public/participant_change_pw.php`
- `public/participant_confirm.php`
- `public/participant_edit.php`
- `public/participant_delete.php`
- `public/participant_show.php`
- `public/participant_logout.php`
- `public/show_calendar.php`
- `public/rules.php`
- `public/privacy.php`
- `public/faq.php`
- `public/faq_vote.php`
- `public/impressum.php`
- `public/contact.php`
- `public/disabled.php`

## Primary form/list surfaces

- Registration: `public/participant_create.php`
- Login: `public/participant_login.php`
- Password reset/change: `public/participant_reset_pw.php`, `public/participant_change_pw.php`
- Profile edit/show and session signup lists: `public/participant_edit.php`, `public/participant_show.php`
- FAQ list/table: `public/faq.php`

## Legacy width/inline-style assumptions (to progressively remove)

- Fixed table widths (`80%`, `90%`, `400px`) across public pages.
- Table-based shell/navigation in:
  - `style/orsee/html_header.php`
  - `style/orsee2/html_header.php`
- Inline style usage on key public pages:
  - `public/participant_create.php`
  - `public/participant_show.php`
  - `public/faq.php`
  - `public/contact.php`
  - `public/privacy.php`
  - `public/rules.php`

## Baseline changes implemented in this branch

- Public viewport/meta and responsive stylesheet hook in `tagsets/html_stuff.php`.
- New public responsive baseline file: `style/public-mobile.css`.
- Scope is public-only (`/public/` routes), no backend contract changes.

## PR 2 changes implemented in this branch

- Public mobile menu toggle injected only for public pages in `html__show_style_header(...)`.
- Accessible attributes: `aria-expanded`, `aria-controls`, Escape close behavior.
- New menu behavior script: `style/public-mobile-nav.js`.
- Mobile nav styles and tap-target sizing in `style/public-mobile.css`.
- Menu closes when a nav link is activated on mobile width.

## Quick manual checks

- Open `http://127.0.0.1:8080/public/participant_create.php`.
- At widths <= 768px:
  - Menu button is visible.
  - Menu opens/closes by tap.
  - Escape closes menu.
  - Nav links are block-level and tap-friendly.
- At widths > 768px:
  - Existing desktop navigation remains visible.

## PR 4 changes implemented in this branch

- Shared content-page wrapper classes applied to:
  - `public/rules.php`
  - `public/privacy.php`
  - `public/impressum.php`
  - `public/contact.php`
- FAQ page converted from popup/table flow to server-rendered accordion:
  - `public/faq.php`
  - No popup dependency needed for FAQ rendering.
  - Voting remains available via async call to `public/faq_vote.php`.
- Reusable content and FAQ styling added in:
  - `style/public-mobile.css`

### PR 4 manual checks

- Open:
  - `/public/rules.php`
  - `/public/privacy.php`
  - `/public/impressum.php`
  - `/public/contact.php`
  - `/public/faq.php`
- On mobile widths:
  - content pages have readable text rhythm and container width.
  - FAQ renders as accordion sections with tap-friendly summaries.
  - FAQ vote button disables after click.

## PR 5 changes implemented in this branch

- Public calendar mobile agenda view added in `tagsets/calendar.php`:
  - Uses the same session/event data as month grid.
  - On small screens, agenda cards are shown; month grid is hidden via CSS.
  - Desktop keeps existing month grid behavior.
- Public session tables now include mobile-friendly hooks in `tagsets/expregister.php`:
  - Added table classes for invited/registered/history lists.
  - Added `data-label` metadata on cells for card-like mobile rendering.
- Responsive calendar/session card styles added in `style/public-mobile.css`.

### PR 5 manual checks

- Open `http://127.0.0.1:8080/public/show_calendar.php`:
  - <=768px: agenda cards should appear instead of the month table.
  - >768px: original month grid remains.
- Open `http://127.0.0.1:8080/public/participant_show.php` after login:
  - invited/registered/history sections should render as stacked cards on mobile widths.
  - register/cancel actions remain visible and clickable.
