<?php
/**
 * API code
 */

require_once 'api.postcodes.php';
require_once realpath(__DIR__) . '/../lib.inc.php';

$GLOBALS['p'] = new Postcode();

function get_address_and_opening_times($user_postcode)
{
	if ($GLOBALS['p']->validate($user_postcode))
	{
		$locations = array_map('str_getcsv', file(realpath(__DIR__) . '/../doc/location_data.csv'));
		$header = array_shift($locations);

		foreach($locations as $location) 
		{
		    $locations_data[] = array_combine($header, $location);
		}

		foreach ($locations_data as $location)
		{
			$postcodes[] = $location['postcode']; // Grab all existing BeautySalon postcodes
		}

		foreach ($postcodes as $postcode) 
		{
			$distance[$postcode] = postcode_distance($user_postcode, $postcode);
		}
		
		// Find nearest location
		$nearest_postcode = nearest_postcode($user_postcode, $distance);

		// Now lookup for address of $nearest_postcode
		$address = address_lookup($nearest_postcode);
		
		foreach ($locations_data as $location)
		{
			if ($location['postcode'] == $nearest_postcode)
			{
				$opening_times = [
					"Monday" => $location['open_Monday'] . '-' . $location['closed_Monday'],
					"Tuesday" => $location['open_Tuesday'] . '-' . $location['closed_Tuesday'],
					"Wednesday" => $location['open_Wednesday'] . '-' . $location['closed_Wednesday'],
					"Thursday" => $location['open_Thursday'] . '-' . $location['closed_Thursday'],
					"Friday" => $location['open_Friday'] . '-' . $location['closed_Friday'],
					"Saturday" => $location['open_Saturday'] . '-' . $location['closed_Saturday'],
					"Sunday" => $location['open_Sunday'] . '-' . $location['closed_Sunday'],
				];
			}
		}
		
		if (!empty($address))
		{
			return [
				"address" => $address,
				"opening_times" => $opening_times,
			];
		}
	}
	else
	{
		phpAlert("Invalid Postcode");
		return;
	}
}


/**
 * Find distance between two postcodes
 *
 * @param string $user_postcode - Postcode given by user
 * @param string $postcode - Existing BeautySalon postcode
 * @return float $distance - Distance in miles
 */
function postcode_distance($user_postcode, $postcode) 
{
	$distance = $GLOBALS['p']->distance($user_postcode, $postcode, 'M');

	return $distance;
}


/**
 * Find nearest postcode
 * 
 * @param string $user_postcode - Postcode given by user
 * @param array $distance - Distance value between $user_postcode to existing BeautySalon postcodes
 * @return string $postcode - Nearest postcode to $user_postcode
 */
function nearest_postcode($user_postcode, $distance)
{
	if (!$user_postcode && !$distance && !is_array($distance)) 
	{ 
		return; 
	}

	$smallest_distance = min($distance); //smallest distance value

	foreach ($distance as $postcode => $distance_value) 
	{
		if ($distance_value == $smallest_distance) 
		{ 
			return $postcode; 
		}
	}
}


/**
 * Find address using postcode
 * 
 * @param string $postcode - Postcode
 * @return string $address
 */
function address_lookup($postcode)
{
	if (!$postcode)
	{
		return;
	}
	else
	{
		$postcode = urlencode($postcode);

		// Get API response based on postcode
		$query = "http://maps.googleapis.com/maps/api/geocode/json?address=" . $postcode . "&sensor=false";
		//echo '<pre>'; print_r(file_get_contents($query)); echo '</pre>';
		$result = json_decode(file_get_contents($query));
		sleep(10);
		//echo '<pre>'; print_r($result); echo '</pre>';
		if (!empty($result) && !empty($result->results) &&
			is_array($result->results) &&
			strtolower($result->status) == 'ok')
		{
			$lat = $result->results[0]->geometry->location->lat;
			$lng = $result->results[0]->geometry->location->lng;

			// Get the address based on returned lat & long
			$address_url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=" . $lat . "," . $lng . "&sensor=false";
			$address_json = json_decode(file_get_contents($address_url));
			sleep(10);
			if (!empty($address_json->results) &&
				is_array($address_json->results) &&
				strtolower($address_json->status) == 'ok')
			{
				$address_data = $address_json->results[0]->address_components;

				foreach($address_data as $data)
				{
					$address[$data->types[0]] = $data->long_name;
				}
				return $address;
			}
			else
			{
				phpAlert("Error: Failed to get address. Please try later.");
			}
		}
		else
		{
			phpAlert("Error: Failed to get response using postcode " . $result->status . " Please try later.");
		}
		return;
	}
}
