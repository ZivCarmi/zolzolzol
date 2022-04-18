<?php namespace app\classes\helpers;

defined('ABSPATH') || exit;

class Tools {
	// check if current time is after the provided date and time, default format: d/m/Y H:i:s
	static public function is_before_datetime ($datetime, $format = 'd/m/Y H:i', $inclusive = true, $show_diff = false) {
		date_default_timezone_set(TIMEZONE);
		
		$timestamp = \DateTime::createFromFormat($format, $datetime)->format('U');
		
		if ($show_diff) {
			echo '<div dir="ltr"><pre>';
			print_r("Provided DateTime: {$datetime}\r\nCurrent DateTime: " . date($format, time()) . "\r\nDateTime format: {$format}\r\nTimezone: " . TIMEZONE);
			echo '</pre></div>';
		}
		
		if ($inclusive) {
			if (time() >= $timestamp) return true;
		} else {
			if (time() > $timestamp) return true;
		}
		
		return false;
	}
}
