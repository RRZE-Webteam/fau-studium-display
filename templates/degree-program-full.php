<?php

defined('ABSPATH') || exit;

use function \Fau\DegreeProgram\Display\Config\get_labels;

//var_dump($data);
//var_dump($atts);

if (empty($atts['degreeProgram'])) {
    print '<div class="components-placeholder is-large">' . __('Please select a degree program.', 'fau-studium-display') . '</div>';
    return;
}

$items = $atts['selectedItemsFull'] ?? [];
$lang = $atts[ 'language' ] ?? 'de';
$labels = get_labels($lang);
$descriptions = get_labels($lang, 'description');
//print "<pre>"; var_dump($labels); print "</pre>";

$number_of_students_raw = $data['number_of_students']['name'] ?? '';
if (!empty($number_of_students_raw)) {
    $number_of_students_array = explode('-', $number_of_students_raw);
    $number_of_students = $number_of_students_array[1];
}

/*
 * Build items
 */

// Title
if (in_array('title', $items) && ! empty($data[ 'title' ])) {
    $title = '<h1 class="title" itemprop="name">' . esc_attr($data['title'] . ' (' . $data['degree']['abbreviation'] . ')') . '</h1>';
} else {
    $title = '';
}

// Subtitle
if (in_array('subtitle', $items) && ! empty($data[ 'subtitle' ])) {
    $subtitle = '<p class="program-subtitle">' . $data['subtitle'] . '</p>';
} else {
    $subtitle = '';
}

