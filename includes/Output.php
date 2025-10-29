<?php

namespace Fau\DegreeProgram\Display;

defined('ABSPATH') || exit;


class Output
{
    public function renderOutput($atts) {

        if (empty($atts['language'])) {
            $atts['language'] = Utils::get_short_locale();
        }

        $data = (new Data)->get_data($atts);

        // Load the template and pass the sorted data
        $variant = Utils::getThemeFamily() != 'fau-elemental' ? '-default' : '';
        $template_dir = plugin()->getPath() . 'templates/';
        $template = new Template($template_dir);
        $format = wp_strip_all_tags($atts['format']);
        if (in_array($format, ['table', 'grid']) && !empty($_GET['display']) && in_array($_GET['display'], ['table', 'grid'])) {
            $format = sanitize_text_field($_GET['display']);
        }
        $templatefile = 'degree-program-'.$format.$variant;

        wp_enqueue_style('fau-studium-display');
        if (isset($atts['showSearch']) && $atts['showSearch'] == '1') {
            wp_enqueue_script('fau-studium-display-script');
        }

        return $template->render($templatefile, $data, $atts);
    }
}