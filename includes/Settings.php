<?php

namespace Fau\DegreeProgram\Display;

class Settings
{
    protected string $pluginFile;
    private array $tabs;
    private string $title;
    private string $slug;

    public function __construct($pluginFile) {
        $this->pluginFile = $pluginFile;
        $this->title = plugin()->getName();
        $this->slug = plugin()->getSlug();
    }

    public function onLoaded() {
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('cmb2_admin_init', [$this, 'register_settings']);
        add_action('cmb2_render_sync-search', [$this, 'render_sync_search' ], 10, 5);
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
            'layout' => __('Layout', 'fau-studium-display'),
        ];

        echo '<div class="wrap cmb2-options-page">';
        echo '<h1>' . get_admin_page_title() . '</h1>';
        echo '<h2 class="nav-tab-wrapper">';
        foreach ($tabs as $key => $value) {
            echo '<a href="?page=' . $this->slug . '&tab=' . $key . '" class="nav-tab ' . ($active_tab == $key ? 'nav-tab-active' : '') . '">' . $value . '</a>';
        }
        echo '</h2>';

        switch ($active_tab) {
            case 'api':
                cmb2_metabox_form($this->slug.'_api', $this->slug);
                break;
            case 'sync':
                cmb2_metabox_form($this->slug.'_sync', $this->slug);
                break;
            case 'layout':
            default:
                cmb2_metabox_form($this->slug.'_layout', $this->slug);
                break;
        }

