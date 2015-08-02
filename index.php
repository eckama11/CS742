<?php
require_once("common.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
        <base href="<?php echo htmlentities(BASE_URL); ?>">
 		<title>Login</title>
 		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<!-- StyleSheet -->
		<link rel="stylesheet" href="css/bootstrap.min.css" />
		<link rel="stylesheet" href="css/custom.css" />
		<style>
        input { max-width: 100%; }
        </style>
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

		function doLogin(form) {
			var username = requiredField($(form.elements.username), "You must enter a username");
			var password = requiredField($(form.elements.password), "You must enter a password");
			if ((username == "") || (password == "")) {
				showError("You must enter both a username and a password.");
				return false;
			}

			$("#loginDiv").hide();
			$("#spinner").show();

			$.ajax({
				"type" : "POST",
				"url" : "doLogin.php",
				"data" : $(form).serialize(),
				"dataType" : "json"
				})
				.done(function(data) {
					if (data.error != null) {
						$("#spinner").hide();
						showError(data.error);
						$(form.elements.password).val('');
						$("#loginDiv").show();
					} else
						document.location.href = data.redirect;
				})
				.fail(function( jqXHR, textStatus, errorThrown ) {
					console.log("Error: "+ textStatus +" (errorThrown="+ errorThrown +")");
					console.log(jqXHR);

					$("#spinner").hide();
					$("#loginDiv").show();
					showError("Request failed, unable to login: "+ errorThrown);
				})
			return false;
		} // doLogin
	</script>
	</head>
 
	<body>
		<div class="navbar navbar-inverse navbar-static-top">
			<div class="container">
				<a href="#" class="navbar-brand">
				<span class="glyphicon glyphicon-hourglass"></span>
				Project Tracker</a>
				
				<div class="collapse navbar-collapse navHeaderCollapse">
					<ul class="nav navbar-nav navbar-right">
					</ul>
				</div>
			</div>
		</div>

        <div id="messageAlert" class="alert alert-danger" style="display:none;position:fixed;left:20px;right:20px">
            <span id="message"></span>
        </div>

   		<div class="container padded">
    		<div class="row" >
    			<div id="spinner" class="col-md-2 col-md-offset-5" style="padding-bottom:10px;text-align:center;display:none">
                    <div style="color:black;padding-bottom:32px;">Authenticating...</div>
                    <img src="spinner.gif">
                </div>
    			<div id="loginDiv" class="col-md-3 col-md-offset-5" style="padding-bottom:10px; outline: 10px solid black;">
            		<form class="form-horizontal" method="post" onsubmit="return doLogin(this)">
              			<input type="hidden" name="page" id="page" value="<?php echo htmlentities(@$_SERVER['PATH_INFO']); ?>"/>
               			<fieldset>
                  			<legend style="color:black;">Login</legend>
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
                        	</br>
                        	<div>
                        		
                        		<button class="btn btn-primary" name="commit" type="submit">
                        		<span class="glyphicon glyphicon-log-in"></span> Login
                        		</button>
                        	</div>
                		</fieldset>
           			</form>
        		</div>
    		</div>
		</div>

		<!-- JavaScript -->
		<script src="js/jquery-2.1.4.min.js"></script>
		<script src="js/bootstrap.js"></script>
        <script>
            $('#username').focus();
        </script>
	</body>
</html>
	