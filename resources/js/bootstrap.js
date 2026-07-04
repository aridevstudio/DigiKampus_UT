import axios from 'axios';
window.axios = axios;

// Defensive: anchor axios to the current page origin so all relative
// requests resolve against https://, even if APP_URL on the server
// drifts to http://. Prevents Mixed Content blocks in HTTPS pages.
if (!window.axios.defaults.baseURL) {
    window.axios.defaults.baseURL = window.location.origin;
}

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';
