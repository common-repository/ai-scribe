<?php

// Exit if accessed directly

if (!defined('ABSPATH'))
    exit;


if (!class_exists('AISCRIBE_OpenAI_Client')) :

    /**
     * Main AISCRIBE_OpenAI_Client Class
     *
     */
    class AISCRIBE_OpenAI_Client {

        /**
         * @throws \Exception
         */
        public function dispatch($url, $params) {

			/* TODO: Change the apiKey settings key to openaiAPIKey and languageModel to defaultOpenAIModel prior to shipping to accommodate other AI engines */
            $open_ai_key = aiscribe_get_option('apiKey');

            // Use print_r with the second parameter set to true to return the array as a string
            $array_string = print_r($params, true);

            // TODO: Remove later. Log the array string to the error log
            // error_log($array_string);

            if ($open_ai_key == '') {
                throw new \Exception(esc_html__('The OpenAI API secret key is not specified in the plugin settings. Go to AI Scribe -> Settings to provide the same.', 'aiscribe'));
            }
            $headers = array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $open_ai_key
            );
            $options = array(
                'headers' => $headers,
                'method' => 'POST',
                'timeout' => 200,
                'redirection' => 5,
                'body' => json_encode($params),
                'sslverify' => false
            );
            $response = wp_remote_post($url, $options);

            if (is_wp_error($response)) {
                throw new \Exception($response->get_error_message());
            }

            $response_decoded = json_decode(wp_remote_retrieve_body($response), true);

            if (isset($response_decoded['error'])) {
                $message = $response_decoded['error']['message'];
                // If the message contains "Incorrect API key provided: THE_KEY.", replace the key by "----".
                if (preg_match('/API key provided(: .*)\./', $message, $matches)) {
                    $message = str_replace($matches[1], '', $message);
                }
                throw new \Exception($message);
            }

            return $response_decoded;
        }

    }

endif; // End if class_exists check


