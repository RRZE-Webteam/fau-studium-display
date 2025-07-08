<?php

namespace Fau\DegreeProgram\Display;

defined('ABSPATH') || exit;

class Utils
{
    public static function renderSearchForm($hide_filter = []): string
    {
        $getParams = Utils::array_map_recursive('sanitize_text_field', $_GET);
        $api = new API();
        $filters_default = [
            ['key' => 'degree', 'label' => __('Degrees', 'fau-studium-display'), 'data' => $api->get_degrees(true)],
            ['key' => 'subject_group', 'label' => __('Subject groups', 'fau-studium-display'), 'data' => $api->get_subject_groups()],
            ['key' => 'attribute', 'label' => __('Special ways to study', 'fau-studium-display'), 'data' => $api->get_attributes()],
        ];
        $filters_extended = [
            ['key' => 'teaching_language', 'label' => __('Teaching language', 'fau-studium-display'), 'data' => $api->get_teaching_languages()],
            ['key' => 'start', 'label' => __('Start of degree program', 'fau-studium-display'), 'data' => $api->get_start_semesters()],
            ['key' => 'location', 'label' => __('Study location', 'fau-studium-display'), 'data' => $api->get_study_locations()],
            ['key' => 'faculty', 'label' => __('Faculty', 'fau-studium-display'), 'data' => $api->get_faculties()],
            ['key' => 'area', 'label' => __('Area of study', 'fau-studium-display'), 'data' => $api->get_areas_of_study()],
        ];

        $output = '<form method="get" class="program-search" action="' . esc_url(get_permalink()) . '">';
        $search = !empty($getParams['search']) ? sanitize_text_field($getParams['search']) : '';

        // Search input
        $output .= '<div class="search-title">'
                   . '<label for="fau_studium_search" class="label sr-only">' . __('Search', 'fau-studium-display') . '</label>'
                   . '<input type="text" name="search" id="fau_studium_search" value="' . $search . '" placeholder="' . __('Please enter search term...', 'fau-studium-display') . '" />'
                   . '<input type="submit" value="' . __('Search', 'fau-studium-display') . '" />'
                   . '</div>';

        // Filter sections default
        foreach ($filters_default as $filter) {
            if (in_array($filter['key'], $hide_filter)) {
                continue;
            }
            $filter_active = !empty($getParams[$filter['key']]);
            $output .= self::renderChecklistSection(
                $filter['key'],
                $filter['label'],
                $filter['data'],
                $filter_active ? array_map('sanitize_text_field', $getParams[$filter['key']]) : [],
                $filter_active ? '<span class="filter-count">' . count($getParams[$filter['key']]) . '</span>' : '',
            );
        }

        // Settings links + Filter sections extended
        $filters_extended_count = 0;
        $filters_extended_html = '';
        foreach ($filters_extended as $filter) {
            if (in_array($filter['key'], $hide_filter)) {
                continue;
            }
            $filter_active = !empty($getParams[$filter['key']]);
            if ($filter_active) {
                $filters_extended_count += count($getParams[$filter['key']]);
            }
            $filters_extended_html .= self::renderChecklistSection(
                $filter['key'],
                $filter['label'],
                $filter['data'],
                $filter_active ? array_map('sanitize_text_field', $getParams[$filter['key']]) : [],
                $filter_active ? '<span class="filter-count">' . count($getParams[$filter['key']]) . '</span>' : '',
            );
        }

        $output .= '<div class="settings-area">'
                   . '<button type="button" class="extended-search-toggle">'
                   . __('Advanced filters', 'fau-studium-display')
                   . ($filters_extended_count > 0 ? '<span class="filter-count">' . $filters_extended_count . '</span>' : '')
                   . '<span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span></button>'
                   . '<div class="reset-link">'
                   . '<a href="' . get_permalink() . '">&#9747; ' . __('Reset all filters', 'fau-studium-display') . '</a>'
                   . '</div></div>';

        $output .= '<div class="extended-search"><div class="flex-wrapper">' . $filters_extended_html . '</div></div>';

        $output .= '</form>';
        return $output;
    }

