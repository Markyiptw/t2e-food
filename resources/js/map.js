import L from "leaflet";
import "leaflet.markercluster";
import "leaflet.markercluster/dist/MarkerCluster.css";
import "leaflet.markercluster/dist/MarkerCluster.Default.css";
import markerIcon2x from "leaflet/dist/images/marker-icon-2x.png";
import markerIcon from "leaflet/dist/images/marker-icon.png";
import markerShadow from "leaflet/dist/images/marker-shadow.png";
import Alpine from "alpinejs";

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: markerIcon2x,
    iconUrl: markerIcon,
    shadowUrl: markerShadow,
});

window.hideFilterForm = (filterForm, filterExpand) => {
    filterForm.classList.add("-translate-y-full");
    filterExpand.classList.remove("hidden");
};

window.showFilterForm = (filterForm, filterExpand) => {
    filterForm.classList.remove("-translate-y-full");
    filterExpand.classList.add("hidden");
};

document.addEventListener("alpine:init", () => {
    Alpine.data("map", () => {
        let map = null;
        let markerGroup = null;
        let bounds = [];
        let fittedInitialBounds = false;

        return {
            count: 0,
            status: "loading",

            get loaded() {
                return this.status === "success";
            },

            get loading() {
                return this.status === "loading";
            },

            get error() {
                return this.status === "error";
            },

            get started() {
                return this.count > 0;
            },

            get label() {
                if (this.error) {
                    return "Unable to load restaurants";
                }

                const label = `${this.count.toLocaleString()} restaurant${this.count !== 1 ? "s" : ""}`;

                return this.loading ? `${label} loaded...` : label;
            },

            setState(next) {
                const transitions = {
                    loading: ["success", "error"],
                };

                if (transitions[this.status]?.includes(next)) {
                    this.status = next;
                }
            },

            init() {
                if (!this.$refs.map || !window.restaurantMarkersEndpoint) {
                    return;
                }

                this.initializeMap();
                this.loadMarkers(window.restaurantMarkersEndpoint);
            },

            initializeMap() {
                if (map) {
                    return;
                }

                map = L.map(this.$refs.map, {
                    attributionControl: false,
                }).setView([22.3193, 114.1694], 12);

                L.tileLayer(
                    "https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png",
                    {
                        attribution:
                            '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                        subdomains: "abcd",
                        maxZoom: 20,
                    },
                ).addTo(map);

                markerGroup = L.markerClusterGroup({
                    maxClusterRadius: 50,
                });

                map.addLayer(markerGroup);
            },

            async loadMarkers(url) {
                if (!url) {
                    return;
                }

                try {
                    const response = await fetch(url, {
                        headers: {
                            Accept: "application/json",
                        },
                    });

                    if (!response.ok) {
                        this.setState("error");

                        return;
                    }

                    const page = await response.json();

                    this.addMarkers(page.markers);

                    if (page.next_page_url) {
                        await this.loadMarkers(page.next_page_url);
                    } else {
                        this.setState("success");
                    }
                } catch {
                    this.setState("error");
                }
            },

            addMarkers(restaurants) {
                let added = 0;

                restaurants.forEach((r) => {
                    const latLng = [r.latitude, r.longitude];

                    bounds.push(latLng);
                    added += 1;

                    const categories = r.categories?.length
                        ? `<br>${escapeHtml(r.categories.join(", "))}`
                        : "";
                    const googleMapsUrl = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(r.name + " " + (r.address || ""))}`;
                    const links = [];

                    if (r.url) {
                        links.push(
                            `<a href="${escapeHtml(r.url)}" target="_blank" rel="noopener noreferrer">Open Rice Page</a>`,
                        );
                    }

                    links.push(
                        `<a href="${escapeHtml(googleMapsUrl)}" target="_blank" rel="noopener noreferrer">Google Maps</a>`,
                    );

                    markerGroup.addLayer(
                        L.marker(latLng).bindPopup(
                            `<strong>${escapeHtml(r.name)}</strong><br>${escapeHtml(r.address)}${categories}<br>${links.join(" | ")}`,
                        ),
                    );
                });

                this.count += added;

                if (!fittedInitialBounds && bounds.length > 1) {
                    fittedInitialBounds = true;
                    map.fitBounds(bounds, { padding: [50, 50], maxZoom: 14 });
                }
            },
        };
    });
});

function escapeHtml(str) {
    const div = document.createElement("div");

    div.textContent = str;

    return div.innerHTML;
}
