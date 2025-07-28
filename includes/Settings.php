<?php

namespace Fau\DegreeProgram\Display;

class Settings
{
    protected string $pluginFile;
    private array $tabs;
    private string $title;
    private string $slug;

    private array $programs = array();

    public function __construct($pluginFile) {
        $this->pluginFile = $pluginFile;
        $this->title = plugin()->getName();
        $this->slug = plugin()->getSlug();
        $this->programs = get_posts([
            'post_type'      => 'degree-program',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'orderby'        => 'title',
            'order'          => 'ASC',
        ]);
    }

    public function onLoaded() {
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('cmb2_admin_init', [$this, 'register_settings']);
        add_action('cmb2_render_sync-search', [$this, 'render_sync_search' ], 10, 5);
        add_action('cmb2_render_sync-imported', [$this, 'render_sync_imported' ], 10, 5);
        add_action('wp_ajax_program_search', [$this,'ajaxProgramSearch']);
        add_action('wp_ajax_program_sync', [$this,'ajaxProgramSync']);
        add_action('wp_ajax_program_delete', [$this,'ajaxProgramDelete']);
    }


    public function add_settings_page() {
        add_options_page(
            $this->title . ': ' . __('Settings', 'fau-studium-display'),
            $this->title,
            'manage_options',
            $this->slug,
            [$this, 'render_settings_page']
        );
    }


    public function render_settings_page() {
        $active_tab = $_GET['tab'] ?? 'layout';
        $tabs = [
            'api' => __('API', 'fau-studium-display'),
            'sync' => __('Sync', 'fau-studium-display'),
            //'layout' => __('Layout', 'fau-studium-display'),
        ];

        echo '<div class="wrap cmb2-options-page">';
        echo '<h1>' . get_admin_page_title() . '</h1>';
        echo '<h2 class="nav-tab-wrapper">';
        foreach ($tabs as $key => $value) {
            echo '<a href="?page=' . $this->slug . '&tab=' . $key . '" class="nav-tab ' . ($active_tab == $key ? 'nav-tab-active' : '') . '">' . $value . '</a>';
        }
        echo '</h2>';

        $metabox_args = [
            'save_button'   => __('Save changes', 'fau-studium-display'),
        ];

        if (!array_key_exists($active_tab, $tabs)) {
            $active_tab = 'sync'; // Fallback
        }

        cmb2_metabox_form(
            $this->slug . '_' . $active_tab,
            $this->slug . '_' . $active_tab,
            $metabox_args
        );

        echo '</div>';
    }
    
