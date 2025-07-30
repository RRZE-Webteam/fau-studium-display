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

        // Save IDs of active degree programs to transient
        /*if (function_exists('get_current_screen') && get_current_screen()->is_block_editor() == 1) {
            $transient_name = 'fau_studium_degree_programs_sync';
            $degree_programs = get_transient($transient_name);
            if (!$degree_programs) {
                $degree_programs = [$lang => []];
            }

            foreach ($data as $program) {
                if (!isset($program['id'])) continue;
                if (!in_array($program['id'], $degree_programs[$lang])) {
                    $degree_programs[$lang][] = $program['id'];
                }
            }
            set_transient($transient_name, $degree_programs, DAY_IN_SECONDS);
            //$sync = new Sync();
            //$sync->do_sync();
        }*/

        return $data;
    }

    public function get_single_program($program_id, $lang, $post_id = '') {

        // check if the program exists as CPT
        if (empty($post_id)) {
            $program_imported = get_posts([
                'post_type'      => 'degree-program',
                'post_status'    => 'publish',
                'meta_key'       => 'id',
                'meta_value' => $program_id
            ]);
            $post_id = $program_imported[0]->ID;
        }

        if (!empty($post_id)) {
            switch ($lang) {
                case 'en':
                    $translations = get_post_meta($post_id, 'translations', true);
                    $data = $translations['en'];
                    break;
                case 'de':
                default:
                    $data = [];
                    $post_meta = get_post_meta($post_id, '', true);
                    foreach ($post_meta as $key => $value) {
                        $data[$key] = is_serialized($value[0]) ? unserialize($value[0]) : $value[0];
                    }
            }
            $data['_thumbnail_rendered'] = get_the_post_thumbnail($post_id, 'full');
            return $data;
        }

        // if not, fetch from API
        $api = new API();
        $program = $api->get_program($program_id);
        return $api->get_localized_data($program, $lang);
    }

    public function get_programs($lang = 'de') {
        $programs_imported = get_posts([
            'post_type'      => 'degree-program',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
        ]);

        $data = [];
        if (!empty($programs_imported)) {
            switch ($lang) {
                case 'en':
                    foreach ($programs_imported as $program) {
                        $translations = get_post_meta($program->ID, 'translations', true);
                        $data[$program->ID] = $translations['en'];
                    }
                    return $data;
                case 'de':
                default:
                    foreach ($programs_imported as $program) {
                        $post_meta = get_post_meta($program->ID, '', true);
                        foreach ($post_meta as $key => $value) {
                            if ($key == 'translations') continue;
                            $data[$program->ID][$key] = is_serialized($value[0]) ? unserialize($value[0]) : $value[0];
                        }

                    }
                    return $data;
            }
        }
        return $data;
    }
}