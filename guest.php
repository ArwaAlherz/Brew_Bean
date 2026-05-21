<?php
session_start();

unset($_SESSION['customer_id']);
unset($_SESSION['customer_name']);
unset($_SESSION['customer_email']);

$_SESSION['guest'] = true;

header("Location: code.php");
exit();
?>