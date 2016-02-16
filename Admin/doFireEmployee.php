<?php
    require_once(dirname(__FILE__)."/../common.php");
    
    $employeeId = @$_GET['id'];
    $rv = (Object)[];
	try {
		$emp1 = $db->fireEmployee($employeeId);
		$rv->success = true;
		if ($rv->success == true) {
			echo '<div class="col-md-6 col-md-offset-3 alert alert-success" role="alert" id="successDiv">';
        	echo 'Employee has been successfully updated.';
    		echo '</div>';
		}
	} catch (Exception $ex) {
		$rv->error = $ex->getMessage();
	}//try/catch
	//echo json_encode($rv);
?>

	
