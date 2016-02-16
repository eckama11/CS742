<?php
	require_once(dirname(__FILE__)."/../common.php");
   
    $division = @$_POST['division'];
    $project = @$_POST['project'];
    
    $project = $db->readProjectID($project);
    
    $project = $project[0];
    /*
    $employeeA = explode(":", $employee);
    $projectA = explode(":", $project);
    
    $employeeName = $employeeA[0];
    $employeeId = $employeeA[1];
    $projectName = $projectA[0];
    $projectId = $projectA[1];
    */
    $rv = (Object)[];
    try {
		if ($db->isProjectDivisionInUse($project,$division))
			throw new Exception("The division is already assigned to project");
	
		$projectDivsionList = $db->writeProjectDivisionList($project, $division);
		$rv->success = true;
	} catch (Exception $ex) {
		$rv = (Object)[ "error" => $ex->getMessage() ];
	}
	
header("Content-Type: application/json");
echo json_encode($rv);
	
?>