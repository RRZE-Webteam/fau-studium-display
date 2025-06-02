<?php

defined('ABSPATH') || exit;

//print "<pre>"; var_dump($data);print "</pre>"; exit;

$program_list = '';
foreach ($data as $program) {
    if (!empty($program)) {
        //print "<pre>"; var_dump($program); print "</pre>";
        $program_list .= sprintf('<li><a href="%s">%s</a></li>',
            $program['link'],
            '<div class="teaser-image">' . $program['teaser_image']['rendered'] . '</div><div class="program-title">' . $program['title'] . ' (' . $program[ 'degree' ][ 'abbreviation' ] . ')</div>');
    }
}

?>

<section class="fau-studium-display degree-program-grid">

    <?php if (!empty($program_list)) : ?>

        <ul class="degree-program-grid">
            <?php echo $program_list; ?>
        </ul>

    <?php endif; ?>

</section>