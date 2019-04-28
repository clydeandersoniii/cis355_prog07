<?php
	session_start();
	if(isset($_SESSION["tJHSQRuoNnWUwLR"]))
	{
		header("Location: action_redirect.php");
	}
	$signInError = $_SESSION["signInError"];
	$addError = $_SESSION["addError"];
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<link rel='icon' href='track_and_field.png' type='image/png'/>
        <meta charset='UTF-8'>
        <link href='https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css' rel='stylesheet'>
        <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js'></script>
	</head>
	<title>Track & Field Result Log</title>
<body>
	<h1 style="float: center; margin-left: 195px;">
		<img width=100 src="track_and_field.png"/>
		Track & Field Result Log
	</h1>
	<br>
    <div class="container">
		<div class="span10 offset1">

			<div class="row">
				<h3>Login</h3>
			</div>

			<form class="form-horizontal" action="action_redirect.php?fun=login" method="post">
								  
				<div class='form-group'><label class='control-label'>Email &nbsp;</label><input name='email' type='text' autofocus placeholder='email@domain.com' value=''></div>				  
				
				<div class='form-group'><label class='control-label'>Password &nbsp;</label><input name='password' type='password' autofocus placeholder='Password' value=''></div>
				

				<div class="form-actions">
					<button type="submit" class="btn btn-success" value='signin' name='signin'>Sign in</button>
				</div>
				
				<div>
					<?php
						echo "<br>";
						echo "<span style='color: red;' class='help-inline'>";
						echo "&nbsp;&nbsp;" . $signInError;
						echo "</span>";
						echo "<br>";
					?>
				</div>
				
			</form>
			
			<div class="row">
				<h3>Join</h3>
			</div>
			
			<form class="form-horizontal" action="action_redirect.php?fun=add_user" method="post">
								  
				<div class='form-group'><label class='control-label'>Email &nbsp;</label><input name='email' type='text' autofocus placeholder='email@domain.com' value=''></div>

				<div class='form-group'><label class='control-label'>Retype Email &nbsp;</label><input name='retypeEmail' type='text' autofocus placeholder='email@domain.com' value=''></div>	
				
				<div class='form-group'><label class='control-label'>Password &nbsp;</label><input name='password' type='password' autofocus placeholder='Password' value=''></div>
				

				<div class="form-actions">
					<button type="submit" class="btn btn-success" value='join' name='join'>Join (New Volunteer)</button>
				</div>
				
				<div>
					<?php
						echo "<br>";
						echo "<span style='color: red;' class='help-inline'>";
						echo "&nbsp;&nbsp;" . $addError;
						echo "</span>";
						echo "<br>";
					?>
				</div>
				
				<footer>
					<small>&copy; Copyright 2019, Clyde E. Anderson, III
					</small>
				</footer>
				
			</form>

		</div> <!-- end div: class="span10 offset1" -->
				
    </div> <!-- end div: class="container" -->
	
  </body>
  
</html>