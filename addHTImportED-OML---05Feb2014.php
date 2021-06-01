<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
<!-- <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//ES">	-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//ES" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />	
<!-- 
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
-->

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">	
<script language="JavaScript" type="text/JavaScript">
<!--
function envia1(){ 
//alert ("Entro a envia 1");
document.Form1.recarga.value="1";
document.Form1.submit();
}


function envia2(){ 
	var v1, msg1;
	v1='s';
	msg1 = '';
	
	//CantCampos=1+(2*document.Form1.pCantReg.value);
	
	if( document.getElementById('file').value == '' ){
		v1 = 'n';
		msg1 = 'Seleccione el archivo que va a subir al proyecto';
	}

	//Si todas las validaciones fueron correctas, el formulario hace submit y permite grabar
	if( v1 == 's' ){		
		document.Form1.recarga.value="2";
		document.Form1.submit();
	}
	else {
		alert (msg1);
	}
}
//-->
</script>
<title>.:: Planeaci&oacute;n de Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript" src="calendar.js"></script>
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<span class="TxtTabla">

</span>
<form action="" method="post" enctype="multipart/form-data"  name="Form1">
  <table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td class="TituloUsuario">Importar EDT del proyecto </td>
    </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><img src="../images/Pixel.gif" width="4" height="2"></td>
        </tr>
      </table>      
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td width="20%" class="TituloTabla">Plantilla XLS de la EDT del proyecto. </td>
          <td class="TxtTabla"><input name="file" id='file' type="file" class="CajaTexto" size="70"></td>
        </tr>
	</table>
        <table width="100%"  border="0" cellspacing="1" cellpadding="0">
            <tr>
                <td class="TxtTabla"><strong>NOTA IMPORTANTE</strong>: <br>
                    El proceso de importaci&oacute;n verificar&aacute; la informaci&oacute;n almacenada en la estructura de la plantilla. S&oacute;lo se cargar&aacute; de manera 
                    autom&aacute;tica la informaci&oacute;n si cumple con las caracter&iacute;sticas iniciales. El proceso presentar&aacute; el resumen de la actividad realizada. 
                </td>
	        </tr>
    	    <tr>
        		<td align="right" class="TxtTabla">
		  		    <input name="cualProyecto" type="hidden" id="cualProyecto" value="<? echo $cualProyecto; ?>" />
		  		    <input name="recarga" type="hidden" id="recarga" value="1">
		      <input name="Submit" type="button" class="Boton" value="Importar" onClick="envia2()" ></td>
	        </tr>
	</table>
	</td>
  </tr>
</table>

	     </td>
         </tr>
         </table>
</form> 

<?php
	session_start();
	//include("../verificaRegistro2.php");
	//include('../conectaBD.php');
	
	include "funciones.php";
	include "validacion.php";
	include "validaUsrBd.php";
	
	if( $recarga == 2 ){
		if($_FILES['file']['name'] != ''){
			$mal = 0;
			$depVer = 0;
			$error="no";
			require_once 'reader/Classes/PHPExcel/IOFactory.php';
			//Funciones extras
			function get_cell( $cell, $objPHPExcel ){
				//select one cell
				$objCell = ($objPHPExcel->getActiveSheet()->getCell($cell));
				//get cell value
				return $objCell->getvalue();
			}
			
			function pp( &$var ){
				$var = chr(ord($var)+1);
				return true;
			}
	
			$name	  = $_FILES['file']['name'];
			$tname 	  = $_FILES['file']['tmp_name'];
			$type 	  = $_FILES['file']['type'];
			
			#echo "<h1>".$type."</h1>";
			$band=0;	
			if($type == 'application/vnd.ms-excel')
			{
				// Extension excel 97
				$ext = 'xls';
			}
			else if($type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
			{
				// Extension excel 2007 y 2010
				$ext = 'xlsx';
			}
			else if( $type == 'application/octet-stream' )
			{
				#	Archivo de excel modificado
				$ext = 'xlsx';
			}
			else
			{
				// Extension no valida
				$band=1;
				echo "<script type='text/javascript'> alert('Solo se permiten documentos excel') </script>";
				#exit();
			}
			//si band=0, es por que el archivo ingresado, es un documento excel valido
			if($band==0){
				$xlsx = 'Excel2007';
				$xls  = 'Excel5';
				$error = "no";  //para identificar si se presenta un error al momento de la grabacion 
				$aError;
				$vError;
				$fError;
				#	creando el lector
				$objReader = PHPExcel_IOFactory::createReader($$ext);
				#	cargamos el archivo
				$objPHPExcel = $objReader->load( $tname );	
				#echo 'Titulo : '.$objPHPExcel->getProperties()->getTitle();
				$dim = $objPHPExcel->getActiveSheet()->calculateWorksheetDimension();
				#echo 'Ingreso.<br />';
				#echo 'fecha XLS : '.$objPHPExcel->getProperties()->getCreated().'<br />';
				#echo 'fecha DATE SIN STRTOTIME : '.date('m/d/Y', ($objPHPExcel->getProperties()->getCreated()) ).'<br />';
				$fch = date('m/d/Y', ($objPHPExcel->getProperties()->getCreated()) );

				if( '05/02/2013' == $fch )
				{
					echo 'Se puede subir esta plantilla.';
				}
			}
		}
	}
?>

</body>
</html>

<? mssql_close ($conexion); ?>	
