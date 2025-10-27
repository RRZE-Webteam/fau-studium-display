<?php

defined('ABSPATH') || exit;

use function Fau\DegreeProgram\Display\Config\get_constants;
use function \Fau\DegreeProgram\Display\Config\get_labels;
use function \Fau\DegreeProgram\Display\Config\get_meinstudium_options;
use function Fau\DegreeProgram\Display\plugin;

//print "<pre>"; print_r($atts); print "</pre>";
//print "<pre>"; var_dump($data); print "</pre>";

if (empty($atts['degreeProgram'])) {
    print '<div class="components-placeholder is-large">' . __('Please select a degree program.', 'fau-studium-display') . '</div>';
    return;
}
if (empty($data)) {
    print '<div class="components-placeholder is-large">' . __('No data available.', 'fau-studium-display') . '</div>';
    return;
}

$items = $atts['selectedItemsFull'] ?? [];
$lang = $atts[ 'language' ] ?? 'de';
$labels = get_labels($lang);
$descriptions = get_labels($lang, 'description');
$constants = get_constants($lang);
$meinstudium_options = get_meinstudium_options($lang);
//print "<pre>"; var_dump($labels); print "</pre>";

$number_of_students_raw = $data['number_of_students']['name'] ?? '';
if (!empty($number_of_students_raw)) {
    if (str_contains($number_of_students_raw, '-')) {
        $number_of_students_array = explode('-', $number_of_students_raw);
        $number_of_students = $number_of_students_array[1];
    } else {
        $number_of_students = filter_var($number_of_students_raw, FILTER_SANITIZE_NUMBER_INT);
    }
}
$standard_duration = $data['standard_duration'] ?? '';
if (!empty($standard_duration)) {
    $standard_duration_years = floor((int)$standard_duration/2);
    $standard_duration_months = $standard_duration % 2;
    $standard_duration_iso = 'P'.$standard_duration_years.'Y'.($standard_duration_months > 0 ? $standard_duration_months . 'M' : '');
}

/*
 * Build items
 */

// Thumbnail
if (in_array('teaser_image', $items)) {
    $thumbnail = !empty($data[ '_thumbnail_rendered' ]) ? $data[ '_thumbnail_rendered' ] : $data[ 'featured_image' ][ 'rendered' ];
} else {
    $thumbnail = '';
}

// Title
if (in_array('title', $items) && ! empty($data[ 'title' ])) {
    $title = '<span itemprop="name">' . $data['title'] . '</span>' . (!empty($data[ 'degree' ][ 'abbreviation' ]) ? ' (' . $data[ 'degree' ][ 'abbreviation' ] . ')' : '');
    $title = '<h1 class="title">' . $title . '</h1>';
} else {
    $title = '<meta name="title" itemprop="name" content="' . esc_attr($data['title']) . '">';
}

// Subtitle
if (in_array('subtitle', $items) && ! empty($data[ 'subtitle' ])) {
    $subtitle = '<p class="program-subtitle">' . $data['subtitle'] . '</p>';
} else {
    $subtitle = '';
}

// Meta data
$meta_data = $constants['schema_termsPerYear']
           . $constants['schema_termDuration']
           . $constants['schema_provider']
           . $constants['schema_offer'];
if (!empty($data['url'])) {
    $meta_data .= '<meta itemprop="url" content="' . esc_url($data['url']) . '">';
}
if (!empty($data['content']['structure']['description'])) {
    $meta_data .= '<div itemprop="hasCourseInstance" itemscope itemtype="https://schema.org/CourseInstance">'
                  . '<meta itemprop="name" content="' . $labels['structure'] . '">'
                  . '<meta itemprop="courseMode" content="Onsite">'
                  . '<meta itemprop="courseWorkload" content="' . ($standard_duration_iso ?? 'P2Y') . '">'
                  . '<meta itemprop="description" content="' . strip_tags($data['content']['structure']['description']) . '">'
                  /*. '<div itemprop="courseSchedule" content="' . strip_tags($data['content']['structure']['description']) . '" itemscope itemtype="https://schema.org/courseSchedule">'
                      . '<meta itemprop="repeatCount" content="' . ($standard_duration_years ?? 1) . '">'
                      . '<meta itemprop="repeatFrequency" content="Yearly">'
                  . '</div>'*/
                  . '</div>';
}

