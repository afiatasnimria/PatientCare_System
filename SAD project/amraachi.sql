-- Create database
CREATE DATABASE IF NOT EXISTS amraachi;
USE amraachi;

-- Users table
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('patient', 'doctor', 'nurse', 'driver', 'hospital', 'admin') NOT NULL,
    specialization VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Appointments table
CREATE TABLE appointments (
    appointment_id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    appointment_date DATETIME NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    FOREIGN KEY (patient_id) REFERENCES users(user_id),
    FOREIGN KEY (doctor_id) REFERENCES users(user_id)
);

-- Health Records table
CREATE TABLE health_records (
    record_id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT,
    diagnosis TEXT,
    treatment TEXT,
    record_date DATE NOT NULL,
    FOREIGN KEY (patient_id) REFERENCES users(user_id),
    FOREIGN KEY (doctor_id) REFERENCES users(user_id)
);

-- Medicines table
CREATE TABLE medicines (
    medicine_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL
);

-- Prescriptions table
CREATE TABLE prescriptions (
    prescription_id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    medicine_id INT NOT NULL,
    dosage VARCHAR(50),
    instructions TEXT,
    prescribed_date DATE NOT NULL,
    FOREIGN KEY (patient_id) REFERENCES users(user_id),
    FOREIGN KEY (doctor_id) REFERENCES users(user_id),
    FOREIGN KEY (medicine_id) REFERENCES medicines(medicine_id)
);

-- Emergency Requests table
CREATE TABLE emergency_requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    driver_id INT,
    hospital_id INT,
    type ENUM('ambulance', 'icu', 'general') NOT NULL,
    status ENUM('pending', 'in_progress', 'completed') DEFAULT 'pending',
    request_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES users(user_id),
    FOREIGN KEY (driver_id) REFERENCES users(user_id),
    FOREIGN KEY (hospital_id) REFERENCES users(user_id)
);

-- Epidemic Alerts table
CREATE TABLE epidemic_alerts (
    alert_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    alert_date DATE NOT NULL
);

-- Health Tips table
CREATE TABLE health_tips (
    tip_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Symptom Checker Logs
CREATE TABLE symptom_checker_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    symptoms TEXT NOT NULL,
    possible_conditions TEXT,
    check_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES users(user_id)
);

-- Daycare Services table
CREATE TABLE daycare_services (
    service_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    cost DECIMAL(10,2)
);

-- Nurse Bookings table
CREATE TABLE nurse_bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    nurse_id INT NOT NULL,
    booking_date DATE NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    FOREIGN KEY (patient_id) REFERENCES users(user_id),
    FOREIGN KEY (nurse_id) REFERENCES users(user_id)
);

-- Sample Data Insertion
INSERT INTO users (full_name, email, password, phone, role, specialization) VALUES
('John Doe', 'john@example.com', 'hashedpassword', '0123456789', 'patient', NULL),
('Dr. Alice Smith', 'alice@example.com', 'hashedpassword', '0198765432', 'doctor', 'Cardiology'),
('Nurse Mary', 'mary@example.com', 'hashedpassword', '0171122334', 'nurse', NULL),
('Driver Rahim', 'rahim@example.com', 'hashedpassword', '0161234567', 'driver', NULL),
('City Hospital', 'hospital@example.com', 'hashedpassword', '0149988776', 'hospital', 'Multi-specialty'),
('Admin User', 'admin@example.com', 'hashedpassword', '0155566778', 'admin', NULL);

INSERT INTO medicines (name, description, price) VALUES
('Paracetamol', 'Pain reliever and fever reducer', 1.50),
('Amoxicillin', 'Antibiotic for bacterial infections', 3.20);

INSERT INTO appointments (patient_id, doctor_id, appointment_date, status, notes) VALUES
(1, 2, '2025-08-20 10:00:00', 'confirmed', 'Regular check-up');

INSERT INTO health_records (patient_id, doctor_id, diagnosis, treatment, record_date) VALUES
(1, 2, 'Fever and headache', 'Paracetamol 500mg twice daily', '2025-08-10');

INSERT INTO prescriptions (patient_id, doctor_id, medicine_id, dosage, instructions, prescribed_date) VALUES
(1, 2, 1, '500mg', 'Take after meals', '2025-08-10');

INSERT INTO emergency_requests (patient_id, driver_id, hospital_id, type, status) VALUES
(1, 4, 5, 'ambulance', 'pending');

INSERT INTO epidemic_alerts (title, description, alert_date) VALUES
('COVID-19 Alert', 'Maintain social distancing and wear masks', '2025-08-01');

INSERT INTO health_tips (title, content) VALUES
('Stay Hydrated', 'Drink at least 8 glasses of water a day.');

INSERT INTO symptom_checker_logs (patient_id, symptoms, possible_conditions) VALUES
(1, 'Fever, cough, fatigue', 'Flu, COVID-19');

INSERT INTO daycare_services (name, description, cost) VALUES
('Minor Surgery', 'Small-scale outpatient surgery', 500.00);

INSERT INTO nurse_bookings (patient_id, nurse_id, booking_date, status) VALUES
(1, 3, '2025-08-15', 'confirmed');
