<?php
/**
 * The template for displaying degree program archive pages with filtering and pagination
 * @package Fau-Elemental
 */

use Fau\DegreeProgram\Display\Output;
use function Fau\DegreeProgram\Display\Config\get_output_fields;

$format = cmb2_get_option('fau-studium-display_layout', 'archive_view', 'grid');
$output_fields = cmb2_get_option('fau-studium-display_layout', 'grid_items' );
if (!$output_fields) {
    $output_fields = get_output_fields('grid');
}
$show_search = cmb2_get_option('fau-studium-display_layout', 'archive_search', '');
$search_filters = cmb2_get_option('fau-studium-display_layout', 'archive_search_filters');
if (!$search_filters) {
    $search_filters = get_output_fields('search-items');
}
$atts = [
    'format' => $format,
    'selectedItemsGrid' => $output_fields,
    'showSearch' => ($show_search == 'on'),
    'selectedFaculties' => [],
    'selectedDegrees' => [],
    'selectedSpecialWays' => [],
    'selectedSearchFilters' => $search_filters,
];
$output = new Output();
$content = $output->renderOutput($atts);

get_header(); ?>

<main class="archive-page">
    <header class="blog-header is-layout-flow">
        <h1 class="blog-title">
            <?php 
            if (is_category()) {
                echo esc_html(single_cat_title('', false));
            } elseif (is_tag()) {
                echo esc_html(single_tag_title('', false));
            } elseif (is_author()) {
                echo esc_html(get_the_author());
            } elseif (is_date()) {
                if (is_year()) {
                    echo esc_html(get_the_date('Y'));
                } elseif (is_month()) {
                    echo esc_html(get_the_date('F Y'));
                } elseif (is_day()) {
                    echo esc_html(get_the_date());
                }
            } else {
                the_archive_title();
            }
            ?>
        </h1>
    </header>

    <div class="is-layout-flow">
        <div class="is-layout-flow faue-content-wrapper">
            <?php echo do_shortcode($content); ?>
        </div>
    </div>

</main>

<?php get_footer(); ?>