    public function register_settings() {
        $api_options = new_cmb2_box([
            'id' => $this->slug.'_api',
            'title' => __('API', 'fau-studium-display'),
            'object_types' => ['options-page'],
            'option_key' => $this->slug.'_api',
            'parent_slug' => $this->slug,
        ]);
        $api_options->add_field([
            'id' => 'dip-edu-api-key',
            'name' => esc_html__('DIP Edu API Key', 'fau-studium-display'),
            'desc' => wp_kses_post(__('API Key can be obtained from the API-Service at <a href="https://api.fau.de/pub/v1/edu/__doc">https://api.fau.de/pub/v1/edu/__doc</a>.', 'fau-studium-display')),
            'type' => 'text',
            'default' => '',
        ]);
        //Search for degree programs to import
        $sync_options = new_cmb2_box([
            'id' => $this->slug.'_sync',
            'title' => __('Sync', 'fau-studium-display'),
            'object_types' => ['options-page'],
            'option_key' => $this->slug.'_sync',
            'parent_slug' => $this->slug,
        ]);
        $sync_options->add_field( [
            'name' => __('Sync and manage degree programs', 'fau-studium-display'),
            //'desc' => __('', 'fau-studium-display'),
            'type' => 'title',
            'id'   => 'apply_now_heading'
        ]);
        $sync_options->add_field([
            'id' => 'sync-search',
            'name' => esc_html__('Import', 'fau-studium-display'),
            //'desc' => '',
            'type' => 'sync-search',
            'default' => '',
        ]);
        $sync_options->add_field([
            'id' => 'sync-imported',
            'name' => esc_html__('Manage Imported', 'fau-studium-display'),
            //'desc' => '',
            'type' => 'sync-imported',
            'default' => '',
        ]);

        /*$layout_options = new_cmb2_box([
            'id' => $this->slug.'_layout',
            'title' => __('Layout', 'fau-studium-display'),
            'object_types' => ['options-page'],
            'option_key' => $this->slug.'_layout',
            'parent_slug' => $this->slug,
        ]);
        $layout_options->add_field( [
            'name' => __('Section "Apply now"', 'fau-studium-display'),
            //'desc' => __('', 'fau-studium-display'),
            'type' => 'title',
            'id'   => 'apply_now_heading'
        ] );
        $layout_options->add_field([
            'id' => 'apply-now-title',
            'name' => esc_html__('Title', 'fau-studium-display'),
            //'desc' => __('', 'fau-studium-display'),
            'type' => 'text',
            'default' => __('Apply now!', 'fau-studium-display'),
        ]);
        $layout_options->add_field([
           'id' => 'apply-now-text',
           'name' => esc_html__('Text', 'fau-studium-display'),
           //'desc' => __('', 'fau-studium-display'),
           'type' => 'textarea_small',
           'default' => __('on campo, the FAU application portal', 'fau-studium-display'),
        ]);
        $layout_options->add_field([
           'id' => 'apply-now-link-text',
           'name' => esc_html__('Link Text', 'fau-studium-display'),
           //'desc' => __('', 'fau-studium-display'),
           'type' => 'text',
           //'default' => __('', 'fau-studium-display'),
        ]);
        $layout_options->add_field([
           'id' => 'apply-now-link-url',
           'name' => esc_html__('Link URL', 'fau-studium-display'),
           //'desc' => __('', 'fau-studium-display'),
           'type' => 'text_url',
           //'default' => __('', 'fau-studium-display'),
        ]);
        $layout_options->add_field([
           'id' => 'apply-now-image',
           'name' => esc_html__('Image', 'fau-studium-display'),
           //'desc' => __('', 'fau-studium-display'),
           'type' => 'file',
           //'default' => '',
           'options' => [
               'url' => false, // Hide the text input for the url
           ],
           'text'    => [
               'add_upload_file_text' => __('Add or Upload File', 'fau-studium-display'),
           ],
           'query_args' => [
               'type' => [
                	'image/gif',
                	'image/jpeg',
                	'image/png',
               ],
           ],
           'preview_size' => 'large',
        ]);
        $layout_options->add_field( [
            'name' => __('Section "More Information for International Applicants"', 'fau-studium-display'),
            //'desc' => __('', 'fau-studium-display'),
            'type' => 'title',
            'id'   => 'internationals_heading'
        ] );
        $layout_options->add_field([
           'id' => 'internationals-image',
           'name' => esc_html__('Image', 'fau-studium-display'),
           //'desc' => __('', 'fau-studium-display'),
           'type' => 'file',
           //'default' => '',
           'options' => [
               'url' => false, // Hide the text input for the url
           ],
           'text'    => [
               'add_upload_file_text' => __('Add or Upload File', 'fau-studium-display'),
           ],
           'query_args' => [
               'type' => [
                   'image/gif',
                   'image/jpeg',
                   'image/png',
               ],
           ],
           'preview_size' => 'medium',
       ]);
        $layout_options->add_field( [
            'name' => __('Section "Student Advice"', 'fau-studium-display'),
            //'desc' => __('', 'fau-studium-display'),
            'type' => 'title',
            'id'   => 'student_advice_heading'
        ] );
        $layout_options->add_field([
           'id' => 'general-student-advice-image',
           'name' => esc_html__('Image for Student Advice Center', 'fau-studium-display'),
           //'desc' => __('', 'fau-studium-display'),
           'type' => 'file',
           //'default' => '',
           'options' => [
               'url' => false, // Hide the text input for the url
           ],
           'text'    => [
               'add_upload_file_text' => __('Add or Upload File', 'fau-studium-display'),
           ],
           'query_args' => [
               'type' => [
                   'image/gif',
                   'image/jpeg',
                   'image/png',
               ],
           ],
           'preview_size' => 'medium',
        ]);
        $layout_options->add_field([
           'id' => 'specific-student-advice-image',
           'name' => esc_html__('Image for Specific Student Advice', 'fau-studium-display'),
           //'desc' => __('', 'fau-studium-display'),
           'type' => 'file',
           //'default' => '',
           'options' => [
               'url' => false, // Hide the text input for the url
           ],
           'text'    => [
               'add_upload_file_text' => __('Add or Upload File', 'fau-studium-display'),
           ],
           'query_args' => [
               'type' => [
                   'image/gif',
                   'image/jpeg',
                   'image/png',
               ],
           ],
           'preview_size' => 'medium',
       ]);*/

        wp_enqueue_style('fau-studium-display-admin');
        wp_enqueue_script('fau-studium-display-admin');
    }

