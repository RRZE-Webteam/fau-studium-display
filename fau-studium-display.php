<?php

/*
Plugin Name: FAU Studium Display
Plugin URI: https://github.com/RRZE-Webteam/fau-studium-display
Description: Plugin for displaying the degree program information on websites.
Version: 1.0.0
Author: RRZE Webteam
License: GNU General Public License v3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Text Domain: fau-studium-display
Domain Path: /languages
Requires at least: 6.7
Requires PHP: 8.2
*/

namespace FAU\StudiumDisplay;

defined('ABSPATH') || exit;

// Define plugin constants
define('FAU_STUDIUM_DISPLAY_PLUGIN_FILE', __FILE__);
define('FAU_STUDIUM_DISPLAY_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('FAU_STUDIUM_DISPLAY_PLUGIN_URL', plugin_dir_url(__FILE__));

// Constants
const FAU_STUDIUM_DISPLAY_PHP_VERSION = '8.2';
const FAU_STUDIUM_DISPLAY_WP_VERSION = '6.7';


/**
 * SPL Autoloader (PSR-4).
 * @param string $class The fully-qualified class name.
 * @return void
 */
spl_autoload_register(function ($class) {
    $prefix = __NAMESPACE__;
    $baseDir = __DIR__ . '/includes/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Load the plugin's text domain for localization.
add_action('init', fn() => load_plugin_textdomain('fau-studium-display', false, dirname(plugin_basename(__FILE__)) . '/languages'));

// System requirements check
function systemRequirements() {
    $error = '';
    if (version_compare(PHP_VERSION, FAU_STUDIUM_DISPLAY_PHP_VERSION, '<')) {
        $error = sprintf(
        /* translators: 1: Current PHP version 2: Required PHP version */
            __('Your PHP version (%1$s) is outdated. Please upgrade to PHP %2$s or higher.', 'rrze-faudir'),
            PHP_VERSION,
            FAU_STUDIUM_DISPLAY_PHP_VERSION
        );
    } elseif (version_compare($GLOBALS['wp_version'], FAU_STUDIUM_DISPLAY_WP_VERSION, '<')) {
        $error = sprintf(
        /* translators: 1: Current WordPress version 2: Required WordPress version */
            __('Your WordPress version (%1$s) is outdated. Please upgrade to WordPress %2$s or higher.', 'rrze-faudir'),
            $GLOBALS['wp_version'],
            FAU_STUDIUM_DISPLAY_WP_VERSION
        );
    }

    if (!empty($error)) {
        add_action('admin_notices', function () use ($error) {
            printf('<div class="notice notice-error"><p>%s</p></div>', esc_html($error));
        });
        return false;
    }
    return true;
}

if (false === systemRequirements())
    return;

// Proceed only, if system requirements are met

// Include necessary files
//require_once plugin_dir_path(__FILE__) . 'includes/Settings.php';


$main = new Main(FAU_STUDIUM_DISPLAY_PLUGIN_FILE);
$main->onLoaded();