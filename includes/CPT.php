<?php

namespace FAU\StudiumDisplay;

defined('ABSPATH') || exit;

class CPT
{
    const POST_TYPE = 'degree-program';

    public function __construct()
    {
        add_action( 'init', [$this, 'register_post_type'] );
        add_action('add_meta_boxes', [$this, 'render_metabox']);
        add_action('admin_menu', [$this, 'disable_new_posts']);

    }

    public function register_post_type()
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

    public function render_metabox()
    {
        add_meta_box(
            'degree_program_metabox',
            'Post Meta',
            [$this, 'degree_program_metabox'],
            'degree-program',
            'normal',
            'high'
        );
    }

    public function degree_program_metabox($post)
    {
        $aPostMeta = get_post_meta($post->ID);

        foreach ($aPostMeta as $key => $aEntry) {
            //var_dump($aEntry[0]);
            $val = (is_serialized($aEntry[0]) ? unserialize($aEntry[0]) : $aEntry[0]);

            echo '<span><strong>' . $key . ':</strong></span><br />' . Utils::arrayToHtmlList($val) . "<hr>";
        }
    }

    public function disable_new_posts()
    {
        global $submenu;

        unset($submenu['edit.php?post_type=degree-program'][10]);

        if (isset($_GET['post_type']) && $_GET['post_type'] == 'degree-program') {
            echo '<style type="text/css">.page-title-action {display:none;}</style>';
        }
    }

}