        echo '</div>';
    }
    
    public function register_settings() {
        $api_options = new_cmb2_box(array(
            'id' => $this->slug.'_api',
            'title' => __('API', 'fau-studium-display'),
            'object_types' => array('options-page'),
            'option_key' => $this->slug.'_api',
            'parent_slug' => $this->slug,
        ));
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
            'object_types' => array('options-page'),
            'option_key' => $this->slug.'_sync',
            'parent_slug' => $this->slug,
        ]);
        $sync_options->add_field( array(
            'name' => __('Search for degree programs to import or sync', 'fau-studium-display'),
            //'desc' => __('', 'fau-studium-display'),
            'type' => 'title',
            'id'   => 'apply_now_heading'
        ) );
        $sync_options->add_field([
            'id' => 'sync-settings',
            'name' => esc_html__('Search', 'fau-studium-display'),
            //'desc' => '',
            'type' => 'sync-search',
            'default' => '',
        ]);

        $layout_options = new_cmb2_box([
            'id' => $this->slug.'_layout',
            'title' => __('Layout', 'fau-studium-display'),
            'object_types' => array('options-page'),
            'option_key' => $this->slug.'_layout',
            'parent_slug' => $this->slug,
        ]);
        $layout_options->add_field( array(
            'name' => __('Section "Apply now"', 'fau-studium-display'),
            //'desc' => __('', 'fau-studium-display'),
            'type' => 'title',
            'id'   => 'apply_now_heading'
        ) );
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
           'text'    => array(
               'add_upload_file_text' => __('Add or Upload File', 'fau-studium-display'),
           ),
           'query_args' => [
               'type' => [
                	'image/gif',
                	'image/jpeg',
                	'image/png',
               ],
           ],
           'preview_size' => 'large',
        ]);
        $layout_options->add_field( array(
            'name' => __('Section "More Information for International Applicants"', 'fau-studium-display'),
            //'desc' => __('', 'fau-studium-display'),
            'type' => 'title',
            'id'   => 'internationals_heading'
        ) );
        $layout_options->add_field([
           'id' => 'internationals-image',
           'name' => esc_html__('Image', 'fau-studium-display'),
           //'desc' => __('', 'fau-studium-display'),
           'type' => 'file',
           //'default' => '',
           'options' => [
               'url' => false, // Hide the text input for the url
           ],
           'text'    => array(
               'add_upload_file_text' => __('Add or Upload File', 'fau-studium-display'),
           ),
           'query_args' => [
               'type' => [
                   'image/gif',
                   'image/jpeg',
                   'image/png',
               ],
           ],
           'preview_size' => 'large',
       ]);
        $layout_options->add_field( array(
            'name' => __('Section "Student Advice"', 'fau-studium-display'),
            //'desc' => __('', 'fau-studium-display'),
            'type' => 'title',
            'id'   => 'student_advice_heading'
        ) );
        $layout_options->add_field([
           'id' => 'general-student-advice-general-image',
           'name' => esc_html__('Image for Student Advice Center', 'fau-studium-display'),
           //'desc' => __('', 'fau-studium-display'),
           'type' => 'file',
           //'default' => '',
           'options' => [
               'url' => false, // Hide the text input for the url
           ],
           'text'    => array(
               'add_upload_file_text' => __('Add or Upload File', 'fau-studium-display'),
           ),
           'query_args' => [
               'type' => [
                   'image/gif',
                   'image/jpeg',
                   'image/png',
               ],
           ],
           'preview_size' => 'large',
        ]);
        $layout_options->add_field([
           'id' => 'specific-student-advice-specific-image',
           'name' => esc_html__('Image for Specific Student Advice', 'fau-studium-display'),
           //'desc' => __('', 'fau-studium-display'),
           'type' => 'file',
           //'default' => '',
           'options' => [
               'url' => false, // Hide the text input for the url
           ],
           'text'    => array(
               'add_upload_file_text' => __('Add or Upload File', 'fau-studium-display'),
           ),
           'query_args' => [
               'type' => [
                   'image/gif',
                   'image/jpeg',
                   'image/png',
               ],
           ],
           'preview_size' => 'large',
       ]);

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
        echo '</div><div class="">'
             . '<h4>' . __('Select Language', 'fau-studium-display') . '</h4>';
        $languages = [
            'de' => __('German', 'fau-studium-display'),
            'en' => __('English', 'fau-studium-display'),
        ];
        foreach ($languages as $value => $label) {
            echo '<label><input type="radio" name="language" value="' . $value . '">' . $label . '</label><br />';
        }
        echo '</div></div>';
        echo '<button id="degree-search-button" class="button">' . __('Search', 'fau-studium-display') . '</button>';
        echo '<div id="degree-program-results"></div>';
    }


    function ajaxProgramSearch() {

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Keine Berechtigung');
        }
        check_ajax_referer('fau_studium_display_admin_ajax_nonce');

        $faculties = $_POST[ 'faculties' ] ?? [];
        $degrees = $_POST[ 'degrees' ] ?? [];
        $language = $_POST[ 'language' ] ?? 'de';

        if (!is_array($faculties) || !is_array($degrees) || (!in_array($language, ['de', 'en']))) {
            wp_send_json_error('Ungültige Daten');
        }

        $atts['language'] = $language;
        $atts['selectedFaculties'] = $faculties;
        $atts['selectedDegrees'] = $degrees;
        $data = new Data();
        $programs = $data->get_data($atts);
        $api = new API();
        $programs = $api->get_programs();

        $output = '';
        foreach ($programs as $program) {
            $programs_imported = get_posts([
                   'post_type'      => 'degree-program',
                   'posts_per_page' => -1,
                   'post_status'    => 'publish',
                   'meta_query'     => [
                       ['key'       => 'id',
                        'value' => $program['id']],
                       ['key'       => 'lang',
                        'value' => $language,]
                   ],
               ]);
            if (empty($programs_imported)) {
                $button = [
                    'task' => 'add',
                    'post_id' => '0',
                    'label' => __('Add', 'fau-studium-display'),
                    'icon'  => 'dashicons-plus',
                ];
                $button_delete = [];
            } else {
                $button = [
                    'task' => 'update',
                    'post_id' => $programs_imported[0]->ID,
                    'label' => __('Update', 'fau-studium-display'),
                    'icon'  => 'dashicons-update',
                ];
                $button_delete = [
                    'task' => 'delete',
                    'post_id' => $programs_imported[0]->ID,
                    'label' => __('Delete', 'fau-studium-display'),
                    'icon'  => 'dashicons-trash',
                ];
            }
            $output .= '<div class="program-item ' . $button['task'] . '-program">'
                       . '<div class="program-title">' . $program['title']. ' (' . $program['degree']['abbreviation'] . ')</div>'
                       . '<div class="program-buttons"><a class="add-degree-program button" data-id="' . $program['id'] . '" data-task="' . $button['task'] . '" data-post_id="' . $button['post_id'] . '"><span class="dashicons ' . $button['icon'] . '"></span> ' . $button['label'] . '</a>'
                       . (!empty($button_delete) ? '<a class="delete-degree-program button" data-id="' . $program['id'] . '" data-task="' . $button_delete['task'] . '" data-post_id="' . $button['post_id'] . '"><span class="dashicons ' . $button_delete['icon'] . '"></span> ' . $button_delete['label'] . '</a>' : '')
                       . '</div>'
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