<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
    if (strlen($_SESSION['bpmsaid']==0)) {
  header('location:logout.php');
  } else{
if(isset($_POST['submit']))
{
    // Validate and sanitize inputs
    $cid = intval($_GET['viewid']);
    $remark = mysqli_real_escape_string($con, $_POST['remark']);
    $status = mysqli_real_escape_string($con, $_POST['status']);
    $currentDate = date('Y-m-d H:i:s');
    
    // Start transaction
    mysqli_begin_transaction($con);
    
    try {
        // Update appointment status
        $query = mysqli_query($con, "UPDATE tblbook SET Remark='$remark', Status='$status', RemarkDate='$currentDate' WHERE ID='$cid'");
        
        if (!$query) {
            throw new Exception("Failed to update appointment status");
        }
        
        if ($status == 'Selected') {
            // Get appointment details including services
            $apt = mysqli_fetch_array(mysqli_query($con,"SELECT UserID, Services FROM tblbook WHERE ID='$cid'"));
            if (!$apt) {
                throw new Exception("Failed to fetch appointment details");
            }
            
            $userid = intval($apt['UserID']);
            $services = array_filter(explode(',', $apt['Services']));
            
            if (!empty($services)) {
                // Generate billing ID
                $billingid = mt_rand(100000000, 999999999);
                
                // Create invoice records for each service
                foreach($services as $serviceid) {
                    $serviceid = intval($serviceid);
                    $service = mysqli_fetch_array(mysqli_query($con,"SELECT Duration, BookingFee FROM tblservices WHERE ID='$serviceid'"));
                    
                    if (!$service) {
                        throw new Exception("Invalid service ID: $serviceid");
                    }
                    
                    $duration = mysqli_real_escape_string($con, $service['Duration']);
                    $bookingfee = floatval($service['BookingFee']);
                    
                    $invoiceQuery = mysqli_query($con,"INSERT INTO tblinvoice(Userid,ServiceId,BillingId,Duration,BookingFee) VALUES('$userid','$serviceid','$billingid','$duration','$bookingfee')");
                    
                    if (!$invoiceQuery) {
                        throw new Exception("Failed to create invoice for service $serviceid");
                    }
                }
                
                // Create complete invoice record
                $completeInvoiceQuery = mysqli_query($con,"INSERT INTO tblinvoices_complete(InvoiceNumber,UserID,InvoiceDate,Status) VALUES('$billingid','$userid','$currentDate','Pending')");
                
                if (!$completeInvoiceQuery) {
                    throw new Exception("Failed to create complete invoice record");
                }
            }
            
            // Commit transaction if all queries succeeded
            mysqli_commit($con);
            $_SESSION['success'] = "Appointment approved and invoice generated. Invoice #$billingid is now pending payment.";
            header('Location: all-appointment.php');
            exit();
        } else {
            mysqli_commit($con);
            $_SESSION['success'] = "Appointment status updated successfully.";
            header('Location: all-appointment.php');
            exit();
        }
    } catch (Exception $e) {
        mysqli_rollback($con);
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: view-appointment.php?viewid=$cid");
        exit();
    }
}
  ?>
<!DOCTYPE HTML>
<html>
<head>
<title>Haven Spa|| View Appointment</title>

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
					<h3 class="title1">View Appointment</h3>
					<div class="table-responsive bs-example widget-shadow">
						
						<h4>View Appointment:</h4>
						<?php
$cid=$_GET['viewid'];
$ret=mysqli_query($con,"select tbluser.FirstName,tbluser.LastName,tbluser.Email,tbluser.MobileNumber,tblbook.ID as bid,tblbook.AptNumber,tblbook.AptDate,tblbook.AptTime,tblbook.Message,tblbook.BookingDate,tblbook.Remark,tblbook.Status,tblbook.RemarkDate from tblbook join tbluser on tbluser.ID=tblbook.UserID where tblbook.ID='$cid'");
$cnt=1;
while ($row=mysqli_fetch_array($ret)) {

?>
						<table class="table table-bordered">
							<tr>
    <th>Appointment Number</th>
    <td><?php  echo $row['AptNumber'];?></td>
  </tr>
  <tr>
<th>Name</th>
    <td><?php  echo $row['FirstName'];?> <?php  echo $row['LastName'];?></td>
  </tr>

<tr>
    <th>Email</th>
    <td><?php  echo $row['Email'];?></td>
  </tr>
   <tr>
    <th>Mobile Number</th>
    <td><?php  echo $row['MobileNumber'];?></td>
  </tr>
   <tr>
    <th>Appointment Date</th>
    <td><?php  echo $row['AptDate'];?></td>
  </tr>
 
<tr>
    <th>Appointment Time</th>
    <td><?php  echo $row['AptTime'];?></td>
  </tr>
  
  
  <tr>
    <th>Apply Date</th>
    <td><?php  echo $row['BookingDate'];?></td>
  </tr>
  

<tr>
    <th>Status</th>
    <td> <?php  
if($row['Status']=="")
{
  echo "Not Updated Yet";
}

if($row['Status']=="Selected")
{
  echo "Selected";
}

if($row['Status']=="Rejected")
{
  echo "Rejected";
}

     ;?></td>
  </tr>
						</table>
						<table class="table table-bordered">
							<?php if($row['Status']==""){ ?>


<form name="submit" method="post" enctype="multipart/form-data"> 

<tr>
    <th>Remark :</th>
    <td>
    <textarea name="remark" placeholder="" rows="6" cols="14" class="form-control wd-450" required="true"></textarea></td>
   </tr>

  <tr>
    <th>Status :</th>
    <td>
   <select name="status" class="form-control wd-450" required="true" >
   	<option value="">Select</option>
     <option value="Selected">Approved</option>
     <option value="Rejected">Rejected</option>
   </select></td>
  </tr>

  <tr align="center">
    <td colspan="2"><button type="submit" name="submit" class="btn btn-primary">Submit</button></td>
  </tr>
  </form>
<?php } else { ?>
						</table>
						<table class="table table-bordered">
							<tr>
    <th>Remark</th>
    <td><?php echo $row['Remark']; ?></td>
  </tr>
<tr>
    <th>Status</th>
    <td><?php echo $row['Status']; ?></td>
  </tr>

<tr>
<th>Remark date</th>
<td><?php echo $row['RemarkDate']; ?>  </td></tr>

						</table>
						<?php } ?>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
		<!--footer-->
		
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
<?php }  ?>