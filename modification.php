<html>
<head> 
    <title> UBC Airlines - Modification History </title>
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
    <h1 style="color:white;">Modification History</h1>
<?php
    //helpers:
    include 'dbLoginCredentials.php'; 
    $db = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = dbhost.ugrad.cs.ubc.ca)(PORT = 1522)))(CONNECT_DATA=(SID=ug)))";
    
    //function:
    function printModification(){
        global $dbusername, $dbpassword, $db;
        if ($conn=OCILogon($dbusername, $dbpassword, $db)) {
            $query = "select * from Modification order by time"; 
            $res = oci_parse($conn,$query); 
            if (oci_execute($res)){ 
                print "<table id=ticketList style='width:100%;'>"; 
                $first = 0; 
                while ($row = @oci_fetch_assoc($res)){ 
                        if (!$first){ 
                                $first = 1; 
                                print "<TR><TH>"; 
                                print implode("</TH><TH>",array_keys($row)); 
                                print "</TH></TR>\n"; 
                        } 
                        print "<TR><TD>"; 
                        print @implode("</TD><TD>",array_values($row)); 
                        print "</TD></TR>\n"; 
                } 
                print "</table>"; 
                echo '<script>var x = document.getElementById("ticketList").rows.length;
                      if(x == 0) document.write("Empty Table");
                      </script>';
            }else{
                echo "<br>Cannot Execute:  " . $query . "<br>";
            }
            OCILogoff($conn);
        } else {
          $err = OCIError();
          echo "Oracle Connect Error " . $err['message'];
        }

    }

    echo "<div id=flight>";
    printModification();
    echo '<form method="POST" action="modification.php">';
    echo '<br>';
    echo '<button name="testFetch">Refresh</button>';
    echo '</form>';
    echo "</div>";
?>
    </div>
</div>
<body> 
</html>



