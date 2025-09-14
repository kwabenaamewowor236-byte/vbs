<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

$aid = $_SESSION['u_id']; // logged in user id

// Handle booking request
if (isset($_GET['v_id'])) {
    $v_id = intval($_GET['v_id']);

    // Get vehicle price (for demo, assume 5000 — ideally fetch from DB if you add v_price column)
    $amount = 5000;

    // ✅ Insert booking (let MySQL handle auto-increment booking_id)
    $stmt = $mysqli->prepare("
        INSERT INTO tms_bookings (u_id, v_id, booking_date, status, amount) 
        VALUES (?, ?, NOW(), 'Pending', ?)
    ");
    $stmt->bind_param("iid", $aid, $v_id, $amount);

    if ($stmt->execute()) {
        // ✅ Get the auto-generated booking_id
        $booking_id = $stmt->insert_id;

        // Redirect to confirmation page with booking_id
        header("Location: user-confirm-booking.php?booking_id=$booking_id");
        exit();
    } else {
        echo "Error creating booking: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<?php include ('vendor/inc/head.php');?>

<body id="page-top">
<?php include ('vendor/inc/nav.php');?>

<div id="wrapper">
  <?php include('vendor/inc/sidebar.php');?>

  <div id="content-wrapper">
    <div class="container-fluid">

      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="user-dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item">Vehicle</li>
        <li class="breadcrumb-item active">Book Vehicle</li>
      </ol>

      <!-- Available Vehicles -->
      <div class="card mb-3">
        <div class="card-header"><i class="fas fa-bus"></i> Available Vehicles</div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Vehicle Name</th>
                  <th>Reg No.</th>
                  <th>Seats</th>
                  <th>Driver</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
              <?php
                $ret = "SELECT * FROM tms_vehicle WHERE v_status ='Available'";
                $stmt = $mysqli->prepare($ret);
                $stmt->execute();
                $res = $stmt->get_result();
                $cnt = 1;
                while($row = $res->fetch_object()) {
              ?>
                <tr>
                  <td><?php echo $cnt; ?></td>
                  <td><?php echo $row->v_name; ?></td>
                  <td><?php echo $row->v_reg_no; ?></td>
                  <td><?php echo $row->v_pass_no; ?> Passengers</td>
                  <td><?php echo $row->v_driver; ?></td>
                  <td>
                    <a href="usr-book-vehicle.php?v_id=<?php echo $row->v_id; ?>" class="btn btn-outline-success">
                      <i class="fa fa-clipboard"></i> Book Vehicle
                    </a>
                  </td>
                </tr>
              <?php $cnt = $cnt + 1; } ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="card-footer small text-muted">
          <?php
            date_default_timezone_set("Africa/Nairobi");
            echo "Generated At : " . date("h:i:sa");
          ?> 
        </div>
      </div>
    </div>
    <?php include("vendor/inc/footer.php");?>
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
<script src="vendor/js/demo/datatables-demo.js"></script>
<script src="vendor/js/demo/chart-area-demo.js"></script>

</body>
</html>
