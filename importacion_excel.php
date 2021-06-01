<?php
session_start();
include("../verificaRegistro2.php");
include('../conectaBD.php');

//Establecer la conexión a la base de datos
$conexion = conectar();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>Programaci&oacute;n de Proyectos</title>
<LINK REL="stylesheet" HREF="../css/estilo.css" TYPE="text/css">
</head>
<script type="text/javascript">
function cargar()
{
	if(document.form1.file.value!='')
	{
//		document.form1.valor.value = 2;
//		alert("se envio el formulario"+valor);
		document.form1.submit();
	}
	else
	{
		alert("Por favor seleccione el documento excel a importar");
	}
}	
</script>
<body bgcolor="E6E6E6" leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" class="TxtTabla">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><? include("../bannerArriba.php") ; ?></td>
  </tr>
</table>
<table align="center"    border="0" cellspacing="1" cellpadding="0" bgcolor="#FFFFFF">
	<tr>
    <form name="form1" method="post" action="importacion_excel.php" enctype="multipart/form-data">
		<td  class="TituloTabla"> &nbsp; Seleccione el documento de excel &nbsp;</td>
		<td  class="TxtTabla">
          <input type="file" class="Boton" name="file"/> 
		</td>
		<td  class="TxtTabla">
          <input type="submit" class="Boton" onClick="cargar()" value="Importar Documento">
	  </td>
    </form>
  </tr>
</table>


