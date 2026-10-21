<?php

namespace AmjadIqbal\SweetAlert2\Livewire;

use AmjadIqbal\SweetAlert2\Alert;

/**
 * Use in a Livewire component to show a popup without a page load:
 * $this->swal('Saved', 'Your changes are live.', 'success') or $this->swalToast('Saved').
 *
 * Dispatches the "sweetalert2" browser event through the component's own
 * dispatch() (tested with Livewire 4); <x-sweetalert2 /> listens for it.
 */
trait SweetAlerts
{
    /**
     * @param  array<string, mixed>|string  $title  a title, or SweetAlert2 options
     */
    public function swal(array|string $title, ?string $text = null, ?string $icon = null): void
    {
        $options = is_array($title) ? $title : array_filter(
            ['title' => $title, 'text' => $text, 'icon' => $icon],
            fn ($value) => $value !== null,
        );

        $this->dispatch('sweetalert2', ...Alert::normalize($options));
    }

    /**
     * @param  array<string, mixed>  $options
     */
    public function swalToast(string $title, ?string $icon = 'success', array $options = []): void
    {
        $this->swal(array_filter(['toast' => true, 'title' => $title, 'icon' => $icon]) + $options);
    }
}
