<?php 
    require_once('../assets/config/database.php');
    require_once('../function.php');
    require_once('../check_login.php');
    global $conn_admin_db;
    
    $id = isset($_GET['id']) ? $_GET['id'] : "";
    $year_select = isset($_POST['year_select']) ? $_POST['year_select'] : date('Y');
    ob_start();
    selectYear('year_select',$year_select,'submit()','','form-control','','');
    $html_year_select = ob_get_clean();
    
    $query = "SELECT * FROM bill_fuji_xerox_account WHERE bill_fuji_xerox_account.id = '$id'";
    
    $result = mysqli_query($conn_admin_db, $query) or die(mysqli_error($conn_admin_db));
    $row = mysqli_fetch_array($result);
    $acc_id = $row['id'];
    $company = $row['company'];
    $serial_no = $row['serial_no'];
    $location = $row['location'];
    
    $details_query = "SELECT MONTHNAME(date) AS month_name, full_color, black_white, color_a3, copy, print, fax, date 
                    FROM bill_fuji_xerox WHERE acc_id = '$acc_id' AND YEAR(date) = '$year_select'";
    
    $rst = mysqli_query($conn_admin_db, $details_query) or die(mysqli_error($conn_admin_db));
    $arr_data = [];
    if ( mysqli_num_rows($rst) ){
        while( $row = mysqli_fetch_assoc( $rst ) ){
//             $total = $row['full_color'] + $row['black_white'] + $row['copy'] + $row['print'] + $row['fax'];            
//             $data = array(
//                 $row['month_name'],
//                 $row['full_color'],
//                 $row['black_white'],
//                 $row['color_a3'],
//                 $row['copy'],
//                 $row['print'],
//                 $row['fax'],
//                 $total,
//                 $row['date']
//             );
            
            $arr_data[] = $row;
        }        
    }
    
    
    
?>

