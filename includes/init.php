<?php

// Exit if accessed directly
if (!defined('ABSPATH'))
	exit;

/**
 * Create a new admin page.
 */
function aiscribe_admin_menu() {
	// Create a new admin page for our app.
	add_menu_page(
		esc_html__('AI Scribe', 'aiscribe'),
		esc_html__('AI Scribe', 'aiscribe'),
		'edit_posts',
		'aiscribe-app',
		function () {
			echo '<div id="aiscribe-app"></div>';
		},
		'dashicons-edit-page',
		3
	);

	// Add a submenu page for the settings.
	add_submenu_page(
		'aiscribe-app',
		esc_html__('Settings', 'aiscribe'),
		esc_html__('Settings', 'aiscribe'),
		'manage_options',
		'aiscribe-settings',
		function () {
			echo '<div id="aiscribe-settings"></div>';
		}
	);
}
add_action('admin_menu', 'aiscribe_admin_menu');


add_action('admin_menu', 'aiscribe_admin_menu');

/**
 * Init. Register scripts and styles.
 */
function aiscribe_register_sidebar_plugin_scripts() {
	// Load the required WordPress packages.

	// Automatically load imported dependencies and assets version.
	$asset_file = include AISCRIBE_PLUGIN_DIR . 'dist/sidebar-plugin.asset.php';

	// Load our app.js.
	wp_register_script(
		'aiscribe-sidebar-plugin-js',
		AISCRIBE_PLUGIN_URL . 'dist/sidebar-plugin.js',
		$asset_file['dependencies'],
		$asset_file['version']
	);

	// Load our style.css.
	wp_register_style(
		'aiscribe-sidebar-plugin-css',
		AISCRIBE_PLUGIN_URL . 'assets/css/sidebar-plugin.css',
		array(),
		$asset_file['version']
	);
}

add_action('init', 'aiscribe_register_sidebar_plugin_scripts');

/**
 * Init. Register scripts and styles.
 */
function aiscribe_register_scripts() {
	// Load the required WordPress packages.

	// Automatically load imported dependencies and assets version.
	$asset_file = include AISCRIBE_PLUGIN_DIR . 'dist/aiscribe.asset.php';

	// Load our app.js.
	wp_register_script(
		'aiscribe-js',
		AISCRIBE_PLUGIN_URL . 'dist/aiscribe.js',
		$asset_file['dependencies'],
		$asset_file['version']
	);

}

add_action('init', 'aiscribe_register_scripts');


function aiscribe_load_sidebar_plugin_scripts($hook) {

	// Load the required WordPress packages.

	// Automatically load imported dependencies and assets version.
	$asset_file = include AISCRIBE_PLUGIN_DIR . 'dist/sidebar-plugin.asset.php';

	// Enqueue CSS dependencies.
	foreach ($asset_file['dependencies'] as $style) {
		wp_enqueue_style($style);
	}

	wp_enqueue_script('aiscribe-sidebar-plugin-js');

	wp_set_script_translations('aiscribe-sidebar-plugin-js', 'aiscribe', AISCRIBE_PLUGIN_DIR . 'languages');

	wp_enqueue_style('aiscribe-sidebar-plugin-css');

}

add_action('enqueue_block_editor_assets', 'aiscribe_load_sidebar_plugin_scripts');

function aiscribe_load_scripts($hook) {
	// Load only on ?page=aiscribe-app.
	if ('toplevel_page_aiscribe-app' !== $hook && 'ai-scribe_page_aiscribe-settings' !== $hook) {
		return;
	}

	// Load the required WordPress packages.

	// Automatically load imported dependencies and assets version.
	$asset_file = include AISCRIBE_PLUGIN_DIR . 'dist/aiscribe.asset.php';

	// Enqueue CSS dependencies.
	foreach ($asset_file['dependencies'] as $style) {
		wp_enqueue_style($style);
	}

	wp_enqueue_script('aiscribe-js');

	wp_set_script_translations('aiscribe-js', 'aiscribe', AISCRIBE_PLUGIN_DIR . 'languages');

}

add_action('admin_enqueue_scripts', 'aiscribe_load_scripts');


