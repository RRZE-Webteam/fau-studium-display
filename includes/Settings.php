<?php

namespace Fau\DegreeProgram\Display;

use function Fau\DegreeProgram\Display\Config\get_labels;
use function Fau\DegreeProgram\Display\Config\get_output_fields;

class Settings
{
    protected string $pluginFile;
    private array $tabs;
    private string $title;
    private string $slug;
    private array $programs = array();
    private bool $fau_studium_active;

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
        $this->fau_studium_active = is_plugin_active('FAU-Studium/fau-degree-program.php');
    }

    public function onLoaded() {
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('cmb2_admin_init', [$this, 'register_settings']);
        add_action('cmb2_render_sync-search', [$this, 'render_sync_search' ], 10, 5);
        add_action('cmb2_render_sync-imported', [$this, 'render_sync_imported' ], 10, 5);
        if (!has_action('cmb2_render_toggle')) {
            add_action( 'cmb2_render_toggle', [$this, 'render_toggle' ], 10, 5 );
        }
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
        $tabs['layout'] = __('Layout', 'fau-studium-display');
        if (!$this->fau_studium_active) {
            $tabs['sync'] = __('Sync', 'fau-studium-display');
        }

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
            $active_tab = 'layout';
        }

        cmb2_metabox_form(
            $this->slug . '_' . $active_tab,
            $this->slug . '_' . $active_tab,
            $metabox_args
        );

        echo '</div>';
    }
    
    public function register_settings() {
        $output_fields = get_output_fields();
        $labels = get_labels();
        $layout_options = new_cmb2_box([
                                           'id' => $this->slug.'_layout',
                                           'title' => __('Layout', 'fau-studium-display'),
                                           'object_types' => ['options-page'],
                                           'option_key' => $this->slug.'_layout',
                                           'parent_slug' => $this->slug,
                                       ]);
        $layout_options->add_field( [
                                        'name' => __('Archive View', 'fau-studium-display'),
                                        'desc' => __('What format should the list view use?', 'fau-studium-display'),
                                        'type' => 'title',
                                        'id'   => 'archive_view_heading'
                                    ] );
        $layout_options->add_field([
                                       'id' => 'archive_search',
                                       'name' => __('Show Search', 'fau-studium-display'),
                                       'type' => 'toggle',
                                   ]);
        $search_filters = $output_fields['search-filters'];
        $search_filter_options = [];
        foreach ($search_filters as $search_filter) {
            $search_filter_options[$search_filter] = $labels[$search_filter];
        }
        $layout_options->add_field([
                                       'id' => 'archive_search_filters',
                                       'name' => esc_html__('Show/Hide Search Filters', 'fau-studium-display'),
                                       //'desc' => __('', 'fau-studium-display'),
                                       'type' => 'multicheck',
                                       'options' => $search_filter_options,
                                       'default' => $search_filters,
                                       'select_all_button' => __('Select / Deselect All', 'fau-studium-display'),
                                   ]);
        $layout_options->add_field([
                                       'id' => 'archive_view',
                                       'name' => esc_html__('Archive Format', 'fau-studium-display'),
                                       'type' => 'radio',
                                       'options' => [
                                           'grid' => __('Grid', 'fau-studium-display'),
                                           'table' => __('Table', 'fau-studium-display'),
                                       ],
                                       'default' => 'grid',
                                   ]);
        $grid_view_fields = $output_fields['grid'];
        $grid_view_options = [];
        foreach ($grid_view_fields as $archive_view_field) {
            $grid_view_options[$archive_view_field] = $labels[$archive_view_field];
        }
        $layout_options->add_field([
                                       'id' => 'grid_items',
                                       'name' => esc_html__('Show/Hide Grid Items', 'fau-studium-display'),
                                       //'desc' => __('', 'fau-studium-display'),
                                       'type' => 'multicheck',
                                       'options' => $grid_view_options,
                                       'default' => $grid_view_fields,
                                       'select_all_button' => __('Select / Deselect All', 'fau-studium-display'),
                                   ]);
        $layout_options->add_field( [
                                        'name' => __('Single View', 'fau-studium-display'),
                                        'desc' => __('Select the elements to be shown in single view', 'fau-studium-display'),
                                        'type' => 'title',
                                        'id'   => 'single_view_heading'
                                    ] );
        $single_view_fields = $output_fields['full'];
        $single_view_options = [];
        foreach ($single_view_fields as $single_view_field) {
            $single_view_options[$single_view_field] = $labels[$single_view_field];
        }
        $layout_options->add_field([
                                       'id' => 'single_items',
                                       'name' => esc_html__('Show/Hide Single Items', 'fau-studium-display'),
                                       //'desc' => __('', 'fau-studium-display'),
                                       'type' => 'multicheck',
                                       'options' => $single_view_options,
                                       'default' => $single_view_fields,
                                       'select_all_button' => __('Select / Deselect All', 'fau-studium-display'),
                                   ]);

        if (!$this->fau_studium_active) {
            //Search for degree programs to import
            $sync_options = new_cmb2_box([
                                             'id'           => $this->slug . '_sync',
                                             'title'        => __('Sync', 'fau-studium-display'),
                                             'object_types' => ['options-page'],
                                             'option_key'   => $this->slug . '_sync',
                                             'parent_slug'  => $this->slug,
                                         ]);
            $sync_options->add_field([
                                         'name' => __('Sync and manage degree programs', 'fau-studium-display'),
                                         //'desc' => __('', 'fau-studium-display'),
                                         'type' => 'title',
                                         'id'   => 'apply_now_heading'
                                     ]);
            $sync_options->add_field([
                                         'id'      => 'sync-search',
                                         'name'    => esc_html__('Import', 'fau-studium-display'),
                                         //'desc' => '',
                                         'type'    => 'sync-search',
                                         'default' => '',
                                     ]);
            $sync_options->add_field([
                                         'id'      => 'sync-imported',
                                         'name'    => esc_html__('Manage Imported', 'fau-studium-display'),
                                         //'desc' => '',
                                         'type'    => 'sync-imported',
                                         'default' => '',
                                     ]);
        }

        wp_enqueue_style('fau-studium-display-admin');
        wp_enqueue_script('fau-studium-display-admin');
    }

    public function render_sync_search() {
        $facultyOptions = Utils::get_faculty_options();
        $degreeOptions = Utils::get_degree_options(true);

        echo '<div class="" style="display:flex; flex-direction: row; flex-wrap: wrap; column-gap: 2em;">'
             . '<div class="">'
             . '<h4>' . __('Select Faculty (optional)', 'fau-studium-display') . '</h4>';
        foreach ($facultyOptions as $facultyOption) {
            echo '<label><input type="checkbox" name="faculty[]" value="' . $facultyOption['value'] . '">' . $facultyOption['label'] . '</label><br />';
        }
        echo '</div><div class="">'
             . '<h4>' . __('Select Degree (optional)', 'fau-studium-display') . '</h4>';
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

    /*
         * CMB2 Toggle
         * Source: https://kittygiraudel.com/2021/04/05/an-accessible-toggle/
         */
    public function render_toggle( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
        $field_name = $field->_name();

        $return_value = 'on';

        if ( $field->args( 'return_value' ) && ! empty( $field->args( 'return_value' ) ) ) {
            $return_value = $field->args( 'return_value' );
        }

        $args = array(
            'type'  => 'checkbox',
            'id'    => $field_name,
            'name'  => $field_name,
            'desc'  => '',
            'value' => $return_value,
        );

        echo '<label class="cmb2-toggle" for="' . esc_attr( $args['id'] ) . '">
  <input type="checkbox" name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( $args['id'] ) . '" value="' . esc_attr( $return_value ) . '" class="Toggle__input" ' . checked( $escaped_value, $return_value, false ) . ' />

  <span class="Toggle__display" hidden>
    <svg
      aria-hidden="true"
      focusable="false"
      class="Toggle__icon Toggle__icon--checkmark"
      width="18"
      height="14"
      viewBox="0 0 18 14"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
    >
      <path
        d="M6.08471 10.6237L2.29164 6.83059L1 8.11313L6.08471 13.1978L17 2.28255L15.7175 1L6.08471 10.6237Z"
        fill="currentcolor"
        stroke="currentcolor"
      />
    </svg>
    <svg
      aria-hidden="true"
      focusable="false"
      class="Toggle__icon Toggle__icon--cross"
      width="13"
      height="13"
      viewBox="0 0 13 13"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
    >
      <path
        d="M11.167 0L6.5 4.667L1.833 0L0 1.833L4.667 6.5L0 11.167L1.833 13L6.5 8.333L11.167 13L13 11.167L8.333 6.5L13 1.833L11.167 0Z"
        fill="currentcolor"
      />
    </svg>
  </span>

  <span class="screen-reader-text"> ' . esc_html($field->args['name']) . '</span>
</label>';

        $field_type_object->_desc( true, true );
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

        $atts['faculty'] = $faculties;
        $atts['degree'] = $degrees;

        $api = new API();
        $programs = $api->get_programs(false, $atts);

        global $wpdb;
        $imported_ids = $wpdb->get_col( "
            SELECT pm.meta_value
            FROM {$wpdb->postmeta} pm
            INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
            WHERE pm.meta_key = 'program_id'
            AND p.post_type = 'degree-program'
            AND p.post_status = 'publish'
        " );

        $output = '<button id="import-select-all-button" class="button">' . __('Select / Deselect All', 'fau-studium-display') . '</button>'
            . '<button id="import-selected-button" class="button button-primary">' . __('Import selected', 'fau-studium-display') . '</button>';
        foreach ($programs as $program) {
            if (in_array($program['id'], $imported_ids)) continue;

            $output .= '<div class="program-item add-program">'
                       . '<div class="program-check"><input type="checkbox" value="1" name="batch-import[' . $program['id'] . ']" id="batch-import' . $program['id'] . '">' . '</div>'
                       . '<div class="program-title"><label for="batch-import' . $program['id'] . '">' . $program['title']. ' (' . $program['degree']['abbreviation'] . ')</label></div>'
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
        $sync->sync_program($program_id, $post_id);

        wp_send_json_success($program_id, $post_id);
    }

    public function ajaxProgramDelete() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Keine Berechtigung');
        }
        check_ajax_referer('fau_studium_display_admin_ajax_nonce');

        if (empty($_POST[ 'post_id' ])) {
            wp_send_json_error('Post ID existiert nicht.');
        }

        $program_id = get_post_meta( $_POST[ 'post_id' ], 'id', true );
        delete_transient('fau_studium_degree_program_' . $program_id);

        wp_delete_post( $_POST[ 'post_id' ], true );

        wp_send_json_success();
    }

}