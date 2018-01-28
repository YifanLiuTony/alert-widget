<?php
	session_start();
	ob_start();
	include ('dbconnect.php');

	if(!empty($_POST["ref_num"]) && !empty($_POST["dates"])){
		$refNums = $conn->real_escape_string($_POST["ref_num"]);
		$dates = $conn->real_escape_string($_POST["dates"]);

		$refsImploded = str_replace(',','","',$refNums);

			// echo 'UPDATE ALERT_DETAIL SET due_date="'.$dates.'" WHERE ref_num IN ("'.$refsImploded.'") AND uid="'.$_SESSION['id'].'"';
		$conn->query('UPDATE ALERT_DETAIL SET due_date="'.$dates.'" WHERE ref_num IN ("'.$refsImploded.'") AND uid="'.$_SESSION['id'].'"');
		$affected = $conn->affected_rows;

		$_SESSION['type'] = 'postpone';
		$_SESSION['refNum'] = $refNums;

		if($affected>0){
			$_SESSION['newDate'] = $dates;
			// header('Location: index.php?refNum='.$refNums.'&type=postpone&newDate='.$dates);
			header('Location: index.php');
			exit();
		}else{
			$_SESSION['hasError'] = true;
			// header('Location: index.php?refNum='.$refNums.'&type=postpone&newDate=0');
			header('Location: index.php');
			exit();
		}
	}else{
		header('Location: index.php');
		exit();
	}
?>