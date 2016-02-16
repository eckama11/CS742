<?php
	require_once(dirname(__FILE__)."/common.php");
    if (!isset($loginSession))
        doUnauthenticatedRedirect();
       
    $id = (@$_GET['id']);
	if ($id[0] != $loginSession->authenticatedEmployee->id)
		doUnauthorizedRedirect();
	
	$id = $id[0];	    
    $projectName = (@$_GET['projectName']);
    $projectName = $projectName[0];
    //echo $projectName;
    $projectID = $db->readProjectID($projectName);
    //echo $projectID;
	
?>
<!-- -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
        <base href="<?php echo htmlentities(BASE_URL); ?>">
 		<title>Add Hours to Project</title>
 		
	<script>
      	function requiredField(elem, errorMsg) {
			var rv = elem.val();
			if (rv == "") {
				elem.tooltip("destroy")
					.addClass("error")
					.data("title", errorMsg)
					.tooltip();
			} else {
				elem.tooltip("destroy")
					.removeClass("error")
					.data("title", "");
			}
			return rv;
		}

		function showError(message) {
			$("#message").text(message);
			$("#messageAlert").show().delay(3000).fadeOut("slow");
		}

		function doAddHours(form) {
			$("#addHoursDiv").hide();
			$("#spinner").show();
			
			$.ajax({
				"type" : "POST",
				"url" : "hours.php",
				"data" : $(form).serialize(),
				"dataType" : "json"
				})
				.done(function(data) {
					$("#spinner").hide();
					if (data.error != null) {
						showError(data.error);
						$("#addHoursDiv").show();
					} else
						$("#successDiv").show();
				})
				.fail(function( jqXHR, textStatus, errorThrown ) {
					console.log("Error: "+ textStatus +" (errorThrown="+ errorThrown +")");
					console.log(jqXHR);
					$("#spinner").hide();
					$("#addHoursDiv").show();
					showError("Request failed, unable to add hours: "+ errorThrown);
				})
			return false;
		} // doHireEmployee
	</script>
	</head>
 
	<body>
		<div id="messageAlert" class="alert alert-danger" style="display:none;position:fixed;left:20px;right:20px">
            <span id="message"></span>
        </div>

   		<div class="container padded">
    		<div class="row" >
    			<div id="spinner" class="col-md-2 col-md-offset-5" style="padding-bottom:10px;text-align:center;display:none">
                    <div style="color:black;padding-bottom:32px;">Updating...</div>
                    <img src="spinner.gif">
                </div>
                <div id="successDiv" class="alert alert-success" style="display:none">
        				<?php
        					echo htmlentities($projectName);
        				?> has been updated to include new hours.
    			</div>
    			<div id="addHoursDiv" class="col-md-3 col-md-offset-5" style="padding-bottom:10px; outline: 10px solid black;">
            		<form class="form-horizontal" method="post" onsubmit="return doAddHours(this)">
              			<input type="hidden" name="page" id="page" value="<?php echo htmlentities(@$_SERVER['PATH_INFO']); ?>"/>
               			<fieldset>
                  			<legend style="color:black;">Update Project Hours</legend>
                   			
                        	<div class="control-group">
                        		<label class="control-label" for="division">Hours</label>
                        		<div>
                        			<input name="time" maxlength="50" placeholder="Enter how many hours..." type="text" class="form-control" id="time" />
								</div>
                        	</div>
                        	<input type="hidden" for="employeeID" name="employeeID" id="employeeID" value="<?php echo htmlentities($id); ?>">
                        	<input type="hidden" for="projectID" name="projectID" id="projectID" value="<?php echo htmlentities($projectID); ?>">
                        	<input type="hidden" for="projectName" name="projectName" id="projectName" value="<?php echo htmlentities($projectName); ?>">
                        	</br>
                        	<div>
                        		<button class="btn btn-primary" name="commit" type="submit">
                        			<span class="glyphicon glyphicon-log-in"></span> Add
                        		</button>
                        	</div>
                		</fieldset>
           			</form>
        		</div>
    		</div>
		</div>
	</body>
</html>
<!-- -->