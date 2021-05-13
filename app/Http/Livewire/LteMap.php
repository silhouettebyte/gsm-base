<?php

namespace App\Http\Livewire;

use App\Models\Location;
use Livewire\Component;

class LteMap extends Component
{
    public function render()
    {
    	$geoloc = Location::orderBy('created_at', 'desc')->first()->geoloc ?? '';
        return view('livewire.lte-map', ['geoloc' => $geoloc]);
    }
}
