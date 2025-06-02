declare module '@wordpress/server-side-render' {
    const ServerSideRender: React.ComponentType<any>;
    export default ServerSideRender;
}

declare const fauStudiumData: {
    degreePrograms: {
        label: string;
        value: string;
    }[];
};

declare module '*.json' {
    const value: any;
    export default value;
}