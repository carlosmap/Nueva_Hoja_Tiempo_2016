<?php
session_start();
$r = 0;
include("../verificaRegistro2.php");
include('../conectaBD.php');

//Establecer la conexión a la base de datos
$conexion = conectar();

/*
10NOV2011
PBM
Traer la información relacionada con la programación de Investigaciones
*/
//Proyecto Actual de Laboratorio
$sql1 = " SELECT A2.*
FROM EnsayosProyectos A1, (
	SELECT DISTINCT A.id_proyecto, A.codigo, A.cargo_defecto, A.nombre, 
	B.unidad AS unidadDir, B.nombre AS nombreDir, B.apellidos AS apellidoDir,
	C.unidad AS unidadCoord, C.nombre AS nombreCoord, C.apellidos AS apellidoCoord
	FROM HojaDeTiempo.dbo.Proyectos A, HojaDeTiempo.dbo.Usuarios B, HojaDeTiempo.dbo.Usuarios C
	WHERE A.id_director *= B.unidad
	AND A.id_coordinador *= C.unidad
) A2
WHERE A1.id_Proyecto = A2.id_Proyecto ";
$sql1 = $sql1 . " AND A1.id_proyecto = " . $_SESSION["sesProyLaboratorio"];
$cursor1 = mssql_query($sql1);
//--Trae los items de la lista
$gSql00lst="SELECT   * ";
$gSql00lst=$gSql00lst." FROM  ".$_SESSION["sesBDgINT"]."POINT ";
$gSql00lst=$gSql00lst." WHERE gINTProjectID = " . $_SESSION["sesProyIDgINT"]." AND FechaFinal is not null";
$gSql00lst=$gSql00lst." ORDER BY  PointID  " ;
$gCursor00lst = mssql_query($gSql00lst);
if ((trim($recargaSondeo) == "") AND (trim($_SESSION["sesPointgINT"]) != "") AND (trim($lstSondeoID) == "") ) {
	$lstSondeoID = $_SESSION["sesPointgINT"];
}
else {
	if ((trim($recargaSondeo) == "SI") AND (trim($lstSondeoID) == "")) {
		$lstSondeoID = "";
		$_SESSION["sesPointgINT"] = "";
	}
}

#	QUERY DE CUSTODIA
#	OSCAR LOPEZ 11-07-2012 11:23

$sql2 = "SELECT *, B.nomItem
	FROM ".$_SESSION["sesBDgINT"]."POINT A, ".$_SESSION["sesBDgINT"]."tbItems B 
	WHERE A.codItemTipoSondeo = B.codItem
	AND A.gINTProjectID = ".$_SESSION['sesProyIDgINT']."
	AND A.FechaFinal is not null ";

if( $nGuia == 'Número de Guía' )
	$nGuia = "";
#
#	BUSCA POR LOS SONDEOS REALIZADOS QUE YA HAN SIDO FINALIZADOS DE ESE PROYECTO
#
if( $lstSondeoID != "" ){
	$sql2 = "SELECT *, B.nomItem 
	FROM ".$_SESSION["sesBDgINT"]."POINT A, ".$_SESSION["sesBDgINT"]."tbItems B
	WHERE A.codItemTipoSondeo = B.codItem
	AND A.gINTProjectID = ".$_SESSION['sesProyIDgINT']."
	AND A.FechaFinal is not null AND A.PointID = '".$lstSondeoID."'";
#	if( $nGuia == 'Número de Guía' )
#		$nGuia = "";
}

#
#	BUSCA POR LA FECHA EN CASO QUE SE HALLA ENVIADO O RECIBIDO 
#
if( $anio != "" ){
	
	if( $mes != "" )
		$fecha = $mes;
	
	else
		$fecha = date ( 'm' );
	
	
	switch( $shrFch ){
		case 1:
			$campo = 'tw.fechaEnvio';
			break;
		case 2:
			$campo = 'tw.fechaEntLab';
			break;
	}

	$sql2 = "SELECT *, B.nomItem 
			 FROM ".$_SESSION["sesBDgINT"]."POINT A, ".$_SESSION["sesBDgINT"]."tbItems B, ".$_SESSION['sesBDgINT']."twEnvios tw 
			 WHERE A.codItemTipoSondeo = B.codItem 
			 AND A.gINTProjectID = ".$_SESSION['sesProyIDgINT']." 
			 AND A.FechaFinal is not null 
			 AND YEAR(".$campo.") = ".$anio." 
			 AND MONTH(".$campo.") = ".$fecha." 
			 AND tw.PointID = A.PointID 
			 AND tw.IDenvio = ( select MAX(idenvio) from ".$_SESSION["sesBDgINT"]."twEnvios where PointID = A.PointID )";
}

