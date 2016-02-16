<?php
    require_once(dirname(__FILE__)."/common.php");
    
    $pro = @$_GET['id'];
?>
<div class="container col-md-6 col-md-offset-3">
<?php echo '<legend>'. htmlentities($pro) .'</legend>';
?>
			<table class="table table-striped table-hover table-bordered table-condensed">
				<thead><tr>
					<th>Name</th>
					<th>Time</th>
				</tr></thead>
				<tbody>

<?php
    $rv = (Object)[];
	try {
		//$project = $db->readProject($name);
		//echo $pro;
		$time = $db->totalProjectEmployeeHours($pro);
		//echo var_dump($time);
		$rv->success = true;
		if ($rv->success == true) {
			foreach ($time as $t) {
				echo	'<tr>';
				echo		'<td>'. htmlentities($db->readEmployee( $t->id )->username) .'</td>';
				echo		'<td>'. htmlentities($t->hours) .'</td>';
				echo	'</tr>';
			}
		}
	} catch (Exception $ex) {
		$rv->error = $ex->getMessage();
	}//try/catch
?>
			</tbody>
		</table>
</div>