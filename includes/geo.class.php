<?php
class GEO
{

	public static function ObterEnderecoPorCoords($location)
	{
		if(!defined("GOOGLEMAPS_KEY") || is_null(GOOGLEMAPS_KEY)) return "Desconhecido";

		$location = json_decode($location);	// location param gets passed as json so we need to decode it
		$geocode = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng='.$location["latitude"].','.$location["longitude"].'&sensor=false&key='.GOOGLEMAPS_KEY);
		$output  = json_decode($geocode);

		// echo '<pre>' , var_dump($output) , '</pre>';

		return sprintf("%s, %s", $output->results[0]->address_components[1]->short_name, $output->results[0]->address_components[2]->short_name);
	}
}
?>