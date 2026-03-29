# Codex Audit — 2026-03-29

## Scope

Live storefront audit of `https://www.crystalcentury.com/` and the tracked custom code pulled from the live host.

## Findings Claude left behind

1. The public site still exposed the old brand in schema and metadata.
   - `blogname` was still `Premier Trophy`
   - Yoast schema rendered `Premier Trophy` / `Premier Trophy Limited`
   - `og:site_name` still showed `Premier Trophy`

2. A legacy must-use plugin was injecting the wrong footer and duplicate schema on every page.
   - File: `mu-plugins/arby-rollout-core.php`
   - Added English CTA copy to the Chinese storefront
   - Added checkout language that contradicts quote-only catalogue mode
   - Linked to non-canonical legal URLs like `/legal/privacy-policy.html`

3. Performance tweaks had become storefront behavior overrides.
   - File: `mu-plugins/perf-tweaks.php`
   - Hard-capped every product query to 8 via `pre_get_posts`
   - Injected an always-visible floating language switcher, likely causing duplicated language rows

4. Frontend strings still leaked through in English on the zh-Hant storefront.
   - Search placeholder: `Type to start searching...`
   - Floating CTA button: `Order Now!`

5. The shop experience was not consistently localized.
   - zh-Hant shop page still rendered `Shop` as the visible heading and breadcrumb target
   - zh-Hant shop metadata still used English copy

## Codex response

The repo baseline and fixes in this repo do four things:

1. Remove the legacy rollout mu-plugin output entirely.
2. Narrow perf overrides to safe concerns only.
3. Move branding, metadata, and storefront localization into tracked child-theme code.
4. Add repeatable pull/deploy scripts so future work is file-based rather than one-off live edits.
