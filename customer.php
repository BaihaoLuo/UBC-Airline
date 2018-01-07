<!DOCTYPE html>
<html> 
<head> 
	<title> UBC Airlines - Customer Info </title>

	<link rel="stylesheet" type="text/css" href="main.css">


</head>

<body> 

<div id=wrapper>
	<div id=header>
		
		<div style="text-align: center;">
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

function printInfo($results){ // prints out the customers info
		echo "<table>";
		echo implode($results);
		echo "</table>";

}

function printFilterOptions(){
		echo "<table style='width:70%;'>";
		echo "<tr>";
		echo "<form method='POST' action='#'>";
		echo "<input style='display:none;' type='text' name='passID' value='{$_POST['passID']}'>";
				
		echo "<th><p> Filter Tickets </p></th>";
		echo "<td><p> Start Date: <input type='date' value='YYYY-MM-DD' name='start' /></p></td>";
		echo "<td><p> End Date: <input type='date' value='YYYY-MM-DD' name='end' /></p></td>";
		echo "<td><input type='submit' value='filter' name='filterTickets'></td>";				
		echo "</form>";
				
		echo "</tr>";
		echo "</table>";
}

function printTickets($ticketresults){

		echo "<table id=ticketList style='width:100%;'>";
		echo "<tr><th>Ticket Number</th><th>Flight Number</th><th>Departrue Airport</th><th>Arrival Airport</th><th>Departrue Time</th><th>Arrival Time</th><th></th><tr>";
		echo implode($ticketresults);
		echo "</table>";
				
}

