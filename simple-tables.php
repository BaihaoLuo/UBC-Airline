<!--Test Oracle file for UBC CPSC304 2011 Winter Term 2
  Created by Jiemin Zhang
  Modified by Simona Radu
  This file shows the very basics of how to execute PHP commands
  on Oracle.  
  specifically, it will drop a table, create a table, insert values
  update values, and then query for values
 
  IF YOU HAVE A TABLE CALLED "tab1" IT WILL BE DESTROYED

  The script assumes you already have a server set up
  All OCI commands are commands to the Oracle libraries
  To get the file to work, you must place it somewhere where your
  Apache server can run it, and you must rename it to have a ".php"
  extension.  You must also change the username and password on the 
  OCILogon below to be your ORACLE username and password -->

<p>To reset and fill the table:</p>
<form method="POST" action="simple-tables.php">
   
<p><input type="submit" value="Init" name="init"></p>
</form>

<form method="POST" action="simple-tables.php">
   
<p><input type="submit" value="Print" name="print"></p>
</form>

<?php

//this tells the system that it's no longer just parsing 
//html; it's now parsing PHP
include 'dbLoginCredentials.php';

$success = True; //keep track of errors so it redirects the page only if there are no errors
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

function executeBoundSQL($cmdstr, $list) {
	/* Sometimes a same statement will be excuted for severl times, only
	 the value of variables need to be changed.
	 In this case you don't need to create the statement several times; 
	 using bind variables can make the statement be shared and just 
	 parsed once. This is also very useful in protecting against SQL injection. See example code below for       how this functions is used */

	global $db_conn, $success;
	$statement = OCIParse($db_conn, $cmdstr);

	if (!$statement) {
		echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
		$e = OCI_Error($db_conn);
		echo htmlentities($e['message']);
		$success = False;
	}

	foreach ($list as $tuple) {
		foreach ($tuple as $bind => $val) {
			//echo $val;
			//echo "<br>".$bind."<br>";
			OCIBindByName($statement, $bind, $val);
			unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype

		}
		$r = OCIExecute($statement, OCI_DEFAULT);
		if (!$r) {
			echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
			$e = OCI_Error($statement); // For OCIExecute errors pass the statementhandle
			echo htmlentities($e['message']);
			echo "<br>";
			$success = False;
		}
	}

}

function printResult($result) { //prints results from a select statement
	echo "<br>Got data from table tab1:<br>";
	echo "<table>";
	echo "<tr><th>ID</th><th>Name</th></tr>";

	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		echo "<tr><td>" . $row["NID"] . "</td><td>" . $row["NAME"] . "</td></tr>"; //or just use "echo $row[0]" 
	}
	echo "</table>";

}