// Entry Text
if (in_array('entry_text', $items) && ! empty($data[ 'entry_text' ])) {
    $entry_text = '<div class="entry-text width-small">' . $data['entry_text'] . '</div>';
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
            'value' => (!empty($data['standard_duration']) ? sprintf(__('%s semesters', 'fau-studium-display'), $data['standard_duration']): ''),
            'itemprop' => 'timeToComplete'
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
            <h1>' . __('Fact Sheet', 'fau-studium-display') . '</h1>';
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
$content_fields_all = ['content.about', 'content.structure', 'content.specializations', 'content.qualities_and_skills', 'content.why_should_study', 'content.career_prospects', 'special_features', 'testimonials'];
$content_fields = array_intersect($content_fields_all, $items);

$content = '<div class="program-details width-small">
    [collapsibles style="light"]';
foreach ($content_fields as $field) {
    $field_name = str_replace('content.', '', $field);
    if (!empty($data['content'][$field_name]['description'])) {
        $content .= '[collapse title="' . $data['content'][$field_name]['title'] . '"]';
        $content .= $data['content'][$field_name]['description'];
        $content .= '[/collapse]';
    }
}
$content .= '[/collapsibles]
    </div>';

// Videos
if (in_array('videos', $items) && !empty($data['videos'])) {
    $videos = '<div class="program-videos width-large">[alert color="#1f4c7a"][columns]';
            foreach ($data['videos'] as $video) {
                $videos .= '[column][fauvideo url="' . $video . '"][/column]';
            }
            $videos .= '[/columns][/alert]</div>';
} else {
    $videos = '';
}

// Admission Requirements and Application
if (in_array('admission_requirements_application', $items)) {

    $admission_requirements = [];
    if (in_array('admission_requirements', $items)) {
        if (!empty($data['admission_requirements']['bachelor_or_teaching_degree'])) {
            $admission_requirements['bachelor_or_teaching_degree'] = __('1st semester', 'fau-studium-display') . ': <a href="' . $data['admission_requirements']['bachelor_or_teaching_degree']['link_url'] . '">' . $data['admission_requirements']['bachelor_or_teaching_degree']['link_text'] . '</a>';
        }
        if (!empty($data['admission_requirements']['teaching_degree_higher_semester'])) {
            $admission_requirements['teaching_degree_higher_semester'] = __('Higher semesters', 'fau-studium-display') . ': <a href="' . $data['admission_requirements']['teaching_degree_higher_semester']['link_url'] . '">' . $data['admission_requirements']['teaching_degree_higher_semester']['link_text'] . '</a>';
        }
        if (!empty($data['admission_requirements']['master'])) {
            $admission_requirements['master'] = __('Master', 'fau-studium-display') . ': <a href="' . $data['admission_requirements']['master']['link_url'] . '">' . $data['admission_requirements']['master']['link_text'] . '</a>';
        }
    }

    $deadlines = [];
    if (in_array('application_deadlines', $items)) {
        $deadlines['winter_semester'] = __('Winter semester', 'fau-studium-display') . ': ' . (empty($data['application_deadline_winter_semester']) ? __('not possible', 'fau-studium-display') : strip_tags($data['application_deadline_winter_semester']));
        $deadlines['summer_semester'] = __('Summer semester', 'fau-studium-display') . ': ' . (empty($data['application_deadline_summer_semester']) ? __('not possible', 'fau-studium-display') : strip_tags($data['application_deadline_summer_semester']));
    }

    $language_skills = (in_array('language_skills', $items) && !empty($data['language_skills'])) ? $data['language_skills'] : '';

    $content_related_master_requirements = (in_array('content_related_master_requirements', $items) && !empty($data['content_related_master_requirements'])) ? $data['content_related_master_requirements'] : '';

    $admission_details = !empty($data['details_and_notes']) ? $data['details_and_notes'] : '';

    $admission_requirements_application = '';
    if (!empty($admission_requirements)
        || !empty($deadlines)
        || !empty($language_skills)
        || !empty($content_related_master_requirements)
        || !empty($admission_details)
        || !empty($internationals_admission_requirements)) {

        $admission_requirements_application .= '<div class="program-admission-general"><h3>' . ($labels['admission_requirements_application'] ?? 'admission_requirements_application') . '</h3>';

        if (!empty($admission_requirements)) {
            $admission_requirements_application .= '<h4>' . ($labels['admission_requirements'] ?? 'admission_requirements') . '</h4><ul>';
            foreach ($admission_requirements as $requirement) {
                $admission_requirements_application .= '<li>' . $requirement . '</li>';
            }
            $admission_requirements_application .= '</ul>';
        }

        if (!empty($deadlines)) {
            $admission_requirements_application .= '<h4>' . ($labels['application_deadline'] ?? 'application_deadline') . '</h4><ul>';
            foreach ($deadlines as $deadline) {
                $admission_requirements_application .= '<li>' . strip_tags($deadline) . '</li>';
            }
            $admission_requirements_application .= '</ul>';
        }

        if (!empty($language_skills)) {
            $admission_requirements_application .= '<h4>' . ($labels['language_skills'] ?? 'language_skills') . '</h4>'
                . '<p>' . strip_tags($language_skills) . '</p>';
        }

        if (!empty($content_related_master_requirements)) {
            $admission_requirements_application .= '<h4>' . ($labels['content_related_master_requirements'] ?? 'content_related_master_requirements') . '</h4>'
                . $content_related_master_requirements;
        }

        if (!empty($admission_details)) {
            $admission_requirements_application .= '<h4>' . ($labels['details_and_notes'] ?? 'details_and_notes') . '</h4>'
                . $admission_details;
        }
        $admission_requirements_application .= '</div>';

            //<!-- Admission – International students -->

    }
} else {
    $admission_requirements_application = '';
}

// More Information for International Applicants
if (in_array('admission_requirements_application_internationals', $items)) {
    $internationals_admission_requirements = [];
    if (!empty($data['german_language_skills_for_international_students']['link_text'])
        && !empty($data['german_language_skills_for_international_students']['link_url'])) {
        $internationals_admission_requirements['german_language_skills'] = '<a href="' . $data['german_language_skills_for_international_students']['link_url'] . '">' . $data['german_language_skills_for_international_students']['link_text'] . '</a>';
    } elseif (!empty($data['german_language_skills_for_international_students']['name'])) {
        $internationals_admission_requirements['german_language_skills'] = $data['german_language_skills_for_international_students']['name'];
    }

    $admission_requirements_application_internationals = '';
    if (!empty($internationals_admission_requirements)) {
        $admission_requirements_application_internationals .= '<div class="program-admission-internationals">'
            . '<div class="icon-globe"></div>'
            . '<h3>' . ($labels['admission_requirements_application_internationals'] ?? 'admission_requirements_application_internationals') . '</h3>';

        if (!empty($internationals_admission_requirements['german_language_skills'])) {
            $admission_requirements_application_internationals .= '<h4>' . ($labels['german_language_skills'] ??'german_language_skills') . '</h4>'
                 . '<p>'. $internationals_admission_requirements['german_language_skills'] . '</p>';
        }

        $admission_requirements_application_internationals .= '</div>';
   }
} else {
    $admission_requirements_application_internationals = '';
}

// Button Student Advice
if (in_array('student_advice', $items)
    && !empty($data['student_advice']['link_text'])
    && !empty($data['student_advice']['link_url'])) {
        $student_advice = '<a href="' . $data['student_advice']['link_url'] . '">'
            . '<span class="link-title">'. $data['student_advice']['link_text'] . '</span>'
            . '<span class="link-description">'. $descriptions['main_student_advice'] . '</span></a>';
} else {
    $student_advice = '';
}

// Button Subject Specific Student Advice
if (in_array('subject_specific_advice', $items)
    && !empty($data['subject_specific_advice']['link_text'])
    && !empty($data['subject_specific_advice']['link_url'])) {
    $subject_specific_advice = '<a href="' . $data['subject_specific_advice']['link_url'] . '">'
                      . '<span class="link-title">'. $data['subject_specific_advice']['link_text'] . '</span>'
                      . '<span class="link-description">'. $descriptions['subject_specific_advice'] . '</span></a>';
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
            . '<h3>' . ($labels['organizational'] ?? 'organizational') . '</h3>'
            . '<ul>';
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
            . '<h3>' . ($labels['downloads'] ?? 'downloads') . '</h3>'
            . '<ul>';
        foreach ($links_downloads as $link) {
            $useful_links .= '<li>' . $link . '</li>';
        }
        $useful_links .= '</ul></div>';
    }
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
            . '<h3>' . ($labels['additional_information'] ?? 'additional_information') . '</h3>'
            . '<ul>';
        foreach ($links_additional as $link) {
            $useful_links .= '<li>' . $link . '</li>';
        }
        $useful_links .= '</ul></div>';
    }
}

