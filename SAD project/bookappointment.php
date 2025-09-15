<?php
session_start();
include 'db.php';

if($_SERVER['REQUEST_METHOD']=='POST'){
    $patient_id = $_SESSION['user_id'];
    $doctor_id  = $_POST['doctor_id'];
    $date       = $_POST['date'];

    $stmt = $conn->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date, status) VALUES (?,?,?, 'pending')");
    $stmt->bind_param("iis", $patient_id, $doctor_id, $date);
    $stmt->execute();

    echo "Appointment booked successfully!";
}
?>
