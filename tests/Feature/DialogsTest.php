<?php

use Illuminate\Support\Facades\Blade;

it('styles Livewire wire:confirm by default', function () {
    $settings = settingsIn(Blade::render('<x-sweetalert2 />'));

    expect($settings['livewireConfirm'])->toBeTrue();
});

it('can leave wire:confirm to the browser', function () {
    config()->set('sweetalert2.livewire_confirm', false);

    expect(settingsIn(Blade::render('<x-sweetalert2 />'))['livewireConfirm'])->toBeFalse();
});

it('passes escaped prompt settings to the runtime', function () {
    config()->set('sweetalert2.prompt.confirmButtonText', '<b>Go</b>');

    $prompt = settingsIn(Blade::render('<x-sweetalert2 />'))['prompt'];

    expect($prompt)->toMatchArray(['input' => 'text', 'showCancelButton' => true, 'cancelButtonText' => 'Cancel'])
        ->and($prompt['confirmButtonText'])->toBe('&lt;b&gt;Go&lt;/b&gt;');
});

it('ships the promise dialogs, the Alpine magic and the wire:confirm hook in the runtime', function () {
    expect(Blade::render('<x-sweetalert2 />'))
        ->toContain('window.LaravelSwal = api')
        ->toContain("Alpine.magic('swal'")
        ->toContain("Livewire.hook('directive.init'")
        ->toContain('el.__livewire_confirm = el.__laravelSweetAlert2Wrapper')
        ->toContain("console.warn('Livewire: Must provide expectation with wire:confirm.prompt')")
        ->toContain("'data-swal-native'");
});

it('ships TypeScript declarations for the browser API', function () {
    expect(file_get_contents(__DIR__.'/../../resources/types/laravel-sweetalert2.d.ts'))
        ->toContain('confirm(message?: SwalMessage, options?: SwalOptions): Promise<boolean>')
        ->toContain('Promise<string | null>');
});
