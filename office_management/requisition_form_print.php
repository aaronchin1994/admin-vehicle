<?php
    require_once('../assets/config/database.php');
    require_once('../function.php');
    require_once('../check_login.php');
    global $conn_admin_db;
    
    $rq_id = $_GET['rq_id'];
    $query = "SELECT *, (SELECT NAME FROM company WHERE company.id = om_requisition.company_id ) AS company_name,
                (SELECT cr_name FROM credential WHERE cr_id=om_requisition.user_id) AS prepared_by FROM om_requisition WHERE id='$rq_id'";
    
    $result = mysqli_query($conn_admin_db, $query) or die(mysqli_error($conn_admin_db));
    $row = mysqli_fetch_array($result);
    $company_name = $row['company_name'];
    $recipient = $row['recipient'];
    $username = itemName("SELECT cr_name FROM credential WHERE cr_id='".$row['user_id']."'");
    $serial_no = $row['serial_no'];
    $date = dateFormatRev($row['date']);
    $payment_date = dateFormatRev($row['payment_date']);
    $status = $row['status'];// 0-pending, 1-confirm, 2-rejected/cancelled
    
    //get the particular details
    $particular_query = "SELECT * FROM om_requisition_item WHERE rq_id='$rq_id'";
    $sql_result = mysqli_query($conn_admin_db, $particular_query) or die(mysqli_error($conn_admin_db));
    $particular_data = [];
    while ($row = mysqli_fetch_array($sql_result)){
        $particular_data[] = $row;
    }
?>

<html>
<title>PAYMENT REQUISITION FORM</title>
<?php include('../allCSS1.php')?>
<style>
.table-1 {
    padding:5px;
    border-collapse:collapse;
}
.table-content {
    border: 1px solid black;
    border-collapse:collapse;
    padding:5px;    
 }
 .button{
    width:10%;
    font-size:10pt;
 }
@page {
/*     font-size:11px; */
    font-family: "Open Sans", sans-serif;
    size: auto;  
    margin: 5mm; 
}
@media print {
    #print{
        display:none;
    }
}   
</style>
<body>
<table class="table-1" style="width: 100%">
<tr>
	<th class="table-1" colspan="6" style="text-align:center;">PAYMENT REQUISITION FORM</th>
</tr>
<tr>
    <td class="table-1">Company</td>
    <td>: &nbsp;<?=$company_name?></td>    
</tr>
<tr>
    <td class="table-1">To</td>
    <td>: &nbsp;<?=$recipient?></td>
    <td class="table-1">Serial No.</td>
    <td class="table-1">: &nbsp;<?=$serial_no?></td>
    <td class="table-1">Date</td>
    <td class="table-1">: &nbsp;<?=$date?></td>
</tr>
<tr>
    <td class="table-1" colspan="6">We append below the details list of payment required to be paid on <b><?=$payment_date?></b></td>    
</tr>
</table>
<br>
<table class="table-content" style="width: 100%">
<tr>
    <th class="table-content text-center">No.</th>
    <th class="table-content text-center">Particular</th>
    <th class="table-content text-right">Total (RM)</th>
    <th class="table-content text-center">Remark</th>
</tr>
<?php 
    $counter = 0;
    $total = 0;
    foreach ($particular_data as $data){
    $counter++;
    $total += $data['total'];
    ?>  
    <tr>
        <td class="table-content" style="text-align: center"><?=$counter?>.</td>
        <td class="table-content"><?=$data['particular']?></td>
        <td style='text-align: right;' class="table-content"><?=number_format($data['total'],2)?></td>
        <td class="table-content text-center">&nbsp;<?=$data['remark']?></td>
    </tr>
<?php }?>    
<tr>
    <td class="table-content">&nbsp;</td>
    <td class="table-content">&nbsp;</td>
    <td class="table-content">&nbsp;</td>
    <td class="table-content">&nbsp;</td>
</tr>
<tr>
    <th class="table-content">&nbsp;</th>
    <th class="table-content" style='text-align: right;'>Total</th>
    <th class="table-content" style='text-align: right;'><?=number_format($total,2)?></th>
    <th class="table-content">&nbsp;</th>
</tr>
</table>
<br><br>
<table class="table-1" style="width: 100%">
<tr>
    <td class="table-1 text-center">Requested By</td>
    <td class="table-1 text-center">Verified By</td>
    <td class="table-1 text-center">Processed By</td>
</tr>
<tr>
    <td class="table-1">&nbsp;</td>
    <td class="table-1">&nbsp;</td>
    <td class="table-1">&nbsp;</td>
</tr>
<tr>
    <td class="table-1 text-center">_____________________</td>
    <td class="table-1 text-center">_____________________</td>
    <td class="table-1 text-center">_____________________</td>
</tr>
<tr>
    <td class="table-1 text-center">Catherine Emas</td>
    <td class="table-1 text-center">Department In-Charge</td>
    <td class="table-1 text-center">Acc. Personnel</td>
</tr>
</table>
</body>
<br><br>
<div style="text-align: center;">
	<button type="button" name='print' id="print" class="btn btn-success"  onClick='window.print();window.close();' >Print</button>
</div>

</html>