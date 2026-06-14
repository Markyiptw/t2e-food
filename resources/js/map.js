import L from "leaflet";
import "leaflet.markercluster";
import "leaflet.markercluster/dist/MarkerCluster.css";
import "leaflet.markercluster/dist/MarkerCluster.Default.css";

// Hand-drawn vintage pin (rust body, forest dot) that matches the warm
// parchment palette instead of Leaflet's clinical bright-blue default marker.
const vintagePinSvg = `
<svg xmlns="http://www.w3.org/2000/svg" width="28" height="40" viewBox="0 0 28 40">
    <path d="M14 0C6.27 0 0 6.13 0 13.7 0 23.98 14 40 14 40s14-16.02 14-26.3C28 6.13 21.73 0 14 0z" fill="#b32b22"/>
    <path d="M14 1.5C7.1 1.5 1.5 6.96 1.5 13.7c0 4.2 2.9 9.78 6.05 14.4A115 115 0 0 0 14 36.2a115 115 0 0 0 6.45-8.1c3.15-4.62 6.05-10.2 6.05-14.4C26.5 6.96 20.9 1.5 14 1.5z" fill="none" stroke="#fbf6e9" stroke-width="1.2" opacity="0.7"/>
    <circle cx="14" cy="13.5" r="5" fill="#0f6b54"/>
</svg>`;

const vintageIcon = L.divIcon({
    html: vintagePinSvg,
    className: "leaflet-vintage-pin",
    iconSize: [28, 40],
    iconAnchor: [14, 40],
    popupAnchor: [0, -36],
});

const mapContainer = document.getElementById("map");

if (mapContainer && window.restaurantMarkersEndpoint) {
    const map = L.map("map").setView([22.3193, 114.1694], 12);
    const initialLoadingOverlay = document.getElementById(
        "map-loading-overlay"
    );

    // CartoDB "Voyager" basemap — warm-toned tiles that sit comfortably
    // against the parchment/forest/rust palette of the rest of the site.
    L.tileLayer(
        "https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png",
        {
            attribution:
                '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: "abcd",
            maxZoom: 20,
        }
    ).addTo(map);

    const markers = L.markerClusterGroup({
        maxClusterRadius: 50,
    });
    const bounds = [];
    const markerCount = document.createElement("div");
    let count = 0;
    let fittedInitialBounds = false;

    map.addLayer(markers);

    markerCount.className =
        "fixed left-4 bottom-4 z-[1000] hidden rounded-full bg-[#0f6b54] px-3 py-1.5 text-xs font-medium text-[#f4ecd8] shadow-[3px_3px_0_rgba(15,107,84,0.25)]";
    markerCount.textContent = "Loading restaurants...";
    document.body.appendChild(markerCount);

    loadMarkers(window.restaurantMarkersEndpoint, true);

    async function loadMarkers(url, isInitialPage = false) {
        if (!url) {
            hideInitialLoadingOverlay();
            updateMarkerCount(false);
            return;
        }

        const response = await fetch(url, {
            headers: {
                Accept: "application/json",
            },
        });

        if (!response.ok) {
            showInitialLoadingError();
            markerCount.textContent = "Unable to load restaurants";
            return;
        }

        const page = await response.json();

        addMarkers(page.markers);

        if (isInitialPage) {
            hideInitialLoadingOverlay();
            markerCount.classList.remove("hidden");
        }

        updateMarkerCount(page.has_more);

        if (page.next_page_url) {
            await loadMarkers(page.next_page_url);
        }
    }

    function addMarkers(restaurants) {
        restaurants.forEach((r) => {
            const latLng = [r.latitude, r.longitude];

            bounds.push(latLng);
            count += 1;

            markers.addLayer(
                L.marker(latLng, { icon: vintageIcon }).bindPopup(
                    `<strong>${escapeHtml(r.name)}</strong><br>${escapeHtml(r.address)}` +
                        (r.url ? `<br><a href="${escapeHtml(r.url)}" target="_blank" rel="noopener noreferrer">More info</a>` : '')
                )
            );
        });

        if (!fittedInitialBounds && bounds.length > 1) {
            fittedInitialBounds = true;
            map.fitBounds(bounds, { padding: [50, 50], maxZoom: 14 });
        }
    }

    function updateMarkerCount(isLoading) {
        markerCount.textContent = `${count.toLocaleString()} restaurant${count !== 1 ? "s" : ""}${isLoading ? " loaded..." : ""}`;
    }

    function hideInitialLoadingOverlay() {
        if (!initialLoadingOverlay) {
            return;
        }

        initialLoadingOverlay.classList.add(
            "pointer-events-none",
            "opacity-0",
            "transition-opacity",
            "duration-300"
        );
    }

    function showInitialLoadingError() {
        if (!initialLoadingOverlay) {
            return;
        }

        initialLoadingOverlay.textContent = "Unable to load restaurants.";
    }

    const filterForm = document.querySelector("form[action='/map']");

    if (filterForm) {
        filterForm.addEventListener("submit", () => {
            markerCount.textContent = "Loading...";
        });
    }
}

function escapeHtml(str) {
    const div = document.createElement("div");

    div.textContent = str;

    return div.innerHTML;
}
