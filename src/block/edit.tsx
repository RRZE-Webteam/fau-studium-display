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
    language: string;
    format: string;
    showSearch : boolean;
    showTitle : boolean;
    selectedItemsGrid: string[];
    selectedItemsFull: string[];
}

const Edit = ({
                  attributes,
                  setAttributes,
              }: BlockEditProps<BlockAttributes>) => {
    const blockProps = useBlockProps();
    const { degreeProgram, language, format = 'full', showSearch, showTitle = true } = attributes;
    const [degreePrograms, setDegreePrograms] = useState(() => fauStudiumData?.degreePrograms ?? []);
    const [itemsGrid, setItemsGrid] = useState(() => fauStudiumData?.itemsGridOptions ?? []);
    const [itemsFull, setItemsFull] = useState(() => fauStudiumData?.itemsFullOptions ?? []);
    const [faculties] = useState(() => fauStudiumData?.facultiesOptions ?? []);
    const [degrees] = useState(() => fauStudiumData?.degreesOptions ?? []);
    const [specialWays] = useState(() => fauStudiumData?.specialWaysOptions ?? []);
    const {selectedItemsGrid = ["teaser_image", "title", "subtitle", "degree", "start", "admission_requirements", "area_of_study"]} = attributes;
    const {selectedItemsFull = ["teaser_image", "title", "subtitle", "degree", "start", "admission_requirements", "area_of_study"]} = attributes;
    const {selectedFaculties = [], selectedDegrees = [], selectedSpecialWays = []} = attributes;
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
                        || selectedFormat === "table"
                        || selectedFormat === "list") && (
                        <ToggleControl
                            label={__('Show Search', 'fau-studium-display')}
                            checked={!!showSearch}
                            onChange={onChangeShowSearch}
                        />
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
                            { label: __('German', 'fau-studium-display'), value: 'de' },
                            { label: __('English', 'fau-studium-display'), value: 'en' },
                        ]}
                        onChange={onChangeLanguage}
                    />

                </PanelBody>

                <PanelBody title={__('Filter Programs', 'fau-studium-display')} initialOpen={true}>
                    <h3>{__('Faculties', 'fau-studium-display')}</h3>
                    {faculties.map((item: { label: string; value: string }) => (
                        <CheckboxControl
                            key={item.value}
                            label={item.label}
                            checked={selectedFaculties.includes(item.value)}
                            onChange={() => toggleFaculties(item.value)}
                        />
                    ))}

                    <hr />

                    <h3>{__('Degrees', 'fau-studium-display')}</h3>
                    {degrees.map((item: { label: string; value: string }) => (
                        <CheckboxControl
                            key={item.value}
                            label={item.label}
                            checked={selectedDegrees.includes(item.value)}
                            onChange={() => toggleDegrees(item.value)}
                        />
                    ))}

                    <hr />

                    <h3>{__('Special ways to study', 'fau-studium-display')}</h3>
                    {specialWays.map((item: { label: string; value: string }) => (
                        <CheckboxControl
                            key={item.value}
                            label={item.label}
                            checked={selectedSpecialWays.includes(item.value)}
                            onChange={() => toggleSpecialWays(item.value)}
                        />
                    ))}
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

            </InspectorControls>

            <ServerSideRender
                block="fau-studium/display"
                attributes={attributes}
            />
        </div>
    );
};

export default Edit;
