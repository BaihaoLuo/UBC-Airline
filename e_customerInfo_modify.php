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
	
<?php 

	include 'dbLoginCredentials.php';

	echo "<div id=customer>";
		$db = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = dbhost.ugrad.cs.ubc.ca)(PORT = 1522)))(CONNECT_DATA=(SID=ug)))";
		if ($conn=OCILogon($dbusername, $dbpassword, $db)) {

			$query = "select * from Customer where passportID = '{$_POST['passID']}'"; 
			$res = oci_parse($conn,$query); 
			  
			if (oci_execute($res)){ 
		          
		        //print "<TABLE border \"1\">"; 
				print "<TABLE>";
		        
		        while ($row = @oci_fetch_assoc($res)){ 
		              $results[]="<tr><th>Passport ID</th><td>{$row['PASSPORTID']}</td></tr>
		              <tr><th>Name</th><td>{$row['NAME']}</td></tr>
		              <tr><th>E-mail</th><td>{$row['EMAIL']}</td></tr>
		              <tr><th>Phone Number</th><td>{$row['PHONENUM']}</td></tr>
		              <tr><th>Address</th><td>{$row['ADDR']}</td></tr>
		              <tr><th>Birthday</th><td>{$row['BIRTHDAY']}</td></tr>
		              <tr><th>Air Miles</th><td>{$row['AIRMILE']}</td></tr>"; 
		        } 
		        echo implode($results);
		        print "</TABLE>"; 
		        
		        
		        print "<div class='infoButton' onclick=\"show()\">";
		        print "Upgrade Customer to VIP";
		        print "</div>";
		        
		        print "<div class='alert' style='display:none' id='upgrade_box'>";
		        
		        $query = "select * from Vip where passportID = '{$_POST['passID']}'"; 
				$res = oci_parse($conn,$query); 
		        
		        if(oci_execute($res)){
		        	while ($row = @oci_fetch_assoc($res)){ 
		              $vipresults[]="Membership: {$row['MEMBERSHIP']}";
		        	} 
		        	if(!empty($vipresults)){
		        		print "<p> Customer is already a VIP <br>";
		        		echo implode($vipresults);
		        		print "</p>";
		        		print "<input type='submit' value='Okay' onclick='hide()'>";
		        	}
		        	else
		        	{
		        		print "<form method='POST' action='#'>";
		        		print "<p> Enter Type of Membership </p>";
		        		print "<input type='text' name='membership' />";
		        		print "<p> Enter A Lounge to Reserve</p>";
		        		print "<input type='text' name='loungeLocation' />";
		        		//print "<input type='text' name='loungeAirport' size='3'/>";
		        		print "<input type='text' name='passID' value='{$_POST['passID']}' style='display:none'>";
		        		print "<br><input type='submit' value='Upgrade' name='upgrade'>";
		        		print "</from>";
		        		print "&nbsp&nbsp<input type='submit' value='Cancel' onclick='hide()'>";
		        		print "</div>";
		        	
		        	}
		        }
		        
		        
			}
			
			if (array_key_exists('upgrade', $_POST)){
				// $query = "INSERT INTO VIP1 VALUES('{$_POST['loungeLocation']}','{$_POST['loungeAirport']}')";
				// $res = oci_parse($conn,$query); // we don't check if this succeeded since we may be attempting to insert a duplicate reservation
				
				$query = "INSERT INTO Vip VALUES('{$_POST['passID']}','{$_POST['membership']}','{$_POST['loungeLocation']}')";				
				$res = oci_parse($conn,$query); 
				 
				if (oci_execute($res)){ 
					  
		
						print "<div class='alert' >";
		        		print "<p> Upgrade Successful! </p>";
		        		print "<input type='submit' value='Done' onclick='button.style.display='none''>";
		        		print "</div>";
				}
				else
				{
					print "<div class='alert' >";
		        	print "<p> Error Occurred, Please Try Again</p>";
		    		print "<input type='submit' value='Done' onclick='button.style.display='none''>";
		       		print "</div>";
				}
			
			
			}

			  OCILogoff($conn);
		} else {
		  $err = OCIError();
		  echo "Oracle Connect Error " . $err['message'];
		}
				
	echo "</div>";
?>

<script>
function show() {
    document.getElementById("upgrade_box").style.display = 'block';
}

function hide() {
    document.getElementById("upgrade_box").style.display = 'none';
}
</script>

	</div>

</div>


</body>


</html>
