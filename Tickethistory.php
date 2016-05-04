


<!doctype html>
<html>
    <head>
	<h3> Ticket History </h3>
	<h5><a href= "http://submititticket.com/IT_tracker/Default.php">Back</a></h5>
        <link href ="main.css" rel="stylesheet"-->
    </head><br>
    <body>
	
	 <!--form id= "statusForm "method="post" action= "TicketHistory.php"-->
	<br><br><br><br>
	     
        <div class="container">
		<p>Select ticket status from the drop down menu. <p>
		<form method = "POST" action = "TicketHistory.php">
             <label class="label-form" for="Ticket Status">Ticket Status: <label>
		   <select name="ticket_status" onchange="this.form.submit();">
		        <option value=""> </option>
				<option value="Open">Open</option>
				<option value="Closed">Closed</option>
		   </select>
		       
		 
		   </form>
		   <br><br>
		   
		   
		   
		  <?php
		  if(isset($_POST['ticket_status'])){
		    include "Config.php";
			$ticket_status = $_POST['ticket_status'];
			$userName = $_SERVER['LOGON_USER'];
		    
                 $connectionInfo = array( "Database"=>DB_NAME, "UID"=>DB_USER, "PWD"=>DB_PASSWORD);
			
		    $conn = sqlsrv_connect( $serverName, $connectionInfo);
			  $query = "EXEC dbo.usp_Ext_ticketDetails @username = '".$userName."',@ticket_status = '".$ticket_status."'";
		     $results = sqlsrv_query($conn,$query) or die ("Query failed:". sqlsrv_errors($conn));
	     #echo $results;
		  echo $ticket_status;
		  echo "<table>";
          echo "<tr><th>Request ID</th><th>Date Submitted</th><th>Assigned To</th><th>Request Description</th><th>Resolution</th><th>Completed Date</th></tr>";
          while ($result = sqlsrv_fetch_array($results)) {
               echo "<tr>".
			          "<td>" . $result['request_id'] . "</td>" . 
                      "<td>" . $result['date_submitted'] . "</td>".
					  "<td>" . $result['AnalystAssigned'] . "</td>".
					  "<td>" . $result['request_desc'] . "</td>".
					  "<td>" . $result['Resolution'] . "</td>".
					   "<td>" .$result['Completed_Date'] . "</td>".
					 "</tr>";
          }
          echo "</table>";

		  
		
		  
		  
			 sqlsrv_free_stmt($results);
				
                 sqlsrv_close($conn);
			
		  }
		   ?>
		  
             </div>
          </div>
     </body>
 </html>
 