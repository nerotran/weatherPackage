<?php

namespace Tranhuq\WeatherPackage;
use GuzzleHttp\Client;

class location {
	public $city;
	public $state;
	public $country;
}

class WeatherDay {
	public $date;
	public $unit;
	public $temperature;
	public $feelslike;
	public $pressure;
	public $humidity;
}

class OpenWeatherClient {
	private $APIKEY;

	/**
     * Creates a new OpenWeatherClient.
     */
    public function __construct($apiKey)
    {
        $this->APIKEY = $apiKey;
    }

	public function getCurrentWeather($location){
		$wd = new WeatherDay;
		$city = $location->$city;
		$state = $location->$state;
		$country = $location->$country;

		$uri = "http://api.openweathermap.org/geo/1.0/direct?q=$city";
	    if (isset($state)) {
	      $uri = $uri . ",$state";
	    }

	    if (isset($country)) {
	      $uri = $uri . ",$country";
	    }

	    $uri = $uri . "&appid=$APIKEY";

	    //create a new client
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => $uri,
            // You can set any number of default request options.
            'timeout'  => 2.0,
        ]);

        try {

            $response = $client->request('GET','');
        } catch (Exception $e) {
        	error_log($e);
        }
        $body = (string) $response->getBody();
        $jbody = json_decode($body);
        if (!$jbody) {
          error_log("no json");
        }

        $lon = $jbody[0]->lon;
        $lat = $jbody[0]->lat;

        $uri = "http://api.openweathermap.org/data/2.5/weather?lat=$lat&lon=$lon&units=imperial&appid=$APIKEY";

        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => $uri,
            // You can set any number of default request options.
            'timeout'  => 2.0,
        ]);

        try {
            $response = $client->request('GET','');
        } catch (Exception $e) {
          error_log($e);
        }
        $body = (string) $response->getBody();
        $jbody = json_decode($body);
        if (!$jbody) {
          error_log("no json");
        }

        $wd->unit = "Imperial";
        $wd->temperature = $jbody->main->temp;
        $wd->feelslike = $jbody->main->feels_like;
        $wd->pressure = $jbody->main->pressure;
        $wd->humidity = $jbody->main->humidity;

		return $wd;
	}
}
?>
