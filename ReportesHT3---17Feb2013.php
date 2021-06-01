<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

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


//Trae las personas que trabajan en la división del usuario activo
$sql="select u.unidad, u.nombre, u.apellidos, u.id_categoria, d.nombre as departamento, d.id_division,   ";
$sql=$sql." v.nombre as division, v.id_dependencia, x.nombre as dependencia, y.nombre as categoria   " ;
$sql=$sql." from usuarios u, departamentos d, divisiones v, dependencias x, categorias y " ;
$sql=$sql." where u.id_departamento = d.id_departamento " ;
$sql=$sql." and d.id_division = v.id_division  " ;
$sql=$sql." and v.id_dependencia = x.id_dependencia " ;
$sql=$sql." and u.id_categoria = y.id_categoria  " ;
$sql=$sql." and u.retirado IS NULL  " ;
$sql=$sql." and d.id_division =" . $elIDDivision ;
$sql=$sql." order by  u.apellidos " ;
$cursor = mssql_query($sql);

//16Jul2007
//ParaMostrar los botones del Reporte del director de proyecto y de división
$muestraDirProyecto = 0;
$sqlB="select count(*) esDirector from proyectos  ";
$sqlB=$sqlB." where (id_director = ". $laUnidad . " or id_coordinador = " . $laUnidad . " ) "; 
$cursorB = mssql_query($sqlB);
if ($regB=mssql_fetch_array($cursorB)) {
	$muestraDirProyecto = $regB[esDirector];
}

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
//-->
</script>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Reportes de Hoja de tiempo</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota1" style="position:absolute; left:200px; top:8px; width: 529px; height: 25px;">
		<div align="center"> 
		  Reportes Hoja de Tiempo <br> Director de división
		</div>
</div>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table><table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>

<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Consulta de otros periodos </td>
  </tr>
</table>
<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td bgcolor="#FFFFFF">
	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="1">
	<form name="form1" method="post" action="">
  <tr>
    <td width="15%" align="right" class="TituloTabla">Mes:&nbsp;</td>
    <td width="30%" class="TxtTabla">
	<? 
	//Seleccionar el mes cuando se carga la página por primera vez
	//si no cuando se recarga la página
	if ($pMes == "") {
		$mesActual=date("m"); //el mes actual
	}
	else {
		$mesActual= $pMes; //el mes seleccionado
	}

	$selMes1 = "";
	$selMes2 = "";
	$selMes3 = "";
	$selMes4 = "";
	$selMes5 = "";
	$selMes6 = "";
	$selMes7 = "";
	$selMes8 = "";
	$selMes9 = "";
	$selMes10 = "";
	$selMes11 = "";
	$selMes12 = "";
	for($m=1; $m<=12; $m++) {
		if (($m == $mesActual) AND ($m == 1)) {
			$selMes1 = "selected";
		}
		if (($m == $mesActual) AND ($m == 2)) {
			$selMes2 = "selected";
		}
		if (($m == $mesActual) AND ($m == 3)) {
			$selMes3 = "selected";
		}
		if (($m == $mesActual) AND ($m == 4)) {
			$selMes4 = "selected";
		}
		if (($m == $mesActual) AND ($m == 5)) {
			$selMes5 = "selected";
		}
		if (($m == $mesActual) AND ($m == 6)) {
			$selMes6 = "selected";
		}
		if (($m == $mesActual) AND ($m == 7)) {
			$selMes7 = "selected";
		}
		if (($m == $mesActual) AND ($m == 8)) {
			$selMes8 = "selected";
		}
		if (($m == $mesActual) AND ($m == 9)) {
			$selMes9 = "selected";
		}
		if (($m == $mesActual) AND ($m == 10)) {
			$selMes10 = "selected";
		}
		if (($m == $mesActual) AND ($m == 11)) {
			$selMes11 = "selected";
		}
		if (($m == $mesActual) AND ($m == 12)) {
			$selMes12 = "selected";
		}



	}
	
	?>
	&nbsp;      <select name="pMes" class="CajaTexto" id="pMes">
      <option value="1" <? echo $selMes1; ?> >Enero</option>
      <option value="2" <? echo $selMes2; ?>>Febrero</option>
      <option value="3" <? echo $selMes3; ?>>Marzo</option>
      <option value="4" <? echo $selMes4; ?>>Abril</option>
      <option value="5" <? echo $selMes5; ?>>Mayo</option>
      <option value="6" <? echo $selMes6; ?>>Junio</option>
      <option value="7" <? echo $selMes7; ?>>Julio</option>
      <option value="8" <? echo $selMes8; ?>>Agosto</option>
      <option value="9" <? echo $selMes9; ?>>Septiembre</option>
      <option value="10" <? echo $selMes10; ?>>Octubre</option>
      <option value="11" <? echo $selMes11; ?>>Noviembre</option>
      <option value="12" <? echo $selMes12; ?>>Diciembre</option>
    </select></td>
    <td width="15%" align="right" class="TituloTabla">A&ntilde;o:&nbsp;</td>
    <td class="TxtTabla">
	&nbsp;
	<select name="pAno" class="CajaTexto" id="pAno">
	<? 
	//Generar los años de 2006 a 2050
	for($i=2006; $i<=2050; $i++) { 
		
		//seleccionar el año cuando se carga la página por primera vez
		if ($pAno == "") {
			$AnoActual=date("Y"); //el año actual
		}
		else {
			$AnoActual= $pAno; //el año seleccionado
		}
		
		if ($i == $AnoActual) {
			$selAno = "selected";
		}
		else {
			$selAno = "";
		}
	?>
      <option value="<? echo $i; ?>" <? echo $selAno; ?> ><? echo $i; ?></option>
	 <? 
	 	
	 } //for 
	 
	 ?>

    </select>	</td>
    <td width="10%"><input name="Submit8" type="submit" class="Boton" value="Consultar"></td>
  </tr>
	</form>
