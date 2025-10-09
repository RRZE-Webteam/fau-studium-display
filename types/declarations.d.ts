declare module '@wordpress/server-side-render' {
    const ServerSideRender: React.ComponentType<any>;
    export default ServerSideRender;
}

declare const fauStudiumData: {
    degreePrograms: {
        label: string;
        value: string;
    }[];
    itemsFullOptions: {
        label: string;
        value: string;
    }[];
    itemsGridOptions: {
        label: string;
        value: string;
    }[];
    facultiesOptions: {
        label: string;
        value: string;
    }[];
    degreesOptions: {
        label: string;
        value: string;
    }[];
    specialWaysOptions: {
        label: string;
        value: string;
    }[];
    searchFilters: {
        label: string;
        value: string;
    }[];
};

declare module '*.json' {
    const value: any;
    export default value;
}