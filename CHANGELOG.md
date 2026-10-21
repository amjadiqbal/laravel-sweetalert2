# Changelog

All notable changes to `amjadiqbal/laravel-sweetalert2` are documented here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and the project uses
[Semantic Versioning](https://semver.org/).

## [Unreleased]

## [1.0.0] - 2026-10-21

First public release. A rewrite of the earlier, never-published `amjadiqbal/sweetalert2`.

### Added
- `SweetAlert2` facade, `swal()` helper and `success`, `error`, `warning`, `info`, `question`,
  `toast` and `fire` methods that flash popups to the session, with fluent options
  (`timer`, `position`, `confirmButton`, `cancelButton`, `footer`, `theme`, `width`, `option`, `raw`).
- Several popups per request, shown one after another; popups survive a chain of redirects.
- `<x-sweetalert2 />` component and `@sweetalert2` directive: SweetAlert2 11.26.25 from jsDelivr
  with Subresource Integrity, a small runtime, Vite CSP nonce on every script tag,
  `data-navigate-once` for Livewire `wire:navigate`.
- `data-swal-confirm` on any button, link or form (including Livewire `wire:click` and
  `wire:submit`) asks for confirmation before the action runs.
- `sweetalert2` browser event, plus a `SweetAlerts` trait for Livewire components
  (`$this->swal(...)`, `$this->swalToast(...)`).
- Config for default, toast and confirm options.
- `php artisan about` entry with the installed version and a support link.

### Security
- Titles are sent as `titleText` and button labels and footers are HTML-escaped, so user input
  in a popup cannot inject HTML. `html()` and `raw()` are the explicit opt-outs for trusted markup.

### Changed (compared with the unpublished `amjadiqbal/sweetalert2`)
- Requires PHP 8.3+ and Laravel 12 or 13.
- SweetAlert2 8.12.1 (2019, bundled) replaced by 11.26.25 from the CDN; `type` is now `icon`.
- Session key and container binding are namespaced (`laravel-sweetalert2.alerts`, `sweetalert2`),
  so it no longer collides with `amjadiqbal/alertify`. The global helper is `swal()`, not `alert()`.
- Removed the unused Bootstrap login/signup demo views.

[Unreleased]: https://github.com/amjadiqbal/laravel-sweetalert2/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/amjadiqbal/laravel-sweetalert2/releases/tag/v1.0.0
