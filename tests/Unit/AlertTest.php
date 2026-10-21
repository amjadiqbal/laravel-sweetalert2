<?php

use AmjadIqbal\SweetAlert2\Alert;

it('sends a string title as titleText so it renders as text', function () {
    expect(Alert::normalize(['title' => '<img src=x onerror=alert(1)>']))
        ->toEqual(['titleText' => '<img src=x onerror=alert(1)>']);
});

it('keeps an explicit titleText', function () {
    expect(Alert::normalize(['title' => 'a', 'titleText' => 'b']))->toEqual(['titleText' => 'b']);
});

it('escapes the options SweetAlert2 inserts as HTML', function () {
    $options = Alert::normalize([
        'confirmButtonText' => '<b>Yes</b>',
        'cancelButtonText' => 'No & back',
        'denyButtonText' => '"x"',
        'footer' => '<script>',
        'text' => '<i>plain text is safe</i>',
    ]);

    expect($options['confirmButtonText'])->toBe('&lt;b&gt;Yes&lt;/b&gt;')
        ->and($options['cancelButtonText'])->toBe('No &amp; back')
        ->and($options['denyButtonText'])->toBe('&quot;x&quot;')
        ->and($options['footer'])->toBe('&lt;script&gt;')
        ->and($options['text'])->toBe('<i>plain text is safe</i>');
});

it('builds options fluently', function () {
    $alert = (new Alert)
        ->title('Saved')
        ->text('All good')
        ->icon('success')
        ->timer(2000)
        ->position('bottom-end')
        ->confirmButton('Great')
        ->cancelButton()
        ->closeButton()
        ->theme('dark')
        ->width(400)
        ->footer('Footer')
        ->option('allowOutsideClick', false);

    expect($alert->toArray())->toEqual([
        'text' => 'All good',
        'icon' => 'success',
        'timer' => 2000,
        'timerProgressBar' => true,
        'position' => 'bottom-end',
        'showConfirmButton' => true,
        'confirmButtonText' => 'Great',
        'showCancelButton' => true,
        'cancelButtonText' => 'Cancel',
        'showCloseButton' => true,
        'theme' => 'dark',
        'width' => 400,
        'footer' => 'Footer',
        'allowOutsideClick' => false,
        'titleText' => 'Saved',
    ]);
});

it('passes html() and raw() through untouched', function () {
    $alert = (new Alert(['title' => 'T']))->html('<b>bold</b>')->raw(['title' => '<em>T</em>']);

    expect($alert->toArray())->toEqual(['titleText' => 'T', 'html' => '<b>bold</b>', 'title' => '<em>T</em>']);
});
