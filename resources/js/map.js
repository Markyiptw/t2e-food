import L from "leaflet";
import "leaflet.markercluster";
import "leaflet.markercluster/dist/MarkerCluster.css";
import "leaflet.markercluster/dist/MarkerCluster.Default.css";

const mapContainer = document.getElementById("map");

if (mapContainer && window.restaurantMarkersEndpoint) {
    const map = L.map("map").setView([22.3193, 114.1694], 12);

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
        "fixed left-4 bottom-4 z-[1000] rounded bg-slate-800 px-3 py-1.5 text-xs font-medium text-white shadow";
    markerCount.textContent = "Loading restaurants...";
    document.body.appendChild(markerCount);

    loadMarkers(window.restaurantMarkersEndpoint, true);

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

            markers.addLayer(
                L.marker(latLng).bindPopup(
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
}

function escapeHtml(str) {
    const div = document.createElement("div");

    div.textContent = str;

    return div.innerHTML;
}
