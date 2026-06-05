import L from "leaflet";
import "leaflet.markercluster";
import "leaflet.markercluster/dist/MarkerCluster.css";
import "leaflet.markercluster/dist/MarkerCluster.Default.css";
import markerIconUrl from "leaflet/dist/images/marker-icon.png";
import markerIconRetinaUrl from "leaflet/dist/images/marker-icon-2x.png";
import markerShadowUrl from "leaflet/dist/images/marker-shadow.png";

delete L.Icon.Default.prototype._getIconUrl;

L.Icon.Default.mergeOptions({
    iconRetinaUrl: markerIconRetinaUrl,
    iconUrl: markerIconUrl,
    shadowUrl: markerShadowUrl,
});

const mapContainer = document.getElementById("map");

if (mapContainer && window.restaurantMarkers) {
    const map = L.map("map").setView([22.3193, 114.1694], 12);

    L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution:
            '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19,
    }).addTo(map);

    const count = window.restaurantMarkers.length;

    if (count > 0) {
        const markers = L.markerClusterGroup({
            maxClusterRadius: 50,
        });
        const bounds = [];

        window.restaurantMarkers.forEach((r) => {
            const latLng = [r.latitude, r.longitude];

            bounds.push(latLng);

            markers.addLayer(
                L.marker(latLng).bindPopup(
                    `<strong>${escapeHtml(r.name)}</strong><br>${escapeHtml(r.address)}` +
                        (r.url ? `<br><a href="${escapeHtml(r.url)}" target="_blank" rel="noopener noreferrer">More info</a>` : '')
                )
            );
        });

        map.addLayer(markers);

        if (bounds.length > 1) {
            map.fitBounds(bounds, { padding: [50, 50], maxZoom: 14 });
        }
    }

    const filterForm = document.querySelector("form[action='/map']");
    const markerCount = document.createElement("div");

    markerCount.className =
        "fixed left-4 bottom-4 z-[1000] rounded-full bg-[#1f2a24]/80 px-3 py-1.5 text-xs font-medium text-white backdrop-blur-sm";
    markerCount.textContent = `${count.toLocaleString()} restaurant${count !== 1 ? "s" : ""}`;
    document.body.appendChild(markerCount);

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
