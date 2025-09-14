<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

if (!isset($_GET['reference']) || !isset($_GET['booking_id'])) {
    die("Invalid request.");
}

$reference  = $_GET['reference'];
$booking_id = intval($_GET['booking_id']);

$secret_key = "sk_test_xxxxxxxxxxxxxxxxxxxxxx"; // replace with your SECRET key

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer " . $secret_key,
        "Cache-Control: no-cache",
    ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    die("cURL Error #: " . $err);
}

$result = json_decode($response, true);

if ($result && isset($result['data']) && $result['data']['status'] === 'success') {
    $amount_paid = $result['data']['amount'] / 100;
    $payment_ref = $result['data']['reference'];

    $stmt = $mysqli->prepare("UPDATE tms_bookings SET status='Paid', payment_reference=? WHERE booking_id=?");
    $stmt->bind_param("si", $payment_ref, $booking_id);

    if ($stmt->execute()) {
        header("Location: user-confirm-booking.php?booking_id=" . $booking_id . "&success=1");
        exit();
    } else {
        die("Database update failed: " . $stmt->error);
    }
} else {
    header("Location: user-confirm-booking.php?booking_id=" . $booking_id . "&error=1");
    exit();
}
