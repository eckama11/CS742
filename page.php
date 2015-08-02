<?php
require_once(dirname(__FILE__)."/common.php");

$prefix = dirname(__FILE__) . DIRECTORY_SEPARATOR;
$page = realpath($prefix . @$_SERVER['PATH_INFO'] .".php");

if (!isset($loginSession))
    doUnauthenticatedRedirect();
else if ((substr($page, 0, strlen($prefix)) != $prefix) || !is_readable($page))
    doUnauthorizedRedirect();
ob_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
        <base href="<?php echo htmlentities(BASE_URL); ?>">
 		<title>Project Tracker</title>
 		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<!-- StyleSheet -->
		<link rel="stylesheet" href="css/bootstrap.min.css" />
		<link rel="stylesheet" href="css/bootstrap-datepicker.css" />
		<link rel="stylesheet" href="css/custom.css" />
        <script data-main="js/main" src="js/require.js"></script>
        <link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">
		<script>
		function showError(message) {
			$("#message").text(message);
			var messageAlert = $("#messageAlert");
			messageAlert.css("z-index", "30000");
			messageAlert.show().delay(5000).fadeOut("slow");
		}
		</script>
	</head>
 
	<body>
		hello
		<?php require_once($page); ?>
	</body>
</html>
<?php
    ob_end_flush();
?>