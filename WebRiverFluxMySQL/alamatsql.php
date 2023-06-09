<?php
$servername = "[server_name]";
$username = "[root or user_name]";
$password = "";
$dbname = "[name_of_databae]";
// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}
?>