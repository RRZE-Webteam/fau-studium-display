<?php
/**
 * The template for displaying all pages.
 * @package WordPress
 * @subpackage FAU
 * @since FAU 1.0
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
    'degreeProgram' => $post->ID,
    'post_id' => $post->ID,
    'selectedItemsFull' => $output_fields,
];
$output = new Output();
$content = $output->renderOutput($atts);


get_header();

while ( have_posts() ) : the_post(); ?>
    <div id="content">
        <div class="content-container">
            <div class="content-row">
                <main<?php echo fau_get_page_langcode($post->ID);?>>
                    <h1 id="maintop" class="screen-reader-text"><?php the_title(); ?></h1>
                    <?php
                    $headline = get_post_meta( $post->ID, 'headline', true );
                    if (!fau_empty($headline)) {
                        echo '<p class="subtitle">'.$headline.'</p>';
                    } ?>
                    <div class="inline-box">
                        <?php get_template_part('template-parts/sidebar', 'inline'); ?>

                        <div class="content-inline">
                            <?php echo do_shortcode($content); ?>
                        </div>
                    </div>
                </main>

            </div>
        </div>
    </div>


<?php endwhile;
get_footer();