// Entry Text
if (in_array('entry_text', $items) && ! empty($data[ 'entry_text' ])) {
    $entry_text = '<div class="entry-text width-small">' . str_replace('<p>', '<p class="is-style-intro-text">', $data['entry_text'] ). '</div>';
} else {
    $entry_text = '';
}

// Fact Sheet
if (in_array('fact_sheet', $items)) {
    $facts = [
        'degree' => [
            'label' => $labels['degree'] ?? 'degree',
            'value' => $data['degree']['name'] ?? '',
            'itemprop' => 'educationalCredentialAwarded'
        ],
        'admission_requirements' => [
            'label' => $labels['admission_requirements'] ?? 'admission_requirements',
            'value' => $data['admission_requirement_link']['name'] ?? '',
            'itemprop' => 'programPrerequisites'
        ],
        'standard_duration' => [
            'label' => $labels['standard_duration'] ?? 'standard_duration',
            'value' => (!empty($data['standard_duration']) ? sprintf($labels['%s_semesters'], $data['standard_duration']): ''),
            'itemprop' => 'timeToComplete',
            'itemprop_content' => (!empty($data['standard_duration']) ? $standard_duration_iso : ''),
        ],
        'teaching_language' => [
            'label' => $labels['teaching_language'] ?? 'teaching_language',
            'value' => $data['teaching_language'] ?? ''
        ],
        'faculty' => [
            'label' => $labels['faculty'] ?? 'faculty',
            'value' => $facts['faculty']['value'] = !empty($data['faculty']) ? implode(', ', array_column($data['faculty'], 'name')) : ''
        ],
        'start' => [
            'label' => $labels['start'] ?? 'start',
            'value' => !empty($data['start']) ? implode(', ', $data['start']) : '',
            //'itemprop' => 'startDate'
        ],
        'number_of_students' => [
            'label' => $labels['number_of_students'] ?? 'number_of_students',
            'value' => $data['number_of_students']['name'] ?? '',
            'itemprop' => 'maximumEnrollment',
            'itemprop_content' => $number_of_students ?? ''
        ],
        'location' => [
            'label' => $labels['location'] ?? 'location',
            'value' => $data['location']['name'] ?? '',
        ],
        'attributes' => [
            'label' => $labels['attributes'] ?? 'attributes',
            'value' => isset($data['attributes']) ? implode(', ', $data['attributes']) : '',
        ]
    ];

    $fact_list = '';
    foreach ($facts as $fact) {
        if (!empty($fact['value'])) {
            $fact_list .= '<div class="dpair"><dt>' . $fact['label'] . '</dt>'
                          . '<dd' . (isset($fact['itemprop']) ? ' itemprop="' . $fact['itemprop'] . '"' : '') . (isset($fact['itemprop_content']) ? ' content="'.$fact['itemprop_content'].'"' : '') . '>' . $fact['value'] . '</dd></div>';
        }
    }
    $special_features = [
        'label' => $labels['special_features'] ?? 'special_features',
        'value' => $data['content']['special_features']['description'] ?? ''
    ];

    $fact_sheet = '<div class="fact-sheet width-small">
            <div class="icon-thumbtack"></div>
            <h1>' . ($labels['fact_sheet'] ?? 'fact_sheet') . '</h1>';
    if (!empty($fact_list)) {
        $fact_sheet .= '<dl class="facts">' . $fact_list . '</dl>';
    }
    if (!empty($special_features['value'])) {
        $fact_sheet .= '<dl class="special-features">'
            . '<dt>' . $special_features['label'] . '</dt>'
            . '<dd>' . $special_features['value'] . '</dd>'
            . '</dl>';
    }
    $fact_sheet .= '</div>';
} else {
    $fact_sheet = '';
}

// Content Collapsibles
$content_fields_all = ['content.structure', 'content.specializations', 'content.qualities_and_skills', 'content.why_should_study', 'content.career_prospects', 'special_features', 'combinations'];
$content_fields = array_intersect($content_fields_all, $items);

$content_title = ($labels['program_overview'] ?? 'program_overview');
$content_id = sanitize_title($content_title);
$content = '<div class="width-large"><h2 id="' . $content_id . '">' . $content_title . '</h2>'
           . '<div class="program-details width-small">';
if (in_array('content.about', $items)) {
    $content .= '<h3>' . ($labels['about'] ?? 'about') . '</h3><div itemprop="description">' . $data['content']['about']['description'] . '</div>';
}

