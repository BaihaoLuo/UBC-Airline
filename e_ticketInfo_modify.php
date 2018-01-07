<!DOCTYPE html>
<html> 
<head> 
	<title> UBC Airlines - TicketModify </title>

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

	include 'dbLoginCredentials.php'; 
	
    $db = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = dbhost.ugrad.cs.ubc.ca)(PORT = 1522)))(CONNECT_DATA=(SID=ug)))";
    $thisTicketNum = $_POST['ticketNum'];
    //print $thisTicketNum;
    //function:
    function printTicket(){
        global $dbusername, $dbpassword, $db, $thisTicketNum;
        if ($conn=OCILogon($dbusername, $dbpassword, $db)) {
 
             $query = "select * from Ticket_refersto_has t where t.ticketNum= '{$_POST['ticketNum']}'";
        	
            // $query = "select * from Ticket_refersto_has t where t.ticketNum= '{$_POST['ticketNum']}'";
            //print "<h1>$query</h1>";
            $res = oci_parse($conn,$query); 
            if (oci_execute($res)){ 
                while ($row = OCI_Fetch_Array($res, OCI_BOTH)) {
				$results[]="<tr><th>Ticket Number</th><td>{$row['TICKETNUM']}</td></tr>
				<tr><th>Passport ID</th><td>{$row['PASSPORTID']}</td></tr>
				<tr><th>Flight Number</th><td>{$row['FLIGHTNUM']}</td></tr>
				<tr><th>Class</th><td>{$row['CLASS']}</td></tr>
                <tr><th>Gate Number</th><td>{$row['GATENUM']}</td></tr>
                <tr><th>Seat Number</th><td>{$row['SEATNUM']}</td></tr>
                <tr><th>Meal Plan</th><td>{$row['MEALPLAN']}</td></tr>";
				$classFromRow = $row['CLASS'];
				}

				if (empty($results)) {
	                echo "<h2> You cannot modify this ticket! </h2>";
				}else {
				$mealPlanBlock[] ="<form method='POST' action='e_ticketInfo_modify.php'>
					<input style='display:none;' type='text' name='ticketNum' value='{$_POST['ticketNum']}'>
					<input style='display:none;' type='text' name='SIN' value='{$_POST['SIN']}'>
					<input type='text' name='newMealPlan'>
					<input type='submit' value='Change Meal Plan' name='updateMealPlan'>
					</form>";

				$seatNumBlock[] = "<form method='POST' action='e_ticketInfo_modify.php'>
					<input style='display:none;' type='text' name='ticketNum' value='{$_POST['ticketNum']}'>
					<input style='display:none;' type='text' name='SIN' value='{$_POST['SIN']}'>
					<input type='text' name='newSeatNum'>
					<input type='submit' value='Update Seat Num&nbsp' name='updateSeatNum'>
					</form>";

				$changeClassBlock[] = "<form method='POST' action='e_ticketInfo_modify.php'>
					<input style='display:none;' type='text' name='ticketNum' value='{$_POST['ticketNum']}'>
					<input style='display:none;' type='text' name='SIN' value='{$_POST['SIN']}'>
					<input type='text' name='newClass'>
					<input type='submit' value='    Change Class&nbsp&nbsp&nbsp' name='updateClass'>
					</form>";

				$finishBlock[] = "<form method='POST' action='employee_ticketInfo.php'>
					<input style='display:none;' type='text' name='SIN' value='{$_POST['SIN']}'>
					<input type='submit' value='Finish Modification' name='finishModifyTicket' class='infoButton'>
					</form>";

					echo "<div id=customer>";
					echo "<table>";
					echo implode($results);
					echo "</table>";
					echo "<br>";

					//print $thisTicketNum;
					echo implode($mealPlanBlock);
					echo implode($seatNumBlock);
					echo implode($changeClassBlock);
					echo implode($finishBlock);
					echo "</div>";

				}
            }
            OCILogoff($conn);
        } else {
          $err = OCIError();
          echo "Oracle Connect Error " . $err['message'];
        }

    }

    function executeNonSelectSQL($sql){
        global $dbusername, $dbpassword, $db;
        if ($conn=OCILogon($dbusername, $dbpassword, $db)) {
            $res = oci_parse($conn,$sql); 
            if(oci_execute($res)){
            	// execute the sql
            }else{
				print  "<div class='alert' id='upgrade_box'>
						<p> Invalid SQL </p>
				     	<input type='submit' value='OK' onclick='hide()'>
				   		</div>";
            }
            OCILogoff($conn);
            } else {
              $err = OCIError();
              echo "Oracle Connect Error " . $err['message'];
        }
    }
    	//print "<h1>$_POST['ticketNum']</h1>";

    if(!empty($_POST['SIN'])){

    	if (array_key_exists('modifyTicket', $_POST)) {
			//print $_POST['SIN'];
			printTicket();
		}else if(array_key_exists('updateMealPlan', $_POST)){
			//print $_POST['SIN'];
			executeNonSelectSQL("Update Ticket_refersto_has
			 			Set mealPlan = '{$_POST['newMealPlan']}'
			 			Where ticketNum = '{$_POST['ticketNum']}'");
			executeNonSelectSQL("INSERT INTO Modification VALUES('{$_POST['SIN']}', CURRENT_TIMESTAMP, '{$_POST['ticketNum']}')");
			printTicket();	
		}else if(array_key_exists('updateSeatNum', $_POST)){
			//print $_POST['SIN'];
			executeNonSelectSQL("Update Ticket_refersto_has
			 			Set seatNum = '{$_POST['newSeatNum']}'
			 			Where ticketNum = '{$_POST['ticketNum']}'");
			executeNonSelectSQL("INSERT INTO Modification VALUES('{$_POST['SIN']}', CURRENT_TIMESTAMP, '{$_POST['ticketNum']}')");
			printTicket();	
		}else if(array_key_exists('updateClass', $_POST)){
			//print $_POST['SIN'];
			executeNonSelectSQL("Update Ticket_refersto_has
			 			Set class = '{$_POST['newClass']}'
			 			Where ticketNum = '{$_POST['ticketNum']}'");
			executeNonSelectSQL("INSERT INTO Modification VALUES('{$_POST['SIN']}', CURRENT_TIMESTAMP, '{$_POST['ticketNum']}')");
			printTicket();	
		}else{
			printTicket();
		}
    }else{
			echo "<div id=customer>";
			echo "No SIN Number";
			echo "</div>";
    }
		
		
?>

		

	</div>

</div>
<script>
function hide() {
    document.getElementById("upgrade_box").style.display = 'none';
}
</script>
<body>


</html>

