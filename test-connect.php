<html>
<head> 
    <title> UBC Airlines - testConnect </title>
    <link rel="stylesheet" type="text/css" href="main.css">
</head>
<?php
    //helpers:
    include 'dbLoginCredentials.php'; 
    $db = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = dbhost.ugrad.cs.ubc.ca)(PORT = 1522)))(CONNECT_DATA=(SID=ug)))";
    
    //function:
    function printTableTime(){
        global $dbusername, $dbpassword, $db;
        if ($conn=OCILogon($dbusername, $dbpassword, $db)) {
            $query = "select * from Time"; 
            $res = oci_parse($conn,$query); 
            usleep(100); 
            if (oci_execute($res)){ 
                while($row = @oci_fetch_assoc($res)){
                    $results[]="<tr><td>{$row['TAKEOFFTIME']}</td>
                                <td>{$row['ARRIVALTIME']}</td></tr>";
                }
                echo "<table>";
                echo "<tr><th>takeOffTime</th><th>arrivalTime</th></tr>";
                echo implode($results);
                echo "</table>";
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
            oci_execute($res);
            OCILogoff($conn);
            } else {
              $err = OCIError();
              echo "Oracle Connect Error " . $err['message'];
        }
    }

    echo "<div id=flight>";

    //Table UI
    if (array_key_exists('testInsert', $_POST)) {
        executeNonSelectSQL("INSERT INTO Time Values('2018-06-15 11:55:00','2018-06-15 18:33:00')");
        printTableTime();
    }else if (array_key_exists('testUpdate', $_POST)) {
        executeNonSelectSQL("UPDATE Time Set takeOffTime = '2017-06-15 11:55:00' where arrivalTime = '2018-06-15 18:33:00'");
        printTableTime();
    }else if (array_key_exists('testFetch', $_POST)){
        printTableTime();
    }else if(array_key_exists('executeSQL', $_POST)){
        executeNonSelectSQL($_POST['plainSQL']);
    }else{
        printTableTime();
    };
    //buttons UI
    echo '<form method="POST" action="test-connect.php">';
    echo '<br>';
    echo '<button name="testInsert">test insert</button>';
    echo '<button name="testUpdate">test update</button>';
    echo '<button name="testFetch">test fetch</button>';
    echo '<br>';
    echo '<p><input type="text" name="plainSQL" size="24">';
    echo '<input type="submit" value="go" name="executeSQL" >';
    echo '</p>';
    echo '</form>';
    echo "</div>";
?>
</html>