if( $nGuia != "" ){
	$sql2 = "SELECT *, B.nomItem 
	FROM ".$_SESSION["sesBDgINT"]."POINT A, ".$_SESSION["sesBDgINT"]."tbItems B, ".$_SESSION['sesBDgINT']."twEnvios tw 
	WHERE A.codItemTipoSondeo = B.codItem 
	AND A.gINTProjectID = ".$_SESSION['sesProyIDgINT']." 
	AND A.FechaFinal is not null 
	AND tw.numGuia = '".$nGuia ."' 
	AND tw.PointID = A.PointID";
	#$nGuia = "";
}

/*
else { #if ( $band == 1 || $band == 2 ) {
	$sql2 = "SELECT *, B.nomItem
		FROM ".$_SESSION["sesBDgINT"]."POINT A, ".$_SESSION["sesBDgINT"]."tbItems B
		WHERE A.codItemTipoSondeo = B.codItem
		AND A.gINTProjectID = ".$_SESSION['sesProyIDgINT']."
		AND A.FechaFinal is not null ";
}
*/
$query2 = mssql_query( $sql2 );


//--Trae las perforaciones que tienen muestras asociadas
/*
$gSql00="SELECT * ";
$gSql00=$gSql00." FROM  ".$_SESSION["sesBDgINT"]."POINT ";
$gSql00=$gSql00." WHERE gINTProjectID = " . $_SESSION["sesProyIDgINT"];
$gSql00=$gSql00." AND PointID IN ( ";
$gSql00=$gSql00." SELECT DISTINCT PointID FROM  ".$_SESSION["sesBDgINT"]."MUESTRA ";
$gSql00=$gSql00." WHERE gINTProjectID = " . $_SESSION["sesProyIDgINT"];
$gSql00=$gSql00." ) ";
if (trim($lstSondeoID) != "") {
	$gSql00=$gSql00." AND PointID = '" . trim($lstSondeoID) . "' " ;
}
$gCursor00 = mssql_query($gSql00);
*/


?>
<html>
<head>

<script language="javascript">
window.name = 'sisLabgINT';
function envia1(){ 
	document.form1.submit();
}

</script>

<title>Investigaciones Geotécnicas</title>
<LINK REL="stylesheet" HREF="../css/estilo.css" TYPE="text/css">
<script language="JavaScript">
window.name="winLaboratorio";
</script>
<script language="JavaScript" type="text/JavaScript">
<!--


function MM_callJS(jsStr) { //v2.0
  return eval(jsStr)
}
//-->
</script>

<script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);

/**
 *	PONER CAMPO EN BLANCO
 */
function blanco( campo ){
	campo.value = '';			
}
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}

function vertabla( id ){
	var alto;
	document.getElementById( 'tabla'+id ).style.display = "block";
	document.getElementById( 'ver'+id ).style.display = "none";
}
function ocultar( id ){
/*
	var alto, i;
	i = document.getElementById('tabla'+id).height;
	alert( "Inicio : " + i );
	for( alto = 400; alto > 0; alto--){
		document.getElementById( 'tabla'+id ).style.height = alto;
	}
*/
	document.getElementById( 'tabla'+id ).style.display = "none";
	document.getElementById( 'ver'+id ).style.display = "block";
}
//-->
</script>
<script language="javascript" type="text/javascript" src="http://code.jquery.com/jquery-1.8.2.min.js"></script>
<script type="text/javascript" language="javascript">
	$(document).ready(function() {
		$("a").click(function (){
			 $("#tabla").removeClass("boton");
        });
/*		
		$("#ver").click(function(){
			$("#desplegable").slideToggle("slow");
		});
		
		$("#ocultar").click(function(){
			$("#desplegable").slideToggle("slow");
		});
*/		
	});
</script>
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<div id="Layer1" style="position:absolute; left:5px; top:55px; width:774px; height:25px; z-index:1; visibility: inherit;">
  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
    <tr>
      <td class="TxtNota1">INVESTIGACIONES GEOT&Eacute;CNICAS </td>
    </tr>
  </table>
