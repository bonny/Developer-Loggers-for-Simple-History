<?php

exit;

// Make sure the Slackit logger is enabled, no matter if it has been enabled before or not
add_filter("simple_history/developer_loggers/enabled_loggers", function($arr_loggers) {

	if (!isset($arr_loggers["Slackit"])) {
		$arr_loggers["Slackit"] = [];
	}

	$arr_loggers["Slackit"]["enabled"] = true;

	return $arr_loggers;

}, 10, 1);

// Set the slackit webhook url
add_filter("simple_history/developer_loggers/slackit/settings", function($settings) {

	$settings["webhook_url"] = "https://callback.url/here";

	return $settings;

}, 10, 1);