    public static function filterPrograms($programs, $filter) {
        $programs_filtered = [];

        foreach ($programs as $program) {

            // Text search
            if (!empty($filter['search']) && !str_contains(strtolower($program['title']), strtolower($filter['search']))) {
                continue;
            }

            // Attribute search
            $filterMap = [
                'degree'            => fn($program, $value) => !empty($program['degree']['parent']['name']) && $program['degree']['parent']['name'] === $value,
                'subject_group'     => fn($program, $value) => !empty($program['subject_groups']) && in_array($value, $program['subject_groups']),
                'attribute'         => fn($program, $value) => !empty($program['attributes']) && in_array($value, $program['attributes']),
                'teaching_language' => fn($program, $value) => !empty($program['teaching_language']) && $program['teaching_language'] === $value,
                'start'             => fn($program, $value) => !empty($program['start']) && in_array($value, $program['start']),
                'location'          => fn($program, $value) => !empty($program['location']) && in_array($value, $program['location']),
                'faculty'           => fn($program, $value) => !empty($program['faculty']) && in_array($value, array_column($program['faculty'], 'name')),
                'area'              => fn($program, $value) => !empty($program['area_of_study']) && in_array($value, array_column($program['area_of_study'], 'name')),
            ];

            foreach ($filterMap as $key => $matcher) {
                if (!empty($filter[$key])) {
                    $matched = false;
                    foreach ($filter[$key] as $value) {
                        if ($matcher($program, $value)) {
                            $matched = true;
                            break;
                        }
                    }
                    if (!$matched) {
                        continue 2;
                    }
                }
            }

            $programs_filtered[] = $program;
        }

        return $programs_filtered;
    }

    private static function renderChecklistSection(string $name, string $label, array $options, array $selected, string $filterCount = ''): string
    {
        $output = '<div class="filter-' . esc_attr($name) . '">';
        $output .= '<button type="button" class="checklist-toggle">'
                   . $label . $filterCount . '<span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span></button>';
        $output .= '<div class="checklist">';
        foreach ($options as $option) {
            $checked = in_array($option, $selected);
            $output .= '<label><input type="checkbox" name="' . esc_attr($name) . '[]" value="' . esc_attr($option) . '" '
                       . checked($checked, true, false) . '>' . esc_html($option) . '</label>';
        }
        $output .= '</div></div>';
        return $output;
    }

    public static function array_map_recursive($callback, $array) {
        $new = array();
        if( is_array($array) ) foreach ($array as $key => $val) {
            if (is_array($val)) {
                $new[$key] = self::array_map_recursive($callback, $val);
            } else {
                $new[$key] = call_user_func($callback, $val);
            }
        }
        else $new = call_user_func($callback, $array);
        return $new;
    }

    public static function arrayToHtmlList($data) {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }
        if (is_array($data)) {
            $html = "<ul style='margin-left: 2em;'>";
            foreach ($data as $key => $value) {
                $html .= "<li><strong>" . htmlspecialchars((string)$key) . ":</strong> ";
                $html .= self::arrayToHtmlList($value);
                $html .= "</li>";
            }
            $html .= "</ul>";
            return $html;
        } else {
            return '<span style="display: inline-block; margin-left: 2em;">' . htmlspecialchars((string)$data) . '</span>';
        }
    }

    public static function get_degree_options() {
        $api = new API();
        $degrees = $api->get_degrees();
        $degreeOptions = [];
        foreach ($degrees as $degree) {
            $degreeOptions[] = [
                'label' => $degree,
                'value' => $degree,
            ];
        }
        return $degreeOptions;
    }

    public static function get_faculty_options() {
        $api = new API();
        $faculties = $api->get_faculties();
        $facultyOptions = [];
        foreach ($faculties as $faculty) {
            $facultyOptions[] = [
                'label' => $faculty,
                'value' => $faculty,
            ];
        }
        $attributes = $api->get_attributes();
        $attributes_formatted = [];
        foreach ($attributes as $attribute) {
            $attributes_formatted[] = [
                'label' => $attribute,
                'value' => $attribute,
            ];
        }
        return $facultyOptions;
    }

    public static function get_attribute_options() {
        $api = new API();
        $attributes = $api->get_attributes();
        $attributeOptions = [];
        foreach ($attributes as $attribute) {
            $attributeOptions[] = [
                'label' => $attribute,
                'value' => $attribute,
            ];
        }
        return $attributeOptions;
    }
}