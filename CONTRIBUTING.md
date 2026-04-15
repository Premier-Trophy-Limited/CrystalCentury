# Contributing to CrystalCentury

This repo contains custom WordPress/WooCommerce code for crystalcentury.com.

## Setup

Requires a local WordPress environment. See .env.example for required constants.

## Standards

- PHP 8.1+ compatible
- Follow WordPress Coding Standards
- No direct wpdb queries — use WP_Query and WP abstractions
- Prefix all custom functions and hooks with `cc_`
- Test on staging before pushing to `main`

## Branching

- `feat/<description>` — new functionality
- `fix/<description>` — bug fixes
- `hotfix/<description>` — urgent production fixes

## Commit format

```feat(shop): add custom order confirmation email template
fix(cart): resolve quantity update on mobile
hotfix(checkout): patch payment gateway redirect loop```