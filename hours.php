<?php
	require_once(dirname(__FILE__)."/common.php");
   	
   	if (!isset($loginSession))
        doUnauthenticatedRedirect();
    $rv = (Object)[]; 
    
    $employeeID = @$_POST['employeeID'];
    $projectID = @$_POST['projectID'];
    $projectName = @$_POST['projectName'];
    $time = @$_POST['time'];
    $rv = (Object)[];
    
	try {
		$projectTimeHistory = $db->addHours($employeeID, $projectID, $projectName, $time);
		$rv->success = true;
		
	} catch (Exception $ex) {
		$rv->error = $ex->getMessage();
	}//try/catch
	
	echo json_encode($rv);

?>