<?php

namespace Fau\DegreeProgram\Display\Config;

defined('ABSPATH') || exit;

function get_output_fields($format = '') {
    $output_fields = [
        'full' => [
            'teaser_image',
            'title',
            'subtitle',
            'standard_duration',
            'start',
            'number_of_students',
            'teaching_language',
            'attributes',
            'degree',
            'faculty',
            'location',
            'subject_groups',
            'videos',
            'content.about',
            'content.structure',
            'content.specializations',
            'content.qualities_and_skills',
            'content.why_should_study',
            'content.career_prospects',
            'admission_requirements',
            'admission_requirement_link',
            'details_and_notes',
            'start_of_semester',
            'semester_dates',
            'examinations_office',
            'examination_regulations',
            'module_handbook',
            'url',
            'department',
            'student_advice',
            'subject_specific_advice',
            'service_centers',
            'info_brochure',
            'semester_fee',
            'abroad_opportunities',
            'keywords',
            'area_of_study',
            'combinations',
            'limited_combinations',
            'notes_for_international_applicants',
            'student_initiatives',
            'apply_now_link',
            'entry_text',
            'content_related_master_requirements',
            'application_deadline_winter_semester',
            'application_deadline_summer_semester',
            'language_skills',
            'language_skills_humanities_faculty',
            'german_language_skills_for_international_students',
            'degree_program_fees'
        ],
        'box' => [
            'title',
            'degree',
            'start',
            'admission_requirements',
            'standard_duration',
            'number_of_students',
            'teaching_language',
            'faculty',
            'special_features',
        ],
        'grid' => [
            'teaser_image',
            'title',
            'subtitle',
            'degree',
            'start',
            'admission_requirements',
            'area_of_study'
        ],
        'table' => [
            'teaser_image',
            'title',
            'degree',
            'start',
            'location',
            'admission_requirements'
        ],
        'list' => [
            'title'
        ],
    ];

    if ($format != '' && isset($output_fields[$format])) {
        return $output_fields[$format];
    }
    return $output_fields;
}

