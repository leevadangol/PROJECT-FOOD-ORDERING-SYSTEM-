<?php
/*
    ===========================================================
    CAPTCHA GENERATOR (captcha.php)
    ===========================================================
    This file draws a small CAPTCHA image (random letters and
    numbers on a picture) and sends it straight to the browser
    as a PNG image.

    It is used like this inside login.php:
        <img src="captcha.php">

    No external service (like Google reCAPTCHA) is used - the
    image is created entirely on our own server using PHP's
    built-in GD image library.

    IMPORTANT: This file must not print/echo anything before the
    header() call near the bottom, otherwise the image will not
    display correctly in the browser.
    ===========================================================
*/

session_start(); // Needed so we can remember the code for login.php to check later


/* -----------------------------------------------------------
   STEP 1: Create a random CAPTCHA code
   -----------------------------------------------------------
   We only use letters/numbers that are easy to tell apart.
   We leave out 0, O, 1, I, L because they look too similar
   on screen and would confuse users.
*/
$characters   = "ABCDEFGHJKMNPQRSTUVWXYZ23456789";
$captcha_code = "";

for ($i = 0; $i < 5; $i++) {
    $captcha_code .= $characters[rand(0, strlen($characters) - 1)];
}


/* -----------------------------------------------------------
   STEP 2: Save the code in the SESSION
   -----------------------------------------------------------
   login.php will compare what the user TYPES against this
   saved value. Sessions let us remember this between the
   page that shows the image and the page that checks the form.
*/
$_SESSION['captcha_code'] = $captcha_code;


/* -----------------------------------------------------------
   STEP 3: Create a blank image to draw on
   -----------------------------------------------------------
   imagecreatetruecolor() makes an empty picture of the given
   width and height (in pixels) that we can now draw on.
*/
$width  = 140;
$height = 50;
$image  = imagecreatetruecolor($width, $height);


/* -----------------------------------------------------------
   STEP 4: Pick some colors
   -----------------------------------------------------------
   imagecolorallocate() makes a color we can reuse, using
   Red, Green, Blue (0-255 each).
*/
$bg_color   = imagecolorallocate($image, 255, 247, 237); // light cream background
$text_color = imagecolorallocate($image, 242, 93, 7);    // orange (matches site theme)
$line_color = imagecolorallocate($image, 210, 210, 210); // light grey noise lines

// Paint the whole image with the background color first
imagefilledrectangle($image, 0, 0, $width, $height, $bg_color);


/* -----------------------------------------------------------
   STEP 5: Draw a few random "noise" lines
   -----------------------------------------------------------
   These lines make it a little harder for a computer program
   to automatically read the text, without making it too hard
   for a real person to read.
*/
for ($i = 0; $i < 5; $i++) {
    imageline(
        $image,
        rand(0, $width), rand(0, $height),
        rand(0, $width), rand(0, $height),
        $line_color
    );
}


/* -----------------------------------------------------------
   STEP 6: Write the CAPTCHA letters onto the image
   -----------------------------------------------------------
   We print one character at a time, and move along the X
   position (left-to-right) and wobble the Y position
   (up-and-down) a little each time, so the text doesn't look
   perfectly straight/robotic.
*/
$x = 15; // starting horizontal position

for ($i = 0; $i < strlen($captcha_code); $i++) {
    $y = rand(8, 18); // small random vertical wobble
    imagestring($image, 5, $x, $y, $captcha_code[$i], $text_color);
    $x += 22; // move right, ready for the next character
}


/* -----------------------------------------------------------
   STEP 7: Send the finished image to the browser
   -----------------------------------------------------------
   The header tells the browser "this is a PNG picture, not
   normal text/HTML". imagepng() then prints the actual image
   data.
*/
header("Content-Type: image/png");
imagepng($image);


/* -----------------------------------------------------------
   STEP 8: Clean up
   -----------------------------------------------------------
   Free the memory PHP used to build the image now that it has
   been sent to the browser.
*/
imagedestroy($image);
