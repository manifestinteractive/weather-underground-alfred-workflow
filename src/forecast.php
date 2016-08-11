<?php

header("Content-type: text/xml");

require_once('common.php');

if (API_KEY && WEATHER_LOCATION) {
    $weather = get_content(WEATHER_FORECAST_URL);
    $weather = json_decode($weather);
    $weather = $weather->forecast->simpleforecast;

    foreach ($weather->forecastday as $forecast):
        $workflow->result(
            $forecast->date->epoch,
            'http://www.wunderground.com' . WEATHER_LOCATION . '#horizontal-day-' . $forecast->date->yday,
            forecast_title($forecast),
            forecast_subtitle($forecast),
            weather_icon($forecast->icon)
        );
    endforeach;

    uasort($workflow->results(), 'date_sort');

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