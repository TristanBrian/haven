<?php  
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('includes/dbconnection.php');

if (strlen($_SESSION['bpmsuid']==0)) {
  header('location:logout.php');
} else {
?>
<!doctype html>
<html lang="en">
  <head>
    <title>Haven Spa | Invoice Details</title>
    <!-- Template CSS -->
    <link rel="stylesheet" href="assets/css/style-starter.css">
    <link href="https://fonts.googleapis.com/css?family=Josefin+Slab:400,700,700i&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
  </head>
  <body id="home">
    <?php include_once('includes/header.php');?>

    <script src="assets/js/jquery-3.3.1.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script>
    $(function () {
      $('.navbar-toggler').click(function () {
        $('body').toggleClass('noscroll');
      })
    });
    </script>

    <section class="w3l-inner-banner-main">
      <div class="about-inner contact">
        <div class="container">   
          <div class="main-titles-head text-center">
            <h3 class="header-name">Invoice History</h3>
            <p class="tiltle-para">View and manage your invoices</p>
          </div>
        </div>
      </div>
      <div class="breadcrumbs-sub">
        <div class="container">   
          <ul class="breadcrumbs-custom-path">
            <li class="right-side propClone"><a href="index.php">Home <span class="fa fa-angle-right" aria-hidden="true"></span></a></li>
            <li class="active">Invoice Details</li>
          </ul>
        </div>
      </div>
    </section>

    <section class="w3l-contact-info-main" id="contact">
      <div class="contact-sec">
        <div class="container">
          <div>
            <div class="cont-details">
              <div class="table-content table-responsive cart-table-content m-t-30">
                <h3 class="title1">Invoice Details</h3>
                
                <?php
                if(!isset($_GET['id']) || empty($_GET['id'])) {
                  echo '<div class="alert alert-danger">Error: No invoice specified</div>';
                  exit();
                }

                $invid = intval($_GET['id']);
                $uid = $_SESSION['bpmsuid'];
                
                // Verify invoice belongs to logged in user
                $check = mysqli_query($con,"SELECT UserID FROM tblinvoices_complete WHERE InvoiceNumber='$invid' AND UserID='$uid' LIMIT 1");
                if(mysqli_num_rows($check) == 0) {
                  echo '<div class="alert alert-danger">Error: Invoice not found or access denied</div>';
                  exit();
                }

                // Get invoice header info with user data
                $ret = mysqli_query($con,"SELECT 
                    tblinvoices_complete.*,
                    tbluser.FirstName,
                    tbluser.LastName,
                    tbluser.Email,
                    tbluser.MobileNumber,
                    tbluser.RegDate
                  FROM tblinvoices_complete 
                  JOIN tbluser ON tbluser.ID=tblinvoices_complete.UserID
                  WHERE tblinvoices_complete.InvoiceNumber='$invid'");
                
                if(mysqli_num_rows($ret) == 0) {
                  echo '<div class="alert alert-warning">No invoice details found</div>';
                  exit();
                }

                $row = mysqli_fetch_array($ret);
                ?>              
                
                <div class="table-responsive bs-example widget-shadow">
                  <h4>Invoice #<?php echo $invid;?></h4>
                  <div class="invoice-header">
                    <div class="row">
                      <div class="col-md-6">
                        <h5>Haven Spa</h5>
                        <p>123 Spa Street, Nairobi</p>
                        <p>Phone: +254 712 345 678</p>
                      </div>
                      <div class="col-md-6 text-right">
                        <h5>Invoice Date: <?php echo date('d M Y', strtotime($row['InvoiceDate']));?></h5>
                        <p>Status: 
                          <span class="badge <?php echo $row['Status'] == 'Paid' ? 'badge-success' : 'badge-warning'; ?>">
                            <?php echo $row['Status']; ?>
                          </span>
                        </p>
                      </div>
                    </div>
                  </div>

                  <?php
                  // Get service details for this invoice
                  $services = mysqli_query($con,"SELECT 
                      tblservices.ServiceName,
                      tblservices.Cost,
                      tblservices.BookingFee,
                      tblservices.Duration
                    FROM tblservices
                    JOIN tblinvoice ON tblinvoice.ServiceId = tblservices.ID
                    WHERE tblinvoice.BillingId='$invid'");
                  
                  $serviceRows = '';
                  $totalDuration = 0;
                  $totalBookingFee = 0;
                  
                  while($service = mysqli_fetch_array($services)) {
                    $serviceRows .= '<tr>
                      <td>'.$service['ServiceName'].'</td>
                      <td>'.$service['Duration'].' mins</td>
                      <td>KSh '.number_format($service['Cost'],2).'</td>
                      <td>KSh '.number_format($service['BookingFee'],2).'</td>
                    </tr>';
                    $totalDuration += $service['Duration'];
                    $totalBookingFee += $service['BookingFee'];
                  }

                  // Display service summary
                    echo '<div class="booking-summary mb-4">
                    <h5>Service Summary</h5>
                    <div class="row">
                      <div class="col-md-6">
                        <p><strong>Total Service Duration:</strong> '.$totalDuration.' mins</p>
                        <p><strong>Estimated Completion Time:</strong> '.($totalDuration + 30).' mins (including setup)</p>
                      </div>
                      <div class="col-md-6">
                        <p><strong>Tax Rate:</strong> 16%</p>
                        <p><strong>Total Services:</strong> KSh '.number_format($row['Total'] - (isset($row['TaxAmount']) ? $row['TaxAmount'] : 0), 2).'</p>
                        <p><strong>Tax Amount:</strong> KSh '.number_format(isset($row['TaxAmount']) ? $row['TaxAmount'] : 0, 2).'</p>
                        <p><strong>Total Payable:</strong> KSh '.number_format($row['Total'], 2).'</p>
                      </div>
                    </div>
                  </div>';

                  if(mysqli_num_rows($services) > 0) {
                    echo '<div class="table-responsive mt-4">
                      <h5>Service Details</h5>
                      <table class="table table-bordered">
                        <thead>
                          <tr>
                            <th>Service</th>
                            <th>Duration</th>
                            <th>Price</th>
                            <th>Booking Fee</th>
                          </tr>
                        </thead>
                        <tbody>';
                    
                    echo $serviceRows;
                    
                    echo '</tbody>
                      </table>
                    </div>';
                  }
                  ?>

                  <div class="text-center mb-3">
                    <button class="btn btn-primary" onclick="generatePDF()">
                      <i class="fa fa-download"></i> Download PDF Summary
                    </button>
                    <p class="small text-muted mt-2">Includes service details, duration and fees</p>
                  </div>

                  <div class="invoice-footer text-center mt-4">
                    <p>Thank you for choosing Haven Spa!</p>
                    <div class="btn-group">
                      <button class="btn btn-outline-secondary" onclick="window.print()">
                        <i class="fa fa-print"></i> Print Invoice
                      </button>
                      <?php if($row['Status'] == 'Pending'): ?>
                      <button class="btn btn-success ml-2" data-toggle="modal" data-target="#transactionModal">
                        <i class="fa fa-credit-card"></i> Complete Payment
                      </button>
                      <?php endif; ?>
                    </div>
                    <p class="mt-3 text-muted">Invoice is due upon receipt</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <?php include_once('includes/footer.php');?>
    
    <!-- PDF Generation Script -->
    <script src="assets/js/html2pdf.bundle.min.js"></script>
    <script>
      const element = document.querySelector('.table-responsive.bs-example.widget-shadow');
      const opt = {
        margin: 10,
        filename: 'invoice_<?php echo $invid; ?>.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
      };

      // Generate PDF
      html2pdf().set(opt).from(element).save();
    }
    </script>

    <!-- Transaction Verification Modal -->
    <div class="modal fade" id="transactionModal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Payment Verification</h5>
            <button type="button" class="close" data-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <form method="post" action="">
            <div class="modal-body">
              <input type="hidden" name="invoice_id" value="<?php echo $invid; ?>">
              <div class="form-group">
                <label>Transaction Code</label>
                <input type="text" class="form-control" name="transaction_code" required 
                       pattern="[A-Z0-9]{10,20}" title="10-20 alphanumeric characters">
                <small class="form-text text-muted">Enter your M-Pesa transaction code</small>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
              <button type="submit" name="verify_payment" class="btn btn-primary">Verify Payment</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Payment Processing Script -->
    <?php
    if(isset($_POST['verify_payment'])) {
      $transactionCode = mysqli_real_escape_string($con, $_POST['transaction_code']);
      $invoiceId = intval($_POST['invoice_id']);
      
      // Validate transaction code format
      if(!preg_match('/^[A-Z0-9]{10,20}$/', $transactionCode)) {
        echo '<script>alert("Invalid transaction code format");</script>';
      } else {
        // Update invoice with transaction code
        $update = mysqli_query($con,"UPDATE tblinvoices_complete SET 
          Status='Paid', 
          TransactionCode='$transactionCode', 
          PaymentDate=NOW() 
          WHERE InvoiceNumber='$invoiceId'");
        
        if($update) {
          echo '<script>
            alert("Payment verified successfully!");
            window.location.href = "view-invoice.php?id='.$invoiceId.'";
          </script>';
        } else {
          echo '<script>alert("Error updating payment status");</script>';
        }
      }
    }
    ?>
    
    <!-- move top button and scripts remain unchanged -->
    
  </body>
</html>
<?php } ?>
