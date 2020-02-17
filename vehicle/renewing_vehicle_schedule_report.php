<?php
	require_once('../assets/config/database.php');
	
	session_start();
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
	
	$date_start = isset($_POST['date_start']) ? $_POST['date_start'] : date('01-m-Y');
	$date_end = isset($_POST['date_end']) ? $_POST['date_end'] : date('t-m-Y');
?>

<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Eng Peng Vehicle</title>
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
        .button_search{
            position: absolute;
            left:    0;
            bottom:   0;
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
                                <strong class="card-title">Renewing Vehicle Schedule</strong>
                            </div>
                            <div class="card-body">
                                <form id="myform" enctype="multipart/form-data" method="post" action="">                	                   
                    	            <div class="form-group row col-sm-12">
                                        <div class="col-sm-3">
                                            <label for="date_start" class="form-control-label"><small class="form-text text-muted">Date Start</small></label>
                                            <div class="input-group">
                                              <input type="text" id="date_start" name="date_start" class="form-control" value="<?=$date_start?>" autocomplete="off">
                                              <div class="input-group-addon"><i class="fas fa-calendar-alt"></i></i></div>
                                            </div>                            
                                        </div>
                                        <div class="col-sm-3">
                                            <label for="date_end" class="form-control-label"><small class="form-text text-muted">Date End</small></label>
                                            <div class="input-group">
                                              <input type="text" id="date_end" name="date_end" class="form-control" value="<?=$date_end?>" autocomplete="off">
                                              <div class="input-group-addon"><i class="fas fa-calendar-alt"></i></i></div>
                                            </div>                             
                                        </div>
                                        <div class="col-sm-4">                                    	
                                        	<button type="submit" class="btn btn-primary button_search ">Submit</button>
                                        </div>
                                     </div>    
                                </form>
                            </div>
                            <hr>
                            <div class="card-body">
                                <table id="vehicle_schedule" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                        	<th>No.</th>
                                            <th>Company</th>
											<th>Vehicle No.</th>
											<th>R-Tax, Sum & NCD</th>
											<th>Task</th>
											<th>Date</th>
                                            <th>Next Due Date</th>            
                                            <th>&nbsp;</th>                                
                                        </tr>
                                    </thead>
                                    <tbody>									           
                                    </tbody>                                    
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- .animated -->
        </div><!-- .content -->
        <!-- Modal edit next due date  -->
        <div id="editItem" class="modal fade">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Schedule</h4>
                </div>
                <div class="modal-body">
                    <form role="form" method="POST" action="" id="update_form">
                        <input type="hidden" name="_token" value="">
                        <input type="hidden" id="id" name="id" value="">
                        <input type="hidden" id="task" name="task" value="">
                        <div class="form-group row col-sm-12">
                            <label for="next_due_date" class="form-control-label"><small class="form-text text-muted">Next due date</small></label>  
                            <div class="input-group">
                              <input type="text" id="next_due_date" name="next_due_date" class="form-control" autocomplete="off">
                              <div class="input-group-addon"><i class="fas fa-calendar-alt"></i></div>
                            </div>                            
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary update_data ">Update</button>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    	</div><!-- /.modal -->
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
	<script type="text/javascript">
      $(document).ready(function() {
    	  var table = $('#vehicle_schedule').DataTable({
              "processing": true,
              "serverSide": true,
              "searching":false,
              "ajax":{
               "url": "renewing.vehicle.schedule.ajax.php",           	
               "data" : function ( data ){
						data.date_start = '<?=$date_start?>';
						data.date_end = '<?=$date_end?>';
                   }
              },
              "columnDefs": [
            	  {
            	      "targets": 7, // your case first column
            	      "className": "text-center",                	     
            	 }],
            "dom": 'Bfrtip',
            "buttons": [ 
             { 
            	extend: 'excelHtml5', 
            	messageTop: 'Renewing Vehicle Schedule',
            	footer: true 
             },
             {
            	extend: 'print',
            	messageTop: 'Renewing Vehicle Schedule',
            	footer: true,
            	customize: function ( win ) {
                    $(win.document.body)
                        .css( 'font-size', '10pt' );
            
                    $(win.document.body).find( 'table' )
                        .addClass( 'compact' )
                        .css( 'font-size', 'inherit' );
                }
             }
            ],
          });

          //retrieve data
          $(document).on('click', '.edit_data', function(){
  			var id = $(this).attr("id");

        	});
        //update form
          $('#update_form').on("submit", function(event){  
              event.preventDefault();  
              if($('#next_due_date').val() == ""){  
                   alert("Date is required");  
              }                 
              else{  
                   $.ajax({  
                        url:"renewing_next_due_date.php",  
                        method:"POST",  
                        data:$('#update_form').serialize(),  
                        success:function(data){   
                             $('#editItem').modal('hide');  
                             $('#vehicle_schedule').html(data);  
                        }  
                   });  
              }  
         });
      	$('#next_due_date').datepicker({
      		format: "dd-mm-yyyy",
            autoclose: true,
            orientation: "top left",
            todayHighlight: true
            });
      });
      $('#date_start, #date_end').datepicker({
          format: "dd-mm-yyyy",
          autoclose: true,
          orientation: "top left",
          todayHighlight: true
      });

       $('#myform').on("submit", function(event){  
    	   	table.clear();
  			table.ajax.reload();
  			table.draw();      
       });

      function editFunction(id, task){	
    		$.ajax({
    				url:"renewing.vehicle.schedule.ajax.php",
    				method:"POST",
    				data:{id:id, task: task},
    				dataType:"json",
    				success:function(data){	  	  					
      					$('#next_due_date').val(data.aaData[0][6]);	
      					$('#id').val(id);	
      					$('#task').val(data.aaData[0][4]);	
                    	$('#editItem').modal('show');
    			}
    		});
      }
  </script>
</body>
</html>