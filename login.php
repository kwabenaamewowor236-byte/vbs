<?php
session_start();
include('vendor/inc/config.php'); // DB connection

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // --- First, check Admin ---
    $stmt = $mysqli->prepare("SELECT a_id, a_pwd, a_email FROM tms_admin WHERE a_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($a_id, $hashed_pwd, $a_email);
        $stmt->fetch();

        // If admin password matches
        if (password_verify($password, $hashed_pwd)) {
            $_SESSION['a_id'] = $a_id;
            $_SESSION['login'] = $a_email;
            header("Location: admin-dashboard.php");
            exit();
        }
    }
    $stmt->close();

    // --- Then, check Client ---
    $stmt = $mysqli->prepare("SELECT u_id, u_pwd, u_email FROM tms_user WHERE u_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($u_id, $hashed_pwd, $u_email);
        $stmt->fetch();

        // If client password matches
        if (password_verify($password, $hashed_pwd)) {
            $_SESSION['u_id'] = $u_id;
            $_SESSION['login'] = $u_email;
            header("Location: user-dashboard.php");
            exit();
        }
    }
    $stmt->close();

    // --- If no match found ---
    $error = "Invalid email or password.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Vehicle Booking System - Login</title>
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="vendor/css/sb-admin.css" rel="stylesheet">
  <script src="vendor/js/swal.js"></script>
</head>
<body class="bg-dark">
  <div class="container">
    <div class="card card-login mx-auto mt-5">
      <div class="card-header">Login Panel</div>
      <div class="card-body">
        <?php if (isset($error)) { ?>
        <script>
          setTimeout(() => {
            swal("Login Failed", "<?php echo $error; ?>", "error");
          }, 100);
        </script>
        <?php } ?>

        <form method="POST">
          <div class="form-group">
            <div class="form-label-group">
              <input type="email" name="email" id="inputEmail" class="form-control" placeholder="Email address" required autofocus>
              <label for="inputEmail">Email address</label>
            </div>
          </div>
          <div class="form-group">
            <div class="form-label-group">
              <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" required>
              <label for="inputPassword">Password</label>
            </div>
          </div>
          <input type="submit" name="login" class="btn btn-success btn-block" value="Login">
        </form>

        <div class="text-center mt-3">
          <a class="d-block small" href="usr-register.php">Register an Account</a>
          <a class="d-block small" href="usr-forgot-password.php">Forgot Password?</a>
          <a class="d-block small" href="../index.php">Home</a>
        </div>
      </div>
    </div>
  </div>

  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
</body>
</html>
