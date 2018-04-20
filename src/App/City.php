<?php

namespace Therour\RajaOngkir\App;

class City extends Base
{
	use UsingAttributes;

	protected $id;

	protected $name;

	protected $type;

	protected $postalCode;

	protected $provinceID;

	public function __construct($id, $name, $type, $postalCode, $provinceID)
	{
		$this->id = $id;

		$this->name = $name;

		$this->type = $type;

		$this->postalCode = $postalCode;

		$this->provinceID = $provinceID;
		
		parent::__construct();
	}

	public function nameAttribute()
	{
		return $this->name;
	}

	public function typeAttribute()
	{
		return $this->type;
	}

	public function postalCodeAttribute()
	{
		return $this->postalCode;
	}

	public function provinceAttribute()
	{
		return (new RajaOngkir)->getProvince($this->provinceID);
	}

	public function citiesAround()
	{
		return $this->province->cities;
	}

	public function __toString()
	{
		return json_encode((object)[
			'id' => $this->id,
			'name' => $this->name,
			'type' => $this->type,
			'postalCode' => $this->postalCode,
			'province' => $this->provinceID
		]);
	}
}