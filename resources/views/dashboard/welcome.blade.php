@extends('layouts.base')

@section('content')
    <div class="row">
        <div class="col-sm-3">
            <div class="card prod-p-card bg-behance background-pattern-white">
                <div class="card-body">
                    <div class="row align-items-center m-b-0">
                        <div class="col">
                            <h6 class="m-b-5 text-white">Registered Network</h6>
                            <h3 class="m-b-0 text-white">{{ $latest->network ?? 'Unregistered' }}</h3>
                        </div>
                        <div class="col-auto">
                            <i class="material-icons-two-tone text-white">person</i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card prod-p-card bg-primary background-pattern-white">
                <div class="card-body">
                    <div class="row align-items-center m-b-0">
                        <div class="col">
                            <h6 class="m-b-5 text-white">Geo Location</h6>
                            <h3 class="m-b-0 text-white">{{ $latest->geoloc ?? 'No Location' }}</h3>
                        </div>
                        <div class="col-auto">
                            <i class="material-icons-two-tone text-white">vpn_key</i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card prod-p-card bg-primary background-pattern-white">
                <div class="card-body">
                    <div class="row align-items-center m-b-0">
                        <div class="col">
                            <h6 class="m-b-5 text-white">Signal Strength</h6>
                            <h3 class="m-b-0 text-white">{{ $latest->rssi ?? 0 }} dBm</h3>
                        </div>
                        <div class="col-auto">
                            <i class="material-icons-two-tone text-white">devices_other</i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <livewire:lte-map>
        </div>
    </div>
@endsection