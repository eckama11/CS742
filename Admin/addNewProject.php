<?php
    require_once(dirname(__FILE__)."/../common.php");
    if (!isset($loginSession))
        doUnauthenticatedRedirect();

    if (!$loginSession->authenticatedEmployee->employeeType = "Admin")
        doUnauthorizedRedirect();
?>
add new project