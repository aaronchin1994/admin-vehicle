<?php 
require_once('../assets/config/database.php');
require_once('../function.php');
global $conn_admin_db;
session_start();

$action = isset($_POST['action']) && $_POST['action'] !="" ? $_POST['action'] : ""; 
$data = isset($_POST['data']) ? $_POST['data'] : ""; 

$telefon_list = isset($_POST['telefon_list']) ? $_POST['telefon_list'] : "";

if( $action != "" ){
    switch ($action){
        case 'add_new_bill':            
            if (!empty($data)) {
                $param = array();
                parse_str($_POST['data'], $param); //unserialize jquery string data
                $bill_type = $param['bill_type'];
                //insert into respective table based on the bill type
                if( $bill_type == 1 ){ //sesb                    
                    insert_billing_sesb($param);
                }
                elseif ( $bill_type == 2 ){ //jabatan air
                    insert_billing_jabatan_air($param);
                }
                elseif ( $bill_type == 3 ){ //telekom
                    insert_billing_telekom($param, $telefon_list);
                }
                elseif( $bill_type == 4 ){
                    insert_billing_celcom($param);
                    
                }
                elseif( $bill_type == 5 ){
                    insert_billing_photocopy_machine($param);
                    
                }
                elseif( $bill_type == 6 ){
                    
                }
                
            }
            
            break;
        default:
            break;
    }
}

function insert_billing_photocopy_machine($param){
    global $conn_admin_db;
    $sel_account = $param['sel_account'];
    $date_entered = $param['date_entered'];
    $full_color = $param['full_color'];
    $black_white = $param['black_white'];
    $color_a3 = $param['color_a3'];
    $copy = $param['copy'];
    $print = $param['print'];
    $fax = $param['fax'];
    $total = 0;
    if(!empty($full_color) && !empty($black_white)){
        $total = $full_color + $black_white;
    }
    elseif (!empty($copy) || !empty($print) || !empty($fax)){
        $total = $copy + $print + $fax;
    }
    
    $query = "INSERT INTO bill_photocopy_machine SET acc_id = '$sel_account',
            date = '".dateFormat($date_entered)."',
            full_color = '$full_color',
            black_white = '$black_white',
            color_a3 = '$color_a3',
            copy = '$copy',
            print = '$print',
            fax = '$fax',
            total = '$total'";
    
    mysqli_query($conn_admin_db, $query) or die(mysqli_error($conn_admin_db));
}
function insert_billing_celcom($param){
    global $conn_admin_db;
    $sel_account = $param['sel_account'];
    $date_entered = $param['date_entered'];
    $bill_amount = $param['bill_amount'];
    
    $query = "INSERT INTO bill_celcom SET acc_id = '$sel_account',
            date = '".dateFormat($date_entered)."',
            bill_amount = '$bill_amount'";
    
    mysqli_query($conn_admin_db, $query) or die(mysqli_error($conn_admin_db));
}

function insert_billing_sesb($param){
    global $conn_admin_db;
    $cheque_no = $param['cheque_no'];
    $from_date = $param['from_date'];
    $to_date = $param['to_date'];
    $paid_date = $param['paid_date'];
    $due_date = $param['due_date'];
    $sel_account = $param['sel_account'];
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
                    SET acc_id = '$sel_account',
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

function insert_billing_jabatan_air($param){
    global $conn_admin_db;
    $cheque_no = $param['cheque_no'];
    $from_date = $param['from_date'];
    $to_date = $param['to_date'];
    $paid_date = $param['paid_date'];
    $due_date = $param['due_date'];
    $sel_account = $param['sel_account'];
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
                    SET acc_id = '$sel_account',
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

function insert_billing_telekom($param, $telefon_list){
    global $conn_admin_db;
    $cheque_no = $param['cheque_no'];
    $from_date = $param['from_date'];
    $to_date = $param['to_date'];
    $paid_date = $param['paid_date'];
    $due_date = $param['due_date'];
    $sel_account = $param['sel_account'];
    $bill_no = $param['bill_no'];
    $monthly_fee = $param['monthly_fee'];
    $rebate = $param['rebate'];
    $cr_adjustment = $param['cr_adjustment'];
    
    $query_insert_telekom = "INSERT INTO bill_telekom 
                        SET acc_id = '$sel_account',
                        bill_no = '$bill_no',
                        monthly_bill = '$monthly_fee',
                        rebate = '$rebate',
                        credit_adjustment = '$cr_adjustment',
                        cheque_no = '$cheque_no',
                        date_start = '".dateFormat($from_date)."',
                        date_end = '".dateFormat($to_date)."',
                        paid_date = '".dateFormat($paid_date)."',
                        due_date = '".dateFormat($due_date)."'";
    
    mysqli_query($conn_admin_db, $query_insert_telekom) or die(mysqli_error($conn_admin_db));
    
    $last_insert_id = mysqli_insert_id($conn_admin_db);
    
    $values = [];
    if(!empty($telefon_list)){
        foreach ($telefon_list as $tel){
            $telefon = $tel['telefon'];
            $type = $tel['type'];
            $usage = $tel['usage'];
            
            $values[] = "('$last_insert_id', '$telefon', '$usage', '$type')";
        
        }
        
        $values = implode(",", $values); 
        $query = "INSERT INTO bill_telefon_list (bt_id, tel_no, usage_amt, phone_type) VALUES" .$values;
        mysqli_query($conn_admin_db, $query) or die(mysqli_error($conn_admin_db));
    }
    
    //update gst, adjustment and amount
    
    $sum_usage = itemName("SELECT SUM(usage_amt) FROM bill_telekom bt
                        INNER JOIN bill_telefon_list btl ON btl.bt_id = bt.id WHERE bt.id='$last_insert_id'");
    
    $total = $monthly_fee + $sum_usage;
    $gst = $total * 0.06;
    $amount = $total + $gst + $cr_adjustment;
    $rounded = round_up($amount);
    $adjustment = number_format(($rounded-$amount), 2);
    $total_amt = $amount + $rebate + $adjustment;
    
    $query_update = "UPDATE bill_telekom SET gst_sst='$gst', adjustment='$adjustment', amount='$total_amt' WHERE id='$last_insert_id'";
    mysqli_query($conn_admin_db, $query_update) or die(mysqli_error($conn_admin_db));
    
}

//to round up0.05
function round_up($x){
    return round($x * 2, 1) / 2;
}
?>