<?php
    require_once('../assets/config/database.php');
    require_once('./function.php');
	
	session_start();
	global $conn_admin_db;
	if(isset($_SESSION['cr_id'])) {
		$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		$url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$query = parse_url($url, PHP_URL_QUERY);
		parse_str($query, $params);
		
		// get id
		$userId = $_SESSION['cr_id'];
		$name = $_SESSION['cr_name'];
		
	} else {
		$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		$url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$PrevURL= $url;
		header("Location: ../login.php?RecLock=".$PrevURL);
    }
?>

<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Eng Peng Insurance</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- link to css -->
	<?php include('../allCSS1.php')?>
   <style>
    #weatherWidget .currentDesc {
        color: #ffffff!important;
    }
        .traffic-chart {
            min-height: 335px;
        }
        #flotPie1  {
            height: 150px;
        }
        #flotPie1 td {
            padding:3px;
        }
        #flotPie1 table {
            top: 20px!important;
            right: -10px!important;
        }
        .chart-container {
            display: table;
            min-width: 270px ;
            text-align: left;
            padding-top: 10px;
            padding-bottom: 10px;
        }
        #flotLine5  {
             height: 105px;
        }

        #flotBarChart {
            height: 150px;
        }
        #cellPaiChart{
            height: 160px;
        }

    </style>
</head>

<body>
    <!--Left Panel -->
	<?php  include('../assets/nav/leftNav.php')?>
    <!-- Right Panel -->
    <?php include('../assets/nav/rightNav.php')?>
    <!-- /#header -->
    <!-- Content -->
        <div id="right-panel" class="right-panel">
        <div class="content">
            <div class="animated fadeIn">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title">Add New Summon</strong>
                            </div>
                            <form action="summon_add_process.php" method="post">
                                <div class="card-body card-block">
                                	<div class="form-group row col-sm-12">
                                        <div class="col-sm-4">
                                            <label for="vehicle_reg_no" class=" form-control-label"><small class="form-text text-muted">Vehicle Reg No.</small></label>
                                            <?php
                                                $vehicle = mysqli_query ( $conn_admin_db, "SELECT vv_id, vv_vehicleNo FROM vehicle_vehicle");
                                                db_select ($vehicle, 'vehicle_reg_no', '','','-select-','form-control','');
                                            ?>
                                        </div>
                                        <div class="col-sm-4">
                                        	<label for="driver_name" class=" form-control-label"><small class="form-text text-muted">Driver's Name</small></label>
                                            <input type="text" id="driver_name" name="driver_name" placeholder="Enter driver's name" class="form-control">
                                            
                                        </div>                                        
                                    </div>
                                    <div class="form-group row col-sm-12">
                                        <div class="col-sm-4">
                                            <label for="summon_no" class=" form-control-label"><small class="form-text text-muted">Summon's No.</small></label>
                                            <input type="text" id="summon_no" name="summon_no" placeholder="Enter summon number" class="form-control">
                                        </div>
                                        <div class="col-sm-4">
                                            <label for="summon_type" class=" form-control-label"><small class="form-text text-muted">Summon's Type</small></label>                                             
                                            <?php
                                                $summon_type = mysqli_query ( $conn_admin_db, "SELECT st_id, st_name FROM vehicle_summon_type");
                                                db_select ($summon_type, 'summon_type', '','','-select-','form-control','');
                                            ?>
                                        </div>
                                        <!-- Only appear when summon type selected is others -->
                                        <div class="col-sm-4" id="desc">
                                            <label for="summon_desc" class=" form-control-label"><small class="form-text text-muted">Description</small></label>
                                    		<textarea id="summon_desc" name="summon_desc" rows="5" class="form-control"></textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row col-sm-12">
                                    	<div class="col-sm-4">
                                            <label for="pv_no" class=" form-control-label"><small class="form-text text-muted">PV No.</small></label>
                                            <input type="text" id="pv_no" name="pv_no" placeholder="Enter PV number" class="form-control">
                                        </div>
                                        <div class="col-sm-4">
                                            <label for="reimburse_amt" class=" form-control-label"><small class="form-text text-muted">Reimburse Amount(RM)</small></label>
                                            <input type="text" id="reimburse_amt" name="reimburse_amt" onkeypress="return isNumberKey(event)" placeholder="e.g 500.00" class="form-control">
                                        </div>                                        
                                    </div>
                                    <div class="form-group row col-sm-12">                                          
                                        <div class="col-sm-4">
                                            <label for="summon_date" class=" form-control-label"><small class="form-text text-muted">Summon's Date</small></label>
                                            <div class="input-group">
                                                <input id="summon_date" name="summon_date" class="form-control" autocomplete="off">
                                                <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div> 
                                        <div class="col-sm-6">
                                            <label for="offence_details" class=" form-control-label"><small class="form-text text-muted">Offense Details</small></label>                                             
                                            <textarea name="offence_details" id="offence_details" name="offence_details" rows="5" placeholder="Offense details..." class="form-control"></textarea>
                                        </div>                                     
                                    </div>
                                    <div class="card-body">
                                        <button type="submit" id="save" name="save" class="btn btn-primary">Save</button>
                                        <button type="button" id="cancel" name="cancel" class="btn btn-secondary">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div><!-- .animated -->
        </div><!-- .content -->
        </div>
        <div class="clearfix"></div>
        <!-- Footer -->
        <?PHP include('../footer.php')?>
        <!-- /.site-footer -->
    <!-- from right panel page -->
    <!-- /#right-panel -->

    <!-- link to the script-->
	<?php include ('../allScript2.php')?>

	<script src="../assets/js/lib/data-table/datatables.min.js"></script>
    <script src="../assets/js/lib/data-table/dataTables.bootstrap.min.js"></script>
    <script src="../assets/js/lib/data-table/dataTables.buttons.min.js"></script>
    <script src="../assets/js/lib/data-table/buttons.bootstrap.min.js"></script>
    <script src="../assets/js/lib/data-table/jszip.min.js"></script>
    <script src="../assets/js/lib/data-table/vfs_fonts.js"></script>
    <script src="../assets/js/lib/data-table/buttons.html5.min.js"></script>
    <script src="../assets/js/lib/data-table/buttons.print.min.js"></script>
    <script src="../assets/js/lib/data-table/buttons.colVis.min.js"></script>
    <script src="../assets/js/init/datatables-init.js"></script>
    <script src="../assets/js/script/bootstrap-datepicker.min.js"></script>
   	
   	<!-- Datepicker JQuery UI -->
<!--     <script src="https://code.jquery.com/jquery-1.12.4.js"></script> -->
<!--     <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> -->

	
	<script type="text/javascript">
        $(document).ready(function() {
          
          $('#desc').hide(); 
          $('#summon_type').change(function(){
              if($('#summon_type').val() == 3) {
                  $('#desc').show(); 
              } else {
                  $('#desc').hide(); 
              } 
          });

          $('#summon_date').datepicker({
        	  	format: 'dd-mm-yyyy',
              	autoclose: true,
              	todayHighlight: true,       
           });
          
      });

    function isNumberKey(evt){
		var charCode = (evt.which) ? evt.which : evt.keyCode;
		if (charCode != 46 && charCode > 31 
		&& (charCode < 48 || charCode > 57))
		return false;
		return true;
	}  
	
	function isNumericKey(evt){
		var charCode = (evt.which) ? evt.which : evt.keyCode;
		if (charCode != 46 && charCode > 31 
		&& (charCode < 48 || charCode > 57))
		return true;
		return false;
	} 
  </script>
</body>
</html>
