<?php

namespace AmjadIqbal\SweetAlert2;

/**
 * One popup. Built fluently; every change is written back to the session, so
 * SweetAlert2::success('Saved')->timer(2000) needs no closing call.
 */
class Alert
{
    /**
     * SweetAlert2 inserts these as HTML, so plain strings are escaped.
     */
    public const ESCAPED_OPTIONS = ['confirmButtonText', 'denyButtonText', 'cancelButtonText', 'footer'];

    /** @var array<string, mixed> */
    protected array $options = [];

    /** @var array<string, mixed> */
    protected array $raw = [];

    /**
     * @param  array<string, mixed>  $options
     */
    public function __construct(array $options = [], protected ?SweetAlert2 $manager = null)
    {
        $this->options = $options;
    }

    public function title(string $title): static
    {
        return $this->option('title', $title);
    }

    public function text(string $text): static
    {
        return $this->option('text', $text);
    }

    /**
     * Trusted HTML only: SweetAlert2 does not sanitize it. Never pass user input.
     */
    public function html(string $html): static
    {
        return $this->raw(['html' => $html]);
    }

    /**
     * @param  'success'|'error'|'warning'|'info'|'question'  $icon
     */
    public function icon(string $icon): static
    {
        return $this->option('icon', $icon);
    }

    public function footer(string $text): static
    {
        return $this->option('footer', $text);
    }

    /**
     * Close automatically after $milliseconds, optionally with a progress bar.
     */
    public function timer(int $milliseconds, bool $progressBar = true): static
    {
        return $this->options(['timer' => $milliseconds, 'timerProgressBar' => $progressBar]);
    }

    /**
     * @param  'top'|'top-start'|'top-end'|'center'|'center-start'|'center-end'|'bottom'|'bottom-start'|'bottom-end'  $position
     */
    public function position(string $position): static
    {
        return $this->option('position', $position);
    }

    public function toast(bool $toast = true): static
    {
        return $this->option('toast', $toast);
    }

    public function confirmButton(string $text): static
    {
        return $this->options(['showConfirmButton' => true, 'confirmButtonText' => $text]);
    }

    public function cancelButton(string $text = 'Cancel'): static
    {
        return $this->options(['showCancelButton' => true, 'cancelButtonText' => $text]);
    }

    public function withoutConfirmButton(): static
    {
        return $this->option('showConfirmButton', false);
    }

    public function closeButton(bool $show = true): static
    {
        return $this->option('showCloseButton', $show);
    }

    /**
     * @param  'light'|'dark'|'auto'|string  $theme
     */
    public function theme(string $theme): static
    {
        return $this->option('theme', $theme);
    }

    public function width(int|string $width): static
    {
        return $this->option('width', $width);
    }

    /**
     * Any SweetAlert2 option. Titles become titleText and HTML-rendered
     * strings are escaped; use raw() for trusted HTML.
     */
    public function option(string $key, mixed $value): static
    {
        return $this->options([$key => $value]);
    }

    /**
     * @param  array<string, mixed>  $options
     */
    public function options(array $options): static
    {
        $this->options = array_merge($this->options, $options);

        return $this->persist();
    }

    /**
     * Options passed to Swal.fire() exactly as given, without escaping.
     * Trusted values only.
     *
     * @param  array<string, mixed>  $options
     */
    public function raw(array $options): static
    {
        $this->raw = array_merge($this->raw, $options);

        return $this->persist();
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_merge(static::normalize($this->options), $this->raw);
    }

    /**
     * Make options safe to hand to Swal.fire(): a string title is sent as
     * titleText (rendered as text) and HTML-rendered labels are escaped.
     *
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public static function normalize(array $options): array
    {
        if (isset($options['title']) && is_string($options['title'])) {
            $options['titleText'] ??= $options['title'];
            unset($options['title']);
        }

        foreach (self::ESCAPED_OPTIONS as $key) {
            if (isset($options[$key]) && is_string($options[$key])) {
                $options[$key] = e($options[$key]);
            }
        }

        return $options;
    }

    protected function persist(): static
    {
        $this->manager?->persist();

        return $this;
    }
}
