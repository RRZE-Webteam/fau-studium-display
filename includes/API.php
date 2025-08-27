<?php

namespace Fau\DegreeProgram\Display;

class API
{
    private $api_url;

    public function __construct() {
        $this->api_url = 'https://meinstudium.fau.de/wp-json/fau/v1/degree-program';
        //$this->api_url = 'http://localhost/wp/meinstudium/wp-json/fau/v1/degree-program';
    }

    public static function isUsingNetworkKey()
    {
        if (is_multisite()) {
            $settingsOptions = get_site_option('rrze_settings');
            if (!empty($settingsOptions->plugins->dip_edu_api_key)) {
                return true;
            }
        }
        return false;
    }

    public function get_programs($show_empty = false) {
        $transient_name = 'fau_studium_degree_programs_list';
        $degree_programs = get_transient($transient_name);
        //$degree_programs = false;
        if (false === $degree_programs || '' === $degree_programs) {
            $response = wp_remote_get($this->api_url);
            if (!is_wp_error($response)) {
                $degree_programs = json_decode(wp_remote_retrieve_body($response), true);
                set_transient($transient_name, $degree_programs, DAY_IN_SECONDS);
            } else {
                $degree_programs = [];
            }
        }
        if ($show_empty) {
            $empty_option[ 0 ] = [
                'label' => __('-- Please select --', 'fau-studium-display'),
                'value' => '0',
            ];
            $degree_programs = array_merge($empty_option, $degree_programs);
        }
        return $degree_programs;
    }

    public function get_program($program_id) {
        $transient_name = 'fau_studium_degree_program_' . $program_id;
        $degree_program = get_transient($transient_name);
        //$degree_program = false;
        if (false === $degree_program || isset( $degree_program['code'])) {
            $response = wp_remote_get($this->api_url.'/'.$program_id);
            if (is_wp_error($response)) {
                return [];
            }
            $degree_program = json_decode(wp_remote_retrieve_body($response), true);
            set_transient($transient_name, $degree_program, DAY_IN_SECONDS);

        }

        return $degree_program;
    }

    public function get_meta_list($meta) {
        $transient_name = 'fau_studium_'.$meta;
        $meta_list = get_transient($transient_name);
        if (false === $meta_list) {
            $meta_list = [];
            $programs = $this->get_programs();
            foreach ($programs as $program) {
                switch ($meta) {
                    case 'degrees':
                        $meta_list[] = $program['degree']['name'];
                        break;
                    case 'degree_parents':
                        $meta_list[] = $program['degree']['parent']['name'] ?? $program['degree']['name'];
                        break;
                    case 'subject_groups':
                        if (!empty($program['subject_groups'])) {
                            foreach ($program['subject_groups'] as $group) {
                                $meta_list[] = $group;
                            }
                        }
                        break;
                    case 'attributes':
                        if (!empty($program['attributes'])) {
                            foreach ($program['attributes'] as $attribute) {
                                $meta_list[] = $attribute;
                            }
                        }
                        break;
                    case 'teaching_languages':
                        if (!empty($program['teaching_language'])) {
                            $meta_list[] = $program['teaching_language'];
                        }
                        break;
                    case 'start_semesters':
                        if (!empty($program['start'])) {
                            foreach ($program['start'] as $start) {
                                $meta_list[] = $start;
                            }
                        }
                        break;
                    case 'study_locations':
                        if (!empty($program['location'])) {
                            foreach ($program['location'] as $location) {
                                $meta_list[] = $location;
                            }
                        }
                        break;
                    case 'faculties':
                        if (!empty($program['faculty'])) {
                            foreach ($program['faculty'] as $faculty) {
                                $meta_list[] = $faculty['name'] ?? '';
                            }
                        }
                        break;
                    case 'areas_of_study':
                        if (!empty($program['area_of_study'])) {
                            foreach ($program['area_of_study'] as $area) {
                                $meta_list[] = $area['name'] ?? '';
                            }
                        }
                        break;
                    case 'admission_requirements':
                        if (!empty($program['admission_requirements'])) {
                            foreach ($program['admission_requirements'] as $level) {
                                if (!empty($level['parent']['name'])) {
                                    $meta_list[] = $level['parent']['name'];
                                }
                            }
                        }
                        break;
                    case 'german_language_skills':
                        if (!empty($program['german_language_skills_for_international_students']['name'])) {
                            $meta_list[] = $program['german_language_skills_for_international_students']['name'];
                        }
                        break;
                }
            }
        }
        $meta_list = array_unique($meta_list);
        sort($meta_list);
        return $meta_list;
    }

    public function get_localized_data($data, $lang = 'de') {
        if ($lang == 'de') {
            unset($data['translations']);
        } elseif ($lang == 'en') {
            $data = $data['translations']['en'] ?? $data;
        }
        return $data;
    }
}