function printLogin($error){
	echo '<div class="alert">';
	if (!empty($error)) echo '<p> <font color="red">'.$error.'</font> <br>';
	echo '<p> Enter Passport ID: </p>';
	echo '<form method="POST" action="#">';
	echo '<p><input type="text" name="passID" size="24">';
	echo '<input type="submit" value="login" name="loginCust" >';
	echo '</p>';
	echo '</form>';
	echo '</div>';

}

		$db = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = dbhost.ugrad.cs.ubc.ca)(PORT = 1522)))(CONNECT_DATA=(SID=ug))    )";
		$db_conn = OCILogon($dbusername, $dbpassword, $db);


		if (array_key_exists('loginCust', $_POST) || array_key_exists('filterTickets', $_POST)) {
			
			$customerresult = executePlainSQL("select * from Customer where PASSPORTID = '{$_POST['passID']}'");
			

			while ($row = OCI_Fetch_Array($customerresult, OCI_BOTH)) {
				$results[]="<tr><th>Passport ID</th><td>{$row['PASSPORTID']}</td></tr><tr><th>Name</th><td>{$row['NAME']}</td></tr><tr><th>E-mail</th><td>{$row['EMAIL']}</td></tr><tr><th>Phone Number</th><td>{$row['PHONENUM']}</td></tr><tr><th>Address</th><td>{$row['ADDR']}</td></tr><tr><th>Birthday</th><td>{$row['BIRTHDAY']}</td></tr><tr><th>Air Miles</th><td>{$row['AIRMILE']}</td></tr>"; 
			}
			
			while ($row = OCI_Fetch_Array($vipresult, OCI_BOTH)) {
				$results[]="<tr><th>VIP Lounge</th><td>{$row['LOCATION']}, {$row['AIRPORT']}</td></tr><tr><th>VIP Membership</th><td>{$row['MEMBERSHIP']}</td></tr>"; 
			}
			
			while ($row = OCI_Fetch_Array($specalresult, OCI_BOTH)) {
				$results[]="<tr><th>Special Needs Status:</th><td>{$row['STATUS']}</td></tr>"; 
			}
			
			if (empty($results)) 
			{
					printLogin("Invalid Passport ID");	
			}
			
			else 
			{
			
				//see if customer has reserved a VIP Lounge
				$vipresult = executePlainSQL("Select l.location,l.airport, v.membership From Lounge l, Vip  v Where v.passportID = '{$_POST['passID']}' and l.location = v.location");
				while ($row = OCI_Fetch_Array($vipresult, OCI_BOTH)) {
					$results[]="<tr><th>VIP Lounge</th><td>{$row['LOCATION']}, {$row['AIRPORT']}</td></tr><tr><th>VIP Membership</th><td>{$row['MEMBERSHIP']}</td></tr>"; 
				}
			
				//see if customer has any specal needs
				$specalresult = executePlainSQL("select status from SpecialNeeds s where s.PASSPORTID = '{$_POST['passID']}'");
				while ($row = OCI_Fetch_Array($specalresult, OCI_BOTH)) {
					$results[]="<tr><th>Special Needs Status:</th><td>{$row['STATUS']}</td></tr>"; 
				}
				
				//get summary of traval 
				$airline = executePlainSQL("Select airline From Flight_schedule f, Customer c,Ticket_refersto_has t Where f.flightNum=t.flightNum and t.passportID = c.passportID and c.passportID = '{$_POST['passID']}' Group by airline Having count(*) >= all(Select count(*) From Flight_schedule f1, Customer c1,Ticket_refersto_has t1 Where f1.flightNum=t1.flightNum and t1.passportID = c1.passportID and c1.passportID = '{$_POST['passID']}' Group by airline)");
				while ($row = OCI_Fetch_Array($airline, OCI_BOTH)) {
					$results[]="<tr><th>Most Frequent Airline:</th><td>{$row['AIRLINE']}</td></tr>"; 
				}
				$city = executePlainSQL("Select arrivalAirport From Flight_schedule f,ticket_refersto_has t Where f.flightNum = t.flightNum and t.passportID = '{$_POST['passID']}' Group by arrivalAirport Having count (*) >= all(Select count(*) From Flight_schedule f,ticket_refersto_has t Where f.flightNum = t.flightNum and t.passportID = '{$_POST['passID']}' Group by arrivalAirport)");
				while ($row = OCI_Fetch_Array($city, OCI_BOTH)) {
					$results[]="<tr><th>Most Frequent Destination:</th><td>{$row['ARRIVALAIRPORT']}</td></tr>"; 
				}

			
				echo "<div id=customer>";
				printInfo($results);
				
				echo "<h2> Ticket History </h2>";

				printFilterOptions();
				
							
				if (array_key_exists('filterTickets', $_POST)) // user is filtering ticket search
				{
				
					if(!empty($_POST['start']) && !empty($_POST['end'])) //user entered both start and end time to filter
					{
						$ticketresult = executePlainSQL("select * from Ticket_refersto_has t, Flight_schedule f where PASSPORTID = '{$_POST['passID']}' and t.flightNum = f.flightNum and f.takeoffTime > '{$_POST['start']}' and f.takeoffTime < '{$_POST['end']}'");
					}
					
					elseif(!empty($_POST['end'])) //user entered only end time to filter
					{
						$ticketresult = executePlainSQL("select * from Ticket_refersto_has t, Flight_schedule f where PASSPORTID = '{$_POST['passID']}' and t.flightNum = f.flightNum and f.takeoffTime < '{$_POST['end']}'");
					}
					
					elseif(!empty($_POST['start'])) //user entered only start time to filter
					{ 
						$ticketresult = executePlainSQL("select * from Ticket_refersto_has t, Flight_schedule f where PASSPORTID = '{$_POST['passID']}' and t.flightNum = f.flightNum and f.takeoffTime > '{$_POST['start']}'");
					}
					
					else //user entered nothing to filter
					{
						$ticketresult = executePlainSQL("select * from Ticket_refersto_has t, Flight_schedule f where PASSPORTID = '{$_POST['passID']}' and t.flightNum = f.flightNum");
					}
				}
				else // user just logged in
				{
					$ticketresult = executePlainSQL("select * from Ticket_refersto_has t, Flight_schedule f where PASSPORTID = '{$_POST['passID']}' and t.flightNum = f.flightNum");
				}	
				
				while ($row = OCI_Fetch_Array($ticketresult, OCI_BOTH)) {
					$takeofftime = date( 'm/d/y g:i A', strtotime( $row['TAKEOFFTIME'] ) );
					$arrivaltime = date( 'm/d/y g:i A', strtotime( $row['ARRIVALTIME'] ) );
					$ticketresults[]="<tr><td>{$row['TICKETNUM']}</td><td>{$row['FLIGHTNUM']}</td><td>{$row['DEPARTUREAIRPORT']}</td><td>{$row['ARRIVALAIRPORT']}</td><td>$takeofftime</td><td>$arrivaltime</td><td><form method='POST' action='tickets.php'><input style='display:none;' type='text' name='ticketNum' value='{$row['TICKETNUM']}'><input type='submit' value='View Ticket' name='viewTicket'></form></td></tr>"; 
				}
				
				if(!empty($ticketresults))
				{
					printTickets($ticketresults);
				}
				else 
				{
					echo "<p> No Tickets Found </p>";
				}
				echo "</div>";
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
