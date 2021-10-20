<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: test.html");
    exit;
}
?>
 
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Link to Mobile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; text-align: center; }
    </style>
</head>
    <nav class="navbar navbar-light bg-light">
  <span class="navbar-brand mb-0 h1"><a onclick="openNav()">MobileTicker</a></span>
    <span class="btn btn-danger ml-3" ><a href="logout.php">Log Out</a></span>
</nav>
<div id="mySidenav" class="sidenav">
  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
  <a href="../welcome.php">Portfolio</a>
  <a href="addsymbol.php">Add New Item</a>
  <a href="#">Link to Mobile</a>
  <a href="#">Contact</a>
</div>
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
<body>
<?php 
use chillerlan\QRCode\{QRCode, QROptions};
include_once "../vendor/autoload.php";

$data = "MobileTickerUser={$_SESSION['id']}";

// quick and simple:
echo '<center><img src="'.(new QRCode)->render($data).'" alt="QR Code" /></center>';
?>

<center>
  <p>
    This QR Code can be scanned on the mobile version of the App to connect to the database.
</p>
</center>

</body>
</html>