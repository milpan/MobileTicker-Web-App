<?php
//Connect to the Database
require_once "config.php";
//Check that the user is not already logged in
session_start();
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true){
	header("location: welcome.php");
	exit;
}
//Obtain the Post Values
$txtEmail = $_POST['txtEmail'];
$txtPass = $_POST['txtPassword'];
//Validate the user credentials
$sql = "SELECT id, email, password FROM users WHERE email = ?";

if($stmt = $link->prepare($sql)){
	$stmt->bind_param("s", $param_username);
	$param_username = $txtEmail;
	if($stmt->execute()){
	//Execute the prepared statement
	$stmt->store_result();
	//Check if any users exist with that email address
	if($stmt->num_rows == 1){
	$stmt->bind_result($id, $email, $hashed_password);
	//Check if the passwords match the result found from the SQL Query
	if(mysqli_stmt_fetch($stmt)){
		if(password_verify($txtPass, $hashed_password)){
			session_start();
			$_SESSION["loggedin"] = true;
			$_SESSION["id"] = $id;
			$_SESSION["username"] = $txtEmail;
			header("location: welcome.php");
		}else{
			$login_err = "Invalid username or password.";
		}
	}
	}else{
		echo "Invalid username or password.";
	}
	mysqli_stmt_close($stmt);
	}
	mysqli_close($link);
}

?>