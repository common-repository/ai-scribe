<?php

// Exit if accessed directly
if (!defined('ABSPATH'))
	exit;

/**
 * Fetch the settings values for AI Scribe->Settings
 * @return string The value of the option specified by the user
 */
function aiscribe_get_all_options() {

	$settings = get_option('aiscribe_settings');

	if (empty($settings))
		$settings = array();

	return apply_filters('aiscribe_get_all_options', $settings);
}

/**
 * Fetch the value of the option specified by the user in the Settings page under AI Scribe->Settings
 * @param $option_name The name of the option for which the value is desired
 * @param $default The default value if the option is not set
 * @return string The value of the option specified by the user
 */
function aiscribe_get_option($option_name, $default = null) {

	$settings = get_option('aiscribe_settings');

	if (!empty($settings) && isset($settings[$option_name]))
		$option_value = $settings[$option_name];
	else
		$option_value = $default;

	return apply_filters('aiscribe_get_option', $option_value, $option_name, $default);
}

/**
 * Remove the option in the DB for the option name specified. This is managed in
 * the Settings page under AI Scribe->Settings
 * @param $option_name The name of the option which needs to be updated
 * @return void
 */
function aiscribe_remove_option($option_name) {

	$settings = get_option('aiscribe_settings');

	if (empty($settings))
		$settings = array();

	unset($settings[$option_name]);

	update_option('aiscribe_settings', $settings);
}

/**
 * Update the option in the DB for the option name specified. This is managed in
 * the Settings page under AI Scribe->Settings
 * @param $option_name The name of the option which needs to be updated
 * @param $option_value The new value for the option which needs updation
 * @return void
 */
function aiscribe_update_option($option_name, $option_value) {

	$settings = get_option('aiscribe_settings');

	if (empty($settings))
		$settings = array();

	$settings[$option_name] = $option_value;

	update_option('aiscribe_settings', $settings);
}

/**
 * Update multiple options in one go for the Settings page under AI Scribe->Settings
 * @param array $setting_data A collection of settings key value pairs;
 */
function aiscribe_update_options($setting_data) {

	$settings = get_option('aiscribe_settings');

	if (empty($settings))
		$settings = array();

	foreach ($setting_data as $setting => $value) {
		// because of get_magic_quotes_gpc()
		$value = stripslashes($value);
		$settings[$setting] = $value;
	}

	update_option('aiscribe_settings', $settings);
}
