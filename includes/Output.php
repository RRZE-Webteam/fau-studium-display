<?php

namespace FAU\StudiumDisplay;

defined('ABSPATH') || exit;


class Output
{
    public function renderOutput($atts) {

        $api = new API();

        //var_dump($atts);

        if (isset($atts['format']) && in_array($atts['format'], ['full', 'box'])) {
            $lang = $atts['language'] == 'en' ? 'en' : 'de';
            $data = $api->get_program((int)$atts['degreeProgram']);
            $data = $this->get_localized_data($data, $lang);
        } else {
            $programs = $api->get_programs();
            //var_dump($programs);
            /*$data = [];
            foreach($programs as $program) {
                $programdata = $api->get_program($program['value']);
                if (!empty($programdata)) {
                    $data[$program['value']] = $programdata;
                }
            }*/
            //var_dump($data);
            if (!empty($_GET)) {
                //$data = http_build_query($_GET);
                $data = Utils::filterPrograms($programs, $_GET);
            } else {
                $data = $programs;
            }
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

    private function get_localized_data($data, $lang) {
        if ($lang == 'de') {
            unset($data['translations']);
        } elseif ($lang == 'en') {
            $data = $data['translations']['en'] ?? $data;
        }
        return $data;
    }
}