<?php

namespace FAU\StudiumDisplay;

defined('ABSPATH') || exit;

use function FAU\StudiumDisplay\Config\get_labels;
use function FAU\StudiumDisplay\Config\get_output_fields;

class Main
{
    protected string $pluginFile;


    public function __construct(string $pluginFile) {
        $this->pluginFile = $pluginFile;

        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
        add_action('init', [$this, 'createBlocks']);
        add_filter('block_categories_all', [$this, 'rrzeBlockCategory'], 10, 2);

        CPT::init();
    }


    public function onLoaded() {}


    function createBlocks(): void {
        register_block_type( plugin_dir_path(__DIR__) . '/build/block' );
        $script_handle = generate_block_asset_handle( 'fau-studium-display', 'editorScript' );
        wp_set_script_translations( $script_handle, 'fau-studium-display', plugin_dir_path( __DIR__ ) . 'languages' );

        // get degree program list for combobox
        $api = new API();
        $degree_programs = $api->get_programs('id_title', true);

        // get format "grid" display options from config
        $labels = get_labels('de');
        $items_grid = get_output_fields('grid');
        $items_grid_formatted = [];
        foreach ($items_grid as $item) {
            $items_grid_formatted[] = [
                'label' => $labels[$item] ?? $item,
                'value' => $item,
            ];
        }
        wp_localize_script($script_handle, 'fauStudiumData', [
            'degreePrograms' => $degree_programs,
            'itemsGridOptions' => $items_grid_formatted,
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
            FAU_STUDIUM_DISPLAY_PLUGIN_URL . 'assets/css/fau-studium-display.css',
            [],
            FAU_STUDIUM_DISPLAY_PLUGIN_VERSION
        );
        wp_register_script(
            'fau-studium-display-script',
            plugins_url('assets/js/fau-studium-display.min.js', FAU_STUDIUM_DISPLAY_PLUGIN_FILE),
            //FAU_STUDIUM_DISPLAY_PLUGIN_URL . 'src/js/fau-studium-display.js',
            ['jquery'],
            FAU_STUDIUM_DISPLAY_PLUGIN_VERSION
        );
    }

}