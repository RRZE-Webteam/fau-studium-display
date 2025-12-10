<?php

/*
Plugin Name: FAU Studium Display
Plugin URI: https://github.com/RRZE-Webteam/fau-studium-display
Description: Plugin for displaying the degree program information on websites.
Version: 1.0.16
Author: RRZE Webteam
License: GNU General Public License v3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Text Domain: fau-studium-display
Domain Path: /languages
Requires at least: 6.7
Requires PHP: 8.2
*/

namespace Fau\DegreeProgram\Display;

// Prevent direct access to the file.
// This line ensures that the file is only executed within the context of WordPress.
// If accessed directly, it will exit the script to prevent unauthorized access.
defined('ABSPATH') || exit;

require_once 'config/config.php';
require_once 'vendor/autoload.php';

/**
 * SPL Autoloader (PSR-4).
 *
 * This autoloader function is registered with the SPL autoload stack to automatically load classes
 * from the plugin's 'includes' directory based on their fully-qualified class names.
 * It follows the PSR-4 autoloading standard, where the namespace corresponds to the directory structure.
 * It maps the namespace prefix to the base directory of the plugin, allowing for easy class loading
 * without the need for manual `require` or `include` statements.
 * This autoloader is particularly useful for organizing plugin code into classes and namespaces,
 * promoting better code structure and maintainability.
 * Use require_once `vendor/autoload.php` instead if you are using Composer for autoloading.
 *
 * @see https://www.php-fig.org/psr/psr-4/
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

// Register activation hook for the plugin
register_activation_hook(__FILE__, __NAMESPACE__ . '\activation');

// Register deactivation hook for the plugin
register_deactivation_hook(__FILE__, __NAMESPACE__ . '\deactivation');

/**
 * Add an action hook for the 'plugins_loaded' hook.
 *
 * This hook is triggered after all active plugins have been loaded, allowing the plugin to perform
 * initialization tasks.
 */
add_action('plugins_loaded', __NAMESPACE__ . '\loaded');

/**
 * Activation callback function.
 *
 * @return void
 */
function activation()
{
    // Use this if you need to perform tasks on activation.

    // Don't activate FAU Studium Display if FAU-Studiengangsanzeige/FAU-Studium-Embed is already active!!!
    if (is_plugin_active('FAU-Studium-Embed/fau-degree-program-output.php')
        || is_plugin_active_for_network('FAU-Studium-Embed/fau-degree-program-output.php')) {
        wp_die(
            __('FAU Studium Display is not compatible with FAU Degree Program Output. Deactivate FAU Degree Program Output before activating FAU Studium Display.', 'fau-studium-display'),
            __('Incompatible Plugin', 'fau-studium-display'),
            array('back_link' => true)
        );
    }
}

/**
 * Deactivation callback function.
 */
function deactivation()
{

    $programs = get_posts([
        'post_type' => 'degree-program',
        'numberposts' => -1,
        'post_status' => 'any'
    ]);

    foreach ($programs as $post) {
        wp_delete_post($post->ID, true);
    }

    if (wp_next_scheduled('fau_studium_display_sync_programs')) {
        wp_clear_scheduled_hook('fau_studium_display_sync_programs');
    }
}

/**
 * Singleton pattern for initializing and accessing the main plugin instance.
 *
 * This method ensures that only one instance of the Plugin class is created and returned.
 *
 * @return Plugin The main instance of the Plugin class.
 */
function plugin()
{
    // Declare a static variable to hold the instance.
    static $instance;

    // Check if the instance is not already created.
    if (null === $instance) {
        // Add a new instance of the Plugin class, passing the current file (__FILE__) as a parameter.
        $instance = new Plugin(__FILE__);
    }

    // Return the main instance of the Plugin class.
    return $instance;
}

/**
 * Main function to initialize the plugin.
 *
 * This function follows the singleton pattern to ensure that only one instance of the Main class is created.
 * It serves as the entry point for the plugin's functionality and is called when the plugin is loaded.
 *
 * @return Main The main instance of the Main class.
 */
function main()
{
    // Declare a static variable to hold the instance.
    static $instance;

    // Check if the instance is not already created.
    if (null === $instance) {
        // Add a new instance of the Main class.
        $instance = (new Main(plugin()->getFile()))->onLoaded();
    }

    // Return the main instance of the Main class.
    return $instance;
}

/**
 * Callback function to load the plugin textdomain.
 *
 * @return void
 */
function load_textdomain()
{
    load_plugin_textdomain(
        'fau-studium-display',
        false,
        dirname(plugin_basename(__FILE__)) . '/languages'
    );
}

/**
 * Handle the loading of the plugin.
 *
 * This function is responsible for initializing the plugin, loading text domains for localization,
 * checking system requirements, and displaying error notices if necessary.
 */
function loaded()
{
    // Trigger the 'loaded' method of the main plugin instance.
    plugin()->loaded();

    // Load the plugin textdomain for translations.
    add_action(
        'init',
        __NAMESPACE__ . '\load_textdomain'
    );

    // Check system requirements.
    if (
        ! $wpCompatibe = is_wp_version_compatible(plugin()->getRequiresWP())
                         || ! $phpCompatible = is_php_version_compatible(plugin()->getRequiresPHP())
    ) {
        // If the system requirements are not met, add an action to display an admin notice.
        add_action('init', function () use ($wpCompatibe, $phpCompatible) {
            // Check if the current user has the capability to activate plugins.
            if (current_user_can('activate_plugins')) {
                // Determine the appropriate admin notice tag based on whether the plugin is network activated.
                $hookName = is_plugin_active_for_network(
                    plugin()->getBaseName()
                ) ? 'network_admin_notices' : 'admin_notices';

                // Get the plugin name for display in the admin notice.
                $pluginName = plugin()->getName();

                $error = '';
                if ( ! $wpCompatibe) {
                    $error = sprintf(
                    /* translators: 1: Server WordPress version number, 2: Required WordPress version number. */
                        __(
                            'The server is running WordPress version %1$s. The plugin requires at least WordPress version %2$s.',
                            'fau-studium-display'
                        ),
                        wp_get_wp_version(),
                        plugin()->getRequiresWP()
                    );
                } elseif ( ! $phpCompatible) {
                    $error = sprintf(
                    /* translators: 1: Server PHP version number, 2: Required PHP version number. */
                        __(
                            'The server is running PHP version %1$s. The plugin requires at least PHP version %2$s.',
                            'fau-studium-display'
                        ),
                        PHP_VERSION,
                        plugin()->getRequiresPHP()
                    );
                }

                // Display the error notice in the admin area.
                // This will show a notice with the plugin name and the error message.
                add_action($hookName, function () use ($pluginName, $error) {
                    printf(
                        '<div class="notice notice-error"><p>' .
                        /* translators: 1: The plugin name, 2: The error string. */
                        esc_html__('Plugins: %1$s: %2$s', 'fau-studium-display') .
                        '</p></div>',
                        $pluginName,
                        $error
                    );
                });
            }
        });

        // If the system requirements are not met, the plugin initialization will not proceed.
        return;
    }

    // If system requirements are met, proceed to initialize the main plugin instance.
    // This will load the main functionality of the plugin.
    main();
}