$content_html = '<!-- wp:rrze-elements/collapsibles {"hstart":3,"expandLabel":"Alle ausklappen"} -->';
foreach ($content_fields as $field) {
    $field_name = str_replace('content.', '', $field);
    if (!empty($data['content'][$field_name]['description'])) {
        $content_html .= '<!-- wp:rrze-elements/collapse {"hstart":3,"title":"' . ($labels[$field_name] ?? $field_name) . '","jumpName":"' . sanitize_title($labels[$field_name] ?? $field_name) . '","isCustomJumpname":true} --><!-- wp:paragraph -->
        ' . $data['content'][$field_name]['description']
        . '<!-- /wp:paragraph --><!-- /wp:rrze-elements/collapse -->';
    }
    if ($field == 'combinations' && (!empty($data['combinations']) || !empty($data['limited_combinations']))) {
        $content_html .= '<!-- wp:rrze-elements/collapse {"hstart":3,"title":"' . ($labels[$field_name] ?? $field_name) . '","jumpName":"' . sanitize_title($labels[$field_name] ?? $field_name) . '","isCustomJumpname":true} --><!-- wp:paragraph -->';
        if (!empty($data['combinations'])) {
            $content_html .= '<h4>' . ($labels['content.combinations'] ?? 'combinations') . '</h4><ul class="program-combinations wp-block-list">';
            foreach ($data['combinations'] as $combination) {
                $content_html .= sprintf('<li><a href="%s">%s</a></li>', $combination['url'], $combination['title']);
            }
            $content_html .= '</ul>';
            $content_html .= !empty($descriptions['content.combinations']) ? '<p>' . $descriptions['content.combinations'] . '</p>' : '';
        }
        if (!empty($data['limited_combinations'])) {
            $content_html .= '<h4>' . ($labels['content.limited_combinations'] ?? 'limited_combinations') . '</h4><ul class="program-limited-combinations wp-block-list">';
            foreach ($data['limited_combinations'] as $limited_combination) {
                $content_html .= sprintf('<li><a href="%s">%s</a></li>', $limited_combination['url'], $limited_combination['title']);
            }
            $content_html .= '</ul>';
            $content_html .= !empty($descriptions['content.limited_combinations']) ? '<p>' . $descriptions['content.limited_combinations'] . '</p>' : '';
        }
        $content_html .= '<!-- /wp:paragraph --><!-- /wp:rrze-elements/collapse -->';

    }
}
$content_html .= '<!-- /wp:rrze-elements/collapsibles -->';
$content .= do_blocks($content_html);
$content .= '</div></div>';

$quicklinks[0] = '<!-- wp:button -->
            <div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#' . $content_id . '">' . $content_title . '</a></div>
            <!-- /wp:button -->';

// Videos
if (in_array('videos', $items) && !empty($data['videos'])) {
    $videos_html = '<div class="program-videos"><!-- wp:columns --><div class="wp-block-columns alignwide">';
    foreach ($data['videos'] as $video) {
        $videos_html .= '<!-- wp:column --><div class="wp-block-column">'
        . '<!-- wp:rrze/rrze-video {"url":"' . $video . '"} /-->'
        . '</div><!-- /wp:column -->';
    }
    $videos_html .= '</div><!-- /wp:columns --></div>';
    $videos = do_blocks($videos_html);
} else {
    $videos = '';
}

