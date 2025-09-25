<?php

namespace Fau\DegreeProgram\Display;

use function Fau\DegreeProgram\Display\Config\get_output_fields;

class Shortcode
{
    public function __construct()
    {
        add_shortcode('fau-studium', [$this, 'shortcode_output']);
    }

    public function shortcode_output($atts, $content = ""): false|string
    {
        $atts = self::sanitize_atts($atts);
        $atts_mapped['language'] = $atts['lang'];
        $atts_mapped['degreeProgram'] = $atts['id'];
        if ($atts['format'] == 'short') {
            $atts_mapped['format'] = 'list';
        } else {
            $atts_mapped['format'] = 'full';
        }
        if ($atts['display'] == 'search') {
            $atts_mapped['showSearch'] = '1';
            $atts_mapped['format'] = $atts['output'] == 'tiles' ? 'grid' : 'table';
            $atts_mapped['selectedItemsGrid'] = get_output_fields('grid');
            $atts_mapped['selectedFaculties'] = [];
            $atts_mapped['selectedDegrees'] = [];
            $atts_mapped['selectedSpecialWays'] = [];
            $hide_items = explode(',', $atts['hide']);
            array_map('trim', $hide_items);
            if (in_array('search', $hide_items)) {
                $atts_mapped['showSearch'] = '';
            }
        }

        if (!empty($atts['include'])) {
            str_replace('admission_requirements', 'admission_requirements_application', $atts['include']);
            $include_items = explode(',', $atts['include']);
            array_map('trim', $include_items);
            $atts_mapped['selectedItemsFull'] = $include_items;
        } else if (!empty($atts['exclude'])) {
            str_replace('admission_requirements', 'admission_requirements_application', $atts['include']);
            $exclude_items = explode(',', $atts['exclude']);
            array_map('trim', $exclude_items);
            $default_items = get_output_fields('full');
            $atts_mapped['selectedItemsFull'] = array_diff($default_items, $exclude_items);
        } else {
            $atts_mapped['selectedItemsFull'] = get_output_fields('full');
        }

        $output = new Output();
        return do_shortcode($output->renderOutput($atts_mapped));
    }

    private function sanitize_atts($atts): array
    {
        $defaults = [
            'display' => 'degree-program', // degree-program|search
            'id' => '',
            'lang' => 'de', // de|en
            'include' => '',
            'exclude' => '',
            'format' => 'full', // full|short
            'output' => 'list',
            'hide' => '',
            'filters' => '',
        ];
        $search_filters = get_output_fields('search-filters');
        foreach ($search_filters as $filter) {
            $defaults[$filter] = '';
        }
        $args = shortcode_atts($defaults, $atts);
        array_walk($args, 'sanitize_text_field');
        return $args;
    }

}