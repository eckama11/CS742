<?php
    require_once(dirname(__FILE__)."/../common.php");
    if (!isset($loginSession))
        doUnauthenticatedRedirect();

    if (!$loginSession->authenticatedEmployee->employeeType = "Admin")
        doUnauthorizedRedirect();
        
     try {
        $employees = $db->readEmployees();
    } catch (Exception $ex) {
        handleDBException($ex);
        return;
    }
    
    $targetPage = "page.php/". "Admin/move";
?>

<script>
    function selectEmployee(row) {
        var id = row.getAttribute('emp-id');
        window.location.href = '<?php echo addcslashes(htmlentities($targetPage), "\0..\37!@\\\177..\377"); ?>?id=' + id;
    } // selectEmployee(row)
</script>
<div class="container col-md-6 col-md-offset-3">
<?php if (count($employees) == 0) { ?>
    <div>Sorry, there are currently no employees to display.</div>
    <div>Please check back later.</div>
<?php } else { ?>
    <legend>Select Employee to Change Employee's Division</legend>
    <table class="table table-striped table-hover table-bordered table-condensed">
    <thead><tr>
      <th>Name</th>
      <th>Username</th>
      <th>Division</th>
      <th>Status</th>
    </tr></thead>
    <tbody>
<?php
    foreach ($employees as $emp) {
    	if ($loginSession->authenticatedEmployee->id != $emp->id && $emp->status != "Inactive") {
			echo	'<tr onclick="selectEmployee(this)" emp-id="'. $emp->id .'">';
			echo		'<td>'. htmlentities($emp->name) .'</td>';
			echo		'<td>'. htmlentities($emp->username) .'</td>';
			echo		'<td>'. htmlentities($emp->division) .'</td>';
            echo		'<td>'. htmlentities($emp->status).'</td>';	
			echo	'</tr>';
		}
    } 
?>
    </tbody>
    </table>
<?php } ?>
</div>