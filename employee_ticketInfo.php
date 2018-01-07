<!DOCTYPE html>
<html> 
<head> 
	<title> UBC Airlines - Employee_TicketInformation </title>

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
	<h1 style="color:white;">Ticket Information</h1>

<?php 
	
	include 'dbLoginCredentials.php';

	echo "<div id=flight>";

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

	function printticketInfo($ticketresult){
		echo "<table id=ticketList style='width:100%;'>";
		echo "<tr><th>Ticket Number</th>
		<th>Passport ID</th>
		<th>Flight Number</th>
		<th>Class</th>
		<th>Gate Number</th>
		<th>Seat Number</th>
		<th>Meal Plan</th>
		<th></th></tr>";
		echo implode($ticketresult);
		echo "</table>";
	}

/*			if (array_key_exists('ticketInfo', $_POST))
			{ */
	function printUI(){
		$ticketresult = executePlainSQL("select * from Ticket_refersto_has order by ticketNum");
	
		while ($row = OCI_Fetch_Array($ticketresult, OCI_BOTH)) 
			{
			$results[] ="<tr><td>{$row['TICKETNUM']}</td>
			<td>{$row['PASSPORTID']}</td>
			<td>{$row['FLIGHTNUM']}</td>
			<td>{$row['CLASS']}</td>
			<td>{$row['GATENUM']}</td>
			<td>{$row['SEATNUM']}</td>
			<td>{$row['MEALPLAN']}</td>
			<td><form method='POST' action='e_ticketInfo_modify.php'>
			<input style='display:none;' type='text' name='ticketNum' value='{$row['TICKETNUM']}'>
			<input style='display:none;' type='text' name='SIN' value='{$_POST['SIN']}'>
			<input type='submit' value='Modify Ticket' name='modifyTicket'>
			</form></td>
			</tr>";
		}

		if(empty($results))
		{
			echo "<h2> No Tickets Information.</h2>";
		}

		else
		{
			//print $_POST['SIN'];
			$ticketNumModifiedByAll = executePlainSQL("Select t.ticketNum
				From Ticket_refersto_has t
				Where NOT EXISTS
				(select *
				From Employee2 e
				Where NOT EXISTS 
				(select *
				From Modification m
				Where m.SIN = e.SIN AND m.ticketNum = t.ticketNum))");

			while ($row = OCI_Fetch_Array($ticketNumModifiedByAll, OCI_BOTH)) {
			$ticketNumModifiedByAllresults[] = "<p>The ticketNum which is modified by all employees:&nbsp&nbsp&nbsp{$row['TICKETNUM']}<p>"; 
			}

			if(empty($ticketNumModifiedByAllresults)){
				$ticketNumModifiedByAllresults[] = "<p>No Ticket is modified by all employees</p>";
			}

			// if(!empty($invalidCancelTicketNum)){
			// 	$cancelTicketBlock[] = "<form method='POST' action='employee_ticketInfo.php'>
			// 							<input style='display:none;' type='text' name='SIN' value='{$_POST['SIN']}'>
			// 							<input stype='text' name='ticketNumBeCanceled'>
			// 							<input type='submit' value='Cancel Ticket' name='cancelTicket'>
			// 							</form>";
			// }else{
			// 	$cancelTicketBlock[] = "<form method='POST' action='employee_ticketInfo.php'>
			// 							<input style='display:none;' type='text' name='SIN' value='{$_POST['SIN']}'>
			// 							<input stype='text' name='ticketNumBeCanceled'>
			// 							<input type='submit' value='Cancel Ticket' name='cancelTicket'>
			// 							</form>";
			// }

			$cancelTicketBlock[] = "<form method='POST' action='employee_ticketInfo.php'>
										<input style='display:none;' type='text' name='SIN' value='{$_POST['SIN']}'>
										<input stype='text' name='ticketNumBeCanceled'>
										<input type='submit' value='Cancel Ticket' name='cancelTicket'>
										</form>";
			
			// echo "<table id=ticketList>";
			echo implode($ticketNumModifiedByAllresults);
			echo implode($cancelTicketBlock);
			echo '<br>';
			//echo '<p>&nbsp</p>';
			// echo "</table>";
			printticketInfo($results);
		}
	}
	//Sin check
	if(!empty($_POST['SIN'])){

		//Main UI
		if(array_key_exists('cancelTicket', $_POST)){
			//print $_POST['SIN'];
			// print $_POST['ticketNumBeCanceled'];
			// echo '<br>';
	//		$lusql = "delete From Luggage_belongsto where ticketNum = '9895515498151'";
			
	//		 $deleteLoungeSQL = "delete from Lounge where Location = 'Room 109'";
			 // $deleteTicketSQL = "Delete From Ticket_refersto_has 
			 // Where ticketNum = '{$_POST['ticketNumBeCanceled']}'";
			$checkTicketSQL = "select * from Ticket_refersto_has Where ticketNum = '{$_POST['ticketNumBeCanceled']}'";
			$checkTicket = executePlainSQL($checkTicketSQL);
			while ($row = OCI_Fetch_Array($checkTicket, OCI_BOTH)) 
			{
			$checkTicketResults[] ="<tr><td>{$row['TICKETNUM']}</td>
			<td>{$row['PASSPORTID']}</td>
			<td>{$row['FLIGHTNUM']}</td>
			<td>{$row['CLASS']}</td>
			<td>{$row['GATENUM']}</td>
			<td>{$row['SEATNUM']}</td>
			<td>{$row['MEALPLAN']}</td>
			</tr>";
			}

			if(empty($checkTicketResults))
			{
				//echo "<script>alert('Invalid Ticket Number');</script>";
				//echo '<p><font size="2" color="red">Invalid Ticket Number</font></p>';
			print  "<div class='alert' id='upgrade_box'>
					<p> Invalid Ticket Number</p>
			     	<input type='submit' value='Ok' onclick='hide()'>
			   		</div>";
			}else{
				//echo '<p>&nbsp</p>';
				//echo "<h2> should delete Tickets</h2>";
				$deleteTicketSQL = "Delete From Ticket_refersto_has 
				 Where ticketNum = '{$_POST['ticketNumBeCanceled']}'";
				$modificationInsertSQL = "INSERT INTO Modification VALUES('{$_POST['SIN']}', CURRENT_TIMESTAMP, '{$_POST['ticketNumBeCanceled']}')";
				//print $modificationInsertSQL;
				
	        	executeNonSelectSQL($deleteTicketSQL);
	        	executeNonSelectSQL($modificationInsertSQL);
			}
			printUI();
			
		}else{
			//print $_POST['SIN'];
			//echo '<p>&nbsp</p>';
			printUI();
		}
	}else{
		print "No SIN";
	}
			
		echo "</div>";
?>

<script>
function hide() {
    document.getElementById("upgrade_box").style.display = 'none';
}
</script>

	</div>

</div>

<body>


</html>




