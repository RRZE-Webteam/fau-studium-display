<?php

defined('ABSPATH') || exit;

use function \FAU\StudiumDisplay\Config\get_labels;

//var_dump($data);
//var_dump($atts);

$lang = $atts[ 'language' ] ?? 'de';
$labels = get_labels($lang);
//var_dump($labels);

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

$admission_requirements = [];
if (!empty($data['admission_requirements']['bachelor_or_teaching_degree'])) {
    $admission_requirements['bachelor_or_teaching_degree'] = __('1st semester', 'fau-studium-display') . ': <a href="' . $data['admission_requirements']['bachelor_or_teaching_degree']['link_url'] . '">' . $data['admission_requirements']['bachelor_or_teaching_degree']['link_text'] . '</a>';
}
if (!empty($data['admission_requirements']['teaching_degree_higher_semester'])) {
    $admission_requirements['teaching_degree_higher_semester'] = __('Higher semesters', 'fau-studium-display') . ': <a href="' . $data['admission_requirements']['teaching_degree_higher_semester']['link_url'] . '">' . $data['admission_requirements']['teaching_degree_higher_semester']['link_text'] . '</a>';
}

if (!empty($data['admission_requirements']['master'])) {
    $admission_requirements['master'] = __('Master', 'fau-studium-display') . ': <a href="' . $data['admission_requirements']['master']['link_url'] . '">' . $data['admission_requirements']['master']['link_text'] . '</a>';
}

$deadlines = [];
$deadlines['winter_semester'] = __('Winter semester', 'fau-studium-display') . ': ' . (empty($data['application_deadline_winter_semester']) ? __('not possible', 'fau-studium-display') : strip_tags($data['application_deadline_winter_semester']));
$deadlines['summer_semester'] = __('Summer semester', 'fau-studium-display') . ': ' . (empty($data['application_deadline_summer_semester']) ? __('not possible', 'fau-studium-display') : strip_tags($data['application_deadline_summer_semester']));

$language_skills = !empty($data['language_skills']) ? $data['language_skills'] : '';

$content_related_master_requirements = !empty($data['content_related_master_requirements']) ? $data['content_related_master_requirements'] : '';

$admission_details = !empty($data['details_and_notes']) ? $data['details_and_notes'] : '';

$internationals_admission_requirements = [];
if (!empty($data['german_language_skills_for_international_students']['link_text'])
    && !empty($data['german_language_skills_for_international_students']['link_url'])) {
    $internationals_admission_requirements['german_language_skills'] = '<a href="' . $data['german_language_skills_for_international_students']['link_url'] . '">' . $data['german_language_skills_for_international_students']['link_text'] . '</a>';
} elseif (!empty($data['german_language_skills_for_international_students']['name'])) {
    $internationals_admission_requirements['german_language_skills'] = $data['german_language_skills_for_international_students']['name'];
}

$fields_organizational = [
    'start_of_semester',
    'semester_dates',
    'examinations_office',
    'semester_fee',
    'service_centers',
    'abroad_opportunities',
];
$links_organizational = [];
foreach ($fields_organizational as $item) {
    if (!empty($data[$item]['link_text'])
        && !empty($data[$item]['link_url'])) {
        $links_organizational[$item] = '<a href="' . $data[$item]['link_url'] . '">' . $data[$item]['link_text'] . '</a>';
    }
}

$fields_downloads = [
    'module_handbook',
    'examination_regulations'
];
$links_downloads = [];
foreach ($fields_downloads as $item) {
    if (!empty($data[$item])) {
        $links_downloads[$item] = '<a href="' . $data[$item] . '">' . ($labels[$item] ?? $item) . '</a>';
    }
}

$fields_additional = [
    'link',
    'examinations_office', //
    'department',
    'faculty', ///
    'student_initiatives',//
];

$links_additional = [];
foreach ($fields_additional as $item) {
    switch ($item) {
        case 'link':
        case 'department':
            if (!empty($data[$item])) {
                $links_additional[$item] = '<a href="' . $data[$item] . '">' . ($labels[$item] ?? $item) . '</a>';
            }
            break;
        case 'examinations_office':
        case 'student_initiatives':
            if (!empty($data[$item]['link_text'])
                && !empty($data[$item]['link_url'])) {
                $links_additional[$item] = '<a href="' . $data[$item]['link_url'] . '">' . $data[$item]['link_text'] . '</a>';
            }
            break;
        case 'faculty':
            $links_faculty = [];
            foreach ($data[$item] as $faculty) {
                if (!empty($faculty['link_text'])
                    && !empty($faculty['link_url'])) {
                    $links_faculty[] = '<a href="' . $faculty['link_url'] . '">' . $faculty['link_text'] . '</a>';
                }
            }
            $links_additional[$item] = implode(', ', $links_faculty);
            break;
    }

}

if (!empty($data['student_advice']['link_text'])
    && !empty($data['student_advice']['link_url'])) {
    $student_advice = '<a href="' . $data['student_advice']['link_url'] . '">' . $data['student_advice']['link_text'] . '</a>';
}

if (!empty($data['subject_specific_advice']['link_text'])
    && !empty($data['subject_specific_advice']['link_url'])) {
    $subject_specific_advice = '<a href="' . $data['subject_specific_advice']['link_url'] . '">' . $data['subject_specific_advice']['link_text'] . '</a>';
}

?>

