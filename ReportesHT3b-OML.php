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
$sql=$sql." and D.id_director = " . $laUnidad; 
$cursor = mssql_query($sql);
if ($reg=mssql_fetch_array($cursor)) {
	$elIDDivision = $reg[id_division];
	$elNomDivision = $reg[nombre];
	$elNomDirector = $reg[nomDir] . " " . $reg[apeDir];
}


//16Jul2007
//ParaMostrar los botones del Reporte del director de proyecto y de división
$muestraDirProyecto = 0;
$sqlB="select count(*) esDirector from proyectos  ";
$sqlB=$sqlB." where (id_director = ". $laUnidad . " or id_coordinador = " . $laUnidad . " ) "; 
$cursorB = mssql_query($sqlB);
if ($regB=mssql_fetch_array($cursorB)) {
	$muestraDirProyecto = $regB[esDirector];
}

//09Abr2012
//Facturación del personal de la división seleccionada con toda la información
$sql00="Select H.unidad, U.nombre, U.apellidos, D.nombre nomDpto, D.id_departamento, Z.nombre nomDiv, Z.id_division, ";
$sql00=$sql00." H.id_proyecto, P.nombre nomProyecto, H.clase_tiempo, sum(H.horas_registradas) hFacturadas, A.salarioBase, P.codigo Pcod, P.cargo_defecto Pcar ";
$sql00=$sql00." from Horas H, Proyectos P, Asignaciones A, Usuarios U, Departamentos D, Divisiones Z ";
$sql00=$sql00." where H.id_proyecto = P.id_proyecto ";
$sql00=$sql00." and H.id_proyecto = A.id_proyecto ";
$sql00=$sql00." and H.id_actividad = A.id_actividad ";
$sql00=$sql00." and H.unidad = A.unidad ";
$sql00=$sql00." and H.clase_tiempo = A.clase_tiempo ";
$sql00=$sql00." and H.localizacion = A.localizacion ";
$sql00=$sql00." and H.cargo = (P.codigo + A.cargo) ";#U.id_categoria = CAT.id_categoria AND
#$sql00=$sql00." and U.id_categoria = CAT.id_categoria ";#AND
$sql00=$sql00." and MONTH(H.fecha)= MONTH(A.fecha_inicial) ";
$sql00=$sql00." and YEAR(H.fecha)= YEAR(A.fecha_inicial) ";
$sql00=$sql00." and H.unidad = U.unidad ";
$sql00=$sql00." and U.id_departamento = D.id_departamento ";
$sql00=$sql00." and D.id_division = Z.id_division";
$sql00=$sql00." and D.id_division = " . $elIDDivision ;
if ($pMes == "") {
	$sql00=$sql00." and MONTH(H.fecha) = month(getdate()) " ;
	$sql00=$sql00." and YEAR(H.fecha)= year(getdate())";
}
else {
	if ($pMes == "TODOS") {
		$sql00=$sql00." and YEAR(H.fecha)= " . $pAno;
	}
	else {
		$sql00=$sql00." and MONTH(H.fecha) =  " . $pMes;
		$sql00=$sql00." and YEAR(H.fecha)= ". $pAno;
	}
}
if (trim($pUnidad) != "") {
	$sql00=$sql00." and H.unidad = " . $pUnidad;
}
if (trim($plstProyecto) != "") {
	$sql00=$sql00." and H.id_proyecto = " . $plstProyecto;
}
$sql00=$sql00." group by H.unidad, U.nombre, U.apellidos, D.nombre, D.id_departamento, Z.nombre, Z.id_division, H.id_proyecto, P.nombre, H.clase_tiempo, A.salarioBase, 
				P.codigo, P.cargo_defecto ";
