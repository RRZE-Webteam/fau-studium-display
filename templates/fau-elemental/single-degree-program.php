<?php

/**
 * The template for displaying single degree programs
 *
 * @package FAU-Elemental
 */

use Fau\DegreeProgram\Display\Output;
use function Fau\DegreeProgram\Display\Config\get_output_fields;

$output_fields = cmb2_get_option('fau-studium-display_layout', 'single_items' );
if (!$output_fields) {
    $output_fields_all = get_output_fields();
    $output_fields = $output_fields_all['full'];
}
$atts = [
    'format' => 'full',
    'degreeProgram' => $post->ID, // if used on meinstudium.fau.de
    'post_id' => $post->ID,
    'selectedItemsFull' => $output_fields,
];
$output = new Output();
$content = $output->renderOutput($atts);

get_header();

?>

    <main id="main" class="site-main" role="main">
        <?php
        if (have_posts()) :
            while (have_posts()) :
                the_post();
                ?>
                <article id="post-<?php echo esc_attr(get_the_ID()); ?>" <?php post_class(); ?>>
                    <div class="is-layout-flow faue-content-wrapper">
                        <?php echo do_shortcode($content); ?>
                    </div>

                </article>
            <?php
            endwhile;
        endif;
        ?>
    </main>

<?php
get_footer();
