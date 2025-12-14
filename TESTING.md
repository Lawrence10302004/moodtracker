Manual test: Ensure calendar, modal and daily-log stay in sync

1. Open `calendar.php`.
2. Click a day that has a diary entry to open the modal.
3. Click `Edit` in the modal and change the mood (click a mood emoji), optionally change diary text.
4. Click `Save`.
   - Confirm the modal mood icon updates and `Saved changes` toast appears.
   - Confirm the calendar updates (emoji or marker for the day) without reloading the page.
5. In another tab open `daily-log.php?date=YYYY-MM-DD` with the same date.
6. After saving the modal in the calendar tab, switch to the `daily-log` tab and confirm the page auto-refreshes and shows the selected mood on the mood selector and updated tags/diary.
7. If you have `home.php` open for today, switch to it and reload; the today mood should reflect the saved mood (home reads `get_today_mood.php`).

Additional checks:
- Turn on camera/audio detection in `daily-log.php` and allow the detectors to run. When the detector changes mood significantly, the `Daily Log` mood selector should update automatically and a save will be attempted (throttled). After the save, `Home` and `Calendar` should refresh (Home via `dayUpdated` listener or manual reload; Calendar via storage event).
- When saving a mood on `Home` using the `Save` button, the change should propagate to `Daily Log` and `Calendar` immediately (via `dayUpdated` + `localStorage`).

Migration and verification:
- Run the migration to normalize historical mood rows: `php scripts/normalize_mood_logs.php`.
- Optionally deduplicate older rows (keep latest per user/date): `php scripts/dedupe_mood_logs.php`.
- Verify `get_daily_log.php` now returns TitleCase moods and percentages for confidences.
- Verify `get_month_moods.php` shows the latest mood per date after saving multiple times for the same day.
- Quick local end-to-end test (no browser): `php scripts/test_end_to_end.php` — this will insert/update today's row for user 1 and print `get_daily_log` and `get_month_moods` outputs.
- After running the migration+dedupe, try saving from `Home` (Save button) and confirm `Daily Log` and `Calendar` reflect the saved values without stale results.

Notes:
- The calendar modal now calls `api/save_mood.php` so a canonical `mood_logs` row is created with `meta.selected_mood` linking to the diary when relevant.
- Detection-driven updates in `daily-log` now persist to `api/save_mood.php` (throttled) and broadcast `dayUpdated`.
- The app uses `localStorage` key `dayUpdated` to notify other tabs; this triggers a storage event to refresh the UI in other tabs.
- To revert changes, remove the `save_mood` call from `calendar.php`, the detection auto-save in `daily-log.php`, and the storage/event code in `calendar.php`, `daily-log.php`, and `home.php`.
