<?php
include('../vendor/inc/config.php');

if ($mysqli->ping()) {
    echo "✅ Database connected successfully!";
} else {
    echo "❌ Database connection failed!";
}
?>
