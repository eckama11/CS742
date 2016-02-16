<?php
    require_once(dirname(__FILE__)."/../common.php");
    if (!isset($loginSession))
        doUnauthenticatedRedirect();

    if (!$loginSession->authenticatedEmployee->employeeType = "Admin")
        doUnauthorizedRedirect();
        
?>
<div class="container padded">
<p>Welcome, <?php echo htmlentities($loginSession->authenticatedEmployee->name); ?>!</p>
<p>Please select an action from the menu at the top of the screen.</p>
</div>