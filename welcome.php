<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: test.html");
    exit;
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
  font-size: 25px;
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
    <nav class="navbar navbar-light bg-light">
  <span class="navbar-brand mb-0 h1"><a onclick="openNav()">MobileTicker</a></span>
  <span class="btn btn-warning ml-3" ><a href="welcome.php">Refresh</a></span>
    <span class="btn btn-danger ml-3" ><a href="logout.php">Log Out</a></span>
</nav>
<div id="mySidenav" class="sidenav">
  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
  <a href="#">Portfolio</a>
  <a href="tools/addsymbol.php">Add New Item</a>
  <a href="#">Sectors</a>
  <a href="#">Contact</a>
</div>

<body>
<p>Welcome to MobileTicker <?php echo $_SESSION["username"]?> with user ID <?php echo $_SESSION["id"]?></p>
<br>
<center>
<?php
$totalvalue = 0;
require_once "config.php";
//Make a query to obtain all stocks associated with the session ID.
$query = "SELECT * FROM stocks WHERE userID = {$_SESSION["id"]}";
if($result = $link->query($query)){
//Fetch the associated data
echo "<table class='tg'><thead>";
echo "<tr><th>Ticker Symbol:</th><th>Stock Name:</th><th>Amount of Shares:</th><th>Date Purchased:</th><th>Current Price:</th><th>Percent Change Today:</th><th></th></tr></thead>";

while($row = $result->fetch_assoc()){
$ticker = $row["symbol"];
$url = "https://query1.finance.yahoo.com/v7/finance/quote?lang=en-US&region=US&corsDomain=finance.yahoo.com&symbols={$ticker}";
$jsonData = file_get_contents($url);
$formatted_jsonData = json_decode($jsonData, true);
$shortname = $formatted_jsonData["quoteResponse"]["result"][0]["shortName"];
$askprice = $formatted_jsonData["quoteResponse"]["result"][0]["ask"];
//Check the ask price is not 0 and if it is use the previous weekly close
if ($askprice == 0){
  $askprice = $formatted_jsonData["quoteResponse"]["result"][0]["regularMarketPrice"];
}
$pctChange = round($formatted_jsonData["quoteResponse"]["result"][0]["regularMarketChangePercent"],2);
$id = $row["id"];
$totalvalue += $askprice * $row["amount"];
echo "<tr><td class='tg-0lax'>{$row["symbol"]}</td><td class='tg-0lax'>{$shortname}</td><td class='tg-0lax'>{$row["amount"]}</td><td>{$row["dateadded"]}</td><td>{$askprice}</td><td>{$pctChange}%</td><td><a href='deletecommand.php?id={$id}'>DELETE</a></td></tr>";
}
echo "</table>";
mysqli_close($link);
echo "<br> Total portfolio Value is {$totalvalue}";
}
?>
</center>
</body>
<script>
window.onload = function openNav() {
  document.getElementById("mySidenav").style.width = "15%";
}

function openNav() {
  document.getElementById("mySidenav").style.width = "15%";
}

/* Set the width of the side navigation to 0 */
function closeNav() {
  document.getElementById("mySidenav").style.width = "0";
}
</script>
</html>