<?php
//Connect to the Database
require_once "config.php";
$id = $_GET['id'];
session_start();
//Check that the user has access to delete this item
$check = mysqli_query($link, "SELECT * FROM stocks WHERE userID = {$_SESSION["id"]} AND id={$id}");
if ($check->num_rows === 0){
	echo "Error, the ID of this stock is not assigned to your user ID";
	die();
}

//SQL Query to delete this item
$del = mysqli_query($link, "DELETE FROM stocks WHERE id={$id}");
if($del){
	mysqli_close($link);
	header("location:welcome.php");
	exit;
}else{
echo "Error deleting this entry, please contact an administrator";
}
?>