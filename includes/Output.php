<?php

namespace Fau\DegreeProgram\Display;

defined('ABSPATH') || exit;


class Output
{
    public function renderOutput($atts) {

        $data = (new Data)->get_data($atts);

        // Load the template and pass the sorted data
        $template_dir = plugin()->getPath() . 'templates/';
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