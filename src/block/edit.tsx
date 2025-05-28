import { __ } from '@wordpress/i18n';
import {
    useBlockProps,
    InspectorControls,
} from '@wordpress/block-editor';
import {
    PanelBody,
    RadioControl,
    ComboboxControl,
    Spinner, ToggleControl,
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
    language: string;
    format: string;
    showSearch : boolean;
    items: string[];
}

const Edit = ({
                  attributes,
                  setAttributes,
              }: BlockEditProps<BlockAttributes>) => {
    const blockProps = useBlockProps();
    const { degreeProgram, language, format = 'full', showSearch = false } = attributes;
    const [degreePrograms, setDegreePrograms] = useState(fauStudiumData.degreePrograms);
    const [selectedFormat, setSelectedFormat] = useState<string>(format);

    const onChangeFormat = (value: string) => {
        setSelectedFormat(value);
        setAttributes({ format: value });
    };

    const onChangeLanguage = (value: string) => {
        setAttributes({ language: value });
    };

    const onChangeDegreeProgram = (value: string) => {
        const numericValue = parseInt(value, 10);
        if (!isNaN(numericValue)) {
            setAttributes({ degreeProgram: numericValue });
        }
    };

    const onChangeShowSearch = (value: boolean) => {
        setAttributes({ showSearch: value });
    }

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

                    <ComboboxControl
                        label={__('Degree Program', 'fau-studium-display')}
                        value={degreeProgram.toString()}
                        options={degreePrograms}
                        onChange={onChangeDegreeProgram}
                    />

                    <ComboboxControl
                        label={__('Language', 'fau-studium-display')}
                        value={language}
                        options={[
                            { label: 'Deutsch', value: 'de' },
                            { label: 'Englisch', value: 'en' },
                        ]}
                        onChange={onChangeLanguage}
                    />

                </PanelBody>
            </InspectorControls>

            <ServerSideRender
                block="fau-studium/display"
                attributes={attributes}
            />
        </div>
    );
};

export default Edit;
