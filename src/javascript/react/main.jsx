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
 * Look for id="component-name" in the page. React itself is only downloaded
 * when at least one root element exists, so pages without components stay light.
 */
const roots = Object.entries(componentImports).filter(([id]) =>
    document.getElementById(id),
);

if (roots.length) {
    import("./render.jsx").then(({ renderComponent }) => {
        roots.forEach(([id, loadComponent]) => {
            renderComponent(document.getElementById(id), loadComponent);
        });
    });
}
