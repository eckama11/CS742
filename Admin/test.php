<?php
	require_once(dirname(__FILE__)."/../common.php");
	
	$row = $db->totalHours('projectA');
	echo ($row->ActualTime);
?>