</table>
	</td>
  </tr>
</table>



<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="TituloUsuario">Informaci&oacute;n de la Divisi&oacute;n</td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td>Divisi&oacute;n</td>
        <td width="40%">Director</td>
        </tr>
      <tr class="TxtTabla">
        <td><? echo ucwords(strtolower($elNomDivision)) ; ?></td>
        <td width="40%"><? echo ucwords(strtolower($elNomDirector)) ; ?></td>
        </tr>
    </table>
	  </td>
      </tr>
    </table>
	
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr>
        <td width="15%" class="FichaAct">Horas facturada por usuario y Proyecto </td>
        <td width="15%" class="FichaInAct"><a href="ReportesHT3b.php" class="FichaInAct1">Horas y valor facturado <br />
        por usuario y proyecto </a></td>
        <td width="15%" class="FichaInAct"><a href="ReportesHT3c.php" class="FichaInAct1">Proyectos <br />
  con facturaci&oacute;n </a></td>
        <td width="15%" class="FichaInAct"><a href="ReportesHT3d.php" class="FichaInAct1">Facturaci&oacute;n por <br />
        Departamentos </a></td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td height="1" colspan="5" class="TituloUsuario"> </td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Facturaci&oacute;n de la divisi&oacute;n</td>
  </tr>
