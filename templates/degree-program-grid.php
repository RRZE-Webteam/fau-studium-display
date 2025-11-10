<?php

defined('ABSPATH') || exit;

use Fau\DegreeProgram\Display\Utils;

use function Fau\DegreeProgram\Display\Config\get_output_fields;
use function Fau\DegreeProgram\Display\Config\get_labels;

//print "<pre>"; var_dump($data);print "</pre>";
//print "<pre>"; var_dump($atts);print "</pre>";
//exit;

$show_search = isset($atts['showSearch']) && $atts['showSearch'] == '1';
if (empty($data) && !$show_search)
    return;

$items = $atts['selectedItemsGrid'] ?? [];
$lang = $atts['language'] ?? 'de';
$labels = get_labels($lang);
//var_dump($labels); exit;

$program_grid = '';

foreach ($data as $post_id => $program) {
    if (empty($program) || ! isset($program[ 'title' ])) {
        continue;
    }

    $grid_content = '';

    if (in_array('teaser_image', $items) && ! empty($program[ '_thumbnail_rendered' ])) {
        $grid_content .= '<div class="teaser-image">' . $program[ '_thumbnail_rendered' ] . '</div>';
    }

    $grid_content .= '<div class="program-content">';

    $grid_content .= '<div class="program-title-wrapper">';
    if (in_array('title', $items) && ! empty($program[ 'title' ])) {
        $title        = $program[ 'title' ] . (! empty($program[ 'degree' ][ 'abbreviation' ]) ? ' (' . $program[ 'degree' ][ 'abbreviation' ] . ')' : '');
        $grid_content .= '<p class="program-title">' . $title . '</p>';
    }

    if (in_array('subtitle', $items) && ! empty($program[ 'subtitle' ])) {
        $grid_content .= '<p class="program-subtitle">' . $program[ 'subtitle' ] . '</p>';
    }
    $grid_content .= '</div>';

    if (in_array('degree', $items) && ! empty($program[ 'degree' ][ 'name' ])) {
        $grid_content .= '<p class="program-degree"><span class="label">' . $labels[ 'degree' ] . '</span> ' . $program[ 'degree' ][ 'name' ] . '</p>';
    }

    if (in_array('start', $items) && ! empty($program[ 'start' ])) {
        $start        = implode(', ', $program[ 'start' ]);
        $grid_content .= '<p class="program-start"><span class="label">' . $labels[ 'start' ] . '</span> ' . $start . '</p>';
    }

    if (in_array('admission_requirements', $items) && ! empty($program[ 'admission_requirement_link' ][ 'name' ])) {
        $grid_content .= '<p class="program-adm-req"><span class="label">' . $labels[ 'admission_requirements' ] . '</span> ' . $program[ 'admission_requirement_link' ][ 'name' ] . '</p>';
    }

    if (in_array('area_of_study', $items) && ! empty($program[ 'area_of_study' ])) {
        $areas        = array_column($program[ 'area_of_study' ], 'name');
        $areas        = implode(', ', $areas);
        $grid_content .= '<p class="program-area-study"><span class="label">' . $labels[ 'area_of_study' ] . '</span> ' . $areas . '</p>';
    }

    $grid_content .= '</div>';

    $url = get_permalink($post_id);

    if ( ! empty($url)) {
        $grid_content .= do_blocks(
            '<!-- wp:buttons {"layout":{"type":"flex","orientation":"horizontal","justifyContent":"left"}} -->
            <div class="wp-block-buttons"><!-- wp:button {"className":"is-style-primary"} -->
            <div class="wp-block-button is-style-primary"><a class="wp-block-button__link wp-element-button" href="' . $url . '">' . __('Read more', 'fau-studium-display') . '</a></div>
            <!-- /wp:button --></div>
            <!-- /wp:buttons -->'
        );
    }

    $program_grid .= sprintf(
        '<li class="is-style-dark">%s</li>',
        $grid_content
    );
}

?>

<section class="fau-studium-display degree-program-grid">

    <?php if (isset($atts['showSearch']) && $atts['showSearch'] == '1') :

        $prefilter = array_map(function ($v) use ($atts) {
            return $atts[ $v ];
        }, ['faculty' => 'selectedFaculties', 'degree' => 'selectedDegrees', 'attribute' => 'selectedSpecialWays']);
        $filter_items = $atts['selectedSearchFilters'] ?? [];
        echo Utils::renderSearchForm($prefilter, $filter_items, $lang, 'grid');

        $count = count($data);
        echo '<p class="items-found" id="degree_program_results">' . sprintf($count == 1 ? $labels['num_programs_found_singular'] : $labels['num_programs_found_plural'], $count) . '</p>';

    endif; ?>

    <?php if (!empty($program_grid)) : ?>
        <ul class="degree-program-grid">
            <?php echo $program_grid; ?>
        </ul>

    <?php else: ?>

        <p><?php _e('No degree programs found.', 'fau-studium-display'); ?></p>

    <?php endif; ?>

</section>