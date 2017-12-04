<?php

require_once("db_config.php");

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "";


if (isset($_GET['type'])) {
	if($_GET['type'] == 0) {
	    $sql = "SELECT * FROM ".$tbname;
	} else {
	    $sql = "SELECT * FROM ".$tbname." WHERE type =".$_GET['type'];
	}
} else {
	 $sql = "SELECT * FROM ".$tbname." WHERE id =".$_GET['id'];
}

$sql .= " ORDER BY id DESC";





$result = $conn->query($sql);
$output = array();

while($row = $result->fetch_assoc()){
    array_push($output,$row);
}

echo json_encode($output);

$conn->close();


?>