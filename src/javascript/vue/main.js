/**
 * Import components dynamically
 * This allows us to only load the components we need on the page
 *
 * @example
 * "id-of-component": () => import("./path/to/component.vue")"
 */

const componentImports = {
  // "demo-vue-toolkit": () => import("./components/Demo.vue"),
  // "map": () => import("./components/Mapbox.vue"),
};

/**
 * Look for id="component-name" in the page. Vue itself is only downloaded
 * when at least one root element exists, so pages without components stay light.
 */
const roots = Object.entries(componentImports).filter(([id]) =>
  document.getElementById(id),
);

if (roots.length) {
  import("./render.js").then(({ renderComponent }) => {
    roots.forEach(([id, loadComponent]) => {
      renderComponent(document.getElementById(id), loadComponent);
    });
  });
}
