<?php

namespace Therour\RajaOngkir;

use Illuminate\Support\Facades\ServiceProvider;

class RajaOngkirServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->publishes([
			__DIR__.'/config/rajaongkir.php' => config_path().'/rajaongkir.php',
		]);
	}

	/**
	 * Register the application services.
	 * 
	 * @return void
	 */
	public function register()
	{
		$this->app->bind('RajaOngkir', function() {
			return new App\RajaOngkir;
		});
	}
}