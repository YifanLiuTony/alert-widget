<html>
    <head>
        <title>Create Account</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

        <script src="vendor/jquery.min.js"></script>
        <script src="vendor/bootstrap.min.js"></script>
        <link rel="stylesheet" media="screen" href="vendor/bootstrap.min.css">
        <link rel="stylesheet" media="screen" href="css/alert-system.css">
        
    </head>
    <body>
    	<div class="container outer">
    		<div class="row middle">
    			<div class="panel panel-default inner">
					<div class="alert alert-danger" style="<?php if($_GET['error']!='emailExist') echo 'display: none;' ?>">
						This email already exists, please login <a href="login.php">here</a> or select a different one.
					</div>
					<div class="panel-body">

						<form action="create_account_action.php" method="post">
						  <div class="form-group">
						    <label for="name">Preferred Name</label>
						    <input type="text" class="form-control" id="name" name="name" required="true">
						  </div>
						  <div class="form-group">
						    <label for="email">Email address:</label>
						    <input type="email" class="form-control" id="email" name="email" required="true">
						  </div>
						  <div class="form-group">
						    <label for="pwd">Password:</label>
						    <input type="password" class="form-control" id="pwd" name="pwd" required="true">
						  </div>
						  <div class="form-group">
						    <label for="pwd">Confirm Password:</label>
						    <input type="password" class="form-control" id="pwd2" name="pwd2" required="true">
						  </div>
						  <br/>
						  <button type="submit" class="btn btn-primary" style="float: right;">Create Account</button>
						</form>
					</div>
				</div>
    		</div>
    	</div>
	</body>
</html>