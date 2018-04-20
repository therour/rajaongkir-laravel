<?php

namespace Therour\RajaOngkir\App;

use Therour\RajaOngkir\Facades\RajaOngkir as ROFacade;

class Province extends Base
{
	use UsingAttributes;

	protected $id;

	protected $name;

	public function __construct($id, $name)
	{
		$this->id = $id;

		$this->name = $name;

		parent::__construct();
	}

	public function getCities()
	{
		if (parent::$cache && is_null(cache('rajaongkir.cities'))) {
			cache(['rajaongkir.cities' => ROFacade::getCities()], parent::$cacheExpires);
		}

		if ($cached = $this->shouldGetFromCache('rajaongkir.cities')) {
			return  $result = collect($cached)
						->filter(function ($i, $k) {
							return strpos($i, 'province":"'.$this->id.'"') !== false; 
						})
						->map(function ($i, $k) {
							$i = json_decode($i);
							return new City($i->id, $i->name, $i->type, $i->postalCode, $i->province);
						});
		}

		$http = $this->getRequest('/city', ['query' => ['province' => $this->id]]);
		$body = json_decode($http->getBody());
		
		$arr = [];
		$cities = $body->rajaongkir->results;
		foreach ($cities as $city) {
			$arr[$city->city_id] = new City($city->city_id, $city->city_name, $city->type, $city->postal_code, $city->province_id);
		}

		return $arr;
	}

	public function citiesAttribute()
	{
		return $this->getCities();
	}

	public function withCities()
	{
		$this->attributes['cities'] = $this->citiesAttribute();
		return $this;
	}

	public function nameAttribute()
	{
		return $this->name;
	}

	public function __get($key)
	{
		if (array_key_exists($key, $this->attributes)) {
			return $this->attributes[$key];
		} 

		if (method_exists(self::class, $methodName = $key."Attribute")) {
			return $this->attributes[$key] = $this->$methodName();
		}

		return;
	}

	public function __toString()
	{
		return json_encode((object) [
			'id' => $this->id,
			'name' => $this->name,
		]);
	}
}