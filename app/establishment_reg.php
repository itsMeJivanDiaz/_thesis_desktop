<?php
header('Access-Control-Allow-Origin: http://localhost:8001');
date_default_timezone_set('Asia/Manila');
if(isset($_POST['username']) && isset($_POST['password'])){
    // imports

    require 'db.php';
    require 'set_establishment_type.php';
    require 'set_token.php';
    require 'set_account.php';
    require 'set_coordinate.php';
    require 'set_address.php';
    require 'set_capacity.php';
    require 'set_count.php';
    require 'set_area.php';
    require 'set_final.php';

    function random_strings($length_of_string) { 
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'; 
        return substr(str_shuffle($str_result),  0, $length_of_string); 
    } 
    function random_ID($prefix){
        return uniqid($prefix) . random_strings(11);
    }

    // Form variables

    $est_name = strtolower($_POST['name']);
    $est_logo = "none";
    $est_type = strtolower($_POST['type']);
    $est_username = $_POST['username'];
    $est_password = $_POST['password'];
    $acc_type = "AcctTyID01";
    $token = random_strings(11);
    $region = strtolower($_POST['region']);
    $province = strtolower($_POST['province']);
    $city = strtolower($_POST['city_munic']);
    $barangay = strtolower($_POST['barangay']);
    $street = strtolower($_POST['street']);
    $branch = strtolower($_POST['area_branch']);
    $lat_long = $_POST['latlong'];
    $coordinates = explode(', ', $lat_long);
    if(sizeof($coordinates) != 2){
        echo json_encode(array(
            'status' => 'Your coordinates is incomplete.',
        ));
        return false;
    }
    $lat = $coordinates[0];
    $long = $coordinates[1];
    $normal_cap = 0;
    $limit_cap = 0;
    $initial_count = 0;
    $tokenstatus = "offline";
    $token = random_strings(20);
    $area = $_POST['area'];
    $date_time = date('Y-m-d h:i:s');

    // IDs

    $typeid = random_ID('IDtyp=');
    $accid = random_ID('IDacc=');
    $tokenid = random_ID('IDtkn=');
    $addressid = random_ID('IDadrs=');
    $coordinateid = random_ID('IDcds=');
    $capacityid = random_ID('IDcty=');
    $countid = random_ID('IDcnt=');
    $areaid = random_ID('IDasq=');
    $estid = random_ID('IDest=');
    $stmt = mysqli_stmt_init($conn);

    
    if(setToken($stmt, $tokenid, $token, $tokenstatus)){
        if(SetAccount($stmt, $accid, $est_password, $est_username, $acc_type, $tokenid, $date_time)){
            if(SetCoordinate($stmt, $coordinateid, $lat, $long)){
                if(SetAddress($stmt, $addressid, $region, $province, $city, $barangay, $street, $branch, $coordinateid)){
                    if(SetCapacity($stmt, $capacityid, $normal_cap, $limit_cap)){
                        if(SetCount($stmt, $countid, $initial_count)){
                            if(SetArea($stmt, $areaid, $area)){
                                $typeid_get = SetEstablishmentType($stmt, $typeid, $est_type);
                                if($typeid_get == 'Inserted' || $typeid_get != 'Error'){
                                    if(SetFinal($stmt, $estid, $est_name, $est_logo, $typeid_get, $typeid, $accid, $addressid, $capacityid, $countid, $areaid)){
                                        echo json_encode('Registration Success');
                                    }else{
                                        echo json_encode('Account Creation stumbled into an error :x');
                                    }
                                }else{
                                    echo json_encode('Setting Establishment type Encountered Some Error :x');
                                }
                            }else{
                                echo json_encode('Setting Area Encountered Some Error :x');
                            }
                        }else{
                            echo json_encode('Setting Initial count Encountered Some Error :x');
                        }
                    }else{
                        echo json_encode('Setting Initial capacity Encountered Some Error :x');
                    }   
                }else{
                    echo json_encode('Setting Address Encountered Some Error :x');
                }
            }else{
                echo json_encode('Setting Coordinates Encountered Some Error :x');
            }
        }else{
            echo json_encode('Someone already used this username :x');
        }
    }else{
        echo json_encode('Setting Token Encountered Some Error :x');
    }


    /*echo json_encode(array(
        'id' => $est_name,
        'est_type' => $est_type,
        'est_username' => $est_username,
        'est_password' => $est_password,
        'token' => $token,
        'region' => $region,
        'province' => $province,
        'city' => $city,
        'barangay' => $barangay,
        'street' => $street,
        'lat' => $lat,
        'long' => $long,
        'area' => $area,
        'date_time' => $date_time,
    ));*/
}
?>