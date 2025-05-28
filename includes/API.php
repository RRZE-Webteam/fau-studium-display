<?php

namespace FAU\StudiumDisplay;

class API
{
    public function get_programs() {
        $degree_programs = get_transient('fau_studium_degree_programs_list');
        //$degree_programs = false;
        if (false === $degree_programs) {
            $response = wp_remote_get('https://meinstudium.fau.de/wp-json/fau/v1/degree-program');
            if (!is_wp_error($response)) {
                $data = json_decode(wp_remote_retrieve_body($response), true);

                $degree_programs = array_map(
                    fn($item) => [
                        'label' => $item['degree']['abbreviation'] . ' ' . $item['title'],
                        'value' => (string)$item['id'],
                    ],
                    $data
                );
                //var_dump($degree_programs); exit;
                set_transient('fau_studium_degree_programs_list', $degree_programs, DAY_IN_SECONDS);
            } else {
                $degree_programs = [];
            }
        }

        return $degree_programs;
    }
}