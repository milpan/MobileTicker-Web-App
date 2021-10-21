<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

<link rel="stylesheet" type="text/css" href="https://uicdn.toast.com/tui-calendar/latest/tui-calendar.css" />
<link rel="stylesheet" type="text/css" href="https://uicdn.toast.com/tui.date-picker/latest/tui-date-picker.css" />
<link rel="stylesheet" type="text/css" href="https://uicdn.toast.com/tui.time-picker/latest/tui-time-picker.css" />

<?php
// Initialize the session
session_start();
global $dividenddata;
global $stocknames;
global $dividendfinal;
global $formatted_stockname;
$dividenddata = array();
$stocknames = array();
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: test.html");
    exit;
}

function obtain_dividendinfo(){
include_once "../config.php";
global $dividenddata;
global $dividendfinal;
global $formatted_stockname;
global $stocknames;
//Check the Value of The dropdown list2
//Make a query to obtain all stocks associated with the session ID.

$query = "SELECT * FROM stocks WHERE userID = {$_SESSION["id"]}";

if($result = $link->query($query)){
//Fetch the associated data
while($row = $result->fetch_assoc()){
$ticker = $row["symbol"];
$dividend_url = "https://query1.finance.yahoo.com/v7/finance/download/{$ticker}?period1=1477008000&period2=1634774400&interval=1wk&events=div&includeAdjustedClose=true";
$url = "https://query1.finance.yahoo.com/v7/finance/quote?lang=en-US&region=US&corsDomain=finance.yahoo.com&symbols={$ticker}";
$jsonData = file_get_contents($url);
//Dividend information is stored into a CSV
$csvData = file_get_contents($dividend_url);
$formatted_dividend = array_map('str_getcsv', file($dividend_url));
$formatted_jsonData = json_decode($jsonData, true);
$shortname = $formatted_jsonData["quoteResponse"]["result"][0]["shortName"];
$askprice = $formatted_jsonData["quoteResponse"]["result"][0]["ask"];
if(count($formatted_dividend) != 1){
  $stocknames[] = $shortname;
  $dividenddata[] = $formatted_dividend;
}
$sector = $row["sector"];
//Add the relevant sector to the list allowing us to plot
$listsectors[] = $sector;
//Check the ask price is not 0 and if it is use the previous weekly close
if ($askprice == 0){
  $askprice = $formatted_jsonData["quoteResponse"]["result"][0]["regularMarketPrice"];
}
$pctChange = round($formatted_jsonData["quoteResponse"]["result"][0]["regularMarketChangePercent"],2);
$id = $row["id"];
}
$dividendfinal = json_encode($dividenddata);
$formatted_stockname = json_encode($stocknames);
//Prepare the JSON File used to chart the sector information
mysqli_close($link);
}
}


?>
 
<!DOCTYPE html>
<html lang="en">
<head>
<script src="https://uicdn.toast.com/tui.code-snippet/v1.5.2/tui-code-snippet.min.js"></script>

<script src="https://uicdn.toast.com/tui.time-picker/latest/tui-time-picker.min.js"></script>

<script src="https://uicdn.toast.com/tui.date-picker/latest/tui-date-picker.min.js"></script>

<script src="https://uicdn.toast.com/tui-calendar/latest/tui-calendar.js"></script>
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
				</button> <a class="navbar-brand" href="#" onclick="openNav()">☰ MobileTicker Web</a>
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="navbar-nav">
						<li class="nav-item">
							 <a class="nav-link" href="../welcome.php">Portfolio</a>
						</li>
						<li class="nav-item">
							 <a class="nav-link" href="performance.php">Performance<span class="sr-only">(current)</span></a>
						</li>
            <li class="nav-item active">
            <a class="nav-link" href="#">Calendar</a>
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
  <table width="100%">
    <tr>
      <td>Current Week Selected:
      <p id="currentweek">
      </p>

      </td>
      <td>  <div align="right">
<a class="btn btn-primary" onclick="previous_date();"><</a>
<a class="btn btn-primary" onclick="next_date();">Today</a>
<a class="btn btn-primary" onclick="next_date();">></a>
</div></td>
</tr>
</table>

<div id="calendar" style="height: 800px;"></div>
</div>
<?php obtain_dividendinfo() ?>
<body>


<script>
//Obtain the dividend data from PHP
var test = JSON.parse('<?= $dividendfinal; ?>');
var names = JSON.parse(`<?= $formatted_stockname; ?>`);

function previous_date(){
  calendar.prev();
  document.getElementById("currentweek").innerHTML = calendar.getDate().toUTCString();
}

function next_date(){
  calendar.next();
  document.getElementById("currentweek").innerHTML = calendar.getDate().toUTCString();
}

function today(){
  calendar.today();
  document.getElementById("currentweek").innerHTML = calendar.getDate().toUTCString();
}

//var Calendar = require('tui-calendar'); /* CommonJS */
var calendar = new tui.Calendar(document.getElementById('calendar'), {
    defaultView: 'week',
    taskView: true,    // Can be also ['milestone', 'task']
    scheduleView: 'allday',  // Can be also ['allday', 'time']
    isReadOnly: true,
    template: {
        milestone: function(schedule) {
            return '<span style="color:red;"><i class="fa fa-flag"></i> ' + schedule.title + '</span>';
        },
        milestoneTitle: function() {
            return 'Milestone';
        },
        task: function(schedule) {
            return '&nbsp;&nbsp;#' + schedule.title;
        },
        taskTitle: function() {
            return '<label><input type="checkbox" />Task</label>';
        },
        allday: function(schedule) {
            return schedule.title + ' <i class="fa fa-refresh"></i>';
        },
        alldayTitle: function() {
            return 'All Day';
        },
        time: function(schedule) {
            return schedule.title + ' <i class="fa fa-refresh"></i>' + schedule.start;
        }
    },
    month: {
        daynames: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
        startDayOfWeek: 0,
        narrowWeekend: true
    },
    week: {
        daynames: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
        startDayOfWeek: 0,
        narrowWeekend: true
    }
});
//Change the view of the calendar to a month view and display the targetted date
calendar.changeView('month', true);
document.getElementById("currentweek").innerHTML = calendar.getDate().toUTCString();

//Populate the Calendar with the dividend dates from PHP
var calendar_it = 1;
for (let i = 0; i<test.length; i++){
  for(let j=1; j<test[i].length; j++){
    var $date = test[i][j][0];
    var name = names[i];
    console.log(name);
    console.log($date);
    calendar.createSchedules([
    {
        id: calendar_it,
        calendarId: '1',
        title: `Dividend from ${name}`,
        category: 'allday',
        dueDateClass: '',
        allday: 'true',
        start: $date
    },
]);
  calendar_it += 1;
  }

}

  </script>
</body>



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