<?php 
	ob_start();
	include ('dbconnect.php');

	$name = $conn->real_escape_string($_POST["name"]);
	$email = $conn->real_escape_string($_POST["email"]);
	$pwd = $conn->real_escape_string($_POST["pwd"]); 
	$pwd2 = $conn->real_escape_string($_POST["pwd2"]); 
	// echo md5($pwd.$salt);

	if($pwd!=$pwd2){
        header("Location: login-content.php?error=pwdNoMatch");
		exit();
	}

	$result = $conn->query('SELECT ID, name, is_active FROM USER_INFO WHERE email="'.$email.'"');

	$rowCount = mysqli_num_rows($result);

	if($rowCount > 0){

        header("Location: create_account.php?error=emailExist");
		exit();
	}else{

		$conn->query('INSERT INTO USER_INFO (name, email, password) VALUES ("'.$name.'","'.$email.'","'.md5($pwd.$salt).'")');

		$affected = $conn -> affected_rows;
		if($affected>0){

	        header("Location: login.php?msg=createdAccount");
			exit();
		}else{

	        header("Location: login.php?msg=letTonyKnow");
			exit();
		}
	}



?>