$sql00=$sql00." UNION ";
$sql00=$sql00." Select H.unidad, U.nombre, U.apellidos, D.nombre nomDpto, D.id_departamento,Z.nombre nomDiv, Z.id_division, ";
$sql00=$sql00." H.id_proyecto, P.nombre nomProyecto, H.clase_tiempo, sum(H.horas_registradas) hFacturadas, A.salarioBase, P.codigo, P.cargo_defecto ";
$sql00=$sql00." from Horas H, Proyectos P, Asignaciones A, Usuarios U, Departamentos D, Divisiones Z ";
$sql00=$sql00." where H.id_proyecto = P.id_proyecto  ";
$sql00=$sql00." and H.id_proyecto = A.id_proyecto ";
$sql00=$sql00." and H.id_actividad = A.id_actividad ";
$sql00=$sql00." and H.unidad = A.unidad ";
$sql00=$sql00." and H.clase_tiempo = A.clase_tiempo ";
$sql00=$sql00." and H.localizacion = A.localizacion ";
$sql00=$sql00." and H.cargo = (P.codigo + A.cargo) ";
$sql00=$sql00." AND H.id_proyecto in (42, 48, 71, 61,65,60, 63, 62, 64, 56) ";
$sql00=$sql00." and H.unidad = U.unidad ";
$sql00=$sql00." and U.id_departamento = D.id_departamento ";
$sql00=$sql00." and D.id_division = Z.id_division ";
$sql00=$sql00." and D.id_division = " . $elIDDivision ;
if ($pMes == "") {
	$sql00=$sql00." and MONTH(H.fecha) = month(getdate()) " ;
	$sql00=$sql00." and YEAR(H.fecha)= year(getdate())";
}
else {
	if ($pMes == "TODOS") {
		$sql00=$sql00." and YEAR(H.fecha)= " . $pAno;
	}
	else {
		$sql00=$sql00." and MONTH(H.fecha) =  " . $pMes;
		$sql00=$sql00." and YEAR(H.fecha)= ". $pAno;
	}
}
if (trim($pUnidad) != "") {
	$sql00=$sql00." and H.unidad = " . $pUnidad;
}
if (trim($plstProyecto) != "") {
	$sql00=$sql00." and H.id_proyecto = " . $plstProyecto;
}
$sql00=$sql00." group by H.unidad, U.nombre, U.apellidos, D.nombre, D.id_departamento, Z.nombre, Z.id_division, H.id_proyecto, P.nombre, H.clase_tiempo, A.salarioBase, 
				P.codigo, P.cargo_defecto ";
#echo $sql00."<br />";
$cursor00 = mssql_query($sql00);
#echo $sql00;
//echo $sql00 . "<br>";

//--PROYECTOS en los que ha participado la división AMBIENTAL
$lstSqlProy="Select H.id_proyecto, P.nombre nomProyecto ";
$lstSqlProy=$lstSqlProy . " from Horas H, Proyectos P, Usuarios U, Departamentos D, Divisiones Z ";
$lstSqlProy=$lstSqlProy . " where H.id_proyecto = P.id_proyecto ";
$lstSqlProy=$lstSqlProy . " and H.unidad = U.unidad ";
$lstSqlProy=$lstSqlProy . " and U.id_departamento = D.id_departamento ";
$lstSqlProy=$lstSqlProy . " and D.id_division = Z.id_division ";
$lstSqlProy=$lstSqlProy . " and D.id_division = " . $elIDDivision ;
if ($pMes == "") {
	$lstSqlProy=$lstSqlProy . " and MONTH(H.fecha)= month(getdate()) " ;
	$lstSqlProy=$lstSqlProy . " and YEAR(H.fecha)= year(getdate()) " ;
}
else {
	if ($pMes == "TODOS") {
		$lstSqlProy=$lstSqlProy . " and YEAR(H.fecha)= ". $pAno;
	}
	else {
		$lstSqlProy=$lstSqlProy . " and MONTH(H.fecha)=  " . $pMes;
		$lstSqlProy=$lstSqlProy . " and YEAR(H.fecha)= ". $pAno;
	}
}
$lstSqlProy=$lstSqlProy . " group by H.id_proyecto, P.nombre ";
$lstSqlProy=$lstSqlProy . " ORDER BY P.nombre ";
$lstCursorProy = mssql_query($lstSqlProy);

