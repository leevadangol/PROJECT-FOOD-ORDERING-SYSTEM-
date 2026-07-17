<?php
/*
    ============================================================
    eSewa CONFIGURATION FILE (esewa_config.php)
    ============================================================
    All eSewa settings are kept in ONE place here.
    To switch from TEST to PRODUCTION, only change this file.

    Official eSewa developer docs: https://developer.esewa.com.np
    ============================================================
*/

// ------------------------------------------------------------------
// TEST / UAT CREDENTIALS  (use these while developing)
// ------------------------------------------------------------------
// Merchant code for testing - provided by eSewa for all test merchants
define('ESEWA_PRODUCT_CODE', 'EPAYTEST');

// Secret key for generating the HMAC signature.
// This is eSewa's published test key - safe to use in development.
define('ESEWA_SECRET_KEY', '8gBm/:&EnhH.1/q');

// eSewa's TEST payment page URL (from official docs)
define('ESEWA_PAYMENT_URL', 'https://rc-epay.esewa.com.np/api/epay/main/v2/form');


// ------------------------------------------------------------------
// TEST LOGIN CREDENTIALS  (use these on eSewa's login page)
// ------------------------------------------------------------------
/*
    eSewa ID : 9806800001  (also try 9806800002, ..003, ..004, ..005)
    Password : Nepal@123
    MPIN     : 1122
    OTP      : 123456
*/


// ------------------------------------------------------------------
// PRODUCTION CREDENTIALS  (use these only when going live)
// ------------------------------------------------------------------
// Uncomment the lines below and comment out the TEST lines above
// when you are ready to accept real payments:
//
// define('ESEWA_PRODUCT_CODE', 'YOUR_REAL_MERCHANT_CODE');
// define('ESEWA_SECRET_KEY',   'YOUR_REAL_SECRET_KEY');
// define('ESEWA_PAYMENT_URL',  'https://epay.esewa.com.np/api/epay/main/v2/form');
