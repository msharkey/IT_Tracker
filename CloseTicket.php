



<!doctype html>
<html>
    <head>
	    <link href="main.css" rel="stylesheet">

    </head>



    <body>
          <div class="container">
	
        <h2>Close ticket</h2>
            <label class="label-form" for="ticket_id">Ticket ID: <label>
              <select name="ticket_id">
                <?php
                #include "configMS.php";
                $serverName = "WIN-08E6TKVCR36"; //serverName\instanceName
				$connectionInfo = array( "Database"=>"ITChangeTracker", "UID"=>"RemoteAdmin", "PWD"=>"MoralHazard30!!");
				$conn = sqlsrv_connect( $serverName, $connectionInfo);
				$userName = $_SERVER['LOGON_USER'];
				if( $conn ) {
					echo "Connection established.<br />";
						}else{
						echo "Connection could not be established.<br />";
					die( print_r( sqlsrv_errors(), true));
					}
                $query = "
                               Select request_id 
						from dbo.request r
						JOIN dbo.employee e
							ON r.assigned_to = e.employee_id
							JOIN dbo.employee s 
								ON s.employee_id = request_by
								JOIN dbo.request_type rt
								ON rt.request_type_id = r.request_type_id
								Where e.userName = '".$userName."'";
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
			    <textarea name="request_desc" style="width:250px;height:150px;"></textarea><br>
                 </div><br>
				 
            <div class="SubmitButton"> <input type ="Submit" value="Submit" name ="submit"> </div>
          
        
     
   </form>
          </div>
    </body>
</html>




