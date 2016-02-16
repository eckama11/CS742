<?php
    require_once(dirname(__FILE__)."/../common.php");
    if (!isset($loginSession))
        doUnauthenticatedRedirect();

    if (!$loginSession->authenticatedEmployee->employeeType = "Admin")
        doUnauthorizedRedirect();
   
    try {
    	$divisions = $db->readDivisions();
    	$projects = $db->readProjects();
	} catch (Exception $ex) {
		handleDBException($ex);
        return;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
        <base href="<?php echo htmlentities(BASE_URL); ?>">
 		<title>Add Division To Project</title>
 		
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

		function doAssignProject(form) {

			$("#addProjectDiv").hide();
			$("#spinner").show();

			$.ajax({
				"type" : "POST",
				"url" : "Admin/doAddDivisionToProject.php",
				"data" : $(form).serialize(),
				"dataType" : "json"
				})
				.done(function(data) {
					$("#spinner").hide();
					if (data.error != null) {
						showError(data.error);
						$("#addProjectDiv").show();
					} else
						$("#successDiv").show();
				})
				.fail(function( jqXHR, textStatus, errorThrown ) {
					console.log("Error: "+ textStatus +" (errorThrown="+ errorThrown +")");
					console.log(jqXHR);
					$("#spinner").hide();
					$("#addProjectDiv").show();
					showError("Request failed, unable to add project for division: "+ errorThrown);
				})
			return false;
		} // doAssignProject
	</script>
	</head>
 
	<body>
		<div id="messageAlert" class="alert alert-danger" style="display:none;position:fixed;left:20px;right:20px">
            <span id="message"></span>
        </div>

   		<div class="container padded">
    		<div class="row" >
    			<div id="spinner" class="col-md-2 col-md-offset-5" style="padding-bottom:10px;text-align:center;display:none">
                    <div style="color:black;padding-bottom:32px;">Authenticating...</div>
                    <img src="spinner.gif">
                </div>
                <div id="successDiv" class="alert alert-success" style="display:none;position:fixed;left:20px;right:20px">
        				Division has been added to project.
    			</div>
    			<div id="addProjectDiv" class="col-md-3 col-md-offset-5" style="padding-bottom:10px; outline: 10px solid black;">
            		<form class="form-horizontal" method="post" onsubmit="return doAssignProject(this)">
              			<input type="hidden" name="page" id="page" value="<?php echo htmlentities(@$_SERVER['PATH_INFO']); ?>"/>
               			<fieldset>
                  			<legend style="color:black;">Add Project To Division</legend>
                        	
                      	<div class="control-group">
                        		<label class="control-label" for="project">Project</label>
                        		<div name="project" id="project">
                        			<select name="project" id="project" class="form-control col-lg-12">
<?php
										foreach ($projects as $pro) {
											echo '<option>'. htmlentities($pro->name). '</option>';
										}
?>
									</select>
								</div>
                        	</div>                      	
                        	
							<div class="control-group">
                        		<label class="control-label" for="division">Division</label>
                        		<div name="division" id="division">
                        			<select name="division" id="division" class="form-control col-lg-12">
<?php
										foreach ($divisions as $div) {
											echo '<option>'. htmlentities($div->name). '</option>';
										}
?>
									</select>
								</div>
                        	</div>
                        	
                        	</br>
                        	</br>
                        	<div>
                        		
                        		<button class="btn btn-primary" name="commit" type="submit">
                        			<span class="glyphicon glyphicon-log-in"></span> Assign
                        		</button>
                        	</div>
                		</fieldset>
           			</form>
        		</div>
    		</div>
		</div>
	</body>
</html>