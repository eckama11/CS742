<?php
	require_once(dirname(__FILE__)."/../common.php");
   
    $employeeId = @$_POST['id'];
    $division = @$_POST['division'];
    $rv = (Object)[];
	try {
		$emp1 = $db->moveEmployee($employeeId, $division);
		$rv->success = true;
	} catch (Exception $ex) {
		$rv->error = $ex->getMessage();
	}//try/catch
	echo json_encode($rv);
?>