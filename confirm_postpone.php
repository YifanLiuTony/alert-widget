<?php
	session_start();
	include ('dbconnect.php');

	if(!empty($_POST["ref_num"]) && !empty($_POST["dates"])){
		$refNums = $conn->real_escape_string($_POST["ref_num"]);
		$dates = $conn->real_escape_string($_POST["dates"]);

		$refsImploded = implode('","',json_decode(stripslashes($refNums)));
		$query = 'UPDATE ALERT_DETAIL SET due_date="'.$dates.'" WHERE ref_num IN ("'.$refsImploded.'") AND uid="'.$_SESSION['id'].'"';
		$conn->query($query);
		$affected = $conn->affected_rows;

		if($affected>0){
			echo "Success!";
		}else{
			echo 'Update failed, let Tony know about the following: '.$query;
		}
	}else{
		echo "Invalid Ref # or dates, let Tony know about this.";
	}
?>