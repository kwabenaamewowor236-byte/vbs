<?php
session_start();

// ✅ Use __DIR__ to make sure path works on all systems
include(__DIR__ . '/../vendor/inc/config.php');

// Handle admin login
if (isset($_POST['admin-login'])) {
    $a_email = trim($_POST['a_email']);
    $a_pwd   = trim($_POST['a_pwd']);

    // Prepare statement
    $stmt = $mysqli->prepare("SELECT a_id, a_pwd, a_email FROM tms_admin WHERE a_email = ?");
    if (!$stmt) {
        die("Database error: " . $mysqli->error);
    }

    $stmt->bind_param("s", $a_email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($a_id, $hashed_password, $email);
        $stmt->fetch();

        // ✅ Verify hashed password
        if (password_verify($a_pwd, $hashed_password)) {
            $_SESSION['a_id']  = $a_id;
            $_SESSION['login'] = $email;

            header("Location: admin-dashboard.php");
            exit();
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "No admin found with that email address.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Login - Vehicle Booking System</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/css/sb-admin.css" rel="stylesheet">
  <script src="../vendor/js/swal.js"></script>
</head>
<body class="bg-dark">
  <div class="container">
    <div class="card card-login mx-auto mt-5">
      <div class="card-header">Admin Login Panel</div>
      <div class="card-body">

        <!-- Show error if login fails -->
        <?php if (isset($error)) { ?>
        <script>
          setTimeout(function () {
            swal("Login Failed", "<?php echo $error; ?>", "error");
          }, 100);
        </script>
        <?php } ?>

        <form action="admin-login.php" method="POST">
          <div class="form-group">
            <input type="email" name="a_email" class="form-control" placeholder="Email address" required autofocus>
          </div>
          <div class="form-group">
            <input type="password" name="a_pwd" class="form-control" placeholder="Password" required>
          </div>
          <button type="submit" name="admin-login" class="btn btn-primary btn-block">Login</button>
        </form>

        <div class="text-center mt-3">
          <a class="d-block small" href="../index.php">Home</a>
          <a class="d-block small" href="forgot-password.php">Forgot Password?</a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
