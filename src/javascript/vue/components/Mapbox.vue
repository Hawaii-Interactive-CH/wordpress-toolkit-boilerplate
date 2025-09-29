<script setup>
import 'mapbox-gl/dist/mapbox-gl.css';
import mapboxgl from 'mapbox-gl';
import { ref, watch, onMounted } from 'vue';

const DEFAULT_COORDINATES = [8.222665776, 46.800663464];

const props = defineProps({
  data : {
    type: Object,
    required: true,
  },
});

const map = ref(null);
const params = ref(JSON.parse(props.data.params));
const coordinates = ref(params.value.location || DEFAULT_COORDINATES);

mapboxgl.accessToken = import.meta.env.VITE_APP_MAPBOX_TOKEN;

// Function to fetch coordinates from an address
const fetchCoordinates = async (address) => {
  const response = await fetch(`https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(address)}.json?access_token=${mapboxgl.accessToken}`).then((response) => response.json());
  if (response.features && response.features.length > 0) {
    return response.features[0].center;
  }
  return params.value.location || DEFAULT_COORDINATES;
};

// Function to fetch coordinates for all markers
const fetchAllCoordinates = async (markers) => {
  const markerPromises = markers.map(async (marker) => {
    const coords = await fetchCoordinates(marker.location);
    return {
      ...marker,
      coordinates: coords,
    };
  });

  return Promise.all(markerPromises);
};

// Watch the address prop and fetch new coordinates when it changes
watch(() => props.data, async (newAddress) => {
  if (params.value.type === 'single') {
    coordinates.value = await fetchCoordinates(newAddress);
    if (map.value) {
      map.value.setCenter(coordinates.value);
    }
  } else if (params.value.type === 'multiple') {
    coordinates.value = params.value.location || DEFAULT_COORDINATES;

    // Fetch coordinates for each marker
    params.value.markers = await fetchAllCoordinates(params.value.markers);
  }
}, { immediate: true });

onMounted(async () => {
  if (params.value.type === 'single') {
    coordinates.value = await fetchCoordinates(location);
  } else if (params.value.type === 'multiple') {
    coordinates.value = params.value.location || DEFAULT_COORDINATES;

    // Fetch coordinates for each marker
    params.value.markers = await fetchAllCoordinates(params.value.markers);
  }

  map.value = new mapboxgl.Map({
    container: params.value.container,
    center: coordinates.value,
    zoom: params.value.zoom,
    minZoom: params.value.minZoom,
    style: params.value.style,
  });

  // Add markers to the map
  if (params.value.markers) {
    if (params.value.type === 'single') {
      new mapboxgl.Marker({
        color: params.value.markers[0].color,
      })
        .setLngLat(coordinates.value)
        .setPopup(new mapboxgl.Popup().setHTML(`
            <h3>${params.value.markers[0].title}</h3>
            <a href="${params.value.markers[0].link}">Voir le lieu</a>
          `
        ))
        .addTo(map.value);
    } else if (params.value.type === 'multiple') {
      params.value.markers.forEach((marker) => {
        new mapboxgl.Marker({
          color: marker.color,
        })
          .setLngLat(marker.coordinates)
          .setPopup(new mapboxgl.Popup({
            className: 'mapbox-popup',
          }).setHTML(`
              <h3>${marker.title}</h3>
              <a href="${marker.link}">Voir le lieu</a>
            `
          ))
          .addTo(map.value);
      });
    }
  }

  // Resize map on display none block
  const observer = new ResizeObserver(() => {
    map.value.resize();
  });

  observer.observe(document.getElementById(params.value.container));
});

</script>

<template>
  <div :id="params.container" :class="['map', params.containerClass]"></div>
</template>

<style scoped lang="scss">
</style>
