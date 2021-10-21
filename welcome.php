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

function draw_Table(){
  if(isset($_POST['sectorview'])){
    $userchoice = $_POST['sectorview'];
  }else{
  $userchoice = "All";
  }

include_once "config.php";
include_once "tools/sectorcalculation.php";
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


<style>
.sidenav {
  height: 100%; /* 100% Full-height */
  width: 0; /* 0 width - change this with JavaScript */
  position: fixed; /* Stay in place */
  z-index: 1; /* Stay on top */
  top: 0; /* Stay at the top */
  left: 0;
  background-color: #111; /* Black*/
  overflow-x: hidden; /* Disable horizontal scroll */
  padding-top: 60px; /* Place content 60px from the top */
  transition: 0.5s; /* 0.5 second transition effect to slide in the sidenav */
}

/* The navigation menu links */
.sidenav a {
  padding: 8px 8px 8px 32px;
  text-decoration: none;
  font-size: 21px;
  color: #818181;
  display: block;
  transition: 0.3s;
}

/* When you mouse over the navigation links, change their color */
.sidenav a:hover {
  color: #f1f1f1;
}

/* Position and style the close button (top right corner) */
.sidenav .closebtn {
  position: absolute;
  top: 0;
  right: 25px;
  font-size: 36px;
  margin-left: 50px;
}

/* Style page content - use this if you want to push the page content to the right when you open the side navigation */
#main {
  transition: margin-left .5s;
  padding: 20px;
}

/* On smaller screens, where height is less than 450px, change the style of the sidenav (less padding and a smaller font size) */
@media screen and (max-height: 450px) {
  .sidenav {padding-top: 15px;}
  .sidenav a {font-size: 18px;}
}

.tg  {border-collapse:collapse;border-color:#aabcfe;border-spacing:0;}
.tg td{background-color:#e8edff;border-color:#aabcfe;border-style:solid;border-width:0px;color:#669;
  font-family:Arial, sans-serif;font-size:14px;overflow:hidden;padding:10px 5px;word-break:normal;}
.tg th{background-color:#b9c9fe;border-color:#aabcfe;border-style:solid;border-width:0px;color:#039;
  font-family:Arial, sans-serif;font-size:14px;font-weight:normal;overflow:hidden;padding:10px 5px;word-break:normal;}
.tg .tg-73oq{border-color:#000000;text-align:left;vertical-align:top}
.tg .tg-dvpl{border-color:inherit;text-align:right;vertical-align:top}
.tg .tg-0pky{border-color:inherit;text-align:left;vertical-align:top}
.tg .tg-0lax{text-align:left;vertical-align:top}
</style>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; text-align: center; }
    </style>
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
				</button> <a class="navbar-brand" href="#" onclick="openNav()">☰ MobileTicker Web</a>
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="navbar-nav">
						<li class="nav-item active">
							 <a class="nav-link" href="#">Portfolio <span class="sr-only">(current)</span></a>
						</li>
						<li class="nav-item">
							 <a class="nav-link" href="#">Trending</a>
						</li>
					</ul>
					<ul class="navbar-nav ml-md-auto">
						<li class="nav-item dropdown">
							 <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown"><?php echo $_SESSION["username"]?></a>
							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
								 <a class="dropdown-item" href="#">My Account</a>
								<div class="dropdown-divider">
								</div> <a class="dropdown-item" href="logout.php">Log Out</a>
							</div>
						</li>
					</ul>
				</div>
			</nav>
			<div class="dropdown dropup">
      <div class="float-right">
</div>
<br><br>
<center>
<div class="container-fluid">
  <table>
    <tr style="vertical-align:top"><td>
      <div style="margin-left: auto;">
    <form name="sectorSelect" action="welcome.php" method="POST">
  <select id="sectorview" name="sectorview" onchange="this.form.submit()">
  <option>Sort by Sector</option>
  <option value="All">All</option>
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
</div>
</form>
    <?php
    //On screen open drawn the portfolio for all the stocks
    draw_Table();
    ?>
    </td><td>
<canvas id="pie-chart" width="400" height="300"></canvas>
    </td></tr>
</table>
</center>
</div>
<script>
    var labellist = [];
    var amount = [];
    var test = JSON.parse('<?= $sectorJson; ?>');
    for(let i=0; i<test.length; i++){
      labellist[i] = test[i].name;
      amount[i] = test[i].allocation;
    }
    console.log(labellist);
    </script>
  <script>
  new Chart(document.getElementById("pie-chart"), {
    type: 'pie',
    data: {
      labels: labellist,
      datasets: [{
        label: "Population (millions)",
        backgroundColor: ["#2c3e50", "#7831d0","#3f3718","#ac5a3a","#1973f0", "#12e687", "#f63d87", "#be2c25", "#54715a", "#2b0e5d", "#d5d47d", "#44298e", "#56c664", "#200e99", "#31946e", "#dc7314"],
        data: amount
      }]
    },
    options: {
      title: {
        display: true,
        text: 'Sector Allocation'
      }
    }
});
</script>
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