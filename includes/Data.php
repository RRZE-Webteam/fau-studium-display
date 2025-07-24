<?php

namespace Fau\DegreeProgram\Display;

class Data
{
    public function get_data($atts) {

        $lang = $atts['language'] == 'en' ? 'en' : 'de';

        if (isset($atts['format']) && in_array($atts['format'], ['full', 'box'])) {
            $data = $this->get_single_program((int)$atts['degreeProgram'], $lang);
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

        /*    // Filter from $_GET parameters
            $getParams = isset($_GET) ? Utils::array_map_recursive('sanitize_text_field', $_GET) : [];
            $getParams = array_filter($getParams);

            // Intersect with $_GET params: if specified, only values present in block settings are allowed in $_GET
            foreach (['faculty', 'degree', 'attribute'] as $key) {
                if (!empty($filterBlock[$key]) && !empty($getParams[$key])) {
                    $getParams[$key] = array_unique(array_merge($getParams[$key], $filterBlock[$key]));
                    foreach ($getParams[$key] as $k => $v) {
                        if (!in_array($v, $filterBlock[$key])) {
                            unset($getParams[$key][$k]);
                        }
                    }
                }
            }

            if (!empty($getParams)) {
                $data = Utils::filterPrograms($programs, $getParams);
            } elseif (!empty($filterBlock)) {
                $data = Utils::filterPrograms($programs, $filterBlock);
            } else {
                $data = $programs;
            }
        */
            $data = Utils::filterPrograms($programs, $filterBlock);
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

    public function get_single_program($program_id, $lang) {

        // check if the program exists as CPT
        $program_imported = get_posts([
           'post_type'      => 'degree-program',
           'post_status'    => 'publish',
           'meta_key'       => 'id',
           'meta_value' => $program_id
        ]);
        if (!empty($program_imported)) {
            switch ($lang) {
                case 'en':
                    $translations = get_post_meta($program_imported[0]->ID, 'translations', true);
                    return $translations['en'];
                case 'de':
                default:
                    $data = [];
                    $post_meta = get_post_meta($program_imported[0]->ID, '', true);
                    foreach ($post_meta as $key => $value) {
                        $data[$key] = is_serialized($value[0]) ? unserialize($value[0]) : $value[0];
                    }
                    return $data;
            }
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