<!doctype html>
<html>
  <head>
    <title>result</title>
    <link href="main.css" rel="stylesheet">
  </head>
  <body>
    <div class="container">
      <h1>result</h1>
        <?php
	      include "Config.php";
		 $connectionInfo = array( "Database"=>DB_NAME, "UID"=>DB_USER, "PWD"=>DB_PASSWORD);
		
         $conn = sqlsrv_connect( $serverName, $connectionInfo);
	
    
		$question1= $_POST['question1'];
		$question2= $_POST['question2'];
		$question3= $_POST['question3'];
		$content= $_POST['content'];
	
	
	
	
		
		 $query = "EXECUTE dbo.usp_inst_SurveyRes @q1 = ".$question1.",@q2=".$question2.",@q3= ".$question3." ,@content ='".$content."'";
		 
		 $results = sqlsrv_query($conn, $query);
		 
		 


		echo 'Thank you for you time';
		   sqlsrv_free_stmt($results);


		   sqlsrv_close($conn);

		   ?>

    </form>
	   </div>
  </body>
</html>