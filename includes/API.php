<?php

namespace Fau\DegreeProgram\Display;

class API
{
    private $api_url;

    public function __construct() {
        $this->api_url = 'https://meinstudium.fau.de/wp-json/fau/v1/degree-program';
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

    public function get_programs($show_empty = false, $filter = []) {

        $base_url = $this->api_url;
        $page     = 1;

        $url      = add_query_arg(['per_page' => 100, 'page' => $page], $base_url);
        $response = wp_remote_get($url);

        if (is_wp_error($response)) {
            return $filter;
        }
        if (empty($response)) {
            return new \WP_Error('api_empty', __('Empty response from API', 'fau-studium-display'));
        }

        $headers       = wp_remote_retrieve_headers($response);
        $total_pages   = isset($headers['x-wp-totalpages']) ? (int) $headers['x-wp-totalpages'] : 1;
        $body          = wp_remote_retrieve_body($response);
        $degree_programs = json_decode($body, true);

        for ($page = 2; $page <= $total_pages; $page++) {
            $url      = add_query_arg(['per_page' => 100, 'page' => $page], $base_url);
            $response = wp_remote_get($url);

            if (is_wp_error($response)) {
                break;
            }

            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);

            if (empty($data)) {
                break;
            }

            $degree_programs = array_merge($degree_programs, $data);
        }


        if ($show_empty) {
            $empty_option[ 0 ] = [
                'label' => __('-- Please select --', 'fau-studium-display'),
                'value' => '0',
            ];
            $degree_programs = array_merge($empty_option, $degree_programs);
        }

        if (!empty($filter)) {
            $degree_programs = Utils::filterPrograms($degree_programs, $filter);
        }

        return $degree_programs;
    }

    public function get_program($program_id) {
        $response = wp_remote_get($this->api_url.'/'.$program_id);
        if (is_wp_error($response) || empty($response)) {
            return [];
        }

        return json_decode(wp_remote_retrieve_body($response), true);
    }

    public function get_meta_list($meta) {
        $transient_name = 'fau_studium_display_' . $meta;
        $meta_list = get_transient($transient_name);

        if (empty($meta_list)) {
            $meta_list = [];
            $programs  = $this->get_programs();
            foreach ($programs as $program) {
                switch ($meta) {
                    case 'degrees':
                        $meta_list[] = $program[ 'degree' ][ 'name' ];
                        break;
                    case 'degree_parents':
                        $meta_list[] = $program[ 'degree' ][ 'parent' ][ 'name' ] ?? $program[ 'degree' ][ 'name' ];
                        break;
                    case 'subject_groups':
                        if ( ! empty($program[ 'subject_groups' ])) {
                            foreach ($program[ 'subject_groups' ] as $group) {
                                $meta_list[] = $group;
                            }
                        }
                        break;
                    case 'attributes':
                        if ( ! empty($program[ 'attributes' ])) {
                            foreach ($program[ 'attributes' ] as $attribute) {
                                $meta_list[] = $attribute;
                            }
                        }
                        break;
                    case 'teaching_languages':
                        if ( ! empty($program[ 'teaching_language' ])) {
                            $meta_list[] = $program[ 'teaching_language' ];
                        }
                        break;
                    case 'start_semesters':
                        if ( ! empty($program[ 'start' ])) {
                            foreach ($program[ 'start' ] as $start) {
                                $meta_list[] = $start;
                            }
                        }
                        break;
                    case 'study_locations':
                        if ( ! empty($program[ 'location' ])) {
                            foreach ($program[ 'location' ] as $location) {
                                $meta_list[] = $location;
                            }
                        }
                        break;
                    case 'faculties':
                        if ( ! empty($program[ 'faculty' ])) {
                            foreach ($program[ 'faculty' ] as $faculty) {
                                $meta_list[] = $faculty[ 'name' ] ?? '';
                            }
                        }
                        break;
                    case 'areas_of_study':
                        if ( ! empty($program[ 'area_of_study' ])) {
                            foreach ($program[ 'area_of_study' ] as $area) {
                                $meta_list[] = $area[ 'name' ] ?? '';
                            }
                        }
                        break;
                    case 'admission_requirements':
                        if ( ! empty($program[ 'admission_requirements' ])) {
                            foreach ($program[ 'admission_requirements' ] as $level) {
                                if ( ! empty($level[ 'parent' ][ 'name' ])) {
                                    $meta_list[] = $level[ 'parent' ][ 'name' ];
                                }
                            }
                        }
                        break;
                    case 'german_language_skills':
                        if ( ! empty($program[ 'german_language_skills_for_international_students' ][ 'name' ])) {
                            $meta_list[] = $program[ 'german_language_skills_for_international_students' ][ 'name' ];
                        }
                        break;
                }
            }

            $meta_list = array_unique($meta_list);
            sort($meta_list);
            set_transient($transient_name, $meta_list, DAY_IN_SECONDS);
        }
        return $meta_list;
    }

}