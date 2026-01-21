<?php

namespace Fau\DegreeProgram\Display;

defined('ABSPATH') || exit;

class Cron
{
    public function init() {
        add_action('init', [$this, 'scheduleDegreeProgramCrons']);
        add_action('fau_studium_display_schedule_sync_batches', [$this, 'scheduleDegreeProgramSyncBatches']);
        add_action('fau_studium_display_sync_batch',[$this, 'doDegreeProgramSyncBatches'], 10, 2);
    }

    public function scheduleDegreeProgramCrons() {
        $wp_tz = wp_timezone();
        $dt = new \DateTime('tomorrow 4:00', $wp_tz);

        if (!wp_next_scheduled('fau_studium_display_schedule_sync_batches')) {
            wp_schedule_event($dt->getTimestamp(), 'daily', 'fau_studium_display_schedule_sync_batches');
        }

    }

    public function scheduleDegreeProgramSyncBatches() {

        $existing = wp_get_scheduled_event('fau_studium_display_sync_batch');
        if ($existing) {
            return;
        }

        $program_ids = get_posts([
            'post_type'      => 'degree-program',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'orderby'        => 'ID',
            'order'          => 'ASC',
            'no_found_rows'  => true,
            'cache_results'             => false,
            'update_post_meta_cache'    => false,
            'update_post_term_cache'    => false,
            'suppress_filters'          => true,
        ]);

        if (empty($program_ids)) {
            return;
        }

        $batchSize  = 10;
        $numBatches = ceil(count($program_ids) / $batchSize);

        for ($i = 0; $i < $numBatches; $i++) {
            wp_schedule_single_event(
                time() + ($i * 5 * 60),
                'fau_studium_display_sync_batch',
                [
                    'offset' => $i * $batchSize,
                    'number' => $batchSize,
                ]
            );
        }
    }

    public function doDegreeProgramSyncBatches($offset, $number) {
        //error_log('Cron batch ' . $offset.'/'. $number . ' started at ' . current_time('mysql'));
        $programs = get_posts([
            'post_type'      => 'degree-program',
            'post_status'    => 'publish',
            'posts_per_page' => $number,
            'offset'         => $offset,
            'orderby'        => 'ID',
            'order'          => 'ASC',
            'fields'         => 'ids',
            'no_found_rows'  => true,
            'cache_results'  => false,
        ]);

        if (empty($programs)) {
            return;
        }

        $sync = new Sync();

        foreach ($programs as $post_id) {
            try {
                $program_id = get_post_meta($post_id, 'program_id', true);
                $sync->sync_program($program_id, $post_id);
                //error_log('Cron for degree program ' . $program_id.'/'. $post_id . ' ended at ' . current_time('mysql'));
            } catch (\Throwable $e) {
                error_log('Degree program sync failed for "' . get_the_title($post_id) . '": ' . $e->getMessage());
            }
        }
        //error_log('Cron batch ' . $offset.'/'. $number . ' completed at ' . current_time('mysql'));
    }

}