    public function render_sync_search() {
        $facultyOptions = Utils::get_faculty_options();
        $degreeOptions = Utils::get_degree_options(true);

        echo '<div class="" style="display:flex; flex-direction: row; flex-wrap: wrap; column-gap: 2em;">'
             . '<div class="">'
             . '<h4>' . __('Select Faculty', 'fau-studium-display') . '</h4>';
        foreach ($facultyOptions as $facultyOption) {
            echo '<label><input type="checkbox" name="faculty[]" value="' . $facultyOption['value'] . '">' . $facultyOption['label'] . '</label><br />';
        }
        echo '</div><div class="">'
             . '<h4>' . __('Select Degree', 'fau-studium-display') . '</h4>';
        foreach ($degreeOptions as $degreeOption) {
            echo '<label><input type="checkbox" name="degree[]" value="' . $degreeOption['value'] . '">' . $degreeOption['label'] . '</label><br />';
        }
        /*echo '</div><div class="">'
             . '<h4>' . __('Select Language', 'fau-studium-display') . '</h4>';
        $languages = [
            'de' => __('German', 'fau-studium-display'),
            'en' => __('English', 'fau-studium-display'),
        ];
        foreach ($languages as $value => $label) {
            echo '<label><input type="radio" name="language" value="' . $value . '"' . ($value == 'de' ? ' checked="checked"' : '') . '>' . $label . '</label><br />';
        }*/
        echo '</div></div>';
        echo '<button id="degree-search-button" class="button">' . __('Search', 'fau-studium-display') . '</button>';
        echo '<div id="degree-program-results"></div>';
    }

    public function render_sync_imported() {
        $buttons = [
            'update' => [
                'label' => __('Update', 'fau-studium-display'),
                'icon'  => 'dashicons-update',
            ],
            'delete' => [
                'label' => __('Delete', 'fau-studium-display'),
                'icon'  => 'dashicons-trash',
            ]
        ];
        echo '<div id="degree-programs-imported">';
        foreach ($this->programs as $program) {
            $title = $program->post_title;
            $program_id = get_post_meta($program->ID, 'id', true);
            echo '<div class="program-item">'
                . '<div class="program-title">' . $title . '</div>'
                . '<div class="program-buttons">';
            foreach ($buttons as $task => $button) {
                echo '<a class="' . $task . '-degree-program button" data-id="' . $program_id . '" data-task="' . $task . '" data-post_id="' . $program->ID . '"><span class="dashicons ' . $button['icon'] . '"></span> ' . $button['label'] . '</a>';
            }
            echo '</div></div>';
        }

        echo '</div>';
    }


    function ajaxProgramSearch() {

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Keine Berechtigung');
        }
        check_ajax_referer('fau_studium_display_admin_ajax_nonce');

        $faculties = $_POST[ 'faculties' ] ?? [];
        $degrees = $_POST[ 'degrees' ] ?? [];

        if (!is_array($faculties) || !is_array($degrees)) {
            wp_send_json_error('Ungültige Daten');
        }

        $atts['selectedFaculties'] = $faculties;
        $atts['selectedDegrees'] = $degrees;
        $data = new Data();
        $programs = $data->get_data($atts);
        $api = new API();
        $programs = $api->get_programs();

        global $wpdb;
        $imported_ids = $wpdb->get_col( "
            SELECT pm.meta_value
            FROM {$wpdb->postmeta} pm
            INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
            WHERE pm.meta_key = 'id'
            AND p.post_type = 'degree-program'
            AND p.post_status = 'publish'
        " );

        $output = '';
        foreach ($programs as $program) {
            if (in_array($program['id'], $imported_ids)) continue;

            $output .= '<div class="program-item add-program">'
                       . '<div class="program-title">' . $program['title']. ' (' . $program['degree']['abbreviation'] . ')</div>'
                       . '<div class="program-buttons"><a class="add-degree-program button" data-id="' . $program['id'] . '" data-task="sync" data-post_id="0"><span class="dashicons dashicons-plus"></span> ' . __('Add', 'fau-studium-display') . '</a></div>'
                       . '</div>';
        }

        $response['message'] = $output;
        wp_send_json_success($response);
    }

    public function ajaxProgramSync() {

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Keine Berechtigung');
        }
        check_ajax_referer('fau_studium_display_admin_ajax_nonce');

        $program_id = isset( $_POST[ 'program_id' ]) ? (int)$_POST[ 'program_id' ] : '';
        $post_id = $_POST[ 'post_id' ] ?? '0';

        $sync = new Sync();
        $result = $sync->sync_program($program_id, $post_id);

        wp_send_json_success();
    }

    public function ajaxProgramDelete() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Keine Berechtigung');
        }
        check_ajax_referer('fau_studium_display_admin_ajax_nonce');

        if (empty($_POST[ 'post_id' ])) {
            wp_send_json_error('Post ID existiert nicht.');
        }

        wp_delete_post( $_POST[ 'post_id' ], true );

        wp_send_json_success();
    }

}