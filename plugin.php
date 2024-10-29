<?php

if (!defined('ABSPATH'))
    exit;


if (!class_exists('AIScribe')) :

    /**
     * Main AIScribe Class
     *
     */
    final class AIScribe {

        /** Singleton *************************************************************/

        private static $instance;

        /**
         * Main AIScribe Instance
         *
         * Insures that only one instance of AIScribe exists in memory at any one
         * time. Also prevents needing to define globals all over the place.
         */
        public static function instance() {

            if (!isset(self::$instance) && !(self::$instance instanceof AIScribe)) {

                self::$instance = new AIScribe;

                self::$instance->setup_debug_constants();

                self::$instance->includes();

                self::$instance->hooks();

            }
            return self::$instance;
        }


        /**
         * Throw error on object clone
         *
         * The whole idea of the singleton design pattern is that there is a single
         * object therefore, we don't want the object to be cloned.
         */
        public function __clone() {
            // Cloning instances of the class is forbidden
            _doing_it_wrong(__FUNCTION__, esc_html__('Cheatin&#8217; huh?', 'aiscribe'), '1.4');
        }

        /**
         * Disable unserializing of the class
         *
         */
        public function __wakeup() {
            // Unserializing instances of the class is forbidden
            _doing_it_wrong(__FUNCTION__, esc_html__('Cheatin&#8217; huh?', 'aiscribe'), '1.4');
        }

        private function setup_debug_constants() {

            $enable_debug = false;

            $settings = get_option('aiscribe_settings');

            if ($settings && isset($settings['aiscribe_enable_debug']) && $settings['aiscribe_enable_debug'] == 'true')
                $enable_debug = true;

            // Enable script debugging
            if (!defined('AISCRIBE_SCRIPT_DEBUG')) {
                define('AISCRIBE_SCRIPT_DEBUG', $enable_debug);
            }

            // Minified JS file name suffix
            if (!defined('AISCRIBE_JS_SUFFIX')) {
                if ($enable_debug)
                    define('AISCRIBE_JS_SUFFIX', '');
                else
                    define('AISCRIBE_JS_SUFFIX', '.min');
            }
        }

        /**
         * Include required files
         *
         */
        private function includes() {

            if (!function_exists('is_plugin_active')) {
                include_once(ABSPATH . 'wp-admin/includes/plugin.php');
            }

            require_once AISCRIBE_PLUGIN_DIR . 'includes/utils/class-openai-client.php';

            require_once AISCRIBE_PLUGIN_DIR . 'includes/core/class-generate-content.php';

			require_once AISCRIBE_PLUGIN_DIR . 'includes/options/class-settings.php';

            require_once AISCRIBE_PLUGIN_DIR . 'includes/helper-functions.php';

            require_once AISCRIBE_PLUGIN_DIR . 'includes/init.php';

        }

        /**
         * Load Plugin Text Domain
         *
         * Looks for the plugin translation files in certain directories and loads
         * them to allow the plugin to be localised
         */
        public function load_plugin_textdomain() {

            $lang_dir = apply_filters('aiscribe_lang_dir', trailingslashit(AISCRIBE_PLUGIN_DIR . 'languages'));

            // Traditional WordPress plugin locale filter
            $locale = apply_filters('plugin_locale', get_locale(), 'aiscribe');
            $mofile = sprintf('%1$s-%2$s.mo', 'aiscribe', $locale);

            // Setup paths to current locale file
            $mofile_local = $lang_dir . $mofile;

            if (file_exists($mofile_local)) {
                // Look in the /wp-content/plugins/ai-scribe/languages/ folder
                load_textdomain('aiscribe', $mofile_local);
            }
            else {
                // Load the default language files
                load_plugin_textdomain('aiscribe', false, $lang_dir);
            }

            return false;
        }

        /**
         * Setup the default hooks and actions
         */
        private function hooks() {

            add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));

        }

    }

    /**
     * The main function responsible for returning the one true AIScribe
     * Instance to functions everywhere.
     *
     * Use this function like you would a global variable, except without needing
     * to declare the global.
     *
     * Example: <?php $aiscribe = AISCRIBE(); ?>
     */
    function AISCRIBE() {
        return AIScribe::instance();
    }

    // Get AISCRIBE Running
    AISCRIBE();

endif; // End if class_exists check


