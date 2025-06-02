<?php

defined('ABSPATH') || exit;

//print "<pre>";var_dump($data); print "</pre>";exit;

$program_list = '';
foreach ($data as $program) {
    if (!empty($program)) {
       $program_list .= sprintf('<li><a href="%s">%s</a></li>', $program['link'],  $program['title'] . ' (' . $program[ 'degree' ][ 'abbreviation' ] . ')');
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
