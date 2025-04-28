<?php
include('includes/dbconnection.php');

// Add Duration and BookingFee columns to tblservices
$sql = "ALTER TABLE tblservices 
        ADD COLUMN Duration VARCHAR(50) NULL AFTER Cost,
        ADD COLUMN BookingFee INT(10) NULL AFTER Duration";

if(mysqli_query($con, $sql)) {
    echo "Database updated successfully - added Duration and BookingFee fields";
    
    // Set default values for existing services
    $updateSql = "UPDATE tblservices SET 
                 Duration = '60 minutes',
                 BookingFee = 100";
    mysqli_query($con, $updateSql);
} else {
    echo "Error updating database: " . mysqli_error($con);
}

mysqli_close($con);
?>
