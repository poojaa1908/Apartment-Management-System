<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "ams_db";
  // Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
  // Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
else{
	echo "Connection Is Okay!";
}



$sql = "INSERT INTO `tbl_add_floor`(floor_no,`branch_id`) values(8,'floor Test')";

    echo $sql;
	$name = mysqli_query($conn,$sql);
     echo $name;
	mysqli_close($conn);

