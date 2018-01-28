

	<?php 
		$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		
		if (strpos($actual_link, 'localhost') !== false) {
			$user = 'root';
			$password = 'root';
			$db = 'tracy_alert_widget';
		}else if(strpos($actual_link, 'tonyliudemo.site') !== false){
			$user = 'liutony778';
			$password = 'aiu14rB59!on';
			$db = 'liutony7_alert_widget';
		}

		$host = 'localhost';
		$port = 8889;

		$conn = mysqli_init();
		$success = mysqli_real_connect(
		   $conn, 
		   $host, 
		   $user, 
		   $password, 
		   $db,
		   $port
		);

		if ($conn->connect_error) {
		    die("Connection failed: " . $conn->connect_error);
		} 
		
		$salt = 'XISJ9D83JR$KQ920DK59';
	?>