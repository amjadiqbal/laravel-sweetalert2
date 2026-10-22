<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SweetAlert2 assets
    |--------------------------------------------------------------------------
    |
    | <x-sweetalert2 /> (or @sweetalert2) prints this script tag plus the
    | package's small runtime, once per request. The default loads
    | SweetAlert2 11.26.25 from jsDelivr with a Subresource Integrity hash.
    | The "all" build injects its own CSS, so no stylesheet tag is needed.
    |
    | Bundling SweetAlert2 with Vite instead? Set "cdn" to false (the runtime
    | is still printed), `npm i sweetalert2`, and in resources/js/app.js:
    |
    |     import Swal from 'sweetalert2';
    |     window.Swal = Swal;
    |
    | If you change the URL, update its integrity hash or set it to null.
    |
    */

    'assets' => [
        'cdn' => true,
        'js' => 'https://cdn.jsdelivr.net/npm/sweetalert2@11.26.25/dist/sweetalert2.all.min.js',
        'js_integrity' => 'sha384-nLoOnA/BDh8A/jxqtckg4DumuCGOBYUnNJLZdQz/zfYNp3wcjGSoWTAzgko06G/2',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default SweetAlert2 options
    |--------------------------------------------------------------------------
    |
    | Applied to every popup, then overridden by the popup's own options.
    | Keys are SweetAlert2 option names: https://sweetalert2.github.io/#configuration
    | For example 'theme' => 'auto' follows the visitor's light/dark setting.
    |
    */

    'defaults' => [
        //
    ],

    /*
    |--------------------------------------------------------------------------
    | Toast options
    |--------------------------------------------------------------------------
    |
    | Merged over the defaults for every toast (SweetAlert2::toast(), or any
    | popup with 'toast' => true).
    |
    */

    'toast' => [
        'toast' => true,
        'position' => 'top-end',
        'timer' => 3000,
        'timerProgressBar' => true,
        'showConfirmButton' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Confirm dialog options
    |--------------------------------------------------------------------------
    |
    | Used by elements with a data-swal-confirm attribute (delete buttons,
    | forms, links), Livewire's wire:confirm, LaravelSwal.confirm() and
    | Alpine's $swal.confirm(). A data-swal-confirm element can override the
    | title, text, icon and button labels with data-swal-* attributes.
    |
    */

    'confirm' => [
        'icon' => 'warning',
        'showCancelButton' => true,
        'confirmButtonText' => 'Yes',
        'cancelButtonText' => 'Cancel',
        'focusCancel' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Prompt dialog options
    |--------------------------------------------------------------------------
    |
    | Used by LaravelSwal.prompt(), Alpine's $swal.prompt() and Livewire's
    | wire:confirm.prompt.
    |
    */

    'prompt' => [
        'input' => 'text',
        'showCancelButton' => true,
        'confirmButtonText' => 'OK',
        'cancelButtonText' => 'Cancel',
    ],

    /*
    |--------------------------------------------------------------------------
    | Style Livewire's wire:confirm
    |--------------------------------------------------------------------------
    |
    | When true, every wire:confirm and wire:confirm.prompt (Livewire 3 and 4)
    | opens a SweetAlert2 dialog instead of the browser's confirm()/prompt(),
    | with no markup changes. Opt a single element (or a whole section) out
    | with a data-swal-native attribute.
    |
    */

    'livewire_confirm' => true,

];
