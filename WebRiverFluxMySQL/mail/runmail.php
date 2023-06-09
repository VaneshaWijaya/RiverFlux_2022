<?php 
$to ='netoiiiii17@gmail.com';
$subject ='try this email';
$message ='<p>hello are you enjoy your day?</p>';

$headers ="From : The Sender Name <vaneshawijaya17@gmail.com> \r\n";
$headers .="Reply-To : vaneshawijaya17@gmail.com";
$headers .="Content-type: text/html\r\n";
mail($to,$subject,$message,$headers);
?>