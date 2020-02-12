<?php 
    require_once('../assets/config/database.php');
    require_once('./function.php');
    global $conn_admin_db;
    session_start();

    $date_start = isset($_POST['date_start']) ? $_POST['date_start'] : date('01-m-Y');
    $date_end = isset($_POST['date_end']) ? $_POST['date_end'] : date('t-m-Y');
    
	$sql_query = "SELECT * FROM vehicle_vehicle  vv
                INNER JOIN vehicle_roadtax vrt ON vv.vv_id = vrt.vv_id
                INNER JOIN company c ON c.id = vv.company_id
                LEFT JOIN vehicle_puspakom vp ON vp.vv_id = vv.vv_id
                LEFT JOIN vehicle_insurance vi ON vi.vv_id = vv.vv_id";
	
	$rst  = mysqli_query($conn_admin_db, $sql_query)or die(mysqli_error($conn_admin_db));
	
	$arr_result = array(
			'sEcho' => 0,
			'iTotalRecords' => 0,
			'iTotalDisplayRecords' => 0,
			'aaData' => array()
	);
	$arr_data = array();
	if ( mysqli_num_rows($rst) ){
	    $count = 0;
		while( $row = mysqli_fetch_assoc( $rst ) ){
		    $row_found = mysqli_fetch_row(mysqli_query($conn_admin_db,"SELECT FOUND_ROWS()"));
			$total_found_rows = $row_found[0];
			$count++;
			
			$year = !empty($row['vrt_roadtaxPeriodYear']) ? $row['vrt_roadtaxPeriodYear'] ." Year(s)" : "";
			$month = !empty($row['vrt_roadtaxPeriodMonth']) ? $row['vrt_roadtaxPeriodMonth'] ." Month(s)" : "";
			$days = !empty($row['vrt_roadtaxPeriodDay']) ? $row['vrt_roadtaxPeriodDay'] ." Day(s)" : "";
			$period = $year ." ". $month ." ".$days;

			$data = array(
			        $count,
					$row['code'],
					$row['vv_vehicleNo'],					
			        dateFormatRev($row['vrt_lpkpPermit_dueDate']),
			        dateFormatRev($row['vi_insurance_fromDate']),
    			    dateFormatRev($row['vi_insurance_dueDate']),
			        $row['vi_premium_amount'],
    			    $row['vi_ncd'],
			        $row['vi_sum_insured'],
			        $row['vi_excess_paid'],
    			    $row['vv_capacity'],
			        dateFormatRev($row['vp_fitnessDate']),
    			    dateFormatRev($row['vrt_roadTax_fromDate']),
    			    dateFormatRev($row['vrt_roadTax_dueDate']),
			        $row['vrt_amount'],
			        $period,
		
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
?>