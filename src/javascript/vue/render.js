import { createApp, h } from "vue";

/**
 * Mount a component in its root element, passing the element's data-* attributes as `data`
 */
export async function renderComponent(element, loadComponent) {
  const data = element.dataset;
  const { default: Component } = await loadComponent();

  createApp({
    render: () => h(Component, { data }),
  }).mount(element);
}
