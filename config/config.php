<?php

namespace Fau\DegreeProgram\Display\Config;

defined('ABSPATH') || exit;

function get_output_fields($format = '') {
    $output_fields = [
        'full' => [
            'teaser_image',
            'title',
            'subtitle',
            'entry_text',
            'fact_sheet',
            //'degree',
            //'admission_requirements',
            //'admission_requirement_link',
            //'standard_duration',
            //'teaching_language',
            //'faculty',
            //'start',
            //'number_of_students',
            //'location',
            //'attributes',
            'content.about',
            'content.structure',
            'content.specializations',
            'content.qualities_and_skills',
            'content.why_should_study',
            'content.career_prospects',
            'content.special_features',
            'content.testimonials',
            'videos',
            'admission_requirements_application',
            'admission_requirements_application_internationals',
            'apply_now_link',
            'student_advice',
            'subject_specific_advice',
            'links.organizational',
            'links.downloads',
            'links.additional_information'

            //'subject_groups',
            //'details_and_notes',
            //'start_of_semester',
            //'semester_dates',
            //'examinations_office',
            //'examination_regulations',
            //'module_handbook',
            //'url',
            //'department',
            //'service_centers',
            //'info_brochure',
            //'semester_fee',
            //'abroad_opportunities',
            //'keywords',
            //'area_of_study',
            //'combinations',
            //'limited_combinations',
            //'notes_for_international_applicants',
            //'student_initiatives',
            //'content_related_master_requirements',
            //'application_deadline_winter_semester',
            //'application_deadline_summer_semester',
            //'language_skills',
            //'language_skills_humanities_faculty',
            //'german_language_skills_for_international_students',
            //'degree_program_fees'
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
            'admission_requirements',
            'german_language_skills_for_international_students',
            'application_deadline',
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

function get_labels ($lang = 'de', $task = 'labels') {
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
        'content.special_features' => [
            'labels' => [
                'de' => 'Inhalt: Besondere Hinweise',
                'en' => 'Content: Special features'
            ]
        ],
        'content.testimonials' => [
            'labels' => [
                'de' => 'Inhalt: Erfahrungsberichte',
                'en' => 'Content: Testimonials'
            ]
        ],
        'admission_requirements' => [
            'labels' => [
                'de' => 'Zugang',
                'en' => 'Admission'
            ]
        ],
        'admission_requirement_link' => [
            'labels' => [
                'de' => 'Link Zugangsvoraussetzungen',
                'en' => 'Admission requirements link'
            ]
        ],
        'admission_requirements_application' => [
            'labels' => [
                'de' => 'Zulassungsvoraussetzungen und Bewerbung',
                'en' => 'Admission Requirements and Application'
            ]
        ],
        'admission_requirements_application_internationals' => [
            'labels' => [
                'de' => 'Ergänzende Hinweise zur Bewerbung für Internationale',
                'en' => 'More Information for International Applicants'
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
                'de' => 'Studienberatung',
                'en' => 'Student advice'
            ]
        ],
        'main_student_advice' => [
            'labels' => [
                'de' => 'Zentrale Studienberatung',
                'en' => 'Student advice center'
            ],
            'description' => [
                'de' => 'Die Zentrale Studienberatung ist deine Anlaufstelle für alle Fragen rund ums Studium und den Studieneinstieg.',
                'en' => 'Die Zentrale Studienberatung ist deine Anlaufstelle für alle Fragen rund ums Studium und den Studieneinstieg.', // ToDo: Übersetzung
            ],
        ],
        'subject_specific_advice' => [
            'labels' => [
                'de' => 'Spezifische Studienberatung',
                'en' => 'Specific Student Advice'
            ],
            'description' => [
                'de' => 'Die Studien-Servcie-Center und Studienfachberater unterstützen dich bei der Planung deines Studiums.',
                'en' => 'Die Studien-Servcie-Center und Studienfachberater unterstützen dich bei der Planung deines Studiums.', // ToDo: Übersetzung
            ],
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
        'more_info_for_international_applicants' => [
            'labels' => [
                'de' => 'Ergänzende Hinweise zur Bewerbung für internationale Bewerber',
                'en' => 'More Information for International Applicants'
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
        'application_deadline' => [
            'labels' => [
                'de' => 'Bewerbungsfrist',
                'en' => 'Application deadline'
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
        'german_language_skills' => [
            'labels' => [
                'de' => 'Deutschkenntnisse',
                'en' => 'German language skills'
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
        ],
        'path_to_admission' => [
            'labels' => [
                'de' => 'Dein Weg zum Studienplatz',
                'en' => 'Your Path to University Admission'
            ]
        ],
        'useful_links' => [
            'labels' => [
                'de' => 'Nützliche Links',
                'en' => 'Useful Links'
            ]
        ],
        'organizational' => [
            'labels' => [
                'de' => 'Organisatorisch',
                'en' => 'Organizational'
            ]
        ],
        'links.organizational' => [
            'labels' => [
                'de' => 'Links: Organisatorisch',
                'en' => 'Links: Organizational'
            ]
        ],
        'downloads' => [
            'labels' => [
                'de' => 'Downloads',
                'en' => 'Downloads'
            ]
        ],
        'links.downloads' => [
            'labels' => [
                'de' => 'Links: Downloads',
                'en' => 'Links: Downloads'
            ]
        ],
        'additional_information' => [
            'labels' => [
                'de' => 'Weitere Informationen',
                'en' => 'Additional Information'
            ]
        ],
        'links.additional_information' => [
            'labels' => [
                'de' => 'Links: Weitere Informationen',
                'en' => 'Links: Additional Information'
            ]
        ],
        'special_features' => [
            'labels' => [
                'de' => 'Besondere Hinweise',
                'en' => 'Special Features'
            ]
        ],
        'fact_sheet' => [
            'labels' => [
                'de' => 'Steckbrief',
                'en' => 'Fact Sheet'
            ]
        ],
    ];

    $labels_out = [];
    $lang_alt   = $lang == 'de' ? 'en' : 'de';
    if ($task == 'description') {
        foreach ($labels as $key => $translations) {
            if ( ! empty($translations[ 'description' ][ $lang ])) { // return selected translation
                $labels_out[ $key ] = $translations[ 'description' ][ $lang ];
                continue;
            }
            if ( ! empty($translations[ 'description' ][ $lang_alt ])) { // return alternative language
                $labels_out[ $key ] = $translations[ 'description' ][ $lang_alt ];
                continue;
            }
            $labels_out[ $key ] = $key; // return key if no translation is found
        }
    } else {
        foreach ($labels as $key => $translations) {
            if ( ! empty($translations[ 'labels' ][ $lang ])) { // return selected translation
                $labels_out[ $key ] = $translations[ 'labels' ][ $lang ];
                continue;
            }
            if ( ! empty($translations[ 'labels' ][ $lang_alt ])) { // return alternative language
                $labels_out[ $key ] = $translations[ 'labels' ][ $lang_alt ];
                continue;
            }
            $labels_out[ $key ] = $key; // return key if no translation is found
        }
    }

    return $labels_out;
}