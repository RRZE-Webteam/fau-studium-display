<?php

namespace Fau\DegreeProgram\Display;


class Settings {

    protected string $pluginFile;
    private string $title;
    private string $slug;
    private $options;

    public function __construct($pluginFile) {
        $this->pluginFile = $pluginFile;
    }

    public function onLoaded() {
        add_action('admin_menu', [$this,'adminMenu']);
        add_action('admin_init', [$this,'adminInit']);
        add_action('wp_ajax_program_search', [$this,'ajaxProgramSearch']);
        add_action('wp_ajax_program_sync', [$this,'ajaxProgramSync']);
        add_action('wp_ajax_program_delete', [$this,'ajaxProgramDelete']);
        $this->title = plugin()->getName();
        $this->slug = plugin()->getSlug();
        $this->options = get_option('fau-studium-display', '');
    }

    public function adminMenu() {
        add_options_page(
            $this->title . ': ' . __('Settings', 'fau-studium-display'),
            $this->title,
            'manage_options',
            $this->slug,
            [$this, 'settingsPage']
        );
    }

    public function settingsPage() {
        ?>
        <div class="wrap">
            <h1><?php echo $this->title . ': ' . __('Settings', 'fau-studium-display'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('fau-studium-display-options');
                do_settings_sections($this->slug);
                submit_button();
                ?>
            </form>
        </div>
        <?php
        wp_enqueue_style('fau-studium-display-admin');
        wp_enqueue_script('fau-studium-display-admin');
    }

    public function adminInit() {
        register_setting('fau-studium-display-options', 'fau-studium-display');

        add_settings_section(
            'fau-studium-display-api',
            __('API', 'fau-studium-display'),
            '',
            $this->slug
        );

        add_settings_field(
            'dip-edu-api-key',
            __('DIP Edu API Key', 'fau-studium-display'),
            [$this, 'textCallback'],
            $this->slug,
            'fau-studium-display-api'
        );

        add_settings_section(
            'fau-studium-display-import-programs',
            __('Import', 'fau-studium-display'),
            '',
            $this->slug
        );

        add_settings_field(
            'search',
            __('Search for degree programs to import', 'fau-studium-display'),
            [$this, 'importProgramsCallback'],
            $this->slug,
            'fau-studium-display-import-programs'
        );
    }

    public function textCallback(): void
    {
        
        if (API::isUsingNetworkKey()) {
            echo '<p>' . esc_html__('The API key is being used from the network installation.', 'fau-studium-display') . '</p>';
        } else {
            $value = $this->options[ 'dip-edu-api-key' ] ?? '';
            echo '<input type="text" class="regular-text" id="dip-edu-api-key" name="fau-studium-display[dip-edu-api-key]" value="' . esc_attr($value) . '" />';
            echo '<p class="description">'.__('API Key can be obtained from the API-Service at <a href="https://api.fau.de/pub/v1/edu/__doc">https://api.fau.de/pub/v1/edu/__doc</a>.', 'fau-studium-display').'</p>';
            
        }
    }

    public function importProgramsCallback() {
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