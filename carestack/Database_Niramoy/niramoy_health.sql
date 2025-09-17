-- Niramoy Healthcare System Database
-- Bangladesh-based Healthcare Management System

-- Create database
CREATE DATABASE IF NOT EXISTS niramoy_health;
USE niramoy_health;

-- =============================================
-- TABLES FOR USER MANAGEMENT
-- =============================================

-- Users table (core user information)
CREATE TABLE users (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    date_of_birth DATE DEFAULT NULL,
    gender ENUM('male', 'female', 'other') DEFAULT NULL,
    blood_group ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') DEFAULT NULL,
    profile_image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Roles table
CREATE TABLE roles (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    description TEXT DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default roles
INSERT INTO roles (name, description) VALUES
('patient', 'Patient role'),
('doctor', 'Doctor role'),
('nurse', 'Nurse role'),
('compounder', 'Compounder role'),
('ambulance', 'Ambulance Driver role'),
('admin', 'Administrator role');

-- User roles (many-to-many relationship)
CREATE TABLE user_roles (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    role_id INT(11) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY (user_role_unique) (user_id, role_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- TABLES FOR DOCTOR REGISTRATION (BM&DC CODE)
-- =============================================

-- Doctors table (BM&DC code verification)
CREATE TABLE doctors (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    bmdc_code VARCHAR(20) NOT NULL,
    specialization VARCHAR(100) DEFAULT NULL,
    experience_years INT(11) DEFAULT 0,
    consultation_fee DECIMAL(10,2) DEFAULT 0.00,
    is_verified BOOLEAN DEFAULT FALSE,
    verification_document VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY (bmdc_code),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- TABLES FOR NURSE MANAGEMENT
-- =============================================

-- Nurses table
CREATE TABLE nurses (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    is_daycare BOOLEAN DEFAULT FALSE,
    is_compounder BOOLEAN DEFAULT FALSE,
    specialization VARCHAR(100) DEFAULT NULL,
    license_number VARCHAR(50) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Doctor-Compounder relationship
CREATE TABLE doctor_compounders (
    id INT(11) NOT NULL AUTO_INCREMENT,
    doctor_id INT(11) NOT NULL,
    nurse_id INT(11) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY (doctor_nurse_unique) (doctor_id, nurse_id),
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    FOREIGN KEY (nurse_id) REFERENCES nurses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- TABLES FOR HOSPITAL MANAGEMENT
-- =============================================

-- Hospitals table
CREATE TABLE hospitals (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    address VARCHAR(255) NOT NULL,
    district VARCHAR(50) NOT NULL,
    division VARCHAR(50) NOT NULL,
    latitude DECIMAL(10,8) NOT NULL,
    longitude DECIMAL(11,8) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    email VARCHAR(100) DEFAULT NULL,
    website VARCHAR(255) DEFAULT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Hospital-doctor relationship
CREATE TABLE hospital_doctors (
    id INT(11) NOT NULL AUTO_INCREMENT,
    hospital_id INT(11) NOT NULL,
    doctor_id INT(11) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY (hospital_doctor_unique) (hospital_id, doctor_id),
    FOREIGN KEY (hospital_id) REFERENCES hospitals(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- TABLES FOR DISEASE AND EPIDEMIC TRACKING
-- =============================================

-- Diseases table (including epidemic diseases)
CREATE TABLE diseases (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    is_epidemic BOOLEAN DEFAULT FALSE,
    symptoms TEXT DEFAULT NULL,
    prevention TEXT DEFAULT NULL,
    treatment TEXT DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert epidemic diseases (including Dengue)
INSERT INTO diseases (name, description, is_epidemic, symptoms, prevention, treatment) VALUES
('Dengue', 'Dengue fever is a mosquito-borne tropical disease caused by the dengue virus.', TRUE, 
 'Sudden high fever, Severe headaches, Pain behind the eyes, Severe joint and muscle pain, Fatigue, Nausea, Skin rash',
 'Use mosquito repellent, Wear long-sleeved clothes, Eliminate standing water where mosquitoes breed',
 'No specific treatment, Rest, Fluid intake, Pain relievers (avoid aspirin)'),
('Chikungunya', 'Chikungunya is a viral disease transmitted to humans by infected mosquitoes.', TRUE,
 'Sudden fever, Severe joint pain, Muscle pain, Headache, Nausea, Fatigue, Rash',
 'Use mosquito repellent, Wear protective clothing, Sleep under mosquito nets',
 'No specific antiviral treatment, Rest, Fluids, Pain relievers'),
('Malaria', 'Malaria is a mosquito-borne infectious disease affecting humans and other animals.', TRUE,
 'Fever, Chills, Headache, Nausea and vomiting, Muscle pain and fatigue',
 'Use mosquito nets, Insect repellent, Antimalarial medication',
 'Antimalarial drugs'),
('COVID-19', 'Coronavirus disease 2019 (COVID-19) is an infectious disease caused by SARS-CoV-2.', TRUE,
 'Fever, Cough, Shortness of breath, Loss of taste or smell, Fatigue',
 'Vaccination, Wear masks, Social distancing, Hand hygiene',
 'Antiviral medications, Supportive care'),
('Tuberculosis', 'Tuberculosis (TB) is a potentially serious infectious disease that mainly affects the lungs.', TRUE,
 'Persistent cough (sometimes with blood), Chest pain, Weakness, Weight loss, Fever, Night sweats',
 'BCG vaccine, Avoid contact with infected people, Good ventilation',
 'Antibiotic treatment for several months'),
('Diarrhea', 'Diarrhea is a common condition characterized by loose, watery stools.', FALSE,
 'Loose, watery stools, Abdominal cramps, Bloating, Urgent need to have a bowel movement',
 'Hand hygiene, Safe drinking water, Proper food handling',
 'Rehydration, Zinc supplements, Antibiotics for bacterial cases'),
('Pneumonia', 'Pneumonia is an infection that inflames the air sacs in one or both lungs.', FALSE,
 'Chest pain when breathing or coughing, Confusion or changes in mental awareness, Cough, Fatigue, Fever',
 'Vaccination, Good hygiene, Not smoking',
 'Antibiotics, Antiviral drugs, Fever reducers'),
('Typhoid', 'Typhoid fever is a bacterial infection that can spread throughout the body.', TRUE,
 'Sustained fever, Weakness, Stomach pain, Headache, Diarrhea or constipation, Loss of appetite',
 'Typhoid vaccination, Safe food and water, Hand hygiene',
 'Antibiotic treatment'),
('Hepatitis A', 'Hepatitis A is a viral liver disease that can cause mild to severe illness.', TRUE,
 'Fatigue, Sudden nausea and vomiting, Abdominal pain, Loss of appetite, Low-grade fever, Dark urine',
 'Vaccination, Good hygiene, Safe food and water',
 'Supportive care, Rest, Proper nutrition'),
('Hepatitis B', 'Hepatitis B is a viral infection that attacks the liver.', TRUE,
 'Abdominal pain, Dark urine, Fever, Joint pain, Loss of appetite, Nausea, Weakness and fatigue',
 'Vaccination, Safe sex, Avoid sharing needles',
 'Antiviral medications, Liver transplant in severe cases');

-- Patient diseases tracking
CREATE TABLE patient_diseases (
    id INT(11) NOT NULL AUTO_INCREMENT,
    patient_id INT(11) NOT NULL,
    disease_id INT(11) NOT NULL,
    diagnosis_date DATE NOT NULL,
    status ENUM('active', 'recovered', 'chronic') DEFAULT 'active',
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (disease_id) REFERENCES diseases(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- TABLES FOR AMBULANCE MANAGEMENT
-- =============================================

-- Ambulances table
CREATE TABLE ambulances (
    id INT(11) NOT NULL AUTO_INCREMENT,
    hospital_id INT(11) NOT NULL,
    vehicle_number VARCHAR(20) NOT NULL,
    driver_name VARCHAR(100) DEFAULT NULL,
    driver_phone VARCHAR(20) DEFAULT NULL,
    is_available BOOLEAN DEFAULT TRUE,
    latitude DECIMAL(10,8) DEFAULT NULL,
    longitude DECIMAL(11,8) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY (vehicle_number),
    FOREIGN KEY (hospital_id) REFERENCES hospitals(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Ambulance trips table
CREATE TABLE ambulance_trips (
    id INT(11) NOT NULL AUTO_INCREMENT,
    ambulance_id INT(11) NOT NULL,
    patient_id INT(11) NOT NULL,
    pickup_location VARCHAR(255) NOT NULL,
    pickup_latitude DECIMAL(10,8) NOT NULL,
    pickup_longitude DECIMAL(11,8) NOT NULL,
    destination_location VARCHAR(255) NOT NULL,
    destination_latitude DECIMAL(10,8) NOT NULL,
    destination_longitude DECIMAL(11,8) NOT NULL,
    distance_km DECIMAL(10,2) NOT NULL,
    fare DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'assigned', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    requested_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    assigned_at TIMESTAMP NULL,
    started_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (ambulance_id) REFERENCES ambulances(id) ON DELETE CASCADE,
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Ambulance paths table (for custom paths)
CREATE TABLE ambulance_paths (
    id INT(11) NOT NULL AUTO_INCREMENT,
    trip_id INT(11) NOT NULL,
    latitude DECIMAL(10,8) NOT NULL,
    longitude DECIMAL(11,8) NOT NULL,
    timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (trip_id) REFERENCES ambulance_trips(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- TABLES FOR APPOINTMENTS AND CONSULTATIONS
-- =============================================

-- Doctor slots (availability)
CREATE TABLE doctor_slots (
    id INT(11) NOT NULL AUTO_INCREMENT,
    doctor_id INT(11) NOT NULL,
    hospital_id INT(11) NOT NULL,
    day_of_week ENUM('saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    max_patients INT(11) DEFAULT 10,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    FOREIGN KEY (hospital_id) REFERENCES hospitals(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Appointments table
CREATE TABLE appointments (
    id INT(11) NOT NULL AUTO_INCREMENT,
    patient_id INT(11) NOT NULL,
    doctor_id INT(11) NOT NULL,
    hospital_id INT(11) NOT NULL,
    slot_id INT(11) NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    status ENUM('pending', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show') DEFAULT 'pending',
    symptoms TEXT DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    FOREIGN KEY (hospital_id) REFERENCES hospitals(id) ON DELETE CASCADE,
    FOREIGN KEY (slot_id) REFERENCES doctor_slots(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- TABLES FOR PRESCRIPTIONS AND MEDICAL RECORDS
-- =============================================

-- Prescriptions table
CREATE TABLE prescriptions (
    id INT(11) NOT NULL AUTO_INCREMENT,
    appointment_id INT(11) NOT NULL,
    patient_id INT(11) NOT NULL,
    doctor_id INT(11) NOT NULL,
    diagnosis TEXT DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    follow_up_date DATE DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Prescription items (medicines)
CREATE TABLE prescription_items (
    id INT(11) NOT NULL AUTO_INCREMENT,
    prescription_id INT(11) NOT NULL,
    medicine_name VARCHAR(100) NOT NULL,
    dosage VARCHAR(50) DEFAULT NULL,
    frequency VARCHAR(50) DEFAULT NULL,
    duration VARCHAR(50) DEFAULT NULL,
    instructions TEXT DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (prescription_id) REFERENCES prescriptions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Patient vitals (recorded by compounder)
CREATE TABLE patient_vitals (
    id INT(11) NOT NULL AUTO_INCREMENT,
    appointment_id INT(11) NOT NULL,
    patient_id INT(11) NOT NULL,
    recorded_by INT(11) NOT NULL, -- compounder/nurse id
    blood_pressure_systolic INT(11) DEFAULT NULL,
    blood_pressure_diastolic INT(11) DEFAULT NULL,
    spo2 DECIMAL(5,2) DEFAULT NULL,
    pulse INT(11) DEFAULT NULL,
    temperature DECIMAL(5,2) DEFAULT NULL,
    weight DECIMAL(5,2) DEFAULT NULL,
    height DECIMAL(5,2) DEFAULT NULL,
    symptoms TEXT DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- TABLES FOR DAYCARE MANAGEMENT
-- =============================================

-- Daycare procedures
CREATE TABLE daycare_procedures (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    duration_minutes INT(11) DEFAULT 30,
    price DECIMAL(10,2) DEFAULT 0.00,
    hospital_id INT(11) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (hospital_id) REFERENCES hospitals(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Nurse slots (daycare)
CREATE TABLE nurse_slots (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nurse_id INT(11) NOT NULL,
    hospital_id INT(11) NOT NULL,
    date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    purpose ENUM('daycare', 'compounder_support') NOT NULL,
    max_patients INT(11) DEFAULT 5,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (nurse_id) REFERENCES nurses(id) ON DELETE CASCADE,
    FOREIGN KEY (hospital_id) REFERENCES hospitals(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Daycare bookings
CREATE TABLE daycare_bookings (
    id INT(11) NOT NULL AUTO_INCREMENT,
    patient_id INT(11) NOT NULL,
    nurse_id INT(11) NOT NULL,
    hospital_id INT(11) NOT NULL,
    procedure_id INT(11) NOT NULL,
    slot_id INT(11) NOT NULL,
    booking_date DATE NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'completed', 'cancelled') DEFAULT 'pending',
    nid_document VARCHAR(255) DEFAULT NULL,
    other_documents TEXT DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (nurse_id) REFERENCES nurses(id) ON DELETE CASCADE,
    FOREIGN KEY (hospital_id) REFERENCES hospitals(id) ON DELETE CASCADE,
    FOREIGN KEY (procedure_id) REFERENCES daycare_procedures(id) ON DELETE CASCADE,
    FOREIGN KEY (slot_id) REFERENCES nurse_slots(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- TABLES FOR REPORTS AND DOCUMENTS
-- =============================================

-- Patient reports
CREATE TABLE patient_reports (
    id INT(11) NOT NULL AUTO_INCREMENT,
    patient_id INT(11) NOT NULL,
    uploaded_by INT(11) NOT NULL, -- admin id
    report_type VARCHAR(50) DEFAULT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT DEFAULT NULL,
    file_path VARCHAR(255) NOT NULL,
    is_confidential BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- TABLES FOR ICU MANAGEMENT
-- =============================================

-- ICU beds
CREATE TABLE icu_beds (
    id INT(11) NOT NULL AUTO_INCREMENT,
    hospital_id INT(11) NOT NULL,
    bed_number VARCHAR(20) NOT NULL,
    is_occupied BOOLEAN DEFAULT FALSE,
    current_patient_id INT(11) DEFAULT NULL,
    ventilator BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY (hospital_bed_unique) (hospital_id, bed_number),
    FOREIGN KEY (hospital_id) REFERENCES hospitals(id) ON DELETE CASCADE,
    FOREIGN KEY (current_patient_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- TABLES FOR BILLING AND PAYMENTS
-- =============================================

-- Invoices
CREATE TABLE invoices (
    id INT(11) NOT NULL AUTO_INCREMENT,
    patient_id INT(11) NOT NULL,
    invoice_number VARCHAR(50) NOT NULL,
    issue_date DATE NOT NULL,
    due_date DATE NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    tax DECIMAL(10,2) DEFAULT 0.00,
    discount DECIMAL(10,2) DEFAULT 0.00,
    total_amount DECIMAL(10,2) NOT NULL,
    amount_paid DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('draft', 'issued', 'paid', 'overdue', 'cancelled') DEFAULT 'draft',
    payment_method VARCHAR(50) DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY (invoice_number),
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Invoice items
CREATE TABLE invoice_items (
    id INT(11) NOT NULL AUTO_INCREMENT,
    invoice_id INT(11) NOT NULL,
    item_type ENUM('consultation', 'daycare', 'medicine', 'test', 'ambulance', 'other') NOT NULL,
    description VARCHAR(255) NOT NULL,
    quantity INT(11) DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    reference_id INT(11) DEFAULT NULL, -- ID of the related item (appointment, daycare_booking, etc.)
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- TABLES FOR LOCATION AND GEOGRAPHY
-- =============================================

-- Districts of Bangladesh
CREATE TABLE districts (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    division_id INT(11) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Divisions of Bangladesh
CREATE TABLE divisions (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert divisions
INSERT INTO divisions (name) VALUES
('Barishal'),
('Chattogram'),
('Dhaka'),
('Khulna'),
('Mymensingh'),
('Rajshahi'),
('Rangpur'),
('Sylhet');

-- Insert districts
INSERT INTO districts (name, division_id) VALUES
('Barishal', 1),
('Barguna', 1),
('Bhola', 1),
('Jhalokati', 1),
('Patuakhali', 1),
('Pirojpur', 1),
('Bandarban', 2),
('Brahmanbaria', 2),
('Chandpur', 2),
('Chattogram', 2),
('Cumilla', 2),
('Cox''s Bazar', 2),
('Feni', 2),
('Khagrachhari', 2),
('Lakshmipur', 2),
('Noakhali', 2),
('Rangamati', 2),
('Dhaka', 3),
('Faridpur', 3),
('Gazipur', 3),
('Gopalganj', 3),
('Kishoreganj', 3),
('Madaripur', 3),
('Manikganj', 3),
('Munshiganj', 3),
('Mymensingh', 3),
('Narayanganj', 3),
('Narsingdi', 3),
('Rajbari', 3),
('Shariatpur', 3),
('Tangail', 3),
('Bagerhat', 4),
('Chuadanga', 4),
('Jessore', 4),
('Jhenaidah', 4),
('Khulna', 4),
('Kushtia', 4),
('Magura', 4),
('Meherpur', 4),
('Narail', 4),
('Satkhira', 4),
('Jamalpur', 5),
('Mymensingh', 5),
('Netrokona', 5),
('Sherpur', 5),
('Bogura', 6),
('Joypurhat', 6),
('Naogaon', 6),
('Natore', 6),
('Chapainawabganj', 6),
('Pabna', 6),
('Rajshahi', 6),
('Sirajganj', 6),
('Dinajpur', 7),
('Gaibandha', 7),
('Kurigram', 7),
('Lalmonirhat', 7),
('Nilphamari', 7),
('Panchagarh', 7),
('Rangpur', 7),
('Thakurgaon', 7),
('Habiganj', 8),
('Moulvibazar', 8),
('Sunamganj', 8),
('Sylhet', 8);

-- =============================================
-- TABLES FOR SYSTEM CONFIGURATION
-- =============================================

-- System settings
CREATE TABLE system_settings (
    id INT(11) NOT NULL AUTO_INCREMENT,
    setting_key VARCHAR(50) NOT NULL,
    setting_value TEXT DEFAULT NULL,
    description TEXT DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default settings
INSERT INTO system_settings (setting_key, setting_value, description) VALUES
('site_name', 'Niramoy (নিরাময়)', 'Name of the healthcare system'),
('site_description', 'Bangladesh Healthcare Management System', 'Description of the system'),
('admin_email', 'admin@niramoy.com.bd', 'Default admin email'),
('ambulance_base_fare', '100.00', 'Base fare for ambulance service'),
('ambulance_per_km_fare', '30.00', 'Per kilometer fare for ambulance service'),
('max_appointment_per_slot', '10', 'Maximum patients per doctor slot'),
('max_daycare_per_slot', '5', 'Maximum patients per nurse slot'),
('default_currency', 'BDT', 'Default currency for the system'),
('date_format', 'd-m-Y', 'Default date format'),
('time_format', 'h:i A', 'Default time format');

-- =============================================
-- INSERT SAMPLE DATA FOR TESTING
-- =============================================

-- Insert sample hospitals
INSERT INTO hospitals (name, address, district, division, latitude, longitude, phone) VALUES
('Dhaka Medical College Hospital', 'Shahbag, Dhaka', 'Dhaka', 'Dhaka', 23.7329, 90.4084, '+8802-9661051'),
('Bangabandhu Sheikh Mujib Medical University', 'Shahbag, Dhaka', 'Dhaka', 'Dhaka', 23.7344, 90.4041, '+8802-9663000'),
('National Institute of Cardiovascular Diseases', 'Sher-E-Bangla Nagar, Dhaka', 'Dhaka', 'Dhaka', 23.7774, 90.3765, '+8802-9130275'),
('Chittagong Medical College Hospital', 'Chawkbazar, Chittagong', 'Chattogram', 'Chattogram', 22.3515, 91.8312, '+88031-619400'),
('Rajshahi Medical College Hospital', 'Rajshahi', 'Rajshahi', 'Rajshahi', 24.3636, 88.6241, '+880721-772055'),
('Khulna Medical College Hospital', 'Khulna', 'Khulna', 'Khulna', 22.8158, 89.5396, '+88041-760371'),
('Barishal Medical College Hospital', 'Barishal', 'Barishal', 'Barishal', 22.7010, 90.3535, '+880431-27670'),
('Sylhet MAG Osmani Medical College', 'Sylhet', 'Sylhet', 'Sylhet', 24.7537, 91.8719, '+880821-713300'),
('Mymensingh Medical College Hospital', 'Mymensingh', 'Mymensingh', 'Mymensingh', 24.7471, 90.4115, '+88091-65521'),
('Rangpur Medical College Hospital', 'Rangpur', 'Rangpur', 'Rangpur', 25.7469, 89.2507, '+880521-62268');

-- Insert sample users (admin)
INSERT INTO users (name, email, password, phone) VALUES
('System Admin', 'admin@niramoy.com.bd', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+8801711111111');

-- Assign admin role
INSERT INTO user_roles (user_id, role_id) VALUES
(1, 6); -- Admin role

-- Insert sample doctors
INSERT INTO users (name, email, password, phone) VALUES
('Dr. Ahmed Hasan', 'ahmed.hasan@niramoy.com.bd', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+8801711111112'),
('Dr. Fatema Begum', 'fatema.begum@niramoy.com.bd', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+8801711111113'),
('Dr. Mohammad Ali', 'mohammad.ali@niramoy.com.bd', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+8801711111114');

-- Insert doctor details
INSERT INTO doctors (user_id, bmdc_code, specialization, experience_years, consultation_fee, is_verified) VALUES
(2, 'A-12345', 'Cardiologist', 15, 1500.00, TRUE),
(3, 'B-12345', 'Gynecologist', 12, 1200.00, TRUE),
(4, 'C-12345', 'Pediatrician', 10, 1000.00, TRUE);

-- Assign doctor role
INSERT INTO user_roles (user_id, role_id) VALUES
(2, 2), -- Doctor role
(3, 2), -- Doctor role
(4, 2); -- Doctor role

-- Insert sample nurses
INSERT INTO users (name, email, password, phone) VALUES
('Nurse Ayesha Siddiqua', 'ayesha.siddiqua@niramoy.com.bd', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+8801711111115'),
('Nurse Karim Ahmed', 'karim.ahmed@niramoy.com.bd', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+8801711111116');

-- Insert nurse details
INSERT INTO nurses (user_id, is_daycare, is_compounder) VALUES
(5, TRUE, TRUE), -- Both daycare nurse and compounder
(6, TRUE, FALSE); -- Only daycare nurse

-- Assign nurse role
INSERT INTO user_roles (user_id, role_id) VALUES
(5, 3), -- Nurse role
(6, 3); -- Nurse role

-- Insert doctor-compounder relationships
INSERT INTO doctor_compounders (doctor_id, nurse_id) VALUES
(1, 1); -- Dr. Ahmed Hasan assigned to Nurse Ayesha Siddiqua

-- Insert sample patients
INSERT INTO users (name, email, password, phone, date_of_birth, gender, blood_group) VALUES
('Rahim Khan', 'rahim.khan@niramoy.com.bd', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+8801711111117', '1985-05-15', 'male', 'B+'),
('Karima Begum', 'karima.begum@niramoy.com.bd', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+8801711111118', '1990-08-22', 'female', 'O+'),
('Jamal Uddin', 'jamal.uddin@niramoy.com.bd', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+8801711111119', '1978-12-10', 'male', 'A+');

-- Assign patient role
INSERT INTO user_roles (user_id, role_id) VALUES
(7, 1), -- Patient role
(8, 1), -- Patient role
(9, 1); -- Patient role

-- Insert sample ambulances
INSERT INTO ambulances (hospital_id, vehicle_number, driver_name, driver_phone) VALUES
(1, 'DHK-AMB-001', 'Abdul Karim', '+8801711111120'),
(1, 'DHK-AMB-002', 'Mohammad Rafiq', '+8801711111121'),
(4, 'CTG-AMB-001', 'Abul Kashem', '+8801711111122');

-- Insert sample ambulance drivers
INSERT INTO users (name, email, password, phone) VALUES
('Abdul Karim', 'abdul.karim@niramoy.com.bd', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+8801711111120'),
('Mohammad Rafiq', 'mohammad.rafiq@niramoy.com.bd', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+8801711111121'),
('Abul Kashem', 'abul.kashem@niramoy.com.bd', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+8801711111122');

-- Assign ambulance driver role
INSERT INTO user_roles (user_id, role_id) VALUES
(10, 5), -- Ambulance role
(11, 5), -- Ambulance role
(12, 5); -- Ambulance role

-- Insert sample doctor slots
INSERT INTO doctor_slots (doctor_id, hospital_id, day_of_week, start_time, end_time, max_patients) VALUES
(1, 1, 'saturday', '09:00:00', '12:00:00', 10),
(1, 1, 'sunday', '09:00:00', '12:00:00', 10),
(1, 1, 'monday', '14:00:00', '17:00:00', 8),
(2, 1, 'tuesday', '10:00:00', '13:00:00', 10),
(2, 1, 'wednesday', '10:00:00', '13:00:00', 10),
(3, 1, 'thursday', '15:00:00', '18:00:00', 12);

-- Insert sample nurse slots
INSERT INTO nurse_slots (nurse_id, hospital_id, date, start_time, end_time, purpose, max_patients) VALUES
(1, 1, DATE_ADD(CURRENT_DATE, INTERVAL 1 DAY), '09:00:00', '12:00:00', 'daycare', 5),
(1, 1, DATE_ADD(CURRENT_DATE, INTERVAL 1 DAY), '14:00:00', '17:00:00', 'compounder_support', 8),
(2, 1, DATE_ADD(CURRENT_DATE, INTERVAL 2 DAY), '10:00:00', '13:00:00', 'daycare', 5);

-- Insert sample daycare procedures
INSERT INTO daycare_procedures (name, description, duration_minutes, price, hospital_id) VALUES
('Dressing Change', 'Simple wound dressing change', 15, 500.00, 1),
('IV Fluid Administration', 'Intravenous fluid therapy', 30, 800.00, 1),
('Injection Administration', 'Intramuscular or subcutaneous injection', 10, 300.00, 1),
('Vital Signs Monitoring', 'Regular monitoring of vital signs', 20, 400.00, 1),
('Blood Pressure Check', 'Blood pressure measurement and monitoring', 15, 300.00, 1);

-- Insert sample ICU beds
INSERT INTO icu_beds (hospital_id, bed_number, is_occupied, ventilator) VALUES
(1, 'ICU-001', FALSE, TRUE),
(1, 'ICU-002', FALSE, TRUE),
(1, 'ICU-003', FALSE, FALSE),
(1, 'ICU-004', FALSE, FALSE),
(1, 'ICU-005', FALSE, TRUE);

-- Insert sample patient diseases (for epidemic tracking)
INSERT INTO patient_diseases (patient_id, disease_id, diagnosis_date, status) VALUES
(7, 1, CURRENT_DATE, 'active'), -- Rahim Khan has Dengue
(8, 2, DATE_SUB(CURRENT_DATE, INTERVAL 5 DAY), 'recovered'), -- Karima Begum had Chikungunya
(9, 3, DATE_SUB(CURRENT_DATE, INTERVAL 10 DAY), 'active'); -- Jamal Uddin has Malaria