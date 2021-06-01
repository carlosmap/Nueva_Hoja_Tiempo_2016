<?php
$excel=$_POST['export'];
$nomfile=date(dmY);
header("Content-type: application/vnd.ms-excel");
header("Content-disposition: attachment; filename=$nomfile.xls");
//header("Content-disposition: attachment; filename=rptFactura01.xls");

print $excel;
exit;
?> 