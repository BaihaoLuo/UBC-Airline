<!DOCTYPE html>
<html> 
<head> 
	<title> UBC Airlines </title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no" />
  <link rel="stylesheet" type="text/css" href="boot.css"> 

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
		


</head>

<body>

<div id=wrapper>
	<div id=header>
		
		<div style="    text-align: center;">
			<?php
			date_default_timezone_set("America/Vancouver");
			echo date("F j, Y, g:i a") . "<br>";
			echo date("l");
			?>
		</div>
		
		<a href="#" class=menu style="float: right; display:none;"> Home </a>
	</div>
</div>	
<div id=main>
	<div id=homebuttons>
		<div id=mobileTitle>
		<!-- <center><h1 style="color:white;">UBC AirLine</h1></center> -->
		<center><img src = 'image/mock-logo-reverse.png'></center>
		<br><br>
		</div>
        <div class="col-md-4 text-center"><a href="customer.php" class=homepage> View Account Info </a><br></div>
        <div class="col-md-4 text-center"><a href="tickets.php" class=homepage> Ticket Check-In </a></div>
        <div class="col-md-4 text-center"><a href="flights.php" class=homepage> Check Flights </a></div>
        <div id=mobileTitle>
		<center><div class="col-md-4 text-center"><a href="employee.php" class=homepage> Employee Login </a></div></center>
		</div>
	</div>
</div>
<div id=wrapper>
	<div id=footer>
		<a href="employee.php" class=menu> Employee Login </a>
	</div>
</div>
<body>


</html>

