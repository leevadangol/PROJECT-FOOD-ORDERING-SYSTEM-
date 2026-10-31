<?php
/*
    ADMIN HEADER (Admin/a-header.php)
    Shared navbar on every admin page.
    Main color  : #f25d07 (orange)
    Sub color   : #c44e04 (dark orange - hover/active)
*/
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

$current = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../CSS/home_style.css" />
    <style>
        /* ===== BASE ===== */
        body { font-family:Arial,sans-serif; margin:0; background:#f1f2f6; }

        /* ===== NAVBAR - Main: #f25d07, Sub/hover: #c44e04 ===== */
        .navbar {
            background: #f25d07;   /* main orange */
            padding: 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        .navbar .container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1300px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .navbar .logo img { height:45px; padding:8px 0; }

        .navbar ul { list-style:none; margin:0; padding:0; display:flex; flex-wrap:wrap; }

        .navbar ul li a {
            display: block;
            padding: 16px 14px;
            color: white;
            text-decoration: none;
            font-size: 13px;
            font-weight: bold;
            transition: background 0.2s;
            border-bottom: 3px solid transparent;
        }
        /* Hover state: sub color (dark orange) */
        .navbar ul li a:hover {
            background: #c44e04;       /* sub-main dark orange */
            border-bottom: 3px solid white;
        }
        /* Active page: white background with orange text */
        .navbar ul li a.active {
            background: white;
            color: #f25d07;
            border-bottom: 3px solid #c44e04;
        }

        /* ===== PAGE WRAPPER ===== */
        .admin-page {
            max-width: 1300px;
            margin: 25px auto;
            padding: 0 20px;
        }

        /* ===== CARDS ===== */
        .admin-card {
            background: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        .admin-card h2 {
            color: #333;
            margin: 0 0 20px 0;
            font-size: 20px;
            border-bottom: 3px solid #f25d07;
            padding-bottom: 10px;
        }

        /* ===== BUTTONS ===== */
        .btn-primary { background:#f25d07; color:white; padding:8px 16px; border:none; border-radius:5px; cursor:pointer; text-decoration:none; font-size:13px; display:inline-block; transition:background 0.2s; }
        .btn-primary:hover { background:#c44e04; }
        .btn-danger  { background:#f44336; color:white; padding:6px 12px; border:none; border-radius:4px; cursor:pointer; text-decoration:none; font-size:13px; display:inline-block; }
        .btn-danger:hover  { background:#c62828; }
        .btn-info    { background:#2196f3; color:white; padding:6px 12px; border:none; border-radius:4px; cursor:pointer; text-decoration:none; font-size:13px; display:inline-block; }
        .btn-info:hover    { background:#1565c0; }
        .btn-success { background:#4caf50; color:white; padding:6px 12px; border:none; border-radius:4px; cursor:pointer; text-decoration:none; font-size:13px; display:inline-block; }
        .btn-success:hover { background:#2e7d32; }

        /* ===== TABLES ===== */
        .admin-table { width:100%; border-collapse:collapse; font-size:14px; }
        .admin-table th, .admin-table td { padding:11px 13px; text-align:left; border-bottom:1px solid #eee; }
        .admin-table th { background:#fff3ec; font-weight:bold; border-bottom:2px solid #f25d07; color:#333; }
        .admin-table tr:hover { background:#fffaf7; }
        .table-responsive { overflow-x:auto; }

        /* ===== SEARCH FORM ===== */
        .search-form { display:flex; gap:8px; margin-bottom:15px; flex-wrap:wrap; }
        .search-form input[type="text"],
        .search-form select {
            padding:8px 12px; border:1px solid #ccc; border-radius:5px;
            font-size:14px; flex:1; min-width:180px;
        }
        .search-form input:focus, .search-form select:focus { border-color:#f25d07; outline:none; }
        .search-form button { padding:8px 16px; background:#f25d07; color:white; border:none; border-radius:5px; cursor:pointer; font-weight:bold; }
        .search-form button:hover { background:#c44e04; }
        .search-form a.clear-btn { padding:8px 12px; background:#ccc; color:#333; border-radius:5px; text-decoration:none; font-size:14px; }

        /* ===== STATUS BADGES ===== */
        .badge { padding:3px 9px; border-radius:12px; font-size:12px; font-weight:bold; }
        .badge-pending   { background:#fff3e0; color:#e65100; }
        .badge-confirmed { background:#e3f2fd; color:#1565c0; }
        .badge-accepted  { background:#fff8e1; color:#f57f17; }
        .badge-ready     { background:#fce4ec; color:#c62828; }
        .badge-completed { background:#e8f5e9; color:#2e7d32; }
        .badge-cancelled { background:#ffebee; color:#b71c1c; }

        /* ===== ALERTS ===== */
        .alert-success { background:#d4edda; color:#155724; padding:12px 15px; border-radius:5px; margin-bottom:15px; border-left:4px solid #4caf50; }
        .alert-error   { background:#f8d7da; color:#721c24; padding:12px 15px; border-radius:5px; margin-bottom:15px; border-left:4px solid #f44336; }

        /* ===== FORM INPUTS ===== */
        .form-group { margin-bottom:15px; }
        .form-group label { display:block; font-weight:bold; margin-bottom:5px; color:#555; font-size:14px; }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width:100%; padding:9px 12px; border:1px solid #ccc;
            border-radius:5px; font-size:14px; box-sizing:border-box;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus { border-color:#f25d07; outline:none; }

        @media(max-width:768px) {
            .navbar ul li a { padding:10px 8px; font-size:12px; }
            .admin-page { padding:0 10px; }
        }
    </style>
</head>
<body>

<section class="navbar">
    <div class="container">
        <div class="logo">
            <img src="../IMAGES/logo.png" alt="LOGO">
        </div>
        <div class="menu">
            <ul>
                <li><a href="a-dashboard.php"  <?php if($current==='a-dashboard.php')  echo 'class="active"'; ?>>&#128200; Dashboard</a></li>
                <li><a href="a-foodmanage.php"  <?php if($current==='a-foodmanage.php')  echo 'class="active"'; ?>>&#127829; Foods</a></li>
                <li><a href="a-orderpage.php"   <?php if($current==='a-orderpage.php')   echo 'class="active"'; ?>>&#128203; Orders</a></li>
                <li><a href="a-payments.php"    <?php if($current==='a-payments.php')    echo 'class="active"'; ?>>&#128176; Payments</a></li>
                <li><a href="a-customers.php"   <?php if($current==='a-customers.php')   echo 'class="active"'; ?>>&#128101; Customers</a></li>
                <li><a href="a-reports.php"     <?php if($current==='a-reports.php')     echo 'class="active"'; ?>>&#128196; Reports</a></li>
                <li><a href="admin_invoice.php" <?php if($current==='admin_invoice.php') echo 'class="active"'; ?>>&#128424; Invoice</a></li>
                <li><a href="../logout.php">&#128275; Logout</a></li>
            </ul>
        </div>
    </div>
</section>
