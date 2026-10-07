# GoodScores – Phase 0 + 1 + 2 (solidified)

School question bank & exam paper generator (not a CBT app).

**App name:** GoodScores  
**Theme:** Teal primary + antique-white gradient  
**Stack:** PHP 8 + PDO REST API · Vanilla JS (ES modules) · Tailwind · PWA

## XAMPP / htdocs setup

1. Copy folder into htdocs:
   ```
   C:\xampp\htdocs\goodscores\
   ```
   Local development serves the root frontend at:
   `http://localhost/goodscores/`

   The Netlify frontend uses `https://goodscores.leadstar.com.ng` as its DirectAdmin API through `GOODSCORES_API_BASE`.

2. Import DB: `backend/sql/schema.sql` (phpMyAdmin or CLI)

3. Open:
   - **App:** `http://localhost/goodscores/`
   - **Health check:** `http://localhost/goodscores/backend/public/health`
     → should return `{"status":"ok","database":"connected",...}`

### Paystack payments

For split hosting, deploy the static frontend to Netlify and the PHP API to DirectAdmin. Set `window.GOODSCORES_API_BASE` in `index.html` to the public DirectAdmin API URL before the `js/app.js` module. Configure the API's `CORS_ALLOWED_ORIGINS` with the exact Netlify frontend origin. Keep all secrets in the DirectAdmin API `.env`, never in the Netlify files.

Copy `backend/.env.example` to `backend/.env` and set `PAYSTACK_SECRET_KEY`. Keep the secret key server-side only. Use the test keys while testing, then replace both the secret and public keys with live Paystack keys after activating the live Paystack account. Set `PAYSTACK_CALLBACK_URL` to the public frontend URL. Payments are initialized on the server and credits are granted only after Paystack verification.

## Solidification (pre–Phase 3)

- Relative asset paths (works under htdocs subfolder)
- Apache `Authorization` header fallback (JWT works on XAMPP)
- `APP_BASE_PATH` stripping in router
- Health endpoint for quick diagnostics
- Image upload limits (5MB, image MIME only, magic-byte check)
- Uploads folder blocks PHP execution
- Offline question sync when back online
- Toast notifications instead of raw alerts
- Clearer network error messages
- PWA icons + relative manifest/SW
- Question list ownership: own + school-shared

## Phases complete

| Phase | Status |
|-------|--------|
| 0 Foundation | Done |
| 1 Auth + Credits | Done |
| 2 Questions + Papers | Done |
| 3 OCR + PDF export | Next |
| 4 Payments + polish | Later |
| 5 Launch hardening | Later |

## Phase 3 – OCR + PDF export

### OCR (OpenAI vision)
1. Create or use an OpenAI API key with access to the configured vision-capable model.
2. Put it in `backend/.env`:
   ```
   OPENAI_API_KEY=your_key_here
   OPENAI_VISION_MODEL=gpt-4o-mini
   ```
3. In the app: **Questions → New → Extract text with OCR** (costs **35 credits**)

The key is read only by the backend and can be replaced independently in production. Do not expose it in frontend JavaScript or commit it to source control.

### PDF export
- Open a paper → **Export PDF (free for all account levels)**
- With mPDF installed: real PDF file
- Without mPDF: printable HTML (open in browser → Print → Save as PDF)

Install the locked Composer dependencies:
```bash
cd backend
composer install
```

For XAMPP, enable `extension=gd` and `extension=mbstring` in `php.ini`, then restart Apache before exporting PDFs. The API will retain the printable HTML fallback if Composer dependencies are unavailable.

### API
| Method | Endpoint | Cost |
|--------|----------|------|
| POST | /ocr | 35 credits |
| POST | /papers/{id}/export | Free |

## Phase 4 – School admin, Pro Plus, polish

### Run migration (existing databases)
```bash
mysql -u root -p question_bank < backend/sql/phase4_migrate.sql
```
(Ignore “duplicate column” errors if columns already exist.)

### Features
- **School admin** (Account): list/create/remove teachers, school name/address, share School ID
- **Pro Plus** (individuals only): multi exam headers; choose Pro vs Pro Plus when buying packs
- **Feature gating**: OCR blocked when school pool empty/expired
- **Paper builder**: pick saved header for individuals
- Subscription packs unlock offline + Pro status

## Phase 5 – Hardening & launch

- Security headers + production JWT check
- Rate limits: login, register, OCR, PDF export
- Usage logging (`usage_logs`) for OCR/PDF
- Hide DB error details when `APP_ENV=production`
- Offline connectivity banner in the app
- Use the deployment checklist below before going live.

```bash
mysql -u root -p question_bank < backend/sql/phase5_migrate.sql
```

## Phase 6 - PDF output settings

Existing databases should run the PDF settings migration once:
```bash
mysql -u root -p question_bank < backend/sql/phase6_migrate.sql
```

Users can choose the PDF font size and font family from Account. The selection applies to both mPDF exports and the printable HTML fallback.

Phase 7 adds the per-question mark visibility setting:
```bash
mysql -u root -p question_bank < backend/sql/phase7_migrate.sql
```

## Deployment

The recommended production setup is split hosting:

- Deploy this frontend repository as a static site on Netlify.
- Deploy the PHP API repository to DirectAdmin under an API domain such as `https://api.example.com`.
- Host MySQL on the API server and keep its credentials in the API environment only.

Before deploying, set the public API URL in `index.html` before `js/app.js` loads:

```html
<script>
   window.GOODSCORES_API_BASE = 'https://api.example.com';
</script>
<script type="module" src="js/app.js"></script>
```

The frontend must not contain database credentials, JWT secrets, Paystack secret keys, AI keys, or `.env` files. Keep `assets/tinymce/` because the editor is self-hosted and required at runtime.

### Netlify settings

- Publish the frontend repository root.
- No build command is required for the current static frontend.
- Do not deploy `backend/`, PHP files, Composer files, or backend storage data with the frontend.
- Enable HTTPS and configure the exact frontend origin in the API's `CORS_ALLOWED_ORIGINS` setting.

### Post-deployment checks

1. Open the deployed frontend and confirm the browser can call the API.
2. Confirm `GET /health` returns a connected database status.
3. Register or log in and create a test question.
4. Test paper creation and browser PDF printing.
5. Test OCR only when the API key is configured server-side.

For API environment variables, database setup, PHP requirements, permissions, and backend smoke tests, see `backend/README.md`.
