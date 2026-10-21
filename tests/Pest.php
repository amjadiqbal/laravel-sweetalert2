<?php

use AmjadIqbal\SweetAlert2\Tests\TestCase;

uses(TestCase::class)->in('Unit', 'Feature');

/**
 * The popups passed to LaravelSweetAlert2.queue() in rendered HTML.
 *
 * @return list<array<string, mixed>>
 */
function queuedIn(string $html): array
{
    if (str_contains($html, '.queue([])')) {
        return [];
    }

    preg_match("/\.queue\(JSON\.parse\('(.*?)'\)\)/", $html, $m);

    expect($m)->not->toBeEmpty();

    return json_decode(json_decode('"'.$m[1].'"'), true);
}

/**
 * The settings passed to LaravelSweetAlert2.configure() in rendered HTML.
 *
 * @return array<string, mixed>
 */
function settingsIn(string $html): array
{
    preg_match("/\.configure\(JSON\.parse\('(.*?)'\)\)/", $html, $m);

    expect($m)->not->toBeEmpty();

    return json_decode(json_decode('"'.$m[1].'"'), true);
}
