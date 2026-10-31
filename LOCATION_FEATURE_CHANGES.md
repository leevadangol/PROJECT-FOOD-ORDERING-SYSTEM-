# Location-Based Delivery — Change Notes

OpenStreetMap + Leaflet.js map, Haversine distance in PHP, delivery-radius check.
No Google Maps, no API key, no paid service.

## How to apply (5 minutes)

1. **Back up** your project folder and database.
2. Copy these into your project (same folder structure):
   - NEW: `location_helper.php`, `check_delivery.php`, `location_migration.sql`, `leaflet/` (whole folder)
   - CHANGED: `myorder.php`, `update_order_details.php`, `Admin/a-orderpage.php`
3. **Edit the restaurant coordinates** in `location_migration.sql` (the `INSERT` line), then run the whole file in phpMyAdmin → your `project` database → SQL tab.
4. Log in as a customer → My Orders → you should see the map.

> ⚠ The coordinates in the SQL (27.7172, 85.3240 = central Kathmandu) are a **placeholder**. Replace with your real restaurant position, otherwise the 5 km circle will be in the wrong place.

---

## Your project, as I found it

| Purpose | File |
|---|---|
| Real checkout page (customer details, address, payment choice) | `myorder.php` |
| Order submit handler (COD / eSewa routing) | `update_order_details.php` |
| DB connection | `db.php` (and an identical `Admin/db.php`) |
| Admin order management | `Admin/a-orderpage.php` |
| Add to cart | `order.php` → `process_order.php` (not touched) |

Notes:
- There is **no pickup option** in your system, so nothing needed to be excluded.
- Your existing "Inside / Outside Ringroad" fee (Rs 100 / 150) is **unchanged**. The map is an *additional* check; it does not replace the fee logic.
- `sql.txt` is out of date: `orders.delivery_address`, `orders.delivery_fee` and the `saved_addresses` table are used by your code but are not defined in any `.sql` file. I did not touch them. (You may want to add them to `sql.txt` so a fresh install works.)

---

## Database changes (`location_migration.sql`)

**New table `restaurant_settings`** (one row, id = 1) — restaurant position kept separate from customer data:

| column | meaning |
|---|---|
| `latitude`, `longitude` | restaurant position |
| `delivery_radius_km` | default **5.00** — change any time with `UPDATE restaurant_settings SET delivery_radius_km = 7 WHERE id = 1;` |
| `restaurant_name` | label only |

**`orders` table — 3 new nullable columns** (old orders keep NULL, nothing deleted):
`delivery_lat`, `delivery_lng`, `delivery_distance_km`.

Safe to run twice (MariaDB / XAMPP). On real MySQL, remove `IF NOT EXISTS` from the `ALTER TABLE` lines.

---

## New files

