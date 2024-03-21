<?php
$emailTo = (isset($_GET['email']) && !empty($_GET['email']) ? $_GET['email'] : "skweblinetest@gmail.com");

$subject = "Request: Send Email";

$headers = "Content-type: text/html; charset=UTF-8" . "\r\n";
$headers .= "To: " . "\r\n";
$headers .= "From: skweblinetest@gmail.com" . "\r\n";
$headers .= "Cc: " . "\r\n";
$headers .= "Bcc: " . "\r\n";

$messageProper = "<h3>Test email from SK Webline LTD</h3><br>IP: ".$_SERVER['REMOTE_ADDR'];

mail($emailTo, $subject, $messageProper,  $headers);
