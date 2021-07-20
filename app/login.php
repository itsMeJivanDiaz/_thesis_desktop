<?php

if(isset($_POST['user-login'])){
    
    require 'db.php';

    $username = $_POST['user-login'];
    $password = $_POST['pass-login'];
    
    $sql_check_user = "SELECT * FROM account WHERE acc_user = ?;";

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
                echo json_encode(array(
                    'status'=> 'Log-in Success',
                    'data' => $row['acc_acc_ID'],
                ));
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