<!doctype html><html class="no-js" lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Eng Peng Vehicle</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- link to css -->
	<?php include('../allCSS1.php')?>
   <style>
    .select2-selection__rendered {
      margin: 5px;
    }
    .select2-selection__arrow {
      margin: 5px;
    }
    .select2-container{ 
        width: 100% !important; 
    }
    .button_add{
        position: absolute;
        left:    0;
        bottom:   0;
    }
    .hideBorder {
        border: 0px;
        background-color: transparent;        
    }
    .hideBorder:hover {
        background: transparent;
        border: 1px solid #dee2e6;
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
                                <strong class="card-title">Account Details</strong>
                            </div>     
                            <div class="card-body">                                       
                                <div class="col-sm-12">
                                    <label for="company" class=" form-control-label"><small class="form-text text-muted">Company : <?=$company?></small></label>                                        
                                </div>
                                <div class="col-sm-12">
                                	<label for="location" class=" form-control-label"><small class="form-text text-muted">Location : <?=$location?></small></label>                                    
                                </div>   
                                <div class="col-sm-12">
                                	<label for="serial_number" class=" form-control-label"><small class="form-text text-muted">Serial Number : <?=$serial_no?></small></label>                                    
                                </div>                                                                                                   
                            	<hr>
                            	<form action="" method="post">
                                	<div class="form-group row col-sm-12">           
                                    	<div class="col-sm-4">
                                    		<b>Monthly Expenses</b>
                                    	</div>                                    	
                                    	<div class="col-sm-4">
                                    		<?=$html_year_select?>
                                    	</div>
                                    	<div class="col-sm-4">
                                    		<button type="button" class="btn btn btn-primary button_add" data-toggle="modal" data-target="#addItem">Add New Record</button>
                                    	</div>
                                	</div>
                            	</form>                            	
                            	<div>     
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                            	<th>Month</th>
    											<th class="text-center">Full Color</th>
                                                <th class="text-center">B/W</th>
    											<th class="text-right">Color A3</th>
    											<th class="text-right">Copy</th>											
    											<th class="text-right">Print</th>
    											<th class="text-right">Fax</th>	
    											<th class="text-right">Total</th>	    																			
                                            </tr>										
                                        </thead>
                                        <tbody> 
                                        <?php
                                            $total = 0;
                                            $sum_fc = 0;
                                            $sum_bw = 0;
                                            $sum_ca3 = 0;
                                            $sum_c = 0;
                                            $sum_p = 0;
                                            $sum_f = 0;
                                            $sum_total = 0;
                                            if(!empty($arr_data)){
                                                foreach ($arr_data as $data){
                                                $total = $data['full_color'] + $data['black_white'] + $data['copy'] + $data['print'] + $data['fax'];                                              
                                           
                                                $sum_fc += $data['full_color'];
                                                $sum_bw += $data['black_white'];
                                                $sum_ca3 += $data['color_a3'];
                                                $sum_c += $data['copy'];
                                                $sum_p += $data['print'];
                                                $sum_f += $data['fax'];
                                                $sum_total += $total;
                                        ?>  
                                        <tr>
                                        	<td class="text-left"><?=$data['month_name']?></td>
                                            <td class="text-center"><?=$data['full_color']?></td>
                                            <td class="text-center"><?=$data['black_white']?></td>
                                            <td class="text-right"><?=$data['color_a3']?></td>
                                            <td class="text-right"><?=$data['copy']?></td>
                                            <td class="text-right"><?=$data['print']?></td>
                                            <td class="text-right"><?=$data['fax']?></td>
                                            <td class="text-right"><?=$total?></td>
                                            
                                        </tr>
                                        <?php }?>                                   
                                        </tbody> 
                                        <tfoot>
                                        	<tr>
                                                <td class="text-left font-weight-bold">TOTAL</td>
                                                <td class="text-center font-weight-bold"><?=$sum_fc?></td>
                                                <td class="text-center font-weight-bold"><?=$sum_bw?></td>
                                                <td class="text-right font-weight-bold"><?=$sum_ca3?></td>
                                                <td class="text-right font-weight-bold"><?=$sum_c?></td>
                                                <td class="text-right font-weight-bold"><?=$sum_p?></td>
                                                <td class="text-right font-weight-bold"><?=$sum_f?></td>
                                                <td class="text-right font-weight-bold"><?=$sum_total?></td>                                                                                
                                            </tr>
                                        </tfoot>
                                        <?php }else {?> 
                                        <tr>
                                    		<td colspan="8" class="text-center"> No records found.</td>
                                    	</tr>
                                        <?php }?>                                                                                                              
                                    </table>
                                </div>                                                                                                            
                          	 
                           	</div> 
                           	<br>                      
                        </div>
                    </div>
                </div>
            </div><!-- .animated -->
        </div><!-- .content -->
        </div>
        <!-- Modal add new fuji xerox bill -->
        <div class="modal fade" id="addItem">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h4 class="modal-title">Add New</h4>
                </div>
                <div class="modal-body">
                    <form role="form" method="POST" action="" id="add_form">  
                    <input type="hidden" id="acc_id" name="acc_id" value="">                                    
                    <div class="row form-group col-sm-12">
                    	<div class="col-sm-4">
                            <label for="date" class=" form-control-label"><small class="form-text text-muted">Date</small></label>
                            <div class="input-group">
                                <input type="text" id="date" name="date" class="form-control" autocomplete="off">
                                <div class="input-group-addon"><i class="fas fa-calendar-alt"></i></div>
                            </div>
                        </div>
                    </div>                                                                    	 
                    <div class="row form-group col-sm-12">
                    	<div class="col-sm-4">
                            <label for="full_color" class=" form-control-label"><small class="form-text text-muted">Full Color</small></label>
                            <input type="text" id="full_color" name="full_color" class="form-control" autocomplete="off">
                        </div>
                        <div class="col-sm-4">
                            <label for="black_white" class=" form-control-label"><small class="form-text text-muted">B/W</small></label>
                            <input type="text" id="black_white" name="black_white" class="form-control" autocomplete="off">
                        </div>
                        <div class="col-sm-4">
                            <label for="color_a3" class=" form-control-label"><small class="form-text text-muted">Color A3</small></label>
                            <input type="text" id="color_a3" name="color_a3" class="form-control" autocomplete="off">
                        </div>                                    
                    </div>
                    <div class="row form-group col-sm-12">
                    	<div class="col-sm-4">
                            <label for="copy" class=" form-control-label"><small class="form-text text-muted">Copy</small></label>
                            <input type="text" id="copy" name="copy" class="form-control" autocomplete="off">
                        </div>
                        <div class="col-sm-4">
                            <label for="print" class=" form-control-label"><small class="form-text text-muted">Print</small></label>
                            <input type="text" id="print" name="print" class="form-control" autocomplete="off">
                        </div>
                        <div class="col-sm-4">
                            <label for="fax" class=" form-control-label"><small class="form-text text-muted">Fax</small></label>
                            <input type="text" id="fax" name="fax" class="form-control" autocomplete="off">
                        </div>                                    
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary save_data ">Save</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <!-- Footer -->
    <?PHP include('../footer.php')?>
        <!-- /.site-footer -->
    <!-- /#right-panel -->

    <!-- link to the script-->
	<?php include ('../allScript2.php')?>
	<!-- Datatables -->
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
    <script src="../assets/js/select2.min.js"></script>
	
	<script type="text/javascript">
    $(document).ready(function() {
		
		var acc_id = '<?=$id?>';
			
        $('#item-data-table').DataTable({
        	"columnDefs": [
//         	    { "width": "10%", "targets": 0 },
//         	    { "width": "80%", "targets": 1 },
//         	    { "width": "10%", "targets": 2 }
        	  ]
  	  	});
        $('#add_form').on("submit", function(event){  
            event.preventDefault();  
            
            $('#acc_id').val(acc_id);

            if($('#date').val() == ""){  
                 alert("Date is required");  
            }                                           
            else{  
                 $.ajax({  
                      url:"fx_bill.ajax.php",  
                      method:"POST",  
                      data:{action:'add_new_bill', data: $('#add_form').serialize()},  
                      success:function(data){   
                           $('#editItem').modal('hide');  
                           $('#bootstrap-data-table').html(data);
//                            location.reload();  
                      }  
                 });  
            }  
          });

        $('#date').datepicker({
              format: "dd-mm-yyyy",
              autoclose: true,
              orientation: "top left",
              todayHighlight: true
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
    });
  </script>
</body>
</html>