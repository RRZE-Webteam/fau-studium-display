<?php

defined('ABSPATH') || exit;

use FAU\StudiumDisplay\Utils;

use function FAU\StudiumDisplay\Config\get_output_fields;
use function FAU\StudiumDisplay\Config\get_labels;

//print "<pre>"; var_dump($data);print "</pre>";
//print "<pre>"; var_dump($atts);print "</pre>";
//print_r($atts);
//exit;

$lang = $atts['language'] ?? 'de';
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

foreach ($data as $program) {
    if (empty($program))
        continue;

    $table_content = '';

    $table_content .= '<td class="image-title">' . $program['teaser_image']['rendered'] . '<span class="program-title">' . $program['title'] . ' (' . $program[ 'degree' ][ 'abbreviation' ] . ')</span></td>';

    $table_content .= '<td class="program-degree"><span class="label">' . $labels['degree'] . ': </span>' . $program['degree']['name']. '</td>';

    $start = implode(', ', $program['start']);
    $table_content .= '<td class="program-start"><span class="label">' . $labels['start'] . ': </span>' . $start. '</td>';

    $table_content .= '<td class="program-location"><span class="label">' . $labels['location'] . ': </span>' . implode(', ',$program['location']) . '</td>';

    $table_content .= '<td class="program-adm-req"><span class="label">' . $labels['admission_requirements'] . ': </span>' . $program['admission_requirement_link']['name']. '</td>';

    //print "<pre>"; var_dump($program); print "</pre>";
    $program_table .= sprintf('<tr>%s</tr>',
                             //$program['link'],
                             $table_content
    );

}

?>

<section class="fau-studium-display degree-program-table">

    <?php if (isset($atts['showSearch']) && $atts['showSearch'] == '1') :
        echo Utils::renderSearchForm();
    endif; ?>

    <?php if (!empty($program_table)) : ?>

        <table class="degree-program-table">
            <?php echo wp_kses_post($program_table); ?>
        </table>

    <?php else: ?>

        <p><?php _e('No degree programs found.', 'fau-studium-display'); ?></p>

    <?php endif; ?>

</section>