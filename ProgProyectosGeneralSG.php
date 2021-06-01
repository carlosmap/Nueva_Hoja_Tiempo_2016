<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

//22Enero2008
//Trae el nombre de los proyectos en los que el usuario activo aparece como director y/o coordinador
$sql="SELECT P.*, D.nombre nombreD, D.apellidos apellidosD, C.nombre nombreC, C.apellidos apellidosC  ";
$sql=$sql." FROM proyectos P, Usuarios D, Usuarios C " ;
$sql=$sql." WHERE P.id_director = D.unidad " ;
$sql=$sql." AND P.id_coordinador = C.unidad " ;
$sql=$sql." AND P.id_proyecto = " . $cualProyecto ;
$cursor = mssql_query($sql);

//18Feb2008
//Trae la información de la programación de ls auma global para al proyecto seleccionado y el usuario activo
//id_proyecto, unidadProgramador, fechaInicio, plazo, valorSumaGlobal
$sql2="SELECT * FROM ProgSumaGlobal ";
$sql2=$sql2." where id_proyecto =" . $cualProyecto ;
$sql2=$sql2." and unidadProgramador =" . $cualUnidad ;
$cursor2 = mssql_query($sql2);
if ($reg2=mssql_fetch_array($cursor2)) {	 
	$pfechaInicio = date("M d Y ", strtotime($reg2[fechaInicio])) ;
	$pplazo = $reg2[plazo];
	$pvalorSumaGlobal = $reg2[valorSumaGlobal];
	$pMesInicial = date("n", strtotime($reg2[fechaInicio])) ;
	$pAnoInicial = date("Y", strtotime($reg2[fechaInicio])) ;
	$pliberar = $reg2[liberar];
}

//20Feb2008
//Verifica si existen usuarios con programación para el proyecto seleccionado y el usuario activo
//para activar o no el botón eliminar
$sql3="select count(*) hayUsuarios from ProgSumaGlobalUsu ";
$sql3=$sql3." where id_proyecto =" . $cualProyecto ;
$sql3=$sql3." and unidadProgramador =" . $cualUnidad ;
$cursor3 = mssql_query($sql3);
if ($reg3=mssql_fetch_array($cursor3)) {	 
	$pExisteUsuarios = $reg3[hayUsuarios];
}

//Trae los usuarios que han sido programados para el proyecto seleccionado y el usuario activo, con sus correspondientes salarios 
//(el último salario registrado en la tabla UsuariosSalario)
$sql4="select distinct P.unidad, U.nombre, U.apellidos, S.salario ";
$sql4=$sql4." from ProgSumaGlobalUsu P, usuarios U, usuariosSalario S ";
$sql4=$sql4." where P.unidad = U.unidad ";
$sql4=$sql4." and P.id_proyecto = " . $cualProyecto ;
$sql4=$sql4." and P.unidadProgramador ="  . $cualUnidad ;
$sql4=$sql4." and P.unidad = S.unidad ";
$sql4=$sql4." and S.fecha = (select max(fecha) maxFecha from usuariosSalario where unidad = P.unidad) ";
$sql4=$sql4." order by  U.apellidos ";
$cursor4 = mssql_query($sql4);


//Trae los costos directos que han sido  programados para el proyecto seleccionado y el usuario activo
$sql6="select * from ProgSumaGlobalCostosD ";
$sql6=$sql6." where id_proyecto =" . $cualProyecto ;
$sql6=$sql6." and unidadProgramador =" . $cualUnidad ;
$cursor6 = mssql_query($sql6);

//Trae el total programado para los usuarios relacionados al proyecto seleccionado y el usuario activo
$sql7="select coalesce(sum(valorProgramado) ,0) totProg  from ProgSumaGlobalUsu ";
$sql7=$sql7." where id_proyecto =" . $cualProyecto ;
$sql7=$sql7." and unidadProgramador =" . $cualUnidad ;
$cursor7 = mssql_query($sql7);
if ($reg7=mssql_fetch_array($cursor7)) {	 
	$pTotalProgUsu = $reg7[totProg];
}

