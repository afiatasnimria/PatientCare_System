<?php
session_start();
require_once __DIR__ . '/db.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = (int) $_SESSION['user_id'];
$displayName = 'Nurse';
$displayRoleEn = 'Nurse';
$displayRoleBn = 'নার্স';

$stmt = $conn->prepare('SELECT full_name, role FROM users WHERE user_id = ? LIMIT 1');
if ($stmt) {
    $stmt->bind_param('i', $userId);
    if ($stmt->execute()) {
        $res = $stmt->get_result();
        if ($res && $row = $res->fetch_assoc()) {
            if ($row['role'] !== 'nurse') {
                header('Location: landing.php');
                exit;
            }
            $displayName = $row['full_name'] ?: $displayName;
        }
    }
    $stmt->close();

    // Load profile photo from user_profiles (if available)
    $photo = '';
    $ps = $conn->prepare('SELECT photo FROM user_profiles WHERE user_id = ? LIMIT 1');
    if ($ps) {
        $ps->bind_param('i', $userId);
        if ($ps->execute()) {
            $res2 = $ps->get_result();
            if ($res2 && $r2 = $res2->fetch_assoc()) {
                $photo = $r2['photo'];
            }
        }
        $ps->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nurse Dashboard - AmraAchi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a5276;
            --secondary-color: #2980b9;
            --accent-color: #27ae60;
            --emergency-color: #e74c3c;
            --epidemic-color: #c0392b;
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
        .shift-status {
            background-color: var(--accent-color);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
            transition: all 0.3s;
            margin-left: 10px;
            display: flex;
            align-items: center;
        }
        .shift-status:hover {
            background-color: #229954;
            transform: translateY(-2px);
        }
        .shift-status.off-duty {
            background-color: #95a5a6;
        }
        .shift-status.off-duty:hover {
            background-color: #7f8c8d;
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
        }
        .main-content.nav-hidden {
            margin-top: 0;
        }
        
        /* ===== DASHBOARD CARDS ===== */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .dashboard-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            border-top: 4px solid transparent;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }
        .dashboard-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        }
        .dashboard-card.primary::before {
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        }
        .dashboard-card.success::before {
            background: linear-gradient(90deg, var(--accent-color), #2ecc71);
        }
        .dashboard-card.danger::before {
            background: linear-gradient(90deg, var(--emergency-color), #e74c3c);
        }
        .dashboard-card.warning::before {
            background: linear-gradient(90deg, #f39c12, #f1c40f);
        }
        .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
        }
        .card-icon.primary {
            background: linear-gradient(135deg, rgba(26, 82, 118, 0.1), rgba(41, 128, 185, 0.2));
            color: var(--primary-color);
        }
        .card-icon.success {
            background: linear-gradient(135deg, rgba(39, 174, 96, 0.1), rgba(46, 204, 113, 0.2));
            color: var(--accent-color);
        }
        .card-icon.danger {
            background: linear-gradient(135deg, rgba(231, 76, 60, 0.1), rgba(231, 76, 60, 0.2));
            color: var(--emergency-color);
        }
        .card-icon.warning {
            background: linear-gradient(135deg, rgba(243, 156, 18, 0.1), rgba(241, 196, 15, 0.2));
            color: #f39c12;
        }
        .card-title {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 5px;
        }
        .card-value {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 10px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .dashboard-card.success .card-value {
            background: linear-gradient(135deg, var(--accent-color), #2ecc71);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .dashboard-card.danger .card-value {
            background: linear-gradient(135deg, var(--emergency-color), #e74c3c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .dashboard-card.warning .card-value {
            background: linear-gradient(135deg, #f39c12, #f1c40f);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .card-link {
            font-size: 0.9rem;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
        }
        .card-link i {
            margin-left: 5px;
            transition: transform 0.3s;
        }
        .card-link:hover i {
            transform: translateX(3px);
        }
        
        /* ===== CONTENT SECTIONS ===== */
        .content-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s;
        }
        .content-section:hover {
            box-shadow: var(--hover-shadow);
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .section-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin: 0;
            color: var(--primary-color);
            display: flex;
            align-items: center;
        }
        .section-title i {
            margin-right: 10px;
            color: var(--accent-color);
        }
        .section-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
        }
        .section-link i {
            margin-left: 5px;
            transition: transform 0.3s;
        }
        .section-link:hover i {
            transform: translateX(3px);
        }
        
        /* ===== TASK LIST ===== */
        .task-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .task-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #eee;
            transition: all 0.3s;
        }
        .task-item:hover {
            background-color: rgba(26, 82, 118, 0.05);
        }
        .task-item:last-child {
            border-bottom: none;
        }
        .task-patient {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 15px;
            border: 2px solid var(--light-bg);
        }
        .task-details {
            flex: 1;
        }
        .task-patient-name {
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--primary-color);
        }
        .task-info {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 3px;
            display: flex;
            align-items: center;
        }
        .task-info i {
            margin-right: 5px;
            font-size: 0.8rem;
            color: var(--secondary-color);
        }
        .task-priority {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .priority-high {
            background-color: rgba(231, 76, 60, 0.1);
            color: var(--emergency-color);
        }
        .priority-medium {
            background-color: rgba(243, 156, 18, 0.1);
            color: #f39c12;
        }
        .priority-low {
            background-color: rgba(39, 174, 96, 0.1);
            color: var(--accent-color);
        }
        .task-actions {
            display: flex;
            gap: 10px;
        }
        .task-action-btn {
            padding: 5px 10px;
            border-radius: 5px;
            border: none;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-complete {
            background-color: var(--accent-color);
            color: white;
        }
        .btn-complete:hover {
            background-color: #229954;
        }
        .btn-view {
            background-color: var(--primary-color);
            color: white;
        }
        .btn-view:hover {
            background-color: var(--secondary-color);
        }
        
        /* ===== MEDICATION SCHEDULE ===== */
        .medication-schedule {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        .medication-card {
            background: linear-gradient(135deg, rgba(26, 82, 118, 0.03), rgba(41, 128, 185, 0.08));
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s;
            border: 1px solid rgba(26, 82, 118, 0.1);
        }
        .medication-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
            background: linear-gradient(135deg, rgba(26, 82, 118, 0.05), rgba(41, 128, 185, 0.12));
        }
        .medication-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .medication-title {
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--primary-color);
        }
        .medication-time {
            font-size: 0.8rem;
            color: #666;
        }
        .medication-patient {
            font-size: 0.8rem;
            color: #666;
            margin-top: 5px;
        }
        
        /* ===== VITAL SIGNS ===== */
        .vital-signs {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }
        .vital-card {
            background: linear-gradient(135deg, rgba(26, 82, 118, 0.03), rgba(41, 128, 185, 0.08));
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s;
            border: 1px solid rgba(26, 82, 118, 0.1);
        }
        .vital-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
            background: linear-gradient(135deg, rgba(26, 82, 118, 0.05), rgba(41, 128, 185, 0.12));
        }
        .vital-icon {
            font-size: 2rem;
            margin-bottom: 10px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .vital-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        .vital-label {
            font-size: 0.8rem;
            color: #666;
        }
        .vital-patient {
            font-size: 0.7rem;
            color: #666;
            margin-top: 5px;
        }
        
        /* ===== SHIFT SCHEDULE ===== */
        .shift-schedule {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .shift-item {
            text-align: center;
            flex: 1;
        }
        .shift-time {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary-color);
        }
        .shift-label {
            font-size: 0.9rem;
            color: #666;
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
        
        /* ===== RESPONSIVE ===== */
        @media (max-width: 992px) {
            .dashboard-cards {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
        }
        @media (max-width: 768px) {
            .contact-info span {
                display: block;
                margin-bottom: 5px;
            }
            .medication-schedule {
                grid-template-columns: 1fr;
            }
            .vital-signs {
                grid-template-columns: repeat(2, 1fr);
            }
            .sidebar {
                width: 100%;
                max-width: var(--sidebar-width);
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
                        <span class="notification-badge">4</span>
                    </div>
                    <div class="user-profile-nav">
                        <img id="headerAvatar" src="<?php echo htmlspecialchars(!empty($photo) ? $photo : 'https://randomuser.me/api/portraits/women/44.jpg'); ?>" alt="User" class="user-avatar-nav">
                        <div class="user-info-nav">
                            <h4><span class="lang-text en"><?= htmlspecialchars($displayName) ?></span><span class="lang-text bn"><?= htmlspecialchars($displayName) ?></span></h4>
                            <p><span class="lang-text en">Staff Nurse</span><span class="lang-text bn">স্টাফ নার্স</span></p>
                        </div>
                        <div class="dropdown">
                            <button class="dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> <span class="lang-text en">Profile</span><span class="lang-text bn">প্রোফাইল</span></a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> <span class="lang-text en">Settings</span><span class="lang-text bn">সেটিংস</span></a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> <span class="lang-text en">Logout</span><span class="lang-text bn">লগআউট</span></a></li>
                            </ul>
                        </div>
                    </div>
                    <button class="shift-status" id="shiftStatus">
                        <i class="fas fa-circle me-2"></i>
                        <span class="lang-text en">On Duty</span>
                        <span class="lang-text bn">দায়িত্বে</span>
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
                <li><a href="#" class="active"><i class="fas fa-home"></i> <span class="lang-text en">Dashboard</span><span class="lang-text bn">ড্যাশবোর্ড</span></a></li>
                <li><a href="#"><i class="fas fa-tasks"></i> <span class="lang-text en">Tasks</span><span class="lang-text bn">কাজ</span></a></li>
                <li><a href="#"><i class="fas fa-users"></i> <span class="lang-text en">Patients</span><span class="lang-text bn">রোগী</span></a></li>
                <li><a href="#"><i class="fas fa-pills"></i> <span class="lang-text en">Medications</span><span class="lang-text bn">ওষুধ</span></a></li>
                <li><a href="#"><i class="fas fa-heartbeat"></i> <span class="lang-text en">Vital Signs</span><span class="lang-text bn">প্রাণসংকেত</span></a></li>
                <li><a href="#"><i class="fas fa-calendar-alt"></i> <span class="lang-text en">Schedule</span><span class="lang-text bn">সময়সূচী</span></a></li>
                <li><a href="#"><i class="fas fa-comments"></i> <span class="lang-text en">Messages</span><span class="lang-text bn">বার্তা</span></a></li>
                <li><a href="#"><i class="fas fa-user"></i> <span class="lang-text en">Profile</span><span class="lang-text bn">প্রোফাইল</span></a></li>
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
    <main class="main-content" id="mainContent">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><span class="lang-text en">Nurse Dashboard</span><span class="lang-text bn">নার্স ড্যাশবোর্ড</span></h1>
            <button class="btn btn-outline-primary" id="hideNavBtn2">
                <i class="fas fa-eye-slash me-2"></i>
                <span class="lang-text en">Hide Navigation</span>
                <span class="lang-text bn">নেভিগেশন লুকান</span>
            </button>
        </div>
        <!-- Dashboard Cards -->
        <div class="dashboard-cards">
            <div class="dashboard-card primary">
                <div class="card-icon primary">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="card-title"><span class="lang-text en">Pending Tasks</span><span class="lang-text bn">মুলতুবি কাজ</span></div>
                <div class="card-value">12</div>
                <a href="#" class="card-link"><span class="lang-text en">View All</span><span class="lang-text bn">সব দেখুন</span> <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="dashboard-card success">
                <div class="card-icon success">
                    <i class="fas fa-users"></i>
                </div>
                <div class="card-title"><span class="lang-text en">Assigned Patients</span><span class="lang-text bn">নির্ধারিত রোগী</span></div>
                <div class="card-value">24</div>
                <a href="#" class="card-link"><span class="lang-text en">View All</span><span class="lang-text bn">সব দেখুন</span> <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="dashboard-card danger">
                <div class="card-icon danger">
                    <i class="fas fa-pills"></i>
                </div>
                <div class="card-title"><span class="lang-text en">Medications Due</span><span class="lang-text bn">ওষুধ দেওয়ার সময়</span></div>
                <div class="card-value">8</div>
                <a href="#" class="card-link"><span class="lang-text en">View All</span><span class="lang-text bn">সব দেখুন</span> <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="dashboard-card warning">
                <div class="card-icon warning">
                    <i class="fas fa-heartbeat"></i>
                </div>
                <div class="card-title"><span class="lang-text en">Vital Checks</span><span class="lang-text bn">প্রাণসংকেত পরীক্ষা</span></div>
                <div class="card-value">16</div>
                <a href="#" class="card-link"><span class="lang-text en">View All</span><span class="lang-text bn">সব দেখুন</span> <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
        <!-- Shift Schedule -->
        <div class="content-section">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-calendar-alt"></i>
                    <span class="lang-text en">Today's Shift</span>
                    <span class="lang-text bn">আজকের শিফট</span>
                </h2>
            </div>
            <div class="shift-schedule">
                <div class="shift-item">
                    <div class="shift-time">7:00 AM</div>
                    <div class="shift-label"><span class="lang-text en">Start Time</span><span class="lang-text bn">শুরুর সময়</span></div>
                </div>
                <div class="shift-item">
                    <div class="shift-time">3:00 PM</div>
                    <div class="shift-label"><span class="lang-text en">Break</span><span class="lang-text bn">বিরতি</span></div>
                </div>
                <div class="shift-item">
                    <div class="shift-time">7:00 PM</div>
                    <div class="shift-label"><span class="lang-text en">End Time</span><span class="lang-text bn">শেষ সময়</span></div>
                </div>
            </div>
        </div>
        <!-- Pending Tasks -->
        <div class="content-section">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-tasks"></i>
                    <span class="lang-text en">Pending Tasks</span>
                    <span class="lang-text bn">মুলতুবি কাজ</span>
                </h2>
                <a href="#" class="section-link">
                    <span class="lang-text en">View All</span>
                    <span class="lang-text bn">সব দেখুন</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <ul class="task-list">
                <li class="task-item">
                    <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Patient" class="task-patient">
                    <div class="task-details">
                        <div class="task-patient-name"><span class="lang-text en">Fatima Rahman</span><span class="lang-text bn">ফাতেমা রহমান</span></div>
                        <div class="task-info"><i class="fas fa-syringe"></i> <span class="lang-text en">Administer Medication</span><span class="lang-text bn">ওষুধ প্রদান</span></div>
                        <div class="task-info"><i class="fas fa-clock"></i> <span class="lang-text en">Due in 30 mins</span><span class="lang-text bn">৩০ মিনিটের মধ্যে</span></div>
                    </div>
                    <span class="task-priority priority-high"><span class="lang-text en">High</span><span class="lang-text bn">উচ্চ</span></span>
                    <div class="task-actions">
                        <button class="task-action-btn btn-complete"><span class="lang-text en">Complete</span><span class="lang-text bn">সম্পন্ন</span></button>
                    </div>
                </li>
                <li class="task-item">
                    <img src="https://randomuser.me/api/portraits/men/67.jpg" alt="Patient" class="task-patient">
                    <div class="task-details">
                        <div class="task-patient-name"><span class="lang-text en">Mohammad Ali</span><span class="lang-text bn">মোহাম্মদ আলী</span></div>
                        <div class="task-info"><i class="fas fa-heartbeat"></i> <span class="lang-text en">Check Vital Signs</span><span class="lang-text bn">প্রাণসংকেত পরীক্ষা</span></div>
                        <div class="task-info"><i class="fas fa-clock"></i> <span class="lang-text en">Due in 1 hour</span><span class="lang-text bn">১ ঘন্টার মধ্যে</span></div>
                    </div>
                    <span class="task-priority priority-medium"><span class="lang-text en">Medium</span><span class="lang-text bn">মাঝারি</span></span>
                    <div class="task-actions">
                        <button class="task-action-btn btn-complete"><span class="lang-text en">Complete</span><span class="lang-text bn">সম্পন্ন</span></button>
                    </div>
                </li>
                <li class="task-item">
                    <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Patient" class="task-patient">
                    <div class="task-details">
                        <div class="task-patient-name"><span class="lang-text en">Nusrat Jahan</span><span class="lang-text bn">নুসরাত জাহান</span></div>
                        <div class="task-info"><i class="fas fa-band-aid"></i> <span class="lang-text en">Wound Dressing</span><span class="lang-text bn">ক্ষত ড্রেসিং</span></div>
                        <div class="task-info"><i class="fas fa-clock"></i> <span class="lang-text en">Due in 2 hours</span><span class="lang-text bn">২ ঘন্টার মধ্যে</span></div>
                    </div>
                    <span class="task-priority priority-low"><span class="lang-text en">Low</span><span class="lang-text bn">নিম্ন</span></span>
                    <div class="task-actions">
                        <button class="task-action-btn btn-complete"><span class="lang-text en">Complete</span><span class="lang-text bn">সম্পন্ন</span></button>
                    </div>
                </li>
            </ul>
        </div>
        <!-- Medication Schedule -->
        <div class="content-section">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-pills"></i>
                    <span class="lang-text en">Medication Schedule</span>
                    <span class="lang-text bn">ওষুধের সময়সূচী</span>
                </h2>
                <a href="#" class="section-link">
                    <span class="lang-text en">View All</span>
                    <span class="lang-text bn">সব দেখুন</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="medication-schedule">
                <div class="medication-card">
                    <div class="medication-icon">
                        <i class="fas fa-pills"></i>
                    </div>
                    <div class="medication-title"><span class="lang-text en">Antibiotics</span><span class="lang-text bn">অ্যান্টিবায়োটিক</span></div>
                    <div class="medication-time"><span class="lang-text en">9:00 AM</span><span class="lang-text bn">সকাল ৯:০০</span></div>
                    <div class="medication-patient"><span class="lang-text en">Fatima Rahman</span><span class="lang-text bn">ফাতেমা রহমান</span></div>
                </div>
                <div class="medication-card">
                    <div class="medication-icon">
                        <i class="fas fa-tablets"></i>
                    </div>
                    <div class="medication-title"><span class="lang-text en">Pain Reliever</span><span class="lang-text bn">ব্যথানাশক</span></div>
                    <div class="medication-time"><span class="lang-text en">12:00 PM</span><span class="lang-text bn">দুপুর ১২:০০</span></div>
                    <div class="medication-patient"><span class="lang-text en">Mohammad Ali</span><span class="lang-text bn">মোহাম্মদ আলী</span></div>
                </div>
                <div class="medication-card">
                    <div class="medication-icon">
                        <i class="fas fa-capsules"></i>
                    </div>
                    <div class="medication-title"><span class="lang-text en">Vitamins</span><span class="lang-text bn">ভিটামিন</span></div>
                    <div class="medication-time"><span class="lang-text en">2:00 PM</span><span class="lang-text bn">দুপুর ২:০০</span></div>
                    <div class="medication-patient"><span class="lang-text en">Nusrat Jahan</span><span class="lang-text bn">নুসরাত জাহান</span></div>
                </div>
                <div class="medication-card">
                    <div class="medication-icon">
                        <i class="fas fa-syringe"></i>
                    </div>
                    <div class="medication-title"><span class="lang-text en">Insulin</span><span class="lang-text bn">ইনসুলিন</span></div>
                    <div class="medication-time"><span class="lang-text en">4:00 PM</span><span class="lang-text bn">বিকাল ৪:০০</span></div>
                    <div class="medication-patient"><span class="lang-text en">Karim Ahmed</span><span class="lang-text bn">করিম আহমেদ</span></div>
                </div>
            </div>
        </div>
        <!-- Vital Signs -->
        <div class="content-section">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-heartbeat"></i>
                    <span class="lang-text en">Recent Vital Signs</span>
                    <span class="lang-text bn">সাম্প্রতিক প্রাণসংকেত</span>
                </h2>
                <a href="#" class="section-link">
                    <span class="lang-text en">View All</span>
                    <span class="lang-text bn">সব দেখুন</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="vital-signs">
                <div class="vital-card">
                    <div class="vital-icon">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <div class="vital-value">72</div>
                    <div class="vital-label"><span class="lang-text en">Heart Rate</span><span class="lang-text bn">হৃদস্পন্দন</span></div>
                    <div class="vital-patient"><span class="lang-text en">Fatima Rahman</span><span class="lang-text bn">ফাতেমা রহমান</span></div>
                </div>
                <div class="vital-card">
                    <div class="vital-icon">
                        <i class="fas fa-thermometer-half"></i>
                    </div>
                    <div class="vital-value">98.6°F</div>
                    <div class="vital-label"><span class="lang-text en">Temperature</span><span class="lang-text bn">তাপমাত্রা</span></div>
                    <div class="vital-patient"><span class="lang-text en">Mohammad Ali</span><span class="lang-text bn">মোহাম্মদ আলী</span></div>
                </div>
                <div class="vital-card">
                    <div class="vital-icon">
                        <i class="fas fa-lungs"></i>
                    </div>
                    <div class="vital-value">16</div>
                    <div class="vital-label"><span class="lang-text en">Respiration</span><span class="lang-text bn">শ্বাসপ্রশ্বাস</span></div>
                    <div class="vital-patient"><span class="lang-text en">Nusrat Jahan</span><span class="lang-text bn">নুসরাত জাহান</span></div>
                </div>
                <div class="vital-card">
                    <div class="vital-icon">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                    <div class="vital-value">120/80</div>
                    <div class="vital-label"><span class="lang-text en">Blood Pressure</span><span class="lang-text bn">রক্তচাপ</span></div>
                    <div class="vital-patient"><span class="lang-text en">Karim Ahmed</span><span class="lang-text bn">করিম আহমেদ</span></div>
                </div>
            </div>
        </div>
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
                            <input type="email" class="newsletter-input" data-placeholder-en="Your email address" data-placeholder-bn="আপনার ইমেইল ঠিকানা" placeholder="Your email address">
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
            const shiftStatus = document.getElementById('shiftStatus');
            
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
                mainContent.classList.add('nav-hidden');
                closeSidebarFunc(); // Also close sidebar if open
            }
            
            // Function to show navigation
            function showNavigation() {
                topHeader.classList.remove('hidden');
                mainHeader.classList.remove('hidden');
                navToggleBtn.classList.remove('hidden');
                showNavBtn.classList.remove('visible');
                mainContent.classList.remove('nav-hidden');
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
                    } catch (e) {}
                });
            }

            if (langToggle) {
                langToggle.addEventListener('click', function() {
                    const isNowBangla = document.body.classList.toggle('bn');
                    applyPlaceholdersForLanguage(isNowBangla);
                });
            }

            applyPlaceholdersForLanguage(document.body.classList.contains('bn'));
            
            // Shift status toggle functionality
            if (shiftStatus) {
                shiftStatus.addEventListener('click', function() {
                    this.classList.toggle('off-duty');
                    const isBangla = document.body.classList.contains('bn');
                    if (this.classList.contains('off-duty')) {
                        this.innerHTML = '<i class="fas fa-circle me-2"></i><span class="lang-text en">Off Duty</span><span class="lang-text bn">দায়িত্বে নয়</span>';
                    } else {
                        this.innerHTML = '<i class="fas fa-circle me-2"></i><span class="lang-text en">On Duty</span><span class="lang-text bn">দায়িত্বে</span>';
                    }
                });
            }
            
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
        });
    </script>
    <script>
        // Apply locally uploaded profile photo if present (from profile.php upload)
        (function(){
            try {
                const url = localStorage.getItem('profilePhoto');
                if (url) {
                    const img = document.getElementById('headerAvatar');
                    if (img) img.src = url;
                }
            } catch (e) {}
        })();

        // Update header avatar across tabs when localStorage.profilePhoto changes
        window.addEventListener('storage', function(e) {
            if (!e) return;
            try {
                if (e.key === 'profilePhoto') {
                    const img = document.getElementById('headerAvatar');
                    if (img) img.src = e.newValue || img.src;
                }
            } catch (err) {}
        });
    </script>
</body>
</html>