


<!doctype html>
<html>
    <head>
	<h3> Ticket History </h3>
	<h5><a href= "http://submititticket.com/It_tracker/Default.php">Home</a></h5>
        <link href ="main.css" rel="stylesheet"-->
    </head><br>
    <body>
	
	 <!--form id= "statusForm "method="post" action= "TicketHistory.php"-->
	<br><br><br><br>
	     
        <div class="container">
		<p>Select ticket status from the drop down menu. <p>
		<form method = "POST" action = "AnalystLanding.php">
             <label class="label-form" for="Ticket Status">Ticket Status: <label>
		   <select name="ticket_status" onchange="this.form.submit();">
		        <option value=""> </option>
				<option value="Open">Open</option>
				<option value="Closed">Closed</option>
		   </select>
		       
		 
		   </form>
		   <br><br>
		   
		   
		   
		  <?php
		  #if(isset($_POST['ticket_status'])){
		    $ticket_status = $_POST['ticket_status'];
			$userName = $_SERVER['LOGON_USER'];
			$serverName = "WIN-08E6TKVCR36"; //serverName\instanceName
	         include "Config.php";
            $connectionInfo = array( "Database"=>DB_NAME, "UID"=>DB_USER, "PWD"=>DB_PASSWORD);
			
		    $conn = sqlsrv_connect( $serverName, $connectionInfo);
			  $query = "EXEC dbo.usp_Ext_ticketDetails @username = '".$userName."',@ticket_status = '".$ticket_status."'";

		     $results = sqlsrv_query($conn,$query) or die ("Query failed:". sqlsrv_errors($conn));
	     #echo $results;
		  echo $ticket_status;
		  echo "<table>";
          echo "<tr><th>Request ID</th><th>Date Submitted</th><th>Due Date</th><th>Submitted By</th><th>Request Priority</th><th>Request Description</th><th>Request Type</th><th>Resolution</th><th>Completed Date</th></tr>";
          while ($result = sqlsrv_fetch_array($results)) {
               echo "<tr>".
			          "<td>" . $result['request_id'] . "</td>" . 
                      "<td>" . $result['date_submitted'] . "</td>".
					  "<td>" . $result['requiredDue_Date'] . "</td>".
					  "<td>" . $result['Employee_name'] . "</td>".
					  "<td>" . $result['request_importance'] . "</td>".
					  "<td>" . $result['request_desc'] . "</td>".
					  "<td>" . $result['Request_type'] . "</td>".
					  "<td>" . $result['Resolution'] . "</td>".
					   "<td>" .$result['Completed_Date'] . "</td>".
					 "</tr>";
          }
          echo "</table>";

		  
		
		  
		  
			 sqlsrv_free_stmt($results);
				
                 sqlsrv_close($conn);
			
		#  }
		   ?>
		  
             </div>
          </div>
		  <br>
		  <hr>
		  <br>
		           <div class="container">
	
        <h2>Close ticket</h2>
		 <form method="post" action= "AnalystLanding.php">
            <label class="label-form" for="ticket_id">Ticket ID: <label>
              <select name="ticket_id">
                <?php
            
                 include "Config.php";
                 $connectionInfo = array( "Database"=>DB_NAME, "UID"=>DB_USER, "PWD"=>DB_PASSWORD);
			
				$conn = sqlsrv_connect( $serverName, $connectionInfo);
				$userName = $_SERVER['LOGON_USER'];
				if( $conn ) {
					echo "Connection established.<br />";
						}else{
						echo "Connection could not be established.<br />";
					die( print_r( sqlsrv_errors(), true));
					}
                $query = "EXEC dbo.usp_Ext_ticketIDs @username = '".$userName."'";
					
								
                $results = sqlsrv_query($conn,$query)
                        or die("Query Failed :" . sqlsrv_errors($conn));
                        while ($result = sqlsrv_fetch_array($results)){
                            echo "<option>" . $result['request_id'] . "</option>";
						
                        }
                 sqlsrv_free_stmt($results);
				
                
				
                 ?>
              </Select>  
     <br><br>
             
             
             
			   <div>
			    <label class="label-form" for="request_desc">Resolution Details: <label><br><br>
			    <textarea name="request_desc" style="width:500px;height:250px;"></textarea><br>
                 </div><br>
				 
            <div class="SubmitButton"> <input type ="Submit" value="Submit" name ="submit"> </div>
          
        
     
   </form>
          </div>
		  
		  
		  <?php
			if(isset($_POST['ticket_id'])){
	    $ticket_id = $_POST['ticket_id'];
		$request_desc = $_POST['request_desc'];
	
			
	          # include "Config.php";
			   #$connectionInfo = array( "Database"=>DB_NAME, "UID"=>DB_USER, "PWD"=>DB_PASSWORD);
			
			#	$conn = sqlsrv_connect( $serverName, $connectionInfo);

			
				$sql = "Exec dbo.usp_ClsTicket @request_id =".$ticket_id.", @resolution ='".$request_desc."'";
				#Echo $sql;
				
				sqlsrv_query($conn,$sql);
				
				 
				Sqlsrv_close($conn);
				
				Echo "</br></br></br></br><h3> Ticket Number: ".$ticket_id." has been closed.  The requestor will be notified and offered a survey.<h3>";
				
				
			}
       ?> 
		  
		  
     </body>
 </html>
 