//Generar el archivo xls
$excel="";
$excel.="Unidad\t";
$excel.="Nombre\t";
$excel.="Categoria\t";
$excel.="Departamento\t";
$excel.="Proyecto\t";
$excel.="Clase de tiempo\t";
$excel.="Horas facturadas\t";
$excel.="Salario base\t";
$excel.="Valor facturado\n";


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
	$selMesTodos= "";
	if ($pMes == "") {
		$mesActual=date("m"); //el mes actual
	}
	else {
		if ($pMes == "TODOS") {
			$selMesTodos= "selected"; //el mes seleccionado
		}
		else {
			$mesActual= $pMes; //el mes seleccionado
		}
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
	  <option value="TODOS" <? echo $selMesTodos; ?>>::: Todos :::</option>
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
    <td width="10%" class="TxtTabla"><input name="Submit8" type="submit" class="Boton" value="Consultar"></td>
  </tr>
  <tr>
    <td align="right" class="TituloTabla">Proyecto:</td>
    <td colspan="3" class="TxtTabla">&nbsp;
      <select name="plstProyecto" class="CajaTexto" id="plstProyecto">
	  		<option value="" selected > </option>
	  <? 
	  while ($lstRegProy=mssql_fetch_array($lstCursorProy)) { 
	  if ($lstRegProy[id_proyecto] == $plstProyecto) {
	  		$selProyecto = "selected";
	  }
	  else {
			$selProyecto = "";
	  }
	  
	  
	  ?>
        <option value="<? echo $lstRegProy[id_proyecto]; ?>" <? echo $selProyecto; ?> ><? echo $lstRegProy[nomProyecto]; ?></option>
	  <? }?>
        
      </select></td>
    <td class="TxtTabla">&nbsp;</td>
  </tr>
  <tr>
    <td align="right" class="TituloTabla">Unidad</td>
    <td colspan="3" class="TxtTabla">&nbsp;      <input name="pUnidad" type="text" class="CajaTexto" id="pUnidad" onKeyPress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" value="<? echo $pUnidad; ?>" /></td>
    <td class="TxtTabla">&nbsp;</td>
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
        <td width="15%" class="FichaInAct"><a href="ReportesHT3.php" class="FichaInAct1">Horas facturada por usuario y Proyecto </a></td>
        <td width="15%" class="FichaAct">Horas y valor facturado <br />
        por usuario y proyecto </td>
        <td width="15%" class="FichaInAct"><a href="ReportesHT3c.php" class="FichaInAct1">Proyectos <br />
  con facturaci&oacute;n </a></td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td height="1" colspan="4" class="TituloUsuario"> </td>
      </tr>
</table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Facturaci&oacute;n de la divisi&oacute;n - Horas y valor facturado por usuario y por proyecto </td>
  </tr>
</table>


    
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="right" class="TituloTabla">Valor Facturado = (Salario / 185) * Horas Facturadas <br /></td>
      </tr>
    </table>
    <table width="100%"  border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
      <tr class="TituloTabla2">
        <td width="8%">Unidad</td>
        <td>Nombre</td>
        <td>Categoria</td>
        <td width="10%">Departamento</td>
        <td width="20%">Proyecto</td>
        <td width="5%">Clase de tiempo </td>
        <td width="10%">Horas facturadas </td>
        <td width="10%">Salario base</td>
        <td width="10%">Valor Facturado</td>
      </tr>
	  <? 
	  $vlrTotalFacturado = 0;
	  while ($reg00=mssql_fetch_array($cursor00)) { 
	  $elNomUsuario = "";
	  $elhFacturadas = "";
	  $elsalarioBase = "";
	  $elvlrFacturado = "";
	  ?>
      <tr class="TxtTabla">
        <td width="8%">
		<? 
			echo $reg00[unidad]; 
		 	$excel.="$reg00[unidad]\t"; 
		?> 
		</td>
        <td>
		<? 
		echo ucwords(strtolower($reg00[apellidos] . " " . $reg00[nombre] ));  
		$elNomUsuario= ucwords(strtolower($reg00[apellidos] . " " . $reg00[nombre] )); 
		$excel.="$elNomUsuario\t"; 
		?>
		</td>
        <td>
		<? 
		#
		#	VERIFICA LA CATEGORIA DEL USUARIO
		#
		
		$sqlCategoria = "select cat.nombre from HojaDeTiempo.dbo.Usuarios u
						 inner join HojaDeTiempo.dbo.Categorias cat on cat.id_categoria = u.id_categoria
						 where u.unidad = ".$reg00[unidad];
		$queryCategoria = mssql_query( $sqlCategoria );
		$rowCategoria = mssql_fetch_array( $queryCategoria );	
		echo ucwords( strtolower( $rowCategoria[nombre] ) );  
		#$categoria = ucwords( strtolower( $rowCategoria[nombre] ) ); 
#		if( strlen($categoria) == 1 )
		if( $rowCategoria[nombre] < 10  && strlen( trim( $rowCategoria[nombre]  ) ) == 2 ){
#			if( strlen( trim( $rowCategoria[nombre]  ) ) == 2 )
				$excel .= ".".$rowCategoria[nombre]."\t";# "0".$categoria;				
#			else
#				$excel .= trim($rowCategoria[nombre])."\t";#-(".strlen( $rowCategoria[nombre] ).")\t";# "0".$categoria;
		}else
			$excel .= trim($rowCategoria[nombre])."\t";
		
		?></td>
        <td width="10%"><? echo $reg00[nomDpto]; ?>
		<? $excel.="$reg00[nomDpto]\t"; ?>
		</td>
        <td width="20%"><? echo "[".$reg00[Pcod].".".$reg00[Pcar]."] ".$reg00[nomProyecto]; ?>
		<? $excel.="$reg00[nomProyecto]\t"; ?>
		</td>
        <td width="5%" align="right"><? echo $reg00[clase_tiempo]; ?>
		<? $excel.="$reg00[clase_tiempo]\t"; ?>
		</td>
        <td width="10%" align="right"><? echo number_format($reg00[hFacturadas], 0, ",", "."); ?>
		<? 
		$elhFacturadas = number_format($reg00[hFacturadas], 0, ",", ".") ;
		$excel.="$elhFacturadas\t"; ?>
		</td>
        <td width="10%" align="right">$ <? echo number_format($reg00[salarioBase], 2, ",", ".") ; ?>
		<? 
		$elsalarioBase = number_format($reg00[salarioBase], 2, ",", ".");
		$excel.="$reg00[salarioBase]\t"; ?>
		</td>
        <td width="10%" align="right">
		$ <? 
		$vlrFacturado = 0;
		if (trim($reg00[salarioBase]) != "") {
			$vlrFacturado = (($reg00[salarioBase] / 185) *  $reg00[hFacturadas]) ;
			$vlrTotalFacturado = $vlrTotalFacturado + $vlrFacturado; 
			echo number_format($vlrFacturado, 2, ",", ".");#echo number_format($vlrFacturado, 2, ",", ".");
		} 
		else {
			echo "0";
		}
		?>
		<? 
		$elvlrFacturado = number_format($vlrFacturado, 2, ",", ".");
		$excel.="$elvlrFacturado\n";#$excel.="$vlrFacturado\n"; ?>		
		</td>
      </tr>
	  <? } ?>
      <tr class="TituloTabla2">
        <td>&nbsp;</td>
        <td colspan="2">&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td align="right">&nbsp;</td>
        <td align="right">&nbsp;</td>
        <td align="right">TOTAL FACTURADO </td>
        <td align="right">$ <? echo number_format($vlrTotalFacturado, 2, ",", "."); ?></td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
	<form action="rptXlsFactura01.php" method="post">
      <tr>
        <td>
		<input type="hidden" name="export" value="<? echo $excel; ?>">
		<input type="submit" class="Boton" value="Generar XLS">		
		</td>
      </tr>
	  </form>
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
