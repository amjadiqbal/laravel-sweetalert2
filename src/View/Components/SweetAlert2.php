<?php

namespace AmjadIqbal\SweetAlert2\View\Components;

use AmjadIqbal\SweetAlert2\SweetAlert2 as Manager;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * <x-sweetalert2 />: put it once in your layout, before </body>.
 */
class SweetAlert2 extends Component
{
    public function html(): string
    {
        return (string) app(Manager::class)->render();
    }

    public function render(): View
    {
        return view('sweetalert2::components.sweetalert2');
    }
}
