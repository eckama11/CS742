<?php
    require_once(dirname(__FILE__)."/../common.php");
    if (!isset($loginSession))
        doUnauthenticatedRedirect();

    if (!$loginSession->authenticatedEmployee->employeeType = "Admin")
        doUnauthorizedRedirect();
        
    $employeeId = @$_GET['id'];
    $rv = (Object)[];
	try {
		$emp = $db->readEmployee($employeeId);
		$name = $emp->name;
		$rv->success = true;
	} catch (Exception $ex) {
		$rv->error = $ex->getMessage();
	}//try/catch
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
        <base href="<?php echo htmlentities(BASE_URL); ?>">
 		<title>Move Employee</title>
 		
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

		function doMoveEmployee(form) {
			$("#moveDiv").hide();
			$("#spinner").show();
			
			$.ajax({
				"type" : "POST",
				"url" : "Admin/doMoveEmployee.php",
				"data" : $(form).serialize(),
				"dataType" : "json"
				})
				.done(function(data) {
					$("#spinner").hide();
					if (data.error != null) {
						showError(data.error);
						$("#moveDiv").show();
					} else
						$("#successDiv").show();
				})
				.fail(function( jqXHR, textStatus, errorThrown ) {
					console.log("Error: "+ textStatus +" (errorThrown="+ errorThrown +")");
					console.log(jqXHR);
					$("#spinner").hide();
					$("#moveDiv").show();
					showError("Request failed, unable to move employee: "+ errorThrown);
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
        					$empD = $db->readEmployee($employeeId);
        					echo htmlentities($empD->name);
        				?>'s division has been updated to 
        				<?php
        					echo htmlentities($empD->division);
        				?>
        				.
    				</div>
    			<div id="moveDiv" class="col-md-3 col-md-offset-5" style="padding-bottom:10px; outline: 10px solid black;">
            		<form class="form-horizontal" method="post" onsubmit="return doMoveEmployee(this)">
              			<input type="hidden" name="page" id="page" value="<?php echo htmlentities(@$_SERVER['PATH_INFO']); ?>"/>
               			<fieldset>
                  			<legend style="color:black;">Update Division</legend>
                   			
                        	<div class="control-group">
                        		<label class="control-label" for="name">Name</label>
                        		<div class="controls">
                            		<label class="control-label" for="name"><?php echo htmlentities($name); ?></label>
                        		</div>
                        	</div>
                        	
                        	<div class="control-group">
                        		<label class="control-label" for="division">Division</label>
                        		<div>
                        			<select name="division" id="division" class="form-control col-lg-12">
										<option value="divA">divA</option>
										<option value="divB">divB</option>
										<option value="divC">divC</option>
										<option value="divD">divD</option>
									</select>
								</div>
                        	</div>
                        	
                        	<input type="hidden" for="id" name="id" id="id" value="<?php echo htmlentities($employeeId); ?>">
                        	</br>
                        	</br>
                        	<div>
                        		<button class="btn btn-primary" name="commit" type="submit">
                        			<span class="glyphicon glyphicon-log-in"></span> Move
                        		</button>
                        	</div>
                		</fieldset>
           			</form>
        		</div>
    		</div>
		</div>
	</body>
</html>