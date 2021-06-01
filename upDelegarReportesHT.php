<script language="JavaScript" type="text/JavaScript">
	<!--
	function MM_openBrWindow(theURL,winName,features) { //v2.0
	  window.open(theURL,winName,features);
	}
	//-->
</script>
<?php

//hecho por Omar Osuna
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";


if($recarga==2)
{
	$curComm = mssql_query("BEGIN TRANSACTION");
			//validacion de unidad
			 $unidad;
		
			$qry = "UPDATE DelegadosDivisionRpts SET ";
		 
				
				$qry=$qry." estado='".$usuario1."',";
				$qry=$qry."fechaMod='". gmdate("n/d/y")."', " ;
				$qry=$qry."usuarioMod='". $laUnidad."'";
				$qry=$qry." WHERE unidadDelegada=".$unidad;
				$cursorIn = mssql_query($qry) ;
				
				
			
		
	
	
		
	
				if  ((trim($cursorIn) != ""))
				{
					//Se hace un commit para asegurar la transacción
					$curComm = mssql_query("COMMIT TRANSACTION");
		//			$curRoll = mssql_query("ROLLBACK TRANSACTION");
					if(trim($curComm) != "")
					{
						echo ("<script>alert('La Grabación se realizó con éxito.');</script>");
					}
				}
				else
				{
					//Se deshacen todas las operaciones de la transacción
					$curRoll = mssql_query("ROLLBACK TRANSACTION");
					if(trim($curRoll) != "")
					{
						echo ("<script>alert('Error en la operación');</script>");
					}
		        }
	

#
echo "<script>
			window.close();
			MM_openBrWindow('DelegarReportesHT.php', 'DelegarReportes', 'toolbar=yes, scrollbars=yes, resizable=yes, width=960, height=700' );
		  </script>";
#*/		
		
	
	}

//Trae la información de la divisiones que tiene a cargo
//16Jul2007
$sql="Select D.*, U.nombre nomDir, U.apellidos apeDir ";
$sql=$sql." from divisiones D, Usuarios U " ;
$sql=$sql." where D.id_director *= U.unidad " ;
//$sql=$sql." and D.id_director = " . $laUnidad; 
//14Ago2012
//PBM
//La anterior línea se cambió para que los subdirectores de división tambien tengan acceso a este reporte.
$sql=$sql." and (D.id_director = " . $laUnidad; 
$sql=$sql." or D.id_subdirector = " . $laUnidad . ") "; 
$cursor = mssql_query($sql);
if ($reg=mssql_fetch_array($cursor)) {
	$elIDDivision = $reg[id_division];
	$elNomDivision = $reg[nombre];
	$elNomDirector = $reg[nomDir] . " " . $reg[apeDir];
}


//genera la consulta para traer los usuarios delegados para generar los reportes
//8 de abril 2013








//--PROYECTOS en los que ha participado la división AMBIENTAL



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
<!--

window.name="winHojaTiempo";

function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}


function envia2(){ 
var v1, mensaje;
v1='s';
v2='s';
v3='s';
v4='s';
v5='s';
v6='s';
v7='s';
v8='s';
v9='s';
v10='s';
msg1 = '';
mensaje = '';
//Si todas las validaciones fueron correctas, el formulario hace submit y permite grabar

	//preguntar();


	if(document.form1.usuario1.value=="")
	{   v1='n';
		mensaje="Seleccione un usuario \n";
	}


	if( ( msg1=="" && v1=='s'))
	{
		document.form1.recarga.value="2";		
		document.form1.submit();
	}
	else {
		
		alert (mensaje);
	}
	
}
//-->
</script>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Reportes de Hoja de tiempo</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	

<form action="" method="post" name="form1">
<? $sql="select * from";

$sql=$sql." c.nombre='".$elNomDivision."'";
//$sql=$sql." and D.id_director = " . $laUnidad; 
//14Ago2012
//PBM
//La anterior línea se cambió para que los subdirectores de división tambien tengan acceso a este reporte.

$cursor1 = mssql_query($sql);
if ($reg=mssql_fetch_array($cursor1)) {
	$division = $reg[division];
	$departamento = $reg[departamento];
	$unidad = $reg[unidad];
    $id_division=$reg[id_division];
	$nombre_delegado = $reg[nombre] . " " . $reg[apellido];
}
?>
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Delegar reportes de facturaci&oacute;n </td>
  </tr>
</table>
	</td>
    </tr>
    </table>
    <?
 $unidad;
$sql1="select a.nombre,a.apellidos, b.estado from DelegadosDivisionRpts b,usuarios a
	       where a.unidad=b.unidadDelegada  and b.unidadDelegada = " . $unidad; 
$cursor = mssql_query($sql1);
if ($reg=mssql_fetch_array($cursor)) {
	$estado = $reg[estado];
	$nombre = $reg[nombre] . " " . $reg[apellidos];
}
//


?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td>Divisi&oacute;n</td>
        <td>Director</td>
        </tr>
      <tr class="TxtTabla">
        <td><? echo ucwords(strtolower($elNomDivision)) ; ?></td>
        <td ><? echo ucwords(strtolower($elNomDirector)) ; ?></td>
        </tr>
    </table>
   
  </table>
	<table width="100%"  border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
     <tr class="TxtTabla">
     <td class="TituloTabla2"  width="70%" >usuario</td>
     <td class="TituloTabla2"  width="70%" >Estado</td>
     </tr>
     <tr class="TxtTabla">
                    <td><? echo $nombre; ?></td>
                    <td class="TxtTabla"><select name="usuario1" class="CajaTexto" id="usuario1">
                      
                      <option value="A" <? if($estado=='A'){echo $ss='selected';}?>>Activo</option>
                      <option value="I" <? if($estado=='I'){echo $ss='selected';}?>>Intactivo</option>
                      
        </select></td>
<?
//echo $sql_acti." *** ".mssql_get_last_message();

?>
      </tr>
  </table>
    
	  <table width="100%"  border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td align="right">
		  <input name="recarga" type="hidden" id="recarga" value="1">  
<input name="id_division" type="hidden" id="id_division" value="<? echo $id_division ?>">  
          <input name="reg" type="hidden" id="reg" value="<?php echo $m; ?>">       
          <input name="Submit2" type="button" class="Boton" value="Grabar" onClick="envia2()"></td>
        </tr>
      </table>
	  </td>
      </tr>
    </table>
	
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td></td>
      </tr>
    </table>
    

    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="right" class="TxtTabla">&nbsp;</td>
          </tr>
        </table>		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
          </tr>
        </table>
		</td>
      </tr>
    </table>
</form>
</body>
</html>
