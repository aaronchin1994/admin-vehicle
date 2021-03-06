<?php 
    require_once('../assets/config/database.php');
    require_once('../function.php');
    require_once('../check_login.php');
    global $conn_admin_db;
    
    $id = isset($_GET['id']) ? $_GET['id'] : "";
    $year_select = isset($_POST['year_select']) ? $_POST['year_select'] : date('Y');
    ob_start();
    selectYear('year_select',$year_select,'submit()','','form-control form-control-sm','','');
    $html_year_select = ob_get_clean();
    
    $query = "SELECT * FROM bill_telekom_account WHERE bill_telekom_account.id = '$id'";
    
    $result = mysqli_query($conn_admin_db, $query) or die(mysqli_error($conn_admin_db));
    $row = mysqli_fetch_assoc($result);
    $acc_id = $row['id'];
    $company = itemName("SELECT UPPER(name) FROM company WHERE id='".$row['company_id']."'");
    $owner = $row['owner'];
    $ref_no = $row['ref_no'];
    $account_no = $row['account_no'];
    
    //get the telefon_list
    
    $details_query = "SELECT bill_telekom.id, acc_id, MONTH(date_end) As month, MONTHNAME(date_end) AS month_name, date_start, date_end, due_date,paid_date, 
                    credit_adjustment, rebate, gst_sst, adjustment, cheque_no, bill_no, amount, monthly_bill FROM bill_telekom                     
                    WHERE acc_id = '$acc_id' AND YEAR(date_end) = '$year_select' ORDER BY date_end";
    $result2 = mysqli_query($conn_admin_db, $details_query) or die(mysqli_error($conn_admin_db));
    $arr_data = [];      
    $t_list = [];    
    while ($rows = mysqli_fetch_assoc($result2)){   
        $t_list = get_active_tel_list($acc_id);
        $tel_list = get_tel_list_usage($acc_id, $rows['month']);
        $tel_list = array_replace($t_list,$tel_list);
        $arr_data[] = array(
            'id' => $rows['id'],
            'acc_id' => $rows['acc_id'],
            'monthly_bill' => $rows['monthly_bill'],
            'month_name' => $rows['month_name'],
            'date_start' => $rows['date_start'],
            'date_end' => $rows['date_end'],
            'due_date' => $rows['due_date'],
            'paid_date' => $rows['paid_date'],
            'credit_adjustment' => $rows['credit_adjustment'],
            'rebate' => $rows['rebate'],
            'gst_sst' => $rows['gst_sst'],
            'adjustment' => $rows['adjustment'],
            'cheque_no' => $rows['cheque_no'],
            'bill_no' => $rows['bill_no'],
            'amount' => $rows['amount'],
            'telefon_list' => $tel_list,
        );
    }  
    //populate column header
    $th = "";
    $count = 0;
    $tel_arr = [];    
    foreach ($t_list as $tel_no => $val){    
        $count++;
        $tel_arr[] = $count;
        $th .="<th scope='col' class='text-center'>".$tel_no."</th>";
    }       
    //populate next column after tel list - 8 column
    $tel_count = [];
    for ($i = 4; $i <= count($tel_arr) + 8; $i++) {
        $tel_count[] = $i;        
    }
    
    $tel_column_str = implode(",", $tel_count);
    
    function get_tel_list_usage($acc_id, $month){
        global $conn_admin_db;
        //get the telefon list
        $qry = "SELECT MONTHNAME(date_added) AS month_name, bill_telefon_list.*, bill_telefon_usage.* FROM bill_telefon_list
            LEFT JOIN bill_telefon_usage ON bill_telefon_usage.telefon_id = bill_telefon_list.id
            WHERE bt_id='".$acc_id."' AND bill_telefon_list.status='1' AND MONTH(date_added)='$month'";
        
        $rst = mysqli_query($conn_admin_db, $qry);
        $tel_list = [];
        while ($row = mysqli_fetch_assoc($rst)) {
            $tel_list[$row['tel_no']] = $row['usage_rm'];
        }
        return $tel_list;
    }
    
    function get_active_tel_list($acc_id){ // to populate the header
        global $conn_admin_db;
        $query = "SELECT tel_no FROM bill_telefon_list WHERE bt_id = '$acc_id' AND status='1'";
        $rst = mysqli_query($conn_admin_db, $query);
        $t_list = [];
        while ($row = mysqli_fetch_assoc($rst)) {
            $t_list[$row['tel_no']] = 0;
        }
        return $t_list;
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
    .button_add_telefon{
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
<?php include('../assets/nav/leftNav.php')?>
<!-- Right Panel -->
<?php include('../assets/nav/rightNav.php')?>
<!-- /#header -->
<!-- Content -->
<div id="right-panel" class="right-panel">
<div class="content">
    <div class="animated fadeIn">
        <div class="row">
        	<div class="col-md-12">
                <div class="card" id="printableArea">
                    <div class="card-header">                            
                        <strong class="card-title">Account Details</strong>
                    </div>     
                    <div class="card-body">                                       
                        <div class="col-sm-12">
                            <label for="company" class=" form-control-label">Company : <?=$company?></label>                                        
                        </div>
                        <div class="col-sm-12">
                        	<label for="account_no" class=" form-control-label">Account No. : <?=$account_no?></label>                                    
                        </div>   
                        <div class="col-sm-12">
                        	<label for="owner" class=" form-control-label">Owner : <?=$owner?></label>
                            
                        </div>
                        <div class="col-sm-12">
                        	<label for="ref_no" class=" form-control-label">Ref No. : <?=$ref_no?></label>                                    	
                        </div>                                                                    
                    	<hr>
                    	<form action="" method="post">
                        	<div class="form-group row col-sm-12">           
                            	<div class="col-sm-3">
                            		<b>Monthly Expenses</b>
                            	</div>                                    	
                            	<div class="col-sm-3">
                            		<?=$html_year_select?>
                            	</div>
                            	<div class="col-sm-2">
                            		<button type="button" class="btn btn-sm btn-primary button_add" data-toggle="modal" data-target="#addItem">Add New Record</button>
                            	</div>
                            	<div class="col-sm-2">
                                	<button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#add_phone">Add telephone bill</button>
                                </div>
                        	</div>
                    	</form>                            	
                    	<div>     
                            <table id="telekom_table" class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th scope="col" class="text-center">Month</th>
                                    <th scope="col" class="text-center">Period</th>
                                    <th scope="col" class="text-center">Bill No.</th>
                                    <th scope="col" class="text-center">Monthly (RM)</th>
                                    <?=$th?>
                                    <th scope="col" class="text-center">Rebate (RM)</th>
                                    <th scope="col" class="text-center">Credit Adj. (RM)</th>
                                    <th scope="col" class="text-center">GST/SST (6%)</th>
                                    <th scope="col" class="text-center">Adj.</th>
                                    <th scope="col" class="text-center">Total (RM)</th>
                                    <th scope="col" class="text-center">Due date</th>
                                    <th scope="col" class="text-center">Cheque No.</th>
                                    <th scope="col" class="text-center">Payment date</th>
                                </tr>  
                                </thead>
                                <tbody>
                                <?php foreach ($arr_data as $data){                                    
                                    $period = $data['date_start']."-".$data['date_end'];                                    
                                    ?>
                                	<tr>
                                		<td><?=$data['month_name']?></td>
                                		<td><?=$period?></td>
                                		<td><?=$data['bill_no']?></td>
                                		<td><?=$data['monthly_bill']?></td>
                                		<?php 
                                		$td="";                                		
                                		foreach ($data['telefon_list'] as $key => $value){                                 		    
                                		    $p_val = $value !=0 ? $value : "-";
                                		    $td .= "<td class='text-right'>".number_format($p_val,2)."</td>";
                                		?>
                                		<?php }?> 
                                		<?=$td?>    
                                		<td><?=$data['rebate']?></td>
                                		<td><?=$data['credit_adjustment']?></td>
                                		<td><?=$data['gst_sst']?></td>
                                		<td><?=$data['adjustment']?></td>   
                                		<td><?=$data['amount']?></td>
                                		<td><?=$data['due_date']?></td>
                                		<td><?=$data['cheque_no']?></td>
                                		<td><?=$data['paid_date']?></td>                           		
                                	</tr>
								<?php }?>
                                </tbody>
                                <tfoot>
                                <tr>
                                	<th colspan="3" class="text-right">GRAND TOTAL (RM)</th>
                                	<th></th> <!-- monthly bill -->
                                	<!-- populate td untuk tel list -->
									<?php for ($i = 0; $i < count($tel_arr); $i++) {?>
										<th class="text-right"></th>
                                	<?php }?>
                                	<th></th>
                                	<th></th>
                                	<th></th>
                                	<th></th>
                                	<th></th>
                                	<th></th>
                                	<th></th>
                                	<th></th>                                	
                                </tr>
                                </tfoot>                                                                                                          
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
<!-- Modal add new telekom bill -->
<div class="modal fade" id="addItem">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h4 class="modal-title">Add New</h4>
        </div>
        <div class="modal-body">
            <form role="form" method="POST" action="" id="add_form">  
            <input type="hidden" id="acc_id" name="acc_id" value="">    
            <input type="hidden" id="tel_count" name="tel_count">   
            <div class="form-group row col-sm-12">            	
            	<div class="col-sm-4">
                    <label for="from_date" class=" form-control-label"><small class="form-text text-muted">From date <span class="color-red">*</span></small></label>                                            
                    <div class="input-group">
                        <input id="from_date" name="from_date" class="form-control form-control-sm" autocomplete="off" required>
                    </div>  
                </div>       
                <div class="col-sm-4">
                    <label for="to_date" class=" form-control-label"><small class="form-text text-muted">To date <span class="color-red">*</span></small></label>                                            
                    <div class="input-group">
                        <input id="to_date" name="to_date" class="form-control form-control-sm" autocomplete="off" required>
                    </div>  
                </div>
                <div class="col-sm-4">
                    <label for="monthly_fee" class=" form-control-label"><small class="form-text text-muted">Monthly (RM) <span class="color-red">*</span></small></label>
                    <input type="text" id="monthly_fee" name="monthly_fee" class="form-control form-control-sm" required>
                </div>                
            </div> 
            <div class="form-group row col-sm-12">                        
                <div class="col-sm-4">
                    <label for="due_date" class=" form-control-label"><small class="form-text text-muted">Due date</small></label>                                            
                    <div class="input-group">
                        <input id="due_date" name="due_date" class="form-control form-control-sm" autocomplete="off">                        
                    </div>  
                </div> 
                <div class="col-sm-4">
                    <label for="paid_date" class=" form-control-label"><small class="form-text text-muted">Paid date</small></label>                                            
                    <div class="input-group">
                        <input id="paid_date" name="paid_date" class="form-control form-control-sm" autocomplete="off">                        
                    </div>  
                </div>
                <div class="col-sm-4">
                    <label for="bill_no" class=" form-control-label"><small class="form-text text-muted">Bill No. <span class="color-red">*</span></small></label>
                    <input type="text" id="bill_no" name="bill_no" class="form-control form-control-sm" required>
                </div>  	                                    
            </div>
            <div class="row form-group col-sm-12">
                <div class="col-sm-4">
                    <label for="rebate" class=" form-control-label"><small class="form-text text-muted">Rebate (RM)</small></label>
                    <input type="text" id="rebate" name="rebate" class="form-control form-control-sm">
                </div>
                <div class="col-sm-4">
                    <label for="cr_adjustment" class=" form-control-label"><small class="form-text text-muted">Credit Adjustment (RM)</small></label>
                    <input type="text" id="cr_adjustment" name="cr_adjustment" class="form-control form-control-sm">
                </div>
                <div class="col-sm-4">
                    <label for="cheque_no" class=" form-control-label"><small class="form-text text-muted">Cheque No. <span class="color-red">*</span></small></label>
                    <input type="text" id="cheque_no" name="cheque_no" class="form-control form-control-sm" required>
                </div>      
            </div>   
            <div class="row form-group col-sm-12">
            	<div class="col-sm-4">
                    <label for="other_charges" class=" form-control-label"><small class="form-text text-muted">Other Charges (RM)</small></label>
                    <input type="text" id="other_charges" name="other_charges" class="form-control form-control-sm">
                </div>
            </div> 
            <div class="row form-group col-sm-12">
            	<table id="telefon_usage" class="table table-bordered data-table wrap">
            	<thead>
                	<tr>
                        <th>Telephone No.</th>
                        <th>Usage (RM)</th>                                                
                  	</tr>
                </thead>
                <tbody>
                
                </tbody>
            	</table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-sm btn-primary save_data ">Save</button>
            </div>
            </form>
        </div>
        </div>
    </div>
</div>
<!-- Modal add telefon list  -->
<div id="add_phone" class="modal fade">
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" id="printableArea">
        <div class="modal-header">
            <h4 class="modal-title">Add Telephone List</h4>
        </div>
        <div class="modal-body">
            <form id="telefon_bill">        
                <div class="row form-group col-sm-12">
                    <div class="col-sm-5">
                        <label><small class="form-text text-muted">Telephone No.</small></label>
                        <input type="text" name="telefon" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-sm-5">
                        <label><small class="form-text text-muted">Type</small></label>
                        <input type="text" name="type" class="form-control form-control-sm" required>
                    </div> 
                    <div class="col-sm-1">
                    	<button type="submit" class="btn btn-sm btn-success button_add_telefon">Add</button>                            	
                    </div> 
                    <div class="col-sm-1">
                    	<button type="button" data-dismiss="modal" class="btn btn-sm btn-secondary button_add">Cancel</button>
                    </div>                         
                </div>                                                                             
          	</form>
          	<table id="telefon_list" class="table table-bordered data-tables">
                <thead>
                	<tr>
                        <th>Telephone No.</th>
                        <th>Type</th>                        
                        <th class="text-center">Action</th>
                  	</tr>
                </thead>
                <tbody>
                
                </tbody>
            </table>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary save_telefon">Save</button>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
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
	//declare empty array to temporary save the data in table
    var TELEPHONE_LIST = [];
    var acc_id = '<?=$id?>';
    var tel_col_str = '<?=$tel_column_str?>';

    $("#telekom_table").DataTable({   
    	"ordering": false,
//         "order":[[1,'asc']], 
		"bInfo": false,
    	"paging": false,
    	"columnDefs": [
    	  {
    	      "targets": [3,tel_col_str], // your case first column
    	      "className": "text-right", 
    	      "render": $.fn.dataTable.render.number(',', '.', 2, '')               	                      	        	     
    	 },
    	 {
   	      "targets": [tel_col_str], // your case first column
   	      "className": "text-right"               	                      	        	     
   	 	}   	 	
    	],    	
        "footerCallback": function( tfoot, data, start, end, display ) {
				var api = this.api(), data;
				var numFormat = $.fn.dataTable.render.number( '\,', '.', 2, '' ).display;

				api.columns([3,tel_col_str], { page: 'current'}).every(function() {
					var sum = this
				    .data()
				    .reduce(function(a, b) {
				    var x = parseFloat(a) || 0;
				    var y = parseFloat(b) || 0;
				    	return x + y;
				    }, 0);			
				       
				    $(this.footer()).html(numFormat(sum));
				}); 
			},
	});
			
    $('#add_form').on("submit", function(event){  
        event.preventDefault();                        
        $('#acc_id').val(acc_id);
        
        $.ajax({  
            url:"telekom_bill.ajax.php",  
            method:"POST",  
            data:{action:'add_new_bill', data: $('#add_form').serialize()},  
            success:function(data){   
                 $('#editItem').modal('hide');  
                 $('#bootstrap-data-table').html(data);
//                  location.reload();  
            }  
       });
        
	});

    $('#from_date, #to_date, #paid_date, #due_date').datepicker({
          format: "dd-mm-yyyy",
          autoclose: true,
          orientation: "top left",
          todayHighlight: true
    });

    $('#telefon_bill').on("submit", function(e){ 
        e.preventDefault();
        var telefon = $("input[name='telefon']").val();
        var type = $("input[name='type']").val();   
        if(telefon != '' && type !=''){
        	$(".data-tables tbody").append("<tr data-telefon='"+telefon+"' data-type='"+type+"'><td>"+telefon+"</td><td>"+type+"</td><td class='text-center'><span class='btn-delete'><i class='fas fa-trash-alt 2x'></i></span></td></tr>");
        }
        else{
    		alert('Please fill in the field!');
        }      	
     	
        //push data into array
        TELEPHONE_LIST.push({
    		telefon: telefon,
    		type: type
    
        });
        console.log(TELEPHONE_LIST);
        $("input[name='telefon']").val('');
        $("input[name='type']").val('');
    });
        
    $('.save_telefon').on("click", function(event){  
        event.preventDefault();     
        console.log(TELEPHONE_LIST);    
        if(TELEPHONE_LIST.length != 0){
        	$.ajax({  
                url:"telekom_bill.ajax.php",  
                method:"POST",  
                data:{action:'add_new_telefon', acc_id:acc_id, telefon_list:TELEPHONE_LIST},  
                success:function(data){ 
                    location.reload();  
                    if(data){
    					alert("Successfully added!");
                    }                                                      	 
                }  
           	});
        }                     
    });

    //retrieve phone list on adding new data modal
    $(".button_add").on("click", function(){
    	$.ajax({  
            url:"telekom_bill.ajax.php",  
            method:"POST",  
            data:{action:'retrieve_telefon_list', acc_id:acc_id},  
            success:function(data){                     
                response = $.parseJSON(data);                
                $("#tel_count").val(response.length);
                var count = 0;
                var dataTable = $('#telefon_usage').DataTable({
                	fixedHeader: true,
                	paging: false,
                	searching: false,
                	ordering: false,
                	bInfo : false              		
				});
                dataTable.clear();
                $.each(response, function(i, item) { 
                    console.log(item.id);   
                    count++;   
                    var tbody = "<tr data-telefon_id='"+item.id+"' data-telefon_no='"+item.tel_no+"'><td>"+item.tel_no+"</td><td><input type='hidden' class='txtedit form-control form-control-sm' name='phone_"+count+"' id='phone_"+count+"' value='"+item.id+"'><input type='text' class='txtedit form-control form-control-sm' name='name_"+count+"' id='name_"+count+"'></td></tr>";             
                    $('#telefon_usage').DataTable().row.add($(tbody).get(0)).draw();
                });                                                     	 
            }  
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
    });
  </script>
</body>
<style>
#telefon_usage,#telefon_list{
    font-size:12px; 
    margin:0px; 
    padding:.5rem; 
}
</style>
</html>