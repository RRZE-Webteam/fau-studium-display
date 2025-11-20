<?php

namespace Fau\DegreeProgram\Display;

defined('ABSPATH') || exit;

class Cron
{
    public function init() {
        add_action('init', [$this, 'scheduleDegreeProgramCrons']);
        add_action('fau_studium_display_sync_programs', [$this, 'cronSyncPrograms']);
    }

    public static function scheduleDegreeProgramCrons() {
        $wp_tz = wp_timezone();
        $dt = new \DateTime('today 4:00', $wp_tz);

        if (!wp_next_scheduled('fau_studium_display_sync_programs')) {
            wp_schedule_event($dt->getTimestamp(), 'daily', 'fau_studium_display_sync_programs');
        }

    }

    public static function cronSyncPrograms() {
        $programs_imported = get_posts([
            'post_type'      => 'degree-program',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ]);

        if (empty($programs_imported)) {
            return;
        }

        $sync = new Sync();
        foreach ($programs_imported as $post_id) {
            $program_id = get_post_meta($post_id, 'id', true);
            $sync->sync_program($program_id, $post_id);
        }
    }

}