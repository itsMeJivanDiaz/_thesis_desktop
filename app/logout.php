<?php

if(isset($_POST['data'])){

    require 'db.php';

    $ref_id = $_POST['data'];

    $stmt = mysqli_stmt_init($conn);

    $sql_update = "UPDATE count_info SET count_current = ?, count_available = ? WHERE count_info_ID = ?;";

    $sql_select = "SELECT * FROM establishment WHERE est_acc_ID = ?;";

    $zero = 0;

    if(!mysqli_stmt_prepare($stmt, $sql_select)){
        echo 'Something went wrong';
    }else{
        mysqli_stmt_bind_param($stmt, 's', $ref_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $count_id = $row['est_count_info_ID'];
        if(!mysqli_stmt_prepare($stmt, $sql_update)){
            echo 'Something went wrong';
        }else{
            mysqli_stmt_bind_param($stmt, 'iis', $zero, $zero, $count_id);
            mysqli_stmt_execute($stmt);
            echo 'Success';
        }
    }
}

?>