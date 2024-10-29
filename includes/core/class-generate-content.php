<?php

if (!defined('ABSPATH'))
	exit;


if (!class_exists('AISCRIBE_Generate_Content')) :

	/**
	 * Main AISCRIBE_Generate_Content Class
	 *
	 */
	class AISCRIBE_Generate_Content {

		/**
		 * AIS OpenAI Client Object
		 *
		 * @var object|AISCRIBE_OpenAI_Client
		 */
		public $openai_client;

		/**
		 * Get things going
		 *
		 * @since 1.4.4
		 */
		public function __construct() {

			$this->openai_client = new AISCRIBE_OpenAI_Client();

			add_action('rest_api_init', array($this, 'register_rest_service'));

		}

		public function register_rest_service() {
			register_rest_route('ais/v1', '/generate_content', array(
				'methods' => 'POST',
				'callback' => array($this, 'generate_content'),
				'permission_callback' => function () {
					return current_user_can('edit_posts');
				}
			));
		}

		/**
		 * Removes any leading numbers, trims wrapping quotes and spaces from the output string.
		 * Also removes empty entries and reindexes the entries after removing them.
		 *
		 * @param array $entries An array of strings.
		 *
		 * @return array The processed array of strings.
		 */
		public function process_entries($entries) {

			// remove any leading numbers from the output string. Also trims wrapping quotes and spaces
			$entries = array_map(function($string) {
				$string = preg_replace("/^\d+[\.\)]\s*/", "", trim($string));
				return trim($string, " \"");
			}, $entries);

			$entries = array_filter($entries); // remove empty entries

			$entries = array_values($entries); // reindex the entries after removing empty entries

			return $entries;
		}

		public function generate_content($request) {

			// do nonce verification
			if (!wp_verify_nonce($request->get_header('X-WP-Nonce'), 'wp_rest')) {
				return new WP_REST_Response(['success' => false, 'message' => esc_html__('Bad API Request', 'aiscribe'),], 400);
			}

			$params = $request->get_json_params();

			try {

				$prompt = $params['prompt'];

				$prompt = str_replace(
					['[MAX_WORDS]', '[LANGUAGE]'],
					[$params['maxWords'], $params['language']],
					$prompt
				);

				if (isset($params['keywords'])) {
					$prompt = str_replace('[KEYWORDS]', $params['keywords'], $prompt);
				}

				if (isset($params['writingStyle'])) {
					$prompt = str_replace('[WRITING_STYLE]', $params['writingStyle'], $prompt);
				}

				if (isset($params['writingTone'])) {
					$prompt = str_replace('[WRITING_TONE]', $params['writingTone'], $prompt);
				}

				if (isset($params['text'])) {
					$prompt = str_replace('[TEXT]', $params['text'], $prompt);
				}

				if (isset($params['outline'])) {
					$prompt = str_replace('[OUTLINE]', $params['outline'], $prompt);
				}

				if (isset($params['topic'])) {
					$prompt = str_replace('[TOPIC]', $params['topic'], $prompt);
				}

				if (isset($params['productName'])) {
					$prompt = str_replace('[PRODUCT_NAME]', $params['productName'], $prompt);
				}

				if (isset($params['productDescription'])) {
					$prompt = str_replace('[PRODUCT_DESCRIPTION]', $params['productDescription'], $prompt);
				}

				if (isset($params['title'])) {
					$prompt = str_replace('[TITLE]', $params['title'], $prompt);
				}

				if (isset($params['numberOfEntries'])) {
					$count = intval($params['numberOfEntries']);
					$prompt = str_replace('[COUNT]', $count, $prompt);
					if ($count > 1)
					$prompt .= esc_html(__('.Separate each variation by @@@'), 'aiscribe');
				}

				$language_model = aiscribe_get_option('languageModel');
				$temperature = aiscribe_get_option('temperature');

				$call_params = array(
					'model' => $language_model,
					'temperature' => floatval($temperature),
					'n' =>  1,
					'frequency_penalty' => 0,
					'presence_penalty' => 0,
				);

				$chat_mode = false;
				if ($call_params['model'] == 'gpt-3.5-turbo' || $call_params['model'] == 'gpt-3.5-turbo-16k' || $call_params['model'] == 'gpt-4' || $call_params['model'] == 'gpt-4-32k') {
					$chat_mode = true;
				}

				if ($chat_mode) {
					$url = 'https://api.openai.com/v1/chat/completions';
					$call_params['messages'] = array(
						array('role' => 'user', 'content' => $prompt)
					);
				}
				else {
					$url = 'https://api.openai.com/v1/completions';
					$call_params['prompt'] = $prompt;
				}

				$response = $this->openai_client->dispatch($url, $call_params);

				$choices = $response['choices'];

				if (count($choices) == 0) {
					throw new \Exception(esc_html__('No response was generated. Please try again using different prompt!', 'aiscribe'));
				}

				$entries = array();

				foreach ($choices as $index => $value) {
					if ($chat_mode) {
						$content = $value['message']['content'];
					}
					else {
						$content = $value['text'];
					}
					$entries = array_merge($entries, explode('@@@', $content));
				}

				$entries = $this->process_entries($entries);

				return new WP_REST_Response(['success' => true, 'data' =>  $entries], 200);

			} catch (\Throwable $throwable) {
				return new WP_REST_Response(['success' => false, 'message' => esc_html__('Error! ', 'aiscribe') . $throwable->getMessage()], 500);
			}

		}

	}

endif; // End if class_exists check

new AISCRIBE_Generate_Content();
