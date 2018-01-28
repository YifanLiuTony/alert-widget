<?php 
	ob_start();
	include ('dbconnect.php');

	$email = $conn->real_escape_string($_POST["email"]);
	$pwd = $conn->real_escape_string($_POST["pwd"]); 

	$result = $conn->query('SELECT ID, name FROM USER_INFO WHERE email="'.$email.'" AND password="'.md5($pwd.$salt).'" AND is_active=1');

	$rowCount = mysqli_num_rows($result);
		// echo $rowCount;

	if($rowCount > 0){
		$firstRow = mysqli_fetch_array($result);
		
		session_start();
        $_SESSION["logged_in"] = 'true'; 
        $_SESSION["id"] = $firstRow['ID']; 
        $_SESSION["name"] = $firstRow['name']; 
        $_SESSION["email"] = $email; 
        $_SESSION["error"] = ''; 

        header("Location: index.php");
		exit();
	}else{
        header("Location: login.php?msg=invalidpwd");
		exit();
	}



?>