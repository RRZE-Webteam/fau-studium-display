<?php

defined('ABSPATH') || exit;

use function \Fau\DegreeProgram\Display\Config\get_labels;

//var_dump($data);
//var_dump($atts);

if (empty($atts['degreeProgram'])) {
    return;
}

$lang = $atts[ 'language' ] ?? 'de';
$labels = get_labels($lang);
//print "<pre>"; var_dump($labels); print "</pre>";

$number_of_students_raw = $data['number_of_students']['name'] ?? '';
if (!empty($number_of_students_raw)) {
    $number_of_students_array = explode('-', $number_of_students_raw);
    $number_of_students = $number_of_students_array[1];
}

$facts = [
    'degree' => [
        'label' => $labels['degree'],
        'value' => $data['degree']['name'] ?? '',
        'itemprop' => 'educationalCredentialAwarded'
    ],
    'admission_requirements' => [
        'label' => $labels['admission_requirements'],
        'value' => $data['admission_requirement_link']['name'] ?? '',
        'itemprop' => 'programPrerequisites'
    ],
    'standard_duration' => [
        'label' => $labels['standard_duration'],
        'value' => (!empty($data['standard_duration']) ? sprintf(__('%s semesters', 'fau-studium-display'), $data['standard_duration']): ''),
        'itemprop' => 'timeToComplete'
    ],
    'teaching_language' => [
        'label' => $labels['teaching_language'],
        'value' => $data['teaching_language'] ?? ''
    ],
    'faculty' => [
        'label' => $labels['faculty'],
        'value' => $facts['faculty']['value'] = !empty($data['faculty']) ? implode(', ', array_column($data['faculty'], 'name')) : ''
    ],
    'start' => [
        'label' => $labels['start'],
        'value' => !empty($data['start']) ? implode(', ', $data['start']) : '',
        //'itemprop' => 'startDate'
    ],
    'number_of_students' => [
        'label' => $labels['number_of_students'],
        'value' => $data['number_of_students']['name'] ?? '',
        'itemprop' => 'maximumEnrollment',
        'itemprop_content' => $number_of_students ?? ''
    ],
    'location' => [
        'label' => $labels['location'],
        'value' => $data['location']['name'] ?? '',
    ],
    'attributes' => [
        'label' => $labels['attributes'],
        'value' => isset($data['attributes']) ? implode(', ', $data['attributes']) : '',
    ]
];

$special_features = [
    'label' => $labels['special_features'],
    'value' => $data['content']['special_features']['description'] ?? ''
];

$fact_list = '';
foreach ($facts as $fact) {
    if (!empty($fact['value'])) {
        $fact_list .= '<div class="dpair"><dt>' . $fact['label'] . '</dt>'
            . '<dd' . (isset($fact['itemprop']) ? ' itemprop="' . $fact['itemprop'] . '"' : '') . (isset($fact['itemprop_content']) ? ' content="'.$fact['itemprop_content'].'"' : '') . '>' . $fact['value'] . '</dd></div>';
    }
}

$title = $atts['showTitle'] ? $data['title'] . ' (' . $data['degree']['abbreviation'] . ')': $labels['fact_sheet'];

?>

<section class="fau-studium-display degree-program-box" itemtype="https://schema.org/EducationalOccupationalProgram" itemscope>
    <div class="thumbtack"></div>
    <h1><?php echo $title;?></h1>
    <meta itemprop="name" content="<?php echo $data['title'];?>">
    <?php if (!empty($fact_list)): ?>
    <dl class="facts">
        <?php echo ($fact_list); ?>
    </dl>
    <?php endif; ?>

    <?php if (!empty($special_features['value'])): ?>
    <dl class="special-features">
        <dt><?php echo esc_attr($special_features['label']); ?></dt>
        <dd><?php echo wp_kses_post($special_features['value']); ?></dd>
    </dl>
    <?php endif; ?>
</section>
