<?php

namespace Respins\BaseFunctions\Controllers\Livewire;

use Livewire\Component;
use Respins\BaseFunctions\BaseFunctions;
use Respins\BaseFunctions\Models\DataLogger;

class DataLoggerViewer extends Component
{

    public $data;

    /**
     * The component's listeners.
     *
     * @var array
     */
    protected $listeners = [
        'update_data' => '$refresh',
    ];
 
    public function boot()
    {
        self::retrieve_data();
    }

    public function dispatcher()
    {
        $this->dispatchBrowserEvent('update_data', ['data' => $this->retrieve_data()]);
    }

    public function retrieve_data()
    {
        $this->data = DataLogger::all();
        return DataLogger::all();
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $data = DataLogger::all();
        return view('respins::livewire.datalogger-viewer', ['data' => $data]);
    }
}
