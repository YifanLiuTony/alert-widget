<?php
	session_start();
	if ($_SESSION["logged_in"] == 'true') {
		header("Location: index.php");
		exit();
	}
?>
<html>
    <head>
        <title>AP Alert System - Login</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="icon" type="image/png" href="img/logo.png" />

        <script src="vendor/jquery.min.js"></script>
        <script src="vendor/bootstrap.min.js"></script>
        <link rel="stylesheet" media="screen" href="vendor/bootstrap.min.css">
        <link rel="stylesheet" media="screen" href="css/alert-system.css">

        <style type="text/css">

            .outer {
                display: table;
                position: absolute;
                height: 100%;
                width: 100%;
            }

            .middle {
                display: table-cell;
                vertical-align: middle;
            }

            .inner {
                margin-left: auto;
                margin-right: auto; 
                width: 400px;
            }
        </style>
    </head>
    <body>
    	<div class="container outer">
    		<div class="row middle">
    			<div class="panel panel-default inner">
    				<?php $msg = $_GET['msg']; ?>
					<div class="alert alert-danger" style="<?php if($msg!='invalidpwd') echo 'display: none;' ?>">
						Incorrect email/password, please try again.
					</div>
					<div class="alert alert-danger" style="<?php if($msg!='pleaseLogin') echo 'display: none;' ?>">
						Please log in first.
					</div>
					<div class="alert alert-danger" style="<?php if($msg!='letTonyKnow') echo 'display: none;' ?>">
						Something went wrong, let Tony know.
					</div>
					<div class="alert alert-success" style="<?php if($msg!='createdAccount') echo 'display: none;' ?>">
						Account created successfully, please log in.
					</div>
					<div class="alert alert-success" style="<?php if($msg!='loggedOut') echo 'display: none;' ?>">
						You have been successfully logged out.
					</div>
					<div class="panel-body">

						<form action="login_action.php" method="post">
						  <div class="form-group">
						    <label for="email">Email address:</label>
						    <input type="email" class="form-control" id="email" name="email" required="true">
						  </div>
						  <div class="form-group">
						    <label for="pwd">Password:</label>
						    <input type="password" class="form-control" id="pwd" name="pwd" required="true">
						  </div>
						  <br/>
						  <a onclick="window.location.replace('create_account.php');" class="btn btn-primary">Create Account</a>
						  <button type="submit" class="btn btn-primary" style="float: right;">Submit</button>
						</form>
					</div>
				</div>
    		</div>
    	</div>
	</body>
</html>