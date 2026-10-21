<?php

use AmjadIqbal\SweetAlert2\Facades\SweetAlert2;
use AmjadIqbal\SweetAlert2\SweetAlert2 as Manager;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;

it('flashes popups to the session, including later fluent changes', function () {
    SweetAlert2::success('Saved', 'Post published.')->timer(1500);
    SweetAlert2::toast('Copied', 'info');

    expect(session(Manager::SESSION_KEY))->toEqual([
        ['text' => 'Post published.', 'icon' => 'success', 'timer' => 1500, 'timerProgressBar' => true, 'titleText' => 'Saved'],
        ['toast' => true, 'icon' => 'info', 'titleText' => 'Copied'],
    ]);
});

it('has a shortcut for every icon', function (string $icon) {
    SweetAlert2::{$icon}('Title');

    expect(SweetAlert2::queued()[0]['icon'])->toBe($icon);
})->with(['success', 'error', 'warning', 'info', 'question']);

it('accepts raw options in fire()', function () {
    SweetAlert2::fire(['title' => 'Hi', 'confirmButtonText' => '<b>OK</b>', 'allowEscapeKey' => false]);

    expect(SweetAlert2::queued())->toEqual([
        ['allowEscapeKey' => false, 'titleText' => 'Hi', 'confirmButtonText' => '&lt;b&gt;OK&lt;/b&gt;'],
    ]);
});

it('works through the swal() helper', function () {
    expect(swal())->toBeInstanceOf(Manager::class);

    swal('Hello', 'World', 'info');

    expect(swal()->queued())->toEqual([['text' => 'World', 'icon' => 'info', 'titleText' => 'Hello']]);
});

it('shows a popup flashed before a redirect on the next page, once', function () {
    Route::middleware('web')->post('/save', function () {
        SweetAlert2::success('Saved', 'Done.');

        return redirect('/page');
    });
    Route::middleware('web')->get('/page', fn () => Blade::render('<html><body><x-sweetalert2 /></body></html>'));

    $this->post('/save')->assertRedirect('/page');

    $first = $this->get('/page')->assertOk()->getContent();
    expect(queuedIn($first))->toEqual([['text' => 'Done.', 'icon' => 'success', 'titleText' => 'Saved']]);

    $second = $this->get('/page')->assertOk()->getContent();
    expect(queuedIn($second))->toEqual([]);
});

it('keeps popups from an earlier request when another redirect adds more', function () {
    Route::middleware('web')->get('/one', function () {
        SweetAlert2::info('First');

        return redirect('/two');
    });
    Route::middleware('web')->get('/two', function () {
        SweetAlert2::info('Second');

        return redirect('/page');
    });
    Route::middleware('web')->get('/page', fn () => Blade::render('<x-sweetalert2 />'));

    $this->get('/one');
    $this->get('/two');

    expect(array_column(queuedIn($this->get('/page')->getContent()), 'titleText'))->toEqual(['First', 'Second']);
});

it('clears the queue', function () {
    SweetAlert2::info('Gone');
    SweetAlert2::clear();

    expect(SweetAlert2::queued())->toEqual([])
        ->and(session()->has(Manager::SESSION_KEY))->toBeFalse();
});
