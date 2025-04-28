<?php
session_start();
include('includes/dbconnection.php');

if(strlen($_SESSION['bpmsaid'])==0) {
    header('location:logout.php');
    exit();
}

if(isset($_GET['id'])) {
    $invoiceId = $_GET['id'];
    
    // Update invoice status
    $query = mysqli_query($con,"UPDATE tblinvoices_complete SET Status='Paid' WHERE InvoiceNumber='$invoiceId'");
    
    if($query) {
        echo "<script>alert('Invoice approved successfully');</script>";
    } else {
        echo "<script>alert('Something went wrong. Please try again.');</script>";
    }
    echo "<script>window.location.href='invoices.php'</script>";
    exit();
}

header('location:invoices.php');
?>