/*
 * HTML Output
 */

?>
<section class="fau-studium-display degree-program-full" itemtype="https://schema.org/EducationalOccupationalProgram" itemscope>
    <?php

    if (in_array('teaser_image', $items)) {
        echo $data['featured_image']['rendered'];
    } ?>

    <div class="program-content">

        <!-- Header -->
        <?php if (!empty($title.$subtitle)) { ?>
            <header class="program-header width-small">
                <?php echo $title . $subtitle ?>
            </header>
        <?php }

        // Entry text
        echo $entry_text;

        // Fact sheet
        echo $fact_sheet;

        // Details / content
        echo $content;

        // Videos
        echo $videos;

        // Admission
        if (!empty($admission_requirements_application . $admission_requirements_application_internationals)) {
            echo '<div class="program-admission width-small">'
            . '<h2>' . ($labels['path_to_admission'] ?? 'path_to_admission') . '</h2>'
             . $admission_requirements_application . $admission_requirements_application_internationals
            . '</div>';
        }

        // Student advice
        if (!empty($student_advice . $subject_specific_advice)) {
            // ToDo: Bilder
            echo '<div class="student-advice width-large">[alert color="#1f4c7a"]'
                . '<h2>' . ($labels['student_advice'] ?? 'student_advice') . '</h2>'
                . '[columns][column]' . $student_advice . '[/column]'
                . '[column]' . $subject_specific_advice . '[/column]'
                . '[/columns][/alert]</div>';
        }

        // Useful Links
        if (!empty($useful_links)) {
            echo '<div class="useful-links width-medium">'
                . '<h2>' . ($labels['useful_links'] ?? 'useful_links') . '</h2>'
                . $useful_links
                . '</div>';
        }

        ?>

    </div>

</section>
