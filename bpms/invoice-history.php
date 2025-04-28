<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if(strlen($_SESSION['bpmsuid'])==0) {
    header('location:logout.php');
    exit();
}
header("Location: client-invoices.php");
exit();
?>
