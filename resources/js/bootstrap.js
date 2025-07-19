import axios from "axios";
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

import htmx from "htmx.org";
window.htmx = htmx;

import "htmx-ext-response-targets";

import Alpine from "alpinejs";

window.Alpine = Alpine;

Alpine.start();
