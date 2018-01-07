<!DOCTYPE html>
<html> 
<head> 
	<title> UBC Airlines - Employee </title>

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
		
		<a href="index.php" class=menu style="float: right;"> Home </a>
	</div>
	
	<div id=main>

<?php 
			echo "<div id=flight>";

			$db = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = dbhost.ugrad.cs.ubc.ca)(PORT = 1522)))(CONNECT_DATA=(SID=ug))    )";
			include 'dbLoginCredentials.php';
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

			function printflight($flightresult){

				echo "<table id=ticketList style='width:100%;'>";
				echo "<tr><th>Flight Number</th><th>Airline</th><th>Arrival Airport</th><th>Departure Airport</th><th>Takeoff Time</th><th>Arrival Time</th><th>Delay Time</th></tr>";
				echo implode($flightresult);
				echo "</table>";
			}

/*			if (array_key_exists('ticketInfo', $_POST))
			{ */
				
				$flightresult = executePlainSQL("select * from Flight_schedule");
			
				while ($row = OCI_Fetch_Array($flightresult, OCI_BOTH)) 
					{
					$results[] ="<tr><td>{$row['FLIGHTNUM']}</td>
					<td>{$row['AIRLINE']}</td>
					<td>{$row['ARRIVALAIRPORT']}</td>
					<td>{$row['DEPARTUREAIRPORT']}</td>
					<td>{$row['TAKEOFFTIME']}</td>
					<td>{$row['ARRIVALTIME']}</td>
					<td>{$row['DELAYTIME']}</td></tr>";
				}

				if(empty($results))
				{
					echo "<h2> No Flights Information.</h2>";
				}

				else
				{
					printflight($results);

				}
			
			
		echo "</div>";
?>


	</div>

</div>

<body>


</html>

