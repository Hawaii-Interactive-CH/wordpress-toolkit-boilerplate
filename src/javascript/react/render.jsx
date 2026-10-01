import React from "react";
import ReactDOM from "react-dom/client";

/**
 * Render a component in its root element, passing the element's data-* attributes as `data`
 */
export async function renderComponent(element, loadComponent) {
    const data = element.dataset;
    const { default: Component } = await loadComponent();

    ReactDOM.createRoot(element).render(
        <React.StrictMode>
            <Component data={data} />
        </React.StrictMode>,
    );
}
