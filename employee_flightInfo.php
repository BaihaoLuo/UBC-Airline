<!DOCTYPE html>
<html> 
<head> 
	<title> UBC Airlines - Employee_FlightInformation </title>

	<link rel="stylesheet" type="text/css" href="main.css">


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
		
		<a href="index.php" class=menu style="float: right;"> Log Out </a>
	</div>
	
	<div id=main>
	<h1 style="color:white;">Flight Information</h1>

<?php 
	
	include 'dbLoginCredentials.php';


	$db = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = dbhost.ugrad.cs.ubc.ca)(PORT = 1522)))(CONNECT_DATA=(SID=ug))    )";
	$db_conn = OCILogon($dbusername, $dbpassword, $db);

	function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
		//echo "<br>running ".$cmdstr."<br>";
		global $db_conn, $success;
		$statement = OCIParse($db_conn, $cmdstr); //There is a set of comments at the end of the file that describe some of the OCI specific functions and how they work

		if (!$statement) {
			echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
			$e = OCI_Error($db_conn); // For OCIParse errors pass the       
			// connection handle
			echo htmlentities($e['message']);
			$success = False;
		}

		$r = OCIExecute($statement, OCI_DEFAULT);
		if (!$r) {
			echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
			$e = oci_error($statement); // For OCIExecute errors pass the statementhandle
			echo htmlentities($e['message']);
			$success = False;
		} else {
	
		}
		return $statement;

	}

    function executeNonSelectSQL($sql){
        global $dbusername, $dbpassword, $db;
        if ($conn=OCILogon($dbusername, $dbpassword, $db)) {
            $res = oci_parse($conn,$sql); 
            oci_execute($res);
            OCILogoff($conn);
            } else {
              $err = OCIError();
              echo "Oracle Connect Error " . $err['message'];
        }
    }

	function printticketInfo($flightresult){

		echo "<table id=ticketList style='width:100%;'>";
		//echo "<table id=flight>";								
		echo "<tr><th>Flight Number</th>
		<th>Airline</th>
		<th>Arrival Airport</th>
		<th>Departure Airport</th>
		<th>Takeoff Time</th>
		<th>Arrival Time</th>
		<th>Delay Time</th>
		<th>Passengers carried</th></tr>";
		echo implode($flightresult);
		echo "</table>";
	}
	
	function printPAA($result2){
		echo "<table>";
		echo "<th>Most Popular Arrival Airport:</th>";
		echo implode($result2);
		echo "</table>";
	}

	function printPDA($result1){
		echo "<table>";
		echo "<th>Most Popular Departure Airport:</th>";
		echo implode($result1);
		echo "</table>";
	}


		
	$flightresult = executePlainSQL("select f.flightNum, f.airLine, f.arrivalAirport, f.departureAirport, f.takeOffTime, f.arrivalTime, f.delayTime, COUNT(*) AS ppl
		from Ticket_refersto_has t, Flight_schedule f
		where t.flightNum = f.flightNum
		group by f.flightNum, f.airLine, f.arrivalAirport, f.departureAirport, f.takeOffTime, f.arrivalTime, f.delayTime");
	$MPA = executePlainSQL("Select  arrivalAirport From Flight_schedule Group by  arrivalAirport Having count (*) >= all(Select count(*) From Flight_schedule Group by  arrivalAirport)");
	$MPD = executePlainSQL("Select departureAirport From Flight_schedule Group by departureAirport Having count (*) >= all(Select count(*) From Flight_schedule Group by departureAirport)");

	while ($row = OCI_Fetch_Array($MPD, OCI_BOTH)){
		$result1[]="<tr><td>{$row['DEPARTUREAIRPORT']}</td></tr>";
	}


	while ($row = OCI_Fetch_Array($MPA, OCI_BOTH)){
		$result2[]="<tr><td>{$row['ARRIVALAIRPORT']}</td></tr>";
	}

	echo "<div id=customer style='background-image:none'>";
	printPDA($result1);
	printPAA($result2);
		//printInfo($result);
	echo "<p>Find the flight Number for which its customers' average airmile is the min/max over all flights</p>";
	if(array_key_exists('findMax', $_POST)){
		//echo "<p>Find Max</p>";
		$dropTE = executeNonSelectSQL("drop view te");
		$createTE = executePlainSQL("Create View te AS
					Select f.flightNum, AVG(c.airmile) AS avgairmile 
					From Flight_schedule f, Customer c, Ticket_refersto_has t
					Where f.flightNum = t.flightNum and t.passportID = c.passportID
					Group by f.flightNum");
		$maxAvgResult = executePlainSQL("
					Select te.flightNum, te.avgairmile
					From te
					Where te.avgairmile = (Select MAX(te.avgairmile)
					From te)");
		//$dropTE = executePlainSQL("drop view te");
		while ($row = OCI_Fetch_Array($maxAvgResult)){
			$maxAvg[] = "<p>  Flight num: {$row['FLIGHTNUM']} MaxAvg: {$row['AVGAIRMILE']}</p>";
		}
		echo implode($maxAvg);
	}else if(array_key_exists('findMin', $_POST)){
		//echo "<p>Find Min</p>";
		$dropTE = executeNonSelectSQL("drop view te");
		$createTE = executePlainSQL("Create View te AS
					Select f.flightNum, AVG(c.airmile) AS avgairmile 
					From Flight_schedule f, Customer c, Ticket_refersto_has t
					Where f.flightNum = t.flightNum and t.passportID = c.passportID
					Group by f.flightNum");
		$minAvgResult = executePlainSQL("
					Select te.flightNum, te.avgairmile
					From te
					Where te.avgairmile = (Select MIN(te.avgairmile)
					From te)");
		//$dropTE = executePlainSQL("drop view te");
		while ($row = OCI_Fetch_Array($minAvgResult)){
			$minAvg[] = "<p>  Flight num: {$row['FLIGHTNUM']} MinAvg: {$row['AVGAIRMILE']}</p>";
		}
		echo implode($minAvg);
	}else{
		echo "<p>&nbsp</p>";
	}

	$buttons[] = "<form method='POST' action='employee_flightInfo.php'>
		 		<input type='submit' value='Max' name='findMax'>
		  		<input type='submit' value='Min' name='findMin'>
		  		</form>";

	echo implode($buttons);



	echo "<h2> Flight Information </h2>";

	while ($row = OCI_Fetch_Array($flightresult, OCI_BOTH)) 
		{
		$results[] ="<tr><td>{$row['FLIGHTNUM']}</td>
		<td>{$row['AIRLINE']}</td>
		<td>{$row['ARRIVALAIRPORT']}</td>
		<td>{$row['DEPARTUREAIRPORT']}</td>
		<td>{$row['TAKEOFFTIME']}</td>
		<td>{$row['ARRIVALTIME']}</td>
		<td>{$row['DELAYTIME']}</td>
		<td>{$row['PPL']}</td></tr>";
	}

	if(empty($results))
	{
		echo "<h2> No Flights Information.</h2>";
	}

	else
	{	
		printticketInfo($results);

	}

	echo "</div>";
			
			
	
?>


	</div>

</div>

<body>


</html>


