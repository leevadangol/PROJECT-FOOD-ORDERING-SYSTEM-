<?php
/*
    ===========================================================
    OTP EMAIL SENDER  (otp_mailer.php)
    ===========================================================
    Contains one function: send_otp_email()
    Called by login.php whenever an OTP needs to be emailed.

    Uses PHP's built-in mail() function — no extra libraries
    needed. Works on XAMPP if sendmail is configured (see
    SETUP NOTES). If not configured, verify_otp.php will
    automatically show the OTP on screen (demo mode) so the
    project can still be demonstrated without email setup.

    SETUP NOTES FOR XAMPP (do this once to enable real emails):
    -----------------------------------------------------------
    1. Open C:\xampp\php\php.ini
    2. Find the [mail function] section and set:
           SMTP=smtp.gmail.com
           smtp_port=587
           sendmail_from=your_gmail@gmail.com

    3. Download "sendmail for Windows":
           https://www.glob.com.au/sendmail/
       Extract it to C:\xampp\sendmail\

    4. Open C:\xampp\sendmail\sendmail.ini and set:
           smtp_server=smtp.gmail.com
           smtp_port=587
           auth_username=your_gmail@gmail.com
           auth_password=your_gmail_app_password
       (Get App Password: Google Account > Security > App Passwords)

    5. In php.ini, set:
           sendmail_path = "C:\xampp\sendmail\sendmail.exe -t"

    6. Restart Apache in XAMPP Control Panel.
    ===========================================================
*/

function send_otp_email($to_email, $to_name, $otp_code) {

    $subject = "Your Login OTP - Food Ordering System";

    // HTML email body with the OTP displayed prominently
    $message = "
    <html>
    <body style='font-family:Arial,sans-serif; background:#f4f4f4; padding:20px;'>
        <div style='max-width:400px; margin:0 auto; background:white; padding:30px;
                    border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,0.1);'>
            <h2 style='color:#f25d07; text-align:center;'>Login Verification</h2>
            <p>Hello <strong>" . htmlspecialchars($to_name) . "</strong>,</p>
            <p>Your One-Time Password (OTP) for login is:</p>
            <div style='text-align:center; margin:25px 0;'>
                <span style='font-size:36px; font-weight:bold; letter-spacing:8px;
                             color:#333; background:#fff3ec; padding:15px 25px;
                             border-radius:8px; border:2px solid #f25d07;'>
                    " . $otp_code . "
                </span>
            </div>
            <p style='color:#666; font-size:13px;'>This OTP is valid for <strong>5 minutes</strong> only.</p>
            <p style='color:#666; font-size:13px;'>If you did not request this, please ignore this email.</p>
            <hr style='border:none; border-top:1px solid #eee; margin:20px 0;'>
            <p style='text-align:center; color:#999; font-size:12px;'>Food Ordering System</p>
        </div>
    </body>
    </html>";

    // Headers tell the mail server this is HTML, not plain text
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: Food Ordering System <no-reply@foodorder.com>\r\n";

    // mail() returns true if accepted by server, false if not
    return mail($to_email, $subject, $message, $headers);
}
