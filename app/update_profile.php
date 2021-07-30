<?php
header('Access-Control-Allow-Origin: *');
require 'db.php';
if(isset($_POST['name'])){

    $id = $_POST['acc_id'];
    $name = $_POST['name'];
    $str = $_POST['street'];
    $brgy = $_POST['brgy'];
    $city =  $_POST['city'];
    $type = $_POST['type'];
    $coords = explode(', ', $_POST['coords']);
    if(sizeof($coords) != 2){
        echo json_encode(array(
            'status' => 'Your lcoation is incomplete.',
        ));
        return false;
    }

    $stmt = mysqli_stmt_init($conn);

    $sql_select = "SELECT * FROM establishment WHERE est_acc_ID = ?;";
    $sql_update_est = "UPDATE establishment SET est_name = ?, est_type = ? WHERE est_acc_ID = ?;";
    $sql_update_loc = "UPDATE location SET loc_city = ?, loc_branch_str = ?, loc_brgy = ?, loc_lat = ?, loc_long = ? WHERE loc_loc_ID = ?;";

    if(!mysqli_stmt_prepare($stmt, $sql_select)){
        echo json_encode(array(
            'status' => 'Something went wrong',
        ));
    }else{
        mysqli_stmt_bind_param($stmt, 's', $id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($res);
        if(!mysqli_stmt_prepare($stmt, $sql_update_est)){
            echo json_encode(array(
                'status' => 'Something went wrong',
            ));
        }else{
            mysqli_stmt_bind_param($stmt, 'sss', $name, $type, $id);
            mysqli_stmt_execute($stmt);
            if(!mysqli_stmt_prepare($stmt, $sql_update_loc)){
                echo json_encode(array(
                    'status' => 'Something went wrong',
                ));
            }else{
                mysqli_stmt_bind_param($stmt, 'sssdds', $city, $str, $brgy, $coords[0], $coords[1], $row['est_loc_ID']);
                mysqli_stmt_execute($stmt);
                echo json_encode(array(
                    'status' => 'Update Success.',
                    'new_name' => $name,
                    'new_str' => $str,
                    'new_brgy' => $brgy,
                    'new_city' => $city,
                    'new_type' => $type,
                    'new_lat' => $coords[0],
                    'new_long' => $coords[1],
                ));
            }
        }
    }
}
?>