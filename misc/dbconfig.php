<?php
$host = "localhost";
$username = "root";
$password = "root";
$dbname = "totsandblocks";

$con = new mysqli($host, $username, $password, $dbname);
if($con->connect_error){
    die("Connection failed: " . $con->connect_error);
}
?>
