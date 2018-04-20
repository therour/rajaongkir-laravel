# rajaongkir-laravel
Raja Ongkir API Laravel 5

This current version of rajaongkir api for laravel is currently for **_starter_** account only

### Installation ###

via Composer
```
composer require therour/rajaongkir-laravel
```

Add Service provider to `config/app.php` of your laravel project
```php
'providers' => [
      ....
      
      Therour\RajaOngkir\RajaOngkirServiceProvider::class,
]
```

### Settings
add your credential in `config/services.php` of your laravel project
```php
....

'rajaongkir' => [
    'base_uri' => env('RAJAONGKIR_BASE_URI', 'https://api.rajaongkir.com'),
    'type' => env('RAJAONGKIR_type', 'starter'),
    'key' => env('RAJAONGKIR_API_KEY', null),
    'origin' => env('RAJAONGKIR_ORIGIN'), // if you have fixed origin city id,
]
```

#### Cache
to enable caching of provinces and cities
add this code to your `app\Providers\AppServiceProvider.php`
```php
use Therour\RajaOngkir\Facades\RajaOngkir; 

class AppServiceProvider extends ServiceProvider
{

    public function boot()
    {
    	....

    	RajaOngkir::shouldCache($expire = 60); // cache expires in 60 minutes
    }

    ....
}
```
### Usage

#### Get Province and City by RajaOngkir
```php
use Therour\RajaOngkir\Facades\RajaOngkir;

$provinces = RajaOngkir::provinces; // return array of Therour\RajaOngkir\Province Objects

$cities = RajaOngkir::cities; // return array of Therour\RajaOngkir\Province Objects

$province = RajaOngkir::getProvince($id); // return a Therour\RajaOngkir\Province

$citiesOfProvince = $province->cities; // return array of Therour\RajaOngkir\City


```

#### Calculate shipment cost

Note:

| Variable        | Type                        | Example |
| --------------- |:---------------------------:| ------- |
| `$originID`     |    _integer_                | `501`   |
| `$destinationID`|    _integer_                | `39`    |
| `$weight`       | _integer_ **(gram)**        | `3000`  |
| `$courier`	  | _string_ **(jne,tiki,pos)** | `'tiki'`|

Code:

```php
use Therour\RajaOngkir\Facades\RajaOngkir;

// get calculation with default origin by your application service config
$cost = RajaOngkir::calculate($destinationID, $weight, $courier);

// get calculation with defining another origin
$cost = RajaOngkir::from($originID)->calculate($destinationID, $weight, $courier);

// another way to calculate with JNE courier
$cost = RajaOngkir::from($originID)->to($destinationID)->withJne()->send($weight);

// another way to calculate with TIKI courier
$cost = RajaOngkir::from($originID)->to($destinationID)->withTiki()->send($weight);

// another way to calculate with POS courier
$cost = RajaOngkir::from($originID)->to($destinationID)->withPos()->send($weight);
```
