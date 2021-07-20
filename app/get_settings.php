<?php

if(isset($_POST['id'])){

    require 'db.php';
    $ref_id = $_POST['id'];

    $sql_get_settings = "SELECT * FROM count_info WHERE count_info_ID = ?;";
    $sql_get_id = "SELECT * FROM establishment WHERE est_acc_ID = ?;";

    $stmt = mysqli_stmt_init($conn);

    if(!mysqli_stmt_prepare($stmt, $sql_get_id)){
        echo 'Something went wrong';
    }else{
        mysqli_stmt_bind_param($stmt, 's', $ref_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $count_id = $row['est_count_info_ID'];
        if(!mysqli_stmt_prepare($stmt, $sql_get_settings)){
            echo 'Something went wrong';
        }else{
            mysqli_stmt_bind_param($stmt, 's', $count_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);
            $normal = $row['count_normal_capacity'];
            $limit = $row['count_allowable_capacity'];
            $max_limit = $normal * $limit;
            echo json_encode(array(
                'max_limit'=> $max_limit,
                'count_id' => $count_id,
            ));
        }
    }

}

?>