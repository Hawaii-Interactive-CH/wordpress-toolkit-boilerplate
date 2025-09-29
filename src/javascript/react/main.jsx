import React from "react";
import ReactDOM from "react-dom/client";


/**
 * Import components dynamically
 * This allows us to only load the components we need on the page
 * 
 * @example
 * "id-of-component": () => import("./path/to/component.jsx"),
 */

const componentImports = {
    "demo-react-toolkit": () => import("./components/demo.jsx"),
};

/**
 * Render components by looking the DOM for their root element
 * Look for id="component-name" and render the component corresponding to the name
 */
async function renderComponent(componentId, loadComponent) {
    const element = document.getElementById(componentId);

    if (element) {
        // load component & data
        const data = element.dataset;
        const { default: Component } = await loadComponent();

        // render component
        ReactDOM.createRoot(element).render(
            <React.StrictMode>
                <Component data={data} />
            </React.StrictMode>,
        );
    }
}

/**
 * Iterate over the component IDs and render them if their root element exists
 * This allows us to only load the components we need on the page
 */
Object.entries(componentImports).forEach(([id, loadComponent]) => {
    if (document.getElementById(id)) {
        // console.info(`Rendering ${id} component`);
        renderComponent(id, loadComponent);
    }
});
