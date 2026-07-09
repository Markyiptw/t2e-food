import L from "leaflet";
import "leaflet.markercluster";
import "leaflet.markercluster/dist/MarkerCluster.css";
import "leaflet.markercluster/dist/MarkerCluster.Default.css";

const mapContainer = document.getElementById("map");

if (mapContainer && window.restaurantMarkersEndpoint) {
    const map = L.map("map", { attributionControl: false }).setView([22.3193, 114.1694], 12);

    L.tileLayer(
        "https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png",
        {
            attribution:
                '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: "abcd",
            maxZoom: 20,
        }
    ).addTo(map);

    const filterForm = document.getElementById("filter-form");
    const filterExpand = document.getElementById("filter-expand");
    const filterCollapse = document.getElementById("filter-collapse");

    function hideFilterForm() {
        filterForm.classList.add("-translate-y-full");
        filterExpand.classList.remove("hidden");
    }

    function showFilterForm() {
        filterForm.classList.remove("-translate-y-full");
        filterExpand.classList.add("hidden");
    }

    filterCollapse.addEventListener("click", hideFilterForm);
    filterExpand.addEventListener("click", showFilterForm);

    const markers = L.markerClusterGroup({
        maxClusterRadius: 50,
    });
    const bounds = [];
    const markerCount = document.getElementById("marker-count");
    let count = 0;
    let fittedInitialBounds = false;

    map.addLayer(markers);

    loadMarkers(window.restaurantMarkersEndpoint, true);

    mapContainer.addEventListener("touchstart", hideFilterForm);
    mapContainer.addEventListener("mousedown", hideFilterForm);

    async function loadMarkers(url, isInitialPage = false) {
        if (!url) {
            updateMarkerCount(false);
            return;
        }

        try {
            const response = await fetch(url, {
                headers: {
                    Accept: "application/json",
                },
            });

            if (!response.ok) {
                markerCount.textContent = "Unable to load restaurants";
                return;
            }

            const page = await response.json();

            addMarkers(page.markers);

            if (isInitialPage) {
                markerCount.classList.remove("hidden");
            }

            updateMarkerCount(page.has_more);

            if (page.next_page_url) {
                await loadMarkers(page.next_page_url);
            }
        } catch {
            markerCount.textContent = "Unable to load restaurants";
        }
    }

    function addMarkers(restaurants) {
        restaurants.forEach((r) => {
            const latLng = [r.latitude, r.longitude];

            bounds.push(latLng);
            count += 1;

            const categories = r.categories?.length ? `<br>${escapeHtml(r.categories.join(", "))}` : "";
            const googleMapsUrl = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(r.name + " " + (r.address || ""))}`;
            const links = [];

            if (r.url) {
                links.push(`<a href="${escapeHtml(r.url)}" target="_blank" rel="noopener noreferrer">Open Rice Page</a>`);
            }

            links.push(`<a href="${escapeHtml(googleMapsUrl)}" target="_blank" rel="noopener noreferrer">Google Maps</a>`);

            const marker = L.marker(latLng).bindPopup(
                `<strong>${escapeHtml(r.name)}</strong><br>${escapeHtml(r.address)}${categories}<br>${links.join(" | ")}`
            );

            markers.addLayer(marker);
        });

        if (!fittedInitialBounds && bounds.length > 1) {
            fittedInitialBounds = true;
            map.fitBounds(bounds, { padding: [50, 50], maxZoom: 14 });
        }
    }

    function updateMarkerCount(isLoading) {
        markerCount.textContent = `${count.toLocaleString()} restaurant${count !== 1 ? "s" : ""}${isLoading ? " loaded..." : ""}`;
    }
}

function escapeHtml(str) {
    const div = document.createElement("div");

    div.textContent = str;

    return div.innerHTML;
}
