-- ===========================================================
-- eSewa PAYMENT INTEGRATION - DATABASE CHANGES
-- ===========================================================
-- Run this in phpMyAdmin (SQL tab) against your existing
-- project database. It only ADDS a new table and one new
-- column to "orders" - nothing existing is changed or removed.
-- ===========================================================

-- 1) New table to record every eSewa payment attempt
CREATE TABLE payments (
    payment_id      INT AUTO_INCREMENT PRIMARY KEY,
    c_id            INT NOT NULL,                          -- which customer paid
    transaction_id  VARCHAR(100) NOT NULL UNIQUE,           -- our own generated ID, sent TO eSewa
    esewa_ref_id    VARCHAR(50)  NULL,                      -- eSewa's own Transaction ID, received BACK from eSewa
    payment_status  VARCHAR(20)  NOT NULL DEFAULT 'Pending', -- Pending / Completed / Failed
    payment_method  VARCHAR(30)  NOT NULL DEFAULT 'eSewa',
    amount          DECIMAL(10,2) NOT NULL,
    payment_date    DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (c_id) REFERENCES signup_page(c_id)
);

-- 2) Link each order to the payment that confirmed it
--    (nullable, since orders start as "Pending" before any payment exists)
ALTER TABLE orders
ADD COLUMN payment_id INT NULL AFTER status,
ADD FOREIGN KEY (payment_id) REFERENCES payments(payment_id);
