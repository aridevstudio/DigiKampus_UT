import assert from 'node:assert/strict';
import test from 'node:test';
import {
    agendaDueForReminder,
    agendaNotificationKey,
    normaliseAgendaEvent,
} from '../../resources/js/agenda-desktop-notifications.js';

test('normaliseAgendaEvent builds a valid local start time from agenda data', () => {
    const event = normaliseAgendaEvent({
        id: 17,
        title: 'Webinar Laravel',
        date: '2026-07-18',
        time: '14:30:00',
        type: 'webinar',
    });

    assert.equal(event.id, '17');
    assert.equal(event.startAt.getFullYear(), 2026);
    assert.equal(event.startAt.getHours(), 14);
    assert.equal(agendaNotificationKey(event), 'digikampus-agenda-17-2026-07-18T14:30:00');
});

test('agendaDueForReminder only selects an agenda inside the 15-minute reminder window', () => {
    const event = normaliseAgendaEvent({
        id: 18,
        title: 'Deadline tugas',
        date: '2026-07-18',
        time: '15:00',
        type: 'deadline',
    });

    assert.equal(agendaDueForReminder(event, new Date('2026-07-18T14:45:00'), 15), true);
    assert.equal(agendaDueForReminder(event, new Date('2026-07-18T14:44:59'), 15), false);
    assert.equal(agendaDueForReminder(event, new Date('2026-07-18T15:00:00'), 15), false);
});
