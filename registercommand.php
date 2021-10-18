<?php
//Connect to the Database
require_once "config.php";
//Define the variables the user types into the form
$email = $password = $confirm_password = "";
$email_err = $password_err = $confirm_password_error = "";
//Process the data once the form has been submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
	//Validate the username first
	if(empty(trim($_POST["email"]))){
		$email_err = "Please enter a valid email address";
	}else{
		$sql = "SELECT id FROM UserInfo WHERE email = ?";
		if($stmt= mysqli_prepare($link, $sql)){
			mysqli_stmt_bind_param($stmt, "s", $param_email);
			//Set the parameter
			$param_email = trim($_POST["email"]);
			//Attempt to execute this prepared statement
			if(mysqli_stmt_execute($stmt)){
				mysqli_stmt_store_result($stmt);
				//Check the email is not already in use
				if(mysqli_stmt_num_rows($stmt)==1){
					$email_err = "This email is already in use";
				}else{
					$email = trim($_POST["email"]);
				}
			
			}else{
				echo "Oops! Something went wrong. Please try again later.";
			}
			//Close prepared statement
			mysqli_stmt_close($stmt);
		}
	}
	//Validate the password and confirm password fields
	if(empty(trim($_POST["password"]))){
		$password_err = "Please enter a valid password.";
	}elseif(strlen(trim($_POST["password"])) <6){
		$password_err = "Please enter a valid password comprising of more than 6 characters.";
	}else{
		$password = trim($_POST["password"]);
	}
	//Validate the confirm password
	if(empty(trim($_POST["confirm_password"]))){
		$confirm_password_error = "Please confirm your password.";
	}else{
		$confirm_password  = trim($_POST["confirm_password"]);
		if(empty($password_err) && ($password != $confirm_password)){
			$confirm_password_error = "Confirm password did not match your entered password.";
		}
	}
	//Check all the confirm errors are blank and if call the INSERT function
	if(empty($email_err) && empty($password_err) && empty($confirm_password_error)){
		$sql = "INSERT INTO users (email, password) VALUES (?,?)";
		if($stmt = mysqli_prepare($link, $sql)){
			mysqli_stmt_bind_param($stmt, "ss", $param_email, $param_password);
			//Set the parameters
			$param_email = trim($_POST["email"]);
			$param_password = password_hash($password, PASSWORD_DEFAULT);
			//Execute the prepared statement
			if(mysqli_stmt_execute($stmt)){
				//Redirect to the login page
				header("location: test.html");
			}else{
				echo "Registration Error!";
			}
			mysqli_stmt_close($stmt);
		}
	}
	mysqli_close($link);
}
?>