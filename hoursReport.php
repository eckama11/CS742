<?php
	require_once(dirname(__FILE__)."/common.php");
    if (!isset($loginSession))
        doUnauthenticatedRedirect();
        
     if (!$loginSession->authenticatedEmployee->employeeType = "Admin" || !$loginSession->authenticatedEmployee->employeeType = "Manager" 
     	|| !$loginSession->authenticatedEmployee->employeeType = "Employee")
        doUnauthorizedRedirect();
        
    try {
        $projects = $db->readProjects();
    } catch (Exception $ex) {
        handleDBException($ex);
        return;
    }
    
    $targetPage = "page.php/". "doHoursReport";
?>

<script>
    function selectEmployee(row) {
        var id = row.getAttribute('emp-id');
        window.location.href = '<?php echo addcslashes(htmlentities($targetPage), "\0..\37!@\\\177..\377"); ?>?id=' + id;
    } // selectEmployee(row)
</script>
<div class="container col-md-6 col-md-offset-3">
<?php if (count($projects) == 0) { ?>
    <div>Sorry, there are currently no employees to display.</div>
    <div>Please check back later.</div>
<?php } else { ?>
    <legend>Select Employee to Change Status</legend>
    <table class="table table-striped table-hover table-bordered table-condensed">
    <thead><tr>
      <th>Id</th>
      <th>Name</th>
      <th>Time Estimate</th>
      <th>Status</th>
    </tr></thead>
    <tbody>
<?php
    foreach ($projects as $pro) {
			echo	'<tr onclick="selectEmployee(this)" emp-id="'. $pro->name .'">';
			echo		'<td>'. htmlentities($pro->id) .'</td>';
			echo		'<td>'. htmlentities($pro->name) .'</td>';
			echo		'<td>'. htmlentities($pro->timeEstimate) .'</td>';
            echo		'<td>'. htmlentities($pro->status).'</td>';	
			echo	'</tr>';
    } 
?>
    </tbody>
    </table>
<?php } ?>
</div>