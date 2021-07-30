<?php
header('Access-Control-Allow-Origin: *');
require 'db.php';
if($_FILES['file'] != ''){

    $id = $_POST['acc_id'];
    $file = $_FILES['file'];
    $fileName = $_FILES['file']['name'];
    $fileTmpName = $_FILES['file']['tmp_name'];
    $filesize = $_FILES['file']['size'];
    $fileError = $_FILES['file']['error'];
    $fileType = $_FILES['file']['type'];
    $fileExt = explode('.', $fileName);
    $fileActExt = strtolower(end($fileExt));

    $allowed = array('jpg', 'jpeg', 'png', 'gif');

    $sql_update = "UPDATE account SET acc_logo = ? WHERE acc_acc_ID = ?;";

    $stmt = mysqli_stmt_init($conn);

    if(in_array($fileActExt, $allowed)){
        if($fileError === 0){
            if($filesize <= 10000000){
                $fileNewName = uniqid('', true).".".$fileActExt;
                $fileDestination = '../uploads/'.$fileNewName;
                if(!mysqli_stmt_prepare($stmt, $sql_update)){
                    echo json_encode(array(
                        'status' => 'Something went wrong',
                    ));
                }else{
                    mysqli_stmt_bind_param($stmt, 'ss', $fileNewName, $id);
                    mysqli_stmt_execute($stmt);
                    move_uploaded_file($fileTmpName, $fileDestination);
                    echo json_encode(array(
                        'status' => 'Update Success',
                        'data_url' => $fileNewName,
                    ));
                }
            }else{
                echo json_encode(array(
                    'status' => 'File is too big',
                ));
            }
        }else{
            echo json_encode(array(
                'status' => 'File might be corrupted',
            ));
        }
    }else{
        echo json_encode(array(
            'status' => 'File type is not allowed',
        ));
    }

}
?>