<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid = $_SESSION['a_id'];

if (isset($_GET['u_id'])) {
    $u_id = $_GET['u_id'];

    // Update to Paid
    $query = "UPDATE tms_user SET u_car_payment_status='Paid' WHERE u_id=?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $u_id);
    if ($stmt->execute()) {
        $succ = "Booking marked as Paid (Offline)";
    } else {
        $err = "Error updating payment";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include('vendor/inc/head.php');?>
<body id="page-top">
<?php include("vendor/inc/nav.php");?>
<div id="wrapper">
    <?php include("vendor/inc/sidebar.php");?>
    <div id="content-wrapper">
        <div class="container-fluid">
            <?php if(isset($succ)) { ?>
                <script>
                    setTimeout(function(){ swal("Success!","<?php echo $succ;?>","success"); }, 100);
                </script>
            <?php } ?>
            <?php if(isset($err)) { ?>
                <script>
                    setTimeout(function(){ swal("Error!","<?php echo $err;?>","error"); }, 100);
                </script>
            <?php } ?>

            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="#">Bookings</a></li>
              <li class="breadcrumb-item active">Mark Paid</li>
            </ol>
            <hr>
            <p>Booking payment updated.</p>
            <a href="admin-view-bookings.php" class="btn btn-primary">Back to Bookings</a>
        </div>
    </div>
</div>
</body>
</html>
