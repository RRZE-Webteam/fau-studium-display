<?php

defined('ABSPATH') || exit;

//print "<pre>";var_dump($data); print "</pre>";exit;

$linkTarget = $atts['linkTarget'] ?? 'local';

$program_list = '';
foreach ($data as $post_id => $program) {

    if (!empty($program) && !empty($program['title'])) {
        $url = match ($linkTarget) {
            'local' => get_permalink($post_id),
            'remote' => ! empty($program[ 'link' ]) ? esc_url($program[ 'link' ]) : '',
            default => '',
        };
        $title = $program['title'] . (!empty($program[ 'degree' ][ 'abbreviation' ]) ? ' (' . $program[ 'degree' ][ 'abbreviation' ] . ')' : '');

        if (!empty($url)) {
            $program_list .= sprintf('<li><a href="%s">%s</a></li>', $url,  $title);
        } else {
            $program_list .= sprintf('<li>%s</li>', $title);
        }
    }

}

?>

<section class="fau-studium-display degree-program-list">

    <?php if (!empty($program_list)) : ?>

        <ul class="degree-program-list">
            <?php echo $program_list; ?>
        </ul>

    <?php endif; ?>

</section>
