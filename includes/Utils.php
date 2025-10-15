<?php

namespace Fau\DegreeProgram\Display;

use function Fau\DegreeProgram\Display\Config\get_labels;
use function Fau\DegreeProgram\Display\Config\get_output_fields;

defined('ABSPATH') || exit;

class Utils
{

    protected static array $themes = [
        'fau' => [
            'FAU-Einrichtungen',
            'FAU-Einrichtungen-BETA',
            'FAU-Medfak',
            'FAU-RWFak',
            'FAU-Philfak',
            'FAU-Techfak',
            'FAU-Natfak'
        ],
        'fau-elemental' => [
            'FAU-Elemental',
        ],
        'rrze' => [
            'rrze-2019'
        ],
        'vendor' => [
            'Francesca-Child',
            'Francesca-Child-Main'
        ]
    ];

    public static function getTemplatePath(): string {
        $currentTheme = wp_get_theme();
        foreach (self::$themes as $slug => $theme) {
            if (in_array(strtolower($currentTheme->stylesheet), array_map('strtolower', $theme))) {
                return plugin()->getPath('templates/') . $slug . '/';
            }
        }
        return plugin()->getPath('templates/');
    }
    public static function renderSearchForm($prefilter = [], $filter_items = [], $lang = 'de'): string
    {
        //var_dump($prefilter);
        $getParams = Utils::array_map_recursive('sanitize_text_field', $_GET);
        $api = new API();
        $degrees = !empty($prefilter['degree']) ? $prefilter['degree'] : $api->get_meta_list('degree_parents');
        $subject_groups = !empty($prefilter['subject_group']) ? $prefilter['subject_group'] : $api->get_meta_list('subject_groups');
        $attributes = !empty($prefilter['attribute']) ? $prefilter['attribute'] : $api->get_meta_list('attributes');
        $labels = get_labels($lang); // ToDO
        if (empty($filter_items)) {
            $filter_items = get_output_fields('search-filters');
        }

        $filters = [
            ['key' => 'degree', 'label' => ($labels['degree'] ?? 'degree'), 'data' => $degrees],
            ['key' => 'subject_group', 'label' => ($labels['subject_group'] ?? 'subject_group'), 'data' => $subject_groups],
            ['key' => 'attribute', 'label' => ($labels['attribute'] ?? 'attribute'), 'data' => $attributes],
            ['key' => 'admission_requirements', 'label' => ($labels['admission_requirements'] ?? 'admission_requirements'), 'data' => $api->get_meta_list('admission_requirements')],
            ['key' => 'semester', 'label' => ($labels['start'] ?? 'start'), 'data' => $api->get_meta_list('start_semesters')],
            ['key' => 'study_location', 'label' => ($labels['location'] ?? 'location'), 'data' => $api->get_meta_list('study_locations')],
            ['key' => 'teaching_language', 'label' => ($labels['teaching_language'] ?? 'teaching_language'), 'data' => $api->get_meta_list('teaching_languages')],
            ['key' => 'faculty', 'label' => ($labels['faculty'] ?? 'faculty'), 'data' => $api->get_meta_list('faculties')],
            ['key' => 'german_language_skills_for_international_students', 'label' => ($labels['german_language_skills'] ?? 'german_language_skills'), 'data' => $api->get_meta_list('german_language_skills')],
            //['key' => 'area', 'label' => ($labels['area'] ?? 'area'), 'data' => $api->get_areas_of_study()],
        ];

        foreach ($filters as $i => $filter) {
            if (!in_array($filter['key'], $filter_items)) {
                unset($filters[$i]);
            }
        }

        $filters_default = array_slice($filters, 0, 3);
        $filters_extended = array_slice($filters, 3);

        if (is_post_type_archive('degree-program')) {
            $url = get_post_type_archive_link( 'degree-program' );
        } else {
            $url = get_permalink();
        }

        $output = '<form method="get" class="program-search" action="' . esc_url($url) . '">';
        $search = !empty($getParams['search']) ? sanitize_text_field($getParams['search']) : '';

        // Search input
        $output .= '<label for="fau_studium_search" class="label">' . __('Search', 'fau-studium-display') . '</label>'
                   . '<div class="search-title">'
                   . '<input type="text" name="search" id="fau_studium_search" value="' . $search . '" placeholder="' . __('Search all degree programs', 'fau-studium-display') . '" />'
                   . '<button type="submit">' . __('Search', 'fau-studium-display') . '</button>'
                   . '</div>';

        // Filter options
        $output .= '<p class="label">' . __('Filter Options', 'fau-studium-display') . '</p>'
            . '<div class="flex-wrapper">';

        $filters_selected = [];

        // Filter sections default
        foreach ($filters_default as $filter) {
            // Don't show filters with only one option
            if (count($filter['data']) < 2) {
                continue;
            }
            $filter_active = !empty($getParams[$filter['key']]);
            if ($filter_active) {
                $filters_selected[$filter['key']] = $getParams[$filter['key']];
                $selected = array_map('sanitize_text_field', $getParams[$filter['key']]);
            } else {
                $selected = [];
            }
            $output .= self::renderChecklistSection(
                $filter['key'],
                $filter['label'],
                $filter['data'],
                $selected,
            );
        }

        if (count($filters_extended) > 0) {
            // Settings links + Filter sections extended
            $filters_extended_html  = '';
            foreach ($filters_extended as $filter) {
                // Don't show filters with only one option
                if (count($filter['data']) < 2) {
                    continue;
                }
                $filter_active = ! empty($getParams[ $filter[ 'key' ] ]);
                if ($filter_active) {
                    $filters_selected[$filter['key']] = $getParams[$filter['key']];
                    $selected = array_map('sanitize_text_field', $getParams[$filter['key']]);
                } else {
                    $selected = [];
                }
                $filters_extended_html .= self::renderChecklistSection(
                    $filter[ 'key' ],
                    $filter[ 'label' ],
                    $filter[ 'data' ],
                    $selected,
                );
            }

            $output .= '<button type="button" class="extended-search-toggle">'
                       . __('More filter options', 'fau-studium-display')
                       . '<span class="icon-wrapper icon-plus" aria-hidden="true"></span></button>';
            $output .= '</div>'; // .flex-wrapper

            $output .= '<div class="extended-search"><div class="flex-wrapper">' . $filters_extended_html . '</div></div>';
        } else {
            $output .= '</div>';
        }

        if (!empty($filters_selected)) {
            $current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $output .= '<div class="filters-selected">'
                . '<p class="filter-selected-title">' . __('Selected filters', 'fau-studium-display') . '</p>';
            foreach ($filters_selected as $filter_key => $filter_selected) {
                foreach ($filter_selected as $filter_item) {
                    $cleared_url = str_replace('&' . $filter_key . '%5B%5D=' . urlencode($filter_item), '', $current_url);
                    $output .= '<a class="filter-selected" data-key="' . $filter_key . '" data-value="' . $filter_item . '" href="' . $cleared_url . '">'  . $filter_item . '</a>';
                }
            }
            $output .= '<a class="filter-selected delete-all" data-key="all" data-value="all" href="' . $url . '">'  . __('Delete all', 'fau-studium-display') . '</a>';
            $output .= '</div>';
        }

        $output .= '</form>';
        return $output;
    }

