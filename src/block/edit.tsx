import { __ } from '@wordpress/i18n';
import {
    useBlockProps,
    InspectorControls,
} from '@wordpress/block-editor';
import {
    PanelBody,
    ComboboxControl,
    ToggleControl, CheckboxControl,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { useState, useEffect } from '@wordpress/element';
import type { BlockEditProps } from '@wordpress/blocks';
import './editor.scss';

interface DegreeProgramOption {
    label: string;
    value: string;
}

interface BlockAttributes {
    degreeProgram: number;
    selectedFaculties: string[];
    selectedDegrees: string[];
    selectedSpecialWays: string[];
    selectedDegreePrograms: string[];
    language: string;
    format: string;
    showSearch : boolean;
    showTitle : boolean;
    selectedItemsGrid: string[];
    selectedItemsFull: string[];
    selectedSearchFilters: string[];
}

const Edit = ({
                  attributes,
                  setAttributes,
              }: BlockEditProps<BlockAttributes>) => {
    const blockProps = useBlockProps();
    const { degreeProgram, language, format = 'full', showSearch, showTitle = true } = attributes;
    const [itemsGrid, setItemsGrid] = useState(() => fauStudiumData?.itemsGridOptions ?? []);
    const [itemsFull, setItemsFull] = useState(() => fauStudiumData?.itemsFullOptions ?? []);
    const [searchFilters, setSearchFilters] = useState(() => fauStudiumData?.searchFilters ?? []);
    const [faculties] = useState(() => fauStudiumData?.facultiesOptions ?? []);
    const [degrees] = useState(() => fauStudiumData?.degreesOptions ?? []);
    const [specialWays] = useState(() => fauStudiumData?.specialWaysOptions ?? []);
    const [degreePrograms] = useState(() => fauStudiumData?.degreePrograms ?? []);
    const {selectedItemsGrid = ["teaser_image", "title", "subtitle", "degree", "start", "admission_requirements", "area_of_study"]} = attributes;
    const {selectedItemsFull = ["teaser_image", "title", "subtitle", "entry_text", "fact_sheet", "content.about", "content.structure", "content.specializations", "content.qualities_and_skills", "content.why_should_study", "content.career_prospects", "content.special_features", "combinations", "videos", "info_internationals_link", "admission_requirements_application", "apply_now_link", "student_advice", "subject_specific_advice", "links.organizational", "links.downloads", "links.additional_information", "benefits"]} = attributes;
    const {selectedSearchFilters = ["admission-requirement", "attribute", "degree", "german-language-skills-for-international-students", "faculty", "semester", "study-location", "subject-group", "teaching-language"]} = attributes;
    const {selectedFaculties = [], selectedDegrees = [], selectedSpecialWays = [], selectedDegreePrograms = []} = attributes;
    const [selectedFormat, setSelectedFormat] = useState<string>(format);

    const onChangeFormat = (value: string | null | undefined) => {
        if (typeof value === 'string') {
            setSelectedFormat(value);
            setAttributes({ format: value });
        }
    };

    const onChangeLanguage = (value: string | null | undefined) => {
        if (typeof value === 'string') {
            setAttributes({ language: value });
        }
    };

    const onChangeDegreeProgram = (value: string | null | undefined) => {
        if (typeof value === 'string') {
            const numericValue = parseInt(value, 10);
            if (!isNaN(numericValue)) {
                setAttributes({ degreeProgram: numericValue });
            }
        }
    };

    const onChangeShowSearch = (value: boolean) => {
        setAttributes({ showSearch: value });
    }

    const onChangeShowTitle = (value: boolean) => {
        setAttributes({ showTitle: value });
    }

    const toggleItemGrid = (value: string) => {
        const updated = selectedItemsGrid.includes(value)
            ? selectedItemsGrid.filter((v: string) => v !== value)
            : [...selectedItemsGrid, value];

        setAttributes({ selectedItemsGrid: [...updated] });

    };

    const toggleItemFull = (value: string) => {
        const updated = selectedItemsFull.includes(value)
            ? selectedItemsFull.filter((v: string) => v !== value)
            : [...selectedItemsFull, value];

        setAttributes({ selectedItemsFull: [...updated] });

    };

    const toggleSearchItem = (value: string) => {
        const updated = selectedSearchFilters.includes(value)
            ? selectedSearchFilters.filter((v: string) => v !== value)
            : [...selectedSearchFilters, value];

        //setAttributes({ selectedSearchFilters: [...updated] });
        setAttributes({ selectedSearchFilters: updated.length > 0 ? updated : [] });
    };

    const toggleFaculties = (value: string) => {
        const updated = selectedFaculties.includes(value)
            ? selectedFaculties.filter((v: string) => v !== value)
            : [...selectedFaculties, value];

        setAttributes({ selectedFaculties: [...updated] });

    };

    const toggleDegrees = (value: string) => {
        const updated = selectedDegrees.includes(value)
            ? selectedDegrees.filter((v: string) => v !== value)
            : [...selectedDegrees, value];

        setAttributes({ selectedDegrees: [...updated] });

    };

    const toggleSpecialWays = (value: string) => {
        const updated = selectedSpecialWays.includes(value)
            ? selectedSpecialWays.filter((v: string) => v !== value)
            : [...selectedSpecialWays, value];

        setAttributes({ selectedSpecialWays: [...updated] });

    };

    const toggleDegreePrograms = (value: string) => {
        const updated = selectedDegreePrograms.includes(value)
            ? selectedDegreePrograms.filter((v: string) => v !== value)
            : [...selectedDegreePrograms, value];

        setAttributes({ selectedDegreePrograms: [...updated] });

    };

    return (
        <div {...blockProps}>
            <InspectorControls>
                <PanelBody title={__('General Settings', 'fau-studium-display')} initialOpen={true}>
                    <ComboboxControl
                        label={__('Format', 'fau-studium-display')}
                        value={selectedFormat.toString()}
                        options={[
                            { label: __('Full', 'fau-studium-display'), value: 'full' }, // Kompletter Studiengang
                            { label: __('Infobox', 'fau-studium-display'), value: 'box'},
                            { label: __('Grid', 'fau-studium-display'), value: 'grid'},
                            { label: __('Table', 'fau-studium-display'), value: 'table'},
                            { label: __('List', 'fau-studium-display'), value: 'list' }, // Früher 'short'

                        ]}
                        onChange={onChangeFormat}
                    />

                    {(selectedFormat === "grid"
                        || selectedFormat === "table") && (
                        <ToggleControl
                            label={__('Show Search', 'fau-studium-display')}
                            checked={!!showSearch}
                            onChange={onChangeShowSearch}
                        />
                    )}

                    {showSearch &&
                        (selectedFormat === "grid"
                        || selectedFormat === "table") && (
                        <>
                        <h3>{__('Search Filters', 'fau-studium-display')}</h3>
                        {searchFilters.map((item: { label: string; value: string }) => (
                                <CheckboxControl
                                    key={item.value}
                                    label={item.label}
                                    checked={selectedSearchFilters.includes(item.value)}
                                    onChange={() => toggleSearchItem(item.value)}
                                />
                            ))}
                        </>
                    )}

                    {(selectedFormat === "full"
                        || selectedFormat === "box") && (
                        <>
                        <ComboboxControl
                            label={__('Degree Program', 'fau-studium-display')}
                            value={degreeProgram?.toString?.() ?? ''}
                            options={degreePrograms ?? []}
                            onChange={onChangeDegreeProgram}
                        />
                        </>
                    )}

                    {(selectedFormat === "box") && (
                        <ToggleControl
                            label={__('Show Title', 'fau-studium-display')}
                            value={degreeProgram?.toString?.() ?? ''}
                            checked={!!showTitle}
                            onChange={onChangeShowTitle}
                        />
                    )}

                    <ComboboxControl
                        label={__('Language', 'fau-studium-display')}
                        value={language}
                        options={[
                            { label: __('Default', 'fau-studium-display'), value: '' },
                            { label: __('German', 'fau-studium-display'), value: 'de' },
                            { label: __('English', 'fau-studium-display'), value: 'en' },
                        ]}
                        onChange={onChangeLanguage}
                    />

                </PanelBody>

                {(selectedFormat === "grid") && (
                    <PanelBody title={__('Select items', 'fau-studium-display')} initialOpen={true}>
                        {itemsGrid.map((item: { label: string; value: string }) => (
                            <CheckboxControl
                                key={item.value}
                                label={item.label}
                                checked={selectedItemsGrid.includes(item.value)}
                                onChange={() => toggleItemGrid(item.value)}
                            />
                        ))}
                    </PanelBody>
                )}

                {(selectedFormat === "full") && (
                    <PanelBody title={__('Select items', 'fau-studium-display')} initialOpen={true}>
                        {itemsFull.map((item: { label: string; value: string }) => (
                            <CheckboxControl
                                key={item.value}
                                label={item.label}
                                checked={selectedItemsFull.includes(item.value)}
                                onChange={() => toggleItemFull(item.value)}
                            />
                        ))}
                    </PanelBody>
                )}

                {(selectedFormat === "grid"
                    || selectedFormat === "table"
                    || selectedFormat === "list") && (

                    <PanelBody title={__('Filter Programs', 'fau-studium-display')} initialOpen={false}>
                        <div key="facultiesWrapper">
                        <h3>{__('Faculties', 'fau-studium-display')}</h3>
                        {faculties.map((item: { label: string; value: string }) => (
                            <CheckboxControl
                                key={item.value}
                                label={item.label}
                                checked={selectedFaculties.includes(item.value)}
                                onChange={() => toggleFaculties(item.value)}
                            />
                        ))}
                        </div>

                        <hr />

                        <div key="degreesWrapper">
                        <h3>{__('Degrees', 'fau-studium-display')}</h3>
                        {degrees.map((item: { label: string; value: string }) => (
                            <CheckboxControl
                                key={item.value}
                                label={item.label}
                                checked={selectedDegrees.includes(item.value)}
                                onChange={() => toggleDegrees(item.value)}
                            />
                        ))}
                        </div>

                        <hr />

                        <div key="specialWaysWrapper">
                        <h3>{__('Special ways to study', 'fau-studium-display')}</h3>
                        {specialWays.map((item: { label: string; value: string }) => (
                            <CheckboxControl
                                key={item.value}
                                label={item.label}
                                checked={selectedSpecialWays.includes(item.value)}
                                onChange={() => toggleSpecialWays(item.value)}
                            />
                        ))}
                        </div>
                        <hr />

                        <div key="degreeProgramsWrapper">
                        <h3>{__('Degree Programs', 'fau-studium-display')}</h3>
                            {degreePrograms.map((item: { label: string; value: string }) => (
                                <CheckboxControl
                                    key={item.value}
                                    label={item.label}
                                    checked={selectedDegreePrograms.includes(item.value)}
                                    onChange={() => toggleDegreePrograms(item.value)}
                                />
                            ))}
                        </div>
                    </PanelBody>

                )}

            </InspectorControls>

            <ServerSideRender
                block="fau-studium/display"
                attributes={attributes}
            />
        </div>
    );
};

export default Edit;
