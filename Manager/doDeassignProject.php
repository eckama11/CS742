<?php
	require_once(dirname(__FILE__)."/../common.php");
	
	if (!isset($loginSession))
        doUnauthenticatedRedirect();

    if (!$loginSession->authenticatedEmployee->employeeType = "Manager")
        doUnauthorizedRedirect();
        
    //should also check if manager is in same division as deleted project and employee
    
    $employeeId = @$_GET['id'];
    $projectName = @$_GET['projectName'];
    
    try {
    	$db->deleteProjectList($employeeId[0], $projectName[0]);

    	echo '<div id="successDiv" class="col-md-4 col-md-offset-4 alert alert-success">';
        echo 'Employee has been deassigned from project.'	;
    	echo '</div>';

    } catch (Exception $ex) {
        handleDBException($ex);
        return;
    }
?>