<?php
//Connect to the Database
require_once "config.php";
$id = $_GET['id'];
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