</table>


    
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td width="10%">Unidad</td>
        <td>Usuarios que facturaron al Proyecto </td>
        <td width="2%">Categoria</td>
        <td width="50%"><table width="100%"  border="1" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF">
          <tr align="center">
            <td>Proyecto</td>
            <td width="20%">Horas facturadas mes actual </td>
            <td width="20%">Total horas facturadas </td>
            </tr>
        </table></td>
        <td width="5%">&nbsp;</td>
        </tr>
       <?
	  while ($reg=mssql_fetch_array($cursor)) {
	  ?>

	  <tr class="TxtTabla">
        <td width="10%"><? echo $reg[unidad]; ?></td>
        <td><? echo ucwords(strtolower($reg[apellidos] . " " . $reg[nombre] )); ?> </td>
        <td width="2%"><?php
        	echo ucwords( strtolower( $reg[categoria] ) ); # . " " . $reg[nombre] )); 
		?></td>
        <td width="50%" align="center">
		<?
		//Trae los proyectos y horas facturadas del usuario del registro
		$sQry="select H.id_proyecto, sum(H.horas_registradas) horasFact, P.nombre, P.cargo_defecto, P.codigo ";
		$sQry=$sQry." from horas H, Proyectos P ";
		$sQry=$sQry." where H.id_proyecto = P.id_proyecto ";
		$sQry=$sQry." and H.unidad =" . $reg[unidad];
		//filtra el resultado de la consulta si la página se carga por primera vez con el mes y año actual
		//sino con lo seleccionado en las listas mes y año
		if ($pMes == "") {
			$sQry=$sQry." and month(H.fecha) = month(getdate())";
			$sQry=$sQry." and year(H.fecha) = year(getdate())";
			$miMesHT = gmdate ("n");
			$MiAnnoHT = gmdate ("Y");
		}
		else {
			$sQry=$sQry." and month(H.fecha) = ". $pMes;
			$sQry=$sQry." and year(H.fecha) = " . $pAno;
			$miMesHT = $pMes;
			$MiAnnoHT = $pAno;
		}
		$sQry=$sQry." group by H.id_proyecto, P.nombre, P.cargo_defecto, P.codigo ";
		$cursorQry = mssql_query($sQry);
		?>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" bgcolor="#FFFFFF" class="TxtTabla">
	  <table width="100%"  border="1" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF">
		   <?
		   $band = 0;
		   
		  while ($regQry=mssql_fetch_array($cursorQry)) {
		  ?>
		
          <tr>
            <td align="left"><? echo "[".$regQry[codigo].".".$regQry[cargo_defecto]."] ".ucwords(strtolower($regQry[nombre])); #  H.cargo_defecto, H.codigo,?></td>
            <td width="20%" align="right"><? echo $regQry[horasFact]; ?></td>            
            <?php 
				if ($band == 0 ){	#	INICIO IF SUMATORIA HR DE TODOS LOS PROYECTOS
			?>
            <td width="20%" rowspan="<?php echo mssql_num_rows($cursorQry); ?>" align="right">
			<? 
				$sumSql = "select sum(H.horas_registradas) horasFact from HojaDeTiempo.dbo.horas H, HojaDeTiempo.dbo.Proyectos P 
						where H.id_proyecto = P.id_proyecto and H.unidad = ".$reg[unidad]." and month(H.fecha) = ".$pMes." and year(H.fecha) = ".$pAno;
				$querySum = mssql_fetch_array( mssql_query( $sumSql ) );
				echo $querySum[horasFact]; 
			?>
            </td>
            <?php
					$band = 1; 
				} 	#	FIN IF SUMATORIA HR DE TODOS LOS PROYECTOS
			?>
          </tr>          
		  <? } ?>          
        </table>	</td>
  </tr>
</table>		</td>
        <td width="5%" align="center">
          <input name="Submit" type="submit" class="Boton" onclick="MM_goToURL('parent','verhdetiempoHT3.php?zUnidad=<? echo $reg[unidad]; ?>&Flmes=<? echo $miMesHT; ?>&Flano=<? echo $MiAnnoHT; ?>');return document.MM_returnValue" value="Ver Hoja" />
</td>
        </tr>
	  <tr class="TxtTabla">
	    <td colspan="5" bgcolor="#999999"><img src="img/images/Pixel.jpg" width="1" height="1" /></td>
	    </tr>
	  <? } ?>
    </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="right" class="TxtTabla">&nbsp;</td>
          </tr>
        </table>		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla"><input name="Submit" type="submit" class="Boton" onclick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value="Página principal Hoja de tiempo" /></td>
            <td align="right" class="TxtTabla">
			<input name="Submit3" type="submit" class="Boton" onclick="MM_goToURL('parent','ReportesHT.php');return document.MM_returnValue" value="Programaci&oacute;n personal" />
			<? if ($muestraDirProyecto > 0) { ?>
			<input name="Submit3" type="submit" class="Boton" onclick="MM_goToURL('parent','ReportesHT2.php');return document.MM_returnValue" value="Reporte Director de proyecto" />
			<? } ?>
			</td>
          </tr>
        </table></td>
      </tr>
    </table>
</body>
</html>
