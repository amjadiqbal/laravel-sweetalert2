<?php

use AmjadIqbal\SweetAlert2\Livewire\SweetAlerts;

class FakeLivewireComponent
{
    use SweetAlerts;

    /** @var list<array{0: string, 1: array<string, mixed>}> */
    public array $dispatched = [];

    public function dispatch(string $event, mixed ...$params): void
    {
        $this->dispatched[] = [$event, $params];
    }
}

it('dispatches a sweetalert2 browser event with safe named params', function () {
    $component = new FakeLivewireComponent;
    $component->swal('Saved', 'Done.', 'success');

    expect($component->dispatched)->toEqual([
        ['sweetalert2', ['text' => 'Done.', 'icon' => 'success', 'titleText' => 'Saved']],
    ]);
});

it('dispatches toasts and raw option arrays', function () {
    $component = new FakeLivewireComponent;
    $component->swalToast('Copied', 'info', ['timer' => 1000]);
    $component->swal(['title' => 'Hi', 'confirmButtonText' => '<b>x</b>']);

    expect($component->dispatched[0][1])->toEqual(['toast' => true, 'icon' => 'info', 'timer' => 1000, 'titleText' => 'Copied'])
        ->and($component->dispatched[1][1])->toEqual(['titleText' => 'Hi', 'confirmButtonText' => '&lt;b&gt;x&lt;/b&gt;']);
});
