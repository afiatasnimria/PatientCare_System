<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AmraAchi - Digital Healthcare Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        :root {
            --primary-color: #1a5276; /* Professional blue */
            --secondary-color: #2980b9; /* Lighter blue */
            --accent-color: #27ae60; /* Green for accents */
            --emergency-color: #e74c3c; /* Red for emergencies */
            --epidemic-color: #c0392b; /* Darker red for epidemic */
            --light-bg: #ecf0f1; /* Light gray background */
            --dark-text: #2c3e50; /* Dark blue-gray text */
            --section-bg: #f8f9fa; /* Very light background */
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--dark-text);
            background-color: var(--section-bg);
        }

        /* Top Header */
        .top-header {
            background-color: var(--primary-color);
            color: white;
            padding: 10px 0;
            position: relative;
            z-index: 1000;
        }

        .contact-info span {
            margin-right: 20px;
            font-size: 14px;
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

        /* Language Toggle */
        .lang-toggle {
            background-color: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .lang-toggle:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }

        /* Main Header - Sticky */
        .main-header {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.8rem;
            color: var(--primary-color) !important;
        }

        .navbar-nav .nav-link {
            font-weight: 500;
            margin: 0 10px;
            transition: all 0.3s;
            position: relative;
            color: var(--dark-text) !important;
        }

        .navbar-nav .nav-link:hover {
            color: var(--primary-color) !important;
        }

        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--primary-color);
            transition: width 0.3s;
        }

        .navbar-nav .nav-link:hover::after {
            width: 100%;
        }

        .auth-buttons .btn {
            margin-left: 10px;
            border-radius: 20px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .auth-buttons .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Search Bar */
        .search-bar-section {
            background-color: var(--secondary-color);
            padding: 15px 0;
            position: sticky;
            top: 90px;
            z-index: 998;
        }

        .search-container {
            position: relative;
            max-width: 800px;
            margin: 0 auto;
        }

        .search-input {
            width: 100%;
            padding: 12px 20px 12px 50px;
            border: none;
            border-radius: 30px;
            font-size: 16px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }

        .search-input:focus {
            outline: none;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        }

        .search-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary-color);
            font-size: 18px;
        }

        .search-btn {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .search-btn:hover {
            background-color: var(--primary-color);
            transform: translateY(-50%) scale(1.05);
        }

        /* Emergency Buttons - Redesigned */
        .emergency-buttons {
            position: fixed;
            right: 30px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .sos-button {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--emergency-color), #f84632);
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 25px rgba(238, 88, 71, 0.4);
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .sos-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, rgba(237, 56, 56, 0) 70%);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .sos-button:hover::before {
            opacity: 1;
        }

        .sos-button:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 30px rgba(231, 76, 60, 0.6);
        }

        .epidemic-button {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--epidemic-color), #ed4132);
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 25px rgba(232, 70, 52, 0.4);
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .epidemic-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0) 70%);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .epidemic-button:hover::before {
            opacity: 1;
        }

        .epidemic-button:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 30px rgba(192, 57, 43, 0.6);
        }

        .emergency-button i {
            font-size: 28px;
            margin-bottom: 5px;
            z-index: 1;
        }

        .emergency-text {
            font-size: 12px;
            font-weight: 700;
            z-index: 1;
        }

        /* Pulse animation for SOS button */
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(239, 75, 57, 0.7);
            }
            70% {
                box-shadow: 0 0 0 20px rgba(231, 76, 60, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(231, 76, 60, 0);
            }
        }

        .sos-button {
            animation: pulse 2s infinite;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(rgba(26, 82, 118, 0.8), rgba(26, 82, 118, 0.8)), url('https://images.unsplash.com/photo-1576091160550-2173dba999ef?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            position: relative;
            overflow: hidden;
        }

        .hero-content {
            animation: fadeInUp 1s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero-content h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .hero-content p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            max-width: 600px;
        }

        .hero-btn {
            padding: 12px 30px;
            margin: 5px;
            font-weight: 600;
            border-radius: 30px;
            transition: all 0.3s;
            display: inline-block;
        }

        .btn-primary-custom {
            background-color: var(--accent-color);
            border: none;
        }

        .btn-primary-custom:hover {
            background-color: #229954;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(39, 174, 96, 0.3);
        }

        .btn-outline-custom {
            border: 2px solid white;
            background: transparent;
            color: white;
        }

        .btn-outline-custom:hover {
            background-color: white;
            color: var(--primary-color);
        }

        /* Epidemic Alert Banner on Homepage */
        .epidemic-banner {
            background: linear-gradient(135deg, var(--epidemic-color), #e74c3c);
            color: white;
            padding: 20px 0;
            position: relative;
            overflow: hidden;
        }

        .epidemic-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="white" opacity="0.1"/><circle cx="30" cy="30" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/><circle cx="70" cy="70" r="1" fill="white" opacity="0.1"/><circle cx="90" cy="90" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .epidemic-content {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .epidemic-info {
            display: flex;
            align-items: center;
        }

        .epidemic-icon {
            font-size: 3rem;
            margin-right: 20px;
            animation: vibrate 1s infinite;
        }

        @keyframes vibrate {
            0% { transform: rotate(0deg); }
            25% { transform: rotate(5deg); }
            50% { transform: rotate(0deg); }
            75% { transform: rotate(-5deg); }
            100% { transform: rotate(0deg); }
        }

        .epidemic-text h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .epidemic-text p {
            margin-bottom: 0;
            font-size: 1.1rem;
        }

        .epidemic-actions {
            display: flex;
            gap: 15px;
        }

        .epidemic-learn-btn {
            background-color: white;
            color: var(--epidemic-color);
            border: none;
            padding: 10px 25px;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .epidemic-learn-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .close-banner {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1.2rem;
        }

        .close-banner:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }

        /* Map Section - Moved Up */
        .map-section {
            padding: 60px 0;
            background-color: white;
        }

        .section-title {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 15px;
            text-align: center;
            position: relative;
            color: var(--primary-color);
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background-color: var(--primary-color);
        }

        .section-subtitle {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 40px;
            color: var(--dark-text);
        }

        #map {
            height: 400px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .hospital-list {
            max-height: 400px;
            overflow-y: auto;
            padding-right: 10px;
        }

        .hospital-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
            border-left: 4px solid var(--primary-color);
        }

        .hospital-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }

        .hospital-name {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--primary-color);
        }

        .hospital-address {
            color: var(--dark-text);
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .hospital-distance {
            font-weight: 600;
            color: var(--accent-color);
            margin-bottom: 12px;
            font-size: 0.9rem;
        }

        .hospital-details-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 5px;
            font-weight: 500;
            transition: all 0.3s;
            font-size: 0.9rem;
        }

        .hospital-details-btn:hover {
            background-color: var(--secondary-color);
        }

        /* Epidemic Alert Section */
        .epidemic-alert-section {
            background-color: var(--epidemic-color);
            color: white;
            padding: 40px 0;
            margin: 40px 0;
            position: relative;
            overflow: hidden;
            display: none;
        }

        .epidemic-alert-section.active {
            display: block;
            animation: slideDown 0.5s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .epidemic-alert-content {
            position: relative;
            z-index: 1;
        }

        .epidemic-alert-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-align: center;
        }

        .epidemic-alert-subtitle {
            font-size: 1.2rem;
            margin-bottom: 30px;
            text-align: center;
            font-weight: 500;
        }

        .epidemic-steps {
            margin-top: 30px;
        }

        .epidemic-step {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s;
        }

        .epidemic-step:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-5px);
        }

        .epidemic-step h3 {
            font-size: 1.3rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }

        .epidemic-step h3 i {
            margin-right: 10px;
            font-size: 1.5rem;
        }

        .epidemic-step p {
            margin-bottom: 0;
            line-height: 1.6;
        }

        .close-epidemic {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            z-index: 2;
        }

        .close-epidemic:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Services Section */
        .services-section {
            padding: 60px 0;
            background-color: var(--section-bg);
        }

        .service-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            height: 100%;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
            text-align: center;
            position: relative;
            overflow: hidden;
            border-top: 4px solid var(--primary-color);
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .service-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 28px;
            transition: all 0.3s;
        }

        .service-card:hover .service-icon {
            background-color: var(--accent-color);
            transform: scale(1.1);
        }

        .service-card h3 {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 12px;
            color: var(--primary-color);
        }
