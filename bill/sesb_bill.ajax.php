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
        parse_str($_POST['data'], $param); //unserialize jquery string data        
        $cheque_no = $param['cheque_no'];
        $from_date = $param['from_date'];
        $to_date = $param['to_date'];
        $paid_date = $param['paid_date'];
        $due_date = $param['due_date'];
        $acc_id = $param['acc_id'];
        $reading_from = $param['reading_from'];
        $reading_to = $param['reading_to'];
        $current_usage = $param['current_usage'];
        $kwtbb = $param['kwtbb'];
        $penalty = $param['penalty'];
        $additional_depo = $param['additional_depo'];
        $other_charges = $param['other_charges'];
        $total_usage = $reading_to - $reading_from;
        $power_factor = $param['power_factor'];
        $amount = $current_usage + $kwtbb + $penalty + $additional_depo + $other_charges;
        $rounded = round_up($amount);
        $adjustment = number_format(($rounded-$amount), 2);
        $total_amt = $amount + $adjustment;
        
        $query_insert_sesb = "INSERT INTO bill_sesb
                        SET acc_id = '$acc_id',
                        meter_reading_from = '$reading_from',
                        meter_reading_to = '$reading_to',
                        total_usage = '$total_usage',
                        current_usage = '$current_usage',
                        kwtbb = '$kwtbb',
                        penalty = '$penalty',
                        power_factor = '$power_factor',
                        additional_deposit = '$additional_depo',
                        other_charges = '$other_charges',
                        amount = '$total_amt',
                        adjustment = '$adjustment',
                        cheque_no = '$cheque_no',
                        date_start = '".dateFormat($from_date)."',
                        date_end = '".dateFormat($to_date)."',
                        paid_date = '".dateFormat($paid_date)."',
                        due_date = '".dateFormat($due_date)."'";
        
        mysqli_query($conn_admin_db, $query_insert_sesb) or die(mysqli_error($conn_admin_db));
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
        $pic =  mysqli_real_escape_string( $conn_admin_db,$param['pic']);
        $remark =  mysqli_real_escape_string( $conn_admin_db,$param['remark']);
        
        $query = "INSERT INTO bill_sesb_account
                    SET company='$company',
                    account_no='$acc_no',
                    owner='$owner',
                    location='$location',
                    deposit='$deposit',
                    tarif='$tariff',
                    remark='$remark',
                    person_in_charge='$pic'";
        
        $result = mysqli_query($conn_admin_db, $query) or die(mysqli_error($conn_admin_db));
    }
}

function retrieve_account($id){
    global $conn_admin_db;
    if (!empty($id)) {        
        $query = "SELECT * FROM bill_sesb_account WHERE id = '$id'";
        $rst  = mysqli_query($conn_admin_db, $query)or die(mysqli_error($conn_admin_db));
        
        $row = mysqli_fetch_assoc($rst);
        echo json_encode($row);
    }
}

function delete_account($id){
    global $conn_admin_db;
    if (!empty($id)) {
        $query = "UPDATE bill_sesb_account SET status = 0 WHERE id = '".$id."' ";
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