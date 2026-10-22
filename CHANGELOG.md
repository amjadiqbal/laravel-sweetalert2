# Changelog

All notable changes to `amjadiqbal/laravel-sweetalert2` are documented here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and the project uses
[Semantic Versioning](https://semver.org/).

## [Unreleased]

## [1.1.0] - 2026-10-22

### Added
- Livewire's `wire:confirm` and `wire:confirm.prompt` open SweetAlert2 dialogs instead of the browser's
  `confirm()` / `prompt()`, with no markup changes. Livewire's rules are kept: "Are you sure?" when the message
  is empty, `\n` as a line break, `question|expected` type-to-confirm, and the console warning when the
  expectation is missing. Works on `wire:click` and `wire:submit`, on elements added later and after
  `wire:navigate`. Uses the `el.__livewire_confirm` callback, which is the same in Livewire 3.8 and 4.4.
- Opt-outs: `'livewire_confirm' => false` in the config, or `data-swal-native` on an element or a parent.
- Promise dialogs: `await $swal.confirm()`, `$swal.prompt()` and `$swal.alert()` in Alpine, and
  `LaravelSwal.confirm()`, `.prompt()` and `.alert()` in plain JavaScript. They resolve to `true`/`false`,
  the entered text or `null`, and nothing (once closed). Messages are text, not HTML, as in 1.0.
- `prompt` options in the config.
- TypeScript declarations in `resources/types/laravel-sweetalert2.d.ts`.

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

[Unreleased]: https://github.com/amjadiqbal/laravel-sweetalert2/compare/v1.1.0...HEAD
[1.1.0]: https://github.com/amjadiqbal/laravel-sweetalert2/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/amjadiqbal/laravel-sweetalert2/releases/tag/v1.0.0
