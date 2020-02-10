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
                                <strong class="card-title">Road Tax Summary</strong>
                            </div>
                            <div class="card-body">
                                <table id="roadtax_summary" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                        	<th rowspan="2">No.</th>
											<th rowspan="2">Vehicle No.</th>
                                            <th>Use Under</th>
											<th>LPKP Permit</th>
											<th>Fitness Test</th>
											<th colspan="2">Insuranse</th>
											<th colspan="3">Road Tax</th>
                                            <th>Road Tax</th>
                                        </tr>
                                        <tr>
                                            <th>Company</th>
											<th>Due Date</th>
											<th>Due Date</th>
											<th>Due</th>
											<th>Status</th>
                                            <th>From</th>
                                            <th>To</th>
                                            <th>Period</th>
                                            <th class="sum">Amount(RM)</th>
                                        </tr>
                                    </thead>
                                    <tbody>            
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="10" class="text-right font-weight-bold">TOTAL</td>
                                            <td class="text-right font-weight-bold"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
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
	
	<script type="text/javascript">
        $(document).ready(function() {
            $('#roadtax_summary').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax":{
                 "url": "roadtax.summary.ajax.php",           	
                 "data" : function ( data ){}
                },
                "footerCallback": function( tfoot, data, start, end, display ) {
    				var api = this.api(), data;

    				for( var i=10; i < 11; i++ ){
    					api.columns(i, { page: 'current'}).every(function() {
    							var sum = this
    						    .data()
    						    .reduce(function(a, b) {
    						    var x = parseFloat(a) || 0;
    						    var y = parseFloat(b) || 0;
    						    	return x + y;
    						    }, 0);			
    						       
    						    $(this.footer()).html(parseFloat(sum).toFixed(2));
    					}); 
    				} 
    			},
                'columnDefs': [
                	  {
                	      "targets": 10, // your case first column
                	      "className": "text-right",                	     
                	 }],
            });
        });
  </script>
</body>
</html>
