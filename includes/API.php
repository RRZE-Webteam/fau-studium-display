<?php

namespace FAU\StudiumDisplay;

class API
{
    private $api_url;

    public function __construct() {
        //$this->api_url = 'https://meinstudium.fau.de/wp-json/fau/v1/degree-program';
        $this->api_url = 'http://localhost/wp/meinstudium/wp-json/fau/v1/degree-program';
    }
    public function get_programs($format = '', $show_empty = false, $lang = 'de') {
        $transient_name = 'fau_studium_degree_programs_list' . ($format ? '_' . $format : '') . '_' . $lang;
        $degree_programs = get_transient($transient_name);
        //$degree_programs = false;
        if (false === $degree_programs) {
            $response = wp_remote_get($this->api_url);
            if (!is_wp_error($response)) {
                $data = json_decode(wp_remote_retrieve_body($response), true);
                foreach ($data as $k => $v) {
                    $data[$k] = $this->get_localized_data($v, $lang);
                }
                if ($format === 'id_title') {
                    $degree_programs = array_map(
                        fn($item) => [
                            'label' => $item[ 'title' ] . ' (' . $item[ 'degree' ][ 'abbreviation' ] . ')',
                            'value' => (string)$item[ 'id' ],
                        ],
                        $data
                    );
                } else {
                    $degree_programs = $data;
                }
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

    public function get_program($id, $lang) {
        $transient_name = 'fau_studium_degree_program_' . $id . '_' . $lang;
        $degree_program = get_transient($transient_name);
        $degree_program = false;
        if (false === $degree_program || isset( $degree_program['code'])) {
            $response = wp_remote_get($this->api_url.'/'.$id);
            if (!is_wp_error($response)) {
                $degree_program = json_decode(wp_remote_retrieve_body($response), true);
                $degree_program = $this->get_localized_data($degree_program, $lang);
                set_transient($transient_name, $degree_program, DAY_IN_SECONDS);
            } else {
                $degree_program = [];
            }
        }

        return $degree_program;
    }

    public function get_degrees($parents = false) {
        $transient_name = 'fau_studium_degrees';
        $degrees = get_transient($transient_name);
        if (false === $degrees) {
            $degrees = [];
            $programs = $this->get_programs();
            foreach ($programs as $program) {
                if ($parents) {
                    $degrees[] = $program['degree']['parent']['name'] ?? $program['degree']['name'];
                } else {
                    $degrees[] = $program['degree']['name'];
                }
            }
            set_transient($transient_name, $degrees, DAY_IN_SECONDS);
        }
        $degrees = array_unique($degrees);
        sort($degrees);

        return $degrees;
    }

    public function get_subject_groups() {
        $transient_name = 'fau_studium_subject_groups';
        $subject_groups = get_transient($transient_name);
        if (false === $subject_groups) {
            $subject_groups = [];
            $programs = $this->get_programs();
            foreach ($programs as $program) {
                if (!empty($program['subject_groups'])) {
                    foreach ($program['subject_groups'] as $group) {
                        $subject_groups[] = $group;
                    }
                }
            }
            set_transient($transient_name, $subject_groups, DAY_IN_SECONDS);
        }
        $subject_groups = array_unique($subject_groups);
        sort($subject_groups);

        return $subject_groups;
    }

    public function get_attributes() {
        $transient_name = 'fau_studium_attributes';
        $attributes = get_transient($transient_name);
        if (false === $attributes) {
            $attributes = [];
            $programs = $this->get_programs();
            foreach ($programs as $program) {
                if (!empty($program['attributes'])) {
                    foreach ($program['attributes'] as $attribute) {
                        $attributes[] = $attribute;
                    }
                }
            }
            set_transient($transient_name, $attributes, DAY_IN_SECONDS);
        }
        $attributes = array_unique($attributes);
        sort($attributes);

        return $attributes;
    }

    public function get_teaching_languages() {
        $transient_name = 'fau_studium_teaching_languages';
        $teaching_languages = get_transient($transient_name);
        if (false === $teaching_languages) {
            $teaching_languages = [];
            $programs = $this->get_programs();
            foreach ($programs as $program) {
                if (!empty($program['teaching_language'])) {
                    $teaching_languages[] = $program['teaching_language'];
                }
            }
        }
        $teaching_languages = array_unique($teaching_languages);
        sort($teaching_languages);
        return $teaching_languages;
    }

    public function get_start_semesters() {
        $transient_name = 'fau_studium_start_semesters';
        $start_semesters = get_transient($transient_name);
        if (false === $start_semesters) {
            $start_semesters = [];
            $programs = $this->get_programs();
            foreach ($programs as $program) {
                if (!empty($program['start'])) {
                    foreach ($program['start'] as $start) {
                        $start_semesters[] = $start;
                    }
                }
            }
        }
        $start_semesters = array_unique($start_semesters);
        sort($start_semesters);
        return $start_semesters;
    }

    public function get_study_locations() {
        $transient_name = 'fau_studium_study_locations';
        $study_locations = get_transient($transient_name);
        if (false === $study_locations) {
            $study_locations = [];
            $programs = $this->get_programs();
            foreach ($programs as $program) {
                if (!empty($program['location'])) {
                    foreach ($program['location'] as $location) {
                        $study_locations[] = $location;
                    }
                }
            }
        }
        $study_locations = array_unique($study_locations);
        sort($study_locations);
        return $study_locations;
    }

    public function get_faculties() {
        $transient_name = 'fau_studium_faculties';
        $faculties = get_transient($transient_name);
        if (false === $faculties) {
            $faculties = [];
            $programs = $this->get_programs();
            foreach ($programs as $program) {
                if (!empty($program['faculty'])) {
                    foreach ($program['faculty'] as $faculty) {
                        $faculties[] = $faculty['name'] ?? '';
                    }
                }
            }
        }
        $faculties = array_unique($faculties);
        sort($faculties);
        return $faculties;
    }

    public function get_areas_of_study() {
        $transient_name = 'fau_studium_areas_of_study';
        $areas_of_studys = get_transient($transient_name);
        if (false === $areas_of_studys) {
            $areas_of_studys = [];
            $programs = $this->get_programs();
            foreach ($programs as $program) {
                if (!empty($program['area_of_study'])) {
                    foreach ($program['area_of_study'] as $area) {
                        $areas_of_studys[] = $area['name'] ?? '';
                    }
                }
            }
        }
        $areas_of_studys = array_unique($areas_of_studys);
        sort($areas_of_studys);
        return $areas_of_studys;
    }

    private function get_localized_data($data, $lang = 'de') {
        if ($lang == 'de') {
            unset($data['translations']);
        } elseif ($lang == 'en') {
            $data = $data['translations']['en'] ?? $data;
        }
        return $data;
    }
}