<?php
/*
    LOCATION HELPER (location_helper.php)  -- NEW FILE
    Shared by:  check_delivery.php, update_order_details.php, myorder.php,
                Admin/a-orderpage.php

    Contains:
      haversine_km()             - the Haversine formula
      valid_coordinates()        - server-side latitude/longitude validation
      get_restaurant_location()  - reads restaurant lat/lng/radius from MySQL
      check_delivery_location()  - validates + measures + checks the radius

    NOTE: Haversine gives the STRAIGHT-LINE ("as the crow flies") distance
    over the Earth's surface. It is NOT the real road distance, which is
    usually longer.
*/

/**
 * Haversine formula - great-circle distance between two points.
 * @return float distance in kilometres
 */
function haversine_km(float $lat1, float $lng1, float $lat2, float $lng2): float
{
    $earthRadiusKm = 6371.0088;           // mean Earth radius

    $dLat = deg2rad($lat2 - $lat1);
    $dLng = deg2rad($lng2 - $lng1);

    $a = sin($dLat / 2) ** 2
       + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

    // min(1, ...) protects asin() from tiny floating-point overshoot
    return 2 * $earthRadiusKm * asin(min(1.0, sqrt($a)));
}

/**
 * True only for real numeric lat/lng inside the valid range.
 * Rejects empty values, text, NaN/INF, out-of-range numbers and the
 * "null island" 0,0 point (which is what a failed lookup often produces).
 */
function valid_coordinates($lat, $lng): bool
{
    if (!is_numeric($lat) || !is_numeric($lng)) return false;

    $lat = (float)$lat;
    $lng = (float)$lng;

    if (!is_finite($lat) || !is_finite($lng)) return false;
    if ($lat < -90  || $lat > 90)             return false;
    if ($lng < -180 || $lng > 180)            return false;
    if ($lat == 0.0 && $lng == 0.0)           return false;

    return true;
}

/**
 * Restaurant position + delivery radius from the restaurant_settings table.
 * Returns null if the table/row is missing (migration not run yet).
 */
function get_restaurant_location(mysqli $conn): ?array
{
    try {
        $res = mysqli_query($conn,
            "SELECT restaurant_name, latitude, longitude, delivery_radius_km
             FROM restaurant_settings WHERE id = 1 LIMIT 1");
        $row = $res ? mysqli_fetch_assoc($res) : null;
    } catch (mysqli_sql_exception $e) {
        return null;                       // table does not exist yet
    }

    if (!$row) return null;

    return [
        'name'      => $row['restaurant_name'],
        'lat'       => (float)$row['latitude'],
        'lng'       => (float)$row['longitude'],
        'radius_km' => (float)$row['delivery_radius_km'],
    ];
}

/**
 * Full server-side check of a customer-selected point.
 *
 * Returns an array:
 *   ok          bool    true = valid AND inside the delivery radius
 *   error       string  customer-friendly message when ok is false
 *   lat, lng    float   cleaned coordinates (only when valid)
 *   distance_km float   straight-line distance, 2 decimals
 *   radius_km   float   configured radius
 */
function check_delivery_location(mysqli $conn, $lat, $lng): array
{
    if (!valid_coordinates($lat, $lng)) {
        return ['ok' => false,
                'error' => 'Please select and confirm a valid delivery location on the map.'];
    }

    $restaurant = get_restaurant_location($conn);
    if ($restaurant === null) {
        return ['ok' => false,
                'error' => 'Delivery area is not configured yet. Please contact the restaurant.'];
    }

    $lat = (float)$lat;
    $lng = (float)$lng;

    $distance = haversine_km($restaurant['lat'], $restaurant['lng'], $lat, $lng);
    $within   = $distance <= $restaurant['radius_km'];

    $result = [
        'ok'          => $within,
        'lat'         => $lat,
        'lng'         => $lng,
        'distance_km' => round($distance, 2),
        'radius_km'   => $restaurant['radius_km'],
    ];

    if (!$within) {
        $result['error'] = 'Sorry, delivery is not available at your selected location.';
    }

    return $result;
}