/* Stats Section */
.stats-section {
    padding: 60px 0;
    background-color: var(--section-bg);
}

/* Flip Card Effect */
.flip-card {
    background-color: transparent;
    width: 100%;
    height: 250px;
    perspective: 1000px;
}

.flip-card-inner {
    position: relative;
    width: 100%;
    height: 100%;
    text-align: center;
    transition: transform 0.8s;
    transform-style: preserve-3d;
}

.flip-card:hover .flip-card-inner {
    transform: rotateY(180deg);
}

.flip-card-front, .flip-card-back {
    position: absolute;
    width: 100%;
    height: 100%;
    -webkit-backface-visibility: hidden;
    backface-visibility: hidden;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

.flip-card-front {
    background-color: white;
}

.flip-card-back {
    background-color: var(--primary-color);
    color: white;
    transform: rotateY(180deg);
}

.stat-icon {
    font-size: 3rem;
    color: var(--primary-color);
    margin-bottom: 15px;
}
        /* Departments Section with Scroller */
        .departments-section {
            padding: 60px 0;
            background-color: white;
        }

        .departments-container {
            position: relative;
            overflow: hidden;
        }

        .departments-scroller {
            display: flex;
            overflow-x: auto;
            scroll-behavior: smooth;
            padding: 20px 0;
            gap: 20px;
        }

        .departments-scroller::-webkit-scrollbar {
            height: 8px;
        }

        .departments-scroller::-webkit-scrollbar-track {
            background: var(--light-bg);
            border-radius: 10px;
        }

        .departments-scroller::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 10px;
        }

        .department-card {
            min-width: 280px;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
            height: 100%;
            border: 1px solid var(--light-bg);
        }

        .department-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .department-img {
            height: 180px;
            object-fit: cover;
            width: 100%;
            transition: all 0.5s;
        }

        .department-card:hover .department-img {
            transform: scale(1.05);
        }

        .department-content {
            padding: 20px;
        }

        .department-content h3 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 12px;
            color: var(--primary-color);
        }

        .department-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 8px 18px;
            border-radius: 20px;
            font-weight: 500;
            transition: all 0.3s;
            font-size: 0.9rem;
        }

        .department-btn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .scroll-controls {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .scroll-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: white;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .scroll-btn:hover {
            background-color: var(--secondary-color);
            transform: scale(1.1);
        }

        /* Doctors Section */
        .doctors-section {
            padding: 60px 0;
            background-color: var(--section-bg);
        }

        .doctor-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
            height: 100%;
        }

        .doctor-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .doctor-img {
            height: 220px;
            object-fit: cover;
            width: 100%;
            transition: all 0.5s;
        }

        .doctor-card:hover .doctor-img {
            transform: scale(1.05);
        }

        .doctor-content {
            padding: 20px;
        }

        .doctor-name {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--primary-color);
        }

        .doctor-specialty {
            color: var(--secondary-color);
            margin-bottom: 12px;
            font-weight: 500;
        }

        .doctor-bio {
            color: var(--dark-text);
            margin-bottom: 15px;
            font-size: 0.9rem;
        }

        .doctor-social {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .doctor-social a {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background-color: var(--light-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            transition: all 0.3s;
        }

        .doctor-social a:hover {
            background-color: var(--primary-color);
            color: white;
            transform: translateY(-3px);
        }

        .doctor-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 8px 18px;
            border-radius: 20px;
            font-weight: 500;
            transition: all 0.3s;
            width: 100%;
            font-size: 0.9rem;
        }

        .doctor-btn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        /* Appointment Section */
        .appointment-section {
            padding: 60px 0;
            background-image: url('https://images.unsplash.com/photo-1532938911079-1b06ac7ceec7?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .appointment-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(26, 82, 118, 0.8);
        }

        .appointment-content {
            position: relative;
            z-index: 1;
            color: white;
            text-align: center;
            max-width: 700px;
            margin: 0 auto;
            animation: fadeIn 1s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .appointment-content h2 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .appointment-content p {
            font-size: 1.1rem;
            margin-bottom: 30px;
        }

        .appointment-btn {
            background-color: var(--accent-color);
            color: white;
            border: none;
            padding: 12px 35px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s;
        }

        .appointment-btn:hover {
            background-color: #229954;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(39, 174, 96, 0.3);
        }

        /* Testimonials Section */
        .testimonials-section {
            padding: 60px 0;
            background-color: white;
        }

        .testimonial-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            height: 100%;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
            border-left: 4px solid var(--accent-color);
        }

        .testimonial-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .testimonial-card p {
            font-style: italic;
            margin-bottom: 20px;
            position: relative;
            color: var(--dark-text);
        }

        .testimonial-card p::before {
            content: '"';
            font-size: 60px;
            color: var(--primary-color);
            position: absolute;
            top: -20px;
            left: -10px;
            opacity: 0.2;
        }

        .client-info {
            display: flex;
            align-items: center;
        }

        .client-img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-right: 15px;
            object-fit: cover;
        }

        .client-name {
            font-weight: 600;
            margin: 0;
            color: var(--primary-color);
        }

        .client-title {
            color: var(--secondary-color);
            font-size: 0.9rem;
            margin: 0;
        }

        .testimonial-rating {
            color: #f39c12;
            margin-bottom: 15px;
        }

        /* Footer */
        footer {
            background-color: var(--primary-color);
            color: white;
            padding: 50px 0 20px;
        }

        .footer-logo {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: white;
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
        }

        .footer-links ul li a:hover {
            color: white;
            padding-left: 5px;
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

        /* Responsive */
        @media (max-width: 991px) {
            .hero-content h1 {
                font-size: 2.5rem;
            }
            
            .hero-content p {
                font-size: 1.1rem;
            }

            .emergency-buttons {
                right: 20px;
            }

            .sos-button, .epidemic-button {
                width: 70px;
                height: 70px;
            }

            .emergency-button i {
                font-size: 24px;
            }

            .emergency-text {
                font-size: 11px;
            }

            #map {
                height: 350px;
                margin-bottom: 30px;
            }

            .hospital-list {
                max-height: none;
            }

            .epidemic-content {
                flex-direction: column;
                text-align: center;
            }

            .epidemic-info {
                margin-bottom: 20px;
            }

            .epidemic-icon {
                margin-right: 0;
                margin-bottom: 10px;
            }
        }

        @media (max-width: 767px) {
            .hero-content h1 {
                font-size: 2rem;
            }
            
            .hero-content p {
                font-size: 1rem;
            }
            
            .section-title {
                font-size: 1.8rem;
            }
            
            .service-card, .department-card, .doctor-card, .testimonial-card {
                margin-bottom: 30px;
            }

            .contact-info span {
                display: block;
                margin-bottom: 5px;
            }

            .auth-buttons {
                margin-top: 10px;
            }

            .newsletter-form {
                flex-direction: column;
            }

            .newsletter-input {
                border-radius: 5px;
                margin-bottom: 10px;
            }

            .newsletter-btn {
                border-radius: 5px;
            }

            .departments-scroller {
                padding: 10px 0;
            }

            .epidemic-step {
                padding: 15px;
            }

            .emergency-buttons {
                right: 15px;
                gap: 15px;
            }

            .sos-button, .epidemic-button {
                width: 60px;
                height: 60px;
            }

            .emergency-button i {
                font-size: 20px;
            }

            .emergency-text {
                font-size: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Top Header -->
    <div class="top-header">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="contact-info">
                        <span><i class="fas fa-phone-alt me-2"></i> <span class="en-text">+880 1234 567890</span><span class="bn-text" style="display: none;">+৮৮০ ১২৩৪ ৫৬৭৮৯০</span></span>
                        <span><i class="fas fa-envelope me-2"></i> <span class="en-text">info@amraaichi.com</span><span class="bn-text" style="display: none;">info@amraaichi.com</span></span>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <button class="lang-toggle" id="langToggle">বাংলা</button>
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

    <!-- Main Header - Sticky -->
    <header class="main-header">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light">
                <a class="navbar-brand" href="#">AmraAchi</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link active" href="#home"><span class="en-text">Home</span><span class="bn-text" style="display: none;">হোম</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#map-section"><span class="en-text">Find Hospitals</span><span class="bn-text" style="display: none;">হাসপাতাল খুঁজুন</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#services"><span class="en-text">Services</span><span class="bn-text" style="display: none;">সেবা</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#departments"><span class="en-text">Departments</span><span class="bn-text" style="display: none;">বিভাগ</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#doctors"><span class="en-text">Doctors</span><span class="bn-text" style="display: none;">ডাক্তার</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#testimonials"><span class="en-text">Testimonials</span><span class="bn-text" style="display: none;">প্রশংসাপত্র</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#contact"><span class="en-text">Contact</span><span class="bn-text" style="display: none;">যোগাযোগ</span></a>
                        </li>
                    </ul>
                    <div class="auth-buttons ms-3">
                        <a href="login.php" class="btn btn-outline-primary"><span class="en-text">Login</span><span class="bn-text" style="display: none;">লগইন</span></a>
                        <a href="login.php" class="btn btn-primary"><span class="en-text">Register</span><span class="bn-text" style="display: none;">নিবন্ধন</span></a>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Search Bar Section - Sticky -->
    <div class="search-bar-section">
        <div class="container">
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-input" data-placeholder-en="Search for doctors, services, hospitals..." data-placeholder-bn="ডাক্তার, সেবা, হাসপাতাল খুঁজুন..." placeholder="Search for doctors, services, hospitals...">
                <button class="search-btn"><span class="en-text">Search</span><span class="bn-text" style="display: none;">খুঁজুন</span></button>
            </div>
        </div>
    </div>

    <!-- Emergency Buttons - Redesigned -->
    <div class="emergency-buttons">
        <div class="sos-button">
            <i class="fas fa-ambulance"></i>
            <a href="SOS.html" class="emergency-text en-text">SOS</a>
            <span class="emergency-text bn-text" style="display: none;">এসওএস</span>
        </div>
        <div class="epidemic-button">
            <i class="fas fa-virus"></i>
            <span class="emergency-text en-text">Alert</span>
            <span class="emergency-text bn-text" style="display: none;">সতর্কতা</span>
        </div>
    </div>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <h1><span class="en-text">Your Complete Digital Healthcare Solution</span><span class="bn-text" style="display: none;">আপনার সম্পূর্ণ ডিজিটাল স্বাস্থ্যসেবা সমাধান</span></h1>
                        <p><span class="en-text">AmraAchi connects patients, doctors, and healthcare services in one integrated platform for better health outcomes.</span><span class="bn-text" style="display: none;">আমরাআছি রোগী, ডাক্তার এবং স্বাস্থ্যসেবা সেবাকে একটি সমন্বিত প্ল্যাটফর্মে সংযুক্ত করে উন্নত স্বাস্থ্য ফলাফলের জন্য।</span></p>
                        <div class="d-flex flex-wrap">
                            <a href="#appointment" class="btn btn-primary-custom hero-btn"><span class="en-text">Book Appointment</span><span class="bn-text" style="display: none;">অ্যাপয়েন্টমেন্ট বুক করুন</span></a>
                            <a href="#map-section" class="btn btn-outline-custom hero-btn"><span class="en-text">Find Hospitals</span><span class="bn-text" style="display: none;">হাসপাতাল খুঁজুন</span></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Epidemic Alert Banner on Homepage -->
    <div class="epidemic-banner" id="epidemicBanner">
        <div class="container">
            <div class="epidemic-content">
                <div class="epidemic-info">
                    <div class="epidemic-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="epidemic-text">
                        <h3><span class="en-text">COVID-19 Alert</span><span class="bn-text" style="display: none;">কোভিড-১৯ সতর্কতা</span></h3>
                        <p><span class="en-text">Cases are rising. Protect yourself and others.</span><span class="bn-text" style="display: none;">কেস বাড়ছে। নিজেকে এবং অন্যদের রক্ষা করুন।</span></p>
                    </div>
                </div>
                <div class="epidemic-actions">
                    <button class="epidemic-learn-btn" onclick="showEpidemicDetails()"><span class="en-text">Learn More</span><span class="bn-text" style="display: none;">আরও জানুন</span></button>
                    <button class="close-banner" id="closeBanner">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Section - Moved Up -->
    <section id="map-section" class="map-section">
        <div class="container">
            <div class="text-center mb-4">
                <h2 class="section-title"><span class="en-text">Find Hospitals Near You</span><span class="bn-text" style="display: none;">আপনার কাছাকাছি হাসপাতাল খুঁজুন</span></h2>
                <p class="section-subtitle"><span class="en-text">Locate the nearest hospitals and healthcare facilities with our interactive map</span><span class="bn-text" style="display: none;">আমাদের ইন্টারেক্টিভ মানচিত্রের সাথে নিকটস্থ হাসপাতাল এবং স্বাস্থ্যসেবা সুবিধা খুঁজুন</span></p>
            </div>
            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div id="map"></div>
                </div>
                <div class="col-lg-4">
                    <div class="hospital-list" id="hospital-list">
                        <!-- Hospital cards will be dynamically inserted here -->
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Epidemic Alert Section -->
    <section id="epidemic-alert" class="epidemic-alert-section">
        <button class="close-epidemic" id="closeEpidemic">
            <i class="fas fa-times"></i>
        </button>
        <div class="container">
            <div class="epidemic-alert-content">
                <h2 class="epidemic-alert-title"><span class="en-text">COVID-19 Health Alert</span><span class="bn-text" style="display: none;">কোভিড-১৯ স্বাস্থ্য সতর্কতা</span></h2>
                <p class="epidemic-alert-subtitle"><span class="en-text">Important information and guidelines to protect yourself and others</span><span class="bn-text" style="display: none;">নিজেকে এবং অন্যদের রক্ষা করার জন্য গুরুত্বপূর্ণ তথ্য এবং নির্দেশিকা</span></p>
                
                <div class="epidemic-steps">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="epidemic-step">
                                <h3><i class="fas fa-hands-wash"></i> <span class="en-text">Wash Your Hands</span><span class="bn-text" style="display: none;">আপনার হাত ধুয়ে ফেলুন</span></h3>
                                <p><span class="en-text">Wash your hands frequently with soap and water for at least 20 seconds or use hand sanitizer with at least 60% alcohol.</span><span class="bn-text" style="display: none;">সাবান এবং পানি দিয়ে কমপক্ষে ২০ সেকেন্ডের জন্য ঘন ঘন আপনার হাত ধুয়ে ফেলুন বা কমপক্ষে ৬০% অ্যালকোহল সহ হ্যান্ড স্যানিটাইজার ব্যবহার করুন।</span></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="epidemic-step">
                                <h3><i class="fas fa-head-side-mask"></i> <span class="en-text">Wear a Mask</span><span class="bn-text" style="display: none;">মাস্ক পরুন</span></h3>
                                <p><span class="en-text">Wear a mask that covers your nose and mouth in public settings, especially when social distancing is difficult.</span><span class="bn-text" style="display: none;">পাবলিক সেটিংসে আপনার নাক এবং মুখ কভার করে একটি মাস্ক পরুন, বিশেষ করে যখন সামাজিক দূরত্ব কঠিন।</span></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="epidemic-step">
                                <h3><i class="fas fa-people-arrows"></i> <span class="en-text">Social Distance</span><span class="bn-text" style="display: none;">সামাজিক দূরত্ব</span></h3>
                                <p><span class="en-text">Maintain at least 6 feet (about 2 arm lengths) distance from others who are not from your household.</span><span class="bn-text" style="display: none;">আপনার পরিবার থেকে নয় এমন অন্যদের থেকে কমপক্ষে ৬ ফুট (প্রায় ২ হাতের দৈর্ঘ্য) দূরত্ব বজায় রাখুন।</span></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="epidemic-step">
                                <h3><i class="fas fa-syringe"></i> <span class="en-text">Get Vaccinated</span><span class="bn-text" style="display: none;">টিকা নিন</span></h3>
                                <p><span class="en-text">COVID-19 vaccines are effective at preventing severe illness, hospitalizations, and death. Get vaccinated as soon as you can.</span><span class="bn-text" style="display: none;">কোভিড-১৯ টিকা গুরুতর অসুস্থতা, হাসপাতালে ভর্তি এবং মৃত্যু প্রতিরোধে কার্যকর। যত তাড়াতাড়ি সম্ভব টিকা নিন।</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services-section">
        <div class="container">
            <div class="text-center mb-4">
                <h2 class="section-title"><span class="en-text">Our Services</span><span class="bn-text" style="display: none;">আমাদের সেবা</span></h2>
                <p class="section-subtitle"><span class="en-text">We offer a comprehensive range of healthcare services to meet all your medical needs</span><span class="bn-text" style="display: none;">আমরা আপনার সমস্ত চিকিৎসা প্রয়োজন মেটাতে স্বাস্থ্যসেবা পরিষেবার একটি বিস্তৃত পরিসর অফার করি</span></p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-ambulance"></i>
                        </div>
                        <h3><span class="en-text">Emergency Services</span><span class="bn-text" style="display: none;">জরুরি সেবা</span></h3>
                        <p><span class="en-text">24/7 emergency care with rapid response and advanced life support systems.</span><span class="bn-text" style="display: none;">দ্রুত প্রতিক্রিয়া এবং উন্নত লাইফ সাপোর্ট সিস্টেম সহ ২৪/৭ জরুরি যত্ন।</span></p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h3><span class="en-text">Online Appointments</span><span class="bn-text" style="display: none;">অনলাইন অ্যাপয়েন্টমেন্ট</span></h3>
                        <p><span class="en-text">Book appointments with certified doctors easily and manage your schedule.</span><span class="bn-text" style="display: none;">প্রত্যয়িত ডাক্তারদের সাথে সহজেই অ্যাপয়েন্টমেন্ট বুক করুন এবং আপনার সময়সূচী পরিচালনা করুন।</span></p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-file-medical"></i>
                        </div>
                        <h3><span class="en-text">Health Records</span><span class="bn-text" style="display: none;">স্বাস্থ্য রেকর্ড</span></h3>
                        <p><span class="en-text">Securely store and access your medical history, prescriptions, and test reports.</span><span class="bn-text" style="display: none;">নিরাপদে আপনার চিকিৎসা ইতিহাস, প্রেসক্রিপশন এবং পরীক্ষার রিপোর্ট সংরক্ষণ এবং অ্যাক্সেস করুন।</span></p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-user-nurse"></i>
                        </div>
                        <h3><span class="en-text">Home Care Services</span><span class="bn-text" style="display: none;">হোম কেয়ার সার্ভিস</span></h3>
                        <p><span class="en-text">Book experienced nurses and caregivers for home medical assistance.</span><span class="bn-text" style="display: none;">হোম মেডিকেল সহায়তার জন্য অভিজ্ঞ নার্স এবং কেয়ারগিভার বুক করুন।</span></p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-pills"></i>
                        </div>
                        <h3><span class="en-text">E-Prescriptions</span><span class="bn-text" style="display: none;">ই-প্রেসক্রিপশন</span></h3>
                        <p><span class="en-text">Digital prescriptions that make medication management simple and error-free.</span><span class="bn-text" style="display: none;">ডিজিটাল প্রেসক্রিপশন যা ওষুধ ব্যবস্থাপনাকে সহজ এবং ত্রুটিমুক্ত করে।</span></p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-virus"></i>
                        </div>
                        <h3><span class="en-text">Health Alerts</span><span class="bn-text" style="display: none;">স্বাস্থ্য সতর্কতা</span></h3>
                        <p><span class="en-text">Get timely notifications about disease outbreaks and preventive health tips.</span><span class="bn-text" style="display: none;">রোগের প্রাদুর্ভাব এবং প্রতিরোধমূলক স্বাস্থ্য টিপস সম্পর্কে সময়োপযোগী বিজ্ঞপ্তি পান।</span></p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-stethoscope"></i>
                        </div>
                        <h3><span class="en-text">Doctor Search</span><span class="bn-text" style="display: none;">ডাক্তার খোঁজ</span></h3>
                        <p><span class="en-text">Find the right specialist by location, expertise, and patient reviews.</span><span class="bn-text" style="display: none;">অবস্থান, দক্ষতা এবং রোগীর পর্যালোচনা দ্বারা সঠিক বিশেষজ্ঞ খুঁজুন।</span></p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h3><span class="en-text">Mobile App</span><span class="bn-text" style="display: none;">মোবাইল অ্যাপ</span></h3>
                        <p><span class="en-text">Access all features on-the-go with our Android application.</span><span class="bn-text" style="display: none;">আমাদের অ্যান্ড্রয়েড অ্যাপ্লিকেশন দিয়ে চলতি পথে সমস্ত বৈশিষ্ট্যগুলি অ্যাক্সেস করুন।</span></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
<!-- Stats Section with Flip Cards -->
<section class="stats-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold">
                <span class="en-text">Trusted by Thousands</span>
                <span class="bn-text" style="display: none;">হাজারো মানুষের আস্থায়</span>
            </h2>
            <p class="lead">
                <span class="en-text">Join our growing community of satisfied patients</span>
                <span class="bn-text" style="display: none;">আমাদের ক্রমবর্ধমান সন্তুষ্ট রোগী সম্প্রদায়ে যোগ দিন</span>
            </p>
        </div>
        <div class="row g-4">
            <div class="col-md-3 col-sm-6">
                <div class="flip-card">
                    <div class="flip-card-inner">
                        <div class="flip-card-front">
                            <div class="stat-icon">
                                <i class="fas fa-user-md"></i>
                            </div>
                            <h3>1700+</h3>
                            <p>
                                <span class="en-text">BMDC Verified Doctors</span>
                                <span class="bn-text" style="display: none;">বিএমডিসি প্রত্যয়িত ডাক্তার</span>
                            </p>
                        </div>
                        <div class="flip-card-back">
                            <h3>
                                <span class="en-text">Expert Medical Team</span>
                                <span class="bn-text" style="display: none;">বিশেষজ্ঞ মেডিকেল টিম</span>
                            </h3>
                            <p>
                                <span class="en-text">All our doctors are certified by BMDC and have years of experience</span>
                                <span class="bn-text" style="display: none;">আমাদের সকল ডাক্তার বিএমডিসি দ্বারা প্রত্যয়িত এবং বছরের অভিজ্ঞতা সম্পন্ন</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="flip-card">
                    <div class="flip-card-inner">
                        <div class="flip-card-front">
                            <div class="stat-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h3>10 Minutes</h3>
                            <p>
                                <span class="en-text">Average Waiting Time</span>
                                <span class="bn-text" style="display: none;">গড় অপেক্ষা সময়</span>
                            </p>
                        </div>
                        <div class="flip-card-back">
                            <h3>
                                <span class="en-text">Quick Consultations</span>
                                <span class="bn-text" style="display: none;">দ্রুত পরামর্শ</span>
                            </h3>
                            <p>
                                <span class="en-text">Get connected with a doctor in just 10 minutes on average</span>
                                <span class="bn-text" style="display: none;">গড়ে মাত্র ১০ মিনিটের মধ্যে একজন ডাক্তারের সাথে যুক্ত হন</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="flip-card">
                    <div class="flip-card-inner">
                        <div class="flip-card-front">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <h3>700K+</h3>
                            <p>
                                <span class="en-text">Trusted Users</span>
                                <span class="bn-text" style="display: none;">বিশ্বস্ত ব্যবহারকারী</span>
                            </p>
                        </div>
                        <div class="flip-card-back">
                            <h3>
                                <span class="en-text">Large Community</span>
                                <span class="bn-text" style="display: none;">বৃহৎ সম্প্রদায়</span>
                            </h3>
                            <p>
                                <span class="en-text">Over 700,000 people trust us with their healthcare needs</span>
                                <span class="bn-text" style="display: none;">৭ লাখেরও বেশি মানুষ তাদের স্বাস্থ্যসেবা প্রয়োজনে আমাদের উপর আস্থা রাখেন</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="flip-card">
                    <div class="flip-card-inner">
                        <div class="flip-card-front">
                            <div class="stat-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <h3>95%</h3>
                            <p>
                                <span class="en-text">5-Star Ratings</span>
                                <span class="bn-text" style="display: none;">৫-তারকা রেটিং</span>
                            </p>
                        </div>
                        <div class="flip-card-back">
                            <h3>
                                <span class="en-text">High Satisfaction</span>
                                <span class="bn-text" style="display: none;">উচ্চ সন্তুষ্টি</span>
                            </h3>
                            <p>
                                <span class="en-text">95% of our users rate their experience with 5 stars</span>
                                <span class="bn-text" style="display: none;">আমাদের ৯৫% ব্যবহারকারী তাদের অভিজ্ঞতাকে ৫ তারকা রেটিং দিয়েছেন</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
    <!-- Departments Section with Scroller -->
    <section id="departments" class="departments-section">
        <div class="container">
            <div class="text-center mb-4">
                <h2 class="section-title"><span class="en-text">Our Departments</span><span class="bn-text" style="display: none;">আমাদের বিভাগ</span></h2>
                <p class="section-subtitle"><span class="en-text">We have specialized departments to provide comprehensive healthcare services</span><span class="bn-text" style="display: none;">আমাদের বিশেষায়িত বিভাগ রয়েছে যা ব্যাপক স্বাস্থ্যসেবা প্রদান করে</span></p>
            </div>
            <div class="departments-container">
                <div class="departments-scroller" id="departmentsScroller">
                    <div class="department-card">
                        <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80" alt="Cardiology" class="department-img">
                        <div class="department-content">
                            <h3><span class="en-text">Cardiology</span><span class="bn-text" style="display: none;">কার্ডিওলজি</span></h3>
                            <p><span class="en-text">Our cardiology department provides comprehensive care for heart conditions with state-of-the-art facilities.</span><span class="bn-text" style="display: none;">আমাদের কার্ডিওলজি বিভাগ হৃদরোগের অবস্থার জন্য সর্বশেষ সুবিধাসহ ব্যাপক যত্ন প্রদান করে।</span></p>
                            <button class="department-btn"><span class="en-text">Learn More</span><span class="bn-text" style="display: none;">আরও জানুন</span></button>
                        </div>
                    </div>
                    <div class="department-card">
                        <img src="https://images.unsplash.com/photo-1532938911079-1b06ac7ceec7?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80" alt="Neurology" class="department-img">
                        <div class="department-content">
                            <h3><span class="en-text">Neurology</span><span class="bn-text" style="display: none;">নিউরোলজি</span></h3>
                            <p><span class="en-text">Expert diagnosis and treatment for disorders of the brain, spinal cord, and nervous system.</span><span class="bn-text" style="display: none;">মস্তিষ্ক, স্নায়ুতন্ত্র এবং স্নায়ুতন্ত্রের ব্যাধির জন্য বিশেষজ্ঞ রোগ নির্ণয় এবং চিকিৎসা।</span></p>
                            <button class="department-btn"><span class="en-text">Learn More</span><span class="bn-text" style="display: none;">আরও জানুন</span></button>
                        </div>
                    </div>
                    <div class="department-card">
                        <img src="https://images.unsplash.com/photo-1579684385127-acec1938f2d7?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80" alt="Pediatrics" class="department-img">
                        <div class="department-content">
                            <h3><span class="en-text">Pediatrics</span><span class="bn-text" style="display: none;">পেডিয়াট্রিক্স</span></h3>
                            <p><span class="en-text">Comprehensive healthcare services for infants, children, and adolescents with compassionate care.</span><span class="bn-text" style="display: none;">সহানুভূতিশীল যত্নের সাথে শিশু, শিশু এবং কিশোর-কিশোরীদের জন্য ব্যাপক স্বাস্থ্যসেবা পরিষেবা।</span></p>
                            <button class="department-btn"><span class="en-text">Learn More</span><span class="bn-text" style="display: none;">আরও জানুন</span></button>
                        </div>
                    </div>
                    <div class="department-card">
                        <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80" alt="Orthopedics" class="department-img">
                        <div class="department-content">
                            <h3><span class="en-text">Orthopedics</span><span class="bn-text" style="display: none;">অর্থোপেডিক্স</span></h3>
                            <p><span class="en-text">Specialized care for bones, joints, ligaments, and muscles with advanced surgical techniques.</span><span class="bn-text" style="display: none;">উন্নত সার্জিক্যাল কৌশল সহ হাড়, জয়েন্ট, লিগামেন্ট এবং পেশীর জন্য বিশেষায়িত যত্ন।</span></p>
                            <button class="department-btn"><span class="en-text">Learn More</span><span class="bn-text" style="display: none;">আরও জানুন</span></button>
                        </div>
                    </div>
                    <div class="department-card">
                        <img src="https://images.unsplash.com/photo-1532938911079-1b06ac7ceec7?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80" alt="Gynecology" class="department-img">
                        <div class="department-content">
                            <h3><span class="en-text">Gynecology</span><span class="bn-text" style="display: none;">গাইনোকোলজি</span></h3>
                            <p><span class="en-text">Complete women's healthcare services from routine check-ups to specialized treatments.</span><span class="bn-text" style="display: none;">রুটিন চেক-আপ থেকে শুরু করে বিশেষায়িত চিকিৎসা পর্যন্ত সম্পূর্ণ মহিলা স্বাস্থ্যসেবা পরিষেবা।</span></p>
                            <button class="department-btn"><span class="en-text">Learn More</span><span class="bn-text" style="display: none;">আরও জানুন</span></button>
                        </div>
                    </div>
                    <div class="department-card">
                        <img src="https://images.unsplash.com/photo-1579684385127-acec1938f2d7?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80" alt="Radiology" class="department-img">
                        <div class="department-content">
                            <h3><span class="en-text">Radiology</span><span class="bn-text" style="display: none;">রেডিওলজি</span></h3>
                            <p><span class="en-text">Advanced imaging services for accurate diagnosis with cutting-edge technology.</span><span class="bn-text" style="display: none;">কাটিং-এজ প্রযুক্তি সহ নির্ভুল রোগ নির্ণয়ের জন্য উন্নত ইমেজিং পরিষেবা।</span></p>
                            <button class="department-btn"><span class="en-text">Learn More</span><span class="bn-text" style="display: none;">আরও জানুন</span></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="scroll-controls">
                <button class="scroll-btn" id="scrollLeft"><i class="fas fa-chevron-left"></i></button>
                <button class="scroll-btn" id="scrollRight"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </section>

    <!-- Doctors Section -->
    <section id="doctors" class="doctors-section">
        <div class="container">
            <div class="text-center mb-4">
                <h2 class="section-title"><span class="en-text">Our Doctors</span><span class="bn-text" style="display: none;">আমাদের ডাক্তার</span></h2>
                <p class="section-subtitle"><span class="en-text">Meet our team of experienced and compassionate healthcare professionals</span><span class="bn-text" style="display: none;">আমাদের অভিজ্ঞ এবং সহানুভূতিশীল স্বাস্থ্যসেবা পেশাদারদের দলের সাথে পরিচিত হন</span></p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="doctor-card">
                        <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Doctor" class="doctor-img">
                        <div class="doctor-content">
                            <h4 class="doctor-name"><span class="en-text">Dr. Ahmed Khan</span><span class="bn-text" style="display: none;">ডাঃ আহমেদ খান</span></h4>
                            <p class="doctor-specialty"><span class="en-text">Cardiologist</span><span class="bn-text" style="display: none;">হৃদরোগ বিশেষজ্ঞ</span></p>
                            <p class="doctor-bio"><span class="en-text">15 years of experience in interventional cardiology with numerous publications.</span><span class="bn-text" style="display: none;">হস্তক্ষেপমূলক কার্ডিওলজিতে ১৫ বছরের অভিজ্ঞতা এবং অসংখ্য প্রকাশনা।</span></p>
                            <div class="doctor-social">
                                <a href="#"><i class="fab fa-facebook-f"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                            <button class="doctor-btn"><span class="en-text">View Profile</span><span class="bn-text" style="display: none;">প্রোফাইল দেখুন</span></button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="doctor-card">
                        <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Doctor" class="doctor-img">
                        <div class="doctor-content">
                            <h4 class="doctor-name"><span class="en-text">Dr. Fatima Rahman</span><span class="bn-text" style="display: none;">ডাঃ ফাতেমা রহমান</span></h4>
                            <p class="doctor-specialty"><span class="en-text">Neurologist</span><span class="bn-text" style="display: none;">নিউরোলজিস্ট</span></p>
                            <p class="doctor-bio"><span class="en-text">Specialized in stroke treatment and neurodegenerative disorders.</span><span class="bn-text" style="display: none;">স্ট্রোক চিকিৎসা এবং নিউরোডিজেনারেটিভ ডিসঅর্ডারে বিশেষজ্ঞ।</span></p>
                            <div class="doctor-social">
                                <a href="#"><i class="fab fa-facebook-f"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                            <button class="doctor-btn"><span class="en-text">View Profile</span><span class="bn-text" style="display: none;">প্রোফাইল দেখুন</span></button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="doctor-card">
                        <img src="https://randomuser.me/api/portraits/men/67.jpg" alt="Doctor" class="doctor-img">
                        <div class="doctor-content">
                            <h4 class="doctor-name"><span class="en-text">Dr. Mohammad Ali</span><span class="bn-text" style="display: none;">ডাঃ মোহাম্মদ আলী</span></h4>
                            <p class="doctor-specialty"><span class="en-text">Pediatrician</span><span class="bn-text" style="display: none;">শিশুরোগ বিশেষজ্ঞ</span></p>
                            <p class="doctor-bio"><span class="en-text">Dedicated to providing comprehensive care for children of all ages.</span><span class="bn-text" style="display: none;">সব বয়সী শিশুদের জন্য ব্যাপক যত্ন প্রদানে নিবেদিত।</span></p>
                            <div class="doctor-social">
                                <a href="#"><i class="fab fa-facebook-f"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                            <button class="doctor-btn"><span class="en-text">View Profile</span><span class="bn-text" style="display: none;">প্রোফাইল দেখুন</span></button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="doctor-card">
                        <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Doctor" class="doctor-img">
                        <div class="doctor-content">
                            <h4 class="doctor-name"><span class="en-text">Dr. Nusrat Jahan</span><span class="bn-text" style="display: none;">ডাঃ নুসরাত জাহান</span></h4>
                            <p class="doctor-specialty"><span class="en-text">Gynecologist</span><span class="bn-text" style="display: none;">গাইনোকোলজিস্ট</span></p>
                            <p class="doctor-bio"><span class="en-text">Expert in high-risk pregnancies and minimally invasive surgeries.</span><span class="bn-text" style="display: none;">ঝুঁকিপূর্ণ গর্ভাবস্থা এবং ন্যূনতম আক্রমণাত্মক সার্জারিতে বিশেষজ্ঞ।</span></p>
                            <div class="doctor-social">
                                <a href="#"><i class="fab fa-facebook-f"></i></a>
                                <a href="#"><i class="fab fa-twitter"></i></a>
                                <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                            <button class="doctor-btn"><span class="en-text">View Profile</span><span class="bn-text" style="display: none;">প্রোফাইল দেখুন</span></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Appointment Section -->
    <section id="appointment" class="appointment-section">
        <div class="container">
            <div class="appointment-content">
                <h2><span class="en-text">Need an Appointment?</span><span class="bn-text" style="display: none;">অ্যাপয়েন্টমেন্ট প্রয়োজন?</span></h2>
                <p><span class="en-text">Book your appointment online with our expert doctors and get the best healthcare services.</span><span class="bn-text" style="display: none;">আমাদের বিশেষজ্ঞ ডাক্তারদের সাথে অনলাইনে আপনার অ্যাপয়েন্টমেন্ট বুক করুন এবং সেরা স্বাস্থ্যসেবা পান।</span></p>
                <button class="appointment-btn"><span class="en-text">Book Appointment Now</span><span class="bn-text" style="display: none;">এখনই অ্যাপয়েন্টমেন্ট বুক করুন</span></button>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="testimonials-section">
        <div class="container">
            <div class="text-center mb-4">
                <h2 class="section-title"><span class="en-text">Patient Testimonials</span><span class="bn-text" style="display: none;">রোগীর প্রশংসাপত্র</span></h2>
                <p class="section-subtitle"><span class="en-text">Hear what our patients have to say about their experience with us</span><span class="bn-text" style="display: none;">আমাদের সাথে তাদের অভিজ্ঞতা সম্পর্কে আমাদের রোগীরা কি বলেছেন তা শুনুন</span></p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="testimonial-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p><span class="en-text">AmraAchi saved my father's life during a heart attack. The ambulance arrived in just 8 minutes!</span><span class="bn-text" style="display: none;">আমরাআছি হার্ট অ্যাটাকের সময় আমার বাবার জীবন বাঁচিয়েছে। অ্যাম্বুলেন্স মাত্র ৮ মিনিটের মধ্যে এসেছিল!</span></p>
                        <div class="client-info">
                            <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Client" class="client-img">
                            <div>
                                <h4 class="client-name"><span class="en-text">Fatima Rahman</span><span class="bn-text" style="display: none;">ফাতেমা রহমান</span></h4>
                                <p class="client-title"><span class="en-text">Patient's Daughter</span><span class="bn-text" style="display: none;">রোগীর কন্যা</span></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="testimonial-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p><span class="en-text">As a doctor, AmraAchi has made it easier to manage appointments and access patient histories instantly.</span><span class="bn-text" style="display: none;">একজন ডাক্তার হিসেবে, আমরাআছি অ্যাপয়েন্টমেন্ট পরিচালনা এবং রোগীর ইতিহাস তাত্ক্ষণিকভাবে অ্যাক্সেস করা সহজ করেছে।</span></p>
                        <div class="client-info">
                            <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Client" class="client-img">
                            <div>
                                <h4 class="client-name"><span class="en-text">Dr. Ahmed Khan</span><span class="bn-text" style="display: none;">ডাঃ আহমেদ খান</span></h4>
                                <p class="client-title"><span class="en-text">Cardiologist</span><span class="bn-text" style="display: none;">হৃদরোগ বিশেষজ্ঞ</span></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="testimonial-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p><span class="en-text">Booking home nursing care for my elderly mother has never been easier. The nurses are professional and caring.</span><span class="bn-text" style="display: none;">আমার বৃদ্ধ মায়ের জন্য হোম নার্সিং কেয়ার বুকিং কখনোই এত সহজ ছিল না। নার্সরা পেশাদার এবং যত্নশীল।</span></p>
                        <div class="client-info">
                            <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Client" class="client-img">
                            <div>
                                <h4 class="client-name"><span class="en-text">Nusrat Jahan</span><span class="bn-text" style="display: none;">নুসরাত জাহান</span></h4>
                                <p class="client-title"><span class="en-text">Caregiver</span><span class="bn-text" style="display: none;">যত্নকারী</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="footer-logo">AmraAchi</div>
                    <p><span class="en-text">Your complete digital healthcare platform connecting patients, doctors, and healthcare services for better health outcomes.</span><span class="bn-text" style="display: none;">আপনার সম্পূর্ণ ডিজিটাল স্বাস্থ্যসেবা প্ল্যাটফর্ম যা রোগী, ডাক্তার এবং স্বাস্থ্যসেবা সেবাকে উন্নত স্বাস্থ্য ফলাফলের জন্য সংযুক্ত করে।</span></p>
                    <div class="social-icons-footer">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <div class="footer-links">
                        <h5><span class="en-text">Quick Links</span><span class="bn-text" style="display: none;">দ্রুত লিঙ্ক</span></h5>
                        <ul>
                            <li><a href="#"><span class="en-text">Home</span><span class="bn-text" style="display: none;">হোম</span></a></li>
                            <li><a href="#"><span class="en-text">About Us</span><span class="bn-text" style="display: none;">আমাদের সম্পর্কে</span></a></li>
                            <li><a href="#"><span class="en-text">Services</span><span class="bn-text" style="display: none;">সেবা</span></a></li>
                            <li><a href="#"><span class="en-text">Departments</span><span class="bn-text" style="display: none;">বিভাগ</span></a></li>
                            <li><a href="#"><span class="en-text">Doctors</span><span class="bn-text" style="display: none;">ডাক্তার</span></a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <div class="footer-links">
                        <h5><span class="en-text">Services</span><span class="bn-text" style="display: none;">সেবা</span></h5>
                        <ul>
                            <li><a href="#"><span class="en-text">Emergency Care</span><span class="bn-text" style="display: none;">জরুরি যত্ন</span></a></li>
                            <li><a href="#"><span class="en-text">Appointments</span><span class="bn-text" style="display: none;">অ্যাপয়েন্টমেন্ট</span></a></li>
                            <li><a href="#"><span class="en-text">Health Records</span><span class="bn-text" style="display: none;">স্বাস্থ্য রেকর্ড</span></a></li>
                            <li><a href="#"><span class="en-text">Home Care</span><span class="bn-text" style="display: none;">হোম কেয়ার</span></a></li>
                            <li><a href="#"><span class="en-text">E-Prescriptions</span><span class="bn-text" style="display: none;">ই-প্রেসক্রিপশন</span></a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="footer-links">
                        <h5><span class="en-text">Contact Us</span><span class="bn-text" style="display: none;">যোগাযোগ করুন</span></h5>
                        <ul class="footer-contact">
                            <li>
                                <i class="fas fa-map-marker-alt"></i>
                                <span><span class="en-text">123 Healthcare Ave, Dhaka, Bangladesh</span><span class="bn-text" style="display: none;">১২৩ হেলথকেয়ার অ্যাভিনিউ, ঢাকা, বাংলাদেশ</span></span>
                            </li>
                            <li>
                                <i class="fas fa-phone-alt"></i>
                                <span><span class="en-text">+880 1234 567890</span><span class="bn-text" style="display: none;">+৮৮০ ১২৩৪ ৫৬৭৮৯০</span></span>
                            </li>
                            <li>
                                <i class="fas fa-envelope"></i>
                                <span><span class="en-text">info@amraaichi.com</span><span class="bn-text" style="display: none;">info@amraaichi.com</span></span>
                            </li>
                            <li>
                                <i class="fas fa-clock"></i>
                                <span><span class="en-text">Mon-Fri: 9am-6pm</span><span class="bn-text" style="display: none;">সোম-শুক্র: সকাল ৯টা-সন্ধ্যা ৬টা</span></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="footer-newsletter">
                        <h5><span class="en-text">Subscribe to Our Newsletter</span><span class="bn-text" style="display: none;">আমাদের নিউজলেটার সাবস্ক্রাইব করুন</span></h5>
                        <p><span class="en-text">Stay updated with our latest news and health tips</span><span class="bn-text" style="display: none;">আমাদের সর্বশেষ খবর এবং স্বাস্থ্য টিপস দিয়ে আপডেট থাকুন</span></p>
                        <form class="newsletter-form">
                            <input type="email" class="newsletter-input" data-placeholder-en="Your email address" data-placeholder-bn="আপনার ইমেইল ঠিকানা" placeholder="Your email address">
                            <button type="submit" class="newsletter-btn"><span class="en-text">Subscribe</span><span class="bn-text" style="display: none;">সাবস্ক্রাইব</span></button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="copyright">
                <p><span class="en-text">&copy; 2023 AmraAchi. All rights reserved.</span><span class="bn-text" style="display: none;">&copy; ২০২৩ আমরাআছি। সর্বস্বত্ব সংরক্ষিত।</span></p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Department Scroller
        const departmentsScroller = document.getElementById('departmentsScroller');
        const scrollLeftBtn = document.getElementById('scrollLeft');
        const scrollRightBtn = document.getElementById('scrollRight');

        scrollLeftBtn.addEventListener('click', () => {
            departmentsScroller.scrollBy({
                left: -300,
                behavior: 'smooth'
            });
        });

        scrollRightBtn.addEventListener('click', () => {
            departmentsScroller.scrollBy({
                left: 300,
                behavior: 'smooth'
            });
        });

        // Language Toggle
        const langToggle = document.getElementById('langToggle');
        let isBangla = false;

        function applyPlaceholdersForLanguageLanding(isBangla) {
            document.querySelectorAll('[data-placeholder-en]').forEach(function(el) {
                try {
                    const en = el.getAttribute('data-placeholder-en');
                    const bn = el.getAttribute('data-placeholder-bn');
                    el.placeholder = isBangla && bn ? bn : (en || '');
                } catch (e) {}
            });
        }

        langToggle.addEventListener('click', () => {
            isBangla = !isBangla;
            const enTexts = document.querySelectorAll('.en-text');
            const bnTexts = document.querySelectorAll('.bn-text');
            
            if (isBangla) {
                enTexts.forEach(text => text.style.display = 'none');
                bnTexts.forEach(text => text.style.display = 'inline');
                langToggle.textContent = 'English';
            } else {
                enTexts.forEach(text => text.style.display = 'inline');
                bnTexts.forEach(text => text.style.display = 'none');
                langToggle.textContent = 'বাংলা';
            }
            applyPlaceholdersForLanguageLanding(isBangla);
        });

        // Apply placeholders on load
        applyPlaceholdersForLanguageLanding(isBangla);

        // Epidemic Alert
        const epidemicButton = document.querySelector('.epidemic-button');
        const epidemicAlert = document.getElementById('epidemic-alert');
        const closeEpidemic = document.getElementById('closeEpidemic');
        const epidemicBanner = document.getElementById('epidemicBanner');
        const closeBanner = document.getElementById('closeBanner');

        function showEpidemicDetails() {
            epidemicAlert.classList.add('active');
            window.scrollTo({
                top: epidemicAlert.offsetTop - 100,
                behavior: 'smooth'
            });
        }

        epidemicButton.addEventListener('click', showEpidemicDetails);

        closeEpidemic.addEventListener('click', () => {
            epidemicAlert.classList.remove('active');
        });

        closeBanner.addEventListener('click', () => {
            epidemicBanner.style.display = 'none';
        });


        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    window.scrollTo({
                        top: target.offsetTop - 150,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Add active class to navigation items on scroll
        window.addEventListener('scroll', () => {
            let current = '';
            const sections = document.querySelectorAll('section');
            
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (scrollY >= (sectionTop - 200)) {
                    current = section.getAttribute('id');
                }
            });

            document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href').substring(1) === current) {
                    link.classList.add('active');
                }
            });
        });

        // Initialize map
        let map;
        let userLocation;
        
        // Mock hospital data
        const hospitals = [
            {
                name: "United Hospital Limited",
                address: "Plot 15, Road 71, Gulshan, Dhaka 1212",
                lat: 23.7810,
                lng: 90.4150,
                phone: "+880 2-55034567"
            },
            {
                name: "Popular Diagnostic Centre",
                address: "House 16, Road 2, Dhanmondi, Dhaka 1205",
                lat: 23.7465,
                lng: 90.3760,
                phone: "+880 2-55012345"
            },
            {
                name: "Ibn Sina Diagnostic & Consultation Center",
                address: "House 48, Road 27, Block K, Banani, Dhaka 1213",
                lat: 23.7925,
                lng: 90.4065,
                phone: "+880 2-55098765"
            },
            {
                name: "Labaid Specialized Hospital",
                address: "House 78, Road 11/A, Dhanmondi, Dhaka 1209",
                lat: 23.7490,
                lng: 90.3725,
                phone: "+880 2-55024680"
            },
            {
                name: "Bangabandhu Sheikh Mujib Medical University",
                address: "Shahbag, Dhaka 1000",
                lat: 23.7380,
                lng: 90.3940,
                phone: "+880 2-55013579"
            },
            {
                name: "Evercare Hospital Dhaka",
                address: "Plot 81, Block E, Bashundhara R/A, Dhaka 1229",
                lat: 23.8125,
                lng: 90.4250,
                phone: "+880 2-55011223"
            }
        ];

        // Function to calculate distance between two points in km
        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371; // Radius of the earth in km
            const dLat = deg2rad(lat2 - lat1);
            const dLon = deg2rad(lon2 - lon1);
            const a = 
                Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * 
                Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
            const d = R * c; // Distance in km
            return d;
        }

        function deg2rad(deg) {
            return deg * (Math.PI/180);
        }

        // Initialize map when page loads
        window.onload = function() {
            // Try to get user's location
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    position => {
                        userLocation = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };
                        initializeMap();
                    },
                    error => {
                        // Default location if geolocation fails (Dhaka city center)
                        userLocation = {
                            lat: 23.8103,
                            lng: 90.4125
                        };
                        initializeMap();
                    }
                );
            } else {
                // Default location if geolocation is not supported
                userLocation = {
                    lat: 23.8103,
                    lng: 90.4125
                };
                initializeMap();
            }
        };

        function initializeMap() {
            // Create map centered at user's location
            map = L.map('map').setView([userLocation.lat, userLocation.lng], 13);

            // Add tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Add user location marker
            L.marker([userLocation.lat, userLocation.lng])
                .addTo(map)
                .bindPopup(isBangla ? 'আপনার অবস্থান' : 'Your Location')
                .openPopup();

            // Filter hospitals within 2-8km radius
            const nearbyHospitals = hospitals.filter(hospital => {
                const distance = calculateDistance(
                    userLocation.lat, userLocation.lng,
                    hospital.lat, hospital.lng
                );
                hospital.distance = distance;
                return distance >= 2 && distance <= 8;
            });

            // If no hospitals found in the range, expand to show all hospitals within 10km
            let hospitalsToShow = nearbyHospitals;
            if (nearbyHospitals.length === 0) {
                hospitalsToShow = hospitals.filter(hospital => {
                    const distance = calculateDistance(
                        userLocation.lat, userLocation.lng,
                        hospital.lat, hospital.lng
                    );
                    hospital.distance = distance;
                    return distance <= 10;
                });
            }

            // Add hospital markers and create list
            const hospitalList = document.getElementById('hospital-list');
            hospitalList.innerHTML = '';

            hospitalsToShow.forEach(hospital => {
                // Add marker to map
                const marker = L.marker([hospital.lat, hospital.lng])
                    .addTo(map)
                    .bindPopup(`
                        <b>${hospital.name}</b><br>
                        ${hospital.address}<br>
                        ${isBangla ? 'দূরত্ব: ' : 'Distance: '}${hospital.distance.toFixed(2)} km<br>
                        ${isBangla ? 'ফোন: ' : 'Phone: '}${hospital.phone}
                    `);

                // Create hospital card for the list
                const hospitalCard = document.createElement('div');
                hospitalCard.className = 'hospital-card';
                hospitalCard.innerHTML = `
                    <div class="hospital-name">${hospital.name}</div>
                    <div class="hospital-address"><i class="fas fa-map-marker-alt me-2"></i>${hospital.address}</div>
                    <div class="hospital-distance"><i class="fas fa-route me-2"></i>${isBangla ? 'দূরত্ব: ' : 'Distance: '}${hospital.distance.toFixed(2)} km</div>
                    <button class="hospital-details-btn" onclick="showHospitalDetails('${hospital.name}')">${isBangla ? 'বিস্তারিত দেখুন' : 'View Details'}</button>
                `;
                hospitalList.appendChild(hospitalCard);

                // Highlight marker when hovering over hospital card
                hospitalCard.addEventListener('mouseenter', () => {
                    marker.openPopup();
                });

                hospitalCard.addEventListener('mouseleave', () => {
                    marker.closePopup();
                });

                // Center map on hospital when clicking on hospital card
                hospitalCard.addEventListener('click', () => {
                    map.setView([hospital.lat, hospital.lng], 15);
                });
            });

            // If no hospitals found, show a message
            if (hospitalsToShow.length === 0) {
                hospitalList.innerHTML = `<p class="text-center">${isBangla ? 'আপনার অবস্থানের ১০কিমি এর মধ্যে কোন হাসপাতাল পাওয়া যায়নি।' : 'No hospitals found within 10km of your location.'}</p>`;
            }
        }

        // Function to show hospital details (placeholder)
        function showHospitalDetails(hospitalName) {
            alert(isBangla ? `${hospitalName} এর বিস্তারিত দেখানো হচ্ছে। একটি প্রকৃত অ্যাপ্লিকেশনে, এটি একটি বিস্তারিত দৃশ্য খুলবে।` : `Showing details for ${hospitalName}. In a real application, this would open a detailed view.`);
        }
    </script>
</body>
</html>