<section class="fau-studium-display degree-program-full" itemtype="https://schema.org/EducationalOccupationalProgram" itemscope>
    <?php

    echo $data['featured_image']['rendered']; ?>

    <div class="program-content">

        <!-- Header -->

        <header class="program-header width-small">
            <h1 class="title" itemprop="name">
                <?php echo esc_attr($data['title'] . ' (' . $data['degree']['abbreviation'] . ')'); ?>
            </h1>
            <?php if (!empty($data['subtitle'])) : ?>
            <p class="program-subtitle"><?php echo esc_attr($data['subtitle']); ?></p>
            <?php endif; ?>
        </header>

        <!-- Entry text -->

        <?php if (!empty($data['entry_text'])) : ?>
            <div class="entry-text width-small">
                <?php echo $data['entry_text']; ?>
            </div>
        <?php endif; ?>

        <!-- Fact sheet -->

        <div class="fact-sheet width-small">
            <div class="icon-thumbtack"></div>
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

        <!-- Details / content -->

        <div class="program-details width-small">
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
        </div>

        <!-- Videos -->

        <?php if (!empty($data['videos'])):
            echo '<div class="program-videos width-large">[alert color="#1f4c7a"][columns]';
            foreach ($data['videos'] as $video):
                echo '[column][fauvideo url="' . $video . '"][/column]';
            endforeach;
            echo '[/columns][/alert]</div>';
        endif; ?>

        <!-- Admission -->

        <?php if (!empty($admission_requirements)
            || !empty($deadlines)
            || !empty($language_skills)
            || !empty($content_related_master_requirements)
            || !empty($admission_details)
            || !empty($internationals_admission_requirements)) { ?>

            <div class="program-admission width-small">

                <h2><?php _e('Your Path to University Admission', 'fau-studium-display'); ?></h2>

                <!-- Admission – General -->

                <div class="program-admission-general">
                    <h3><?php _e('Admission Requirements and Application', 'fau-studium-display'); ?></h3>

                    <?php if (!empty($admission_requirements)) { ?>
                        <h4><?php _e('Admission Requirements', 'fau-studium-display'); ?></h4>
                        <ul>
                        <?php foreach ($admission_requirements as $requirement) { ?>
                            <li><?php echo wp_kses_post($requirement); ?></li>
                        <?php } ?>
                        </ul>
                    <?php } ?>

                    <?php if (!empty($deadlines)) { ?>
                        <h4><?php _e('Application Deadline', 'fau-studium-display'); ?></h4>
                        <ul>
                        <?php foreach ($deadlines as $deadline) { ?>
                            <li><?php echo strip_tags($deadline); ?></li>
                        <?php } ?>
                        </ul>
                    <?php } ?>

                    <?php if (!empty($language_skills)) { ?>
                        <h4><?php _e('Language skills', 'fau-studium-display'); ?></h4>
                        <p><?php echo strip_tags($language_skills); ?></p>
                    <?php } ?>

                    <?php if (!empty($content_related_master_requirements)) { ?>
                        <h4><?php _e('Content-related admission requirements', 'fau-studium-display'); ?></h4>
                        <?php echo wp_kses_post($content_related_master_requirements); ?>
                    <?php } ?>

                    <?php if (!empty($admission_details)) { ?>
                        <h4><?php _e('Details and notes', 'fau-studium-display'); ?></h4>
                        <?php echo wp_kses_post($admission_details); ?>
                    <?php } ?>
                </div>

                <!-- Admission – International students -->

                <?php if (!empty($internationals_admission_requirements)) { ?>
                <div class="program-admission-internationals">
                    <div class="icon-globe"></div>
                    <h3><?php _e('More Information for International Applicants', 'fau-studium-display'); ?></h3>

                    <?php if (!empty($internationals_admission_requirements['german_language_skills'])) { ?>
                        <h4><?php _e('German language skills', 'fau-studium-display'); ?></h4>
                        <?php echo wp_kses_post($internationals_admission_requirements['german_language_skills']); ?>
                    <?php } ?>

                </div>
                <?php } ?>
            </div>

        <?php } ?>

        <!-- Student advice -->

        <?php if (isset($student_advice) || isset($subject_specific_advice)) { ?>

            <div class="student-advice width-small">
                <h2><?php _e('Student Advice', 'fau-studium-display'); ?></h2>
                <ul>
                <?php if (isset($student_advice)) {
                    echo '<li>' . $student_advice . '</li>';
                }
                if (isset($subject_specific_advice)) {
                    echo '<li>' . $subject_specific_advice . '</li>';
                }
                ?>
                </ul>
            </div>
        <?php } ?>

        <!-- Useful links -->

        <div class="useful-links width-medium">
            <h2><?php _e('Useful Links', 'fau-studium-display'); ?></h2>

            <?php if (!empty($links_organizational)) { ?>
                <div class="useful-links-organizational">
                    <h3><?php _e('Organizational', 'fau-studium-display'); ?></h3>
                    <ul>
                    <?php foreach ($links_organizational as $link) { ?>
                        <li><?php echo wp_kses_post($link); ?></li>
                    <?php } ?>
                </div>
            <?php } ?>

            <?php if (!empty($links_downloads)) { ?>
                <div class="useful-links-downloads">
                    <h3><?php _e('Downloads', 'fau-studium-display'); ?></h3>
                    <ul>
                        <?php foreach ($links_downloads as $link) { ?>
                            <li><?php echo wp_kses_post($link); ?></li>
                        <?php } ?>
                </div>
            <?php } ?>

            <?php if (!empty($links_additional)) { ?>
                <div class="useful-links-additional">
                    <h3><?php _e('Additional Information', 'fau-studium-display'); ?></h3>
                    <ul>
                        <?php foreach ($links_additional as $link) { ?>
                            <li><?php echo wp_kses_post($link); ?></li>
                        <?php } ?>
                </div>
            <?php } ?>

        </div>

        <!-- -->

    </div>

</section>
