<!DOCTYPE html>
<html> 
<head> 
	<title> UBC Airlines - Luggage </title>

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

function printLuggage($luggageresult){

        echo "<table id=ticketList style='width:100%;'>";
		echo "<tr><th>Weight</th><th>Luggage ID</th><th>Fee</th></tr>";
		echo implode($luggageresult);
		echo "</table>";
}

function debug_to_console( $data ) {
    $output = $data;
    if ( is_array( $output ) )
        $output = implode( ',', $output);

    echo "<script>console.log( 'Debug Objects: " . $output . "' );</script>";
}

		include 'dbLoginCredentials.php';

		$db = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = dbhost.ugrad.cs.ubc.ca)(PORT = 1522)))(CONNECT_DATA=(SID=ug))    )";
		$db_conn = OCILogon($dbusername, $dbpassword, $db);
				
			if (array_key_exists('viewLuggage', $_POST))
			{
				
				$luggageresult = executePlainSQL("select weight,LID,fee from Luggage_belongsto l,Ticket_refersto_has t where l.ticketNum= t.ticketNum and t.ticketNum = '{$_POST['ticketNum']}' ");
				//print_r("select weight,LID,fee from Luggage_belongsto l,Ticket_refersto_has t where l.ticketNum= t.ticketNum and t.ticketNum = '{$_POST['ticketNum']}' ");
			
				while ($row = OCI_Fetch_Array($luggageresult, OCI_BOTH)) {
					$results[]="<tr><td>{$row['WEIGHT']}</td>
                    <td>{$row['LID']}</td>
                    <td>{$row['FEE']}</td></tr>";
				}


				if (empty($results)) 
				{
					 echo "<h2> There is no luggage for your flight. </h2>";	
				}
				else
				{
                    printLuggage($results);
					
					
				}
				
			} 


				echo "</div>";		
		?>	

<body>


</html>