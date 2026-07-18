# Booking Rules

- Timezone: Asia/Jakarta.
- Operating hours: 09:00–24:00.
- Eight billiard tables.
- Duration: 1–4 hours.
- Interval: 1 hour for online booking.
- Maximum advance window: 14 days.
- Online lead time: 1 hour.
- Weekday billiard: Rp15,000/table/hour.
- Weekend billiard: Rp20,000/table/hour.
- Online unpaid hold: 15 minutes.

Conflict formula: `new_start < existing_end AND new_end > existing_start` for locking statuses. Creation locks the selected table row before checking conflicts.
