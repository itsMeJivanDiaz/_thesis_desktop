<?php
header('Access-Control-Allow-Origin: *');
require 'db.php';
if(isset($_POST['new'])){
    
    $stmt = mysqli_stmt_init($conn);

    $id = $_POST['acc_id'];
    $newpass = $_POST['new'];
    $oldpass = $_POST['old'];

    $sql_select = "SELECT * FROM account WHERE acc_acc_ID = ?;";
    $sql_update = "UPDATE account SET acc_pass = ?;";

    if(!mysqli_stmt_prepare($stmt, $sql_select)){
        echo json_encode(array(
            'status' => 'Error',
        ));
    }else{
        mysqli_stmt_bind_param($stmt, 's', $id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($res);
        if(password_verify($oldpass, $row['acc_pass'])){
            if(!mysqli_stmt_prepare($stmt, $sql_update)){
                echo json_encode(array(
                    'status' => 'Error',
                ));
            }else{
                $newhash = password_hash($newpass, PASSWORD_DEFAULT);
                mysqli_stmt_bind_param($stmt, 's', $newhash);
                mysqli_stmt_execute($stmt);
                echo json_encode(array(
                    'status' => 'Success',
                ));
            }
        }else{
            echo json_encode(array(
                'status' => 'Error',
            ));
        }
    }

}
?>