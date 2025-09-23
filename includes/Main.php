<?php

namespace Fau\DegreeProgram\Display;

defined('ABSPATH') || exit;

use function Fau\DegreeProgram\Display\Config\get_labels;
use function Fau\DegreeProgram\Display\Config\get_output_fields;

class Main
{
    protected string $pluginFile;


    public function __construct(string $pluginFile) {
        $this->pluginFile = $pluginFile;

        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminScripts']);
        add_action('init', [$this, 'createBlocks']);
        add_filter('block_categories_all', [$this, 'rrzeBlockCategory'], 10, 2);
        add_filter('single_template', [__CLASS__, 'include_single_template']);
        add_filter('archive_template', [__CLASS__, 'include_archive_template']);

        if (!is_plugin_active('FAU-Studium/fau-degree-program.php')) {
            new CPT();
        }
    }


    public function onLoaded()
    {
        $settings = new Settings($this->pluginFile);
        $settings->onLoaded();
    }


    function createBlocks(): void {
        register_block_type( plugin_dir_path(__DIR__) . '/build/block' );
        $script_handle = generate_block_asset_handle( 'fau-studium-display', 'editorScript' );
        wp_set_script_translations( $script_handle, 'fau-studium-display', plugin_dir_path( __DIR__ ) . 'languages' );

        // get degree program lists for combobox
        $degree_programs = Utils::get_program_options();
        $facultyOptions = Utils::get_faculty_options();
        $degreeOptions = Utils::get_degree_options(true);
        $attributeOptions = Utils::get_attribute_options();

        // get display options from config
        $labels = get_labels('de');

        $items_grid = get_output_fields('grid');
        $items_grid_formatted = [];
        foreach ($items_grid as $item) {
            $items_grid_formatted[] = [
                'label' => $labels[$item] ?? $item,
                'value' => $item,
            ];
        }
        $items_table = get_output_fields('table');
        $items_table_formatted = [];
        foreach ($items_table as $item) {
            $items_table_formatted[] = [
                'label' => $labels[$item] ?? $item,
                'value' => $item,
            ];
        }

        $items_full = get_output_fields('full');
        $items_full_formatted = [];
        foreach ($items_full as $item) {
            $items_full_formatted[] = [
                'label' => $labels[$item] ?? $item,
                'value' => $item,
            ];
        }

        wp_localize_script($script_handle, 'fauStudiumData', [
            'degreePrograms' => $degree_programs,
            'facultiesOptions' => $facultyOptions,
            'specialWaysOptions' => $attributeOptions,
            'degreesOptions' => $degreeOptions,
            'itemsGridOptions' => $items_grid_formatted,
            'itemsTableOptions' => $items_table_formatted,
            'itemsFullOptions' => $items_full_formatted,
        ]);

    }


    /**
     * Adds custom block category if not already present.
     *
     * @param array   $categories Existing block categories.
     * @param WP_Post $post       Current post object.
     * @return array Modified block categories.
     */
    function rrzeBlockCategory($categories, $post): array
    {
        // Check if there is already a RRZE category present
        foreach ($categories as $category) {
            if (isset($category['slug']) && $category['slug'] === 'rrze') {
                return $categories;
            }
        }

        $custom_category = [
            'slug'  => 'rrze',
            'title' => __('RRZE', 'fau-studium-display'),
        ];

        // Add RRZE to the end of the categories array
        $categories[] = $custom_category;

        return $categories;
    }

    public function enqueueScripts(): void
    {
        wp_register_style(
            'fau-studium-display',
            plugin()->getUrl() . 'assets/css/fau-studium-display.css',
            [],
            plugin()->getVersion()
        );
        wp_register_script(
            'fau-studium-display-script',
            plugins_url('assets/js/fau-studium-display.min.js', plugin()->getFile()),
            ['jquery'],
            plugin()->getVersion()
        );
    }

    public function enqueueAdminScripts(): void {
        wp_register_style(
            'fau-studium-display-admin',
            plugin()->getUrl() . 'assets/css/fau-studium-display-admin.css',
            [],
            plugin()->getVersion()
        );
        wp_register_script(
            'fau-studium-display-admin',
            plugins_url('assets/js/fau-studium-display-admin.min.js', plugin()->getFile()),
            ['jquery'],
            plugin()->getVersion()
        );
        wp_localize_script('fau-studium-display-admin', 'program_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('fau_studium_display_admin_ajax_nonce')
        ]);
    }

    public static function include_single_template($template_path)
    {
        global $post;

        if (!in_array($post->post_type, ['degree-program', 'studiengang'])) {
            return $template_path;
        }

        $template_path = plugin()->getPath() . 'templates/fau/single-degree-program.php';

        wp_enqueue_style('fau-studium-display');

        return $template_path;
    }

    public static function include_archive_template($template_path)
    {
        global $post;
        if (in_array($post->post_type, ['degree-program', 'studiengang']) && is_archive()) {
            if ($theme_file = locate_template(array('archive-degree-program.php'))) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin()->getPath() . '/templates/fau/archive-degree-program.php';
            }
        }

        wp_enqueue_style('fau-studium-display');

        return $template_path;
    }

}