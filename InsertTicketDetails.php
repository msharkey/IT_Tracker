

<!doctype html>
<html>
<header>
 <link href ="main.css" rel="stylesheet"-->
 </header>
  <body>
    <h3>A ticket has been opened.  Thank you for the submission.  You will receive a confirmation email shortly.</h3> <br/>
   
 
    <?php
 
	    $sub_name = $_POST['sub_name'];
		$request_type = $_POST['request_type'];
		$analyst_name = $_POST['Analyst_Name'];
		$priority_level = $_POST['priority_level'];
		$daterange = $_POST['daterange'];
		$request_desc = $_POST['request_desc'];
		$_SERVER['LOGON_USER'];
			
	           include "Config.php";
			   $connectionInfo = array( "Database"=>DB_NAME, "UID"=>DB_USER, "PWD"=>DB_PASSWORD);
			
				$conn = sqlsrv_connect( $serverName, $connectionInfo);

				if( $conn ) {
					echo "<br />";
						}else{
						echo "Connection could not be established.<br />";
					die( print_r( sqlsrv_errors(), true));
					}
			
				$sql = "exec ITChangeTracker.dbo.usp_insrtNewticket @sub_name='".$sub_name."', @request_type='".$request_type."', @analyst_name='".$analyst_name."', @priority_level='".$priority_level."', @daterange='".$daterange."', @request_desc='".$request_desc."'";
				#Echo $sql;
				
				sqlsrv_query($conn,$sql);
				
				 
				 
				Sqlsrv_close($conn);

       ?> 
	 <br><br><br><br>
	   <p> Click <a style="color:blue;" href="http://submititticket.com/IT_tracker/Default.php">here</a> to submit another ticket.
	  <br><br>
			Click <a style="color:blue;" href="http://submititticket.com/IT_tracker/Tickethistory.php">here</a> to view previous tickets.
	  </p> 
	
	
  </body>
</html>