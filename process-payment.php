<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

$booking_id = intval($_POST['booking_id']);
$payment_method = $_POST['payment_method'];

if ($payment_method === "offline") {
    $status = "Pending Payment (Offline)";
    $stmt = $mysqli->prepare("UPDATE tms_bookings SET status=? WHERE booking_id=?");
    $stmt->bind_param("si", $status, $booking_id);
    $stmt->execute();

    header("Location: user-confirm-booking.php?booking_id=" . $booking_id . "&offline=1");
    exit();
} else {
    // For Paystack, nothing to do here (handled by JS inline + verify_payment.php)
    die("Invalid request.");
}
