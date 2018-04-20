<?php

namespace Therour\RajaOngkir\App;

trait UsingAttributes {

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
}