<?php

namespace Fau\DegreeProgram\Display;

class Data
{
    public function get_data($atts) {

        $lang = !empty($atts['language']) && $atts['language'] == 'en' ? 'en' : 'de';

        if (isset($atts['format']) && in_array($atts['format'], ['full', 'box'])) {
            $data = $this->get_single_program((int)$atts['degreeProgram'], $lang, $atts['post_id'] ?? '');

        } else {

            $programs = $this->get_programs($lang);

            //$api = new API();
            //$programs = $api->get_programs('', false, $lang);

            // Filter from block settings
            $filterBlock = [];
            if (!empty($atts['selectedFaculties'])) {
                $filterBlock['faculty'] = $atts['selectedFaculties'];
            }
            if (!empty($atts['selectedDegrees'])) {
                $filterBlock['degree'] = $atts['selectedDegrees'];
            }
            if (!empty($atts['selectedSpecialWays'])) {
                $filterBlock['attribute'] = $atts['selectedSpecialWays'];
            }

            // Filter from $_GET parameters respecting block presets
            $getParams = isset($_GET) ? Utils::array_map_recursive('sanitize_text_field', $_GET) : [];
            $getParams = array_filter($getParams);
            $filter = array_merge($filterBlock, $getParams);
            $data = Utils::filterPrograms($programs, $filter);
        }

        return $data;
    }

    public function get_single_program($program_id, $lang, $post_id = '') {

        // if on meinstudium.fau.de -> get local post type (studiengang)
        if (is_plugin_active('FAU-Studium/fau-degree-program.php')) {
            return Utils::map_post_type_data($program_id, $lang);
        }

        // check if the program exists as imported CPT (degree-program)
        if (empty($post_id)) {
            $program_imported = get_posts([
                'post_type'      => 'degree-program',
                'post_status'    => 'publish',
                'meta_key'       => 'program_id',
                'meta_value' => $program_id
            ]);
            $post_id = $program_imported[0]->ID ?? '';
        }

        if (!empty($post_id)) {
            switch ($lang) {
                case 'en':
                    $data = get_post_meta($post_id, 'program_data_en', true);
                    break;
                case 'de':
                default:
                    $data = get_post_meta($post_id, 'program_data_de', true);
            }
            $data['_thumbnail_rendered'] = get_the_post_thumbnail($post_id, 'full');
            $data['post_id'] = $post_id;
            return $data;
        }

        // if not, fetch from API
        $api = new API();
        $program = $api->get_program($program_id);
        $sync = new Sync();
        $sync->sync_program($program_id);
        return $api->get_localized_data($program, $lang);
    }

    public function get_programs($lang = 'de') {
        $data = [];

        // if on meinstudium.fau.de -> get local post type (studiengang)
        if (is_plugin_active('FAU-Studium/fau-degree-program.php')) {
            $programs = get_posts([
                'post_type'      => 'studiengang',
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'orderby'        => 'title',
                'order'          => 'ASC'
            ]);
            foreach ($programs as $program) {
                $data[$program->ID] = Utils::map_post_type_data($program->ID, $lang);
            }
            return $data;
        }

        // else get imported degree programs (post type 'degree-program')
        $programs_imported = get_posts([
            'post_type'      => 'degree-program',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC'
        ]);

        if (!empty($programs_imported)) {
            switch ($lang) {
                case 'en':
                    foreach ($programs_imported as $program) {
                        $data[$program->ID] = get_post_meta($program->ID, 'program_data_en', true);
                        $data[$program->ID]['_thumbnail_rendered'] = get_the_post_thumbnail($program->ID, 'full');
                    }
                    return $data;
                case 'de':
                default:
                    foreach ($programs_imported as $program) {
                        $data[$program->ID] = get_post_meta($program->ID, 'program_data_de', true);
                        $data[$program->ID]['_thumbnail_rendered'] = get_the_post_thumbnail($program->ID, 'full');
                    }
                    return $data;
            }
        }
        return $data;
    }
}