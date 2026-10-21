<?php

namespace AmjadIqbal\SweetAlert2\Facades;

use AmjadIqbal\SweetAlert2\Alert;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Alert fire(array<string, mixed>|string|null $title = null, ?string $text = null, ?string $icon = null)
 * @method static Alert success(string $title, ?string $text = null)
 * @method static Alert error(string $title, ?string $text = null)
 * @method static Alert warning(string $title, ?string $text = null)
 * @method static Alert info(string $title, ?string $text = null)
 * @method static Alert question(string $title, ?string $text = null)
 * @method static Alert toast(string $title, ?string $icon = 'success')
 * @method static list<array<string, mixed>> queued()
 * @method static void clear()
 * @method static \Illuminate\Support\HtmlString assets()
 * @method static \Illuminate\Support\HtmlString render()
 *
 * @see \AmjadIqbal\SweetAlert2\SweetAlert2
 */
class SweetAlert2 extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \AmjadIqbal\SweetAlert2\SweetAlert2::class;
    }
}
