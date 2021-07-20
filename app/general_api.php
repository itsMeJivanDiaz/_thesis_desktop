<?php

require 'db.php';

header('Content-Type:application/json');

$stmt = mysqli_stmt_init($conn);

if(isset($_GET['all'])){

    $sql_select = "SELECT * FROM establishment ORDER BY est_name ASC";

    $sql_join = "SELECT * FROM establishment INNER JOIN location ON establishment.est_loc_ID = location.loc_loc_ID INNER JOIN account ON account.acc_acc_ID = establishment.est_acc_ID
    INNER JOIN count_info ON count_info_ID = est_count_info_ID WHERE est_loc_ID = ? AND est_acc_ID = ? AND est_count_info_ID = ?;";

    $json_data = array();

    if(!mysqli_stmt_prepare($stmt, $sql_select)){
        echo json_encode(array(
            'Response_message' => 'Something went wrong'
        ));
    }else{
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while($row = mysqli_fetch_assoc($result)){
            $loc_id = $row['est_loc_ID'];
            $acc_id = $row['est_acc_ID'];
            $count_id = $row['est_count_info_ID'];
            if(!mysqli_stmt_prepare($stmt, $sql_join)){
                echo json_encode(array(
                    'Response_message' => 'Something went wrong'
                ));
            }else{
                mysqli_stmt_bind_param($stmt, 'sss', $loc_id, $acc_id, $count_id);
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);
                while($row = mysqli_fetch_assoc($res)){
                    $array = array(
                        'establishment-name' => $row['est_name'],
                        'establishment-type' => $row['est_type'],
                        'city' => $row['loc_city'],
                        'logo'=> $row['acc_logo'],
                        'branch-street' => $row['loc_branch_str'],
                        'barangay-area' => $row['loc_brgy'],
                        'latitude' => $row['loc_lat'],
                        'longitude' => $row['loc_long'],
                        'allowed-capacity' => $row['count_allowable_capacity'],
                        'normal-capacity' => $row['count_normal_capacity'],
                        'available-capacity' => $row['count_available'],
                        'current-crowd' => $row['count_current'],
                        'limited-capacity' => $row['count_normal_capacity'] * $row['count_allowable_capacity'],
                        'establishment-ID' => $row['est_ID'],
                        'location-ID' => $row['loc_loc_ID'],
                        'account-ID' => $row['acc_acc_ID'],
                    );
                    array_push($json_data,  $array);
                }
            }
        }
        echo json_encode($json_data, JSON_PRETTY_PRINT);
    }
}else if(isset($_GET['eid'])){

    $spec_json_data = array();

    $ref_id = $_GET['eid'];

    $sql_select_est = "SELECT * FROM establishment WHERE est_ID = ?;";

    $sql_join_est = "SELECT * FROM establishment INNER JOIN location ON establishment.est_loc_ID = location.loc_loc_ID INNER JOIN account ON account.acc_acc_ID = establishment.est_acc_ID
    INNER JOIN count_info ON count_info_ID = est_count_info_ID WHERE est_loc_ID = ? AND est_acc_ID = ? AND est_count_info_ID = ?;";

    if(!mysqli_stmt_prepare($stmt, $sql_select_est)){
        echo json_encode(array(
            'Response_message' => 'Something went wrong'
        ));
    }else{
        mysqli_stmt_bind_param($stmt, 's', $ref_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($res)){
            $loc_id = $row['est_loc_ID'];
            $acc_id = $row['est_acc_ID'];
            $count_id = $row['est_count_info_ID'];
            if(!mysqli_stmt_prepare($stmt, $sql_join_est)){
                echo json_encode(array(
                    'Response_message' => 'Something went wrong'
                ));
            }else{
                mysqli_stmt_bind_param($stmt, 'sss', $loc_id, $acc_id, $count_id);
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);
                while($row = mysqli_fetch_assoc($res)){
                    $array = array(
                        'establishment-name' => $row['est_name'],
                        'establishment-type' => $row['est_type'],
                        'city' => $row['loc_city'],
                        'logo'=> $row['acc_logo'],
                        'branch-street' => $row['loc_branch_str'],
                        'barangay-area' => $row['loc_brgy'],
                        'latitude' => $row['loc_lat'],
                        'longitude' => $row['loc_long'],
                        'allowed-capacity' => $row['count_allowable_capacity'],
                        'normal-capacity' => $row['count_normal_capacity'],
                        'available-capacity' => $row['count_available'],
                        'current-crowd' => $row['count_current'],
                        'limited-capacity' => $row['count_normal_capacity'] * $row['count_allowable_capacity'],
                        'establishment-ID' => $row['est_ID'],
                        'location-ID' => $row['loc_loc_ID'],
                        'account-ID' => $row['acc_acc_ID'],
                    );
                    array_push($spec_json_data,  $array);
                }
            }
        }
        echo json_encode($spec_json_data, JSON_PRETTY_PRINT);
    }
}else if(isset($_GET['search'])){

    $search = $_GET['search'];

    $filter = $_GET['city'];

    $search_array = array();

    $sql_search = "SELECT * FROM establishment WHERE est_name LIKE CONCAT(?,'%');";

    if($filter == "None"){
        $sql_get_search_ID = "SELECT * FROM establishment INNER JOIN location ON establishment.est_loc_ID = location.loc_loc_ID INNER JOIN account ON account.acc_acc_ID = establishment.est_acc_ID
        INNER JOIN count_info ON count_info_ID = est_count_info_ID WHERE est_loc_ID = ? AND est_acc_ID = ? AND est_count_info_ID = ?";
    }else{
        $sql_get_search_ID = "SELECT * FROM establishment INNER JOIN location ON establishment.est_loc_ID = location.loc_loc_ID INNER JOIN account ON account.acc_acc_ID = establishment.est_acc_ID
        INNER JOIN count_info ON count_info_ID = est_count_info_ID WHERE est_loc_ID = ? AND est_acc_ID = ? AND est_count_info_ID = ? AND loc_city = ?";
    }

    if(!mysqli_stmt_prepare($stmt, $sql_search)){
        echo json_encode(array(
            'Response_message' => 'Something went wrong'
        ));
    }else{
        mysqli_stmt_bind_param($stmt, 's', $search);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while($row = mysqli_fetch_assoc($result)){
            $loc_id = $row['est_loc_ID'];
            $acc_id = $row['est_acc_ID'];
            $count_id = $row['est_count_info_ID'];
            if(!mysqli_stmt_prepare($stmt, $sql_get_search_ID)){
                echo json_encode(array(
                    'Response_message' => 'Something went wrong'
                ));
            }else{
                if($filter == "None"){
                    mysqli_stmt_bind_param($stmt, 'sss', $loc_id, $acc_id, $count_id);
                }else{
                    mysqli_stmt_bind_param($stmt, 'ssss', $loc_id, $acc_id, $count_id, $filter);
                }
                mysqli_stmt_execute($stmt);
                $result1 = mysqli_stmt_get_result($stmt);
                while($row = mysqli_fetch_assoc($result1)){
                    $array = array(
                        'establishment-name' => $row['est_name'],
                        'establishment-type' => $row['est_type'],
                        'city' => $row['loc_city'],
                        'logo'=> $row['acc_logo'],
                        'branch-street' => $row['loc_branch_str'],
                        'barangay-area' => $row['loc_brgy'],
                        'latitude' => $row['loc_lat'],
                        'longitude' => $row['loc_long'],
                        'allowed-capacity' => $row['count_allowable_capacity'],
                        'normal-capacity' => $row['count_normal_capacity'],
                        'available-capacity' => $row['count_available'],
                        'current-crowd' => $row['count_current'],
                        'limited-capacity' => $row['count_normal_capacity'] * $row['count_allowable_capacity'],
                        'establishment-ID' => $row['est_ID'],
                        'location-ID' => $row['loc_loc_ID'],
                        'account-ID' => $row['acc_acc_ID'],
                    );
                    array_push($search_array, $array);
                }
            }
        }
        echo json_encode($search_array, JSON_PRETTY_PRINT);
    }
}
?>