GoodScores Product TODO
=======================

SEO and discoverability
-----------------------
- [ ] Review page titles, meta descriptions, canonical URLs, Open Graph tags, and social sharing images.
- [ ] Add a crawlable public landing page and useful sitemap/robots configuration without exposing authenticated app screens.
- [ ] Connect the Netlify domain to Google Search Console and monitor indexing, Core Web Vitals, and structured-data warnings.

OCR improvements
----------------
- [ ] Make credit deduction atomic so concurrent OCR requests cannot overspend credits.
- [ ] Consider an opt-in "Keep source image" checkbox; default should be off for privacy.
- [ ] Add OCR tests for image validation, empty output, MCQ parsing, credit deduction, and rate limits.

Ask AI: question drafting assistant
-----------------------------------
- [ ] Create a dedicated in-app usage guide covering the main GoodScores workflows.
- [ ] Add a newly rotated `OPENAI_API_KEY` to `backend/.env` after revoking the exposed key.
- [ ] Confirm the selected OpenAI model and review current API pricing before launch.
- [ ] Add curriculum topic selection and stronger Nigerian curriculum mappings.

Platform admin dashboard
------------------------
- [ ] Add a separate `platform_admin` / `super_admin` role; keep it distinct from `school_admin`.
- [ ] Build a protected admin dashboard with system health, API uptime, database, storage, and error-rate indicators.
- [ ] Add school management: inspect schools, subscriptions, credit balances, teacher counts, and school status.
- [ ] Add account controls: suspend/reactivate users, revoke sessions, and force password resets.
- [ ] Add credit and billing tools: review payments, inspect Paystack references, adjust credits, and record refunds or reversals.
- [ ] Add AI/OCR usage monitoring with request counts, estimated costs, failures, and configurable limits.
- [ ] Add feature flags and maintenance mode controls for temporarily disabling AI, OCR, payments, or new registrations.
- [ ] Add platform announcements and a basic support workflow.
- [ ] Add content tools to search, moderate, remove, and restore questions and generated papers.
- [ ] Add security monitoring for failed logins, rate-limit events, suspicious activity, and session revocation.
- [ ] Require two-factor authentication for platform admins.
- [ ] Record a complete audit entry for every sensitive admin action, including actor, target, reason, and result.
- [ ] Add admin tests for role isolation, school scoping, credit adjustments, session revocation, and audit coverage.