    public static function filterPrograms($programs, $filter) {
        $programs_filtered = [];

        foreach ($programs as $id => $program) {

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

            $programs_filtered[$id] = $program;
        }

        return $programs_filtered;
    }

    private static function renderChecklistSection(string $name, string $label, array $options, array $selected): string
    {
        $output = '<div class="filter-' . esc_attr($name) . '">';
        $output .= '<button type="button" class="checklist-toggle">'
                   . $label . '<span class="icon-wrapper" aria-hidden="true"></span></button>';
        $output .= '<div class="checklist">';
        foreach ($options as $option) {
            $checked = in_array($option, $selected);
            $output .= '<label><input type="checkbox" name="' . esc_attr($name) . '[]" value="' . esc_attr($option) . '" '
                       . checked($checked, true, false) . '>' . esc_html($option) . '</label>';
        }
        $output .= '<button type="submit" class="submit-filter" value="' . __('Apply filter', 'fau-studium-display') . '">' . __('Apply filter', 'fau-studium-display') . '</button>';
        $output .= '</div>';
        $output .= '</div>';
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

    public static function get_program_options() {
        $degreeProgramOptions = [];
        if (is_plugin_active('FAU-Studium/fau-degree-program.php')) {
            $degreePrograms = get_posts([
                'posts_per_page' => -1,
                'post_type'      => 'studiengang',
                'post_status'    => 'publish',
                'orderby'        => 'title',
                'order'          => 'ASC',
            ]);
            foreach ($degreePrograms as $degreeProgram) {
                $degreeTerm = get_the_terms( $degreeProgram->ID, 'abschluss' );
                if ($degreeTerm && ! is_wp_error($degreeTerm)) {
                    $degree = ' ( ' . $degreeTerm[0]->name . ')';
                }
                $degreeProgramOptions[] = [
                    'label' => $degreeProgram->post_title . $degree,
                    'value' => (string) $degreeProgram->ID,
                ];
            }
        } else {
            $api = new API();
            $data = $api->get_programs(false);
            $degreeProgramOptions = array_map(
                fn($item) => [
                    'label' => $item[ 'title' ] . ' (' . $item[ 'degree' ][ 'abbreviation' ] . ')',
                    'value' => (string)$item[ 'id' ],
                ],
                $data
            );
        }
        return $degreeProgramOptions;
    }

    public static function get_degree_options($parents = false) {
        $degreeOptions = [];
        if (is_plugin_active('FAU-Studium/fau-degree-program.php')) {
            $degree_terms = get_terms([
                'taxonomy'      => 'abschluss',
                'hide_empty' => true,
            ]);
            $child_degrees = array_filter( $degree_terms, function( $term ) {
                return $term->parent != 0;
            });
            foreach($child_degrees as $degree) {
                $degreeOptions[] = [
                    'label' => $degree->name,
                    'value' => $degree->name,
                ];
            }
        } else {
            $api           = new API();
            $degrees       = $parents ? $api->get_meta_list('degree_parents') : $api->get_meta_list('degrees');
            foreach ($degrees as $degree) {
                $degreeOptions[] = [
                    'label' => $degree,
                    'value' => $degree,
                ];
            }
        }
        return $degreeOptions;
    }

    public static function get_faculty_options() {
        $facultyOptions = [];
        if (is_plugin_active('FAU-Studium/fau-degree-program.php')) {
            $faculty_terms = get_terms([
                'taxonomy'      => 'faculty',
                'hide_empty' => true,
            ]);
            foreach($faculty_terms as $faculty) {
                $facultyOptions[] = [
                    'label' => $faculty->name,
                    'value' => $faculty->name,
                ];
            }
        } else {
            $api = new API();
            $faculties = $api->get_meta_list('faculties');
            foreach ($faculties as $faculty) {
                $facultyOptions[] = [
                    'label' => $faculty,
                    'value' => $faculty,
                ];
            }
        }
        return $facultyOptions;
    }

    public static function get_attribute_options() {
        $attributeOptions = [];
        if (is_plugin_active('FAU-Studium/fau-degree-program.php')) {
            $attribute_terms = get_terms([
               'taxonomy'      => 'attribute',
               'hide_empty' => true,
           ]);
            foreach($attribute_terms as $attribute) {
                $attributeOptions[] = [
                    'label' => $attribute->name,
                    'value' => $attribute->name,
                ];
            }
        } else {
            $api = new API();
            $attributes = $api->get_meta_list('attributes');
            foreach ($attributes as $attribute) {
                $attributeOptions[] = [
                    'label' => $attribute,
                    'value' => $attribute,
                ];
            }
        }
        return $attributeOptions;
    }

    public static function import_image_from_url($image_url, $post_id)
    {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        // Prüfen, ob URL gültig ist
        if (empty($image_url) || ! filter_var($image_url, FILTER_VALIDATE_URL)) {
            return new \WP_Error('invalid_url', 'Ungültige Bild-URL');
        }

        // Temporäre Datei holen
        $tmp = download_url($image_url);

        if (is_wp_error($tmp)) {
            return $tmp; // Fehler beim Herunterladen
        }

        $filename = basename(parse_url($image_url, PHP_URL_PATH));
        $attachments = get_posts([
             'post_type'  => 'attachment',
             'name'       => sanitize_title(pathinfo($filename, PATHINFO_FILENAME)),
             'post_status'=> 'inherit',
             'posts_per_page' => 1,
         ]);

        if (!empty($attachments)) {
            return $attachments[0]->ID;
        }

        // Dateinamen aus URL extrahieren
        $file_array               = [];
        $file_array[ 'name' ]     = $filename;
        $file_array[ 'tmp_name' ] = $tmp;

        // Datei in die Mediathek hochladen
        $attachment_id = media_handle_sideload($file_array, $post_id);

        // Bei Fehler temporäre Datei löschen
        if (is_wp_error($attachment_id)) {
            @unlink($file_array[ 'tmp_name' ]);
        }

        return $attachment_id;
    }

    public static function map_post_type_data($program_id, $lang = 'de') {
        $program = get_post($program_id);
        $program_meta = get_post_meta($program_id);
        //print "<pre>"; var_dump($program_id); print "</pre>";
        //print "<pre>"; var_dump($program_meta); print "</pre>";
        if (empty($program_meta)) {
            return [];
        }
        $data =[
            'id' => $program->ID,
            'date' => $program->post_date,
            'modified' => $program->post_modified,
            'link' => get_permalink($program->ID),
            'slug' => $program->post_name,
            'lang' => $lang,
            '_thumbnail_rendered' => get_the_post_thumbnail($program_id, 'full'),
            'title' => $program->post_title,
            'subtitle' => $program_meta['subtitle'][0] ?? '',
            'standard_duration' => $program_meta['standard_duration'][0] ?? '',
            'fee_required' => $program_meta['fee_required'][0] ?? '',
            'start' => $program_meta['start_de'] ?? [],
            'videos' => explode(',', $program_meta['videos'][0]),
            'application_deadline_winter_semester' => $program_meta['application_deadline_winter_semester'][0] ?? '',
            'application_deadline_summer_semester' => $program_meta['application_deadline_summer_semester'][0] ?? '',
            'examination_regulations' => $program_meta['examination_regulations'][0] ?? '',
            'module_handbook' => $program_meta['module_handbook'][0] ?? '',
            'info_brochure' => $program_meta['info_brochure'][0] ?? '',
        ];
        $number_of_students = get_the_terms( $program_id, 'number_of_students' );
        if ($number_of_students && ! is_wp_error($number_of_students)) {
            $data['number_of_students'] = $number_of_students[0]->name;
        }
        $teaching_language = get_the_terms( $program_id, 'sprache' );
        $attributes = get_the_terms( $program_id, 'attribute' );
        $degree = get_the_terms( $program_id, 'abschluss' );
        $faculties = get_the_terms( $program_id, 'faculty' );
        $locations = get_the_terms( $program_id, 'standort' );
        $subject_groups = get_the_terms( $program_id, 'faechergruppe' );
        $bachelor_or_teaching_adm_req = get_the_terms( $program_id, 'bachelor_or_teaching_adm_req' );
        $teaching_higher_semester_adm_req = get_the_terms( $program_id, 'teaching_higher_semester_adm_req' );
        $master_degree_adm_req = get_the_terms( $program_id, 'master_degree_adm_req' );
        $german_for_int_students = get_the_terms($program_id, 'german_for_int_students');
        $examinations_office = get_the_terms($program_id, 'examinations_office');
        $subject_specific_advice = get_the_terms($program_id, 'subject_specific_advice');
        $area_of_study = get_the_terms($program_id, 'area_of_study');
        $apply_now_link = get_the_terms($program_id, 'apply_now_link');
        switch ($lang) {
            case 'de':
                $data['title'] = $program->post_title;
                $data['subtitle'] = $program_meta['subtitle'][0] ?? '';
                $data['start'] = $program_meta['start_de'] ?? '';
                $data['meta_description'] = $program_meta['meta_description'][0] ?? '';
                $data['content']['about']['description'] = $program_meta['about'][0] ?? '';
                $data['content']['structure']['description'] = $program_meta['structure'][0] ?? '';
                $data['content']['specializations']['description'] = $program_meta['specializations'][0] ?? '';
                $data['content']['qualities_and_skills']['description'] = $program_meta['qualities_and_skills'][0] ?? '';
                $data['content']['why_should_study']['description'] = $program_meta['why_should_study'][0] ?? '';
                $data['content']['career_prospects']['description'] = $program_meta['career_prospects'][0] ?? '';
                $data['content']['content_related_master_requirements'] = $program_meta['content_related_master_requirements'][0] ?? '';
                $data['details_and_notes'] = $program_meta['details_and_notes'][0] ?? '';
                $data['language_skills'] = $program_meta['language_skills'][0] ?? '';
                $data['url'] = $program_meta['url'][0] ?? '';
                $data['department'] = $program_meta['department'][0] ?? '';
                $data['degree_program_fees'] = $program_meta['degree_program_fees'][0] ?? '';
                $data['entry_text'] = $program_meta['entry_text'][0] ?? '';
                if ($teaching_language && ! is_wp_error($teaching_language)) {
                    $data['teaching_language'] = $teaching_language[0]->name;
                }
                if ($attributes && ! is_wp_error($attributes)) {
                    foreach ( $attributes as $attribute ) {
                        $data['attributes'][] = $attribute->name;
                    }
                }
                if ($degree && ! is_wp_error($degree)) {
                    $degree_parent_id = $degree[0]->parent;
                    $degree_parent = get_term($degree_parent_id);
                    $data['degree']['name'] = $degree[0]->name;
                    $data['degree']['abbreviation'] = get_term_meta($degree[0]->term_id, 'abbreviation',true);
                    $data['degree']['parent']['name'] = $degree_parent->name;
                    $data['degree']['parent']['abbreviation'] = get_term_meta($degree_parent->term_id, 'abbreviation', true);
                }
                if ($faculties && ! is_wp_error($faculties)) {
                    foreach ($faculties as $faculty) {
                        $data['faculty'][] = [
                            'name' => $faculty->name,
                            'link_text' => get_term_meta($faculty->term_id, 'link_text', true),
                            'link_url' => get_term_meta($faculty->term_id, 'link_url', true),
                        ];
                    }
                }
                if ($locations && ! is_wp_error($locations)) {
                    foreach ($locations as $location) {
                        $data['location'][] = $location->name;
                    }
                }
                if ($subject_groups && ! is_wp_error($subject_groups)) {
                    foreach ($subject_groups as $subject_group) {
                        $data['subject_groups'][] = $subject_group->name;
                    }
                }
                if ($bachelor_or_teaching_adm_req && ! is_wp_error($bachelor_or_teaching_adm_req)) {
                    $bachelor_or_teaching_adm_req_parent_id = $bachelor_or_teaching_adm_req[0]->parent;
                    $data['admission_requirements']['bachelor_or_teaching_degree']['name'] = $bachelor_or_teaching_adm_req[0]->name;
                    $data['admission_requirements']['bachelor_or_teaching_degree']['link_text'] = get_term_meta($bachelor_or_teaching_adm_req[0]->term_id, 'link_text',true);
                    $data['admission_requirements']['bachelor_or_teaching_degree']['link_url'] = get_term_meta($bachelor_or_teaching_adm_req[0]->term_id, 'link_url',true);
                    if (!empty($bachelor_or_teaching_adm_req_parent_id)) {
                        $bachelor_or_teaching_adm_req_parent = get_term($bachelor_or_teaching_adm_req_parent_id);
                        $data[ 'admission_requirements' ][ 'bachelor_or_teaching_degree' ][ 'parent' ][ 'name' ] = $bachelor_or_teaching_adm_req_parent->name;
                        $data[ 'admission_requirements' ][ 'bachelor_or_teaching_degree' ][ 'parent' ][ 'link_text' ] = get_term_meta(
                            $bachelor_or_teaching_adm_req_parent_id,
                            'link_text',
                            true
                        );
                        $data[ 'admission_requirements' ][ 'bachelor_or_teaching_degree' ][ 'parent' ][ 'link_url' ] = get_term_meta(
                            $bachelor_or_teaching_adm_req_parent_id,
                            'link_url',
                            true
                        );
                    }
                }
                if ($teaching_higher_semester_adm_req && ! is_wp_error($teaching_higher_semester_adm_req)) {
                    $teaching_higher_semester_adm_req_parent_id = $teaching_higher_semester_adm_req[0]->parent;
                    $data['admission_requirements']['teaching_degree_higher_semester']['name'] = $teaching_higher_semester_adm_req[0]->name;
                    $data['admission_requirements']['teaching_degree_higher_semester']['link_text'] = get_term_meta($teaching_higher_semester_adm_req[0]->term_id, 'link_text',true);
                    $data['admission_requirements']['teaching_degree_higher_semester']['link_url'] = get_term_meta($teaching_higher_semester_adm_req[0]->term_id, 'link_url',true);
                    if (!empty($teaching_higher_semester_adm_req_parent_id)) {
                        $teaching_higher_semester_adm_req_parent = get_term($teaching_higher_semester_adm_req_parent_id);
                        $data[ 'admission_requirements' ][ 'teaching_degree_higher_semester' ][ 'parent' ][ 'name' ] = $teaching_higher_semester_adm_req_parent->name;
                        $data[ 'admission_requirements' ][ 'teaching_degree_higher_semester' ][ 'parent' ][ 'link_text' ] = get_term_meta(
                            $teaching_higher_semester_adm_req_parent_id,
                            'link_text',
                            true
                        );
                        $data[ 'admission_requirements' ][ 'teaching_degree_higher_semester' ][ 'parent' ][ 'link_url' ] = get_term_meta(
                            $teaching_higher_semester_adm_req_parent_id,
                            'link_url',
                            true
                        );
                    }
                }
                if ($master_degree_adm_req && ! is_wp_error($master_degree_adm_req)) {
                    $master_degree_adm_req_parent_id = $master_degree_adm_req[0]->parent;
                    $data['admission_requirements']['master']['name'] = $master_degree_adm_req[0]->name;
                    $data['admission_requirements']['master']['link_text'] = get_term_meta($master_degree_adm_req[0]->term_id, 'link_text',true);
                    $data['admission_requirements']['master']['link_url'] = get_term_meta($master_degree_adm_req[0]->term_id, 'link_url',true);
                    if (!empty($master_degree_adm_req_parent_id)) {
                        $master_degree_adm_req_parent = get_term($master_degree_adm_req_parent_id);
                        $data[ 'admission_requirements' ][ 'master' ][ 'parent' ][ 'name' ] = $master_degree_adm_req_parent->name;
                        $data[ 'admission_requirements' ][ 'master' ][ 'parent' ][ 'link_text' ] = get_term_meta(
                            $master_degree_adm_req_parent_id,
                            'link_text',
                            true
                        );
                        $data[ 'admission_requirements' ][ 'master' ][ 'parent' ][ 'link_url' ] = get_term_meta(
                            $master_degree_adm_req_parent_id,
                            'link_url',
                            true
                        );
                    }
                }
                if ($german_for_int_students && ! is_wp_error($german_for_int_students)) {
                    $data['german_language_skills_for_international_students']['name'] = $german_for_int_students[0]->name;
                    $data['german_language_skills_for_international_students']['link_text'] = get_term_meta($german_for_int_students[0]->term_id, 'link_text',true);
                    $data['german_language_skills_for_international_students']['link_url'] = get_term_meta($german_for_int_students[0]->term_id, 'link_url',true);
                }
                if ($examinations_office && ! is_wp_error($examinations_office)) {
                    $data['examinations_office']['name'] = $examinations_office[0]->name;
                    $data['examinations_office']['link_text'] = get_term_meta($examinations_office[0]->term_id, 'link_text',true);
                    $data['examinations_office']['link_url'] = get_term_meta($examinations_office[0]->term_id, 'link_url',true);
                }
                if ($subject_specific_advice && ! is_wp_error($subject_specific_advice)) {
                    $data['subject_specific_advice']['link_text'] = $subject_specific_advice[0]->name;
                    $data['subject_specific_advice']['link_url'] = get_term_meta($subject_specific_advice[0]->term_id, 'link_url',true);
                }
                if ($area_of_study && ! is_wp_error($area_of_study)) {
                    foreach ($area_of_study as $area) {
                        $data['area_of_study'][] = [
                            'name' => $area->name,
                            'link_text' => get_term_meta($area->term_id, 'link_text', true),
                            'link_url' => get_term_meta($area->term_id, 'link_url', true),
                        ];
                    }
                }
                if ($apply_now_link && ! is_wp_error($apply_now_link)) {
                    $data['apply_now_link']['name'] = $apply_now_link[0]->name;
                    $data['apply_now_link']['link_text'] = get_term_meta($apply_now_link[0]->term_id, 'link_text',true);
                    $data['apply_now_link']['link_url'] = get_term_meta($apply_now_link[0]->term_id, 'link_url',true);
                }

                break;
            case 'en':
                $data['title'] = $program_meta['title_en'][0] ?? '';
                $data['subtitle'] = $program_meta['subtitle_en'][0] ?? '';
                $data['start'] = $program_meta['start_en'] ?? [];
                $data['meta_description'] = $program_meta['meta_description_en'][0] ?? '';
                $data['content']['about']['description'] = $program_meta['about_en'][0] ?? '';
                $data['content']['structure']['description'] = $program_meta['structure_en'][0] ?? '';
                $data['content']['specializations']['description'] = $program_meta['specializations_en'][0] ?? '';
                $data['content']['qualities_and_skills']['description'] = $program_meta['qualities_and_skills_en'][0] ?? '';
                $data['content']['why_should_study']['description'] = $program_meta['why_should_study_en'][0] ?? '';
                $data['content']['career_prospects']['description'] = $program_meta['career_prospects_en'][0] ?? '';
                $data['content']['special_features']['description'] = $program_meta['special_features_en'][0] ?? '';
                $data['content']['content_related_master_requirements'] = $program_meta['content_related_master_requirements_en'][0] ?? '';
                $data['details_and_notes'] = $program_meta['details_and_notes_en'][0] ?? '';
                $data['language_skills'] = $program_meta['language_skills_en'][0] ?? '';
                $data['url_en'] = $program_meta['url_en'][0] ?? '';
                $data['department_en'] = $program_meta['department_en'][0] ?? '';
                $data['degree_program_fees'] = $program_meta['degree_program_fees_en'][0] ?? '';
                $data['entry_text'] = $program_meta['entry_text_en'][0] ?? '';
                if ($teaching_language && ! is_wp_error($teaching_language)) {
                    $data['teaching_language'] = get_term_meta($teaching_language[0]->term_id, 'name_en',true);
                }
                if ($attributes && ! is_wp_error($attributes)) {
                    foreach ( $attributes as $attribute ) {
                        $data['attributes'][] = get_term_meta($attribute->term_id, 'name_en',true);
                    }
                }
                if ($degree && ! is_wp_error($degree)) {
                    $degree_parent_id = $degree[0]->parent;
                    $degree_parent = get_term($degree_parent_id);
                    $data['degree']['name'] = get_term_meta($degree[0]->term_id, 'name_en',true);
                    $data['degree']['abbreviation'] = get_term_meta($degree[0]->term_id, 'abbreviation_en',true);
                    $data['degree']['parent']['name'] = get_term_meta($degree_parent->term_id, 'name_en',true);
                    $data['degree']['parent']['abbreviation'] = get_term_meta($degree_parent->term_id, 'abbreviation_en', true);
                }
                if ($faculties && ! is_wp_error($faculties)) {
                    foreach ($faculties as $faculty) {
                        $data['faculty'][] = [
                            'name' => get_term_meta($faculty->term_id, 'name_en', true),
                            'link_text' => get_term_meta($faculty->term_id, 'link_text_en', true),
                            'link_url' => get_term_meta($faculty->term_id, 'link_url_en', true),
                        ];
                    }
                }
                if ($locations && ! is_wp_error($locations)) {
                    foreach ($locations as $location) {
                        $data['location'][] = get_term_meta($location->term_id, 'name_en', true);
                    }
                }
                if ($subject_groups && ! is_wp_error($subject_groups)) {
                    foreach ($subject_groups as $subject_group) {
                        $data['subject_groups'][] = get_term_meta($subject_group->term_id, 'name_en', true);
                    }
                }
                if ($bachelor_or_teaching_adm_req && ! is_wp_error($bachelor_or_teaching_adm_req)) {
                    $bachelor_or_teaching_adm_req_parent_id = $bachelor_or_teaching_adm_req[0]->parent;
                    $data['admission_requirements']['bachelor_or_teaching_degree']['name'] = get_term_meta($bachelor_or_teaching_adm_req[0]->term_id, 'name_en',true);
                    $data['admission_requirements']['bachelor_or_teaching_degree']['link_text'] = get_term_meta($bachelor_or_teaching_adm_req[0]->term_id, 'link_text_en',true);
                    $data['admission_requirements']['bachelor_or_teaching_degree']['link_url'] = get_term_meta($bachelor_or_teaching_adm_req[0]->term_id, 'link_url_en',true);
                    if (!empty($bachelor_or_teaching_adm_req_parent_id)) {
                        $data['admission_requirements']['bachelor_or_teaching_degree']['parent']['name'] = get_term_meta($bachelor_or_teaching_adm_req_parent_id, 'name_en',true);
                        $data['admission_requirements']['bachelor_or_teaching_degree']['parent']['link_text'] = get_term_meta($bachelor_or_teaching_adm_req_parent_id, 'link_text_en',true);
                        $data['admission_requirements']['bachelor_or_teaching_degree']['parent']['link_url'] = get_term_meta($bachelor_or_teaching_adm_req_parent_id, 'link_url_en',true);
                    }
                }
                if ($teaching_higher_semester_adm_req && ! is_wp_error($teaching_higher_semester_adm_req)) {
                    $teaching_higher_semester_adm_req_parent_id = $teaching_higher_semester_adm_req[0]->parent;
                    $data['admission_requirements']['teaching_degree_higher_semester']['name'] = get_term_meta($teaching_higher_semester_adm_req[0]->term_id, 'name_en',true);
                    $data['admission_requirements']['teaching_degree_higher_semester']['link_text'] = get_term_meta($teaching_higher_semester_adm_req[0]->term_id, 'link_text_en',true);
                    $data['admission_requirements']['teaching_degree_higher_semester']['link_url'] = get_term_meta($teaching_higher_semester_adm_req[0]->term_id, 'link_url_en',true);
                    if (!empty($teaching_higher_semester_adm_req_parent_id)) {
                        $data[ 'admission_requirements' ][ 'teaching_degree_higher_semester' ][ 'parent' ][ 'name' ] = get_term_meta(
                            $teaching_higher_semester_adm_req_parent_id,
                            'name_en',
                            true
                        );
                        $data[ 'admission_requirements' ][ 'teaching_degree_higher_semester' ][ 'parent' ][ 'link_text' ] = get_term_meta(
                            $teaching_higher_semester_adm_req_parent_id,
                            'link_text_en',
                            true
                        );
                        $data[ 'admission_requirements' ][ 'teaching_degree_higher_semester' ][ 'parent' ][ 'link_url' ] = get_term_meta(
                            $teaching_higher_semester_adm_req_parent_id,
                            'link_url_en',
                            true
                        );
                    }
                }
                if ($master_degree_adm_req && ! is_wp_error($master_degree_adm_req)) {
                    $master_degree_adm_req_parent_id = $master_degree_adm_req[0]->parent;
                    $data['admission_requirements']['master']['name'] = get_term_meta($master_degree_adm_req[0]->term_id, 'name_en',true);
                    $data['admission_requirements']['master']['link_text'] = get_term_meta($master_degree_adm_req[0]->term_id, 'link_text_en',true);
                    $data['admission_requirements']['master']['link_url'] = get_term_meta($master_degree_adm_req[0]->term_id, 'link_url_en',true);
                    if (!empty($master_degree_adm_req_parent_id)) {
                        $data[ 'admission_requirements' ][ 'master' ][ 'parent' ][ 'name' ] = get_term_meta(
                            $master_degree_adm_req_parent_id,
                            'name_en',
                            true
                        );
                        $data[ 'admission_requirements' ][ 'master' ][ 'parent' ][ 'link_text' ] = get_term_meta(
                            $master_degree_adm_req_parent_id,
                            'link_text_en',
                            true
                        );
                        $data[ 'admission_requirements' ][ 'master' ][ 'parent' ][ 'link_url' ] = get_term_meta(
                            $master_degree_adm_req_parent_id,
                            'link_url_en',
                            true
                        );
                    }
                }
                if ($german_for_int_students && ! is_wp_error($german_for_int_students)) {
                    $data['german_language_skills_for_international_students']['name'] = get_term_meta($german_for_int_students[0]->term_id, 'name_en',true);
                    $data['german_language_skills_for_international_students']['link_text'] = get_term_meta($german_for_int_students[0]->term_id, 'link_text_en',true);
                    $data['german_language_skills_for_international_students']['link_url'] = get_term_meta($german_for_int_students[0]->term_id, 'link_url_en',true);
                }
                if ($examinations_office && ! is_wp_error($examinations_office)) {
                    $data['examinations_office']['name'] = get_term_meta($examinations_office[0]->term_id, 'name_en',true);
                    $data['examinations_office']['link_text'] = get_term_meta($examinations_office[0]->term_id, 'link_text_en',true);
                    $data['examinations_office']['link_url'] = get_term_meta($examinations_office[0]->term_id, 'link_url_en',true);
                }
                if ($subject_specific_advice && ! is_wp_error($subject_specific_advice)) {
                    $data['examinations_office']['name'] = get_term_meta($subject_specific_advice[0]->term_id, 'name_en',true);
                    $data['examinations_office']['link_url'] = get_term_meta($subject_specific_advice[0]->term_id, 'link_url_en',true);
                }
                if ($area_of_study && ! is_wp_error($area_of_study)) {
                    foreach ($area_of_study as $area) {
                        $data['area_of_study'][] = [
                            'name' => get_term_meta($area->term_id, 'name_en', true),
                            'link_text' => get_term_meta($area->term_id, 'link_text', true),
                            'link_url' => get_term_meta($area->term_id, 'link_url', true),
                        ];
                    }
                }
                if ($apply_now_link && ! is_wp_error($apply_now_link)) {
                    $data['apply_now_link']['name'] = get_term_meta($apply_now_link[0]->term_id, 'name_en',true);
                    $data['apply_now_link']['link_text'] = get_term_meta($apply_now_link[0]->term_id, 'link_text_en',true);
                    $data['apply_now_link']['link_url'] = get_term_meta($apply_now_link[0]->term_id, 'link_url_en',true);
                }

                break;
        }
        //print "<pre>"; print_r($data); print "</pre>"; exit;
        return $data;
    }

    /**
     * Weist einem Post einen Term einer Taxonomie zu (inkl. Parent).
     *
     * @param int    $post_id   ID des Posts
     * @param string $taxonomy  Slug der Taxonomie
     * @param string $term      Name des Child-Terms
     * @param string $parent    Name des Parent-Terms
     *
     * @return int|WP_Error Term-ID des Child-Terms oder Fehler
     */
    public static function assign_post_term($post_id, $taxonomy, $term, $parent = null) {
        $parent_id = false;

        if (!empty($parent)) {
            $parent_term = term_exists($parent, $taxonomy);
            if (!$parent_term) {
                $parent_term = wp_insert_term($parent, $taxonomy);
            }
            $parent_id = is_array($parent_term) ? $parent_term['term_id'] : false;
        }

        $child_term = term_exists($term, $taxonomy, ($parent_id ?? null));
        if ($child_term === null) {
            $args = [];
            if ($parent_id) {
                $args['parent'] = $parent_id;
            }
            $child_term = wp_insert_term($term, $taxonomy, $args);
        }
        $child_id = is_array($child_term) ? $child_term['term_id'] : false;

        if ($child_id !== false) {
            wp_set_object_terms($post_id, (int)$child_id, $taxonomy, true);
        }

        return $child_id;
    }
}