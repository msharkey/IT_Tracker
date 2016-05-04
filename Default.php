



<!doctype html>
<html>
    <head>
	    <link href="main.css" rel="stylesheet">

    </head>



    <body>
          <div class="container">
	
        <h3>Data Analytics and IT request Form</h3>
		<h5><a href= "http://submititticket.com/IT_Tracker/Tickethistory.php" onclick="doClick(); return false;">Ticket Dashboard</a></h5>
        <form method="post" action= "InsertTicketDetails.php">
           	<script>
		var deptName = <?php 
	                 include "Config.php";
                 $connectionInfo = array( "Database"=>DB_NAME, "UID"=>DB_USER, "PWD"=>DB_PASSWORD);
			
				$conn = sqlsrv_connect( $serverName, $connectionInfo);
		
	           $sql = "exec dbo.uspGetDepartment @userName='".$_SERVER['LOGON_USER']."'";
			   $results = sqlsrv_query($conn,$sql);
			   while ($result = sqlsrv_fetch_array($results)){
                
		      $jresult = $result['Department_name'];
			  
			   Echo json_encode($jresult);
			   }
	?>
			
			function doClick() {
				if (deptName=='Account Management'){
					window.location.href = "http://submititticket.com/IT_Tracker/Tickethistory.php";
				}
				else{
					window.location.href = "http://submititticket.com/IT_tracker/AnalystLanding.php";
				}
					}
</script>
		   <br><br><br><br><br>
		   
            <label class="label-form" for="sub_name">Name: <label>
              <select name="sub_name">
                <?php
                
			
				$conn = sqlsrv_connect(DB_HOST, $connectionInfo);
				ECHO $conn;
				if( $conn ) {
					echo "Connection established.<br />";
						}else{
						echo "Connection could not be established.<br />";
					die( print_r( sqlsrv_errors(), true));
					}
                $query = " EXECUTE dbo.usp_getAccountManagers";
                $results = sqlsrv_query($conn,$query)
                        or die("Query Failed :" . sqlsrv_errors($conn));
                        while ($result = sqlsrv_fetch_array($results)){
                            echo "<option>" . $result['sub_name'] . "</option>";
						
                        }
                 sqlsrv_free_stmt($results);
				
                ECHO $connectionInfo;
				
                 ?>
              </Select>  
     <br><br>
             <label for="Request_Type">Request Type: </label>
             
            <select name="request_type">
                <?php
                #include "config.php";
               $query = "EXECUTE dbo.usp_Ext_RequestType";
                $results = sqlsrv_query($conn,$query)
                        or die("Query Failed :" . sqlsrv_errors($conn));
                        while ($result = sqlsrv_fetch_array($results)){
                            echo "<option>" . $result['request_type'] . "</option>";
						
                        }
                 sqlsrv_free_stmt($results);
			  
			  
                 ?>
            </select> <br><br>
                 <label for="Analyst_Name">Analyst: </label>
                         <select name="Analyst_Name">
                <?php
                
				   $query = " EXECUTE dbo.usp_Ext_Analyst";
										
                $results = sqlsrv_query($conn,$query)
                        or die("Query Failed :" . sqlsrv_errors($conn));
                        while ($result = sqlsrv_fetch_array($results)){
                            echo "<option>" . $result['Analyst_Name'] . "</option>";
						
                        }
                 sqlsrv_free_stmt($results);
				
                 ?>
            </select><br><br>
             
                 <div>
                     <label for="priority_level">Priority Level: </label> <br>
                     <input type="radio" name="priority_level" value="High"> High<br>
                     <input type="radio" name="priority_level" value="Medium" Checked> Medium<br>
                     <input type="radio" name="priority_level" value="Low"> Low<br>
                 </div><br><br>
           
              
                  <label class="label-form" for="daterange">Due Date: <label>
              <select name="daterange">
                <?php
                 
				   $query = "Execute dbo.usp_Ext_DateRange";
										
                $results = sqlsrv_query($conn,$query)
                        or die("Query Failed :" . sqlsrv_errors($conn));
                        while ($result = sqlsrv_fetch_array($results)){
                            echo "<option>" . $result['daterange'] . "</option>";
						
                        }
						  sqlsrv_free_stmt($results);
						  sqlsrv_close($conn)
                 ?>
              </select> <br><br>
			   <div>
			    <label class="label-form" for="request_desc">Request Details: <label><br><br>
			    <textarea name="request_desc" style="width:250px;height:150px;"></textarea><br>
                 </div><br>
				 
            <div class="SubmitButton"> <input type ="Submit" value="Submit" name ="submit"> </div>
          
        
     
   </form>
          </div>
    </body>
</html>




