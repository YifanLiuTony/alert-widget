<?php
	session_start();
	include('dbconnect.php');

	if(!empty($_GET["vendor_name"])){
        date_default_timezone_set('America/Los_Angeles');
		$vendorName = stripslashes($_GET["vendor_name"]);
		$query = 'select id,vendor,amount_due,ref_num,memo,due_date from ALERT_DETAIL WHERE uid="'.$_SESSION['id'].'" AND vendor = "'.$vendorName.'" AND due_date<='.date('Ymd').' AND is_done=0 ORDER BY due_date';
		$result = $conn->query($query);
		$returnList = [];
		while($row = $result->fetch_assoc()){
			$returnList[] = $row;
		}
		echo json_encode($returnList);
	}else{
		echo 'Something wrong happened, no vendor name provided.';
	}
?>