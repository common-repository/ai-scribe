<?php
/**
 * Plugin Name: AI Scribe- Content Writer, OpenAI GPT
 * Plugin URI: https://livemeshwp.com/ai-scribe
 * Description: An AI powered content writer and generator for WordPress utilizing the OpenAI API that powers Chat GPT.
 * Author: Livemesh
 * Author URI: https://livemeshwp.com
 * License: GPL3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Version: 1.4
 * Text Domain: aiscribe
 * Domain Path: languages
 *
 * AI Scribe for WordPress is distributed under the terms of the GNU
 * General Public License as published by the Free Software Foundation,
 * either version 2 of the License, or any later version.
 *
 * AI Scribe for WordPress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with AI Scribe for WordPress. If not, see <http://www.gnu.org/licenses/>.
 *
 *
 */

// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;


// Plugin version
define('AISCRIBE_VERSION', '1.4');

// Plugin Root File
define('AISCRIBE_PLUGIN_FILE', __FILE__);

define('AISCRIBE_PLUGIN_SLUG', dirname(plugin_basename(__FILE__)));

// Plugin Folder Path
define('AISCRIBE_PLUGIN_DIR', plugin_dir_path(__FILE__));

// Plugin Folder URL
define('AISCRIBE_PLUGIN_URL', plugins_url('/', __FILE__));

require_once(dirname(__FILE__) . '/plugin.php');
