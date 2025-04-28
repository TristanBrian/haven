<?php
// Database configuration with provided credentials
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '@Bray124'; // Using provided root password
$db_name = 'bpmsdb';

$con = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$con) {
    $error = mysqli_connect_error();
    if (strpos($error, "Unknown database") !== false) {
        die("Database 'bpmsdb' doesn't exist. Please create it first.");
    } else {
        die("Database connection failed. Error: " . $error);
    }
}

// Verify database tables exist
$tables = mysqli_query($con, "SHOW TABLES");
if (mysqli_num_rows($tables) == 0) {
    die("Database is empty. Please import the SQL schema.");
}
?>
