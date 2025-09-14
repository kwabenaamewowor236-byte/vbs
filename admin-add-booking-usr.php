<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();
$aid=$_SESSION['a_id'];

/* ==============================
   ADD BOOKING PROCESS
============================== */
if(isset($_POST['book_vehicle']))
{
    $u_id = intval($_POST['u_id']); // ✅ safer than $_GET
    $u_car_type = $_POST['u_car_type'];
    $u_car_regno  = $_POST['u_car_regno'];
    $u_car_bookdate = $_POST['u_car_bookdate'];
    $u_car_book_status  = $_POST['u_car_book_status'];

    $query="UPDATE tms_user 
            SET u_car_type=?, 
                u_car_bookdate=?, 
                u_car_regno=?, 
                u_car_book_status=?, 
                u_car_payment_status='Unpaid' 
            WHERE u_id=?";

    $stmt = $mysqli->prepare($query);
    if($stmt){
        $stmt->bind_param('ssssi', $u_car_type, $u_car_bookdate, $u_car_regno, $u_car_book_status, $u_id);
        $exec = $stmt->execute();
        if($exec){
            $succ = "User Booking Added";
        } else {
            $err = "Please Try Again Later";
        }
    } else {
        $err = "Query Preparation Failed";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include('vendor/inc/head.php');?>
<body id="page-top">
<?php include("vendor/inc/nav.php");?>
<div id="wrapper">

  <!-- Sidebar -->
  <?php include("vendor/inc/sidebar.php");?>
  <!--End Sidebar-->

  <div id="content-wrapper">
    <div class="container-fluid">

      <!-- ✅ Alerts -->
      <?php if(isset($succ)) { ?>
        <script>
          setTimeout(function () { swal("Success!","<?php echo $succ;?>!","success"); }, 100);
        </script>
      <?php } if(isset($err)) { ?>
        <script>
          setTimeout(function () { swal("Failed!","<?php echo $err;?>!","error"); }, 100);
        </script>
      <?php } ?>

      <!-- Registered Users -->
      <div class="card mb-3">
        <div class="card-header">
          <i class="fas fa-users"></i> Registered Users
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Contact</th>
                  <th>Address</th>
                  <th>Email</th>
                  <th>Payment</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
              <?php
              $ret="SELECT * FROM tms_user WHERE u_category='User'"; 
              $stmt= $mysqli->prepare($ret);
              $stmt->execute();
              $res=$stmt->get_result();
              $cnt=1;
              while($row=$res->fetch_object()) {
              ?>
                <tr>
                  <td><?php echo $cnt;?></td>
                  <td><?php echo $row->u_fname;?> <?php echo $row->u_lname;?></td>
                  <td><?php echo $row->u_phone;?></td>
                  <td><?php echo $row->u_addr;?></td>
                  <td><?php echo $row->u_email;?></td>
                  <td>
                    <?php 
                      if($row->u_car_payment_status == "Paid"){ 
                          echo '<span class="badge badge-success">Paid</span>'; 
                      } else { 
                          echo '<span class="badge badge-danger">Unpaid</span>'; 
                      }
                    ?>
                  </td>
                  <td>
                    <a href="admin-add-booking-usr.php?u_id=<?php echo $row->u_id;?>" 
                       class="badge badge-success">
                      <i class="fa fa-clipboard"></i> Book Vehicle
                    </a>
                  </td>
                </tr>
              <?php $cnt++; } ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="card-footer small text-muted">
          <?php date_default_timezone_set("Africa/Nairobi"); echo "Updated ".date("d M Y h:i A"); ?>
        </div>
      </div>

      <!-- Booking Form (when u_id provided) -->
      <?php if(isset($_GET['u_id'])) {
        $aid=intval($_GET['u_id']);
        $ret="SELECT * FROM tms_user WHERE u_id=?";
        $stmt= $mysqli->prepare($ret);
        $stmt->bind_param('i',$aid);
        $stmt->execute();
        $res=$stmt->get_result();
        if($row=$res->fetch_object()){
      ?>
      <div class="card">
        <div class="card-header">Add Booking</div>
        <div class="card-body">
          <form method="POST"> 
            <input type="hidden" name="u_id" value="<?php echo $row->u_id; ?>">
            <div class="form-group">
              <label>First Name</label>
              <input type="text" value="<?php echo $row->u_fname;?>" class="form-control" readonly>
            </div>
            <div class="form-group">
              <label>Last Name</label>
              <input type="text" value="<?php echo $row->u_lname;?>" class="form-control" readonly>
            </div>
            <div class="form-group">
              <label>Contact</label>
              <input type="text" value="<?php echo $row->u_phone;?>" class="form-control" readonly>
            </div>
            <div class="form-group">
              <label>Address</label>
              <input type="text" value="<?php echo $row->u_addr;?>" class="form-control" readonly>
            </div>
            <div class="form-group">
              <label>Email</label>
              <input type="email" value="<?php echo $row->u_email;?>" class="form-control" readonly>
            </div>

            <div class="form-group">
              <label>Vehicle Category</label>
              <select class="form-control" name="u_car_type" required>
                <option <?php if($row->u_car_type=="Bus") echo "selected"; ?>>Bus</option>
                <option <?php if($row->u_car_type=="Matatu") echo "selected"; ?>>Matatu</option>
                <option <?php if($row->u_car_type=="Nissan") echo "selected"; ?>>Nissan</option>
              </select>
            </div>

            <div class="form-group">
              <label>Vehicle Registration Number</label>
              <select class="form-control" name="u_car_regno" required>
                <?php
                $ret="SELECT * FROM tms_vehicle";
                $stmt2= $mysqli->prepare($ret);
                $stmt2->execute();
                $res2=$stmt2->get_result();
                while($vrow=$res2->fetch_object()){ ?>
                  <option <?php if($row->u_car_regno==$vrow->v_reg_no) echo "selected"; ?>>
                    <?php echo $vrow->v_reg_no;?>
                  </option>
                <?php } ?> 
              </select>
            </div>

            <div class="form-group">
              <label>Booking Date</label>
              <input type="date" class="form-control" name="u_car_bookdate" 
                     value="<?php echo $row->u_car_bookdate;?>" required>
            </div>

            <div class="form-group">
              <label>Booking Status</label>
              <select class="form-control" name="u_car_book_status" required>
                <option <?php if($row->u_car_book_status=="Approved") echo "selected"; ?>>Approved</option>
                <option <?php if($row->u_car_book_status=="Pending") echo "selected"; ?>>Pending</option>
              </select>
            </div>

            <button type="submit" name="book_vehicle" class="btn btn-success">Confirm Booking</button>
          </form>
        </div>
      </div>
      <?php } } ?>

    </div>
    <?php include("vendor/inc/footer.php");?>
  </div>
</div>

<!-- Scripts -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="vendor/datatables/jquery.dataTables.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.js"></script>
<script src="vendor/js/sb-admin.min.js"></script>
<script src="vendor/js/demo/datatables-demo.js"></script>
<script src="vendor/js/swal.js"></script>
</body>
</html>
