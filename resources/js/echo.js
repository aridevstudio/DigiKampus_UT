import Echo from 'laravel-echo';

import Pusher from 'pusher-js';

const reverbAppKey = import.meta.env.VITE_REVERB_APP_KEY;
const reverbHost = import.meta.env.VITE_REVERB_HOST;

// Realtime tidak diperlukan pada halaman autentikasi. Jangan biarkan konfigurasi
// Reverb yang belum tersedia menghentikan bootstrap JavaScript global (termasuk Alpine).
if (reverbAppKey && reverbHost) {
    window.Pusher = Pusher;

    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: reverbAppKey,
        wsHost: reverbHost,
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
    });
}
