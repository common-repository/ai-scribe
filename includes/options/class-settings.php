<?php

if (!defined('ABSPATH'))
	exit;


if (!class_exists('AISCRIBE_Settings')) :

	/**
	 * Main AISCRIBE_Settings Class
	 *
	 */
	class AISCRIBE_Settings {

		/**
		 * Get things going
		 *
		 * @since 1.4.4
		 */
		public function __construct() {

			add_action('rest_api_init', array($this, 'register_rest_services'));

		}

		public function register_rest_services() {


			register_rest_route('ais/v1', '/update_option', array(
				'methods' => 'POST',
				'callback' => array($this, 'update_option'),
				'permission_callback' => function () {
					return current_user_can('manage_options');
				}
			));


			register_rest_route('ais/v1', '/remove_option', array(
				'methods' => 'POST',
				'callback' => array($this, 'remove_option'),
				'permission_callback' => function () {
					return current_user_can('manage_options');
				}
			));

			register_rest_route('ais/v1', '/save_settings', array(
				'methods' => 'POST',
				'callback' => array($this, 'save_settings'),
				'permission_callback' => function () {
					return current_user_can('manage_options');
				}
			));

			register_rest_route('ais/v1', '/retrieve_settings', array(
				'methods' => 'GET',
				'callback' => array($this, 'retrieve_settings'),
				'permission_callback' => function () {
					return current_user_can('manage_options');
				}
			));

			register_rest_route('ais/v1', '/get_generation_options', array(
				'methods' => 'GET',
				'callback' => array($this, 'get_generation_options'),
				'permission_callback' => function () {
					return current_user_can('edit_posts');
				}
			));
		}

		public function update_option($request) {

			// do nonce verification
			if (!wp_verify_nonce($request->get_header('X-WP-Nonce'), 'wp_rest')) {
				return new WP_REST_Response(['success' => false, 'message' => esc_html__('Bad API Request', 'aiscribe'),], 400);
			}

			$settings = $request->get_json_params();

			try {

				foreach ($settings as $key => $value) {
					aiscribe_update_option($key, $value);
				}

				return new WP_REST_Response(['success' => true, 'message' => esc_html__('Saved data successfully! ', 'aiscribe')], 200);

			} catch (\Throwable $throwable) {
				return new WP_REST_Response(['success' => false, 'message' => esc_html__('Error! ', 'aiscribe') . $throwable->getMessage()], 500);
			}

		}

		public function remove_option($request) {

			// do nonce verification
			if (!wp_verify_nonce($request->get_header('X-WP-Nonce'), 'wp_rest')) {
				return new WP_REST_Response(['success' => false, 'message' => esc_html__('Bad API Request', 'aiscribe'),], 400);
			}

			$settings = $request->get_json_params();

			try {

				foreach ($settings as $key) {
					aiscribe_remove_option($key);
				}

				return new WP_REST_Response(['success' => true, 'message' => esc_html__('Reset saved data successfully! ', 'aiscribe')], 200);

			} catch (\Throwable $throwable) {
				return new WP_REST_Response(['success' => false, 'message' => esc_html__('Error! ', 'aiscribe') . $throwable->getMessage()], 500);
			}

		}

		public function save_settings($request) {

			// do nonce verification
			if (!wp_verify_nonce($request->get_header('X-WP-Nonce'), 'wp_rest')) {
				return new WP_REST_Response(['success' => false, 'message' => esc_html__('Bad API Request', 'aiscribe'),], 400);
			}

			$settings = $request->get_json_params();

			try {
				aiscribe_update_options($settings);

				return new WP_REST_Response(['success' => true, 'message' => esc_html__('Saved Successfully! ', 'aiscribe')], 200);

			} catch (\Throwable $throwable) {
				return new WP_REST_Response(['success' => false, 'message' => esc_html__('Error! ', 'aiscribe') . $throwable->getMessage()], 500);
			}

		}

		public function retrieve_settings($request) {

			// do nonce verification
			if (!wp_verify_nonce($request->get_header('X-WP-Nonce'), 'wp_rest')) {
				return new WP_REST_Response(['success' => false, 'message' => esc_html__('Bad API Request', 'aiscribe'),], 400);
			}

			try {
				$settings = aiscribe_get_all_options();

				return new WP_REST_Response(['success' => true, 'data' => $settings], 200);

			} catch (\Throwable $throwable) {
				return new WP_REST_Response(['success' => false, 'message' => esc_html__('Error! ', 'aiscribe') . $throwable->getMessage()], 500);
			}


		}

		public function get_generation_options($request) {

			// do nonce verification
			if (!wp_verify_nonce($request->get_header('X-WP-Nonce'), 'wp_rest')) {
				return new WP_REST_Response(['success' => false, 'message' => esc_html__('Bad API Request', 'aiscribe'),], 400);
			}

			try {
				$settings = aiscribe_get_all_options();

				unset($settings['apiKey']);

				return new WP_REST_Response(['success' => true, 'data' => $settings], 200);

			} catch (\Throwable $throwable) {
				return new WP_REST_Response(['success' => false, 'message' => esc_html__('Error! ', 'aiscribe') . $throwable->getMessage()], 500);
			}

		}

	}

endif; // End if class_exists check

new AISCRIBE_Settings();
