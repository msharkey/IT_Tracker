



<!doctype html>
<html>
    <head>
	    <link href="main.css" rel="stylesheet">

    </head>



    <body>
          <div class="container">
	
        <h3>Data Analytics and IT request Form</h3>
		<h5><a href= "http://submititticket.com/Tickethistory.php" onclick="doClick(); return false;">Ticket Dashboard</a></h5>
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
					window.location.href = "http://submititticket.com/Tickethistory.php";
				}
				else{
					window.location.href = "http://submititticket.com/AnalystLanding.php";
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
                $query = "
                                       Select '' as sub_name
										UNION ALL

										SELECT Employee_name
										FROM [ITChangeTracker].[dbo].[employee] e
										JOIN [ITChangeTracker].[dbo].[Department] d
										ON e.department_ID = d.department_id
										Where d.Department_name = 'Account Management'";
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
               $query = "
                                    Select '' as [request_type]
									UNION ALL

									SELECT  [request_type]
									FROM [ITChangeTracker].[dbo].[request_type]";
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
                
				   $query = "
                                    
								         Select '' as Analyst_Name
										UNION ALL

										SELECT Employee_name
										FROM [ITChangeTracker].[dbo].[employee] e
										JOIN [ITChangeTracker].[dbo].[Department] d
										ON e.department_ID = d.department_id
										Where d.Department_name = 'Data Team'";
										
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
                 
				   $query = "Select Convert(Varchar(20),daterange,101) as daterange
							from [ITChangeTracker].[dbo].[PopulateDates](Getdate())
							Where daterange Between DATEADD(dd,-1,Getdate()) and DATEADD(dd,30,Getdate())
							";
										
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




