<!DOCTYPE html>
<html> 
<head> 
	<title> UBC Airlines - Ticket </title>

	<link rel="stylesheet" type="text/css" href="main.css">

	<style>
	
	@media print {

		#header {display:none;}
		.ticketButton {display:none;}
	}

	</style>

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

			function printLogin($error){
				
				echo '<div class="alert">';
				if (!empty($error)) echo '<p> <font color="red">'.$error.'</font> <br>';
				echo '<p> Enter Ticket Number: </p>';
				echo '<form method="POST" action="#">';
				echo '<p><input type="text" name="ticketNum" size="24">';
				echo '<input type="submit" value="Find" name="viewTicket" >';
				echo '</p>';
				echo '</form>';
				echo '</div>';
					
			}

				
			if (array_key_exists('viewTicket', $_POST))
			{
				
				$ticketresult = executePlainSQL("Select * From Ticket_refersto_has t, Customer c, Flight_schedule f Where t.ticketNum = '{$_POST['ticketNum']}' and t.passportID = c.passportID and t.flightNum = f.flightNum");
			
				while ($row = OCI_Fetch_Array($ticketresult, OCI_BOTH)) {
					$takeofftime = date( 'g:i A', strtotime( $row['TAKEOFFTIME'] ) );
					$takeoffdate = date( 'm/d/y', strtotime( $row['TAKEOFFTIME'] ) );
					$boardingtime = date( 'g:i A', strtotime( $row['TAKEOFFTIME'] ) - 15*60 );
				
					$results[]="<table style='width:68%; display: inline-block; border-right-width:5px; border-right-style:dashed; border-spacing:1em;'>";
					$results[]="<tr><td>Boarding Pass</td><td style='font-size: x-large;'>{$row['CLASS']}</td><td colspan='2'></td></tr>";
					$results[]="<tr style='font-size: small;'><td colspan='4'>Name</td></tr>";
					$results[]="<tr><td colspan='4'>{$row['NAME']}</td></tr>";
					$results[]="<tr style='font-size: small;'><td>From</td><td>Flight</td><td>Date</td><td>Time</td></tr>";
					$results[]="<tr style='font-size: large;'><td>{$row['DEPARTUREAIRPORT']}</td><td>{$row['FLIGHTNUM']}</td><td>$takeoffdate</td><td>$takeofftime</td></tr>";
					$results[]="<tr style='font-size: small;'><td colspan='4'>To</td></tr>";
					$results[]="<tr style='font-size: large;'><td colspan='4'>{$row['ARRIVALAIRPORT']}</td></tr>";
					$results[]="<tr style='font-size: small;'><td style='width:17%'>Gate</td><td style='width:17%'>Boarding Time</td><td style='width:17%'>Seat</td><td style='width:17%'>Meal</td></tr>";
					$results[]="<tr style='font-size: x-large;'><td>{$row['GATENUM']}</td><td>$boardingtime</td><td>{$row['SEATNUM']}</td><td>{$row['MEALPLAN']}</td></tr>";
					$results[]="<tr><td colspan='3'></td><td>{$row['TICKETNUM']}</td></tr>";
					$results[]="<tr style='font-size: small;'><td colspan='4'>Please be at the Boarding Gare 35 Minutes Before Departure</td></tr>";
					$results[]="</table>";

					$results[]="<table style='width:30%; display: inline-block; float: right; border-spacing:1em;'>";
					$results[]="<tr style='text-align: right;'><td colspan='3'><img src='image/mock-logo.png' width='50%'/></td></tr>";
					$results[]="<tr style='font-size: x-large;'><td colspan='3'>{$row['CLASS']}</td></tr>";
					$results[]="<tr><td> From: <b>{$row['DEPARTUREAIRPORT']}</b> </td> <td colspan='2'> To: <b>{$row['ARRIVALAIRPORT']}</b></td></tr>";
					$results[]="<tr><td style='width:33%;'>Flight</td><td style='width:33%;'>Date</td><td style='width:33%;'>Time</td></tr>";
					$results[]="<tr><td>{$row['FLIGHTNUM']}</td><td>$takeoffdate</td><td>$takeofftime</td></tr>";
					$results[]="<tr><td>Seat</td><td>Gate</td><td>Meal</td></tr>";
					$results[]="<tr style='font-size: x-large; font-weight:bold;'><td>{$row['SEATNUM']}</td><td>{$row['GATENUM']}</td><td>{$row['MEALPLAN']}</td></tr>";
					$results[]="<tr><td colspan='3'>{$row['TICKETNUM']}</td></tr>";				
					$results[]="</table>";
					
					// $results[]="<tr><th>Ticket Number</th><td>{$row['TICKETNUM']}</td></tr><tr><th>Meal Plan</th><td>{$row['MEALPLAN']}</td></tr><tr><th>Class</th><td>{$row['CLASS']}</td></tr><tr><th>Seat Number</th><td>{$row['SEATNUM']}</td></tr><tr><th>Gate</th><td>{$row['GATENUM']}</td></tr><tr><th>Passport ID</th><td>{$row['PASSPORTID']}</td></tr>"; 
				}

				if (empty($results)) 
				{
					printLogin("Invalid Ticket Number");	
				}
				else
				{
					echo "<div id=ticket>";
					
					echo implode($results);
					
					echo "</div>";
					
					echo "<a href='javascript:window.print()' class=ticketButton> Print Boarding Pass </a>";
					
					echo "<form method='POST' action='luggage.php' style='display: inline-block;'>";
					echo "<input style='display:none;' type='text' value='{$_POST['ticketNum']}' name='ticketNum' />" ;
					echo "<input type='submit' value='View Luggage' name='viewLuggage' class='ticketButton' />";
					echo "</form>";
					
					
				}
				
			} 
			else
			{
				printLogin();
			}
				
		?>	

	</div>

</div>

<body>


</html>