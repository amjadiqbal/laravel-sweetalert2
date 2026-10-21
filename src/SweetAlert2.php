<?php

namespace AmjadIqbal\SweetAlert2;

use Illuminate\Session\Store;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Js;

/**
 * Queues popups in the session (flashed, so they show on the next page that
 * renders <x-sweetalert2 />) and prints the SweetAlert2 assets.
 *
 * Bound as a scoped singleton, so state resets per request (and per Octane request).
 */
class SweetAlert2
{
    public const SESSION_KEY = 'laravel-sweetalert2.alerts';

    /** @var list<Alert> */
    protected array $alerts = [];

    /**
     * Alerts already flashed by an earlier request and not shown yet.
     *
     * @var list<array<string, mixed>>|null
     */
    protected ?array $carried = null;

    protected bool $assetsRendered = false;

    /**
     * @param  array<string, mixed>|string|null  $title  a title, or SweetAlert2 options
     */
    public function fire(array|string|null $title = null, ?string $text = null, ?string $icon = null): Alert
    {
        $options = is_array($title) ? $title : array_filter(
            ['title' => $title, 'text' => $text, 'icon' => $icon],
            fn ($value) => $value !== null,
        );

        $alert = new Alert($options, $this);
        $this->alerts[] = $alert;
        $this->persist();

        return $alert;
    }

    public function success(string $title, ?string $text = null): Alert
    {
        return $this->fire($title, $text, 'success');
    }

    public function error(string $title, ?string $text = null): Alert
    {
        return $this->fire($title, $text, 'error');
    }

    public function warning(string $title, ?string $text = null): Alert
    {
        return $this->fire($title, $text, 'warning');
    }

    public function info(string $title, ?string $text = null): Alert
    {
        return $this->fire($title, $text, 'info');
    }

    public function question(string $title, ?string $text = null): Alert
    {
        return $this->fire($title, $text, 'question');
    }

    /**
     * A small self-closing notification; see config('sweetalert2.toast').
     */
    public function toast(string $title, ?string $icon = 'success'): Alert
    {
        return $this->fire(array_filter(['toast' => true, 'title' => $title, 'icon' => $icon]));
    }

    /**
     * The popups waiting to be shown, as Swal.fire() options.
     *
     * @return list<array<string, mixed>>
     */
    public function queued(): array
    {
        return [
            ...$this->carried(),
            ...array_map(fn (Alert $alert) => $alert->toArray(), $this->alerts),
        ];
    }

    /**
     * Drop every queued popup.
     */
    public function clear(): void
    {
        $this->alerts = [];
        $this->carried = [];
        $this->session()?->forget(self::SESSION_KEY);
    }

    /**
     * Write the queue to the session. Called by Alert on every change.
     */
    public function persist(): void
    {
        $this->session()?->flash(self::SESSION_KEY, $this->queued());
    }

    /**
     * The SweetAlert2 script and the package runtime, printed once per request.
     */
    public function assets(): HtmlString
    {
        if ($this->assetsRendered) {
            return new HtmlString('');
        }

        $this->assetsRendered = true;

        $assets = (array) config('sweetalert2.assets', []);
        $nonce = $this->nonceAttribute();
        $html = '';

        if (($assets['cdn'] ?? true) && ! empty($assets['js'])) {
            $integrity = is_string($assets['js_integrity'] ?? null) && $assets['js_integrity'] !== ''
                ? ' integrity="'.e($assets['js_integrity']).'" crossorigin="anonymous"'
                : '';

            $html .= '<script src="'.e($assets['js']).'"'.$integrity.$nonce.' data-navigate-once></script>'."\n";
        }

        $runtime = (string) file_get_contents(__DIR__.'/../resources/js/runtime.js');

        $html .= '<script'.$nonce.' data-navigate-once>'.$runtime.'</script>'."\n";

        return new HtmlString($html);
    }

    /**
     * Assets, settings and the queued popups. Put it once in your layout.
     * Showing the popups removes them from the session.
     */
    public function render(): HtmlString
    {
        $alerts = $this->queued();
        $this->clear();

        $settings = [
            'defaults' => (object) config('sweetalert2.defaults', []),
            'toast' => (object) config('sweetalert2.toast', []),
            'confirm' => (object) Alert::normalize((array) config('sweetalert2.confirm', [])),
        ];

        return new HtmlString(
            $this->assets()
            .'<script'.$this->nonceAttribute().'>window.LaravelSweetAlert2 && window.LaravelSweetAlert2'
            .'.configure('.Js::from($settings).')'
            .'.queue('.Js::from($alerts).');</script>'."\n"
        );
    }

    public function assetsRendered(): bool
    {
        return $this->assetsRendered;
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function carried(): array
    {
        if ($this->carried === null) {
            $previous = $this->session()?->get(self::SESSION_KEY);
            $this->carried = is_array($previous) ? array_values($previous) : [];
        }

        return $this->carried;
    }

    protected function session(): ?Store
    {
        return app()->bound('session.store') ? app('session.store') : null;
    }

    protected function nonceAttribute(): string
    {
        $nonce = Vite::cspNonce();

        return $nonce ? ' nonce="'.e($nonce).'"' : '';
    }
}
