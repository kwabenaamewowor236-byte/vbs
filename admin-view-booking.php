<?php
  session_start();
  include('vendor/inc/config.php');
  include('vendor/inc/checklogin.php');
  check_login();
  $aid=$_SESSION['a_id'];
?>
<!DOCTYPE html>
<html lang="en">

<?php include('vendor/inc/head.php');?>

<body id="page-top">

 <!-- Navbar -->
 <?php include("vendor/inc/nav.php");?>

 <div id="wrapper">

    <!-- Sidebar -->
    <?php include('vendor/inc/sidebar.php');?>

    <div id="content-wrapper">

      <div class="container-fluid">

        <!-- Breadcrumbs-->
        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="#">Bookings</a>
          </li>
          <li class="breadcrumb-item active">View</li>
        </ol>

        <!--Bookings Table-->
        <div class="card mb-3">
          <div class="card-header">
            <i class="fas fa-table"></i>
            Bookings</div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Vehicle Type</th>
                    <th>Vehicle Reg No</th>
                    <th>Booking Date</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                  $ret="SELECT * FROM tms_user WHERE u_car_book_status = 'Approved' OR u_car_book_status = 'Pending'";
                  $stmt= $mysqli->prepare($ret);
                  $stmt->execute();
                  $res=$stmt->get_result();
                  $cnt=1;
                  while($row=$res->fetch_object())
                  {
                ?>
                  <tr>
                    <td><?php echo $cnt;?></td>
                    <td><?php echo $row->u_fname;?> <?php echo $row->u_lname;?></td>
                    <td><?php echo $row->u_phone;?></td>
                    <td><?php echo $row->u_car_type;?></td>
                    <td><?php echo $row->u_car_regno;?></td>
                    <td><?php echo $row->u_car_bookdate;?></td>
                    <td>
                      <?php 
                        if($row->u_car_book_status == "Pending"){ 
                          echo '<span class="badge badge-warning">'.$row->u_car_book_status.'</span>'; 
                        } else { 
                          echo '<span class="badge badge-success">'.$row->u_car_book_status.'</span>';
                        }
                      ?>
                    </td>
                    <td>
                      <?php 
                        if($row->u_car_payment_status == "Unpaid"){ 
                          echo '<span class="badge badge-danger">Unpaid</span>'; 
                        } else { 
                          echo '<span class="badge badge-success">Paid</span>';
                        }
                      ?>
                    </td>
                    <td>
                      <?php if($row->u_car_payment_status == "Unpaid"){ ?>
                        <a href="admin-mark-paid.php?u_id=<?php echo $row->u_id;?>" class="badge badge-info">
                          <i class="fa fa-money-bill"></i> Mark Paid
                        </a>
                      <?php } else { ?>
                        <span class="badge badge-secondary">Settled</span>
                      <?php } ?>
                    </td>
                  </tr>
                <?php $cnt++; } ?>
                </tbody>
              </table>
            </div>
          </div>
          <div class="card-footer small text-muted">
            <?php
              date_default_timezone_set("Africa/Nairobi");
              echo "The time is " . date("h:i:sa");
            ?> 
          </div>
        </div>
      </div>
      <!-- /.container-fluid -->

      <!-- Sticky Footer -->
      <?php include("vendor/inc/footer.php");?>
    </div>
    <!-- /.content-wrapper -->

  </div>
  <!-- /#wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <!-- Logout Modal-->
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <a class="btn btn-danger" href="admin-logout.php">Logout</a>
        </div>
      </div>
    </div>
  </div>

  <!-- JS -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="vendor/datatables/jquery.dataTables.js"></script>
  <script src="vendor/datatables/dataTables.bootstrap4.js"></script>
  <script src="js/sb-admin.min.js"></script>
  <script src="js/demo/datatables-demo.js"></script>

</body>
</html>
