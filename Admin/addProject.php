<?php
    require_once(dirname(__FILE__)."/../common.php");
    if (!isset($loginSession))
        doUnauthenticatedRedirect();

    if (!$loginSession->authenticatedEmployee->employeeType = "Admin")
        doUnauthorizedRedirect();
        
    try {
    	$divisions = $db->readDivisions();
	} catch (Exception $ex) {
		handleDBException($ex);
        return;
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
        <base href="<?php echo htmlentities(BASE_URL); ?>">
 		<title>Add Project</title>
 		
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

			function doHireEmployee(form) {
				var name = requiredField($(form.elements.name), "You must enter a name.");
				var time = requiredField($(form.elements.time), "You must enter a time estimate.");
				
				$("#addDiv").hide();
				$("#spinner").show();

				$.ajax({
					"type" : "POST",
					"url" : "Admin/doAddProject.php",
					"data" : $(form).serialize(),
					"dataType" : "json"
					})
					.done(function(data) {
						$("#spinner").hide();
						if (data.error != null) {
							showError(data.error);
							$("#addDiv").show();
						} else
							$("#successDiv").show();
					})
					.fail(function( jqXHR, textStatus, errorThrown ) {
						console.log("Error: "+ textStatus +" (errorThrown="+ errorThrown +")");
						console.log(jqXHR);
						$("#spinner").hide();
						$("#addDiv").show();
						showError("Request failed, unable to add project: "+ errorThrown);
					})
				return false;
			} // doAddProject
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
        				Project has been added. Managers can now assign employees to project.
    			</div>
    			<div id="addDiv" class="col-md-3 col-md-offset-5" style="padding-bottom:10px; outline: 10px solid black;">
            		<form class="form-horizontal" method="post" onsubmit="return doHireEmployee(this)">
              			<input type="hidden" name="page" id="page" value="<?php echo htmlentities(@$_SERVER['PATH_INFO']); ?>"/>
               			<fieldset>
                  			<legend style="color:black;">Project</legend>
                  			
                   			<div class="control-group">
                        		<label class="control-label" for="name">Name</label>
                        		<div class="controls">
                        			<input name="name" maxlength="50" placeholder="Enter project name..." type="text" class="form-control" id="name" />
                    			</div>
                    		</div>
                    		
                			<div class="control-group">
                        		<label class="control-label" for="time">Time Estimate</label>
                        		<div class="controls">
                            		<input name="time" maxlength="50" placeholder="Enter time estimate..." type="number" min="1" class="form-control" id="time" />
                        		</div>
                        	</div>
                        	
                        	<input type="hidden" for="status" name="status" id="status" value="Active">
                        	
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