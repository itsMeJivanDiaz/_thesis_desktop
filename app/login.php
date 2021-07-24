<?php

if(isset($_POST['user-login'])){
    
    require 'db.php';

    $username = $_POST['user-login'];
    $password = $_POST['pass-login'];
    
    $sql_check_user = "SELECT * FROM account WHERE acc_user = ?;";

    $sql_get_det = "SELECT * FROM establishment WHERE est_acc_ID = ?;";

    $sql_join = "SELECT * FROM establishment INNER JOIN location ON establishment.est_loc_ID = location.loc_loc_ID INNER JOIN account ON account.acc_acc_ID = establishment.est_acc_ID
    INNER JOIN count_info ON count_info_ID = est_count_info_ID WHERE est_loc_ID = ? AND est_acc_ID = ? AND est_count_info_ID = ?;";

    $stmt = mysqli_stmt_init($conn);
    
    if(!mysqli_stmt_prepare($stmt, $sql_check_user)){
        echo 'Something went wrong';
    }else{
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $ver_pass = $row['acc_pass'];
        if($row != 0){
            $verify_pass = password_verify($password, $ver_pass);
            if ($verify_pass == true){
                $acc_id = $row['acc_acc_ID'];
                if(!mysqli_stmt_prepare($stmt, $sql_get_det)){
                    echo 'Something went wrong';
                }else{
                    mysqli_stmt_bind_param($stmt, 's', $acc_id);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $row_ = mysqli_fetch_assoc($result);
                    $est_loc_id = $row_['est_loc_ID'];
                    $est_count_ID = $row_['est_count_info_ID'];
                    if(!mysqli_stmt_prepare($stmt, $sql_join)){
                        echo 'Something went wrong';
                    }else{
                        mysqli_stmt_bind_param($stmt, 'sss', $est_loc_id, $acc_id, $est_count_ID);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        $row_1 = mysqli_fetch_assoc($result);
                        echo json_encode(array(
                            'status'=> 'Log-in Success',
                            'id' => $row_1['acc_acc_ID'],
                            'name' => $row_1['est_name'],
                            'type' => $row_1['est_type'],
                            'logo' => $row_1['acc_logo'],
                            'normal_capacity' => $row_1['count_normal_capacity'],
                            'current_count' => $row_1['count_current'],
                            'allowable' => $row_1['count_allowable_capacity'] * $row_1['count_normal_capacity'],
                            'city' => $row_1['loc_city'],
                            'branch' => $row_1['loc_branch_str'],
                            'brgy' => $row_1['loc_brgy'],
                            'lat' => $row_1['loc_lat'],
                            'long' => $row_1['loc_long'],
                        ));
                    }
                }
            }else{
                echo json_encode(array(
                    'status'=> 'Log-in Denied',
                    'data' => null,
                ));
            }
        }else{
            echo json_encode(array(
                'status'=> 'Log-in Denied',
                'data' => null,
            ));
        }
    }

}

?>