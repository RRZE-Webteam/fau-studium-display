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
        wp_enqueue_script('fau-studium-display-admin-ajax');
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
        $degreeOptions = Utils::get_degree_options();

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
        echo '<button id="mein-button">Test</button>';
        echo '<div id="degree-program-results"></div>';

        //var_dump($_REQUEST);
    }

    function ajaxProgramSearch() {
        // Prüfe Berechtigungen, z.B.:
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

        $response = ['message' => ''];

        $atts['lang'] = $language;
        $atts['selectedFaculties'] = $faculties;
        $atts['selectedDegrees'] = $degrees;
        //$programs = (new Data)->get_data($atts);
        //$api = new API();
        //$programs = $api->get_programs('', false, $language);
        //$response['message'] = serialize($programs);
        wp_send_json_success($response);
    }


}