# CrystalCentury

Version-controlled custom code for [crystalcentury.com](https://crystalcentury.com) — a WordPress/WooCommerce trophy and recognition products storefront.

Tracks only the parts of the stack that are customised and benefit from version control. WordPress core, uploads, and the live database are excluded.

## What's tracked

| Directory | Contents |
|-----------|----------|
| `child-theme/` | `hello-elementor-child` custom styles and hooks |
| `mu-plugins/` | Must-use plugins affecting storefront behaviour |
| `scripts/` | Pull/deploy helpers for syncing code with the live host |
| `docs/audits/` | Reproducible audit notes and captured HTML evidence |
| `docs/exports/` | Lightweight option and identity exports from the live site |

## Sync workflow

```powershell
# Pull current live custom code
pwsh -NoProfile -File .\scripts\Pull-LiveCustomCode.ps1

# Deploy tracked code back to the live site
pwsh -NoProfile -File .\scripts\Deploy-LiveCustomCode.ps1
```

## Operating boundaries

Changes are scoped to child theme, must-use plugins, WP-CLI verification, and cache operations. Root config, Nginx/OpenLiteSpeed topology, and server internals are out of scope.