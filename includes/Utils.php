<?php

namespace FAU\StudiumDisplay;

defined('ABSPATH') || exit;

class Utils
{
    public static function renderSearchForm(): string
    {
        $api = new API();
        $filters = [
            ['key' => 'degree', 'label' => __('Degrees', 'fau-studium-display'), 'data' => $api->get_degrees(true)],
            ['key' => 'subject_group', 'label' => __('Subject groups', 'fau-studium-display'), 'data' => $api->get_subject_groups()],
            ['key' => 'attribute', 'label' => __('Special ways to study', 'fau-studium-display'), 'data' => $api->get_attributes()],
            ['key' => 'teaching_language', 'label' => __('Teaching language', 'fau-studium-display'), 'data' => $api->get_teaching_languages()],
            ['key' => 'start', 'label' => __('Start of degree program', 'fau-studium-display'), 'data' => $api->get_start_semesters()],
            ['key' => 'location', 'label' => __('Study location', 'fau-studium-display'), 'data' => $api->get_study_locations()],
            ['key' => 'faculty', 'label' => __('Faculty', 'fau-studium-display'), 'data' => $api->get_faculties()],
            ['key' => 'area', 'label' => __('Area of study', 'fau-studium-display'), 'data' => $api->get_areas_of_study()],
        ];

        $output = '<form method="get" class="program-search" action="' . esc_url(get_permalink()) . '">';
        $search = !empty($_REQUEST['search']) ? sanitize_text_field($_REQUEST['search']) : '';

        // Search input
        $output .= '<div class="search-title">'
                   . '<label for="fau_studium_search" class="label">' . __('Search', 'fau-studium-display') . '</label>'
                   . '<input type="text" name="search" id="fau_studium_search" value="' . $search . '" placeholder="' . __('Please enter search term...', 'fau-studium-display') . '" />'
                   . '<input type="submit" value="' . __('Search', 'fau-studium-display') . '" />'
                   . '</div>';

        // Filter sections
        foreach ($filters as $filter) {
            $output .= self::renderChecklistSection(
                $filter['key'],
                $filter['label'],
                $filter['data'],
                !empty($_REQUEST[$filter['key']]) ? array_map('sanitize_text_field', $_REQUEST[$filter['key']]) : []
            );
        }

        // Reset link
        $layout = 'table';
        $output .= '<div class="settings-area"><div class="filter-reset">'
                   . '<a href="' . get_permalink() . '?display=' . $layout . '">&#9747; ' . __('Reset all filters', 'fau-studium-display') . '</a>'
                   . '</div></div>';

        $output .= '</form>';
        return $output;
    }

    public static function filter_programs($programs, $filter) {
        //var_dump($filter);
        $programs_filtered = [];
        foreach ($programs as $program) {
            if (!empty($filter['search'])) {
                if (!str_contains(strtolower($program['title']), strtolower($filter['search']))) {
                    continue;
                }
            }
            if (!empty($filter['degree'])) {
                $degree_matched = false;
                foreach ($filter['degree'] as $degree) {
                    if (isset($program['degree']['parent']['name']) && $program['degree']['parent']['name'] == $degree) {
                        $degree_matched = true;
                        break;
                    }
                }
                if (!$degree_matched) {
                    continue;
                }
            }
            if (!empty($filter['subject_group'])) {
                $group_matched = false;
                foreach ($filter['subject_group'] as $subject_group) {
                    if (!empty($program['subject_groups']) && in_array($subject_group, $program['subject_groups'])) {
                        $group_matched = true;
                        break;
                    }
                }
                if (!$group_matched) {
                    continue;
                }
            }
            if (!empty($filter['attribute'])) {
                $attribute_matched = false;
                foreach ($filter['attribute'] as $attribute) {
                    if (!empty($program['attributes']) && in_array($attribute, $program['attributes'])) {
                        $attribute_matched = true;
                        break;
                    }
                }
                if (!$attribute_matched) {
                    continue;
                }
            }
            $programs_filtered[] = $program;
        }
        return $programs_filtered;
    }

    private static function renderChecklistSection(string $name, string $label, array $options, array $selected): string
    {
        $output = '<div class="filter-' . esc_attr($name) . '">';
        $output .= '<button type="button" class="checklist-toggle">'
                   . $label . '<span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span></button>';
        $output .= '<div class="checklist" style="display: none;">';
        foreach ($options as $option) {
            $checked = in_array($option, $selected);
            $output .= '<label><input type="checkbox" name="' . esc_attr($name) . '[]" value="' . esc_attr($option) . '" '
                       . checked($checked, true, false) . '>' . esc_html($option) . '</label>';
        }
        $output .= '</div></div>';
        return $output;
    }
}