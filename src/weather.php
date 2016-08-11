<?php

header("Content-type: text/xml");

require_once('common.php');

if (API_KEY && WEATHER_LOCATION) {
	$json = get_content(WEATHER_CONDITIONS_URL);
	$data = json_decode($json);
	$weather = $data->current_observation;
	$workflow->result(
		'weather',
		'http://www.wunderground.com' . WEATHER_LOCATION,
		weather_title($weather),
		weather_subtitle($weather),
		weather_icon($weather->icon)
	);
} else if ( !API_KEY) {
	$workflow->result(
		'weathersetup',
		'api',
		'Missing API Key',
		'Click here to Set your API Key',
		'settings.png'
	);
} else if ( !WEATHER_LOCATION) {
	$workflow->result(
		'weathersetup',
		'location',
		'Missing Location',
		'Click here to Set your Current Location',
		'settings.png'
	);
}

exit($workflow->toxml());