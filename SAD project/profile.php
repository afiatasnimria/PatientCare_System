<?php
session_start();
require_once __DIR__ . '/db.php';

// If user not logged in, redirect to login
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = (int) $_SESSION['user_id'];

$profile = [
    'full_name' => '', 'email' => '', 'phone' => '',
    'address' => '', 'house_no' => '', 'city' => '', 'postal_code' => '',
    'blood_group' => '', 'last_donation' => '', 'donation_count' => '', 'emergency_contact' => ''
];

$stmt = $conn->prepare('SELECT full_name, email, phone FROM users WHERE user_id = ? LIMIT 1');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($full_name, $email, $phone);
if ($stmt->fetch()) {
    $profile['full_name'] = $full_name;
    $profile['email'] = $email;
    $profile['phone'] = $phone;
}
$stmt->close();

$tableCheck = $conn->query("SHOW TABLES LIKE 'user_profiles'");
if ($tableCheck && $tableCheck->num_rows > 0) {
    $stmt = $conn->prepare('SELECT address, house_no, city, postal_code, blood_group, last_donation, donation_count, emergency_contact, photo FROM user_profiles WHERE user_id = ? LIMIT 1');
    if ($stmt) {
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $stmt->bind_result($address, $house_no, $city, $postal_code, $blood_group, $last_donation, $donation_count, $emergency_contact, $photo);
        if ($stmt->fetch()) {
            $profile['address'] = $address;
            $profile['house_no'] = $house_no;
            $profile['city'] = $city;
            $profile['postal_code'] = $postal_code;
            $profile['blood_group'] = $blood_group;
            $profile['last_donation'] = $last_donation;
            $profile['donation_count'] = $donation_count;
            $profile['emergency_contact'] = $emergency_contact;
            $profile['photo'] = $photo;
        }
        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings - AmraAchi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a5276;
            --secondary-color: #2980b9;
            --accent-color: #27ae60;
            --emergency-color: #e74c3c;
            --light-bg: #ecf0f1;
            --dark-text: #2c3e50;
            --sidebar-width: 280px;
            --card-shadow: 0 10px 20px rgba(0,0,0,0.05);
            --hover-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4eaf5 100%);
            color: var(--dark-text);
            overflow-x: hidden;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* ===== TOP HEADER ===== */
        .top-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 10px 0;
            position: relative;
            z-index: 1000;
            transition: transform 0.3s ease, opacity 0.3s ease;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .top-header.hidden {
            transform: translateY(-100%);
            opacity: 0;
        }
        .contact-info span {
            margin-right: 20px;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
        }
        .contact-info i {
            margin-right: 5px;
        }
        .social-icons a {
            color: white;
            margin-left: 15px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: rgba(255,255,255,0.1);
        }
        .social-icons a:hover {
            color: var(--light-bg);
            transform: translateY(-2px);
            background-color: rgba(255,255,255,0.2);
        }
        .lang-toggle {
            background-color: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
        }
        .lang-toggle:hover {
            background-color: rgba(255, 255, 255, 0.3);
            transform: scale(1.05);
        }
        
        /* ===== MAIN HEADER ===== */
        .main-header {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1001;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }
        .main-header.hidden {
            transform: translateY(-100%);
            opacity: 0;
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.8rem;
            color: var(--primary-color) !important;
            display: flex;
            align-items: center;
        }
        .navbar-brand i {
            margin-right: 10px;
            color: var(--emergency-color);
        }
        .menu-toggle {
            background: none;
            border: none;
            color: var(--primary-color);
            font-size: 1.5rem;
            cursor: pointer;
            margin-right: 15px;
            transition: all 0.3s;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .menu-toggle:hover {
            color: var(--secondary-color);
            background-color: rgba(26, 82, 118, 0.1);
        }
        
        /* ===== USER PROFILE IN NAVIGATION ===== */
        .user-profile-nav {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 20px;
            transition: all 0.3s;
            margin-right: 15px;
        }
        .user-profile-nav:hover {
            background-color: var(--light-bg);
        }
        .user-avatar-nav {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary-color);
        }
        .user-info-nav h4 {
            margin: 0;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--dark-text);
        }
        .user-info-nav p {
            margin: 0;
            font-size: 0.8rem;
            color: #666;
        }
        .dropdown-toggle {
            background: none;
            border: none;
            color: var(--dark-text);
            padding: 0;
            font-size: 0.8rem;
        }
        .dropdown-toggle::after {
            display: none;
        }
        .dropdown-menu {
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            padding: 10px 0;
            min-width: 150px;
        }
        .dropdown-item {
            padding: 10px 20px;
            transition: all 0.3s;
        }
        .dropdown-item:hover {
            background-color: var(--light-bg);
            color: var(--primary-color);
        }
        .dropdown-item i {
            margin-right: 10px;
            width: 16px;
            text-align: center;
        }
        .dropdown-divider {
            margin: 10px 0;
        }
        .notification-icon {
            position: relative;
            font-size: 1.2rem;
            color: var(--dark-text);
            cursor: pointer;
            margin-right: 15px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }
        .notification-icon:hover {
            background-color: rgba(26, 82, 118, 0.1);
        }
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--emergency-color);
            color: white;
            font-size: 0.7rem;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(231, 76, 60, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(231, 76, 60, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(231, 76, 60, 0);
            }
        }
        .emergency-btn {
            background: linear-gradient(135deg, var(--emergency-color), #c0392b);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
            transition: all 0.3s;
            margin-left: 10px;
            box-shadow: 0 4px 8px rgba(231, 76, 60, 0.3);
            display: flex;
            align-items: center;
        }
        .emergency-btn:hover {
            background: linear-gradient(135deg, #c0392b, var(--emergency-color));
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(231, 76, 60, 0.4);
        }
        
        /* ===== SIDEBAR ===== */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 20px 0;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            z-index: 1002;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }
        .sidebar.active {
            transform: translateX(0);
        }
        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .sidebar-logo {
            font-size: 1.8rem;
            font-weight: 700;
            display: flex;
            align-items: center;
        }
        .sidebar-logo i {
            margin-right: 10px;
            font-size: 1.5rem;
            color: var(--emergency-color);
        }
        .close-sidebar {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 5px;
            border-radius: 50%;
            transition: all 0.3s;
        }
        .close-sidebar:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: rotate(90deg);
        }
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar-menu li {
            margin-bottom: 5px;
        }
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
            position: relative;
        }
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }
        .sidebar-menu a.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background-color: white;
        }
        .sidebar-menu i {
            margin-right: 15px;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }
        .sidebar-footer {
            position: absolute;
            bottom: 20px;
            left: 0;
            right: 0;
            padding: 0 20px;
            text-align: center;
        }
        .sidebar-footer a {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .sidebar-footer a:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .sidebar-footer i {
            margin-right: 8px;
        }
        
        /* ===== MAIN CONTENT ===== */
        .main-content {
            padding: 20px;
            transition: all 0.3s ease;
            flex: 1;
            margin-left: 0;
        }
        .main-content.nav-hidden {
            margin-top: 0;
        }
        .main-content.sidebar-active {
            margin-left: var(--sidebar-width);
        }
        
        /* ===== PROFILE SUMMARY ===== */
        .profile-summary {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: var(--card-shadow);
            display: flex;
            align-items: center;
            gap: 30px;
            flex-wrap: wrap;
        }
        .profile-pic-container {
            flex-shrink: 0;
        }
        .profile-pic {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--primary-color);
        }
        .profile-info {
            flex: 1;
            min-width: 250px;
        }
        .profile-info h2 {
            color: var(--primary-color);
            margin-bottom: 10px;
            font-size: 1.8rem;
        }
        .profile-info p {
            color: #666;
            margin-bottom: 5px;
            font-size: 14px;
        }
        .profile-details {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 15px;
        }
        .profile-detail-item {
            display: flex;
            align-items: center;
            gap: 10px;
            background-color: rgba(26, 82, 118, 0.05);
            padding: 8px 15px;
            border-radius: 20px;
        }
        .profile-detail-item i {
            color: var(--primary-color);
            font-size: 16px;
        }
        .profile-detail-item span {
            font-size: 14px;
            font-weight: 500;
        }
        .change-photo-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 10px;
        }
        .change-photo-btn:hover {
            background-color: var(--secondary-color);
        }
        
        /* ===== PROFILE SETTINGS ===== */
        .profile-header {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: var(--card-shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .profile-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-color);
            margin: 0;
            display: flex;
            align-items: center;
        }
        .profile-title i {
            margin-right: 10px;
            color: var(--accent-color);
        }
        .profile-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .profile-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            border-top: 4px solid transparent;
        }
        .profile-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }
        .profile-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        }
        .profile-card.primary::before {
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        }
        .profile-card.success::before {
            background: linear-gradient(90deg, var(--accent-color), #2ecc71);
        }
        .profile-card.danger::before {
            background: linear-gradient(90deg, var(--emergency-color), #e74c3c);
        }
        .profile-card.warning::before {
            background: linear-gradient(90deg, #f39c12, #f1c40f);
        }
        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .card-header i {
            margin-right: 10px;
            font-size: 1.2rem;
        }
        .card-header h3 {
            margin: 0;
            font-size: 1.1rem;
            color: var(--primary-color);
        }
        .required::after {
            content: " *";
            color: var(--emergency-color);
        }
        .photo-upload {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .photo-circle {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background-color: #333;
            margin-bottom: 15px;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            position: relative;
        }
        .photo-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .photo-circle i {
            color: white;
            font-size: 40px;
        }
        .upload-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        .upload-btn:hover {
            background-color: var(--secondary-color);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(26, 82, 118, 0.2);
        }
        .btn {
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            border: none;
            transition: all 0.3s;
        }
        .btn-cancel {
            background-color: #e0e0e0;
            color: #333;
        }
        .btn-cancel:hover {
            background-color: #d0d0d0;
        }
        .btn-update {
            background-color: var(--emergency-color);
            color: white;
            padding: 10px 20px;
            font-weight: 500;
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
        .btn-update:hover {
            background-color: #c0392b;
        }
        
        /* ===== PREVIOUS RECORDS ===== */
        .previous-records {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: var(--card-shadow);
        }
        .previous-records h3 {
            color: var(--primary-color);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
        }
        .previous-records h3 i {
            margin-right: 10px;
            color: var(--accent-color);
        }
        .record-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
        }
        .record-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-radius: 10px;
            background-color: rgba(26, 82, 118, 0.03);
            transition: all 0.3s;
        }
        .record-item:hover {
            background-color: rgba(26, 82, 118, 0.08);
            transform: translateY(-3px);
        }
        .record-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(26, 82, 118, 0.1), rgba(41, 128, 185, 0.2));
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            color: var(--primary-color);
            flex-shrink: 0;
        }
        .record-details {
            flex: 1;
        }
        .record-name {
            font-weight: 600;
            color: var(--dark-text);
            margin-bottom: 3px;
        }
        .record-date {
            font-size: 12px;
            color: #666;
        }
        .record-status {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
        }
        .status-completed {
            background-color: rgba(39, 174, 96, 0.1);
            color: var(--accent-color);
        }
        .status-pending {
            background-color: rgba(243, 156, 18, 0.1);
            color: #f39c12;
        }
        
        /* ===== OVERLAY ===== */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1001;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .overlay.active {
            display: block;
            opacity: 1;
        }
        
        /* ===== NAVIGATION TOGGLE BUTTONS ===== */
        .nav-toggle-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1003;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            transition: all 0.3s;
        }
        .nav-toggle-btn:hover {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            transform: scale(1.1);
        }
        .nav-toggle-btn.hidden {
            display: none;
        }
        .show-nav-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1003;
            background: linear-gradient(135deg, var(--accent-color), #2ecc71);
            color: white;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: none;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            transition: all 0.3s;
        }
        .show-nav-btn:hover {
            background: linear-gradient(135deg, #2ecc71, var(--accent-color));
            transform: scale(1.1);
        }
        .show-nav-btn.visible {
            display: flex;
        }
        
        /* ===== LANGUAGE SWITCHING ===== */
        .lang-text {
            display: inline;
        }
        .lang-text.bn {
            display: none;
        }
        body.bn .lang-text.en {
            display: none;
        }
        body.bn .lang-text.bn {
            display: inline;
        }
        
        /* ===== FOOTER ===== */
        footer {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 40px 0 20px;
            margin-top: auto;
        }
        .footer-logo {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: white;
            display: flex;
            align-items: center;
        }
        .footer-logo i {
            margin-right: 10px;
            color: var(--emergency-color);
        }
        .footer-links h5 {
            font-size: 1.2rem;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
        }
        .footer-links h5:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background-color: var(--accent-color);
        }
        .footer-links ul {
            list-style: none;
            padding: 0;
        }
        .footer-links ul li {
            margin-bottom: 10px;
        }
        .footer-links ul li a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
        }
        .footer-links ul li a:hover {
            color: white;
            padding-left: 5px;
        }
        .footer-links ul li a i {
            margin-right: 8px;
            font-size: 0.9rem;
        }
        .footer-contact li {
            margin-bottom: 15px;
            display: flex;
            align-items: flex-start;
        }
        .footer-contact i {
            margin-right: 10px;
            margin-top: 5px;
        }
        .footer-newsletter p {
            margin-bottom: 20px;
        }
        .newsletter-form {
            display: flex;
        }
        .newsletter-input {
            flex: 1;
            padding: 10px 15px;
            border: none;
            border-radius: 5px 0 0 5px;
        }
        .newsletter-btn {
            background-color: var(--accent-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 0 5px 5px 0;
            font-weight: 500;
            transition: all 0.3s;
        }
        .newsletter-btn:hover {
            background-color: #229954;
        }
        .social-icons-footer a {
            display: inline-block;
            width: 40px;
            height: 40px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            text-align: center;
            line-height: 40px;
            margin-right: 10px;
            color: white;
            transition: all 0.3s;
        }
        .social-icons-footer a:hover {
            background-color: var(--accent-color);
            transform: translateY(-3px);
        }
        .copyright {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
            margin-top: 40px;
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
        }
        
        /* ===== RESPONSIVE ===== */
        @media (max-width: 992px) {
            .profile-cards {
                grid-template-columns: 1fr;
            }
            .record-list {
                grid-template-columns: 1fr;
            }
            .profile-summary {
                flex-direction: column;
                text-align: center;
            }
            .profile-details {
                justify-content: center;
            }
        }
        @media (max-width: 768px) {
            .contact-info span {
                display: block;
                margin-bottom: 5px;
            }
            .sidebar {
                width: 100%;
                max-width: var(--sidebar-width);
            }
            .photo-circle {
                width: 120px;
                height: 120px;
            }
            .footer-links {
                margin-bottom: 30px;
            }
        }
    </style>
</head>
<body>
    <!-- Top Header -->
    <div class="top-header" id="topHeader">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="contact-info">
                        <span><i class="fas fa-phone-alt"></i> <span class="lang-text en">+880 1234 567890</span><span class="lang-text bn">+৮৮০ ১২৩৪ ৫৬৭৮৯০</span></span>
                        <span><i class="fas fa-envelope"></i> <span class="lang-text en">info@amraaichi.com</span><span class="lang-text bn">info@amraaichi.com</span></span>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <button class="lang-toggle" id="langToggle">
                        <span class="lang-text en">বাংলা</span>
                        <span class="lang-text bn">English</span>
                    </button>
                    <div class="social-icons d-inline-block ms-3">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Main Header -->
    <header class="main-header" id="mainHeader">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <button class="menu-toggle" id="menuToggle" aria-label="Toggle navigation">
                        <i class="fas fa-bars"></i>
                    </button>
                    <a class="navbar-brand" href="index.html">
                        <i class="fas fa-heartbeat"></i>
                        <span class="lang-text en">AmraAchi</span>
                        <span class="lang-text bn">আমরাআছি</span>
                    </a>
                </div>
                <div class="d-flex align-items-center">
                    <div class="notification-icon">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">3</span>
                    </div>
                    <div class="user-profile-nav">
                        <img src="<?php echo htmlspecialchars(!empty($profile['photo']) ? $profile['photo'] : 'https://randomuser.me/api/portraits/women/44.jpg'); ?>" alt="User" class="user-avatar-nav" id="headerAvatar">
                        <div class="user-info-nav">
                            <h4 id="headerName"><?php echo htmlspecialchars($profile['full_name'] ?: ''); ?></h4>
                            <p id="headerRole"><?php echo htmlspecialchars(ucfirst($_SESSION['user_role'] ?? 'Patient')); ?></p>
                        </div>
                        <div class="dropdown">
                            <button class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i> <span class="lang-text en">Profile</span><span class="lang-text bn">প্রোফাইল</span></a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> <span class="lang-text en">Settings</span><span class="lang-text bn">সেটিংস</span></a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> <span class="lang-text en">Logout</span><span class="lang-text bn">লগআউট</span></a></li>
                            </ul>
                        </div>
                    </div>
                    <button class="emergency-btn">
                        <i class="fas fa-ambulance me-2"></i>
                        <span class="lang-text en">SOS</span>
                        <span class="lang-text bn">এসওএস</span>
                    </button>
                </div>
            </div>
        </div>
    </header>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar" aria-label="Main navigation">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <i class="fas fa-heartbeat"></i>
                <span class="lang-text en">AmraAchi</span>
                <span class="lang-text bn">আমরাআছি</span>
            </div>
            <button class="close-sidebar" id="closeSidebar" aria-label="Close navigation">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <nav>
            <ul class="sidebar-menu">
                <li><a href="#"><i class="fas fa-home"></i> <span class="lang-text en">Dashboard</span><span class="lang-text bn">ড্যাশবোর্ড</span></a></li>
                <li><a href="#"><i class="fas fa-calendar-check"></i> <span class="lang-text en">Appointments</span><span class="lang-text bn">অ্যাপয়েন্টমেন্ট</span></a></li>
                <li><a href="#"><i class="fas fa-user-md"></i> <span class="lang-text en">Find Doctors</span><span class="lang-text bn">ডাক্তার খুঁজুন</span></a></li>
                <li><a href="#"><i class="fas fa-file-medical"></i> <span class="lang-text en">Health Records</span><span class="lang-text bn">স্বাস্থ্য রেকর্ড</span></a></li>
                <li><a href="#"><i class="fas fa-pills"></i> <span class="lang-text en">Prescriptions</span><span class="lang-text bn">প্রেসক্রিপশন</span></a></li>
                <li><a href="#"><i class="fas fa-hospital"></i> <span class="lang-text en">Hospitals</span><span class="lang-text bn">হাসপাতাল</span></a></li>
                <li><a href="#"><i class="fas fa-ambulance"></i> <span class="lang-text en">Emergency</span><span class="lang-text bn">জরুরি</span></a></li>
                <li><a href="#" class="active"><i class="fas fa-user"></i> <span class="lang-text en">Profile</span><span class="lang-text bn">প্রোফাইল</span></a></li>
                <li><a href="#"><i class="fas fa-cog"></i> <span class="lang-text en">Settings</span><span class="lang-text bn">সেটিংস</span></a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a href="#" id="hideNavBtn"><i class="fas fa-eye-slash"></i> <span class="lang-text en">Hide Navigation</span><span class="lang-text bn">নেভিগেশন লুকান</span></a></li>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <a href="#"><i class="fas fa-sign-out-alt"></i> <span class="lang-text en">Logout</span><span class="lang-text bn">লগআউট</span></a>
        </div>
    </aside>
    <!-- Overlay -->
    <div class="overlay" id="overlay" aria-label="Sidebar overlay"></div>
    <!-- Navigation Toggle Button -->
    <button class="nav-toggle-btn" id="navToggleBtn" title="Hide Navigation">
        <i class="fas fa-eye-slash"></i>
    </button>
    <!-- Show Navigation Button -->
    <button class="show-nav-btn" id="showNavBtn" title="Show Navigation">
        <i class="fas fa-eye"></i>
    </button>
    <!-- Main Content -->
    <main class="main-content sidebar-active" id="mainContent">
        <!-- Profile Summary -->
        <div class="profile-summary">
            <div class="profile-pic-container">
                <img src="<?php echo htmlspecialchars(!empty($profile['photo']) ? $profile['photo'] : 'https://randomuser.me/api/portraits/women/44.jpg'); ?>" alt="Profile" class="profile-pic" id="profilePic">
                <input type="file" id="photoInput" accept="image/*" style="display: none;">
                <input type="hidden" id="photoPath" value="<?php echo htmlspecialchars($profile['photo'] ?? ''); ?>">
                <button class="change-photo-btn" onclick="document.getElementById('photoInput').click()">
                    <span class="lang-text en">Change Photo</span>
                    <span class="lang-text bn">ছবি পরিবর্তন করুন</span>
                </button>
            </div>
            <div class="profile-info">
                <h2 id="profileName"><?php echo htmlspecialchars($profile['full_name'] ?: ''); ?></h2>
                <p><span class="lang-text en">Patient ID: </span><span class="lang-text bn">রোগী আইডি: </span>PT-<?php echo str_pad($user_id, 4, '0', STR_PAD_LEFT); ?></p>
                <p><span class="lang-text en">Member since: </span><span class="lang-text bn">সদস্য হয়েছেন: </span><?php // created_at is not loaded here ?></p>
                <div class="profile-details">
                    <div class="profile-detail-item">
                        <i class="fas fa-home"></i>
                        <span id="profileAddress"><?php echo htmlspecialchars($profile['address'] ?: ''); ?></span>
                    </div>
                    <div class="profile-detail-item">
                        <i class="fas fa-tint"></i>
                        <span id="profileBloodGroup"><?php echo htmlspecialchars($profile['blood_group'] ?: ''); ?></span>
                    </div>
                    <div class="profile-detail-item">
                        <i class="fas fa-phone-alt"></i>
                        <span id="profilePhone"><?php echo htmlspecialchars($profile['phone'] ?: ''); ?></span>
                    </div>
                    <div class="profile-detail-item">
                        <i class="fas fa-envelope"></i>
                        <span id="profileEmail"><?php echo htmlspecialchars($profile['email'] ?: ''); ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="profile-header">
            <h1 class="profile-title">
                <i class="fas fa-user-edit"></i>
                <span class="lang-text en">Profile Settings</span>
                <span class="lang-text bn">প্রোফাইল সেটিংস</span>
            </h1>
            <button class="btn btn-outline-primary" id="hideNavBtn2">
                <i class="fas fa-eye-slash me-2"></i>
                <span class="lang-text en">Hide Navigation</span>
                <span class="lang-text bn">নেভিগেশন লুকান</span>
            </button>
        </div>
        
        <div class="profile-cards">
            <!-- Present Address Card -->
            <div class="profile-card success">
                <div class="card-header">
                    <i class="fas fa-home"></i>
                    <h3 class="required"><span class="lang-text en">Present Address</span><span class="lang-text bn">বর্তমান ঠিকানা</span></h3>
                </div>
                <div class="form-group">
                    <label for="address"><span class="lang-text en">Address</span><span class="lang-text bn">ঠিকানা</span></label>
                    <input type="text" id="address" class="form-control" value="<?php echo htmlspecialchars($profile['address'] ?: ''); ?>" data-placeholder-en="Enter your address" data-placeholder-bn="আপনার ঠিকানা লিখুন" placeholder="Enter your address">
                </div>
                <div class="form-group">
                    <label for="houseNo"><span class="lang-text en">House no</span><span class="lang-text bn">বাড়ি নম্বর</span></label>
                    <input type="text" id="houseNo" class="form-control" value="<?php echo htmlspecialchars($profile['house_no'] ?: ''); ?>" data-placeholder-en="Enter house number" data-placeholder-bn="বাড়ি নম্বর লিখুন" placeholder="Enter house number">
                </div>
                <div class="form-group">
                    <label for="city"><span class="lang-text en">City</span><span class="lang-text bn">শহর</span></label>
                    <input type="text" id="city" class="form-control" value="<?php echo htmlspecialchars($profile['city'] ?: ''); ?>" data-placeholder-en="Enter city" data-placeholder-bn="শহর লিখুন" placeholder="Enter city">
                </div>
                <div class="form-group">
                    <label for="postalCode"><span class="lang-text en">Postal Code</span><span class="lang-text bn">পোস্টাল কোড</span></label>
                    <input type="text" id="postalCode" class="form-control" value="<?php echo htmlspecialchars($profile['postal_code'] ?: ''); ?>" data-placeholder-en="Enter postal code" data-placeholder-bn="পোস্টাল কোড লিখুন" placeholder="Enter postal code">
                </div>
                <div style="text-align: right;">
                    <button class="btn btn-cancel">
                        <span class="lang-text en">Cancel</span>
                        <span class="lang-text bn">বাতিল করুন</span>
                    </button>
                </div>
            </div>
            
            <!-- Blood Info Card -->
            <div class="profile-card danger">
                <div class="card-header">
                    <i class="fas fa-tint"></i>
                    <h3><span class="lang-text en">Blood Info</span><span class="lang-text bn">রক্তের তথ্য</span></h3>
                </div>
                <div class="form-group">
                    <label for="bloodGroup"><span class="lang-text en">Blood Group</span><span class="lang-text bn">রক্তের গ্রুপ</span></label>
                    <select id="bloodGroup" class="form-control">
                        <option value="" data-option-en="Select blood group" data-option-bn="রক্তের গ্রুপ নির্বাচন করুন"><?php echo ''; ?></option>
                        <option value="A+" data-option-en="A+" data-option-bn="A+">A+</option>
                        <option value="A-" data-option-en="A-" data-option-bn="A-">A-</option>
                        <option value="B+" data-option-en="B+" data-option-bn="B+">B+</option>
                        <option value="B-" data-option-en="B-" data-option-bn="B-">B-</option>
                        <option value="AB+" data-option-en="AB+" data-option-bn="AB+">AB+</option>
                        <option value="AB-" data-option-en="AB-" data-option-bn="AB-">AB-</option>
                        <option value="O+" <?php echo ($profile['blood_group']==='O+' ? 'selected' : ''); ?> data-option-en="O+" data-option-bn="O+">O+</option>
                        <option value="O-" data-option-en="O-" data-option-bn="O-">O-</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="lastDonation"><span class="lang-text en">Last Donation</span><span class="lang-text bn">শেষ রক্তদান</span></label>
                    <input type="date" id="lastDonation" class="form-control" value="<?php echo htmlspecialchars($profile['last_donation'] ?: ''); ?>">
                </div>
                <div class="form-group">
                    <label for="donationCount"><span class="lang-text en">Donation Count</span><span class="lang-text bn">রক্তদানের সংখ্যা</span></label>
                    <input type="number" id="donationCount" class="form-control" value="<?php echo htmlspecialchars($profile['donation_count'] ?: ''); ?>" data-placeholder-en="Enter donation count" data-placeholder-bn="রক্তদানের সংখ্যা লিখুন" placeholder="Enter donation count">
                </div>
                <div style="text-align: right;">
                    <button class="btn btn-cancel">
                        <span class="lang-text en">Cancel</span>
                        <span class="lang-text bn">বাতিল করুন</span>
                    </button>
                </div>
            </div>
            
            <!-- Additional Info Card -->
            <div class="profile-card warning">
                <div class="card-header">
                    <i class="fas fa-info-circle"></i>
                    <h3><span class="lang-text en">Additional Info</span><span class="lang-text bn">অতিরিক্ত তথ্য</span></h3>
                </div>
                <div class="form-group">
                    <label for="phone"><span class="lang-text en">Phone Number</span><span class="lang-text bn">ফোন নম্বর</span></label>
                    <input type="tel" id="phone" class="form-control" value="<?php echo htmlspecialchars($profile['phone'] ?: ''); ?>" data-placeholder-en="Enter phone number" data-placeholder-bn="ফোন নম্বর লিখুন" placeholder="Enter phone number">
                </div>
                <div class="form-group">
                    <label for="email"><span class="lang-text en">Email</span><span class="lang-text bn">ইমেইল</span></label>
                    <input type="email" id="email" class="form-control" value="<?php echo htmlspecialchars($profile['email'] ?: ''); ?>" data-placeholder-en="Enter email" data-placeholder-bn="ইমেইল লিখুন" placeholder="Enter email">
                </div>
                <div class="form-group">
                    <label for="emergencyContact"><span class="lang-text en">Emergency Contact</span><span class="lang-text bn">জরুরি যোগাযোগ</span></label>
                    <input type="text" id="emergencyContact" class="form-control" value="<?php echo htmlspecialchars($profile['emergency_contact'] ?: ''); ?>" data-placeholder-en="Enter emergency contact" data-placeholder-bn="জরুরি যোগাযোগ লিখুন" placeholder="Enter emergency contact">
                </div>
                <div style="text-align: right;">
                    <button class="btn btn-cancel">
                        <span class="lang-text en">Cancel</span>
                        <span class="lang-text bn">বাতিল করুন</span>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Previous Records -->
        <div class="previous-records">
            <h3>
                <i class="fas fa-history"></i>
                <span class="lang-text en">Previously Updated Records</span>
                <span class="lang-text bn">পূর্বে আপডেট করা রেকর্ড</span>
            </h3>
            <div class="record-list">
                <div class="record-item">
                    <div class="record-icon">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <div class="record-details">
                        <div class="record-name"><span class="lang-text en">Blood Test</span><span class="lang-text bn">রক্ত পরীক্ষা</span></div>
                        <div class="record-date"><span class="lang-text en">10 Dec 2023</span><span class="lang-text bn">১০ ডিসেম্বর ২০২৩</span></div>
                    </div>
                    <span class="record-status status-completed"><span class="lang-text en">Completed</span><span class="lang-text bn">সম্পন্ন</span></span>
                </div>
                <div class="record-item">
                    <div class="record-icon">
                        <i class="fas fa-x-ray"></i>
                    </div>
                    <div class="record-details">
                        <div class="record-name"><span class="lang-text en">X-Ray</span><span class="lang-text bn">এক্স-রে</span></div>
                        <div class="record-date"><span class="lang-text en">5 Dec 2023</span><span class="lang-text bn">৫ ডিসেম্বর ২০২৩</span></div>
                    </div>
                    <span class="record-status status-completed"><span class="lang-text en">Completed</span><span class="lang-text bn">সম্পন্ন</span></span>
                </div>
                <div class="record-item">
                    <div class="record-icon">
                        <i class="fas fa-microscope"></i>
                    </div>
                    <div class="record-details">
                        <div class="record-name"><span class="lang-text en">Urine Test</span><span class="lang-text bn">মূত্র পরীক্ষা</span></div>
                        <div class="record-date"><span class="lang-text en">28 Nov 2023</span><span class="lang-text bn">২৮ নভেম্বর ২০২৩</span></div>
                    </div>
                    <span class="record-status status-completed"><span class="lang-text en">Completed</span><span class="lang-text bn">সম্পন্ন</span></span>
                </div>
                <div class="record-item">
                    <div class="record-icon">
                        <i class="fas fa-file-prescription"></i>
                    </div>
                    <div class="record-details">
                        <div class="record-name"><span class="lang-text en">Prescription</span><span class="lang-text bn">প্রেসক্রিপশন</span></div>
                        <div class="record-date"><span class="lang-text en">20 Nov 2023</span><span class="lang-text bn">২০ নভেম্বর ২০২৩</span></div>
                    </div>
                    <span class="record-status status-completed"><span class="lang-text en">Completed</span><span class="lang-text bn">সম্পন্ন</span></span>
                </div>
                <div class="record-item">
                    <div class="record-icon">
                        <i class="fas fa-procedures"></i>
                    </div>
                    <div class="record-details">
                        <div class="record-name"><span class="lang-text en">General Checkup</span><span class="lang-text bn">সাধারণ চেকআপ</span></div>
                        <div class="record-date"><span class="lang-text en">15 Nov 2023</span><span class="lang-text bn">১৫ নভেম্বর ২০২৩</span></div>
                    </div>
                    <span class="record-status status-completed"><span class="lang-text en">Completed</span><span class="lang-text bn">সম্পন্ন</span></span>
                </div>
                <div class="record-item">
                    <div class="record-icon">
                        <i class="fas fa-tooth"></i>
                    </div>
                    <div class="record-details">
                        <div class="record-name"><span class="lang-text en">Dental Checkup</span><span class="lang-text bn">দন্ত চেকআপ</span></div>
                        <div class="record-date"><span class="lang-text en">1 Nov 2023</span><span class="lang-text bn">১ নভেম্বর ২০২৩</span></div>
                    </div>
                    <span class="record-status status-completed"><span class="lang-text en">Completed</span><span class="lang-text bn">সম্পন্ন</span></span>
                </div>
                <div class="record-item">
                    <div class="record-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="record-details">
                        <div class="record-name"><span class="lang-text en">Eye Test</span><span class="lang-text bn">চোখের পরীক্ষা</span></div>
                        <div class="record-date"><span class="lang-text en">25 Oct 2023</span><span class="lang-text bn">২৫ অক্টোবর ২০২৩</span></div>
                    </div>
                    <span class="record-status status-completed"><span class="lang-text en">Completed</span><span class="lang-text bn">সম্পন্ন</span></span>
                </div>
                <div class="record-item">
                    <div class="record-icon">
                        <i class="fas fa-vial"></i>
                    </div>
                    <div class="record-details">
                        <div class="record-name"><span class="lang-text en">Vaccination</span><span class="lang-text bn">টিকাদান</span></div>
                        <div class="record-date"><span class="lang-text en">10 Oct 2023</span><span class="lang-text bn">১০ অক্টোবর ২০২৩</span></div>
                    </div>
                    <span class="record-status status-completed"><span class="lang-text en">Completed</span><span class="lang-text bn">সম্পন্ন</span></span>
                </div>
            </div>
        </div>
        
        <!-- Update Button -->
        <button class="btn btn-update" id="updateAllBtn">
            <i class="fas fa-save me-2"></i>
            <span class="lang-text en">Update All</span>
            <span class="lang-text bn">সব আপডেট করুন</span>
        </button>
    </main>
    
    <!-- Footer -->
    <footer id="contact">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="footer-logo">
                        <i class="fas fa-heartbeat"></i>
                        <span class="lang-text en">AmraAchi</span>
                        <span class="lang-text bn">আমরাআছি</span>
                    </div>
                    <p><span class="lang-text en">Your complete digital healthcare platform connecting patients, doctors, and healthcare services for better health outcomes.</span><span class="lang-text bn">আপনার সম্পূর্ণ ডিজিটাল স্বাস্থ্যসেবা প্ল্যাটফর্ম যা রোগী, ডাক্তার এবং স্বাস্থ্যসেবা সেবাকে উন্নত স্বাস্থ্য ফলাফলের জন্য সংযুক্ত করে।</span></p>
                    <div class="social-icons-footer">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <div class="footer-links">
                        <h5><span class="lang-text en">Quick Links</span><span class="lang-text bn">দ্রুত লিঙ্ক</span></h5>
                        <ul>
                            <li><a href="#"><i class="fas fa-home"></i> <span class="lang-text en">Home</span><span class="lang-text bn">হোম</span></a></li>
                            <li><a href="#"><i class="fas fa-info-circle"></i> <span class="lang-text en">About Us</span><span class="lang-text bn">আমাদের সম্পর্কে</span></a></li>
                            <li><a href="#"><i class="fas fa-stethoscope"></i> <span class="lang-text en">Services</span><span class="lang-text bn">সেবা</span></a></li>
                            <li><a href="#"><i class="fas fa-hospital"></i> <span class="lang-text en">Departments</span><span class="lang-text bn">বিভাগ</span></a></li>
                            <li><a href="#"><i class="fas fa-user-md"></i> <span class="lang-text en">Doctors</span><span class="lang-text bn">ডাক্তার</span></a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <div class="footer-links">
                        <h5><span class="lang-text en">Services</span><span class="lang-text bn">সেবা</span></h5>
                        <ul>
                            <li><a href="#"><i class="fas fa-ambulance"></i> <span class="lang-text en">Emergency Care</span><span class="lang-text bn">জরুরি যত্ন</span></a></li>
                            <li><a href="#"><i class="fas fa-calendar-check"></i> <span class="lang-text en">Appointments</span><span class="lang-text bn">অ্যাপয়েন্টমেন্ট</span></a></li>
                            <li><a href="#"><i class="fas fa-file-medical"></i> <span class="lang-text en">Health Records</span><span class="lang-text bn">স্বাস্থ্য রেকর্ড</span></a></li>
                            <li><a href="#"><i class="fas fa-user-nurse"></i> <span class="lang-text en">Home Care</span><span class="lang-text bn">হোম কেয়ার</span></a></li>
                            <li><a href="#"><i class="fas fa-pills"></i> <span class="lang-text en">E-Prescriptions</span><span class="lang-text bn">ই-প্রেসক্রিপশন</span></a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="footer-links">
                        <h5><span class="lang-text en">Contact Us</span><span class="lang-text bn">যোগাযোগ করুন</span></h5>
                        <ul class="footer-contact">
                            <li>
                                <i class="fas fa-map-marker-alt"></i>
                                <span><span class="lang-text en">123 Healthcare Ave, Dhaka, Bangladesh</span><span class="lang-text bn">১২৩ হেলথকেয়ার অ্যাভিনিউ, ঢাকা, বাংলাদেশ</span></span>
                            </li>
                            <li>
                                <i class="fas fa-phone-alt"></i>
                                <span><span class="lang-text en">+880 1234 567890</span><span class="lang-text bn">+৮৮০ ১২৩৪ ৫৬৭৮৯০</span></span>
                            </li>
                            <li>
                                <i class="fas fa-envelope"></i>
                                <span><span class="lang-text en">info@amraaichi.com</span><span class="lang-text bn">info@amraaichi.com</span></span>
                            </li>
                            <li>
                                <i class="fas fa-clock"></i>
                                <span><span class="lang-text en">Mon-Fri: 9am-6pm</span><span class="lang-text bn">সোম-শুক্র: সকাল ৯টা-সন্ধ্যা ৬টা</span></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="footer-newsletter">
                        <h5><span class="lang-text en">Subscribe to Our Newsletter</span><span class="lang-text bn">আমাদের নিউজলেটার সাবস্ক্রাইব করুন</span></h5>
                        <p><span class="lang-text en">Stay updated with our latest news and health tips</span><span class="lang-text bn">আমাদের সর্বশেষ খবর এবং স্বাস্থ্য টিপস দিয়ে আপডেট থাকুন</span></p>
                        <form class="newsletter-form">
                            <input type="email" class="newsletter-input" placeholder="Your email address">
                            <button type="submit" class="newsletter-btn"><span class="lang-text en">Subscribe</span><span class="lang-text bn">সাবস্ক্রাইব</span></button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="copyright">
                <p><span class="lang-text en">&copy; 2023 AmraAchi. All rights reserved.</span><span class="lang-text bn">&copy; ২০২৩ আমরাআছি। সর্বস্বত্ব সংরক্ষিত।</span></p>
            </div>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Wait for the DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Get all the elements
            const menuToggle = document.getElementById('menuToggle');
            const closeSidebar = document.getElementById('closeSidebar');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            const topHeader = document.getElementById('topHeader');
            const mainHeader = document.getElementById('mainHeader');
            const mainContent = document.getElementById('mainContent');
            const navToggleBtn = document.getElementById('navToggleBtn');
            const showNavBtn = document.getElementById('showNavBtn');
            const hideNavBtn = document.getElementById('hideNavBtn');
            const hideNavBtn2 = document.getElementById('hideNavBtn2');
            const langToggle = document.getElementById('langToggle');
            const updateAllBtn = document.getElementById('updateAllBtn');
            
            // Function to open sidebar
            function openSidebar() {
                sidebar.classList.add('active');
                overlay.classList.add('active');
                document.body.style.overflow = 'hidden'; // Prevent scrolling when sidebar is open
            }
            
            // Function to close sidebar
            function closeSidebarFunc() {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
                document.body.style.overflow = 'auto'; // Restore scrolling
            }
            
            // Function to hide navigation
            function hideNavigation() {
                topHeader.classList.add('hidden');
                mainHeader.classList.add('hidden');
                navToggleBtn.classList.add('hidden');
                showNavBtn.classList.add('visible');
                mainContent.classList.remove('sidebar-active');
                closeSidebarFunc(); // Also close sidebar if open
            }
            
            // Function to show navigation
            function showNavigation() {
                topHeader.classList.remove('hidden');
                mainHeader.classList.remove('hidden');
                navToggleBtn.classList.remove('hidden');
                showNavBtn.classList.remove('visible');
                mainContent.classList.add('sidebar-active');
            }
            
            // Function to update profile summary
            function updateProfileSummary() {
                // Get form values
                const address = document.getElementById('address').value;
                const city = document.getElementById('city').value;
                const bloodGroup = document.getElementById('bloodGroup').value;
                const phone = document.getElementById('phone').value;
                const email = document.getElementById('email').value;
                
                // Update profile summary elements
                if (address && city) {
                    document.getElementById('profileAddress').innerHTML = `${address}, ${city}`;
                }
                
                if (bloodGroup) {
                    document.getElementById('profileBloodGroup').textContent = bloodGroup;
                }
                
                if (phone) {
                    document.getElementById('profilePhone').textContent = phone;
                }
                
                if (email) {
                    document.getElementById('profileEmail').textContent = email;
                }
            }
            
            // Add event listener to menu toggle button
            if (menuToggle) {
                menuToggle.addEventListener('click', openSidebar);
            }
            
            // Add event listener to close sidebar button
            if (closeSidebar) {
                closeSidebar.addEventListener('click', closeSidebarFunc);
            }
            
            // Add event listener to overlay
            if (overlay) {
                overlay.addEventListener('click', closeSidebarFunc);
            }
            
            // Add event listeners to hide navigation buttons
            if (hideNavBtn) {
                hideNavBtn.addEventListener('click', hideNavigation);
            }
            
            if (hideNavBtn2) {
                hideNavBtn2.addEventListener('click', hideNavigation);
            }
            
            // Add event listener to show navigation button
            if (showNavBtn) {
                showNavBtn.addEventListener('click', showNavigation);
            }
            
            // Language toggle functionality
            function applyPlaceholdersForLanguage(isBangla) {
                document.querySelectorAll('[data-placeholder-en]').forEach(function(el) {
                    try {
                        const en = el.getAttribute('data-placeholder-en');
                        const bn = el.getAttribute('data-placeholder-bn');
                        el.placeholder = isBangla && bn ? bn : (en || '');
                    } catch (e) {
                        // ignore
                    }
                });
                // Update select option texts that provide translations
                document.querySelectorAll('select').forEach(function(sel) {
                    if (!sel) return;
                    sel.querySelectorAll('option[data-option-en]').forEach(function(opt) {
                        try {
                            const en = opt.getAttribute('data-option-en');
                            const bn = opt.getAttribute('data-option-bn');
                            opt.textContent = (isBangla && bn) ? bn : (en || opt.textContent);
                        } catch (e) {}
                    });
                });
            }

            if (langToggle) {
                langToggle.addEventListener('click', function() {
                    const isNowBangla = document.body.classList.toggle('bn');
                    applyPlaceholdersForLanguage(isNowBangla);
                });
            }

            // Apply placeholders on load according to current language
            applyPlaceholdersForLanguage(document.body.classList.contains('bn'));
            
            // Close sidebar when clicking on a link (optional, for better UX)
            const sidebarLinks = document.querySelectorAll('.sidebar-menu a');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth <= 992) { // Only close on mobile
                        closeSidebarFunc();
                    }
                });
            });
            
            // Close sidebar on escape key press
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && sidebar.classList.contains('active')) {
                    closeSidebarFunc();
                }
                
                // Keyboard shortcut to toggle navigation (Ctrl+N)
                if (e.ctrlKey && e.key === 'n') {
                    e.preventDefault();
                    if (topHeader.classList.contains('hidden')) {
                        showNavigation();
                    } else {
                        hideNavigation();
                    }
                }
            });
            
            // Initialize main content with sidebar active
            mainContent.classList.add('sidebar-active');
            
            // Update profile summary when form values change
            const formInputs = document.querySelectorAll('.form-control');
            formInputs.forEach(input => {
                input.addEventListener('change', updateProfileSummary);
            });
        });
        
        // Photo upload functionality
        document.addEventListener('DOMContentLoaded', function() {
            const photoInput = document.getElementById('photoInput');
            const profilePic = document.getElementById('profilePic');
            
            if (photoInput) {
                photoInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        // Show local preview immediately
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            if (profilePic) profilePic.src = e.target.result;
                        }
                        reader.readAsDataURL(file);

                        // Upload to server
                        const fd = new FormData();
                        fd.append('photo', file);
                        fetch('upload_profile_photo.php', {
                            method: 'POST',
                            credentials: 'include',
                            body: fd
                        }).then(r => r.json()).then(data => {
                            if (data.success && data.url) {
                                document.getElementById('photoPath').value = data.url;
                                // Use server URL for stable preview
                                profilePic.src = data.url;
                                // Persist to localStorage so other pages can pick it up
                                try { localStorage.setItem('profilePhoto', data.url); } catch (e) {}
                                // Also update header avatar on this page if present
                                const headerAvatarEl = document.getElementById('headerAvatar');
                                if (headerAvatarEl) headerAvatarEl.src = data.url;
                            } else {
                                alert('Failed to upload photo: ' + (data.message || 'Unknown'));
                            }
                        }).catch(err => {
                            console.error(err);
                            alert('Network error uploading photo');
                        });
                    }
                });
            }
        });
        
        // Cancel button functionality
        document.addEventListener('DOMContentLoaded', function() {
            const cancelButtons = document.querySelectorAll('.btn-cancel');
            
            cancelButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Find the parent card
                    const card = this.closest('.profile-card');
                    
                    // Reset all form inputs in this card to their original values
                    const inputs = card.querySelectorAll('.form-control');
                    inputs.forEach(input => {
                        // Get the original value from the defaultValue property
                        if (input.defaultValue) {
                            input.value = input.defaultValue;
                        } else {
                            input.value = '';
                        }
                    });
                });
            });
        });
        
        // Update All button functionality
        document.addEventListener('DOMContentLoaded', function() {
            const updateAllBtn = document.getElementById('updateAllBtn');
            
            if (updateAllBtn) {
                updateAllBtn.addEventListener('click', async function() {
                    // Gather form values
                    const payload = {
                        full_name: document.getElementById('profileName').textContent.trim(),
                        address: document.getElementById('address').value.trim(),
                        house_no: document.getElementById('houseNo').value.trim(),
                        city: document.getElementById('city').value.trim(),
                        postal_code: document.getElementById('postalCode').value.trim(),
                        blood_group: document.getElementById('bloodGroup').value,
                        last_donation: document.getElementById('lastDonation').value,
                        donation_count: document.getElementById('donationCount').value,
                        phone: document.getElementById('phone').value.trim(),
                        email: document.getElementById('email').value.trim(),
                        emergency_contact: document.getElementById('emergencyContact').value.trim()
                    };

                    // Optimistically update UI
                    if (payload.address && payload.city) {
                        document.getElementById('profileAddress').textContent = payload.address + (payload.city ? (', ' + payload.city) : '');
                    }
                    if (payload.blood_group) {
                        document.getElementById('profileBloodGroup').textContent = payload.blood_group;
                    }
                    if (payload.phone) {
                        document.getElementById('profilePhone').textContent = payload.phone;
                    }
                    if (payload.email) {
                        document.getElementById('profileEmail').textContent = payload.email;
                    }

                    try {
                        const resp = await fetch('save_profile.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            credentials: 'include',
                            body: JSON.stringify(payload)
                        });
                        const result = await resp.json();
                        if (result.success) {
                            const isBangla = document.body.classList.contains('bn');
                            if (isBangla) {
                                alert('আপনার প্রোফাইল সফলভাবে সার্ভারে সংরক্ষিত হয়েছে।');
                            } else {
                                alert('Profile saved successfully.');
                            }
                        } else {
                            console.error(result);
                            alert('Unable to save profile: ' + (result.message || 'Unknown error'));
                        }
                    } catch (err) {
                        console.error(err);
                        alert('Network error while saving profile.');
                    }
                });
            }
        });
        
        // Emergency Button
        document.addEventListener('DOMContentLoaded', function() {
            const emergencyBtn = document.querySelector('.emergency-btn');
            if (emergencyBtn) {
                emergencyBtn.addEventListener('click', () => {
                    const isBangla = document.body.classList.contains('bn');
                    if (isBangla) {
                        alert('জরুরি পরিষেবা জানানো হয়েছে। একটি অ্যাম্বুলেন্স পথে আছে!');
                    } else {
                        alert('Emergency services have been notified. An ambulance is on the way!');
                    }
                });
            }
        });
        
        // Newsletter form functionality
        document.addEventListener('DOMContentLoaded', function() {
            const newsletterForm = document.querySelector('.newsletter-form');
            if (newsletterForm) {
                newsletterForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const emailInput = this.querySelector('.newsletter-input');
                    const email = emailInput.value;
                    
                    if (email) {
                        const isBangla = document.body.classList.contains('bn');
                        if (isBangla) {
                            alert('সফলভাবে সাবস্ক্রাইব করা হয়েছে! আমরা আপনাকে সর্বশেশ আপডেট পাঠাবো।');
                        } else {
                            alert('Successfully subscribed! We will send you the latest updates.');
                        }
                        emailInput.value = '';
                    }
                });
            }
        });
    </script>
</body>
</html>