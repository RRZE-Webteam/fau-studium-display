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

            if (!empty($program['degree'][0]['name'])) {
                foreach ($program['degree'] as $degree) {
                    Utils::assign_post_term($result, 'degree', esc_attr($degree['name']), ($degree[ 'parent' ][ 'name' ] ?? null));
                }
            }
            if (!empty($program['subject_group'])) {
                foreach ($program['subject_group'] as $subject_group) {
                    Utils::assign_post_term($result, 'subject_group', $subject_group);
                }
            }
            if (!empty($program['attributes'])) {
                foreach ($program['attributes'] as $attribute) {
                    Utils::assign_post_term($result, 'attribute', $attribute);
                }
            }
            if (!empty($program['admission_requirement_link'])) {
                Utils::assign_post_term($result, 'admission_requirement', esc_attr($program['admission_requirement_link']['name']), ($program['admission_requirement_link'][ 'parent']['name'] ?? null));
            }
            if (!empty($program['start'])) {
                foreach ($program['start'] as $start) {
                    Utils::assign_post_term($result, 'start', $start);
                }
            }
            if (!empty($program['location'])) {
                foreach ($program['location'] as $location) {
                    Utils::assign_post_term($result, 'location', $location);
                }
            }
            if (!empty($program['teaching_language'])) {
                Utils::assign_post_term($result, 'teaching_language', esc_attr($program['teaching_language']));
            }
            if (!empty($program['faculty'])) {
                foreach ($program['faculty'] as $faculty) {
                    if (!empty($faculty['name'])) {
                        Utils::assign_post_term($result, 'faculty', $faculty['name']);
                    }
                }
            }
            if (!empty($program['german_language_skills_for_international_students']['name'])) {
                Utils::assign_post_term($result, 'german_language_skills', $program['german_language_skills_for_international_students']['name']);
            }
        }

        return $result;
    }

}