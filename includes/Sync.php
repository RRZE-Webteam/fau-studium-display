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

        $meta = [];
        $program = $this->api->get_program($id);
        foreach ($program as $key => $value) {
            if (empty($program['title'])) {
                continue;
            }
            if ($key != 'translations') {
                $meta['program_data_de'][$key] = $value;
            }
        }
        $meta['program_data_en'] = $program['translations']['en'] ?? [];
        $meta['program_id'] = $id;
        $title = esc_attr($program['title'] . (isset($program['degree']['abbreviation']) ? ' (' . $program['degree']['abbreviation'] . ')' : ''));

        $result = wp_insert_post([
           'ID' => $post_id,
           'post_title' => $title,
           'post_status' => 'publish',
           'post_type' => 'degree-program',
           'post_name' => sanitize_title($title),
           'meta_input' => $meta
        ]);

        if ( !is_wp_error($result) && $result > 0) {
            if (!empty($program['featured_image']['url'])) {
                $attachment_id = Utils::import_image_from_url($program[ 'featured_image' ][ 'url' ], $result);
                set_post_thumbnail($result, $attachment_id);
            }
            if (!empty($program['degree']['name'])) {
                Utils::assign_post_term($result, 'degree', esc_attr($program['degree']['name']), ($program[ 'degree' ][ 'parent' ][ 'name' ] ?? null));
            }
            if (!empty($program['subject_groups'])) {
                foreach ($program['subject_groups'] as $subject_group) {
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