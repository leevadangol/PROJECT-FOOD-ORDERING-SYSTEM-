CREATE TABLE IF NOT EXISTS restaurant_settings (
    id                 TINYINT UNSIGNED NOT NULL PRIMARY KEY,
    restaurant_name    VARCHAR(100)  NOT NULL DEFAULT 'Restaurant',
    latitude           DECIMAL(10,7) NOT NULL,
    longitude          DECIMAL(10,7) NOT NULL,
    delivery_radius_km DECIMAL(5,2)  NOT NULL DEFAULT 5.00
);

INSERT INTO restaurant_settings (id, restaurant_name, latitude, longitude, delivery_radius_km)
VALUES (1, 'Restaurant', 27.670825, 85.309726, 5.00)
ON DUPLICATE KEY UPDATE id = id;

-- Customer's chosen delivery point + calculated distance, saved per order.

ALTER TABLE orders
    ADD COLUMN IF NOT EXISTS delivery_lat         DECIMAL(10,7) NULL,
    ADD COLUMN IF NOT EXISTS delivery_lng         DECIMAL(10,7) NULL,
    ADD COLUMN IF NOT EXISTS delivery_distance_km DECIMAL(6,2)  NULL;

-- ---------------------------------------------------------------
-- To change the restaurant position or radius later, run e.g.:
--   UPDATE restaurant_settings
--      SET latitude = 27.670825, longitude =85.309726, delivery_radius_km = 5
--    WHERE id = 1;
-- ---------------------------------------------------------------

-- achs location:: latitude = 27.670825, longitude =85.309726