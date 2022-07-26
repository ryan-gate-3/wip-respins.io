<?php

namespace Respins\BaseFunctions\Controllers\Livewire;

use Livewire\Component;
use Respins\BaseFunctions\BaseFunctions;

class NavigationBar extends Component
{
    /**
     * The component's listeners.
     *
     * @var array
     */
    protected $listeners = [
        'refresh-navigation-menu' => '$refresh',
    ];

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {

        return view('respins::livewire.navigation');
    }
}
