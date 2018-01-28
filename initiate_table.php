<?php
        include ('dbconnect.php');

        date_default_timezone_set('America/Los_Angeles');
        $date_striped = (int) date('Ymd');
        $date = date('Y/M/d');

        $result = $conn->query('SELECT description, amount_due, ref_num, memo FROM ALERT_DETAIL WHERE uid="'.$_SESSION['id'].'" AND due_date='.$date_striped.' AND is_done=0 ORDER BY description');

        $rowCount = mysqli_num_rows($result);
?>