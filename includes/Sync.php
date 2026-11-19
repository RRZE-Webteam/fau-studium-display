<?php

namespace Fau\DegreeProgram\Display;

defined('ABSPATH') || exit;

class Sync
{
    private $api;

    public function __construct() {
        $this->api = new API();
    }

    public function sync_program($id, $post_id = '0') {

        $program = $this->api->get_program($id);
        if (empty($program['title'])) {
            return new \WP_Error();
        }

        $meta['program_id'] = $id;
        $meta['program_data'] = $program;

        $degree_abbreviations = [];
        $degrees = $program['degree'] ?? [];
        foreach ($degrees as $degree) {
            if (!empty($degree['abbreviation'])) {
                $degree_abbreviations[] = $degree['abbreviation'];
            }
        }
        $title = esc_attr($program['title'] . (!empty($degree_abbreviations) ? ' (' . implode('/ ', $degree_abbreviations) . ')' : ''));

        $author = get_post_field('post_author', $post_id);
        $result = wp_insert_post([
           'ID' => $post_id,
           'post_author' => $author,
           'post_title' => $title,
           'post_status' => 'publish',
           'post_type' => 'degree-program',
           'post_name' => sanitize_title($title),
           'meta_input' => $meta
        ]);

        if ( !is_wp_error($result) && $result > 0) {
            if (!empty($program['featured_image']['url'])) {
                $thumbnail_id = get_post_thumbnail_id($result);
                if ($thumbnail_id) {
                    // thumbnail already exists -> check if it has changed
                    $old_file = get_attached_file($thumbnail_id);
                    $old_filename = basename($old_file);
                    $old_filesize = filesize($old_file);
                    $new_filename = basename($program['featured_image']['url']);
                    $response = wp_remote_head( $program['featured_image']['url'] );
                    if ( ! is_wp_error( $response ) ) {
                        $new_filesize = wp_remote_retrieve_header( $response, 'content-length' );
                    } else {
                        $new_filesize = 0;
                    }
                    if (($old_filename != $new_filename) || ($old_filesize != $new_filesize)) {
                        // image has changed -> replace it
                        wp_delete_attachment($thumbnail_id, true);
                        delete_post_thumbnail($result);
                        $attachment_id = Utils::import_image_from_url($program[ 'featured_image' ][ 'url' ], $result, $program[ 'featured_image' ][ 'id' ]);
                        set_post_thumbnail($result, $attachment_id);
                    }
                } else {
                    // no thumbnail -> upload it
                    $attachment_id = Utils::import_image_from_url($program[ 'featured_image' ][ 'url' ], $result, $program[ 'featured_image' ][ 'id' ]);
                    set_post_thumbnail($result, $attachment_id);
                }
            }
            $taxonomies = get_object_taxonomies(get_post_type($result));
            foreach ($taxonomies as $taxonomy) {
                wp_set_object_terms($result, [], $taxonomy);
            }

            if (!empty($program['de']['degree'][0]['name'])) {
                foreach ($program['de']['degree'] as $degree) {
                    Utils::assign_post_term($result, 'degree', esc_attr($degree['name']), ($degree[ 'parent' ][ 'name' ] ?? null));
                }
            }
            if (!empty($program['de']['subject_group'][0]['name'])) {
                foreach ($program['de']['subject_group'] as $subject_group) {
                    Utils::assign_post_term($result, 'subject_group', $subject_group['name']);
                }
            }
            if (!empty($program['de']['attribute'][0]['name'])) {
                foreach ($program['de']['attribute'] as $attribute) {
                    Utils::assign_post_term($result, 'attribute', $attribute['name']);
                }
            }
            if (!empty($program['de']['adm_req_bachelor_teaching'][0]['name'])) {
                foreach ($program['de']['adm_req_bachelor_teaching'] as $adm_req_bachelor_teaching) {
                    Utils::assign_post_term($result, 'adm_req_bachelor_teaching', esc_attr($adm_req_bachelor_teaching['name']), ($adm_req_bachelor_teaching['parent']['name'] ?? null));
                }
            }
            if (!empty($program['de']['adm_req_bachelor_teaching_higher'][0]['name'])) {
                foreach ($program['de']['adm_req_bachelor_teaching_higher'] as $adm_req_bachelor_teaching_higher) {
                    Utils::assign_post_term($result, 'adm_req_bachelor_teaching_higher', esc_attr($adm_req_bachelor_teaching_higher['name']), ($adm_req_bachelor_teaching_higher['parent']['name'] ?? null));
                }
            }
            if (!empty($program['de']['adm_req_master'][0]['name'])) {
                foreach ($program['de']['adm_req_master'] as $adm_req_master) {
                    Utils::assign_post_term($result, 'adm_req_master', esc_attr($adm_req_master['name']), ($adm_req_master['parent']['name'] ?? null));
                }
            }
            if (!empty($program['de']['start_of_studies'][0]['name'])) {
                foreach ($program['de']['start_of_studies'] as $start) {
                    Utils::assign_post_term($result, 'start', $start['name']);
                }
            }
            if (!empty($program['de']['study_location'][0]['name'])) {
                foreach ($program['de']['study_location'] as $location) {
                    Utils::assign_post_term($result, 'location', $location['name']);
                }
            }
            if (!empty($program['de']['teaching_language'][0]['name'])) {
                foreach ($program['de']['teaching_language'] as $language) {
                    Utils::assign_post_term($result, 'teaching_language', esc_attr($language['name']));
                }
            }
            if (!empty($program['de']['faculty'][0]['name'])) {
                foreach ($program['de']['faculty'] as $faculty) {
                    if (!empty($faculty['name'])) {
                        Utils::assign_post_term($result, 'faculty', $faculty['name']);
                    }
                }
            }
            if (!empty($program['de']['german_for_int_students'][0]['name'])) {
                foreach ($program['de']['german_for_int_students'] as $german) {
                    Utils::assign_post_term($result, 'german_language_skills', esc_attr($german['name']), ($german[ 'parent' ][ 'name' ] ?? null));
                }
            }
        }

        return $result;
    }

}