<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

// Check session
if (strlen($_SESSION['bpmsaid']) == 0) {
    header('location:logout.php');
    exit();
}

// Process form submission
if (isset($_POST['submit'])) {
    // Get next invoice number
    $next_inv = mysqli_query($con, "SELECT MAX(InvoiceNumber)+1 AS next_num FROM tblinvoices_complete");
    $inv_row = mysqli_fetch_array($next_inv);
    $invoiceid = $inv_row['next_num'] ?: 1000; // Start from 1000 if no invoices exist

    $uid = intval($_GET['addid']);
    $sid = $_POST['sids'];
    $gtotal = 0;

    // Calculate totals
    foreach ($sid as $svid) {
        $service = mysqli_query($con, "SELECT Cost, BookingFee FROM tblservices WHERE ID='$svid'");
        $srow = mysqli_fetch_array($service);
        $gtotal += ($srow['Cost'] + $srow['BookingFee']);
    }

    // Create invoice record
    $tax = $gtotal * 0.16; // 16% VAT
    $total = $gtotal + $tax;
    mysqli_query($con, "INSERT INTO tblinvoices_complete 
        (InvoiceNumber, UserID, InvoiceDate, Subtotal, Tax, Total, Status) 
        VALUES ('$invoiceid', '$uid', NOW(), '$gtotal', '$tax', '$total', 'Pending')");

    // Link services to invoice
    foreach ($sid as $svid) {
        mysqli_query($con, "INSERT INTO tblinvoice(Userid,ServiceId,BillingId) VALUES('$uid','$svid','$invoiceid')");
    }

    // Success message with invoice details
    echo '<script>alert("Invoice #' . $invoiceid . ' created successfully.\\nSubtotal: ' . number_format($gtotal, 2) . '\\nTax: ' . number_format($tax, 2) . '\\nTotal: ' . number_format($total, 2) . '")</script>';
    echo "<script>window.location.href ='invoices.php'</script>";
}
 


  ?>
<!DOCTYPE HTML>
<html>
<head>
<title>Haven spa || Assign Services</title>

<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<!-- Bootstrap Core CSS -->
<link href="css/bootstrap.css" rel='stylesheet' type='text/css' />
<!-- Custom CSS -->
<link href="css/style.css" rel='stylesheet' type='text/css' />
<!-- font CSS -->
<!-- font-awesome icons -->
<link href="css/font-awesome.css" rel="stylesheet"> 
<!-- //font-awesome icons -->
 <!-- js-->
<script src="js/jquery-1.11.1.min.js"></script>
<script src="js/modernizr.custom.js"></script>
<!--webfonts-->
<link href='//fonts.googleapis.com/css?family=Roboto+Condensed:400,300,300italic,400italic,700,700italic' rel='stylesheet' type='text/css'>
<!--//webfonts--> 
<!--animate-->
<link href="css/animate.css" rel="stylesheet" type="text/css" media="all">
<script src="js/wow.min.js"></script>
	<script>
		 new WOW().init();
	</script>
<!--//end-animate-->
<!-- Metis Menu -->
<script src="js/metisMenu.min.js"></script>
<script src="js/custom.js"></script>
<link href="css/custom.css" rel="stylesheet">
<!--//Metis Menu -->
</head> 
<body class="cbp-spmenu-push">
	<div class="main-content">
		<!--left-fixed -navigation-->
		 <?php include_once('includes/sidebar.php');?>
		<!--left-fixed -navigation-->
		<!-- header-starts -->
		 <?php include_once('includes/header.php');?>
		<!-- //header-ends -->
		<!-- main content start-->
		<div id="page-wrapper">
			<div class="main-page">
				<div class="tables">
					<h3 class="title1">Assign Services</h3>
					
					
				
					<div class="table-responsive bs-example widget-shadow">
						<h4>Assign Services:</h4>
<form method="post">
						<table class="table table-bordered"> <thead> <tr> <th>#</th> <th>Service Name</th> <th>Service Price</th> <th>Action</th> </tr> </thead> <tbody>
<?php
$ret=mysqli_query($con,"select *from  tblservices");
$cnt=1;
while ($row=mysqli_fetch_array($ret)) {

?>

 <tr> 
<th scope="row"><?php echo $cnt;?></th> 
<td><?php  echo $row['ServiceName'];?></td> 
<td><?php  echo $row['Cost'];?></td> 
<td><input type="checkbox" name="sids[]" value="<?php  echo $row['ID'];?>" ></td> 
</tr>   
<?php 
$cnt=$cnt+1;
}?>
<tr>
<td colspan="4" align="center">
<button type="submit" name="submit" class="btn btn-primary">Submit</button>		
</td>

</tr>

</tbody> </table> 
</form>
					</div>
				</div>
			</div>
		</div>
		<!--footer-->
		 <?php include_once('includes/footer.php');?>
        <!--//footer-->
	</div>
	<!-- Classie -->
		<script src="js/classie.js"></script>
		<script>
			var menuLeft = document.getElementById( 'cbp-spmenu-s1' ),
				showLeftPush = document.getElementById( 'showLeftPush' ),
				body = document.body;
				
			showLeftPush.onclick = function() {
				classie.toggle( this, 'active' );
				classie.toggle( body, 'cbp-spmenu-push-toright' );
				classie.toggle( menuLeft, 'cbp-spmenu-open' );
				disableOther( 'showLeftPush' );
			};
			
			function disableOther( button ) {
				if( button !== 'showLeftPush' ) {
					classie.toggle( showLeftPush, 'disabled' );
				}
			}
		</script>
	<!--scrolling js-->
	<script src="js/jquery.nicescroll.js"></script>
	<script src="js/scripts.js"></script>
	<!--//scrolling js-->
	<!-- Bootstrap Core JavaScript -->
	<script src="js/bootstrap.js"> </script>
</body>
</html>
<?php ?>
