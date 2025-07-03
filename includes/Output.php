<?php

namespace FAU\StudiumDisplay;

defined('ABSPATH') || exit;


class Output
{
    public function renderOutput($atts) {

        $api = new API();

        $lang = $atts['language'] == 'en' ? 'en' : 'de';

        if (isset($atts['format']) && in_array($atts['format'], ['full', 'box'])) {
            $data = $api->get_program((int)$atts['degreeProgram'], $lang);
        } else {
            $programs = $api->get_programs('', false, $lang);

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

            // Filter from $_GET parameters
            $getParams = Utils::array_map_recursive('sanitize_text_field', $_GET);
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
        }

        // Save IDs of active degree programs to transient
        //print "<pre>"; var_dump(function_exists('get_current_screen') && get_current_screen()->is_block_editor() == 1); print "</pre>";  exit;
        if (function_exists('get_current_screen') && get_current_screen()->is_block_editor() == 1) {
            $transient_name = 'fau_studium_degree_programs_sync';
            $degree_programs = get_transient($transient_name);
            if (!$degree_programs) {
                $degree_programs = [$lang => []];
            }

            foreach ($data as $program) {
                if (!in_array($program['id'], $degree_programs[$lang])) {
                    $degree_programs[$lang][] = $program['id'];
                }
            }
            set_transient($transient_name, $degree_programs, DAY_IN_SECONDS);
            //$sync = new Sync();
            //$sync->do_sync();
        }

        // Load the template and pass the sorted data
        $template_dir = FAU_STUDIUM_DISPLAY_PLUGIN_PATH . 'templates/';
        $template = new Template($template_dir);
        $format = wp_strip_all_tags($atts['format']);
        $templatefile = 'degree-program-'.$format;

        wp_enqueue_style('fau-studium-display');
        if (isset($atts['showSearch']) && $atts['showSearch'] == '1') {
            wp_enqueue_script('fau-studium-display-script');
        }

        return $template->render($templatefile, $data, $atts);
    }

}