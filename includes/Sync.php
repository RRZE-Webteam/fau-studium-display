<?php

namespace FAU\StudiumDisplay;

defined('ABSPATH') || exit;

class Sync
{
    public function do_sync() {
        $programs = get_transient( 'fau_studium_degree_programs_sync' );

        $existing_programs = get_posts( [
            'post_type' => 'degree-program',
            'post_status' => 'publish',
            'posts_per_page' => -1,]);
        $existing_ids = [];
        foreach ( $existing_programs as $existing_program ) {
            $existing_ids[$existing_program->ID] = get_post_meta( $existing_program->ID, 'id', true ); // [local ID => original ID]
        }

        $api = new API();
        foreach ( $programs as $lang => $ids ) {
            foreach ( $ids as $id ) {
                $program = $api->get_program($id, $lang);
                if ( empty($program) || !isset($program['title'])) continue;

                $title = esc_attr($program['title'] . ' (' . $program['degree']['abbreviation'] . ')');
                if (in_array($program['id'], $existing_ids)) {
                    $program_id = array_search($program['id'], $existing_ids);
                } else {
                    $program_id = 0;
                }
                //var_dump($program_id, $program['id'], $existing_ids); exit;
                wp_insert_post([
                   'ID' => $program_id,
                   'post_title' => $title,
                   'post_status' => 'publish',
                   'post_type' => 'degree-program',
                   'post_name' => sanitize_title($title),
                   'meta_input' => $program
               ]);

            }
        }
    }

}