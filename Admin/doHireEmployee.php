<?php
require_once(dirname(__FILE__)."/../common.php");

$username = @$_POST['username'];
$password = @$_POST['password'];
$confirmPassword = @$_POST['confirmPassword'];
$employeeType = @$_POST['employeeType'];
$name = @$_POST['name'];
$status = @$_POST['status'];
$division = @$_POST['division'];

$rv = (Object)[];
try {
	// Verify the username is unique
	if ($db->isUsernameInUse($username))
		throw new Exception("The username '$username' is already assigned to another user");
	
    // id = 0 means no id given yet 
	//will be used in DBInterface to make new id
	$id = 0;
	// Create/update the employee record
    $emp1 = new Employee(
                $id, $username, $password, $employeeType, $name, $status, $division
            );            
    $emp = $db->hireEmployee($emp1);
    $rv->success = true;
} catch (Exception $ex) {
    $rv->error = $ex->getMessage();
}//try/catch
echo json_encode($rv);