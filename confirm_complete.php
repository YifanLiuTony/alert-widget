<?php
	session_start();
	include ('dbconnect.php');
	// include ('helpers.php');

	if(!empty($_POST["ref_num"])){
		$refNums = json_decode(stripslashes($_POST["ref_num"]));
		$refsImploded = implode('","',$refNums);
		// $refsImploded = '"'.$refsImploded.'"';
		// $refNums = $conn->real_escape_string($_POST["ref_num"]);
		
		$conn->query('UPDATE ALERT_DETAIL SET is_done=1 WHERE ref_num IN ("'.$refsImploded.'") AND uid="'.$_SESSION['id'].'"');
		$affected = $conn->affected_rows;

		if($affected>0){
			echo "Success! Please refresh the page by clicking on 'OK' to apply new change.";
		}else{
			// echo  'UPDATE ALERT_DETAIL SET is_done=1 WHERE ref_num IN ("'.$refsImploded.'") AND uid="'.$_SESSION['id'].'"';
			echo 'Update failed, let Tony know about this';
		}
	}else{
		echo "Invalid Ref #, let Tony know about this.";
	}
?>