<?php

namespace Fau\DegreeProgram\Display;

defined('ABSPATH') || exit;

class Sync
{
    private $api;

    public function __construct() {
        $this->api = new API();
    }

    public function do_sync() {
        $programs = get_transient( 'fau_studium_degree_programs_sync' );

        foreach ( $programs as $lang => $ids ) {
            foreach ( $ids as $id ) {
                if ( empty($program) || !isset($program['title'])) continue;
                $existing_programs = get_posts( [
                                                    'post_type' => 'degree-program',
                                                    'post_status' => 'publish',
                                                    'posts_per_page' => -1,]);
                $existing_ids = [];
                foreach ( $existing_programs as $existing_program ) {
                    $existing_ids[$existing_program->ID] = get_post_meta( $existing_program->ID, 'id', true ); // [local ID => original ID]
                }
                if (in_array($program['id'], $existing_ids)) {
                    $post_id = array_search($program['id'], $existing_ids);
                } else {
                    $post_id = 0;
                }
                $this->sync_program( $id, $lang, $post_id );
            }
        }
    }

    public function sync_program($id, $post_id = '0') {

        $program = $this->api->get_program($id);

        $title = esc_attr($program['title'] . ' (' . $program['degree']['abbreviation'] . ')');

        //var_dump($program_id, $program['id'], $existing_ids); exit;
        $result = wp_insert_post([
           'ID' => $post_id,
           'post_title' => $title,
           'post_status' => 'publish',
           'post_type' => 'degree-program',
           'post_name' => sanitize_title($title),
           'meta_input' => $program
        ]);

        if ( !is_wp_error($result) && $result > 0 && !empty($program['featured_image']['url'])) {
            $attachment_id = Utils::import_image_from_url($program['featured_image']['url'], $result);
            set_post_thumbnail($result, $attachment_id);
        }

        return $result;
    }

}