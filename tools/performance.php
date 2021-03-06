<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

<?php
// Initialize the session
session_start();
$listsectors = array();
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: test.html");
    exit;
}

function obtainHistoricalData(){

}


function draw_Table(){
  if(isset($_POST['sectorview'])){
    $userchoice = $_POST['sectorview'];
  }else{
  $userchoice = "All";
  }

include_once "config.php";
$totalvalue = 0;

//Check the Value of The dropdown list2
//Make a query to obtain all stocks associated with the session ID.
if($userchoice == "All" || $userchoice == null){
  $query = "SELECT * FROM stocks WHERE userID = {$_SESSION["id"]}";
} else{
  $query = "SELECT * FROM stocks WHERE userID = {$_SESSION["id"]} AND sector = '{$userchoice}'";
}
if($result = $link->query($query)){
//Fetch the associated data
echo "<table class='tg'><thead>";
echo "<tr><th>Ticker Symbol:</th><th>Stock Name:</th><th>Amount of Shares:</th><th>Date Purchased:</th><th>Current Price:</th><th>Percent Change Today:</th><th>Sector</th><th></th><th></th></tr></thead>";

while($row = $result->fetch_assoc()){
$ticker = $row["symbol"];
$url = "https://query1.finance.yahoo.com/v7/finance/quote?lang=en-US&region=US&corsDomain=finance.yahoo.com&symbols={$ticker}";
$jsonData = file_get_contents($url);
$formatted_jsonData = json_decode($jsonData, true);
$shortname = $formatted_jsonData["quoteResponse"]["result"][0]["shortName"];
$askprice = $formatted_jsonData["quoteResponse"]["result"][0]["ask"];
$sector = $row["sector"];
//Add the relevant sector to the list allowing us to plot
$listsectors[] = $sector;
//Check the ask price is not 0 and if it is use the previous weekly close
if ($askprice == 0){
  $askprice = $formatted_jsonData["quoteResponse"]["result"][0]["regularMarketPrice"];
}
$pctChange = round($formatted_jsonData["quoteResponse"]["result"][0]["regularMarketChangePercent"],2);
$id = $row["id"];
$totalvalue += $askprice * $row["amount"];
echo "<tr><td class='tg-0lax'>{$row["symbol"]}</td><td class='tg-0lax'>{$shortname}</td><td class='tg-0lax'>{$row["amount"]}</td><td>{$row["dateadded"]}</td><td>{$askprice}</td><td>{$pctChange}%</td><td>{$row["sector"]}</td><td><a href='deletecommand.php?id={$id}'>DELETE</a><td><a href='editcommand.php?id={$id}'>EDIT</a></td></tr>";
}
echo "</table>";
//Prepare the JSON File used to chart the sector information
global $sectorJson;
$sectorJson = json_encode(spit_sectors_toJson($listsectors));
mysqli_close($link);
}
}


?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; text-align: center; }
    </style>
    <link rel="stylesheet" href="../style/main.css">
</head>
<div id="mySidenav" class="sidenav">
  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
  <a href="welcome.php">Portfolio</a>
  <a href="tools/addsymbol.php">Add New Item</a>
  <a href="#">Sector Breakdown</a>
  <a href="tools/qrcode.php">Link to Mobile</a>
</div>

<body>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<nav class="navbar navbar-expand-lg navbar-light bg-light navbar-dark bg-dark">
				 
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					<span class="navbar-toggler-icon"></span>
				</button> <a class="navbar-brand" href="#" onclick="openNav()">??? MobileTicker Web</a>
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="navbar-nav">
						<li class="nav-item">
							 <a class="nav-link" href="../welcome.php">Portfolio</a>
						</li>
						<li class="nav-item active">
							 <a class="nav-link" href="#">Performance<span class="sr-only">(current)</span></a>
						</li>
            <li>
            <a class="nav-link" href="calendar.php">Calendar</a>
          </li>
					</ul>
					<ul class="navbar-nav ml-md-auto">
						<li class="nav-item dropdown">
							 <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown"><?php echo $_SESSION["username"]?></a>
							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
								 <a class="dropdown-item" href="#">My Account</a>
								<div class="dropdown-divider">
								</div> <a class="dropdown-item" href="../logout.php">Log Out</a>
							</div>
						</li>
					</ul>
				</div>
			</nav>
			<div class="dropdown dropup">
      <div class="float-right">
</div>
<br><br>
<div class="flex-container">
<div class="card purple" style="width: 18rem;">
  <div class="card-body">
    <h5 class="card-title">Build your portfolio</h5>
    <p class="card-text">You can start adding stocks and products to your portfolio to analyse performance metrics.</p>
    <a href="addsymbol.php" class="btn btn-primary">Add a symbol</a>
  </div>
</div>
  <div>2</div>
</div>




</body>
<script>

function openNav() {
  document.getElementById("mySidenav").style.width = "12%";
}

/* Set the width of the side navigation to 0 */
function closeNav() {
  document.getElementById("mySidenav").style.width = "0";
}
</script>
</html>