<?php

defined('ABSPATH') || exit;

//var_dump($data);
//var_dump($atts);

$number_of_students_raw = $data['number_of_students']['name'] ?? '';
if (!empty($number_of_students_raw)) {
    $number_of_students_array = explode('-', $number_of_students_raw);
    $number_of_students = $number_of_students_array[1];
}

$facts = [
    'degree' => [
        'label' => __('Degree', 'fau-studium-display'),
        'value' => $data['degree']['name'] ?? '',
        'itemprop' => 'educationalCredentialAwarded'
    ],
    'admission_requirements' => [
        'label' => __('Admission Requirements', 'fau-studium-display'),
        'value' => $data['admission_requirement_link']['name'] ?? '',
        'itemprop' => 'programPrerequisites'
    ],
    'standard_duration' => [
        'label' => __('Standard Duration', 'fau-studium-display'),
        'value' => (!empty($data['standard_duration']) ? sprintf(__('%s semesters', 'fau-studium-display'), $data['standard_duration']): ''),
        'itemprop' => 'timeToComplete'
    ],
    'teaching_language' => [
        'label' => __('Teaching Language', 'fau-studium-display'),
        'value' => $data['teaching_language'] ?? ''
    ],
    'faculty' => [
        'label' => __('Faculty', 'fau-studium-display'),
        'value' => $facts['faculty']['value'] = !empty($data['faculty']) ? implode(', ', array_column($data['faculty'], 'name')) : ''
    ],
    'start' => [
        'label' => __('Start of Degree Program', 'fau-studium-display'),
        'value' => !empty($data['start']) ? implode(', ', $data['start']) : '',
        //'itemprop' => 'startDate'
    ],
    'number_of_students' => [
        'label' => __('Number of Students', 'fau-studium-display'),
        'value' => $data['number_of_students']['name'] ?? '',
        'itemprop' => 'maximumEnrollment',
        'itemprop_content' => $number_of_students ?? ''
    ],
    'location' => [
        'label' => __('Location', 'fau-studium-display'),
        'value' => $data['location']['name'] ?? '',
    ],
    'attribute' => [
        'label' => __('Special ways to study', 'fau-studium-display'),
        'value' => isset($data['attributes']) ? implode(', ', $data['attributes']) : '',
    ]
];

$special_features = [
    'label' => __('Special Features', 'fau-studium-display'),
    'value' => $data['content']['special_features']['description'] ?? ''
];

$fact_list = '';
foreach ($facts as $fact) {
    if (!empty($fact['value'])) {
        $fact_list .= '<div class="dpair"><dt>' . $fact['label'] . '</dt>'
            . '<dd' . (isset($fact['itemprop']) ? ' itemprop="' . $fact['itemprop'] . '"' : '') . (isset($fact['itemprop_content']) ? ' content="'.$fact['itemprop_content'].'"' : '') . '>' . $fact['value'] . '</dd></div>';
    }
}

$title = $atts['showTitle'] ? $data['title'] . ' (' . $data['degree']['abbreviation'] . ')': __('Fact Sheet', 'fau-studium-display');

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
