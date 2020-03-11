<?php
class GEO
{
	public static function ObterEnderecoPorCoords($latitude, $longitude)
	{
		$geocode=file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng='.$latitude.','.$longitude.'&sensor=false&key='.GOOGLEMAPS_KEY);
		$output= json_decode($geocode);
		// echo '<pre>' , var_dump($output) , '</pre>';

		$formattedAddress = @$output->results[0]->address_components[1]->short_name. ', '.@$output->results[0]->address_components[2]->short_name;

		return $formattedAddress;
	}
}
?>