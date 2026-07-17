const STORAGE_PREFIX = 'digikampus-agenda-reminder:';
const REMINDER_MINUTES = 15;

export function normaliseAgendaEvent(rawEvent) {
    if (!rawEvent || rawEvent.id === undefined || !rawEvent.title || !rawEvent.date || !rawEvent.time) {
        return null;
    }

    const time = String(rawEvent.time).slice(0, 5);
    const startAt = new Date(`${rawEvent.date}T${time}:00`);

    if (Number.isNaN(startAt.getTime())) {
        return null;
    }

    return {
        id: String(rawEvent.id),
        title: String(rawEvent.title),
        type: String(rawEvent.type || 'agenda'),
        date: String(rawEvent.date),
        time,
        startAt,
    };
}

export function agendaNotificationKey(event) {
    const start = `${event.date}T${event.time}:00`;
    return `digikampus-agenda-${event.id}-${start}`;
}

export function agendaDueForReminder(event, now = new Date(), reminderMinutes = REMINDER_MINUTES) {
    const millisecondsUntilStart = event.startAt.getTime() - now.getTime();
    return millisecondsUntilStart > 0 && millisecondsUntilStart <= reminderMinutes * 60 * 1000;
}

function formatTime(event) {
    return event.startAt.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
    });
}

function formatDate(event) {
    return event.startAt.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
}

class AgendaDesktopNotifier {
    constructor(element) {
        this.element = element;
        this.events = (window.DigiKampusAgendaNotificationPayload || [])
            .map(normaliseAgendaEvent)
            .filter(Boolean);
        this.calendarUrl = element.dataset.calendarUrl || '/mahasiswa/calendar';
        this.enableButton = element.querySelector('[data-agenda-notification-enable]');
        this.testButton = element.querySelector('[data-agenda-notification-test]');
        this.status = element.querySelector('[data-agenda-notification-status]');
        this.timer = null;
    }

    supported() {
        return 'Notification' in window;
    }

    setStatus(message, tone = 'neutral') {
        if (!this.status) return;

        this.status.textContent = message;
        this.status.dataset.tone = tone;
    }

    refreshControls() {
        if (!this.supported()) {
            this.enableButton?.setAttribute('hidden', 'hidden');
            this.testButton?.setAttribute('hidden', 'hidden');
            this.setStatus('Browser ini belum mendukung notifikasi desktop.', 'warning');
            return;
        }

        if (Notification.permission === 'granted') {
            this.enableButton?.setAttribute('hidden', 'hidden');
            this.testButton?.removeAttribute('hidden');
            this.setStatus('Notifikasi desktop aktif. Pengingat dikirim 15 menit sebelum agenda dimulai.', 'success');
            return;
        }

        this.testButton?.setAttribute('hidden', 'hidden');

        if (Notification.permission === 'denied') {
            this.enableButton?.setAttribute('hidden', 'hidden');
            this.setStatus('Notifikasi diblokir di Chrome. Klik ikon kunci di address bar, buka Izin situs, lalu izinkan Notifikasi.', 'warning');
            return;
        }

        this.enableButton?.removeAttribute('hidden');
        this.setStatus('Aktifkan agar pengingat agenda muncul sebagai notifikasi desktop Chrome.', 'neutral');
    }

    async requestPermission() {
        if (!this.supported()) {
            this.refreshControls();
            return;
        }

        const permission = await Notification.requestPermission();
        this.refreshControls();

        if (permission === 'granted') {
            this.showTestNotification();
            this.start();
        }
    }

    showTestNotification() {
        if (!this.supported() || Notification.permission !== 'granted') return;

        const notification = new Notification('DigiKampus UT', {
            body: 'Notifikasi desktop aktif. Pengingat agenda akan muncul 15 menit sebelum kegiatan dimulai.',
            tag: 'digikampus-agenda-permission-test',
        });

        notification.onclick = () => {
            window.focus();
            notification.close();
        };
    }

    notify(event) {
        const notification = new Notification('Pengingat Agenda DigiKampus', {
            body: `${event.title} dimulai pukul ${formatTime(event)} (${formatDate(event)}).`,
            tag: agendaNotificationKey(event),
            requireInteraction: true,
        });

        notification.onclick = () => {
            window.focus();
            window.location.assign(this.calendarUrl);
            notification.close();
        };
    }

    checkAgenda() {
        if (!this.supported() || Notification.permission !== 'granted') return;

        const now = new Date();
        this.events.forEach((event) => {
            const storageKey = `${STORAGE_PREFIX}${agendaNotificationKey(event)}`;
            if (localStorage.getItem(storageKey) || !agendaDueForReminder(event, now)) return;

            this.notify(event);
            localStorage.setItem(storageKey, String(now.getTime()));
        });
    }

    start() {
        this.checkAgenda();

        if (this.timer) return;
        this.timer = window.setInterval(() => this.checkAgenda(), 60 * 1000);
    }

    init() {
        this.enableButton?.addEventListener('click', () => this.requestPermission());
        this.testButton?.addEventListener('click', () => this.showTestNotification());
        this.refreshControls();

        if (this.supported() && Notification.permission === 'granted') {
            this.start();
        }
    }
}

function bootAgendaDesktopNotifications() {
    document.querySelectorAll('[data-agenda-desktop-notifications]').forEach((element) => {
        new AgendaDesktopNotifier(element).init();
    });
}

if (typeof document !== 'undefined') {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootAgendaDesktopNotifications, { once: true });
    } else {
        bootAgendaDesktopNotifications();
    }
}
