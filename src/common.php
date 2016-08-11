<?php

error_reporting(0);

require_once('workflows.php');

$workflow = new Workflows();

define('API_KEY', $workflow->get('api.key', 'settings.plist'));
define('ICON_COLOR', $workflow->get('icon.color', 'settings.plist'));
define('WEATHER_LOCATION', $workflow->get('default.location', 'settings.plist'));
define('WEATHER_UNIT', $workflow->get('preferred.unit', 'settings.plist'));
define('CACHE_PATH', getcwd() . '/cache/');
define('CACHE_EXPIRE', 300);
define('WEATHER_FORECAST_URL', "https://api.wunderground.com/api/" . API_KEY . "/forecast/" . WEATHER_LOCATION . ".json");
define('WEATHER_CONDITIONS_URL', "https://api.wunderground.com/api/" . API_KEY . "/conditions/" . WEATHER_LOCATION . ".json");

/**
 * @param $url
 * @return mixed|string
 */
function get_content($url) {

    /** Purge old files from cache */
    foreach (glob(CACHE_PATH . '*.json') as $file) {
        if (filemtime($file) > time() - CACHE_EXPIRE) {
            unlink($file);
        }
    }

    $file = CACHE_PATH . md5($url) . '.json';
    $current_time = time();
    $expire_time = CACHE_EXPIRE;
    $file_time = filemtime($file);
    $time_difference = $current_time - $expire_time;

    if(file_exists($file) && ($time_difference < $file_time)) {
        return file_get_contents($file);
    } else {
        $content = get_url($url);
        file_put_contents($file, $content);
        return $content;
    }
}
/**
 * @param $url
 * @return mixed
 */
function get_url($url) {
    $workflow = new Workflows();
    return $workflow->request($url);
}

/**
 * @param $icon
 * @return string
 */
function weather_icon($icon) {
    return "icons/" . ICON_COLOR . "/{$icon}.png";
}

/**
 * @param $weather
 * @return string
 */
function weather_title($weather) {
    return "{$weather->display_location->full}: {$weather->weather}";
}

/**
 * @param $weather
 * @return string
 */
function weather_subtitle($weather) {
    if (WEATHER_UNIT === 'F') {
        return "{$weather->temp_f}'°F";
    } else {
        return "{$weather->temp_c}'°C";
    }
}

/**
 * @param $fc
 * @return string
 */
function forecast_title($fc) {
    return "{$fc->date->weekday}: $fc->conditions";
}

/**
 * @param $fc
 * @return string
 */
function forecast_subtitle($fc) {
    if (WEATHER_UNIT === 'F') {
        return "" .
            "High: {$fc->high->fahrenheit}°F " .
            "Low: {$fc->low->fahrenheit}°F " .
            "Precipitation: {$fc->pop}%";
    } else {
        return "" .
            "High: {$fc->high->celsius}°C " .
            "Low: {$fc->low->celsius}°C " .
            "Precipitation: {$fc->pop}%";
    }
}

/**
 * @param $a
 * @param $b
 * @return int
 */
function date_sort($a, $b) {
    if ($a['arg'] === $b['arg']):
        return 0;
    endif;

    return ($a['arg'] < $b['arg']) ? -1 : 1;
}