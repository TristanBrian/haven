<?php
session_start();
require_once('includes/dbconnection.php');

if (!isset($_SESSION['bpmsuid']) || !isset($_SESSION['aptno'])) {
    header('location:logout.php');
    exit();
}

// Get appointment details
$aptQuery = mysqli_query($con, "SELECT * FROM tblbook WHERE AptNumber='".$_SESSION['aptno']."'");
$appointment = mysqli_fetch_array($aptQuery);

if (!$appointment) {
    die("Invalid appointment");
}

// Calculate total cost
$services = explode(",", $appointment['Services']);
$totalCost = 0;
foreach($services as $serviceId) {
    $costQuery = mysqli_query($con, "SELECT Cost FROM tblservices WHERE ID='$serviceId'");
    $serviceCost = mysqli_fetch_array($costQuery);
    $totalCost += $serviceCost['Cost'];
}
$_SESSION['totalCost'] = $totalCost;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment | Haven Spa</title>
    <link rel="stylesheet" href="assets/css/style-starter.css">
    <style>
        .payment-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .loading {
            display: none;
            text-align: center;
        }
        #feedback {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<?php include_once('includes/header.php'); ?>

<div class="payment-container">
    <h2>Complete Your Payment</h2>
    <p>Appointment #: <?php echo $_SESSION['aptno']; ?></p>
    <p>Total Amount: KSH <?php echo number_format($totalCost, 2); ?></p>
    
    <div class="form-group">
        <label for="phone">M-Pesa Phone Number</label>
        <input type="text" id="phone" class="form-control" placeholder="e.g. 254712345678" required>
    </div>
    
    <button id="payBtn" class="btn btn-primary">Pay via M-Pesa</button>
    
    <div class="loading" id="loading">
        <p>Processing payment request...</p>
    </div>
    
    <div id="feedback"></div>
</div>

<script src="assets/js/jquery-3.3.1.min.js"></script>
<script>
$(document).ready(function() {
    $('#payBtn').click(function() {
        const phone = $('#phone').val();
        const amount = <?php echo $totalCost; ?>;
        
        if (!phone.match(/^254[0-9]{9}$/)) {
            alert('Please enter a valid M-Pesa phone number starting with 254');
            return;
        }
        
        $('#loading').show();
        $('#feedback').html('');
        
        // Call STK Push API
        fetch('assets/js/stk_push.js', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                phone: phone,
                amount: amount
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) throw new Error(data.error);
            
            // Record payment in database
            return fetch('record_payment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    transactionId: data.transactionId,
                    amount: amount,
                    phone: phone
                })
            });
        })
        .then(response => response.json())
        .then(result => {
            if (!result.success) throw new Error(result.message);
            
            $('#loading').hide();
            $('#feedback').html('<div class="alert alert-success">Payment successful! Redirecting...</div>');
            setTimeout(() => {
                window.location.href = 'thank-you.php?payment=success';
            }, 2000);
        })
        .catch(error => {
            $('#loading').hide();
            $('#feedback').html('<div class="alert alert-danger">Error: ' + error.message + '</div>');
            console.error('Payment error:', error);
        });
    });
});
</script>

<?php include_once('includes/footer.php'); ?>
</body>
</html>
