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
            'fau-studium-display-layout',
            __('Layout', 'fau-studium-display'),
            '',
            $this->slug
        );

        add_settings_field(
            'show-errors',
            __('Show Errors', 'fau-studium-display'),
            [$this, 'radioCallback'],
            $this->slug,
            'fau-studium-display-layout'
        );
    }

    public function textCallback() {
        
        if (API::isUsingNetworkKey()) {
            echo '<p>' . esc_html__('The API key is being used from the network installation.', 'fau-studium-display') . '</p>';
        } else {
            $value = $this->options[ 'dip-edu-api-key' ] ?? '';
            echo '<input type="text" class="regular-text" id="dip-edu-api-key" name="fau-studium-display[dip-edu-api-key]" value="' . esc_attr($value) . '" />';
            echo '<p class="description">'.__('API Key can be obtained from the API-Service at <a href="https://api.fau.de/pub/v1/edu/__doc">https://api.fau.de/pub/v1/edu/__doc</a>.', 'fau-studium-display').'</p>';
            
        }
    }

    public function radioCallback() {
        $value = $this->options[ 'show-errors' ] ?? '';
        echo '<input type="checkbox" id="show-errors" name="fau-studium-display[show-errors]" value="on" ' . checked($value, 'on', false) . '><label for="show-errors">'.__('Show errors in frontend', 'fau-studium-display').'</label></p>';
        echo '<p class="description">' . __('If not checked, the shortcode will not show anything in case of error.', 'fau-studium-display') . '</p>';
    }

}