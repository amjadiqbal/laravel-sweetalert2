<?php

use AmjadIqbal\SweetAlert2\Alert;
use AmjadIqbal\SweetAlert2\SweetAlert2;

if (! function_exists('swal')) {
    /**
     * swal() returns the SweetAlert2 manager; swal('Saved', 'All good.', 'success') queues a popup.
     *
     * @param  array<string, mixed>|string|null  $title
     */
    function swal(array|string|null $title = null, ?string $text = null, ?string $icon = null): SweetAlert2|Alert
    {
        $manager = app(SweetAlert2::class);

        return $title === null ? $manager : $manager->fire($title, $text, $icon);
    }
}