// Connect Oracle...
if ($db_conn) {

	if (array_key_exists('init', $_POST)) {
		// Drop old table...
		echo "<br> Setting up tables <br>";
		executePlainSQL("Drop table customer");
		executePlainSQL("Drop table Ticket_refersto_has");
		executePlainSQL("Drop table Flight_schedule");

		// Create new table...
		echo "<br> creating new table <br>";		
		executePlainSQL("create table customer (passportID varchar2(8), airmile number, birthday date, name varchar2(30), email varchar2(30), addr varchar2(30), phoneNum number)");
		executePlainSQL("create table Ticket_refersto_has (ticketNum varchar(13),mealPlan varchar(10), class varchar(20), seatNum varchar(3), gateNum varchar(3), flightNum varchar2(10), passportID varchar2(8))");
		executePlainSQL("create table Flight_schedule (flightNum varchar2(10), airline varchar2(30), arrivalAirport varchar(3), departrueAirport varchar2(3), takeoffTime TIMESTAMP, arrivalTime TIMESTAMP, delayTime number)");
		
		
		executePlainSQL("insert into customer (passportID, airmile, birthday, name, email, addr, phoneNum) values ('AB000509', 1050, TO_DATE('02/01/1995', 'DD/MM/YYYY') , 'John', 'john@gmail.com', '#100 2nd Ave', 6048858987)");
		executePlainSQL("insert into customer (passportID, airmile, birthday, name, email, addr, phoneNum) values ('SE012635', 5202, TO_DATE('14/03/1975', 'DD/MM/YYYY') , 'Josh', 'josh@gmail.com', '#203 10th Ave', 6045832145)");
		executePlainSQL("insert into customer (passportID, airmile, birthday, name, email, addr, phoneNum) values ('BC452698', 253, TO_DATE('05/08/1954', 'DD/MM/YYYY') , 'Stan', 'stan@gmail.com', '#412 41st Ave', 4588759658)");
		executePlainSQL("insert into Ticket_refersto_has (ticketNum, mealPlan, class, seatNum, gateNum, flightNum, passportID) values ('8382177546344', 'None', 'Business', '13C', 'B11', 'WJ 7291', 'SE012635')");
		executePlainSQL("insert into Ticket_refersto_has (ticketNum, mealPlan, class, seatNum, gateNum, flightNum, passportID) values ('8382135654654', 'None', 'Business', '33C', 'B11', 'WJ 7291', 'SE012635')");
		executePlainSQL("insert into Flight_schedule (flightNum, airline, arrivalAirport, departrueAirport, takeoffTime, arrivalTime, delayTime) values ('WJ 7291', 'Westjet', 'YVR', 'YEG', TO_DATE('2016/06/15 07:45:00', 'yyyy/mm/dd hh24:mi:ss'), TO_DATE('2016/06/15 10:16:00', 'yyyy/mm/dd hh24:mi:ss'), 15)");
		
		
		OCICommit($db_conn);

	} else 
		if (array_key_exists('print', $_POST)){
			$result = executePlainSQL("select * from customer");
			echo "<br>Got data from table customer:<br>";
			echo "<table>";
			echo "<tr><th>passportID</th><th>name</th></tr>";

			while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
				echo "<tr><td>" . $row["PASSPORTID"] . "</td><td>" . $row["NAME"] . "</td></tr>"; //or just use "echo $row[0]" 
			}
			echo "</table>";
			
			$resultTicket = executePlainSQL("select * from Ticket_refersto_has");
			echo "<br>Got data from table customer:<br>";
			echo "<table>";
			echo "<tr><th>ticketNum</th><th>passportID</th></tr>";

			while ($row = OCI_Fetch_Array($resultTicket, OCI_BOTH)) {
				echo "<tr><td>" . $row["TICKETNUM"] . "</td><td>" . $row["PASSPORTID"] . "</td></tr>"; //or just use "echo $row[0]" 
			}
			echo "</table>";
		
			$resultFlights = executePlainSQL("select * from Flight_schedule");
			echo "<br>Got data from table customer:<br>";
			echo "<table>";
			echo "<tr><th>flightNum</th><th>airline</th></tr>";

			while ($row = OCI_Fetch_Array($resultFlights, OCI_BOTH)) {
				echo "<tr><td>" . $row["FLIGHTNUM"] . "</td><td>" . $row["AIRLINE"] . "</td></tr>"; //or just use "echo $row[0]" 
			}
			echo "</table>";
		
		}
	
} else {
	echo "cannot connect";
	$e = OCI_Error(); // For OCILogon errors pass no handle
	echo htmlentities($e['message']);
}

/* OCILogon() allows you to log onto the Oracle database
     The three arguments are the username, password, and database
     You will need to replace "username" and "password" for this to
     to work. 
     all strings that start with "$" are variables; they are created
     implicitly by appearing on the left hand side of an assignment 
     statement */

/* OCIParse() Prepares Oracle statement for execution
      The two arguments are the connection and SQL query. */
/* OCIExecute() executes a previously parsed statement
      The two arguments are the statement which is a valid OCI
      statement identifier, and the mode. 
      default mode is OCI_COMMIT_ON_SUCCESS. Statement is
      automatically committed after OCIExecute() call when using this
      mode.
      Here we use OCI_DEFAULT. Statement is not committed
      automatically when using this mode */

/* OCI_Fetch_Array() Returns the next row from the result data as an  
     associative or numeric array, or both.
     The two arguments are a valid OCI statement identifier, and an 
     optinal second parameter which can be any combination of the 
     following constants:

     OCI_BOTH - return an array with both associative and numeric 
     indices (the same as OCI_ASSOC + OCI_NUM). This is the default 
     behavior.  
     OCI_ASSOC - return an associative array (as OCI_Fetch_Assoc() 
     works).  
     OCI_NUM - return a numeric array, (as OCI_Fetch_Row() works).  
     OCI_RETURN_NULLS - create empty elements for the NULL fields.  
     OCI_RETURN_LOBS - return the value of a LOB of the descriptor.  
     Default mode is OCI_BOTH.  */
?>
