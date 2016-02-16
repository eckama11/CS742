<?php
	require_once(dirname(__FILE__)."/../common.php");
   
    $name = @$_POST['name'];
    $time = @$_POST['time'];
    $status = @$_POST['status'];
    $division = @$_POST['division'];
    
    $rv = (Object)[];
    try {
		if ($db->isProjectNameInUse($name))
			throw new Exception("The project name is already being used.");
	
		$tempA = $db->writeProject($name, $time, $status);
		$projectID = $db->readProjectID($name);
		$tempB = $db->writeProjectDivisionList($projectID, $division);
		$rv->success = true;
	} catch (Exception $ex) {
		$rv = (Object)[ "error" => $ex->getMessage() ];
	}
	
header("Content-Type: application/json");
echo json_encode($rv);
	
?>