<?php
require_once "../config.php";
session_start();
if($_SERVER["REQUEST_METHOD"] == "POST"){
//Check the user is infact logged in
$ticker = trim($_POST['tickersymbol']);
$amount = trim($_POST['amountof']);
$sector = $_POST['sector'];

$allgood = 0;

//Check that the Ticker Symbol and Amount of stock entered is not empty
if(empty(trim($ticker)) || empty(trim($amount))){
	$allgood = 0;
echo "The ticker symbol or Amount of Stock purchased needs to be populated.";
exit;
}else{
	$sql = "SELECT * FROM stocks WHERE userID = {$_SESSION['id']}";
	//Check that the Ticker symbol is not already added
	if($result = $link->query($sql)){
		while($row = $result->fetch_assoc()){
			if($row["symbol"] == $ticker){
				echo "This stock has already been added to your portfolio.";
				exit;
			}else{
			$allgood = 1;
			}
		}
		$allgood = 1;
	}
		if($allgood==1){

			$sql2 = "INSERT INTO stocks (symbol, amount, userID, sector) VALUES (?, ?, ?, ?)";
				if($stmt = mysqli_prepare($link, $sql2)){
					$stmt->bind_param("sdis", $bind_ticker, $bind_amount, $bind_SESSIONID, $sector);
					//Redirect back to the main page
					$bind_ticker = trim($ticker);
					$bind_amount = trim($amount);
					$bind_SESSIONID = trim($_SESSION["id"]);
					$stmt->execute();
					header("location: ../welcome.php");
					/*
					if(){
						header("location: welcome.php");
					}else{
						echo "Error when adding this symbol to the portfolio";
					}
					*/
					$stmt->close();
					$link->close();
				}
		}
		
		}
	}
	

?>
<!DOCTYPE html>
<html>
    <head>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
        <title>Add a new Stock</title>
    </head>
<body>

<nav class="navbar navbar-light bg-light">
  <span class="navbar-brand mb-0 h1">MobileTicker</span>
  <span class="btn btn-danger ml-3" ><a href="../welcome.php">Back</a></span>
</nav>

<fieldset>
<div class="align-self-center bg-dark border border-primary text-white" background-color=Black>
<center>

<Legend>Add a stock to the portfolio:</Legend>
<form name="frmTest" method="post">
<p>
    <label for="Name of Ticker">Ticker Symbol:</label>
    <input type="text" name="tickersymbol" id="tickersymbol"> 
</p>
<p>
    <label for="Amount of Shares">Amount</label>
    <input type="number" name="amountof" id="amountof"> 
</p>
<label for="Sector Information">Corrosponding Sector:</label>
<p>
    <select class="form-select" size="7" aria-label="size 7 select example" name="sector" id="sector">
  	<option value="Air Travel">Air Travel</option>
  	<option value="Basic Materials">Basic Materials</option>
  	<option value="Communication Services">Communication Services</option>
  	<option value="Conglomerates">Conglomerates</option>
	<option value="Consumer Cyclical">Consumer Cyclical</option>
  	<option value="Consumer Defensive">Consumer Defensive</option>
  	<option value="Energy">Energy</option>
	<option value="Financial">Financial</option>
  	<option value="Financial Services">Financial Services</option>
  	<option value="Healthcare">Healthcare</option>
  	<option value="Industrial Goods">Industrial Goods</option>
	<option value="Industrials">Industrials</option>
  	<option value="Real Estate">Real Estate</option>
  	<option value="Services">Services</option>
  	<option value="Technology">Technology</option>
  	<option value="Utilities">Utilities</option>
</select>
</p>
<p>
    <input type="submit" name="Submit" id="Submit" value="Add this symbol to the portfolio">
</p>
</form>

</center>
</div>
</fieldset>

</body>
</html>