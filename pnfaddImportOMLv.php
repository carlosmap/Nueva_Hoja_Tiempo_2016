<?		session_start();	?>	
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
<title>Investigaciones Geot&eacute;cnicas</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript" src="calendar.js"></script>
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<span class="TxtTabla">

</span>
<form action="" method="post" enctype="multipart/form-data"  name="Form1">
  <table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td class="TituloUsuario">Importar EDT del proyecto <?= $_SESSION['sesUnidadUsuario'] ?></td>
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
            El proceso de importaci&oacute;n verificar&aacute; la informaci&oacute;n almacenada en la estructura de la plantilla. S&oacute;lo se cargar&aacute; de manera autom&aacute;tica la informaci&oacute;n si cumple con las caracter&iacute;sticas iniciales. El proceso presentar&aacute; el resumen de la actividad realizada. </td>
        </tr>
        <tr>
          <td align="right" class="TxtTabla">
  		    <input name="recarga" type="hidden" id="recarga" value="1">
  		    <input name="Submit" type="button" class="Boton" value="Guardar" onClick="envia2()" ></td>
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
	//include("../verificaRegistro2.php");
	//include('../conectaBD.php');
	
	//Establecer la conexión a la base de datos
	//$conexion = conectar();
	include "funciones.php";
	include "validacion.php";
	include "validaUsrBd.php";
	
	if( $recarga == 2 ){
		if($_FILES['file']['name'] != ''){
			$mal = 0;
			$depVer = 0;
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
			
			function vCifra( $cifra ){
				if( is_numeric( $cifra ) )
					$b = $cifra;
				else
					$b = 0;
				return $b;
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
			else if( $type == 'application/octet-stream' ){
				#	Archivo de excel modificado
				$ext = 'xlsx';
			}
			else{
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
				#	creando el lector
				$objReader = PHPExcel_IOFactory::createReader($$ext);
				#	cargamos el archivo
				$objPHPExcel = $objReader->load( $tname );	
				$dim = $objPHPExcel->getActiveSheet()->calculateWorksheetDimension();
		
				#	list coloca en array $start y $end
				list($start, $end) = explode(':', $dim);
				
				if(!preg_match('#([A-Z]+)([0-9]+)#', $start, $rslt)){
					return false;
				}
				list($start, $start_h, $start_v) = $rslt;
				if( !preg_match( '#([A-Z]+)([0-9]+)#', $end, $rslt)){
					return false;
				}
				list( $end, $end_h, $end_v ) = $rslt;
				
				#	empieza  lectura vertical, definimos el fango de inicio vertical, en la fila 2
				$start_v = "2";
				
				#	definimos hasta donde va a leer columna D
				$end_h = "E";
		
				#/*#	VERIFICAR CONSECUTIVO
				$primero = get_cell( $start_h.$start_v, $objPHPExcel );
				if( $primero == 1 ){
					#$mal = 1;
					#$depVer = 2;
					#/*
					$inc = $primero;
					$valor = 0;
					#$mal = 0;
					#$depVer = 0;
					for( $v = $start_v; $v <= $end_v; $v++ ){
						#	validar consecutivo
						if( $inc != get_cell($start_h.$v, $objPHPExcel) ){
							$mal = 1;
							$aError[$v] = 1;
						}
						else
							$aError[$v] = 0;
						$inc++;
						#	Mirar asignacion
						$lote = explode( '.', get_cell("B".$v, $objPHPExcel) );
						switch( count($lote) ){
							case 1:
								#	Valida que el lote de control venga de manera correcta.
								if( get_cell("D".$v, $objPHPExcel) != 0 ){
									$depVer = 1;
									$aErrorD[$v][0] = count( explode( '.', get_cell("B".$v, $objPHPExcel) ) );
									$aErrorD[0][$v] = 1; #	Indica la posicion del error
								}
								else{
									$lc = get_cell("A".$v, $objPHPExcel);
									$aErrorD[$v] = 0;
								}
							break;
								
							case 2:
								#	Valida que el Lote de trabajo corresponda al lote de control
								if( get_cell("D".$v, $objPHPExcel) != $lc ){
									$depVer = 1;
									$aErrorD[$v][0] = count( explode( '.', get_cell("B".$v, $objPHPExcel) ) );
									$aErrorD[0][$v] = 1; #	Indica la posicion del error
								}
								else{
									$lt = get_cell("A".$v, $objPHPExcel);									
									$aErrorD[0][$v] = 0;
								}
							break;
							
							case 3:
								#	Valida que la division dependa del lote de trabajo
								if( get_cell("D".$v, $objPHPExcel) != $lt ){
									$depVer = 1;
									$aErrorD[$v][0] = count( explode( '.', get_cell("B".$v, $objPHPExcel) ) );
									$aErrorD[0][$v] = 1; #	Indica la posicion del error
								}
								else{
									$ld = get_cell("A".$v, $objPHPExcel);	
									$aErrorD[0][$v] = 0;
									$valor = vCifra( get_cell("E".$v, $objPHPExcel) );	# Valida que valla una cifra valida
									if( $valor == 0 ){
										$depVer = 1;
										$aErrorD[0][$v] = 2;	
									}
								}
							break;
							
							case 4:
								# Valida que la actividad corresponda a la division
								if( get_cell("D".$v, $objPHPExcel) != $ld ){
									$depVer = 1;
									$aErrorD[$v][0] = count( explode( '.', get_cell("B".$v, $objPHPExcel) ) );
									#$valor = vCifra( get_cell("D".$v, $objPHPExcel) );
									$aErrorD[0][$v] = 1; #	Indica la posicion del error
								}
								else{
									$aError[0][$v] = 0;
									$valor = vCifra( get_cell("E".$v, $objPHPExcel) );	# Valida que valla una cifra valida
									if( $valor == 0 ){
										$depVer = 1;
										$aErrorD[0][$v] = 2; #	
									}
								}
							break;							
						}
					}
					#*/
				}
				else
					$depVer = 1;
				#*/
				#echo "Mal : ".$mal."; Dep : ".$depVer;
				if( $mal == 0 and $depVer == 0 ){
					#empieza lectura horizontal
					$tran1 = "BEGIN TRANSACTION
							  GO ";
					$cursorTran = mssql_query( $tran1 );
					/*
					if(trim($cursorTran) == ""){
						$error = "No";
					}			
					#*/
					
					for( $v = $start_v; $v <= $end_v; $v++ ){
						$sql_insert_edt = "Insert into HojaDeTiempo.dbo.TMPactividadesHT2 
										   ( id_proyecto, id_Actividad, macroactividad, nombre, dependeDe, id_division, nivel, actPrincipal, valor, usuarioCrea, fechaCrea ) 
										   values( 683";
						for( $h = $start_h; ord($h) <= ord( $end_h ); pp($h) ){
							$cellValue = get_cell($h.$v, $objPHPExcel);
							if( $cellValue !== null ){
								#las columnas B y C, contienen datos alfanumericos, le agregamos '' para insertarlos en la base de datos
								if( $h == "B" or $h == "C"){
									if( $h == "B" ){
										$list = explode( '.', $cellValue );
										$nivel = count($list);
									}
									#	Buscar la division por el nombre para poner el id
									if( $nivel == 3 and $h == "C" ){
										$sqlDiv = "SELECT id_division FROM Divisiones WHERE nombre = '".$cellValue."'";
										$division = mssql_fetch_array( mssql_query( $sqlDiv ) );
									}								
									if( $nivel < 4 )
										$insert = strtoupper( $cellValue );									
									else
										$insert = ucfirst( $cellValue );
									
									# Remplazar caracteres
									$str1 = array( 'Á', 'É', 'Í', 'Ó', 'Ú', 'á', 'é', 'í', 'ó', 'ú', 'Ñ', 'ñ' );
									$str2 = array( 'A', 'E', 'I', 'O', 'U', 'a', 'e', 'i', 'o', 'u', 'N', 'n' );
									$insert = str_replace( $str1, $str2, $insert );
									#
									$sql_insert_edt .= ", '".$insert."'";
								}
								#si no el resto de columnas son numericos, y se almacena de forma normal
								else if( $h != "E" )
									$sql_insert_edt .= ", ".$cellValue;
								#	Indica a cual dependende la actividad
								if( $h == "D" and $cellValue == 0 )
									$lote = get_cell("A".$v, $objPHPExcel);
									
								if( $h == "E" and ( $nivel == 3 or $nivel == 4 ) )#{
									$recurso = get_cell("E".$v, $objPHPExcel);
								else
									$recurso = "NULL";
									#echo "Cell value : ".$cellValue."; Valor : ".$valor." <br />";
								#}
							}							
						}
						if( $nivel == 3 )
							$sql_insert_edt .= ", ".$division[id_division].", ".$nivel;
						else
							$sql_insert_edt .= ", NULL, ".$nivel;
						
						if( $recurso == "" )
							$recurso = 0;
						
						$sql_insert_edt .= ", ".$lote.", ".$recurso.", ".$_SESSION['sesUnidadUsuario'].", ".date( 'd/m/Y' )." )";
						
						

						$cursor_insert_edt = mssql_query( $sql_insert_edt );	
						if( !$cursor_insert_edt ){
							$error="si";
							#echo "***************************<br />".mssql_get_last_message()."<br />***************************<br />";
						}
						#echo $sql_insert_edt."<br />";
						#mssql_free_result($cursor_insert_edt);						
					}
					#	Validar formularios.
					if( $error == "no"){
						$tran2 = "COMMIT TRANSACTION
								  GO ";
						$cursorTran2 = mssql_query( "COMMIT TRANSACTION GO" );
						echo "<script type='text/javascript'> alert('Informacion almacenada');</script>";
					}
					else{
						$cursorTran2 = mssql_query("ROLLBACK TRANSACTION");
						echo "<script type='text/javascript'> alert('Error en la grabación');</script>";
					}
				}
				else{
					echo "<script type='text/javascript'> 
							alert('Hay un error en el archivo que se quiere subir. Verifiqué la información y corríjala.');
						  </script>";
				
				?>
<div align='center'>
  <table width='80%' border='0' cellpadding='0' cellspacing='1'  bgcolor='#FFFFFF'>
                	<tr class='TituloTabla' >
                    <td width="8%">CodActividad</td><td width="8%">Identificador</td><td width="50%">Nombre</td>
                    <td width="5%">DependeDe</td>
                    <td >Valor</td><td width="20%">Error</td>
                    </tr>
                <?php
					for( $v = $start_v; $v <= $end_v; $v++ ){
						?>
                        
						<tr class='TxtTabla'>
						<?php
						for( $h = $start_h; ord($h) <= ord( $end_h ); pp($h) ){
							$cellValue = get_cell($h.$v, $objPHPExcel);
						?><td width="8%" valign="top"><?= $cellValue ?></td>
                        <?
						}						
						#	Mostrar el error en donde se produjo por el consecutivo
						$mensaje = "<img src='img/icoAplica.gif' title='Bien' />";

						switch($aError[$v]){						
							case 1:
								$mensaje = "<img src='img/icoAlerta.gif' title='Consecutivo' /><b>CodActividad</b> no cumple con las normas. Debe seguir un consecutivo.";
							break;
						}
						#	Mostrar el error en donde se produjo por la dependencia
						switch($aErrorD[0][$v]){							
							case 1:
								switch( $aErrorD[$v][0] ){
									case 1:
										$lote = " el <b>código al Lote control</b> ";
									break;
									
									case 2:
										$lote = " el <b>Lote de control</b> ";
									break;
									
									case 3:
										$lote = " al <b>Lote de trabajo</b> ";
									break;
									
									case 4:
										$lote = " a la <b>Division</b> ";
									break;
								} 
								$mensaje = "<img src='img/icoAlerta.gif' title='Dependencia' />No corresponde".$lote."que fue asignada.";
								$lote = "";
							break;
							case 2:
								$mensaje = "<img src='img/icoAlerta.gif' title='Cifra no valida' />El dato ingresado no es acorde a lo solicitado.";
							break;
						}
						#	Mostrar el error en donde se produjo por el Valor ingresado
						?>
    <td width="8%" valign="top"><?= $mensaje ?></td></tr><?
					}
					?>
    </table>
                <p>&nbsp;</p>
                </div>
                    <?
				}
				/*
				if( $error == "no")
					echo "<script type='text/javascript'> alert('Informacion almacenada');</script>";				
				else
					echo "<script type='text/javascript'> alert('Error en la grabación');</script>";
				#*/
			}
		}
	}
?>

</body>
</html>

<? mssql_close ($conexion); ?>	
