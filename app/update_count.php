<?php
if(isset($_POST['account'])){

    require 'db.php';

    $id = $_POST['account'];
    $count = $_POST['count'];
    $cap = $_POST['cap'];
    $current = $cap - $count;
    $sql_update = "UPDATE count_info SET count_current = ?, count_available = ? WHERE count_info_ID = ?;";

    $stmt = mysqli_stmt_init($conn);

    if(!mysqli_stmt_prepare($stmt, $sql_update)){
        echo 'Something went wrong';
    }else{
        mysqli_stmt_bind_param($stmt, 'iis', $current, $count, $id);
        mysqli_stmt_execute($stmt);
    }
}
?>