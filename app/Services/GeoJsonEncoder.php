<?php


namespace App\Services;


use Illuminate\Support\Collection;

class GeoJsonEncoder
{
	/**
	 * @var int|mixed
	 */
	protected $limit;
	protected $data;

	public function __construct(Collection $data, $num = 50)
	{
		$this->data = $data;
		$this->limit = $num;
	}

	public function generate(): array
	{
		$base = [
			'type' => 'FeatureCollection',
			'features' => $this->prepare_array()
		];
		return $base;
	}

	private function prepare_array(): array
	{
		$data = $this->data->map(function ($item, $key) {
			return [
				'type' => 'Feature',
				'properties' => [
					'rssi' => $item['rssi'],
					'strength' => $this->rssi_level($item['rssi']),
					'network' => $item['network'],
				],
				'geometry' => [
					'type' => 'Point',
					'coordinates' => $this->parse_coordinates($item['geoloc'])
				]
			];
		});
		return $data->toArray();
	}

	private function parse_coordinates($geoloc)
	{
		$geoloc = explode(',', $geoloc);
		return array_reverse(array_map('floatval', $geoloc));
	}

	private function rssi_level($level)
	{
		if($level <= -95 && $level >= -109)
		{
			return "Marginal";
		}
		if($level <= -84 && $level >= -94)
		{
			return "Fair";
		}
		if($level <= -74 && $level >= -83)
		{
			return "Good";
		}
		if($level <= -53 && $level >= -73)
		{
			return "Excellent";
		}
		return "Dead";
	}
}