<?php
require_once(dirname(__FILE__)."/common.php");

$prefix = dirname(__FILE__) . DIRECTORY_SEPARATOR;
$page = realpath($prefix . @$_SERVER['PATH_INFO'] .".php");

if (!isset($loginSession))
    doUnauthenticatedRedirect();
//else if ((substr($page, 0, strlen($prefix)) != $prefix) || !is_readable($page))
    //doUnauthorizedRedirect();
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
        <!--<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.no-icons.min.css" rel="stylesheet">-->
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
		<div class="navbar navbar-inverse navbar-static-top">
			<div class="container">
				<a href="#" class="navbar-brand">
				<span class="glyphicon glyphicon-hourglass"></span>
				Project Tracker</a>
				<button class="navbar-toggle" data-toggle="collapse" data-target=".navHeaderCollapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<div class="collapse navbar-collapse navHeaderCollapse">
					<ul class="nav navbar-nav navbar-right">		
<?php if ($loginSession->authenticatedEmployee->employeeType == "Admin") { ?>
						<li class="dropdown">
          					<a class="dropdown-toggle" data-toggle="dropdown">
          					<span class="glyphicon glyphicon-user"></span>
          					HR <b class="caret"></b></a>
          					<ul class="dropdown-menu" role="menu">
            					<li><a href="page.php/Admin/hireEmployee">Hire Employee</a></li>
            					<li><a href="page.php/Admin/fireEmployee">Terminate Employee</a></li>
            					<li><a href="page.php/Admin/moveEmployee">Move Employee</a></li>
          					</ul>
        				</li>
        				<li class="dropdown">
          					<a class="dropdown-toggle" data-toggle="dropdown">
          					<span class="glyphicon glyphicon-briefcase"></span>
          					Project <b class="caret"></b></a>
          					<ul class="dropdown-menu" role="menu">
          						<li><a href="page.php/Admin/completeProject">Complete Project</a></li>
            					<li><a href="page.php/Admin/addProject">Add Project</a></li>
            					<li><a href="page.php/Admin/addDivisionToProject">Add Division to Project</a></li>
          					</ul>
        				</li>
							
<?php } ?>

<?php if ($loginSession->authenticatedEmployee->employeeType == "Admin" || $loginSession->authenticatedEmployee->employeeType == "Employee" || $loginSession->authenticatedEmployee->employeeType ==  "Manager") { ?>
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown">
          					<span class="glyphicon glyphicon-hourglass"></span>
          					Hours <b class="caret"></b></a>
          					<ul class="dropdown-menu" role="menu">
								<li><a href="page.php/addHours">Add Hours</a></li>
								<li><a href="page.php/hoursReport">Project Hours Report</a></li>
							</ul>
                        </li>
                        
<?php } ?>
<?php if ($loginSession->authenticatedEmployee->employeeType == "Manager") { ?>	
						<li class="dropdown">
						  <a class="dropdown-toggle" data-toggle="dropdown">
						  <span class="glyphicon glyphicon-user"></span>
							Edit Employee Divisions <b class="caret"></b></a>
							<span class="caret"></span>
						  </button>
						  <ul class="dropdown-menu" role="menu">
							<li><a href="page.php/Manager/assignProject"><span>Assign Project</span></a></li>
						  	<li><a href="page.php/Manager/deassignProject"><span>Deassign Project</span></a></li>
						  </ul>
						</li>
						
						
<?php } ?>				<li>
							<a href="logout.php">
								<span class="glyphicon glyphicon-log-out"></span>
							Logout</a>
						</li>
					</ul>
				</div>
			</div>
		</div>

        <div id="messageAlert" class="alert alert-danger" style="display:none;position:fixed;left:20px;right:20px">
            <span id="message"></span>
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        </div>
<?php	require_once($page); ?>		
	</body>
</html>
<?php
    ob_end_flush();
?>