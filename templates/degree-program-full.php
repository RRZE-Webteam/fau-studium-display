<?php
//var_dump($data);

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
?>

<section class="fau-studium-display degree-program-full" itemtype="https://schema.org/EducationalOccupationalProgram" itemscope>
    <?php
    //print "<pre>";var_dump($data); print "</pre>";exit;

    echo $data['featured_image']['rendered']; ?>

    <div class="program-content">

        <header class="program-header">
            <h1 class="title" itemprop="name">
                <?php echo esc_attr($data['title'] . ' (' . $data['degree']['abbreviation'] . ')'); ?>
            </h1>
            <?php if (!empty($data['subtitle'])) : ?>
            <p class="program-subtitle"><?php echo esc_attr($data['subtitle']); ?></p>
            <?php endif; ?>
        </header>

        <?php if (!empty($data['entry_text'])) : ?>
            <div class="entry-text">
                <?php echo $data['entry_text']; ?>
            </div>
        <?php endif; ?>

        <div class="fact-sheet">
            <div class="thumbtack"></div>
            <h1><?php _e('Fact Sheet', 'fau-studium-display')?></h1>
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
        </div>


        [collapsibles style="light"]
        <?php
        $content_fields = ['about', 'structure', 'specializations', 'qualities_and_skills', 'why_should_study', 'career_prospects', 'special_features', 'testimonials'];
        foreach ($content_fields as $field) {
            if (!empty($data['content'][$field]['description'])) {
                echo '[collapse title="' . esc_attr($data['content'][$field]['title']) . '"]';
                echo wp_kses_post($data['content'][$field]['description']);
                echo '[/collapse]';
            }
        }
        ?>
        [/collapsibles]


        <?php if (!empty($data['videos'])):
            echo '<div class="program-videos">[alert color="#1f4c7a"][columns]';
            foreach ($data['videos'] as $video):
                echo '[column][fauvideo url="' . $video . '"][/column]';
            endforeach;
            echo '[/columns][/alert]</div>';
        endif; ?>


        <div class="program-admission">
            <h2><?php _e('Admission Requirements and Application', 'fau-studium-display'); ?></h2>
            <h3><?php _e('Admission Requirements', 'fau-studium-display'); ?></h3>
            <ul>
                <?php if (!empty($data['admission_requirements']['bachelor_or_teaching_degree'])): ?>
                    <li><?php echo _e('1st semester', 'fau-studium-display') . ': ' . wp_kses_post('<a href="' . $data['admission_requirements']['bachelor_or_teaching_degree']['link_url'] . '">' . $data['admission_requirements']['bachelor_or_teaching_degree']['link_text'] . '</a>'); ?></li>
                <?php endif; ?>
                <?php if (!empty($data['admission_requirements']['teaching_degree_higher_semester'])): ?>
                    <li><?php echo _e('Higher semesters', 'fau-studium-display') . ': ' . wp_kses_post('<a href="' . $data['admission_requirements']['teaching_degree_higher_semester']['link_url'] . '">' . $data['admission_requirements']['teaching_degree_higher_semester']['link_text'] . '</a>'); ?></li>
                <?php endif; ?>
            </ul>

            <h3><?php _e('Application Deadline', 'fau-studium-display'); ?></h3>
            <ul>
                <?php
                $deadline_winter = empty($data['application_deadline_winter_semester']) ? __('not possible', 'fau-studium-display') : $data['application_deadline_winter_semester'];
                $deadline_summer = empty($data['application_deadline_summer_semester']) ? __('not possible', 'fau-studium-display') : $data['application_deadline_summer_semester'];
                ?>
                <li><?php echo __('Winter semester', 'fau-studium-display') . ': ' . strip_tags($deadline_winter); ?></li>
                <li><?php echo __('Summer semester', 'fau-studium-display') . ': ' . strip_tags($deadline_summer); ?></li>
            </ul>

            <h3><?php _e('Language skills', 'fau-studium-display'); ?></h3>
            <ul><li><?php echo strip_tags($data['language_skills']); ?></li></ul>
        </div>

    </div>

</section>
