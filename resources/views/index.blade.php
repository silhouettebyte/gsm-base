@extends('layouts.base')

@section('content')
    <div class="row">
        @foreach($devices as $device)
        <div class="col-4">
            <div class="card support-bar overflow-hidden">
                <div class="card-body pb-0">
                    <div class="row">
                        <div class="col">
                            <h2 class="m-0">{{ $device->identifier }}</h2>
                        </div>
                        <div class="col">
                            <form action="/dashboard/devices/{{ $device->identifier }}">
                                <button type="submit" class="btn btn-sm btn-outline-primary float-right">Open</button>
                            </form>
                        </div>
                    </div>
                    <span class="text-primary">Device Identifer</span>
                    <h2 class="mb-3 mt-3">{{ $device->status }}</h2>
                </div>
                @php
                    $datum = $device->telemetries()->orderByDesc('created_at')->first();
                    $card = ['IDLE' => 'blue', 'ACTIVE' => 'green', 'INACTIVE' => 'red'];
                @endphp
                <div class="card-footer border-0 bg-{{ $card[$device->status] }}-300 text-white background-pattern-white">
                    <div class="row text-center">
                        <div class="col">
                            <h4 class="m-0 text-white">{{ round($datum->bpm, 2) }}</h4>
                            <span>BPM</span>
                        </div>
                        <div class="col">
                            <h4 class="m-0 text-white">{{ round($datum->spo2, 2) }}</h4>
                            <span>SpO2</span>
                        </div>
                        <div class="col">
                            <h4 class="m-0 text-white">{{ round($datum->temperature, 2) }} C</h4>
                            <span>Temperature</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endsection


@section('js')

@endsection

@section('css')

@endsection