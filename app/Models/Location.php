<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = ['rssi', 'gnss', 'network', 'geoloc'];

    public function getGeolocAttribute($value)
	{
		$geo = explode(',', $value);
		$geo[0] = round($geo[0], 5);
		$geo[1] = round($geo[1], 5);
		return implode(',', $geo);
	}
}
