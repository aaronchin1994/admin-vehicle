<?php 
require_once('../assets/config/database.php');
require_once('../function.php');
global $conn_admin_db;
session_start();

$action = isset($_POST['action']) && $_POST['action'] !="" ? $_POST['action'] : ""; 
$data = isset($_POST['data']) ? $_POST['data'] : ""; 
$id = isset($_POST['id']) ? $_POST['id'] : "";

if( $action != "" ){
    switch ($action){
        case 'add_new_account':
            add_new_account($data);
            break;
            
        case 'retrieve_account':
            retrieve_account($id);
            break;
            
        case 'delete_account':
            delete_account($id);
            break;
            
        case 'add_new_bill':
            add_new_bill($data);
            break;
        default:
            break;
    }
}

function add_new_bill($data){
    global $conn_admin_db;
    if (!empty($data)) {
        $param = array();
        parse_str($data, $param); //unserialize jquery string data       
        $cheque_no = $param['cheque_no'];
        $from_date = $param['from_date'];
        $to_date = $param['to_date'];
        $paid_date = $param['paid_date'];
        $due_date = $param['due_date'];
        $acc_id = $param['acc_id'];
        $read_from = $param['read_from'];
        $read_to = $param['read_to'];
        $usage_1 = $param['usage_1'];
        $usage_2 = $param['usage_2'];
        $credit = $param['credit'];
        $rate_70 = $usage_1 * 1.60;
        $rate_71 = $usage_2 * 2.00;
        $amount = $rate_70 + $rate_71;
        $rounded = round_up($amount);
        $adjustment = number_format(($rounded-$amount), 2);
        $total_amt = $amount + $adjustment;
        
        $query_insert_ja = "INSERT INTO bill_jabatan_air
                    SET acc_id = '$acc_id',
                    meter_reading_from = '$read_from',
                    meter_reading_to = '$read_to',
                    usage_70 = '$usage_1',
                    usage_71 = '$usage_2',
                    rate_70 = '$rate_70',
                    rate_71 = '$rate_71',
                    credit_adjustment = '$credit',
                    amount = '$total_amt',
                    adjustment = '$adjustment',
                    cheque_no = '$cheque_no',
                    date_start = '".dateFormat($from_date)."',
                    date_end = '".dateFormat($to_date)."',
                    paid_date = '".dateFormat($paid_date)."',
                    due_date = '".dateFormat($due_date)."'";
        
        mysqli_query($conn_admin_db, $query_insert_ja) or die(mysqli_error($conn_admin_db));
    }   
}

function add_new_account($data){
    global $conn_admin_db;
    if (!empty($data)) {
        $param = array();
        parse_str($data, $param); //unserialize jquery string data
        
        $company =  mysqli_real_escape_string( $conn_admin_db,$param['company']);
        $acc_no =  mysqli_real_escape_string( $conn_admin_db,$param['acc_no']);
        $location =  mysqli_real_escape_string( $conn_admin_db,$param['location']);
        $deposit =  mysqli_real_escape_string( $conn_admin_db,$param['deposit']);
        $tariff =  mysqli_real_escape_string( $conn_admin_db,$param['tariff']);
        $owner =  mysqli_real_escape_string( $conn_admin_db,$param['owner']);        
        $remark =  mysqli_real_escape_string( $conn_admin_db,$param['remark']);
        $jenis_bacaan =  mysqli_real_escape_string( $conn_admin_db,$param['jenis_bacaan']);
        $jenis_premis =  mysqli_real_escape_string( $conn_admin_db,$param['jenis_premis']);
        
        $query = "INSERT INTO bill_jabatan_air_account
                    SET company='$company',
                    account_no='$acc_no',
                    owner='$owner',
                    location='$location',
                    deposit='$deposit',
                    kod_tariff='$tariff',
                    remark='$remark',                    
                    jenis_bacaan='$jenis_bacaan',
                    jenis_premis='$jenis_premis'";

        $result = mysqli_query($conn_admin_db, $query) or die(mysqli_error($conn_admin_db));
    }
}

function retrieve_account($id){
    global $conn_admin_db;
    if (!empty($id)) {        
        $query = "SELECT * FROM bill_jabatan_air_account WHERE id = '$id'";
        $rst  = mysqli_query($conn_admin_db, $query)or die(mysqli_error($conn_admin_db));
        
        $row = mysqli_fetch_assoc($rst);
        echo json_encode($row);
    }
}

function delete_account($id){
    global $conn_admin_db;
    if (!empty($id)) {
        $query = "UPDATE bill_jabatan_air_account SET status = 0 WHERE id = '".$id."' ";
        $result = mysqli_query($conn_admin_db, $query);
        if ($result) {
            alert ("Deleted successfully", "sesb_setup.php");
        }
    }
}

//to round up0.05
function round_up($x){
    return round($x * 2, 1) / 2;
}
?>