// Admission Requirements and Application
if (in_array('admission_requirements_application', $items)) {

    $admission_requirements = [];
    if (!empty($data['admission_requirements']['bachelor_or_teaching_degree'])) {
        $admission_requirements['bachelor_or_teaching_degree'] = $labels['1st_semester'] . ': ' . $data['admission_requirements']['bachelor_or_teaching_degree']['link_text'];
    }
    if (!empty($data['admission_requirements']['teaching_degree_higher_semester'])) {
        $admission_requirements['teaching_degree_higher_semester'] = $labels['higher_semesters'] . ': ' . $data['admission_requirements']['teaching_degree_higher_semester']['link_text'];
    }
    if (!empty($data['admission_requirements']['master'])) {
        $admission_requirements['master'] = 'Master: ' . $data['admission_requirements']['master']['link_text'];
    }

    if (empty($data['application_deadline_winter_semester']) && empty($data['application_deadline_summer_semester'])) {
        $deadlines = [];
    } else {
        $deadlines['winter_semester'] = $labels['winter_semester'] . ': ' . (empty($data['application_deadline_winter_semester']) ? $labels['not_possible'] : strip_tags($data['application_deadline_winter_semester']));
        $deadlines['summer_semester'] = $labels['summer_semester'] . ': ' . (empty($data['application_deadline_summer_semester']) ? $labels['not_possible'] : strip_tags($data['application_deadline_summer_semester']));
    }

    $language_skills = [];
    if (!empty($data['language_skills'])) {
        $language_skills[] = strip_tags($data['language_skills']);
    }
    if (!empty($data['german_language_skills_for_international_students']['link_text'])
        && !empty($data['german_language_skills_for_international_students']['link_url'])) {
        $language_skills[] = ($labels['german_language_skills_for_international_students'] ?? 'german_language_skills_for_international_students') . ': <a href="' . $data['german_language_skills_for_international_students']['link_url'] . '">' . $data['german_language_skills_for_international_students']['link_text'] . '</a>';
    } elseif (!empty($data['german_language_skills_for_international_students']['name'])) {
        $language_skills[] = ($labels['german_language_skills_for_international_students'] ?? 'german_language_skills_for_international_students') . ': ' . $data['german_language_skills_for_international_students']['name'];
    }

    $content_related_master_requirements = (/*in_array('content_related_master_requirements', $items) && */!empty($data['content_related_master_requirements'])) ? '<div itemprop="programPrerequisites">' . str_replace('<ul>', '<ul class="wp-block-list">', $data['content_related_master_requirements']) . '</div>' : '';

    $admission_details = !empty($data['details_and_notes']) ? $data['details_and_notes'] : '';

    $admission_requirements_application = '';
    if (!empty($admission_requirements)
        || !empty($deadlines)
        || !empty($language_skills)
        || !empty($content_related_master_requirements)
        || !empty($admission_details)) {

        $admission_requirements_application_title = ($labels['application_for_program'] ?? 'application_for_program');
        $admission_requirements_application_id = sanitize_title($admission_requirements_application_title);
        $admission_requirements_application .= '<div class="program-admission width-small"><h3>' . ($labels['admission_requirements_application'] ?? 'admission_requirements_application') . '</h3>';
        $quicklinks[2] = '<!-- wp:button -->
            <div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#' . $admission_requirements_application_id . '">' . $admission_requirements_application_title . '</a></div>
            <!-- /wp:button -->';

        if (!empty($admission_requirements)) {
            $admission_requirements_application .= '<h4>' . ($labels['admission_requirements'] ?? 'admission_requirements') . '</h4><ul class="wp-block-list">';
            foreach ($admission_requirements as $requirement) {
                $admission_requirements_application .= '<li>' . $requirement . '</li>';
            }
            $admission_requirements_application .= '</ul>';
        }

        if (!empty($deadlines)) {
            $admission_requirements_application .= '<h4>' . ($labels['application_deadline'] ?? 'application_deadline') . '</h4><ul class="wp-block-list">';
            foreach ($deadlines as $deadline) {
                $admission_requirements_application .= '<li itemprop="applicationDeadline">' . strip_tags($deadline) . '</li>';
            }
            $admission_requirements_application .= '</ul>';
        }

        if (!empty($language_skills)) {
            $admission_requirements_application .= '<h4>' . ($labels['language_skills'] ?? 'language_skills') . '</h4><ul class="wp-block-list">';
            foreach ($language_skills as $skill) {
                $admission_requirements_application .= '<li>' . $skill . '</li>';
            }
            $admission_requirements_application .= '</ul>';
        }

        if (!empty($content_related_master_requirements)) {
            $admission_requirements_application .= '<h4>' . ($labels['content_related_master_requirements'] ?? 'content_related_master_requirements') . '</h4>'
                . $content_related_master_requirements;
        }

        if (!empty($admission_details)) {
            $admission_requirements_application .= '<h4>' . ($labels['details_and_notes'] ?? 'details_and_notes') . '</h4>'
                . $admission_details;
        }

        $admission_requirements_application .= do_blocks('<!-- wp:buttons -->
            <div class="wp-block-buttons"><!-- wp:button -->
            <div class="wp-block-button"><a class="wp-block-button__link wp-element-button">' . ($labels['how_to_apply'] ?? 'how_to_apply') . '</a></div>
            <!-- /wp:button --></div>
            <!-- /wp:buttons --><!-- wp:buttons -->
            <div class="wp-block-buttons"><!-- wp:button -->
            <div class="wp-block-button"><a class="wp-block-button__link wp-element-button">' . ($labels['how_to_apply_internationals'] ?? 'how_to_apply_internationals') . '</a></div>
            <!-- /wp:button --></div>
            <!-- /wp:buttons -->');
        $admission_requirements_application .= '</div>';

    }
} else {
    $admission_requirements_application = '';
}

// Info Internationals
if (in_array('info_internationals_link', $items)) {
    //$cta_internationals_title = __('International Prospective Students', 'fau-studium-display');
    //$cta_internationals_id = sanitize_title($cta_internationals_title);
    $cta_internationals = '<div class="width-medium">'
                          . do_blocks('<!-- wp:rrze-elements/cta {
                          "url":"' . $constants['internationals-image'] . '",
                          "buttonUrl":"fau.de",
                          "alt":"",
                          "title":"' . ($labels['how_to_apply_internationals_title'] ?? 'how_to_apply_internationals_title') . '",
                          "subtitle":"' . ($labels['all_information_internationals'] ?? 'all_information_internationals') . '",
                          "buttonText":"' . ($labels['button_internationals'] ?? 'button_internationals') . '"
                          } /-->')
                          . '</div>';
    /*$quicklinks[1] = '<!-- wp:button -->
                <div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#' . $cta_internationals_id . '">' . $cta_internationals_title . '</a></div>
            <!-- /wp:button -->';*/
} else {
    $cta_internationals = '';
}

// Apply now!
$apply_now = '';
if (in_array('apply_now_link', $items) && !empty($data['apply_now_link']['link_url'])) {
    $apply_now_title = $constants['apply-now-title'];
    $apply_now_text = $constants['apply-now-text'];
    $apply_now_link_text = !empty($data['apply_now_link']['link_text']) ? $data['apply_now_link']['link_text'] : $constants['apply-now-link-text'];
    $apply_now_link_url = $data['apply_now_link']['link_url'];
    $apply_now_image = $constants['apply-now-image'];
    if (!empty($apply_now_title.$apply_now_text.$apply_now_link_text.$apply_now_link_url.$apply_now_image)) {
        $apply_now = '<div class="program-apply-now width-medium">'
                      . do_blocks('<!-- wp:rrze-elements/cta {"url":"' . esc_url($apply_now_image) . '","buttonUrl":"' . esc_url($apply_now_link_url) . '","alt":"","title":"' . esc_attr($apply_now_title) . '","subtitle":"' . esc_attr($apply_now_text) . '","buttonText":"' . esc_attr($apply_now_link_text) . '"} /-->')
                      . '</div>';
    }
}

// Button Student Advice
$student_advice = '';
if (in_array('student_advice', $items)) {
    $student_advice_img = $constants['general-student-advice-image'] ?? '';
    $student_advice_text = $descriptions['main_student_advice'] ?? '';
    $student_advice_link_text = $meinstudium_options['student_advice']['link_text'] ?? '';
    $student_advice_link_url = $meinstudium_options['student_advice']['link_url'] ?? '';
    if (!empty($student_advice_img.$student_advice_text.$student_advice_link_text.$student_advice_link_url)) {
        $student_advice = '<div class="advice-wrapper">';
        if (!empty($student_advice_img)) {
            $student_advice .= '<img src="' . $student_advice_img . '"  alt=""/>';
        }
        $student_advice .= '<a href="' . $student_advice_link_url . '">'
                           . '<span class="link-title">'. $student_advice_link_text . '</span>'
                           . '<span class="link-description">'. $descriptions['main_student_advice'] . '</span>'
                           . '<span class="icon-arrow-right"></span></a>';
        $student_advice .= '</div>';
    }
}

// Button Subject Specific Student Advice
if (in_array('subject_specific_advice', $items)
    && !empty($data['subject_specific_advice']['link_text'])
    && !empty($data['subject_specific_advice']['link_url'])) {
        $subject_specific_advice_img = $constants['specific-student-advice-image'] ?? '';
        $subject_specific_advice = '<div class="advice-wrapper">';
        if (!empty($subject_specific_advice_img)) {
            $subject_specific_advice .= '<img src="' . $subject_specific_advice_img . '"  alt=""/>';
        }
        $subject_specific_advice .= '<a href="' . $data['subject_specific_advice']['link_url'] . '">'
            . '<span class="link-title">'. $data['subject_specific_advice']['link_text'] . '</span>'
            . '<span class="link-description">'. $descriptions['subject_specific_advice'] . '</span>'
            . '<span class="icon-arrow-right"></span></a>';
    $subject_specific_advice .= '</div>';
} else {
    $subject_specific_advice = '';
}

$useful_links = '';
// Links: Organizational
if (in_array('links.organizational', $items)) {
    $fields_organizational = [
        'start_of_semester',
        'semester_dates',
        'examinations_office',
        'semester_fee',
        'service_centers',
        'abroad_opportunities',
    ];
    $links_organizational  = [];
    foreach ($fields_organizational as $item) {
        if ( ! empty($data[ $item ][ 'link_text' ])
             && ! empty($data[ $item ][ 'link_url' ])) {
            $links_organizational[ $item ] = '<a href="' . $data[ $item ][ 'link_url' ] . '">' . $data[ $item ][ 'link_text' ] . '</a>';
        }
    }
    if (!empty($links_organizational)) {
        $useful_links .= '<div class="useful-links-organizational">'
            . '<h4>' . ($labels['organizational'] ?? 'organizational') . '</h4>'
            . '<ul class="wp-block-list">';
        foreach ($links_organizational as $link) {
            $useful_links .= '<li>' . $link . '</li>';
        }
        $useful_links .= '</ul></div>';
    }
}

// Links: Downloads
if (in_array('links.downloads', $items)) {
    $fields_downloads = [
        'module_handbook',
        'examination_regulations'
    ];
    $links_downloads  = [];
    foreach ($fields_downloads as $item) {
        if ( ! empty($data[ $item ])) {
            $links_downloads[ $item ] = '<a href="' . $data[ $item ] . '">' . ($labels[ $item ] ?? $item) . '</a>';
        }
    }
    if (!empty($links_downloads)) {
        $useful_links .= '<div class="useful-links-downloads">'
            . '<h4>' . ($labels['downloads'] ?? 'downloads') . '</h4>'
            . '<ul class="wp-block-list">';
        foreach ($links_downloads as $link) {
            $useful_links .= '<li>' . $link . '</li>';
        }
        $useful_links .= '</ul></div>';
    }
}

if (!empty($student_advice . $subject_specific_advice . $useful_links)) {
    $student_advice_title = ($labels['student_advice_more'] ?? 'student_advice_more');
    $student_advice_id = sanitize_title($student_advice_title);
    $quicklinks[3] = '<!-- wp:button -->
            <div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#' . $student_advice_id . '">' . $student_advice_title . '</a></div>
            <!-- /wp:button -->';
}

// Links Additional
if (in_array('links.additional_information', $items)) {
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
                if ( ! empty($data[ $item ])) {
                    $links_additional[ $item ] = '<a href="' . $data[ $item ] . '">' . ($labels[ $item ] ?? $item) . '</a>';
                }
                break;
            case 'examinations_office':
            case 'student_initiatives':
                if ( ! empty($data[ $item ][ 'link_text' ])
                     && ! empty($data[ $item ][ 'link_url' ])) {
                    $links_additional[ $item ] = '<a href="' . $data[ $item ][ 'link_url' ] . '">' . $data[ $item ][ 'link_text' ] . '</a>';
                }
                break;
            case 'faculty':
                $links_faculty = [];
                foreach ($data[ $item ] as $faculty) {
                    if ( ! empty($faculty[ 'link_text' ])
                         && ! empty($faculty[ 'link_url' ])) {
                        $links_faculty[] = '<a href="' . $faculty[ 'link_url' ] . '">' . $faculty[ 'link_text' ] . '</a>';
                    }
                }
                $links_additional[ $item ] = implode(', ', $links_faculty);
                break;
        }
    }
    if (!empty($links_additional)) {
        $useful_links .= '<div class="useful-links-additional">'
            . '<h4>' . ($labels['additional_information'] ?? 'additional_information') . '</h4>'
            . '<ul class="wp-block-list">';
        foreach ($links_additional as $link) {
            $useful_links .= '<li>' . $link . '</li>';
        }
        $useful_links .= '</ul></div>';
    }
}

