<?php

namespace Fau\DegreeProgram\Display;

defined('ABSPATH') || exit;

class CPT
{
    const string POST_TYPE = 'degree-program';
    private $post_type_slug;
    private $site_language;


    public function __construct()
    {
        add_action('init', [$this, 'register_post_type'], 9);
        add_action('init', [$this, 'register_taxonomies'], 9);
        add_action('add_meta_boxes', [$this, 'render_metabox']);
        add_action('admin_menu', [$this, 'disable_new_posts']);
        add_filter( 'query_vars', [$this, 'register_custom_query_vars'] );
        add_filter('redirect_canonical', [$this, 'archive_redirect_canonical'], 10, 2);
        add_filter('request', [$this, 'preserve_archive_filters']);
        add_filter('get_canonical_url', [$this, 'modify_canonical_url'], 10, 2 );
        add_filter( 'post_row_actions', [$this, 'remove_quick_edit_for_degree_program'], 10, 2 );

        $this->site_language = substr( get_locale(), 0, 2 );
        $this->post_type_slug = $this->site_language == 'de' ? 'studiengang' : 'degree-program';
    }

    public function register_post_type()
    {
        $args = [
            'label'             => __('Degree Programs', 'fau-studium-display'),
            'hierarchical'       => false,
            'public'             => true,
            'show_ui'            => false,
            'supports'           => ['title', 'thumbnail'],
            'menu_icon'          => 'dashicons-portfolio',
            'capability_type'    => 'page',
            'has_archive'        => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'rewrite'            => ['slug' => $this->post_type_slug],
            'show_in_rest'       => false,
        ];

        if (is_plugin_active('rrze-multilang/rrze-multilang.php') || is_plugin_active_for_network('rrze-multilang/rrze-multilang.php')) {
            $args['show_ui'] = true;
        }

        register_post_type(self::POST_TYPE, $args);
    }

    public function register_taxonomies() {

        $taxonomies = [
            'degree' => __('Degrees', 'fau-studium-display'),
            'subject_group' => __('Subject groups', 'fau-studium-display'),
            'attribute' => __('Special ways to study', 'fau-studium-display'),
            'admission_requirement' => __('Admission requirements', 'fau-studium-display'),
            'start' => __('Start of degree program', 'fau-studium-display'),
            'location' => __('Study location', 'fau-studium-display'),
            'teaching_language' => __('Teaching language', 'fau-studium-display'),
            'faculty' => __('Faculty', 'fau-studium-display'),
            'german_language_skills' => __('German language skills', 'fau-studium-display'),
        ];

        foreach ($taxonomies as $taxonomy => $label) {
            register_taxonomy($taxonomy, self::POST_TYPE, [
                'label'        => $label,
                'public'       => true,
                'show_ui'      => false,
                //'show_ui'      => true,
                'show_in_rest' => true,
                'hierarchical' => true
            ]);
        }
    }

    public function render_metabox()
    {
        add_meta_box(
            'degree_program_metabox',
            __('Post Meta German', 'fau-studium-display'),
            [$this, 'degree_program_metabox'],
            'degree-program',
            'normal',
            'high'
        );

        add_meta_box(
            'degree_program_metabox_english',
            __('Post Meta English', 'fau-studium-display'),
            [$this, 'degree_program_metabox_english'],
            'degree-program',
            'normal',
            'high'
        );
    }

    public function degree_program_metabox($post)
    {
        $aPostMeta = get_post_meta($post->ID, 'program_data_de', true);
        if (!isset($aPostMeta['title'])) {
            echo __('No data available', 'fau-studium-display');
            return;
        }
        foreach ($aPostMeta as $key => $value) {
            echo '<span><strong>' . $key . ':</strong></span><br />' . Utils::arrayToHtmlList($value) . '<hr>';
        }
    }

    public function degree_program_metabox_english($post)
    {
        $aPostMeta = get_post_meta($post->ID, 'program_data_en', true);
        if (!isset($aPostMeta['title'])) {
            echo __('No data available', 'fau-studium-display');
            return;
        }
        foreach ($aPostMeta as $key => $value) {
            echo '<span><strong>' . $key . ':</strong></span><br />' . Utils::arrayToHtmlList($value) . '<hr>';
        }
    }

    public function disable_new_posts()
    {
        global $submenu;
        unset($submenu['edit.php?post_type=degree-program'][10]);
    }

    public function register_custom_query_vars( $vars ) {
        $vars[] = 'search';
        $vars[] = 'subject_group';
        $vars[] = 'degree';
        $vars[] = 'attribute';
        $vars[] = 'admission_requirements';
        $vars[] = 'semester';
        $vars[] = 'study_location';
        $vars[] = 'teaching_language';
        $vars[] = 'faculty';
        $vars[] = 'german_language_skills_for_international_students';
        return $vars;
    }

    public function archive_redirect_canonical($redirect_url, $requested_url) {
        if (is_post_type_archive('degree-program') && !empty($_GET)) {
            return false;
        }
        return $redirect_url;
    }

    public function preserve_archive_filters(array $query_vars): array
    {
        if (is_admin()) {
            return $query_vars;
        }

        $postTypes = isset($query_vars['post_type']) ? (array) $query_vars['post_type'] : [];
        if (!in_array(self::POST_TYPE, $postTypes, true)) {
            return $query_vars;
        }

        $filterKeys = [
            'attribute',
            'subject_group',
            'degree',
            'admission_requirements',
            'semester',
            'study_location',
            'teaching_language',
            'faculty',
            'german_language_skills_for_international_students',
        ];

        foreach ($filterKeys as $key) {
            if (isset($query_vars[$key]) && isset($_GET[$key]) && is_array($_GET[$key])) {
                unset($query_vars[$key]);
            }
        }

        return $query_vars;
    }

    public function modify_canonical_url( $canonical_url, $post ) {
        if ($post->post_type == self::POST_TYPE) {
            $domain = $this->site_language == 'de' ? 'https://fau.de/studiengang/' : 'https://fau.eu/degree-program/';
            $canonical_url = $domain . basename(get_permalink());
        }
        return $canonical_url;
    }

    public function remove_quick_edit_for_degree_program( $actions, $post ) {
        if ( $post->post_type == self::POST_TYPE ) {
            unset( $actions['inline hide-if-no-js'] ); // Quick Edit
            unset( $actions['inline'] );               // fallback
        }
        return $actions;
    }

}