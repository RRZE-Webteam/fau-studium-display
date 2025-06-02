<?php

namespace FAU\StudiumDisplay;

class API
{
    private $api_url;

    public function __construct() {
        $this->api_url = 'https://meinstudium.fau.de/wp-json/fau/v1/degree-program';
    }
    public function get_programs($format = '', $show_empty = false) {
        $transient_name = 'fau_studium_degree_programs_list';
        $degree_programs = get_transient($transient_name);
        //$degree_programs = false;
        if (false === $degree_programs) {
            $response = wp_remote_get($this->api_url);
            if (!is_wp_error($response)) {
                $data = json_decode(wp_remote_retrieve_body($response), true);

                if ($format === 'id_title') {
                    $degree_programs = array_map(
                        fn($item) => [
                            'label' => $item[ 'degree' ][ 'abbreviation' ] . ' ' . $item[ 'title' ],
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

    public function get_program($id) {
        $transient_name = 'fau_studium_degree_program_' . $id;
        $degree_program = get_transient($transient_name);
        //$degree_program = false;
        if (false === $degree_program) {
            $response = wp_remote_get($this->api_url.'/'.$id);
            if (!is_wp_error($response)) {
                $degree_program = json_decode(wp_remote_retrieve_body($response), true);
                set_transient($transient_name, $degree_program, DAY_IN_SECONDS);
            } else {
                $degree_program = [];
            }
        }

        return $degree_program;
    }
}