function get_labels ($lang = 'de') {
    $labels = [
        'featured_image' => [
            'labels' => [
                'de' => 'Headerbild',
                'en' => 'Featured image'
            ]
        ],
        'teaser_image' => [
            'labels' => [
                'de' => 'Thumbnail',
                'en' => 'Thumbnail'
            ]
        ],
        'title' => [
            'labels' => [
                'de' => 'Titel',
                'en' => 'Title'
            ]
        ],
        'subtitle' => [
            'labels' => [
                'de' => 'Untertitel',
                'en' => 'Subtitle'
            ]
        ],
        'standard_duration' => [
            'labels' => [
                'de' => 'Regelstudienzeit',
                'en' => 'Duration of studies'
            ]
        ],
        'start' => [
            'labels' => [
                'de' => 'Studienbeginn',
                'en' => 'Start of degree program'
            ]
        ],
        'number_of_students' => [
            'labels' => [
                'de' => 'Größe',
                'en' => 'Number of students'
            ]
        ],
        'teaching_language' => [
            'labels' => [
                'de' => 'Unterrichtssprache',
                'en' => 'Teaching language'
            ]
        ],
        'attributes' => [
            'labels' => [
                'de' => 'Besondere Studienformen',
                'en' => 'Special ways to study'
            ]
        ],
        'degree' => [
            'labels' => [
                'de' => 'Abschluss',
                'en' => 'Degree'
            ]
        ],
        'faculty' => [
            'labels' => [
                'de' => 'Fakultät',
                'en' => 'Faculty'
            ]
        ],
        'location' => [
            'labels' => [
                'de' => 'Studienort',
                'en' => 'Study location'
            ]
        ],
        'subject_groups' => [
            'labels' => [
                'de' => 'Fächergruppen',
                'en' => 'Subject groups'
            ]
        ],
        'videos' => [
            'labels' => [
                'de' => 'Videos',
                'en' => 'Videos'
            ]
        ],
        'content.about' => [
            'labels' => [
                'de' => 'Inhalt: Worum geht es?',
                'en' => 'Content: About'
            ]
        ],
        'content.structure' => [
            'labels' => [
                'de' => 'Inhalt: Struktur',
                'en' => 'Content: Structure'
            ]
        ],
        'content.specializations' => [
            'labels' => [
                'de' => 'Inhalt: Schwerpunkte',
                'en' => 'Content: Specializations'
            ]
        ],
        'content.qualities_and_skills' => [
            'labels' => [
                'de' => 'Inhalt: Was mitbringen',
                'en' => 'Content: Qualities and skills'
            ]
        ],
        'content.why_should_study' => [
            'labels' => [
                'de' => 'Inhalt: Warum FAU?',
                'en' => 'Content: Why study at FAU?'
            ]
        ],
        'content.career_prospects' => [
            'labels' => [
                'de' => 'Inhalt: Karriereperspektiven',
                'en' => 'Content: Career prospects'
            ]
        ],
        'admission_requirements' => [
            'labels' => [
                'de' => 'Zugangsvoraussetzungen',
                'en' => 'Admission requirements'
            ]
        ],
        'admission_requirement_link' => [
            'labels' => [
                'de' => 'Link Zugangsvoraussetzungen',
                'en' => 'Admission requirements link'
            ]
        ],
        'details_and_notes' => [
            'labels' => [
                'de' => 'Details und Anmerkungen',
                'en' => 'Details and notes'
            ]
        ],
        'start_of_semester' => [
            'labels' => [
                'de' => 'Informationen zum Semesterstart',
                'en' => 'Start of semester'
            ]
        ],
        'semester_dates' => [
            'labels' => [
                'de' => 'Semestertermine',
                'en' => 'Semester dates'
            ]
        ],
        'examinations_office' => [
            'labels' => [
                'de' => 'Prüfungsamt',
                'en' => 'Examinations office'
            ]
        ],
        'examination_regulations' => [
            'labels' => [
                'de' => 'Prüfungsordnungen',
                'en' => 'Examination regulations'
            ]
        ],
        'module_handbook' => [
            'labels' => [
                'de' => 'Modulhandbuch',
                'en' => 'Module handbook'
            ]
        ],
        'url' => [
            'labels' => [
                'de' => 'URL',
                'en' => 'URL'
            ]
        ],
        /*'department' => [
            'labels' => [
                'de' => 'Department',
                'en' => 'Department'
            ]
        ],*/
        'student_advice' => [
            'labels' => [
                'de' => 'Zentrale Studienberatung',
                'en' => 'Student advice center'
            ]
        ],
        'subject_specific_advice' => [
            'labels' => [
                'de' => 'Spezifische Studienberatung',
                'en' => 'Specific Student Advice'
            ]
        ],
        'service_centers' => [
            'labels' => [
                'de' => 'Servicecenter',
                'en' => 'Service centers'
            ]
        ],
        'info_brochure' => [
            'labels' => [
                'de' => 'Infobroschüre',
                'en' => 'Info brochure'
            ]
        ],
        'semester_fee' => [
            'labels' => [
                'de' => 'Semesterbeitrag',
                'en' => 'Semester fee'
            ]
        ],
        'abroad_opportunities' => [
            'labels' => [
                'de' => 'Wege ins Ausland',
                'en' => 'Going abroad'
            ]
        ],
        'keywords' => [
            'labels' => [
                'de' => 'Schlagwörter',
                'en' => 'Keywords'
            ]
        ],
        'area_of_study' => [
            'labels' => [
                'de' => 'Fachbereich',
                'en' => 'Area of study'
            ]
        ],
        'combinations' => [
            'labels' => [
                'de' => 'Kombinationen',
                'en' => 'Combinations'
            ]
        ],
        'limited_combinations' => [
            'labels' => [
                'de' => 'Eingeschränkte Kombinationen',
                'en' => 'Limited combinations'
            ]
        ],
        'notes_for_international_applicants' => [
            'labels' => [
                'de' => 'Hinweise für internationale Bewerber',
                'en' => 'Notes for international applicants'
            ]
        ],
        'student_initiatives' => [
            'labels' => [
                'de' => 'Studentische Initiativen',
                'en' => 'Student initiatives'
            ]
        ],
        'apply_now_link' => [
            'labels' => [
                'de' => 'Jetzt-Bewerben-Link',
                'en' => 'Apply now link'
            ]
        ],
        'entry_text' => [
            'labels' => [
                'de' => 'Einstiegstext',
                'en' => 'Entry text'
            ]
        ],
        'content_related_master_requirements' => [
            'labels' => [
                'de' => 'Voraussetzungen für Master',
                'en' => 'Content related master requirements'
            ]
        ],
        'application_deadline_winter_semester' => [
            'labels' => [
                'de' => 'Bewerbungsfrist Wintersemester',
                'en' => 'Application deadline winter semester'
            ]
        ],
        'application_deadline_summer_semester' => [
            'labels' => [
                'de' => 'Bewerbungsfrist Sommersemester',
                'en' => 'Application deadline summer semester'
            ]
        ],
        'language_skills' => [
            'labels' => [
                'de' => 'Sprachkenntnisse',
                'en' => 'Language skills'
            ]
        ],
        'language_skills_humanities_faculty' => [
            'labels' => [
                'de' => 'Sprachkenntnisse Philosophische Fakultät',
                'en' => 'Language skills at humanities faculty'
            ]
        ],
        'german_language_skills_for_international_students' => [
            'labels' => [
                'de' => 'Deutschkenntnisse für internationale Studierende',
                'en' => 'German language skills for international applicants'
            ]
        ],
        'degree_program_fees' => [
            'labels' => [
                'de' => 'Studiengebühren',
                'en' => 'Degree program fees']
        ],
        'link' => [
            'labels' => [
                'de' => 'Webseite des Studiengangs',
                'en' => 'Website of the degree program'
            ]
        ],
        'department' => [
            'labels' => [
                'de' => 'Webseite des Departments/Instituts',
                'en' => 'Department/Institute website'
            ]
        ]
    ];

    $labels_out = [];
    $lang_alt = $lang == 'de' ? 'en' : 'de';
    foreach ($labels as $key => $translations) {
        if (!empty($translations[ 'labels' ][ $lang ])) { // return selected translation
            $labels_out[  $key ] = $labels[ $key ][ 'labels' ][ $lang ];
            continue;
        }
        if (!empty($translations[ 'labels' ][ $lang_alt ])) { // return alternative language
            $labels_out[  $key ] = $labels[ $key ][ 'labels' ][ $lang_alt ];
            continue;
        }
        $labels_out[  $key ] = $key; // return key if no translation is found
    }

    return $labels_out;
}