</div>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><? include("../modLaboratorio2/bannerArriba.php") ; ?></td>
  </tr>
</table>

<table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
<table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr>
        <td class="TituloUsuario">Proyecto</td>
      </tr>
    </table>
    </td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="1">
  <tr>
    <td colspan="2" class="fondo"><table width="100%" cellspacing="1">
      <tr class="TituloTabla2">
        <td>Nombre del Proyecto </td>
        <td>Codigo - Cargo del Proyecto </td>
        <td>Director</td>
        <td>Coordinador</td>
        </tr>
      <? if($reg1 = mssql_fetch_array($cursor1)){ ?>
      <tr class="TxtTabla">
        <td><? echo strtoupper($reg1["nombre"]); ?></td>
        <td><? echo $reg1["codigo"]. "." .$reg1["cargo_defecto"]; ?></td>
        <td><? echo strtoupper($reg1["nombreDir"]. " " .$reg1["apellidoDir"]); ?></td>
        <td><? echo strtoupper($reg1["nombreCoord"]. " " .$reg1["apellidoCoord"]); ?></td>
        </tr>
	  <? } ?>
      
    </table></td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2"><a href="sisLabProyectos.php" class="menu"><img src="../images/flechaAtras1.gif" width="50" height="44" border="0">Volver a Listado de Proyectos </a></td>
  </tr>
  <tr>
    <td colspan="2"><table width="100%"  cellspacing="1" class="fondo">
      <tr >
        <td width="20%" height="25" class="FichaAct">Sondeos</td>
        <td width="20%" height="25" class="FichaInAct"><a href="sisLabgINT02.php" class="FichaInAct1">S.I.G. </a></td>
        <td height="25" class="TxtTabla">&nbsp;</td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td colspan="2" class="fondo"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td height="5" class="TxtTabla"> </td>
      </tr>
    </table>    <table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr>
        <td width="15%" class="FichaInAct"><a href="sisLabgINT01.php" class="FichaInAct1">Solicitud <br>
  Dise&ntilde;o </a></td>
        <td width="15%" class="FichaInAct"><a href="sisLabgINT01b.php" class="FichaInAct1">Programaci&oacute;n <br>
          Investigaciones </a></td>
        <td width="15%" class="FichaInAct"><a href="sisLabgINT01c.php" class="FichaInAct1">Inspecciones <br>
          de Campo </a></td>
        <td width="15%" class="FichaAct">Custodia <br>
          de Muestras </td>
        <td class="TxtTabla">&nbsp;</td>
      </tr>
      <tr class="TituloUsuario">
        <td height="1"> </td>
        <td height="1"> </td>
        <td></td>
        <td width="15%" class="TituloUsuario"></td>
        <td height="1"> </td>
      </tr>
    </table>    
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td height="5" class="TxtTabla"> </td>
        </tr>
      </table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td class="TxtTabla">&nbsp;</td>
        </tr>
      </table>
	  
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td align="center" class="TxtTabla"><table width="50%"  border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
            <tr>
              <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td class="TituloUsuario">Criterios de b&uacute;squeda </td>
                </tr>
              </table>
                <table width="100%"  border="0" cellspacing="1" cellpadding="0">
				<form name="formSondeo" method="post" action="">
                  <tr>
                    <td width="20%" class="TituloTabla">Sondeo</td>
                    <td colspan="2" class="TxtTabla">
                    <select name="lstSondeoID" class="CajaTexto" id="lstSondeoID" onChange="document.formSondeo.submit();">
                      <option value="" selected>::: Todos :::</option>
                      <? while($gReg00lst = mssql_fetch_array($gCursor00lst)){ 
						if ($lstSondeoID == $gReg00lst["PointID"]) {
							$selSondeo="selected";
							$_SESSION["sesPointgINT"] = $gReg00lst["PointID"];
						}
						else {
							$selSondeo="";
						}
					
					?>
                      <option value="<? echo $gReg00lst["PointID"] ?>" <? echo $selSondeo ; ?> ><? echo $gReg00lst["PointID"] ?></option>
                      <? } ?>
                      </select>
                      <input name="recargaSondeo" type="hidden" id="recargaSondeo" value="SI"></td>
                  </tr>
                  <tr>
                    <td class="TituloTabla"><span class="TituloTabla2">Buscar por Fecha </span></td>
                    <td width="40%" class="TxtTabla"><table width="70%" cellspacing="1" class="TxtTabla">
                      <tr>
      <td width="60%">Mes:
        <?
			#
			#	MODIFICADO OSCAR MAURICIO LOPEZ SEGRUA
			#	30 DE JULIO
			#
				$optMes01 = "";
				$optMes02 = "";
				$optMes03 = "";
				$optMes04 = "";
				$optMes05 = "";
				$optMes06 = "";
				$optMes07 = "";
				$optMes08 = "";
				$optMes09 = "";
				$optMes10 = "";
				$optMes11 = "";
				$optMes12 = "";
				
				$mesAct1 = "optMes" . date("m");
				$mesAct2 = "optMes" . $mes;
				/*
				if(trim($mes) == ""){
					${$mesAct1} = "selected";
				}
				else{
					${$mesAct2} = "selected";
				}
				*/
				$nMes = array( '::: Selecciones Mes :::', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre' );
			?>
        <select name="mes" class="CajaTexto" id="mes" onChange="envia1()">
          <!--<option value="" selected >::: Selecciones Mes :::</option>-->
		  <?
		  	for( $i = 0; $i < count($nMes); $i++){
				#$a = $i + 1;
				if( trim($mes) == $i ){
		  ?>
<!--          <option value="" selected >::: Selecciones Mes :::</option> -->
    		      <option value="<? echo $i; ?>" selected ><?php	echo $nMes[$i];	?></option>
          <?	}	else{	?>
	        	  <option value="<? echo $i; ?>" ><?php	echo $nMes[$i];	?></option>
			<?	
				}
			}
			?>
          </select>
          </td>
      <td width="40%">A&ntilde;o:
        <select name="anio" class="CajaTexto" id="anio" onChange="document.formSondeo.submit();">        
        	<option value="" selected>::: Seleccione a&ntilde;o :::</option>
          <? 
			  #for($i = 2007; $i <= 2050; $i++){
			  #	ACTUALIZADO OSCAR LOPEZ
			  # 30 JULIO 2012 
			  for($i = 2007; $i <= date( 'Y' ); $i++){ 			  	
			  	if( trim($anio) == $i ){
					?>
                    <option value="<? echo $i; ?>" selected ><? echo $i ?></option>
                    <?
				}
			  	else{
			  ?>
		          <option value="<? echo $i; ?>" ><? echo $i ?></option>
          <?	}	}	?>
          
          </select></td>
      </tr>
</table></td>
                    <td width="70%" class="TxtTabla">
		<?
			#$optFE = "";
			#if(trim($buscarFecha) == "1"){
			#	$optFE = "checked";
			#}
		?>
                      <!-- <input name="shrFch" type="checkbox" id="shrFch1" value="1" checked /> -->
                      <input type="radio" name="shrFch" id="shrFch1" value="1" checked />
                      de Env&iacute;o 
<?
			#$optFE = "";
			#if(trim($buscarFecha) == "1"){
			#	$optFE = "checked";
			#}
		?>
<!--<input name="shrFch" type="checkbox" id="shrFch2" value="2" /> -->
<input type="radio" name="shrFch" id="shrFch1" value="2" />
de Recibido</td>
                  </tr>
                  <tr>
                    <td class="TituloTabla"><span class="TituloTabla2">N&uacute;mero de gu&iacute;a: </span></td>
                    <td colspan="2" class="TxtTabla">
                    <input name="nGuia" type="text" class="CajaTexto" id="nGuia" onClick="blanco(this);" value="Número de Guía" />
                    <!-- onKeyPress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" value="<? #echo $numEnsayo; ?>" -->
                    </td>
                  </tr>
                  <tr>
                    <td colspan="3" align="right" class="TxtTabla"><input name="Submit" type="submit" class="Boton" value="Consultar"></td>
                    </tr>
				  </form>
                </table></td>
            </tr>
          </table></td>
        </tr>
      </table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td class="TxtTabla">          
          <?php
		  	#echo $sql2;
			/*
			echo "Data : ".$_SESSION['sesBDgINT']."<br />".
				 "Proyecto : ".$_SESSION['sesProyLaboratorio']."<br />".
				 "Consulta : ".$sql2;			
			*/
			$sqlM = "SELECT distinct m.Tipo FROM ".$_SESSION['sesBDgINT']."MUESTRA m";
			$queryM = mssql_query( $sqlM );
			$numRows = mssql_num_rows( $queryM );
		  ?>
          &nbsp;
          </td>
        </tr>
      </table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td class="TituloUsuario">Custodia de muestras </td>
        </tr>
      </table>
	  <table width="100%"  border="0" cellpadding="0" cellspacing="1">
        <tr class="TituloTabla2">
          <td colspan="5" valign="middle">Inf. del sondeo </td>
          <td colspan="5" valign="middle">Inf. de envio </td>
          <td colspan="3" valign="middle">Inf. de Laboratorio</td>
          <td colspan="2" valign="middle">Inf. de Geolog&iacute;a</td>
          <td valign="middle">Inf. de Ensayos</td>
        </tr>
        <!-- SITIO ULTIMA COLUMNA -->
        <!-- SITIO ULTIMA COLUMNA -->
        <tr class="TituloTabla2">
          <td width="12%" rowspan="2" valign="middle">Sondeo</td>
          <td width="3%" rowspan="2" valign="middle">Profundidad</td>
          <td width="3%" rowspan="2" valign="middle">Tipo</td>
          <td colspan="2" valign="middle">Fecha</td>
          <td width="2%" rowspan="2" valign="middle">Tipo de embalaje </td>
          <td width="5%" rowspan="2" valign="middle">Fecha env&iacute;o</td>
          <td width="10%" rowspan="2" valign="middle">Quien Env&iacute;o</td>
          <td width="7%" rowspan="2" valign="middle">Servicio de Env&iacute;o</td>
          <td width="3%" rowspan="2" valign="middle">N&uacute;mero de Gu&iacute;a</td>
          <td width="1%" rowspan="2" valign="middle">V.B.</td>
          <td width="5%" rowspan="2" valign="middle">Quien Recibe</td>
          <td width="5%" rowspan="2" valign="middle">Fecha Recibe</td>
          <td width="10%" rowspan="2" valign="middle">Quien Revisa</td>
          <td width="5%" rowspan="2" valign="middle">Fecha Revisi&oacute;n</td>
          <td rowspan="2" valign="middle"  >Ensayos solicitados por los clientes</td>
        </tr>
        <tr class="TituloTabla2">
          <td width="5%" height="5%" valign="middle"> Inicio</td>
          <td width="5%" height="5%" valign="middle"> Finalizaci&oacute;n </td>
        </tr>
        <?php
			#	INICIA CICLO DE CUSTODIA 
			while( $custodia = mssql_fetch_array( $query2 ) ){
		?>	
        <tr class="TxtTabla">
          <td width="12%" valign="top"><?php
          		echo $custodia[PointID];
			?></td>
          <td width="3%" align="right" valign="top"><?php
          		echo $custodia[HoleDepth]; #echo $custodia[HoleDepth]; pint
			?></td>
          <td width="3%" valign="top"><?php
          		echo $custodia[nomItem]; #Tipo custodia
		  ?></td>
          <td width="5%" valign="top">
		  <? 
		if(trim($custodia[FechaComienzo]) != ""){
			echo date("d-M-Y ", strtotime($custodia[FechaComienzo])); 
		}
		 ?>		  </td>
          <td width="5%" valign="top"><? 
		if(trim($custodia[FechaFinal]) != ""){
			echo date("d-M-Y ", strtotime($custodia[FechaFinal])); 
		}
		 ?></td>
          <td width="2%" valign="top"><?php
		  	 $sqlMuestras = "select distinct(m.Tipo), codItemTipoMuestra from ".$_SESSION['sesBDgINT']."POINT p
							inner join ".$_SESSION['sesBDgINT']."MUESTRA m on m.PointID = p.PointID
							where p.PointID = '".$custodia[PointID]."' order by Tipo desc";
			#echo $sqlMuestras;
			$queryMuestra = mssql_query( $sqlMuestras );
			$noM = $nom = 0;
			#
			#	MODIFICADO OSCAR LOPEZ 31-07-2012
			#
			$point = $custodia[codItemTipoSondeo];
			#$point = substr($custodia[PointID],0,2);
			$pointId = $custodia[PointID];
			#
			#	CUENTA EL NUMERO DE CAJAS O DE LONAS QUE HALLA DEL SONDEO
			#
			$sqlCL = "Select count( C.numero ) num from ".$_SESSION['sesBDgINT']."POINT p
				inner join ".$_SESSION['sesBDgINT']."CAJA C on C.PointID = p.PointID
				where p.PointID = '".$custodia[PointID]."'";
			$queryCL = mssql_fetch_array( mssql_query( $sqlCL ) );
			
			#echo $sqlCL;
			if( ( $point == 3 || $point == 2 || $point == 4 ) && $queryCL[num] != 0 ) 
				echo "Caja : ".$queryCL[num];
			else if ( ( $point == 1 || $point == 5 ) && $queryCL[num] != 0 ) 
				echo "Lona : ".$queryCL[num];
			
			#	SALTO DE LINEA
			echo "<br />";
				
			while( $rowMuestra = mssql_fetch_array(  $queryMuestra ) ) {
				$point = substr($custodia[PointID],0,2);
				if( $rowMuestra[Tipo] != ''  && $rowMuestra[codItemTipoMuestra] == 130 ){# && ( $point == 'PT' || $point == 'BR' || $point == 'PZ' ) ) {					
					#
					#	CUENTA LA CANTIDAD DE MUESTRAS QUE ALLA POR CADA TIPO DE MUESTRAS
					#
					$sqlNoMuestras = "select count( m.Tipo) Tipo from ".$_SESSION['sesBDgINT']."POINT p
						inner join ".$_SESSION['sesBDgINT']."MUESTRA m on m.PointID = p.PointID
						where p.PointID = '".$custodia[PointID]."' AND m.Tipo = '".$rowMuestra[Tipo]."'";
					$noMuestra = mssql_fetch_array( mssql_query( $sqlNoMuestras ) );
					echo $rowMuestra[Tipo]." : ".$noMuestra[Tipo];
				}
			}
			
		  ?></td>
          <td width="5%" valign="top">
		  <?php
		  	#
			#	Muestra el envío de la perforación
			#
			$sql4 = "Select item.nomItem nomServ, fechaEnvio, codItemEmpTransporte, quienEnvio, numGuia, gINTProjectID, PointID from ".$_SESSION['sesBDgINT']."twEnvios tw
					 left join ".$_SESSION['sesBDgINT']."tbItems item on item.codItem = tw.codItemEmpTransporte 
					 where tw.PointID = '".$pointId."' AND tw.IDenvio = ( 
					 select MAX(IDenvio) fch from  ".$_SESSION['sesBDgINT']."twEnvios where PointID = '".$pointId."')";			
			$query4 = mssql_query( $sql4 );
			$row4 = mssql_fetch_array( $query4 );
			
			if(  trim( $row4[fechaEnvio] ) != "" )
		 		echo date("d-M-Y ", strtotime( $row4[fechaEnvio] ) );	
		  ?>
          </td>
          <td width="10%" valign="top" nowrap><?php	echo $row4[quienEnvio];	?></td>
          <td width="7%" valign="top"><?php	echo $row4[nomServ];	?></td>
          <td width="3%" valign="top"><?php	echo $row4[numGuia];	?></td>
          <td width="1%" valign="top"><?php	
 			#	VERIFICAR QUE LOS CAMPOS NO ESTEN VACIOS
			if( trim($row4[gINTProjectID]) != "" ){
				#	VERIFICAR MUESTRAS RECIVIDAS EN LABORATORIO
				$sqlM = "Select A.*, B.* From ( select COUNT(*) numR from ".$_SESSION['sesBDgINT']."MUESTRA 
												WHERE gINTProjectID = ".$row4[gINTProjectID]." AND PointID = '".$row4[PointID]."' and seRecibioLab = 1 and codItemTipoMuestra = 130 ) A,
												( select COUNT(*) num from ".$_SESSION['sesBDgINT']."MUESTRA 
												  WHERE gINTProjectID =".$row4[gINTProjectID]." AND PointID = '".$row4[PointID]."' and codItemTipoMuestra = 130 ) B";
				$rwM = mssql_fetch_array( mssql_query( $sqlM ) );
				#	VERIFICAR CAJAS RECIVIDAS EN LABORATORIO
				$sqlC = "Select A.*, B.* From ( select COUNT(*) numR from ".$_SESSION['sesBDgINT']."CAJA 
												WHERE gINTProjectID = ".$row4[gINTProjectID]." AND PointID = '".$row4[PointID]."' and seRecibioLab = 1 ) A,
												( select COUNT(*) num from ".$_SESSION['sesBDgINT']."CAJA 
												  WHERE gINTProjectID =".$row4[gINTProjectID]." AND PointID = '".$row4[PointID]."' ) B";				
				$rwC = mssql_fetch_array( mssql_query( $sqlC ) );
				if( ( $rwM[numR] < $rwM[num] ) || ( $rwC[numR] < $rwC[num] ) ){
		  ?>
            <img src="../images/ver.gif" width="16" height="16" style="cursor: hand;" 
              		onClick="MM_openBrWindow('sisLabRec.php?idPoint=<?php	echo $custodia[PointID];	?>','mrtEnvio','width=450,height=520')">
            <?php	
				}
				else{
		  ?>
            <img src="../images/Si.gif" width="16" height="14">
          <?php	
				}
			}
		  ?></td>
          <td width="5%" align="left" valign="top">
          <!--&nbsp;-->
          <?php
		  	$sqlEntLab = "Select nombre, apellidos, fechaEntLab from ".$_SESSION['sesBDgINT']."twEnvios, HojaDeTiempo.dbo.Usuarios  
						  WHERE gINTProjectID =".$row4[gINTProjectID]." AND PointID = '".$row4[PointID]."' and usuEntLab = unidad";
			#echo $sqlEntLab."<br />";
			$qryEntLab = mssql_query( $sqlEntLab );
			$rwEntLab = mssql_fetch_array( $qryEntLab );
			echo ucfirst($rwEntLab[nombre])." ".ucfirst($rwEntLab[apellidos]);
		  ?>
          </td>
          <td width="5%" valign="top">
		  <?	
		  	if( trim( $rwEntLab[fechaEntLab] ) != "" )
				echo date("d-M-Y ", strtotime($rwEntLab[fechaEntLab]));	
		  ?>
          </td>
          <td width="10%" valign="top">
          <?php	
		  	#
			#	CONSULTAR QUIEN RESIBIO EN LITOLOGIA
			#
			$sql5 = "SELECT lt.fechaGraba fecLtM2, lt.PointID, usu.nombre nom, usu.apellidos ape ".
					"FROM ".$_SESSION['sesBDgINT']."LITOLOGIA lt, HojaDeTiempo.dbo.Usuarios usu ".
					 "WHERE usu.unidad = lt.usuarioMod2 AND lt.PointID = '".$custodia[PointID]."' AND ".
					 "lt.usuarioMod2 is not null AND lt.fechaMod2 is not null AND ultimaDesc = 1";
			#echo $sql5."<br />";
			$qry5 = mssql_query( $sql5 );			
			$row5 = mssql_fetch_array( $qry5 );
		  	#echo ucwords($row5[nom])." ".ucwords($row5[ape]);	ucfirst(
			echo ucfirst($row5[nom])." ".ucfirst($row5[ape]);
		  ?></td>
          <td width="5%" valign="top">
		  <?php	
		  	if( trim( $row5[fecLtM2] ) != "" )
				echo date("d-M-Y ", strtotime($row5[fecLtM2]) );	
		  ?>
          </td>
          <td valign="top">
            <?php
		  	$sql6 = " SELECT IDsolEnsayoOrden, voBoSolicitud, usuVoBoSolicitud, fechaVoBoSolicitud, usuOrden, fechaEntrega1, fechaEntrega2, fechaEntregaFin 
			FROM ".$_SESSION['sesBDgINT']."twSolEnsayosOrden 
					  WHERE gINTProjectID = ".$_SESSION['sesProyIDgINT']." AND PointID = '".$custodia[PointID]."'";
			echo $sql6."<br />";
			$qry6 = mssql_query( $sql6 );
			#echo $sql6."<br />";
			if( mssql_num_rows( $qry6 ) > 0 ){
				$r++;
		  ?>
          <a href="#tabla<?	echo $r; ?>" name="ver<? echo $r; ?>" onClick="vertabla(<? echo $r; ?>)" id="ver<? echo $r; ?>" style="display: none; text-decoration: none;">
          Ver</a>
          
          <div id="tabla<? echo $r; ?>" >
          	<a href="#ver<?	echo $r; ?>" name="tabla<? echo $r; ?>" onClick="ocultar(<?	echo $r; ?>)" style="text-decoration: none;">Ocultar</a>
            <table>
              <tr><td bgcolor="#FFFFFF">
                <table width="100%" border="0" cellpadding="0" cellspacing="1" >
                  <tr class="TituloTabla">
                    <td colspan="4" align="center">Solicitud de ensayo</td>
                    <td colspan="4" align="center">Entrega de resultados</td>
                    </tr>
                  <tr class="TituloTabla">
                    <td width="1%" rowspan="2">Num.</td>
                    <td width="1%" rowspan="2">V.B.</td>
                    <td width="10%" rowspan="2" align="center">Quien Solicita</td>
                    <td width="7%" rowspan="2" align="center">Fecha</td>
                    <td colspan="2" align="center">Caracterizaci&oacute;n</td>
                    <td colspan="2" align="center">Parametros de resistencia</td>
                    </tr>
                  <tr class="TituloTabla">
                    <td width="7%" align="center">Programada</td>
                    <td width="7%" align="center">Entregada</td>
                    <td width="7%" align="center">Programada</td>
                    <td width="7%" align="center">Entregada</td>
                    </tr>
                  <?php
				while( $row6 = mssql_fetch_array( $qry6 ) ){
			?>
                  <tr class="TxtTabla">
                    <td width="1%"><?php	echo $row6[IDsolEnsayoOrden];	?></td>
                    <td width="1%">
                      <?php	
			  	if( $row6[voBoSolicitud] == 1 ) {	
			  ?>
                      <img src="../images/Si.gif" alt="" width="16" height="14">
                      <?	
			  	}	
				else{
			  ?>
                      <img src="../images/No.gif" width="12" height="16">
                      
                      <?	}	?>
                      </td>
                    
                    <td width="10%">
                      <?php	
					$sqlUsuSol = "Select nombre, apellidos From HojaDeTiempo.dbo.Usuarios where unidad = ".$row6[usuOrden]; #$row6[usuVoBoSolicitud];
					$nom = mssql_fetch_array( mssql_query( $sqlUsuSol ) );
					echo $nom[nombre]." ".$nom[apellidos];	
				?>
                      </td>
                    <td width="7%"><?php	
					if( $row6[fechaVoBoSolicitud] != "" )
						echo date("d-M-Y ", strtotime( $row6[fechaVoBoSolicitud] ) );					
				?></td>
                    <td width="7%"><?php	
					if( $row6[fechaVoBoSolicitud] != "" )
						echo date("d-M-Y ", strtotime( $row6[fechaEntrega1] ) );					
				?></td>
                    <td width="7%"><?php	
					if( $row6[fechaVoBoSolicitud] != "" )
						echo date("d-M-Y ", strtotime( $row6[fechaEntregaFin] ) );					
				?></td>
                    <td width="7%"><?php	
					if( $row6[fechaVoBoSolicitud] != "" )
						echo date("d-M-Y ", strtotime( $row6[fechaEntrega2] ) );					
				?></td>
                    <td width="7%">
						<?php	
	                        #if( $row6[fechaVoBoSolicitud] != "" )
    	                    #	echo date("d-M-Y ", strtotime( $row6[fechaVoBoSolicitud] ) );					
                        ?>
	                </td>
                    </tr>
                  <?php	}	?>
                  </table>
            </td></tr></table>
            </div>
            <?php	
				}	#	CIERRA SI HAY REGISTROS.
			?><br />
            <div id="oculta" style="width: 440px; position: absolute;" ></div>
          </td>
        </tr>
        <?php
			#	CIERRA CICLO DE CUSTODIA
			}
		?>
    </table>
   
    </td>
  </tr>
  <tr>
    <td colspan="2">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;	</td>
    <td align="right" valign="bottom">
	<input name="Submit2" type="submit" class="Boton" onClick="javascript:window.close()" value="Cerrar Ventana"></td>
  </tr>
</table>
    <table width="100%" border="0" cellspacing="0" cellpadding="1">
      <tr>
        <td>&nbsp;</td>
      </tr>
</table>

    <p>&nbsp;</p>
</body>
</html>

<? mssql_close ($conexion); ?>	
