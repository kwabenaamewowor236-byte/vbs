<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

$aid = $_SESSION['u_id']; // logged in user id

// Get booking_id from query string
if (!isset($_GET['booking_id'])) {
    die("Booking ID missing.");
}
$booking_id = intval($_GET['booking_id']);

// Fetch booking details
$query = "SELECT b.booking_id, b.amount, b.status, v.v_name, v.v_reg_no, v.v_driver, u.u_email 
          FROM tms_bookings b
          JOIN tms_vehicle v ON b.v_id = v.v_id
          JOIN tms_user u ON b.u_id = u.u_id
          WHERE b.booking_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows == 0) {
    die("Booking not found.");
}
$booking = $res->fetch_assoc();

// Assign variables
$vehicle_name = $booking['v_name'] . " (" . $booking['v_reg_no'] . ")";
$driver = $booking['v_driver'];
$amount = $booking['amount'];
$status = $booking['status'];
$user_email = $booking['u_email'];
?>

<!DOCTYPE html>
<html lang="en">
<?php include('vendor/inc/head.php'); ?>
<body id="page-top">
<?php include('vendor/inc/nav.php'); ?>
<div id="wrapper">
  <?php include('vendor/inc/sidebar.php'); ?>
  <div id="content-wrapper">
    <div class="container-fluid">

      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="user-dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item">Bookings</li>
        <li class="breadcrumb-item active">Confirm Booking</li>
      </ol>

      <div class="card mb-3">
        <div class="card-header"><i class="fas fa-clipboard"></i> Booking Confirmation</div>
        <div class="card-body">
          <h4>Booking Details</h4>
          <p><b>Booking ID:</b> <?php echo $booking_id; ?></p>
          <p><b>Vehicle:</b> <?php echo $vehicle_name; ?></p>
          <p><b>Driver:</b> <?php echo $driver; ?></p>
          <p><b>Amount:</b> ₦<?php echo number_format($amount, 2); ?></p>
          <p><b>Status:</b> <?php echo $status; ?></p>
          <hr>
          <h5>Proceed to Payment</h5>
          <button type="button" id="payBtn" class="btn btn-success">Pay with Paystack</button>
        </div>
      </div>

    </div>
    <?php include("vendor/inc/footer.php"); ?>
  </div>
</div>

<a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>
<?php include("vendor/inc/logout-modal.php"); ?>

<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="vendor/chart.js/Chart.min.js"></script>
<script src="vendor/datatables/jquery.dataTables.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.js"></script>
<script src="vendor/js/sb-admin.min.js"></script>

<!-- ✅ Paystack Inline Script -->
<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
document.getElementById('payBtn').addEventListener("click", function(e){
    e.preventDefault();

    var handler = PaystackPop.setup({
        key: 'pk_test_f70535da983234617a35a994659666eef46134c0', // ✅ Replace with your PUBLIC key (use pk_live... for live)
        email: "<?php echo $user_email; ?>", // customer email
        amount: <?php echo $amount * 100; ?>, // amount in pesewas (e.g. GHS 50 = 5000)
        currency: "GHS", // ✅ correct Ghana Cedi code
        ref: 'BOOK-<?php echo $booking_id; ?>-' + Math.floor((Math.random() * 1000000000) + 1), // unique reference
        callback: function(response){
            // redirect to verification page
            window.location.href = "verify_payment.php?reference=" + response.reference + "&booking_id=<?php echo $booking_id; ?>";
        },
        onClose: function(){
            alert('Payment window closed.');
        }
    });
    handler.openIframe();
});
</script>

</body>
</html>
