<?php

namespace Fau\DegreeProgram\Display\Config;

use function Fau\DegreeProgram\Display\plugin;

defined('ABSPATH') || exit;

function get_output_fields($format = '') {
    $output_fields = [
        'full' => [
            'teaser_image',
            'title',
            'subtitle',
            'entry_text',
            'fact_sheet',
            'content.about',
            'content.structure',
            'content.specializations',
            'content.qualities_and_skills',
            'content.why_should_study',
            'content.career_prospects',
            'content.special_features',
            'combinations',
            'videos',
            'info_internationals_link',
            'admission_requirements_application',
            'apply_now_link',
            'student_advice',
            'subject_specific_advice',
            'links.organizational',
            'links.downloads',
            'links.additional_information',
            'benefits'
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
        'search-filters' => [
            'degree',
            'subject_group',
            'attribute',
            'admission_requirements',
            'semester',
            'study_location',
            'teaching_language',
            'faculty',
            'german_language_skills_for_international_students',
            //'area-of-study',
        ]
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
        'about' => [
            'labels' => [
                'de' => 'Worum geht es im Studiengang?',
                'en' => 'What is the degree program about?'
            ]
        ],
        'content.about' => [
            'labels' => [
                'de' => 'Inhalt: Worum geht es?',
                'en' => 'Content: About'
            ]
        ],
        'structure' => [
            'labels' => [
                'de' => 'Aufbau und Struktur',
                'en' => 'Design and structure'
            ]
        ],
        'content.structure' => [
            'labels' => [
                'de' => 'Inhalt: Struktur',
                'en' => 'Content: Structure'
            ]
        ],
        'specializations' => [
            'labels' => [
                'de' => 'Studienrichtungen und Schwerpunkte',
                'en' => 'Fields of study and specializations'
            ]
        ],
        'content.specializations' => [
            'labels' => [
                'de' => 'Inhalt: Schwerpunkte',
                'en' => 'Content: Specializations'
            ]
        ],
        'qualities_and_skills' => [
            'labels' => [
                'de' => 'Was sollte ich mitbringen?',
                'en' => 'Which qualities and skills do I need?'
            ]
        ],
        'content.qualities_and_skills' => [
            'labels' => [
                'de' => 'Inhalt: Was mitbringen',
                'en' => 'Content: Qualities and skills'
            ]
        ],
        'why_should_study' => [
            'labels' => [
                'de' => 'Gute Gründe für ein Studium an der FAU',
                'en' => 'Why should I study at FAU?'
            ]
        ],
        'content.why_should_study' => [
            'labels' => [
                'de' => 'Inhalt: Warum FAU?',
                'en' => 'Content: Why study at FAU?'
            ]
        ],
        'career_prospects' => [
            'labels' => [
                'de' => 'Welche beruflichen Perspektiven stehen mir offen?',
                'en' => 'Which career prospects are open to me?'
            ]
        ],
        'content.career_prospects' => [
            'labels' => [
                'de' => 'Inhalt: Karriereperspektiven',
                'en' => 'Content: Career prospects'
            ]
        ],
        'special_features' => [
            'labels' => [
                'de' => 'Besondere Hinweise',
                'en' => 'Special features'
            ]
        ],
        'content.special_features' => [
            'labels' => [
                'de' => 'Inhalt: Besondere Hinweise',
                'en' => 'Content: Special features'
            ]
        ],
        'testimonials' => [
            'labels' => [
                'de' => 'Erfahrungsberichte',
                'en' => 'Testimonials'
            ]
        ],
        'content.testimonials' => [
            'labels' => [
                'de' => 'Inhalt: Erfahrungsberichte',
                'en' => 'Content: Testimonials'
            ]
        ],
        'info_internationals_link' => [
            'labels' => [
                'de' => 'CTA: Info für Internationals',
                'en' => 'CTA: Information for Internationals'
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
        'student_advice_more' => [
            'labels' => [
                'de' => 'Studienberatung und weitere Infos',
                'en' => 'Student advice and more'
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
                'en' => 'Specific student advice'
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
                'de' => 'Mögliche Studiengangskombinationen',
                'en' => 'Possible degree program combinations'
            ]
        ],
        'content.combinations' => [
            'labels' => [
                'de' => 'In der Regel ohne Überschneidungen',
                'en' => 'Possible combinations without overlaps'
            ]
        ],
        'limited_combinations' => [
            'labels' => [
                'de' => 'Eingeschränkte Kombinationen',
                'en' => 'Limited combinations'
            ]
        ],
        'content.limited_combinations' => [
            'labels' => [
                'de' => 'Mögliche Überschneidungen im Stundenplan',
                'en' => 'Possible overlaps in the timetable'
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
                'de' => 'CTA: Jetzt bewerben',
                'en' => 'CTA: Apply now'
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
        /*'special_features' => [
            'labels' => [
                'de' => 'Besondere Hinweise',
                'en' => 'Special Features'
            ]
        ],*/
        'fact_sheet' => [
            'labels' => [
                'de' => 'Steckbrief',
                'en' => 'Fact Sheet'
            ]
        ],
        'benefits' => [
            'labels' => [
                'de' => 'Deine Vorteile an der FAU',
                'en' => 'Your Benefits at FAU'
            ]
        ],
        'how_to_apply' => [
            'labels' => [
                'de' => 'So läuft die Bewerbung ab',
                'en' => 'How to apply'
            ]
        ],
        'how_to_apply_internationals' => [
            'labels' => [
                'de' => 'So läuft die Bewerbung für Internationale ab',
                'en' => 'How to apply as an international student'
            ]
        ]
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

function get_constants($lang = 'de') {
    $mein_studium_options = get_meinstudium_options($lang);
    $constants = [
        'apply-now-title' => [
            'de' => 'Bewirb dich jetzt!',
            'en' => 'Apply now!'
        ],
        'apply-now-text' => [
            'de' => 'auf campo, dem Bewerbungsportal der FAU',
            'en' => 'on campo, the FAU application portal'
        ],
        'apply-now-link-text' => [
            'de' => 'Jetzt auf campo bewerben/anmelden',
            'en' => 'Apply/Register now on campo'
        ],
        'apply-now-link-url' => [
            'de' => 'https://www.campo.fau.de',
            'en' => 'https://www.campo.fau.de'
        ],
        'apply-now-image' => [
            'de' => plugin()->getUrl() . 'assets/img/apply-now.jpg',
            'en' => plugin()->getUrl() . 'assets/img/apply-now.jpg'
        ],
        'internationals-image' => [
            'de' => plugin()->getUrl() . 'assets/img/internationals.jpg',
            'en' => plugin()->getUrl() . 'assets/img/internationals.jpg'
        ],
        'general-student-advice-image' => [
            'de' => plugin()->getUrl() . 'assets/img/student-advice.jpg',
            'en' => plugin()->getUrl() . 'assets/img/student-advice.jpg'
        ],
        'specific-student-advice-image' => [
            'de' => plugin()->getUrl() . 'assets/img/student-advice-specific.jpeg',
            'en' => plugin()->getUrl() . 'assets/img/student-advice-specific.jpeg'
        ],
        'benefits-fau-title' => [
            'de' => 'Deine Vorteile an der FAU',
            'en' => 'Deine Vorteile an der FAU'
        ],
        'benefits-fau-text' => [
            'de' => 'Die FAU bietet Ihnen mit über 270 Studiengängen eine inspirierende Lernumgebung, studentische Gemeinschaft und zahlreiche Möglichkeiten, Ihre Leidenschaft zu entdecken.',
            'en' => 'Die FAU bietet Ihnen mit über 270 Studiengängen eine inspirierende Lernumgebung, studentische Gemeinschaft und zahlreiche Möglichkeiten, Ihre Leidenschaft zu entdecken.'
        ],
        'benefits-fau-link-text' => [
            'de' => 'Mehr erfahren',
            'en' => 'Mehr erfahren'
        ],
        'benefits-fau-link-url' => [
            'de' => 'https://fau.de',
            'en' => 'https://fau.eu'
        ],
        'benefits-fau-image' => [
            'de' => plugin()->getUrl() . 'assets/img/benefits-fau.png',
            'en' => plugin()->getUrl() . 'assets/img/benefits-fau.png'
        ],
        'features-1-text' => [
            'de' => 'Mehr als 270 Studiengänge',
            'en' => 'More than 270 degree programs',
        ],
        'features-1-icon' => [
            'de' => 'school',
            'en' => 'school',
        ],
        'features-2-text' => [
            'de' => 'Internationale Partnerschaften',
            'en' => 'International partnerships',
        ],
        'features-2-icon' => [
            'de' => 'language',
            'en' => 'language',
        ],
        'features-3-text' => [
            'de' => 'Enge Verknüpfung mit der Wirtschaft',
            'en' => 'Close ties to industry',
        ],
        'features-3-icon' => [
            'de' => 'handshake',
            'en' => 'handshake',
        ],
        'features-4-text' => [
            'de' => 'Duales Bachelorstudium möglich',
            'en' => "Cooperative bachelor's program available",
        ],
        'features-4-icon' => [
            'de' => 'join_left',
            'en' => 'join_left',
        ],
        'schema_termsPerYear' => [
            'de' => '<meta itemprop="termsPerYear" content="2">',
        ],
        'schema_termDuration' => [
            'de' => '<meta itemprop="termDuration" content="P6M">',
        ],
        'schema_provider' => [
            'de' => '<div itemprop="provider" itemscope itemtype="https://schema.org/CollegeOrUniversity">'
                    . '<meta itemprop="name" content="Friedrich-Alexander-Universität Erlangen-Nürnberg">'
                    . '<meta itemprop="url" content="https://www.fau.de">'
                    . '<div itemprop="address" itemscope="" itemtype="https://schema.org/PostalAddress">'
                    . '<meta itemprop="streetAddress" content="Freyeslebenstr. 1">'
                    . '<meta itemprop="postalCode" content="91058">'
                    . '<meta itemprop="addressLocality" content="Erlangen">'
                    . '<meta itemprop="addressCountry" content="DE">'
                    . '</div>'
                    . '</div>',
        ],
        'schema_offer' => [
            'de' => '<div itemprop="offers" itemscope itemtype="https://schema.org/Offer">'
                    //. '<meta itemprop="category" content="">'
                    . '<meta itemprop="category" content="' . $mein_studium_options['semester_fee']['name'] . '">'
                    . '<meta itemprop="url" content="' . $mein_studium_options['semester_fee']['link_url'] . '">'
                    . '</div>',
        ],
    ];

    $constants_out = [];
    $lang_alt = $lang == 'de' ? 'en' : 'de';
    foreach ($constants as $key => $translations) {
        if ( ! empty($translations[ $lang ])) { // return selected translation
            $constants_out[ $key ] = $translations[ $lang ];
            continue;
        }
        if ( ! empty($translations[ $lang_alt ])) { // return alternative language
            $constants_out[ $key ] = $translations[ $lang_alt ];
            continue;
        }
        $constants_out[ $key ] = $key;
    }

    return $constants_out;
}

function get_meinstudium_options($lang) {
    $options = [
        'notes_for_international_applicants' => [
            'name' => [
                'de' => 'Hinweise für internationale Bewerber',
                'en' => 'Tips for international applicants'
            ],
            'link_text' => [
                'de' => 'Hinweise für Internationals',
                'en' => 'Tips for internationals'
            ],
            'link_url' => [
                'de' => 'https://www.fau.de/education/international/aus-dem-ausland-an-die-fau/bewerbung-und-einschreibung-fuer-internationale-bewerberinnen-und-bewerber/',
                'en' => 'https://www.fau.eu/education/international/from-abroad/application-and-enrolment/'
            ],
        ],
        'admission_requirement' => [
            'link_text' => [
                'de' => 'Tipps zur Bewerbung',
                'en' => 'Tips for application',
            ]
        ],
        'start_of_semester' => [
            'name' => [
                'de' => 'Infos zum Semesterstart',
                'en' => 'Start of the semester',
            ],
            'link_text' => [
                'de' => 'Infos zum Semesterstart',
                'en' => 'Start of the semester',
            ],
            'link_url' => [
                'de' => 'https://www.fau.de/semesterstart/',
                'en' => 'https://www.fau.eu/start-of-the-semester/',
            ]
        ],
        'semester_dates' => [
            'name' => [
                'de' => 'Semestertermine',
                'en' => 'Semester dates'
            ],
            'link_text' => [
                'de' => 'Semestertermine',
                'en' => 'Semester dates'
            ],
            'link_url' => [
                'de' => 'https://www.fau.de/education/studienorganisation/semestertermine/',
                'en' => 'https://www.fau.eu/education/study-organisation/semester-dates/'
            ],
        ],
        'semester_fee' => [
            'name' => [
                'de' => 'Semesterbeitrag',
                'en' => 'Semester fees'
            ],
            'link_text' => [
                'de' => 'Semesterbeitrag',
                'en' => 'Semester fees'
            ],
            'link_url' => [
                'de' => 'https://www.fau.de/education/studienorganisation/rueckmeldung/',
                'en' => 'https://www.fau.eu/education/study-organisation/re-registration/'
            ],
        ],
        'service_centers' => [
            'name' => [
                'de' => 'Beratungs- und Servicestellen',
                'en' => 'Advice and services'
            ],
            'link_text' => [
                'de' => 'Beratungs- und Servicestellen',
                'en' => 'Advice and services'
            ],
            'link_url' => [
                'de' => 'https://www.fau.de/education/beratungs-und-servicestellen/',
                'en' => 'https://www.fau.eu/education/advice-and-services/'
            ],
        ],
        'abroad_opportunities' => [
            'name' => [
                'de' => 'Wege ins Ausland',
                'en' => 'Going abroad'
            ],
            'link_text' => [
                'de' => 'Wege ins Ausland',
                'en' => 'Going abroad'
            ],
            'link_url' => [
                'de' => 'https://www.fau.de/education/international/wege-ins-ausland/',
                'en' => 'https://www.fau.eu/education/international/going-abroad/'
            ],
        ],
        'student_initiatives' => [
            'name' => [
                'de' => 'Studierendenvertretung der FAU',
                'en' => 'Student Representatives at FAU'
            ],
            'link_text' => [
                'de' => 'Studierendenvertretung der FAU',
                'en' => 'Student Representatives at FAU'
            ],
            'link_url' => [
                'de' => 'https://stuve.fau.de/',
                'en' => 'https://stuve.fau.de/en/'
            ],
        ],
        'student_advice' => [
            'name' => [
                'de' => 'Zentrale Studienberatung',
                'en' => 'Student Advice Center'
            ],
            'link_text' => [
                'de' => 'Zentrale Studienberatung',
                'en' => 'Student Advice Center'
            ],
            'link_url' => [
                'de' => 'https://ibz.fau.de',
                'en' => 'https://ibz.fau.eu'
            ],
        ],
    ];

    $options_out = [];
    $lang_alt = $lang == 'de' ? 'en' : 'de';
    foreach ($options as $key => $items) {
        foreach ($items as $item => $translations) {
            $options_out[$key][$item] = $translations[$lang] ?? $translations[$lang_alt] ?? '';
        }
    }

    return $options_out;
}