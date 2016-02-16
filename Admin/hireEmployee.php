<?php
    require_once(dirname(__FILE__)."/../common.php");
    if (!isset($loginSession))
        doUnauthenticatedRedirect();

    if (!$loginSession->authenticatedEmployee->employeeType = "Admin")
        doUnauthorizedRedirect();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
        <base href="<?php echo htmlentities(BASE_URL); ?>">
 		<title>Hire Employee</title>
 		
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
			var username = requiredField($(form.elements.username), "You must enter a username");
			var password = requiredField($(form.elements.password), "You must enter a password");
			var passwordConfirm = requiredField($(form.elements.confirmPassword), "You must confirm a password");
			var name = requiredField($(form.elements.name), "You must enter a name");
			if (password != passwordConfirm) {
				showError("Password and confirm password must match.");
				return false;
			}
			if ((username == "") || (password == "") || (password == "")) {
				showError("You must enter username and password.");
				return false;
			}

			$("#hireDiv").hide();
			$("#spinner").show();

			$.ajax({
				"type" : "POST",
				"url" : "Admin/doHireEmployee.php",
				"data" : $(form).serialize(),
				"dataType" : "json"
				})
				.done(function(data) {
					$("#spinner").hide();
					if (data.error != null) {
						showError(data.error);
						$("#hireDiv").show();
					} else
						$("#successDiv").show();
				})
				.fail(function( jqXHR, textStatus, errorThrown ) {
					console.log("Error: "+ textStatus +" (errorThrown="+ errorThrown +")");
					console.log(jqXHR);
					$("#spinner").hide();
					$("#hireDiv").show();
					showError("Request failed, unable to hire employee: "+ errorThrown);
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
                    <div style="color:black;padding-bottom:32px;">Authenticating...</div>
                    <img src="spinner.gif">
                </div>
                <div id="successDiv" class="alert alert-success" style="display:none;position:fixed;left:20px;right:20px">
        				Employee has been added. Employee can now login.
    			</div>
    			<div id="hireDiv" class="col-md-3 col-md-offset-5" style="padding-bottom:10px; outline: 10px solid black;">
            		<form class="form-horizontal" method="post" onsubmit="return doHireEmployee(this)">
              			<input type="hidden" name="page" id="page" value="<?php echo htmlentities(@$_SERVER['PATH_INFO']); ?>"/>
               			<fieldset>
                  			<legend style="color:black;">Hire</legend>
                   			<div class="control-group">
                        		<label class="control-label" for="username">Username</label>
                        		<div class="controls">
                        			<input name="username" maxlength="50" placeholder="Enter your username..." type="text" class="form-control" id="username" />
                    			</div>
                    		</div>
                			<div class="control-group">
                        		<label class="control-label" for="password">Password</label>
                        		<div class="controls">
                            		<input name="password" maxlength="50" placeholder="Enter your password..." type="password" class="form-control" id="password" />
                        		</div>
                        	</div>
                        	<div class="control-group">
                        		<label class="control-label" for="confirmPassword">Confirm Password</label>
                        		<div class="controls">
                            		<input name="confirmPassword" maxlength="50" placeholder="Confirm your password..." type="password" class="form-control" id="confirmPassword" />
                        		</div>
                        	</div>
                        	<div class="control-group">
                        		<label class="control-label" for="employeeType">Employee Type</label>
                        		<div>
                        			<select name="employeeType" id="employeeType" class="form-control col-lg-12">
										<option value="Manager">Manager</option>
										<option value="Employee">Employee</option>
									</select>
								</div>
                        	</div>
                        	
                        	<div class="control-group">
                        		<label class="control-label" for="name">Name</label>
                        		<div class="controls">
                            		<input name="name" maxlength="50" placeholder="Name..." type="name" class="form-control" id="name" />
                        		</div>
                        	</div>
                        	<input type="hidden" for="status" name="status" id="status" value="Active">
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
                        	</br>
                        	</br>
                        	<div>
                        		
                        		<button class="btn btn-primary" name="commit" type="submit">
                        			<span class="glyphicon glyphicon-log-in"></span> Hire
                        		</button>
                        	</div>
                		</fieldset>
           			</form>
        		</div>
    		</div>
		</div>
	</body>
</html>