// Benefits @FAU
if (in_array('benefits', $items)) {

    $benefits_fau_image = $constants[ 'benefits-fau-image' ];
    $benefits_fau = '<div class="benefits width-full"><h2>' . ($labels['studies'] ?? 'studies'). '</h2>';
    $benefits_fau .= '<div class="fau-big-teaser width-large">'
        . '<div class="fau-big-teaser__content">'
        . '<h3 class="fau-big-teaser__headline">' . $constants[ 'benefits-fau-title' ] . '</h3>'
        . '<p class="fau-big-teaser__teaser-text">' . $constants[ 'benefits-fau-text' ] . '</p>'
        //. '<a href="' . $constants[ 'benefits-fau-link-url' ] . '" class="wp-block-buttons is-layout-flex">' . $constants[ 'benefits-fau-link-text' ] . '</a>'
        . '</div>'
        . '<div class="fau-big-teaser__image">'
        . '<img src="' . $benefits_fau_image . '" alt="" />'
        . '</div>'
        . '</div>';
    $benefits_fau .= do_blocks('<!-- wp:rrze-elements/iconbox-row -->
<!-- wp:rrze-elements/rrze-iconbox {"title":"Mehr als 275","description":"Studiengänge","materialSymbol":"school"} /-->
<!-- wp:rrze-elements/rrze-iconbox {"title":"Internationale","description":"Partnerschaften","materialSymbol":"language"} /-->
<!-- wp:rrze-elements/rrze-iconbox {"title":"\u003cstrong\u003eEnge Verknüpfung\u003c/strong\u003e","description":"mit der Wirtschaft","materialSymbol":"handshake"} /-->
<!-- wp:rrze-elements/rrze-iconbox {"title":"\u003cstrong\u003eBachelorverbundstudium\u003c/strong\u003e","description":"dual studieren","materialSymbol":"join_left"} /-->
<!-- /wp:rrze-elements/iconbox-row -->');
    $benefits_fau .= '</div>';
} else {
    $benefits_fau = '';
}


/*
 * HTML Output
 */

?>
<section class="fau-studium-display degree-program-full" itemtype="https://schema.org/EducationalOccupationalProgram https://schema.org/Course" itemscope>

    <?php echo $thumbnail; ?>

    <div class="program-content">

        <header class="program-header width-small">
            <?php echo $title . $subtitle . $meta_data ?>
        </header>

        <?php
        // Entry text
        echo $entry_text;

        // Fact sheet
        echo $fact_sheet;

        // Quicklinks
        echo '<div class="quicklinks width-large">';
        echo do_blocks('<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"space-between","orientation":"horizontal"}} --><div class="wp-block-buttons">' . implode('', $quicklinks) . '</div><!-- /wp:buttons -->');
        echo '</div>';

        // Details / content
        echo $content;

        // Videos
        echo $videos;

        if (!empty($cta_internationals.$admission_requirements_application.$apply_now)) {
            echo '<h2 id="' . $admission_requirements_application_id . '">' . $admission_requirements_application_title . '</h2>';
        }

        // Internationals
        echo $cta_internationals;

        // Admission
        if (!empty($admission_requirements_application)) {
            echo $admission_requirements_application;
        }

        // Apply now CTA
        echo $apply_now;

        // Student advice + more
        if (!empty($student_advice . $subject_specific_advice . $useful_links)) {
            echo '<div class="student-advice-more width-full">'
                . '<h2 id="' . $student_advice_id . '">' . $student_advice_title . '</h2>';

            // Student advice
            if (!empty($student_advice . $subject_specific_advice)) {
                echo '<div class="student-advice width-large"><h3>' . $labels['student_advice_title'] . '</h3>'
                     . do_blocks('<!-- wp:columns --><div class="wp-block-columns alignwide">'
                         . '<!-- wp:column --><div class="wp-block-column">' . $student_advice . '</div><!-- /wp:column -->'
                         . '<!-- wp:column --><div class="wp-block-column">' . $subject_specific_advice . '</div><!-- /wp:column -->'
                         . '</div><!-- /wp:columns -->')
                     . '</div>';
            }

            // Useful Links
            if (!empty($useful_links)) {
                echo '<div class="useful-links width-large">'
                     . '<h3>' . ($labels['useful_links'] ?? 'useful_links') . '</h3>'
                     . $useful_links
                     . '</div>';
            }

            echo '</div>';
        }

        //
        echo $benefits_fau;
        ?>

    </div>

</section>
