<?php
	require_once(dirname(__FILE__)."/../common.php");
   
    $employee = @$_POST['employeeName'];
    $project = @$_POST['projectName'];
    
    $employeeA = explode(":", $employee);
    $projectA = explode(":", $project);
    
    $employeeName = $employeeA[0];
    $employeeId = $employeeA[1];
    $projectName = $projectA[0];
    $projectId = $projectA[1];
   // throw new Exception($employeeId);
    $rv = (Object)[];
    try {
		if ($db->isEmployeeProjectInUse($employeeId, $projectId))
			throw new Exception("The employee is already assigned to the project");
	
		$emp1 = $db->insertProjectList(0, $employeeId, $projectId, $projectName);
		$rv->success = true;
	} catch (Exception $ex) {
		$rv = (Object)[ "error" => $ex->getMessage() ];
	}
	
header("Content-Type: application/json");
echo json_encode($rv);
	
?>