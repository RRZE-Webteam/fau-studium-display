<?php

defined('ABSPATH') || exit;

use Fau\DegreeProgram\Display\Utils;

use function Fau\DegreeProgram\Display\Config\get_output_fields;
use function Fau\DegreeProgram\Display\Config\get_labels;

//print "<pre>"; var_dump($data); print "</pre>";
//print "<pre>"; var_dump($atts); print "</pre>";
//print "<pre>"; print_r($atts); print "</pre>";
//exit;

$show_search = isset($atts['showSearch']) && $atts['showSearch'] == '1';

if (empty($data) && !$show_search)
    return;

$lang = $atts['language'] ?? 'de';
$linkTarget = $atts['linkTarget'] ?? 'local';
$labels = get_labels($lang);
//var_dump($labels); exit;

$program_table = '';

$table_fields = [
    'title',
    'degree',
    'start',
    'location',
    'admission_requirements',
];
$table_header = '';
foreach ($table_fields as $field) {
    $table_header .= '<th>' . ($labels[$field] ?? $field) . '</th>';
}
$program_table .= sprintf('<tr>%s</tr>', $table_header);

foreach ($data as $post_id => $program) {
    if (empty($program) || !isset($program['title']))
        continue;

    $url = match ($linkTarget) {
        'local' => get_permalink($post_id),
        'remote' => ! empty($program[ 'link' ]) ? esc_url($program[ 'link' ]) : '',
        default => '',
    };
    $title = $program['title'] . (!empty($program[ 'degree' ][ 'abbreviation' ]) ? ' (' . $program[ 'degree' ][ 'abbreviation' ] . ')' : '');

    if (!empty($url)) {
        $title = sprintf('<a class="program-title" href="%s">%s</a>', $url, $title);
    }

    $table_content = '<td class="image-title">';
    if (!empty($program['_thumbnail_rendered'] . $title)) {
        $table_content .= $program[ '_thumbnail_rendered' ] . $title;
    }
    $table_content .= '</td>';

    $table_content .= '<td class="program-degree">';
    if (!empty($program['degree']['name'])) {
        $table_content .= '<span class="label">' . $labels['degree'] . ': </span>' . $program['degree']['name'];
    }
    $table_content .= '</td>';

    $table_content .= '<td class="program-start">';
    if (!empty($program['start'])) {
        $start = implode(', ', $program['start']);
        $table_content .= '<span class="label">' . $labels['start'] . ': </span>' . $start;
    }
    $table_content .= '</td>';

    $table_content .= '<td class="program-location">';
    if (!empty($program['location'])) {
        $table_content .= '<span class="label">' . $labels['location'] . ': </span>' . implode(', ',$program['location']);
    }
    $table_content .= '</td>';

    $table_content .= '<td class="program-adm-req">';
    if (!empty($program['admission_requirement_link']['name'])) {
        $table_content .= '<span class="label">' . $labels['admission_requirements'] . ': </span>' . $program['admission_requirement_link']['name'];
    }
    $table_content .= '</td>';

    //print "<pre>"; var_dump($program); print "</pre>";
    $program_table .= sprintf('<tr>%s</tr>',
                             //$program['link'],
                             $table_content
    );

}

?>

<section class="fau-studium-display degree-program-table">

    <?php if (isset($atts['showSearch']) && $atts['showSearch'] == '1') :

        $prefilter = array_map(function ($v) use ($atts) {
            return $atts[ $v ];
        }, ['faculty' => 'selectedFaculties', 'degree' => 'selectedDegrees', 'attribute' => 'selectedSpecialWays']);
        $filter_items = $atts['selectedSearchFilters'] ?? [];
        echo Utils::renderSearchForm($prefilter, $filter_items, $lang);

    endif; ?>

    <?php if (!empty($data)) :
        $count = count($data);
        printf(_n('%s%d degree program found%s', '%s%d degree programs found%s', $count, 'fau-studium-display'), '<p>', $count, '</p>'); ?>
        <table class="degree-program-table">
            <?php echo wp_kses_post($program_table); ?>
        </table>

    <?php else: ?>

        <p><?php _e('No degree programs found.', 'fau-studium-display'); ?></p>

    <?php endif; ?>

</section>