//Trae el total programado para los costos directos relacionados al proyecto seleccionado y el usuario activo
$sql8="select coalesce(sum(valorItem), 0) totCD from ProgSumaGlobalCostosD";
$sql8=$sql8." where id_proyecto =" . $cualProyecto ;
$sql8=$sql8." and unidadProgramador =" . $cualUnidad ;
$cursor8 = mssql_query($sql8);
if ($reg8=mssql_fetch_array($cursor8)) {	 
	$pTotalProgCD = $reg8[totCD];
}


//Trae la información de la bitácora de la suma global
//secuencia, fecha, id_proyecto, unidadProgramador, comentaProgramador, unidadProyecto, comentaProyecto
$sqlB="SELECT B.*, P.nombre, P.apellidos, C.nombre nomCoordina, C.apellidos apeCoordina  ";
$sqlB=$sqlB." FROM BitacoraSumaGlobal B, Usuarios P, Usuarios C ";
$sqlB=$sqlB." WHERE B.unidadProgramador *= P.Unidad ";
$sqlB=$sqlB." AND B.unidadProyecto *= C.unidad ";
$sqlB=$sqlB." AND B.id_proyecto =" . $cualProyecto ;
$sqlB=$sqlB." AND B.unidadProgramador =" . $cualUnidad ;
$sqlB=$sqlB." order by B.fecha ";
$cursorB = mssql_query($sqlB);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
<!--
window.name="winProgProyectos";

function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}

function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Programaci&oacute;n de Proyectos por Divisi&oacute;n</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota2" style="position:absolute; left:3px; top:55px; width: 521px; height: 30px;">
Programaci&oacute;n  Suma Global </div>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table><table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>

    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="right">&nbsp;</td>
      </tr>
    </table>
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">  Proyecto </td>
  </tr>
