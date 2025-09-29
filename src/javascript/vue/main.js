import { createApp, h } from "vue";
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
 * Render components by looking the DOM for their root element
 * Look for id="component-name" and render the component corresponding to the name
 */
async function renderComponent(componentId, loadComponent) {
    
  const element = document.getElementById(componentId);

  if (element) {
    // load component
    const data = element.dataset;
    const { default: Component } = await loadComponent();

    const app = createApp({
        render: () => h(Component, { data }),
    });
    app.mount(element);
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
