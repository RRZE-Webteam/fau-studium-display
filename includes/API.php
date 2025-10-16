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

}