</table>


    
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellspacing="1" cellpadding="1">
      <tr class="TituloTabla2">
        <td width="10%">ID</td>
        <td>Proyectos</td>
        <td width="20%">C&oacute;digo</td>
        <td width="20%">Encargados</td>
        </tr>
       <?  while ($reg=mssql_fetch_array($cursor)) {	  ?>
	  <tr class="TxtTabla">
	    <td width="10%"><? echo  $reg[id_proyecto] ; ?></td>
        <td><? echo  ucwords(strtolower($reg[nombre])) ; ?></td>
        <td width="20%"><? echo  trim($reg[codigo]) . "." . $reg[cargo_defecto] ; ?>
		<? $codProyecto = trim($reg[codigo]) ;?></td>
        <td width="20%"><? echo  ucwords(strtolower($reg[nombreD])) . " " . ucwords(strtolower($reg[apellidosD])) . "<br>" . ucwords(strtolower($reg[nombreC])) . " " . ucwords(strtolower($reg[apellidosC])); ?></td>
        </tr>
	  <? } ?>
    </table>
		
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td>&nbsp;</td>
              </tr>
            </table></td>
          </tr>
        </table>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TituloUsuario">Informaci&oacute;n de la programaci&oacute;n </td>
          </tr>
        </table>
        <table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td width="20%" class="TituloTabla">Fecha de inicio </td>
            <td class="TxtTabla">
			<? echo $pfechaInicio ;	?>
			</td>
          </tr>
          <tr>
            <td width="20%" class="TituloTabla">Plazo</td>
            <td class="TxtTabla"><? echo $pplazo ;	?></td>
          </tr>
          <tr>
            <td width="20%" class="TituloTabla">Suma global</td>
            <td class="TxtTabla">$ <? echo number_format($pvalorSumaGlobal, 0, ',', '.');?></td>
          </tr>
          <tr>
            <td width="20%" class="TituloTabla">Valor causado</td>
            <td class="TxtTabla">$ 
			<?
			$pTotalCausado=$pTotalProgUsu + $pTotalProgCD ;
			echo number_format($pTotalCausado, 0, ',', '.') ;
			$pTotalRemanente = $pvalorSumaGlobal - $pTotalCausado ;
			if ($pTotalRemanente < 0) {
				$cualClase = "TxtTablaAlerta" ;
			}
			else {
				$cualClase = "TxtTabla" ;			
			}
			?> 
			</td>
          </tr>
          <tr>
            <td width="20%" class="TituloTabla">Valor remanente </td>
            <td class="<? echo $cualClase; ?>">$ 
			<? 
			
			echo number_format($pTotalRemanente, 0, ',', '.') ;
			?> 
			</td>
          </tr>
        </table>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TituloUsuario">Empleados que participan en el proyecto </td>
          </tr>
        </table>
        <table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr class="TituloTabla2">
            <td width="5%">Unidad</td>
            <td>Nombre</td>
            <td width="10%">Salario</td>
			<? 
			$mesActual = $pMesInicial ;
			$anoActual = $pAnoInicial ;
			for ($e=1; $e<=$pplazo ; $e++) { 
				switch ($mesActual) {
				case 1:
					$nombreMes="Ene";
					break;
				case 2:
					$nombreMes="Feb";
					break;
				case 3:
					$nombreMes="Mar";
					break;
				case 4:
					$nombreMes="Abr";
					break;
				case 5:
					$nombreMes="May";
					break;
				case 6:
					$nombreMes="Jun";
					break;
				case 7:
					$nombreMes="Jul";
					break;
				case 8:
					$nombreMes="Ago";
					break;
				case 9:
					$nombreMes="Sep";
					break;
				case 10:
					$nombreMes="Oct";
					break;
				case 11:
					$nombreMes="Nov";
					break;
				case 12:
					$nombreMes="Dic";
					break;
				}
			?>
            <td><? echo $nombreMes . "-" . $anoActual;  ?></td>
			<? 
			$mesActual = $mesActual + 1;
			if ($mesActual > 12) {
				$mesActual = 1;
				$anoActual = $anoActual + 1;
			}
			} ?>
          </tr>
         <? while ($reg4=mssql_fetch_array($cursor4)) {  ?>
          <tr class="TxtTabla">
            <td width="5%"><? echo $reg4[unidad] ; ?></td>
            <td><? echo  ucwords(strtolower($reg4[apellidos])) . ", " .  ucwords(strtolower($reg4[nombre])) ; ?></td>
            <td width="10%" align="right">$ <? echo number_format($reg4[salario], 0, ',', '.');?></td>
            <? 
			$mesActualP = $pMesInicial ;
			$anoActualP = $pAnoInicial ;
			for ($p=1; $p<=$pplazo ; $p++) { 
				$phorasProgramadas = "";
				$pvalorProgramado = "";
				//Trae la programación para cada periodo
				$sql5="select * from ProgSumaGlobalUsu ";
				$sql5=$sql5." where id_proyecto = " . $cualProyecto ;
				$sql5=$sql5." and unidadProgramador = " . $cualUnidad ;
				$sql5=$sql5." and unidad =" . $reg4[unidad];
				$sql5=$sql5." and mes =" . $mesActualP ;
				$sql5=$sql5." and vigencia =" . $anoActualP;
				$cursor5 = mssql_query($sql5);
				if ($reg5=mssql_fetch_array($cursor5)) {	 
					$phorasProgramadas = $reg5[horasProgramadas];
					$pvalorProgramado = $reg5[valorProgramado];
				}
			?>
	            <td align="right">
				<? 
				if (trim($phorasProgramadas ) != "") {
					echo $phorasProgramadas . "<br>" . "$" . number_format($pvalorProgramado, 0, ',', '.') ; 
				}
				?>				</td>
			<? 
			$mesActualP = $mesActualP + 1;
			if ($mesActualP > 12) {
				$mesActualP = 1;
				$anoActualP = $anoActualP + 1;
			}
			} ?>
          </tr>
		<? } ?>  
        </table>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="right" class="TxtTabla">
			<? if ($pliberar == 1) { ?>
			<input name="Submit" type="submit" class="Boton" onclick="MM_openBrWindow('upRechazarSG.php?cualProyecto=<? echo $cualProyecto; ?>&cualUnidad=<? echo $cualUnidad; ?>','vRecSG','scrollbars=yes,resizable=yes,width=500,height=220')" value="Rechazar" />
            <input name="Submit2" type="submit" class="Boton" onclick="MM_openBrWindow('addProgProyectosSG.php?cualProyecto=<? echo $cualProyecto; ?>&cualUnidad=<? echo $cualUnidad; ?>','adPPSG','scrollbars=yes,resizable=yes,width=800,height=400')" value="Aceptar" />
			<? } ?>			</td>
          </tr>
        </table>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TituloUsuario">Costos directos </td>
          </tr>
        </table>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla"><table width="50%"  border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
              <tr>
                <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr class="TituloTabla2">
            <td>Item</td>
            <td width="20%">Valor</td>
            </tr>
          <? 
		  $totalCD=0;
		  while ($reg6=mssql_fetch_array($cursor6)) {  
		  	$totalCD=$totalCD+$reg6[valorItem];
		  ?>
		  <tr class="TxtTabla">
            <td><? echo ucfirst(strtolower($reg6[item])) ; ?></td>
            <td width="20%" align="right">$ <? echo number_format($reg6[valorItem], 0, ',', '.');?> </td>
            </tr>
		  <? } ?>
		  <tr class="TxtTabla">
		    <td>&nbsp;</td>
		    <td align="right"><strong>$ <? echo number_format($totalCD, 0, ',', '.');?></strong></td>
		    </tr>
        </table>
		        </td>
              </tr>
            </table></td>
          </tr>
        </table>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
          </tr>
        </table>
        
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TituloUsuario">Bit&aacute;cora Programaci&oacute;n por suma global </td>
          </tr>
        </table>
        <table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr class="TituloTabla2">
            <td width="10%">Fecha</td>
            <td width="15%">Programador Divisi&oacute;n</td>
            <td width="30%">Comentarios Programador Divisi&oacute;n </td>
            <td width="15%">Director/Coordinador Proyecto </td>
            <td>Comentario Director/Coordinador Proyecto </td>
          </tr>
          <? while ($regB=mssql_fetch_array($cursorB)) {  ?>
		  <tr class="TxtTabla">
            <td width="10%"><? echo date("M d Y ", strtotime($regB[fecha])) ; ?></td>
            <td width="15%"><? echo ucwords(strtolower($regB[apellidos])) . ", " . ucwords(strtolower($regB[nombre])) ; ?></td>
            <td width="30%"><? echo ucfirst(strtolower($regB[comentaProgramador]))  ; ?></td>
            <td width="15%"><? echo ucfirst(strtolower($regB[apeCoordina])) . ", " . ucfirst(strtolower($regB[nomCoordina])) ; ?></td>
            <td><? echo ucfirst(strtolower($regB[comentaProyecto]))  ; ?></td>
          </tr>
		<? } ?>
        </table>		
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
          </tr>
        </table>
        
        </td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td>&nbsp;</td>
          </tr>
</table><table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><input name="Submit3" type="submit" class="Boton" onclick="MM_goToURL('parent','ProgProyectosGeneral.php?cualProyecto=<? echo $cualProyecto; ?>');return document.MM_returnValue" value="Retornar a Programaci&oacute;n de proyectos general" />
    </td>
    <td align="right">&nbsp;
	</td>
  </tr>
</table>
</body>
</html>
