<?php

namespace Fau\DegreeProgram\Display;

defined('ABSPATH') || exit;

class CPT
{
    const POST_TYPE = 'degree-program';

    public function __construct()
    {
        add_action( 'init', [$this, 'register_post_type'] );
        add_action('add_meta_boxes', [$this, 'render_metabox']);
        add_action('admin_menu', [$this, 'disable_new_posts']);
        //add_filter('single_template', [__CLASS__, 'include_single_template']);
        //add_filter('archive_template', [__CLASS__, 'include_archive_template']);
        add_filter('the_content', [$this, 'replace_content_with_cpt_content']);

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
        $aPostMeta = get_post_meta($post->ID);

        foreach ($aPostMeta as $key => $aEntry) {
            if (str_starts_with($key, '_')) continue;
            $val = (is_serialized($aEntry[0]) ? unserialize($aEntry[0]) : $aEntry[0]);

            echo '<span><strong>' . $key . ':</strong></span><br />' . Utils::arrayToHtmlList($val) . '<hr>';
        }
    }

    public function degree_program_metabox_english($post)
    {
        $translations = get_post_meta($post->ID, 'translations', true);
        $en = $translations['en'];
        foreach ($en as $key => $value) {
            $value_formatted = (is_serialized($value) ? unserialize($value) : $value);

            echo '<span><strong>' . $key . ':</strong></span><br />' . Utils::arrayToHtmlList($value_formatted) . '<hr>';
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

    public static function include_single_template($template_path)
    {
        global $post;

        if (!($post->post_type == 'degree-program')) {
            return $template_path;
        }

        $template_path = plugin()->getPath() . 'templates/single-degree-program.php';

        wp_enqueue_style('fau-studium-display');

        return $template_path;
    }

    public static function include_archive_template($template_path)
    {
        global $post;
        if ($post->post_type == 'degree-program' && is_archive()) {
            if ($theme_file = locate_template(array('archive-degree-program.php'))) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin()->getPath() . '/templates/archive-degree-program.php';
            }
        }

        wp_enqueue_style('fau-studium-display');

        return $template_path;
    }

    function replace_content_with_cpt_content($content) {
        if (is_singular('degree-program')) {
            global $post;
            $atts = [
                'format' => 'full',
                'degreeProgram' => $post->ID,
                'post_id' => $post->ID,
                'selectedItemsFull' => [
                    'standard_duration',
                    'start',
                    'number_of_students',
                    'teaching_language',
                    'attributes',
                    'degree',
                    'faculty',
                    'location',
                    'subject_groups',
                    'videos',
                    'content.structure',
                    'content.specializations',
                    'content.qualities_and_skills',
                    'content.why_should_study',
                    'content.career_prospects',
                    'admission_requirement_link',
                    'details_and_notes',
                    'start_of_semester',
                    'semester_dates',
                    'examinations_office',
                    'examination_regulations',
                    'module_handbook',
                    'url',
                    'department',
                    'student_advice',
                    'subject_specific_advice',
                    'service_centers',
                    'info_brochure',
                    'semester_fee',
                    'abroad_opportunities',
                    'keywords',
                    'area_of_study',
                    'combinations',
                    'limited_combinations',
                    'notes_for_international_applicants',
                    'student_initiatives',
                    'apply_now_link',
                    'content_related_master_requirements',
                    'application_deadline_winter_semester',
                    'application_deadline_summer_semester',
                    'language_skills',
                    'language_skills_humanities_faculty',
                    'german_language_skills_for_international_students',
                    'degree_program_fees',
                    'content.about',
                    'content.special_features',
                    'content.testimonials',
                    'admission_requirements',
                    'fact_sheet',
                    'teaser_image',
                    'title',
                    'subtitle',
                    'entry_text',
                    'admission_requirements_application',
                    'admission_requirements_application_internationals',
                    'links.organizational',
                    'links.downloads',
                    'links.additional_information'
                ]
            ];
            $output = new Output();
            $content = $output->renderOutput($atts);
        }

        return $content;
    }

}