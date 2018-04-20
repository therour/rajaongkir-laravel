<?php

namespace Therour\RajaOngkir\App;

class RajaOngkir extends Base
{	
	use UsingAttributes;

	public function getProvinces()
	{
		if ($cached = $this->shouldGetFromCache('rajaongkir.provinces')) {
			return $cached;
		}
		
		$http = $this->getRequest('/province');
		$body = json_decode($http->getBody());

		$provinces =  $body->rajaongkir->results;
		$arr = [];
		foreach ($provinces as $province) {
			$arr[(int)$province->province_id] = new Province($province->province_id, $province->province);
		}

		return $this->shouldSaveToCache('rajaongkir.provinces', $arr);
	}

	public function getCities()
	{

		if ($cached = $this->shouldGetFromCache('rajaongkir.cities')) {
			return $cached;
		}

		$http = $this->getRequest('/city');
		$body = json_decode($http->getBody());

		$arr = [];
		$cities = $body->rajaongkir->results;
		foreach ($cities as $city) {
			$arr[$city->city_id] = new City($city->city_id, $city->city_name, $city->type, $city->postal_code, $city->province_id);
		}

		return $this->shouldSaveToCache('rajaongkir.cities', $arr);
	}

	public function citiesAttribute()
	{
		return $this->getCities();
	}

	public function provincesAttribute()
	{
		return $this->getProvinces();
	}

	public function getProvince($id)
	{	
		if (isset($this->attributes['provinces']) && array_key_exists($id, $this->attributes['provinces'])) {
			return $this->attributes['provinces'][$id];
		}

		if ($cached = $this->shouldGetFromCache('rajaongkir.provinces.'.$id)) {
			return $cached;
		}

		$http = $this->getRequest('/province', ['query' => ['id' => $id]]);
		$body = json_decode($http->getBody());

		return $this->shouldSaveToCache('rajaongkir.provinces.'.$id, new Province($id, $body->rajaongkir->results->province));
	}

	public function getCity($id)
	{
		if (isset($this->attributes['cities']) && array_key_exists($id, $this->attributes['cities'])) {
			return $this->attributes['cities'][$id];
		}
		
		if ($cached = $this->shouldGetFromCache('rajaongkir.cities.'.$id)) {
			return $cached;
		}

		$http = $this->getRequest('/city', ['query' => ['id' => $id]]);
		$body = json_decode($http->getBody());

		$city = $body->rajaongkir->results;

		return $this->shouldSaveToCache('rajaongkir.cities.'.$id, new City($id, $city->city_name, $city->type, $city->postal_code, $city->province_id));
	}

	public function couriersAttribute()
	{
		return $this->couriers;
	}

	public function calculate($destination = false, $weight = false, $courier = false)
	{
		$form_params = [
			'origin' => is_null($this->from) ? $this->origin : $this->from,
			'destination' => ! ($destination) ? $this->destination : $destination,
			'weight' => ! ($weight) ? $this->weight : $weight,
			'courier' => ! ($courier) ? $this->courier : $courier,
		];
		$http = $this->postRequest('/cost', ['form_params' => $form_params]);
		$body = json_decode($http->getBody());

		return $body->rajaongkir->results;
	}

	public function withJne()
	{
		$this->courier = 'jne'; 
		return $this;
	}

	public function withTiki()
	{
		$this->courier = 'tiki'; 
		return $this;
	}

	public function withPos()
	{
		$this->courier = 'pos'; 
		return $this;
	}

	public function to($destination)
	{
		$this->destination = $destination;
		return $this;
	}

	public function send($weight)
	{
		return $this->calculate(false, $weight);
	}

	public function from($origin)
	{
		$this->from = $origin;
		return $this;
	}

	public static function shouldCache($expire = 60, $yes = true)
	{
		parent::$cache = $yes;
		parent::$cacheExpires = $expire;
	}
}
