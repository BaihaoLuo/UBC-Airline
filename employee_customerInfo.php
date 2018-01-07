<!DOCTYPE html>
<html> 
<head> 
	<title> UBC Airlines - Employee_CustomerInfo </title>

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
	<h1 style="color:white;">Customer Information</h1>

<?php 

	include 'dbLoginCredentials.php';

	echo "<div id=employee_cusinfo>";
		$db = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = dbhost.ugrad.cs.ubc.ca)(PORT = 1522)))(CONNECT_DATA=(SID=ug)))";
		if ($conn=OCILogon($dbusername, $dbpassword, $db)) {

		$query = "select * from Customer"; 
		$res = oci_parse($conn,$query); 
		usleep(100); 
		if (oci_execute($res)){ 
		        //print "<TABLE border \"1\">"; 
				print "<TABLE>";
		        $first = 0; 
		        while ($row = @oci_fetch_assoc($res)){ 
		                if (!$first){ 
		                        $first = 1; 
		                        print "<TR><TH>"; 
		                        print implode("</TH><TH>",array_keys($row)); 
		                        print "</TH><TH>";
		                        print "</TH></TR>\n"; 
		                } 
		                print "<TR><TD>"; 
		                print @implode("</TD><TD>",array_values($row)); 
		                print "</TD><TD>";
		                print "<form method='POST' action='e_customerInfo_modify.php'>";
						print "<input style='display:none;' type='text' name='passID' value='{$row['PASSPORTID']}'>";
						print "<input type='submit' value='Modify Customer Info' name='modifyCustomer'>";
						print "</form>";
		                print "</TD></TR>\n"; 
		        } 
		        print "</TABLE>"; 
		}

			  OCILogoff($conn);
		} else {
		  $err = OCIError();
		  echo "Oracle Connect Error " . $err['message'];
		}
				
	echo "</div>";
?>


	</div>

</div>

<body>


</html>

