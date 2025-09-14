<?php
session_start();
include('vendor/inc/config.php');
include('vendor/inc/checklogin.php');
check_login();

// Assume booking details already set
$booking_id = 123; 
$user_email = "customer@mail.com"; 
$amount = 50; // GHS
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Booking Payment</title>
  <script src="https://js.paystack.co/v1/inline.js"></script>
</head>
<body>
  <h2>Booking Payment</h2>
  <p><b>Amount:</b> GHS <?php echo $amount; ?></p>

  <!-- Payment options -->
  <form id="paymentForm" method="POST" action="process-payment.php">
    <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
    <input type="hidden" name="amount" value="<?php echo $amount; ?>">
    <input type="hidden" name="email" value="<?php echo $user_email; ?>">

    <label>
      <input type="radio" name="payment_method" value="paystack" checked>
      Pay Online (Paystack)
    </label><br>

    <label>
      <input type="radio" name="payment_method" value="offline">
      Pay Offline (Mobile Money / Bank Transfer)
    </label>

    <!-- Offline instructions -->
    <div id="offline-instructions" style="display:none; margin-top:10px; padding:10px; border:1px solid #ccc;">
      <p><strong>Send payment to:</strong></p>
      <p>📱 MTN Momo: <b>024XXXXXXX</b></p>
      <p>🏦 Bank Transfer: <b>Bank Name - Acc No: 123456789</b></p>
      <p>After payment, send your transaction ID to support.</p>
    </div>

    <br>
    <button type="submit" id="payBtn">Proceed to Payment</button>
  </form>

  <script>
  // Show/hide offline instructions
  document.querySelectorAll('input[name="payment_method"]').forEach(function(el){
    el.addEventListener("change", function(){
      if(this.value === "offline"){
        document.getElementById("offline-instructions").style.display = "block";
      } else {
        document.getElementById("offline-instructions").style.display = "none";
      }
    });
  });

  // Handle Paystack inline payment
  document.getElementById('paymentForm').addEventListener("submit", function(e){
    var method = document.querySelector('input[name="payment_method"]:checked').value;

    if(method === "paystack"){
      e.preventDefault(); // stop form submission

      var amount = <?php echo $amount * 100; ?>; // amount in pesewas
      var email  = "<?php echo $user_email; ?>";
      var booking_id = "<?php echo $booking_id; ?>";

      var handler = PaystackPop.setup({
        key: 'pk_test_f70535da983234617a35a994659666eef46134c0', // replace with your PUBLIC KEY
        email: email,
        amount: amount,
        currency: "GHS",
        ref: 'BOOK-' + booking_id + '-' + Math.floor((Math.random() * 1000000000) + 1),
        callback: function(response){
          window.location.href = "verify_payment.php?reference=" + response.reference + "&booking_id=" + booking_id;
        },
        onClose: function(){
          alert('Payment window closed.');
        }
      });
      handler.openIframe();
    }
  });
  </script>
</body>
</html>
