<?php

header("Content-type: text/xml");

require_once('common.php');

$query = urlencode(preg_replace('/[^a-zA-Z 0-9.]/', '', $argv[1]));
$mode = preg_replace('/[^a-zA-Z 0-9.]/', '', $argv[2]);

if ($mode === 'api') {

	if (strlen($query) === 0) {
		$workflow->result(
			'api',
			$query,
			'Enter API Key',
			'Enter your Weather Underground API Key',
			'settings.png',
			'no'
		);
		$workflow->result(
			'api',
			'https://www.wunderground.com/weather/api',
			'I need an API Key',
			'Create a Free Weather Underground Developer Account',
			'settings.png'
		);
	} else if (strlen($query) === 16) {
		$workflow->result(
			$query,
			$query,
			'Set API Key to ' . $query,
			'This is a Valid API Key',
			'icons/' . ICON_COLOR . '/clear.png'
		);
	}  else {
		$workflow->result(
			'api',
			null,
			'Invalid API Key',
			'API Key must be 16 characters in length',
			'icons/' . ICON_COLOR . '/chancetstorms.png',
			'no'
		);
	}

	exit($workflow->toxml());

} else if ($mode === 'location') {

	if (strlen( $query ) < 3):
		$workflow->result(
			'weather',
			'na',
			'Set Location ...',
			'Enter your Zipcode or City',
			'settings.png',
			'no'
		);
		exit($workflow->toxml());
	endif;

	$json = get_content("http://autocomplete.wunderground.com/aq?query={$query}&format=json");
	$suggestions = json_decode($json);

	if (!$suggestions || count($suggestions->RESULTS) == 0) {
		$workflow->result(
			'weather',
			'na',
			'No Results',
			'No results for ' . $query,
			'icons/' . ICON_COLOR . '/chancetstorms.png',
			'no'
		);
		exit($workflow->toxml());
	}

	foreach( $suggestions->RESULTS as $suggest ):
		$workflow->result(
			$suggest->l,
			$suggest->l,
			$suggest->name,
			"Country: {$suggest->c} Timezone: {$suggest->tz}",
			"icons/" . ICON_COLOR . "/partlysunny.png"
		);
	endforeach;

	exit($workflow->toxml());

} else if ($mode === 'icon') {

	$workflow->result(
		'weathericon',
		'black',
		'Black Icons',
		'Use Black Icons',
		'icons/black/chancetstorms.png',
		'yes',
		'Black'
	);

	$workflow->result(
		'weathericon',
		'white',
		'White Icons',
		'Use White Icons',
		'icons/white/chancetstorms.png',
		'yes',
		'White'
	);

	exit($workflow->toxml());

} else if ($mode === 'unit') {

	$workflow->result(
		'weatherunit',
		'F',
		'Fahrenheit',
		'Set your default weather unit to fahrenheit',
		'settings.png',
		'yes',
		'Fahrenheit'
	);

	$workflow->result(
		'weatherunit',
		'C',
		'Celsius',
		'Set your default weather unit to celsius',
		'settings.png',
		'yes',
		'Celsius'
	);

	exit($workflow->toxml());

}