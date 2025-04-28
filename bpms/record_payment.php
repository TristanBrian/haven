<?php
session_start();
header('Content-Type: application/json');
require_once('includes/dbconnection.php');

if (!isset($_SESSION['aptno']) || !isset($_SESSION['bpmsuid'])) {
    die(json_encode(['success' => false, 'message' => 'Session expired']));
}

$input = json_decode(file_get_contents('php://input'), true);

try {
    // Get appointment ID
    $aptQuery = mysqli_query($con, "SELECT ID FROM tblbook WHERE AptNumber='".$_SESSION['aptno']."'");
    $appointment = mysqli_fetch_array($aptQuery);
    
    if (!$appointment) {
        throw new Exception("Appointment not found");
    }

    // Insert payment record
    $query = mysqli_query($con, "INSERT INTO tblpayments (
        AppointmentID,
        Amount,
        PaymentMethod,
        TransactionID,
        PhoneNumber,
        PaymentStatus
    ) VALUES (
        '".$appointment['ID']."',
        '".$input['amount']."',
        'M-Pesa',
        '".$input['transactionId']."',
        '".$input['phone']."',
        'completed'
    )");

    if (!$query) {
        throw new Exception(mysqli_error($con));
    }

    echo json_encode(['success' => true]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
