<?php
    require_once(dirname(__FILE__)."/../common.php");
    if (!isset($loginSession))
        doUnauthenticatedRedirect();

    if (!$loginSession->authenticatedEmployee->employeeType = "Admin")
        doUnauthorizedRedirect();
   
    try {
    	$projects = $db->readActiveProjects();
	} catch (Exception $ex) {
		handleDBException($ex);
        return;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
        <base href="<?php echo htmlentities(BASE_URL); ?>">
 		<title>Complete Project</title>
 		
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
		
		function showMes(message) {
			$("#messageA").text(message);
			$("#messageSuccess").show();
		}

		function doCompleteProject(form) {

			$("#completeProjectDiv").hide();
			$("#spinner").show();

			$.ajax({
				"type" : "POST",
				"url" : "Admin/doCompleteProject.php",
				"data" : $(form).serialize(),
				"dataType" : "json"
				})
				.done(function(data) {
					$("#spinner").hide();
					if (data.error != null) {
						showError(data.error);
						$("#completeProjectDiv").show();
					} else
						$("#successDiv").show();
						showMes("The project "+data.project+" had an estimate of "
							+data.timeEstimate+ " hours. The actual hours were "+data.actualTime+".");
				})
				.fail(function( jqXHR, textStatus, errorThrown ) {
					console.log("Error: "+ textStatus +" (errorThrown="+ errorThrown +")");
					console.log(jqXHR);
					$("#spinner").hide();
					$("#completeProjectDiv").show();
					showError("Request failed, unable unable to complete project: "+ errorThrown);
				})
			return false;
		} // doCompleteProject
	</script>
	</head>
 
	<body>
		<div id="messageAlert" class="alert alert-danger" style="display:none;position:fixed;left:20px;right:20px">
            <span id="message"></span>
            
        </div>


		<div id="messageSuccess" class="well well-lg" style="display:none;position:fixed;left:20px;right:20px">
            <span id="messageA"></span>
        </div>
        
   		<div class="container padded">
    		<div class="row" >
    			<div id="spinner" class="col-md-2 col-md-offset-5" style="padding-bottom:10px;text-align:center;display:none">
                    <div style="color:black;padding-bottom:32px;">Authenticating...</div>
                    <img src="spinner.gif">
                </div>
                <div id="successDiv" class="alert alert-success" style="display:none;position:fixed;left:20px;right:20px">      				
        				Project has been completed.
    			</div>
    			<div id="completeProjectDiv" class="col-md-3 col-md-offset-5" style="padding-bottom:10px; outline: 10px solid black;">
            		<form class="form-horizontal" method="post" onsubmit="return doCompleteProject(this)">
              			<input type="hidden" name="page" id="page" value="<?php echo htmlentities(@$_SERVER['PATH_INFO']); ?>"/>
               			<fieldset>
                  			<legend style="color:black;">Complete Project</legend>
                        	
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
                        	
                        	</br>
                        	</br>
                        	<div>
                        		
                        		<button class="btn btn-primary" name="commit" type="submit">
                        			<span class="glyphicon glyphicon-log-in"></span> Complete
                        		</button>
                        	</div>
                		</fieldset>
           			</form>
        		</div>
    		</div>
		</div>
	</body>
</html>