<?php
	require_once(dirname(__FILE__)."/../common.php");
	
	$project = @$_POST['project'];
	
	$rv = (Object)[];
    try {
		$temp = $db->completeProject($project);
		$pro = $db->readProject($project);
		$actualTime = $db->totalHours($project);
		$rv->timeEstimate = $pro->timeEstimate;
		$rv->actualTime = $actualTime->ActualTime;
		$rv->project = $project;
		$rv->success = true;
	} catch (Exception $ex) {
		$rv = (Object)[ "error" => $ex->getMessage() ];
	}
	
	header("Content-Type: application/json");
	echo json_encode($rv);
?>