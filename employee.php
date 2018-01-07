<!DOCTYPE html>
<html> 
<head> 
	<title> UBC Airlines - Employee </title>

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
		
		<a href="index.php" class=menu style="float: right;"> Log Out </a>
	</div>
	
	<div id=main>

<?php 

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
		include 'dbLoginCredentials.php';

		$db = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = dbhost.ugrad.cs.ubc.ca)(PORT = 1522)))(CONNECT_DATA=(SID=ug))    )";
		$db_conn = OCILogon($dbusername, $dbpassword, $db);


		if (array_key_exists('loginCust', $_POST)) {
			// Drop old table...
			$employeeResult = executePlainSQL("select * from Employee1 e1, Employee2 e2 where e1.userName = '{$_POST['userName']}' AND e1.password = '{$_POST['password']}' AND e1.userName = e2.userName");
			
			while ($row = OCI_Fetch_Array($employeeResult, OCI_BOTH)) {
				$results[]="<tr><th>Name </th><td>{$row['NAME']}</td></tr>
				<tr><th>User name</th><td>{$row['USERNAME']}</td></tr>
				<tr><th>SIN</th><td>{$row['SIN']}</td></tr>
				<tr><th>Job postion</th><td>{$row['JOBPOS']}</td></tr>";
				
				// session_start();
				// $_SESSION['testSIN'] = $row['SIN'];
				// if (!is_writable(session_save_path())) {
   	// 			 echo 'Session path "'.session_save_path().'" is not writable for PHP!'; 
				// }
				// session_commit();

				$testSIN =  $row['SIN'];
				//print($testSIN);
			}
			
			if (empty($results)) {
				
					echo '<div class="alert">';
					echo '<p> <font color="red">Invalid credentials </font> <br>';
					echo 'Employee login: </p>';
					echo '<form method="POST" action="#">';
					echo '<p> <input type="text" style="height:18px;font-size:14pt;" placeholder="Username" name="userName" size="24"></p>';
					echo '<p> <input type="password" style="height:18px;font-size:14pt;" placeholder="password" name="password" size="24"></p>';
					echo '<input type="submit" value="login" name="loginCust" >';
					echo '</form>';
					echo '</div>';
				
				}
			else {
				$buttons[]="
				<tr><th>
				<form method='POST' action='employee_customerInfo.php'>
		 		<input style='display:none;' type='text'>
		  		<input type='submit' value='Customer Information' name='customerInfo' class='infoButton'>
		  		</form></th>
				  
				<td>
				<form method='POST' action='employee_ticketInfo.php'>
				<input style='display:none;' type='text' name='SIN' value='{$testSIN}'>
				<input type='submit' value='Ticket Information' name='ticketInfo' class='infoButton'>
				</form></td></tr>
				
				<tr><th>
				<form method='POST' action='employee_flightInfo.php'>
				<input style='display:none;' type='text'>
				<input type='submit' value='Flight Information' name='flightInfo' class='infoButton'>
				</form></th>

				<td>
				<form method='POST' action='modification.php'>
				<input style='display:none;' type='text'>
				<input type='submit' value='Modification History' name='modification' class='infoButton'>
				</form></td></tr>

				<tr><th>
				<form method='POST' action='employee_luggage.php'>
				<input style='display:none;' type='text'>
				<input type='submit' value='Luggage Information' name='luggageInfo' class='infoButton'>
				</form></th>

				<td>
				<form method='POST' action='employee_vip.php'>
				<input style='display:none;' type='text'>
				<input type='submit' value='Vip Infomation' name='vipInfo' class='infoButton'>
				</form></td></tr>";
			
				echo "<div id=customer>";
				//print $_SESSION['SIN'];
				echo "<table>";
				echo implode($results);
				echo "</table>";
				echo "<br>";
				echo "<table>";
				echo implode($buttons);
				echo "</table>";
				echo "</div>";

			}
			

		}
		else {
			echo '<div class="alert">';
			echo 'Employee login: </p>';
			echo '<form method="POST" action="#">';
			echo '<p> <input type="text" style="height:18px;font-size:14pt;" placeholder="Username" name="userName" size="24"></p>';
			echo '<p> <input type="password" style="height:18px;font-size:14pt;" placeholder="password" name="password" size="24"></p>';
			echo '<input type="submit" value="login" name="loginCust" >';
			echo '</form>';
			echo '</div>';
		}
?>

		

	</div>

</div>

<body>


</html>


