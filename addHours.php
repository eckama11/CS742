<?php
	require_once(dirname(__FILE__)."/common.php");
    if (!isset($loginSession))
        doUnauthenticatedRedirect();
    
    $p = $loginSession->authenticatedEmployee->id;
       
     try {
        $projects = $db->readEmployeeProjects($loginSession->authenticatedEmployee->id);
    } catch (Exception $ex) {
        handleDBException($ex);
        return;
    }
    
    $targetPage = "page.php/". "doAddHours";
?>

<script>
    function selectProject(row) {
        var id = row.getAttribute('emp-id');
        var projectName = row.getAttribute('pN');
        window.location.href = '<?php echo addcslashes(htmlentities($targetPage), "\0..\37!@\\\177..\377"); ?>?id[]=' + id + '&projectName[]=' + projectName;
    } // selectEmployee(row)
</script>
<div class="container col-md-6 col-md-offset-3">
<?php if (count($projects) == 0) { ?>
    <div>Sorry, there are currently no projects to display.</div>
    <div>Please check back later.</div>
<?php } else { ?>
    <legend>Add Hours </legend>
    <table class="table table-striped table-hover table-bordered table-condensed">
    <thead><tr>
      <th>Project Name</th>
    </tr></thead>
    <tbody>
<?php
    foreach ($projects as $pro) {
    	if ($loginSession->authenticatedEmployee->id) {
			echo	'<tr onclick="selectProject(this)" emp-id="'. $pro->employeeID .'" pN="'. $pro->projectName .'" >';
			echo		'<td>'. htmlentities($pro->projectName) .'</td>';
			echo		'<td style="display:none;">'. htmlentities($pro->employeeID) .'</td>';
			echo	'</tr>';
		}
    } 
?>
    </tbody>
    </table>
<?php } ?>
</div>