### `location_helper.php`
- `haversine_km()` – the Haversine formula (Earth radius 6371.0088 km).
- `valid_coordinates()` – rejects empty, text, NaN/INF, out-of-range, and 0,0.
- `get_restaurant_location()` – reads `restaurant_settings` (returns null if the migration hasn't been run, so pages show a friendly message instead of crashing).
- `check_delivery_location()` – validate → measure → compare with radius. The radius comparison uses the unrounded distance; the stored value is rounded to 2 decimals.

### `check_delivery.php`
JSON endpoint the map calls after every click/drag. Login required, POST only. It only gives the customer instant feedback; **the real check is repeated on submit**.

### `leaflet/`
Leaflet 1.9.4 (BSD-2 licence, included) stored locally so it works without any CDN. Map **tiles** come from `tile.openstreetmap.org`, with the required "© OpenStreetMap contributors" attribution shown on both maps.

### `location_migration.sql`
See above.

---

## Changed files (complete files are in the ZIP)

### `myorder.php`  (+230 lines, nothing removed)
1. `require_once "location_helper.php"` and load the restaurant row.
2. `<link>` to `leaflet/leaflet.css` + small map styles in `<head>`.
3. **New "Pin Your Delivery Location" block** inside the existing `#detailsForm`, placed just above your Delivery Location (Ringroad) radios: map, *Use My Current Location* button, *Confirm This Location* button, lat/lng/distance readout, straight-line note, message area, and two hidden inputs `delivery_lat` / `delivery_lng`.
4. **New `<script>` at the bottom**: click-to-place, draggable marker, GPS, calls `check_delivery.php`, enables *Confirm* only when inside the radius, clears confirmation if the marker moves again, blocks form submit until a location is confirmed. Handles GPS denied / unavailable / timeout, tile failure, Leaflet failing to load.

Your existing cart table, saved-address selector, fee radios, payment cards and both existing `updateDeliveryFee()` scripts are untouched.

### `update_order_details.php`  (+31 / −3)
- Includes the helper.
- **After** the existing required-field check and **before** anything is written: recomputes the Haversine distance from the posted lat/lng. Missing/invalid → back to `myorder.php` with an error. Outside radius → *"Sorry, delivery is not available at your selected location."* plus the distance.
- The existing `UPDATE orders …` now also saves `delivery_lat`, `delivery_lng`, `delivery_distance_km` (prepared statement; bind types `ssssdi` → `ssssddddi`).
- COD / eSewa routing, payment rows, fees: unchanged.

### `Admin/a-orderpage.php`  (+121 / −1)
- Selects the 3 new columns; new **"Distance / Map"** column showing `x.xx km (straight line)` and a **"View Location on Map"** button (only for orders that have a saved location; older orders show `—`).
- Popup map: customer pin, restaurant dot, radius circle and a dashed line between them.
- Empty-state `colspan` 12 → 13.

---

## What was tested (and what was not)

Tested on PHP 8.3 + MariaDB 10.11 with a database rebuilt from your SQL files, plus Chromium automation:

- Haversine vs. an independent formula (5 cases incl. London–Paris 343.56 km): match.
- Coordinate validation: 12 inputs (text, blank, null, out-of-range, NaN, INF, SQL-ish string, 0,0).
- `check_delivery.php`: 401 without login, 405 on GET, inside / exactly-on-edge / outside / garbage.
- Order submit: missing, garbage, out-of-range, outside-radius and **forged-distance** requests are all rejected and write nothing; a valid COD order is confirmed, creates its payment row, and stores lat/lng/distance (2.54 km for the test point).
- Browser: map renders, click, drag, GPS granted and denied, confirm/cancel logic, submit blocked without location, OSM attribution, no horizontal scroll at 400 px, tile-failure and Leaflet-missing messages, admin column + popup, history tab with an old order.

**Not tested:** (1) the eSewa payment path end-to-end (needs eSewa's servers; the code path up to the redirect is the same as COD, and `initiate_payment.php` is untouched); (2) real OpenStreetMap tiles — the test machine had no internet, so tile requests were intercepted; (3) real phone GPS hardware; (4) your actual database — I rebuilt it from your files, so if your real `orders` table differs, run the migration and check.

## Things to know

- **Haversine = straight-line distance**, not road distance. This is stated on the checkout page and in the admin popup. A customer 4.8 km away "as the crow flies" may be 7 km by road.
- **GPS in the browser needs HTTPS or `localhost`.** `http://localhost/...` on XAMPP works. Opening the site by LAN IP (e.g. `http://192.168.1.5/...`) will make the browser refuse GPS — the customer can still click the map.
- **OSM tile policy:** fine for a college project and normal use. Keep the attribution; don't bulk-download tiles. For a real production launch with heavy traffic, use a tile provider.
- All orders in the "Pending" cart share the same delivery point (same as your existing address/fee behaviour).
- Saved addresses do not store the map point (kept out of scope so `saved_addresses` isn't altered); the customer re-pins each time.
- Restaurant location/radius is edited by SQL (no admin screen) — a small settings page could be added later.
