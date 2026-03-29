# File-backed Audit Workflow

Use this path when browser automation or the Playwright bridge is unavailable on the local machine.

Run:

```powershell
pwsh -NoProfile -File .\scripts\Update-LiveAudit.ps1
```

Outputs:

- `docs/audits/live-home.html`
- `docs/audits/live-shop.html`
- `docs/exports/live-audit-summary.json`

The script fetches live HTML over HTTP, writes repeatable local captures, and records a small JSON summary for quick diffing and review.
