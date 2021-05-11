<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Services\GeoJsonEncoder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LocationController extends Controller
{
	public function lat($lat)
	{
		$lat = str_replace('"', '', $lat);
		$lat = ((float) $lat);
		return (float) substr($lat, 0, 2) + (float) (substr($lat, 2, 9)) / 60;
	}

	public function lng($lng)
	{
		$lng = str_replace('"', '', $lng);
		$lng = ((float) $lng);
		return (float) substr($lng, 0, 3) + (float) (substr($lng, 3, 10)) / 60;
	}

	public function nmea_convert($gnss): string
	{
		return "" . $this->lat($gnss[0]) . "," . $this->lng($gnss[2]);
	}

    public function track(Request $request)
	{
		$data = $request->validate([
			'rssi' => 'required',
			'gnss' => 'required',
			'network' => 'required',
		]);
		Log::info('REQUEST DATA', ['request' => $data]);

		$data['rssi'] = -113 + $data['rssi'] * 2;
		if($data['network'] == '0')
		{
			$data['network'] = 'Not Registered';
		}
		if($data['network'] != '0')
		{
			$a = explode(',', $data['network'][2]);
			$data['network'] = explode(' ', $a[0])[0];
		}

		$data['geoloc'] = $this->nmea_convert($data['gnss']);

		Log::info('DUMP', [
			'rssi' => $data['rssi'],
			'network' => $data['network'],
			'geoloc' => $data['geoloc'],
			'gnss' => $data['gnss'],
		]);

		return Location::create([
			'rssi' => $data['rssi'],
			'gnss' => json_encode($data['gnss']),
			'geoloc' => $data['geoloc'],
			'network' => $data['network']
		]);
	}

	public function ping()
	{
		$a = Location::all()->map(function ($item, $key) {
			return ['rssi' => $item->rssi, 'network' => $item->network, 'geoloc' => $item->geoloc];
		});
		$data = new GeoJsonEncoder($a);
		return response()->json($data->generate());
	}
}
