<?php 
    session_start();
	include ('dbconnect.php');
	if(!empty($_POST['data'])) {
	    
	    $data = json_decode($_POST['data']);
	    // echo '<pre>'; print_r($data); echo '</pre>';
	    $id = $_SESSION['id'];
	    $sql = array(); 

	    // upload alert info records
	    $counter = 0;
		foreach( $data[0] as $row ) {
			if($counter>0 && !empty($row[0])){
				// extract correct columns
				$amount_due_num = floatval(str_replace(str_split('$,'), '', $row[3]));
				$due_date = str_replace('/','-',$row[4]);
			    $sql[] = '('.$id.',"'.$row[0].'","'.$row[2].'","'.$row[3].'","'.$amount_due_num.'",CAST("'.$due_date.'" AS DATE),CAST("'.$due_date.'" AS DATE),"'.$row[6].'")';
			}
			$counter++;
		}

		// ignore duplicates 
		$conn->query('INSERT IGNORE INTO ALERT_DETAIL (uid, vendor, ref_num, amount_due, amount_due_num, due_date, orginal_due_date, memo) VALUES '.implode(',', $sql));

		$affected = $conn -> affected_rows;
		if($affected>0){
			echo 'Sheet 1 status: Upload success! '.$affected.' rows inserted.'."\n";
		}else{
			echo 'Sheet 1 status: Upload failed. This is probably because all of the rows are considered duplicates (i.e. the corresponding ref# already exist in database). Otherwise, send this file to Tony along with the following error message: '.mysqli_error($conn)."\n";
		}

		// upload threshold records
	    $sql = array(); 
	    $counter = 0;
		foreach( $data[1] as $row ) {
			if($counter>0 && !empty($row[0])){
				// extract correct columns
				$amount_due_num = floatval(str_replace(str_split('$,'), '', $row[1]));
			    $sql[] = '('.$id.',"'.$row[0].'","'.$amount_due_num.'")';
			}
			$counter++;
		}

		// ignore duplicates 
		$conn->query('REPLACE INTO THRESHOLD_INFO (uid, vendor, threshold) VALUES '.implode(',', $sql));

		$affected = $conn -> affected_rows;

		if($affected>0){
			echo 'Sheet 2 status: Upload success! '.$affected.' rows inserted.';
		}else{
			echo 'Sheet 2 status: Upload failed. This is probably because all of the rows are considered duplicates (i.e. the corresponding ref# already exist in database). Otherwise, send this file to Tony along with the following error message: '.mysqli_error($conn);
		}
	}

?>	