<?php

namespace FAU\StudiumDisplay;

defined('ABSPATH') || exit;

class CPT
{
    const POST_TYPE = 'degree-program';

    public static function init()
    {
        add_action('init', [__CLASS__, 'registerPostType']);
    }

    public static function registerPostType()
    {
        $settings = get_option('fau-studium-display-settings');
        $slug = (isset($settings['slug']) && $settings['slug'] != '') ? $settings['slug'] : 'degree-program';

        $args = [
            'label'             => __('Degree Programs', 'fau-studium-display'),
            'hierarchical'       => false,
            'public'             => true,
            //'show_ui'            => false,
            'supports'           => ['title', 'thumbnail'],
            'menu_icon'          => 'dashicons-portfolio',
            'capability_type'    => 'page',
            'has_archive'        => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'rewrite'            => ['slug' => $slug],
            'show_in_rest'       => false,
        ];

        register_post_type(self::POST_TYPE, $args);
    }
}