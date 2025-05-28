<?php

namespace FAU\StudiumDisplay;

class Main
{
    protected string $pluginFile;


    public function __construct(string $pluginFile) {
        $this->pluginFile = $pluginFile;

        add_action('init', [$this, 'createBlocks']);
        add_filter('block_categories_all', [$this, 'rrzeBlockCategory'], 10, 2);
    }


    public function onLoaded() {}


    function createBlocks(): void {
        register_block_type( plugin_dir_path(__DIR__) . '/build/block' );
        $script_handle = generate_block_asset_handle( 'fau-studium-display', 'editorScript' );
        wp_set_script_translations( $script_handle, 'fau-studium-display', plugin_dir_path( __DIR__ ) . 'languages' );

        // fetch degree program list for combobox
        $api = new API();
        $degree_programs = $api->get_programs('id_title');
        wp_localize_script($script_handle, 'fauStudiumData', [
            'degreePrograms' => $degree_programs,
        ]);

        error_log( 'Script Handle: ' . $script_handle );
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

}