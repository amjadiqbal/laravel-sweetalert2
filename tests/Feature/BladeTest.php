<?php

use AmjadIqbal\SweetAlert2\Facades\SweetAlert2;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Vite;

it('prints SweetAlert2 11 from the CDN with an integrity hash, and the runtime', function () {
    $html = Blade::render('<x-sweetalert2 />');

    expect($html)
        ->toContain('https://cdn.jsdelivr.net/npm/sweetalert2@11.26.25/dist/sweetalert2.all.min.js')
        ->toContain('integrity="sha384-nLoOnA/BDh8A/jxqtckg4DumuCGOBYUnNJLZdQz/zfYNp3wcjGSoWTAzgko06G/2"')
        ->toContain('crossorigin="anonymous"')
        ->toContain('data-navigate-once')
        ->toContain('window.LaravelSweetAlert2 = api')
        ->not->toContain('sweetalert.all.js');
});

it('renders queued popups and removes them from the session', function () {
    SweetAlert2::error('Failed', 'Try again.');

    $html = Blade::render('<x-sweetalert2 />');

    expect(queuedIn($html))->toEqual([['text' => 'Try again.', 'icon' => 'error', 'titleText' => 'Failed']])
        ->and(SweetAlert2::queued())->toEqual([]);
});

it('works as the @sweetalert2 directive and prints the assets once', function () {
    $html = Blade::render('@sweetalert2 <x-sweetalert2 />');

    expect(substr_count($html, 'sweetalert2.all.min.js'))->toBe(1)
        ->and(substr_count($html, '.queue('))->toBe(2);
});

it('passes defaults, toast and escaped confirm settings to the runtime', function () {
    config()->set('sweetalert2.defaults', ['theme' => 'auto']);
    config()->set('sweetalert2.confirm.confirmButtonText', '<b>Delete</b>');

    $settings = settingsIn(Blade::render('<x-sweetalert2 />'));

    expect($settings['defaults'])->toEqual(['theme' => 'auto'])
        ->and($settings['toast'])->toMatchArray(['toast' => true, 'position' => 'top-end', 'timer' => 3000])
        ->and($settings['confirm']['confirmButtonText'])->toBe('&lt;b&gt;Delete&lt;/b&gt;')
        ->and($settings['confirm']['showCancelButton'])->toBeTrue();
});

it('sends empty defaults as an object, not an array', function () {
    preg_match("/\\.configure\\(JSON\\.parse\\('(.*?)'\\)\\)/", Blade::render('<x-sweetalert2 />'), $m);

    expect(json_decode('"'.$m[1].'"'))->toContain('"defaults":{}');
});

it('skips the CDN tag but keeps the runtime when cdn is false', function () {
    config()->set('sweetalert2.assets.cdn', false);

    expect(Blade::render('<x-sweetalert2 />'))->not->toContain('jsdelivr')->toContain('window.LaravelSweetAlert2 = api');
});

it('omits integrity when the hash is null', function () {
    config()->set('sweetalert2.assets.js_integrity', null);

    expect(Blade::render('<x-sweetalert2 />'))->not->toContain('integrity=');
});

it('adds the Vite CSP nonce to every script tag', function () {
    Vite::useCspNonce('abc123');

    $html = Blade::render('<x-sweetalert2 />');

    expect(substr_count($html, '<script'))->toBe(3)
        ->and(substr_count($html, 'nonce="abc123"'))->toBe(3);
});

it('cannot be broken out of the script tag by popup text', function () {
    SweetAlert2::warning('</script><script>alert(1)</script>');

    expect(Blade::render('<x-sweetalert2 />'))->not->toContain('</script><script>alert(1)');
});

it('publishes the config and views', function () {
    Artisan::call('vendor:publish', ['--tag' => 'sweetalert2-config', '--force' => true]);

    expect(config_path('sweetalert2.php'))->toBeFile();

    unlink(config_path('sweetalert2.php'));
});

it('adds a passive entry to artisan about', function () {
    Artisan::call('about', ['--only' => 'sweetalert2']);

    expect(Artisan::output())
        ->toContain('SweetAlert2')
        ->toContain('utm_campaign=laravel-sweetalert2');
});
