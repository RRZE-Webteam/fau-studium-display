<?php

defined('ABSPATH') || exit;

//var_dump($data);

$facts = [
    'degree' => [
        'label' => __('Degree', 'fau-studium-display'),
        'value' => $data['degree']['name'] ?? ''
    ],
    'start' => [
        'label' => __('Start of Degree Program', 'fau-studium-display'),
        'value' => !empty($data['start']) ? implode(', ', $data['start']) : ''
    ],
    'admission_requirements' => [
        'label' => __('Admission Requirements', 'fau-studium-display'),
        'value' => $data['admission_requirement_link']['name'] ?? ''
    ],
    'standard_duration' => [
        'label' => __('Standard Duration', 'fau-studium-display'),
        'value' => (!empty($data['standard_duration']) ? sprintf(__('%s semesters', 'fau-studium-display'), $data['standard_duration']): '')
    ],
    'number_of_students' => [
        'label' => __('Number of Students', 'fau-studium-display'),
        'value' => $data['number_of_students']['name'] ?? ''
    ],
    'teaching_language' => [
        'label' => __('Teaching Language', 'fau-studium-display'),
        'value' => $data['teaching_language'] ?? ''
    ],
    'faculty' => [
        'label' => __('Faculty', 'fau-studium-display'),
        'value' => $facts['faculty']['value'] = !empty($data['faculty']) ? implode(', ', array_column($data['faculty'], 'name')) : ''
    ]
];

$special_features = [
    'label' => __('Special Features', 'fau-studium-display'),
    'value' => $data['content']['special_features']['description'] ?? ''
];

$fact_list = '';
foreach ($facts as $fact) {
    //var_dump($fact);
    if (!empty($fact['value'])) {
        $fact_list .= '<div class="dpair"><dt>' . $fact['label'] . '</dt>'
            . '<dd>' . $fact['value'] . '</dd></div>';
    }
}

?>

<section class="fau-studium-display degree-program-box">
    <h1><?php _e('Fact Sheet', 'fau-studium-display');?></h1>

    <?php if (!empty($fact_list)): ?>
    <dl class="facts">
        <?php echo wp_kses_post($fact_list); ?>
    </dl>
    <?php endif; ?>

    <?php if (!empty($special_features['value'])): ?>
    <dl class="special-features">
        <dt><?php echo esc_attr($special_features['label']); ?></dt>
        <dd><?php echo wp_kses_post($special_features['value']); ?></dd>
    </dl>
    <?php endif; ?>
</section>
