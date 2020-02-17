<?php 
    require_once('../assets/config/database.php');
    require_once('./function.php');
    global $conn_admin_db;
    session_start();

    $action = isset($_POST['action']) && $_POST['action'] !="" ? $_POST['action'] : "";    
    $date_start = isset($_POST['date_start']) ? $_POST['date_start'] : date('01-m-Y');
    $date_end = isset($_POST['date_end']) ? $_POST['date_end'] : date('t-m-Y');
   
    if( $action != "" ){
        switch ($action){
            
            case 'update_roadtax':
                if( !empty($_POST) ){
                    $vrt_id = $_POST['vrt_id'];
                    $vehicle_reg_no = $_POST['vehicle_reg_no'];
                    $lpkp_date = $_POST['lpkp_date'];
                    $insurance_from_date = $_POST['insurance_from_date'];
                    $insurance_due_date = $_POST['insurance_due_date'];
                    $roadtax_from_date = $_POST['roadtax_from_date'];
                    $roadtax_due_date = $_POST['roadtax_due_date'];
                    $premium_amount = $_POST['premium_amount'];
                    $ncd = $_POST['ncd'];
                    $sum_insured = $_POST['sum_insured'];
                    $excess_paid = $_POST['excess_paid'];
                    $roadtax_amount = $_POST['roadtax_amount'];
                    $insurance_status = $_POST['insurance_status'];
                    $insurance_amount = $_POST['insurance_amount'];
                    
                    //calculate the roadtax period
                    $diff = abs(strtotime($roadtax_due_date) - strtotime($roadtax_from_date));
                    $years = floor($diff / (365*60*60*24));
                    $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
                    $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
                    
                    //update roadtax
                    $query = "UPDATE vehicle_roadtax
                            SET vv_id = '$vehicle_reg_no',
                            vrt_lpkpPermit_dueDate = '$lpkp_date',
                            vrt_roadTax_fromDate = '$roadtax_from_date',
                            vrt_roadTax_dueDate = '$roadtax_due_date',
                            vrt_roadtaxPeriodYear = '$years',
                            vrt_roadtaxPeriodMonth = '$months',
                            vrt_roadtaxPeriodDay = '$days',
                            vrt_amount = '$roadtax_amount',
                            vrt_updatedBy = '".$_SESSION['cr_id']."',
                            vrt_lastUpdated = now()
                            WHERE vrt_id='".$vrt_id."'";
                    
                    $result = mysqli_query($conn_admin_db, $query) or die(mysqli_error($conn_admin_db));
                    
                    //update insurance
                    $query2 = "UPDATE vehicle_insurance
                            SET vv_id = '".$vehicle_reg_no."',
                            WHERE vrt_id='".$vrt_id."',
                            vi_insurance_fromDate = '".$insurance_from_date."',
                            vi_insurance_dueDate = '".$insurance_due_date."',
                            vi_insuranceStatus = '".$insurance_status."',
                            vi_amount = '".$insurance_amount."',
                            vi_premium_amount='".$premium_amount."',
                            vi_ncd='".$ncd."',
                            vi_sum_insured='".$sum_insured."',
                            vi_excess_paid='".$excess_paid."',
                            vi_lastUpdated = now(),
                            vi_updatedBy = '".$_SESSION['cr_id']."'";
                    
                    $result2 = mysqli_query($conn_admin_db, $query2) or die(mysqli_error($conn_admin_db));
                    
                    alert ("Updated successfully","roadtax.php");
                }
                break;
                
            case 'delete_roadtax': 
                //also inactive the insurance if roadtax is deleted
                if(!empty($_POST)){
                    $updated_id = $_POST['id'];
                
                    //update roadtax table
                    $query = "UPDATE vehicle_roadtax SET status = 0 WHERE vp_id = '".$updated_id."' ";
                    $result = mysqli_query($conn_admin_db, $query);
                    
                    //update insurance table
                    $query_ins = "UPDATE vehicle_insurance SET vi_status = 0 WHERE vi_vrt_id = '".$updated_id."' ";
                    $result_ins = mysqli_query($conn_admin_db, $query_ins);
                    
                    if ($result && $result_ins) {
                        alert ("Deleted successfully", "puspakom.php");
                    }
                    
                }
                break;
                
            case 'display_roadtax':
                
                $sql_query = "SELECT * FROM vehicle_roadtax
                        INNER JOIN vehicle_vehicle ON vehicle_vehicle.vv_id = vehicle_roadtax.vv_id
                        LEFT JOIN vehicle_insurance ON vehicle_insurance.vi_vrt_id = vehicle_roadtax.vrt_id
                        INNER JOIN company ON company.id = vehicle_vehicle.company_id
                        WHERE vehicle_roadtax.status='1' ";
                
                if (!empty($date_start) && !empty($date_end)) {
                    $sql_query .= " AND vehicle_roadtax.vrt_roadTax_dueDate BETWEEN '".dateFormat($date_start)."' AND '".dateFormat($date_end)."'" ;
                }
                
                $rst  = mysqli_query($conn_admin_db, $sql_query)or die(mysqli_error($conn_admin_db));
                
                $arr_result = array(
                    'sEcho' => 0,
                    'iTotalRecords' => 0,
                    'iTotalDisplayRecords' => 0,
                    'aaData' => array()
                );
                $arr_data = array();
                $total_found_rows = 0;
                if ( mysqli_num_rows($rst) ){
                    $count = 0;
                    while( $row = mysqli_fetch_assoc( $rst ) ){
                        $row_found = mysqli_fetch_row(mysqli_query($conn_admin_db,"SELECT FOUND_ROWS()"));
                        $total_found_rows = $row_found[0];
                        $count++;
                        $year = !empty($row['vrt_roadtaxPeriodYear']) ? $row['vrt_roadtaxPeriodYear'] ."Year(s)" : "";
                        $month = !empty($row['vrt_roadtaxPeriodMonth']) ? $row['vrt_roadtaxPeriodMonth'] ."Month(s)" : "";
                        $days = !empty($row['vrt_roadtaxPeriodDay']) ? $row['vrt_roadtaxPeriodDay'] ."Day(s)" : "";
                        $period = $year ." ". $month ." ".$days;
                        $insurance_status = $row['vi_insuranceStatus'] == 1 ? "Active" : "Inactive";
                        
                        $action = '<span id='.$row['vrt_id'].' data-toggle="modal" class="edit_data" data-target="#editItem"><i class="menu-icon fa fa-edit"></i>
                        </span><br><span id='.$row['vrt_id'].' data-toggle="modal" class="delete_data" data-target="#deleteItem"><i class="menu-icon fa fa-trash-alt"></i>
                        </span>';
                        
                        $data = array(
                            $count,
                            $row['vv_vehicleNo'],
                            $row['code'],
                            dateFormatRev($row['vrt_lpkpPermit_dueDate']),
                            dateFormatRev($row['vi_insurance_dueDate']),
                            $insurance_status,
                            dateFormatRev($row['vrt_roadTax_fromDate']),
                            dateFormatRev($row['vrt_roadTax_dueDate']),
                            $period,
                            number_format($row['vrt_amount'], 2),
                            $action
                            
                        );
                        $arr_data[] = $data;
                    }
                    
                }
                
                $arr_result = array(
                    'sEcho' => 0,
                    'iTotalRecords' => $total_found_rows,
                    'iTotalDisplayRecords' => $total_found_rows,
                    'aaData' => $arr_data
                );
                
                echo json_encode($arr_result);
                break;
                
            case 'retrive_roadtax':
                
                if(isset($_POST["vrt_id"])){
                    $query = "SELECT * FROM vehicle_roadtax
                            INNER JOIN vehicle_vehicle ON vehicle_vehicle.vv_id = vehicle_roadtax.vv_id
                            LEFT JOIN vehicle_insurance ON vehicle_insurance.vi_vrt_id = vehicle_roadtax.vrt_id
                            WHERE vehicle_roadtax.vrt_id='".$_POST['vrt_id']."'";
                    
                    $result = mysqli_query($conn_admin_db, $query) or die(mysqli_error($conn_admin_db));
                    $row = mysqli_fetch_array($result);
                    
                    echo json_encode($row);
                }
                break;
            default:
                break;
        }
    }
    
?>