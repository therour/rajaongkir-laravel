<?php

namespace Therour\RajaOngkir\App;

use GuzzleHttp\Client;

abstract class Base
{	
	protected static $cache = false;

	protected static $cacheExpires = 60;

	protected $client;

	protected $baseUri;

	protected $accountType;

	protected $key;

	protected $origin;

	protected $attributes = [];

	protected $couriers = ['jne', 'tiki', 'pos'];

	protected $destination;
	
	protected $weight;
	
	protected $courier;

	protected $from;

	public function __construct()
	{
		$this->accountType = config('services.rajaongkir.account', 'starter');
		$this->baseUri = config('services.rajaongkir.base_uri', 'https://api.rajaongkir.com');
		$this->key = config('services.rajaongkir.key');
		$this->origin = config('services.rajaongkir.origin');

		$this->getClient();
	}

	protected function getRequest($uri, $options = array())
	{
		return $this->client->get('/'.$this->accountType.$uri, $options);
	}
	
	protected function postRequest($uri, $options = array())
	{
		return $this->client->post('/'.$this->accountType.$uri, $options);
	}

	private function getClient()
	{
		$config = [
			'base_uri' => $this->baseUri,
			'headers' => [
				'key' => $this->key,
			],
		];

		return $this->client = app(Client::class, ['config' => $config]);
	}

	protected function shouldGetFromCache($key) 
	{
		if (self::$cache && ($result = cache($key))) {
			return $result;
		}

		return;
	}

	protected function shouldSaveToCache($key, $value)
	{
		if (self::$cache) {
			if (is_array($value)) {
				$serialized = collect($value)->map( function ($item, $key) {
					return (string) $item;
				})->all();
			} else {
				$serialized = (string) $value;
			}
			cache([$key => $serialized], self::$cacheExpires);
		}

		return $value;
	}
}