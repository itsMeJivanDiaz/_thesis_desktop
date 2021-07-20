<?php
date_default_timezone_set('Asia/Manila');
if(isset($_POST['cap'])){

    require 'db.php';

    $id = $_POST['id'];
    $normal_capacity = $_POST['cap'];
    $limitation = $_POST['lim'];

    $sql_select = "SELECT * FROM establishment WHERE est_acc_ID = ?;";
    $sql_update = "UPDATE count_info SET count_normal_capacity = ?, count_allowable_capacity = ? WHERE count_info_ID = ?;";
    $stmt = mysqli_stmt_init($conn);

    if(!mysqli_stmt_prepare($stmt, $sql_select)){
        echo 'Something went wrong';
    }else{
        mysqli_stmt_bind_param($stmt, 's', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $count_id = $row['est_count_info_ID'];
        if(!mysqli_stmt_prepare($stmt, $sql_update)){
            echo 'Something went wrong';
        }else{
            mysqli_stmt_bind_param($stmt, 'ids', $normal_capacity, $limitation, $count_id);
            mysqli_stmt_execute($stmt);
            echo 'Success';
        }
    }
}

?>