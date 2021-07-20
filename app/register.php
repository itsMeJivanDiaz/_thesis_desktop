<?php
date_default_timezone_set('Asia/Manila');

if(isset($_POST['name'])){
    require 'db.php';
    function random_strings($length_of_string) { 
    $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'; 
    return substr(str_shuffle($str_result),  0, $length_of_string); 
    } 

    $uniqid_gen_cnt = uniqid('IDcnt=');
    $uniqid_gen_loc = uniqid('IDloc=');
    $uniqid_gen_acc = uniqid('IDacc=');
    $uniqid_gen_est = uniqid('IDest=');
    $uid_cnt = $uniqid_gen_cnt . random_strings(11);
    $uid_acc = $uniqid_gen_acc . random_strings(11);
    $uid_loc = $uniqid_gen_loc . random_strings(11);
    $uid_est = $uniqid_gen_est . random_strings(11);
    $name = $_POST['name'];
    $city = $_POST['city'];
    $branch = $_POST['branch'];
    $barangay = $_POST['brgy'];
    $type = $_POST['type'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $latlong = $_POST['latlong'];
    $coords = explode(',', $latlong);
    $date_time = date('Y-m-d h:i:s');
    $count = 0; 
    $lat = $coords[0];
    $long = $coords[1];

    $sql_account = "INSERT INTO `account` (`acc_acc_ID`, `acc_logo`, `acc_user`, `acc_pass`, `acc_date_time_cr`) VALUES (?, ?, ?, ?, ?);";

    $sql_count = "INSERT INTO `count_info` (`count_info_ID`, `count_allowable_capacity`, `count_current`, `count_available`, `count_date_time_cr`)
        VALUES (?, ?, ?, ?, ?);";

    $sql_location = "INSERT INTO `location` (`loc_loc_ID`, `loc_city`, `loc_branch_str`, `loc_brgy`, `loc_lat`, `loc_long`, `loc_date_time_cr`) VALUES (?, ?, ?, ?, ?, ?, ?);";

    $sql_establishment = "INSERT INTO `establishment` (`est_ID`, `est_name`, `est_type`, `est_count_info_ID`, `est_loc_ID`, `est_acc_ID`, `est_date_time_cr`) VALUES (?, ?, ?, ?, ?, ?, ?);";

    $sql_check = "SELECT * FROM account WHERE acc_user = ?;";

    $stmt = mysqli_stmt_init($conn);

    function check_user($process, $db, $statement, $user){
       
        if(!mysqli_stmt_prepare($statement, $process)){
            return false;
        }else{
            mysqli_stmt_bind_param($statement, 's', $user);
            mysqli_stmt_execute($statement);
            $res = mysqli_stmt_get_result($statement);
            if(mysqli_fetch_assoc($res) > 0){
                return true;
            }else{
                return false;
            }
        }

    }

    function register_account($process, $db, $statement, $id, $logo, $user, $pass, $dt){

        $hash_pass = password_hash($pass, PASSWORD_DEFAULT);

        if(!mysqli_stmt_prepare($statement, $process)){
            return false;
        }else{
            mysqli_stmt_bind_param($statement, "sssss", $id, $logo, $user, $hash_pass, $dt);
            mysqli_stmt_execute($statement);
            return true;
        }
    }

    function register_count($process, $db, $statement, $id, $init, $dt){

        if(!mysqli_stmt_prepare($statement, $process)){
            return false;
        }else{
            mysqli_stmt_bind_param($statement, "siiis", $id, $init, $init, $init, $dt);
            mysqli_stmt_execute($statement);
            return true;
        }
    }

    function register_location($process, $db, $statement, $id, $city, $branch, $barangay, $lat, $long, $dt){

        if(!mysqli_stmt_prepare($statement, $process)){
            return false;
        }else{
            mysqli_stmt_bind_param($statement, "ssssdds", $id, $city, $branch, $barangay, $lat, $long, $dt);
            mysqli_stmt_execute($statement);
            return true;
        }
    }

    function register_establishment($process, $db, $statement, $id, $est_name, $est_type, $est_count_info_id, $est_loc_id, $est_acc_id, $dt){

        if(!mysqli_stmt_prepare($statement, $process)){
            return false;
        }else{
            mysqli_stmt_bind_param($statement, "sssssss", $id, $est_name, $est_type, $est_count_info_id, $est_loc_id, $est_acc_id, $dt);
            mysqli_stmt_execute($statement);
            return true;
        }
    }

    $_process = check_user($sql_check, $conn, $stmt, $username);

    if($_process == false){
        $first_p = register_account($sql_account, $conn, $stmt, $uid_acc, 'None', $username, $password, $date_time);
        if($first_p == true){
            $second_p = register_count($sql_count, $conn, $stmt, $uid_cnt, $count, $date_time);
            if($second_p == true){
                $third_p = register_location($sql_location, $conn, $stmt, $uid_loc, $city, $branch, $barangay, $lat, $long, $date_time);
                if($third_p == true){
                    $last = register_establishment($sql_establishment, $conn, $stmt, $uid_est, $name, $type, $uid_cnt, $uid_loc, $uid_acc, $date_time);
                    if($last == true){
                        echo "Registration Success";
                    }else{
                        echo "Something went wrong";
                    }
                }else{
                    echo "Something went wrong";
                }
            }else{
                echo "Something went wrong";
            }
        }else{
            echo "Something went wrong";
        }
    }else{
        echo "Username is taken";
    }
}

?>