<?php 

		if($_FILES['file']['name'] != '')
		{
			
			require_once 'reader/Classes/PHPExcel/IOFactory.php';

			//Funciones extras
			
			function get_cell($cell, $objPHPExcel){
				//select one cell
				$objCell = ($objPHPExcel->getActiveSheet()->getCell($cell));
				//get cell value
				return $objCell->getvalue();
			}
			
			function pp(&$var){
				$var = chr(ord($var)+1);
				return true;
			}
	
			$name	  = $_FILES['file']['name'];
			$tname 	  = $_FILES['file']['tmp_name'];
			$type 	  = $_FILES['file']['type'];
			
//			echo "Nombre documento: ".$name." Typo: ".$type;
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
			}else{
				// Extension no valida
				$band=1;
				echo "<script type='text/javascript'> alert('Solo se permiten documentos excel') </script>";
//echo -1;
				exit();
			}
			//si band=0, es por que el archivo ingresado, es un documento excel valido
			if($band==0)
			{
				$xlsx = 'Excel2007';
				$xls  = 'Excel5';
				$error="no";  //para identificar si se presenta un error al momento de la grabacion 
			//creando el lector
				$objReader = PHPExcel_IOFactory::createReader($$ext);
			
			//cargamos el archivo
				$objPHPExcel = $objReader->load($tname);
		
				$dim = $objPHPExcel->getActiveSheet()->calculateWorksheetDimension();
		
			// list coloca en array $start y $end
				list($start, $end) = explode(':', $dim);
				
				if(!preg_match('#([A-Z]+)([0-9]+)#', $start, $rslt)){
					return false;
				}
				list($start, $start_h, $start_v) = $rslt;
				if(!preg_match('#([A-Z]+)([0-9]+)#', $end, $rslt)){
					return false;
				}
				list($end, $end_h, $end_v) = $rslt;
		
			//empieza  lectura vertical
				//definimos el fango de inicio vertical, en la fila 2
				$start_v="2";
				//definimos hasta donde va a leer columna D
				$end_h="D";
//				echo "<br> -".gettype($start_v)."<br>".$start_v."<br>";
			
//			echo "inicio columna vertical ".$start_v." <br> inicio horizontal ".$start_h." <br> fin vertical ".$end_v."<br>fin horizontal ".$end_h;
			
//				$table = "<table  border='1'>";
				for($v=$start_v; $v<=$end_v; $v++)
				{
				//empieza lectura horizontal
//					$table .= "<tr>";
					
					$sql_insert_edt="insert into HojaDeTiempo.dbo.tmpEDT2 (codProyecto ,codActividad ,identificador ,nombre ,dependeDe) values(1";
					for($h=$start_h; ord($h)<=ord($end_h); pp($h))
					{
						$cellValue = get_cell($h.$v, $objPHPExcel);
//						$table .= "<td>";
						if($cellValue !== null)
						{
							//las columnas B y C, contienen datos alfanumericos, le agregamos '' para insertarlos en la base de datos
							if($h=="B" or $h=="C")
							{
//echo " antes -----   $cellValue  ------ <br>";
//$cellValue=strtr($cellValue, "Ã", "Í"); 
		$carEspecial = array( 'á', 'é', 'í', 'ó', 'ú', #1
							  'ä', 'ë', 'ï', 'ö', 'ü', #2
							  'à', 'è', 'ì', 'ò', 'ù', #3
							  'â', 'ê', 'î', 'ô', 'û', #4	MAY
							  'Á', 'É', 'Í', 'Ó', 'Ú', #5
							  'Ä', 'Ë', 'Ï', 'Ö', 'Ü', #6
							  'À', 'È', 'Ì', 'Ò', 'Ù', #7
							  'Â', 'Ê', 'Î', 'Ô', 'Û', #8
							  '%', '|', '°', '¬', '"', #9
							  '#', '$', '%', '&', '(', #10
							  ')', '=', '?', '.', '¡',  #11
							  '¿', '+', '{', '}', '[', #12
							  ']', ':', ',', '@', '~', 'ñ', 'Ñ'  );
		$remplazar = array( 'a', 'e', 'i', 'o', 'u', #1
							'a', 'e', 'i', 'o', 'u', #2
							'a', 'e', 'i', 'o', 'u', #3
							'a', 'e', 'i', 'o', 'u', #4	MAY
							'A', 'E', 'I', 'O', 'U', #5
							'A', 'E', 'I', 'O', 'U', #6
							'A', 'E', 'I', 'O', 'U', #7
							'A', 'E', 'I', 'O', 'U', #8
							'-', '-', '-', '-', '-', #9
							'-', '-', '-', '-', '-', #10
							'-', '-', '-', '-', '-', #11
							'-', '-', '-', '-', '-', #12
							'-', '-', '-', '-', '-', 'n', 'N' );	
		#$quitarguion = str_replace( $carEspecial, $remplazar, $cadenados );
		$cellValue=str_replace($carEspecial,$remplazar,$cellValue);

//$cellValue=strtr($cellValue, "í", "");

//$cellValue=str_replace($cellValue,'Ã','i');


//$cellValue=strtr($cellValue, 'Ã', "i");
//$cellValue=str_replace('Ã','i',$cellValue);
//echo " despues -----   $cellValue  ------ <br>";
//								$cellValue=strtoupper($cellValue);
								//REPLACE('hola hola','a','@')as val
//								$cellValue=str_replace("Ã","@",$cellValue);
/*								$cellValue=str_replace("Ã©","É",$cellValue); 
								$cellValue=str_replace("Ã‰","É",$cellValue);
								$cellValue=str_replace("Ã","Í",$cellValue);
								$cellValue=str_replace("Ã­","Í",$cellValue);
								$cellValue=str_replace("Ã“","Ó",$cellValue);
								$cellValue=str_replace("Ã±","ñ",$cellValue);
replace( upper(ltrim(rtrim(C.nombre)) ), 'Á', 'A')
								
*/							
			//				$cellValu=preg_replace("/Ã/", "í",$cellValue);
//echo " --**---*- ".$cellValu."<br>";
					
			$sql_insert_edt=$sql_insert_edt.",'".$cellValue."'";
								//$ff='Ã­';		
//$sql_insert_edt=$sql_insert_edt.",REPLACE(SUBSTRING('".$cellValue."', 1, DATALENGTH('".$cellValue."')),'Ã­','i')";
//$sql_insert_edt=$sql_insert_edt.",REPLACE('".$cellValue."','Ã','i')";
								//$sql_insert_edt=$sql_insert_edt.",REPLACE(ltrim(rtrim(UPPER('".$cellValue."')) ),'".$ff."','I')";
							}
							//si no el resto de columnas son numericos, y se almacena de forma normal
							else
							{
								$sql_insert_edt=$sql_insert_edt.",".$cellValue;
							}


//							echo "insert  ".$cellValue;

							$table .= $cellValue;
						}
						$table .= "</td>";
					}
//					$table .= "</tr>";
					$sql_insert_edt=$sql_insert_edt.")";
//		echo "<br> ".$sql_insert_edt;
					$cursor_insert_edt=mssql_query($sql_insert_edt);
//echo mssql_get_last_message()."<br><br>";
					if($cursor_insert_edt=="")
					{
						$error="si";
					}
				}
//				$table .= "</table>";

				if($error=="no")
				{
					echo "<script type='text/javascript'> alert('Informacion almacenada');</script>";
				}
				else
				{
					echo "<script type='text/javascript'> alert('Error en la grabación');</script>";
				}
		
	//			echo $table;		
			}
		}
//	}	
		?>	
	


</body>

</html>	