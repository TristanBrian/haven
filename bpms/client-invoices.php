<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if(strlen($_SESSION['bpmsuid'])==0) {
    header('location:logout.php');
} else {
?>
<!DOCTYPE html>
<html>
<head>
    <title>Haven Spa | My Invoices</title>
    <link rel="stylesheet" href="assets/css/style-starter.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,700&display=swap" rel="stylesheet">
</head>
<body id="home">
    <?php include_once('includes/header.php');?>
    
    <section class="w3l-inner-banner-main">
        <div class="about-inner contact">
            <div class="container">   
                <div class="main-titles-head text-center">
                    <h3 class="header-name">My Invoices</h3>
                    <p class="tiltle-para">View your service invoices and payment history</p>
                </div>
            </div>
        </div>
    </section>

    <section class="w3l-contact-info-main" id="contact">
        <div class="container">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Service</th>
                            <th>Duration</th>
                            <th>Amount</th>
                            <th>Appointment Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $uid = $_SESSION['bpmsuid'];
                        // Get invoices with their services
                        $invoices = mysqli_query($con,"SELECT ic.InvoiceNumber, ic.InvoiceDate, ic.Total, ic.Status,
                                                     GROUP_CONCAT(s.ServiceName SEPARATOR ', ') as Services,
                                                     SUM(s.Duration) as TotalDuration
                                                     FROM tblinvoices_complete ic
                                                     JOIN tblinvoice i ON ic.InvoiceNumber = i.BillingId
                                                     JOIN tblservices s ON i.ServiceId = s.ID
                                                     WHERE ic.UserID = '$uid'
                                                     GROUP BY ic.InvoiceNumber
                                                     ORDER BY ic.InvoiceDate DESC");
                        
                        while($inv = mysqli_fetch_array($invoices)) {
                            $invoiceNumber = $inv['InvoiceNumber'];
                            $total = $inv['Total'];
                            
                            $statusClass = $inv['Status'] == 'Paid' ? 'badge-success' : 'badge-warning';
                            echo "<tr>
                                <td>{$invoiceNumber}</td>
                                <td>{$inv['Services']}</td>
                                <td>{$inv['TotalDuration']} mins</td>
                                <td>KSH ".number_format($total, 2)."</td>
                                <td>{$inv['InvoiceDate']}</td>
                                <td><span class='badge {$statusClass}'>{$inv['Status']}</span></td>
                                <td>
                                    <a href='view-invoice.php?id={$invoiceNumber}' class='btn btn-primary'>View Details</a>
                                    ".($inv['Status'] != 'Paid' ? "<a href='payment.html?v=2?invoice={$invoiceNumber}' class='btn btn-success'>Complete Payment</a>" : "")."
                                </td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    
    <?php include_once('includes/footer.php');?>
</body>
</html>
<?php } ?>