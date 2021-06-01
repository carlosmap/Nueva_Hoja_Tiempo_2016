<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";


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

//29Abr2008
//Trae el listado de las actividades de proyecto
$sqlA="select * from actividades ";
$sqlA=$sqlA." where id_proyecto = " . $cualProyecto ;
//$cursorA = mssql_query($sqlA);

//trae los datosa de la lista clase de tiempo
$sqlB="select * from clase_tiempo " ;
$sqlB=$sqlB." WHERE (clase_tiempo = 1  OR clase_tiempo = 2) " ;
//$cursorB = mssql_query($sqlB);

//Trae el cargo_defecto y los cargos_adicionales del proyeecto seleccionado	
$sqlC="select id_proyecto, cargo_defecto cargos  " ;
$sqlC=$sqlC." from proyectos where id_proyecto = " . $cualProyecto ;
$sqlC=$sqlC." union " ;
$sqlC=$sqlC." select id_proyecto, cargos_adicionales cargos  " ;
$sqlC=$sqlC." from cargos where id_proyecto =" . $cualProyecto ;
//$cursorC = mssql_query($sqlC);

//Trae los horarios del proyecto
$sqlD="select H.* , A.NomHorario, A.Lunes, A.Martes, A.Miercoles,  " ;
$sqlD=$sqlD." A.Jueves, A.Viernes, A.Sabado, A.Domingo " ;
$sqlD=$sqlD." from HorariosProy H, Horarios A " ;
$sqlD=$sqlD." where H.IDhorario = A.IDHorario  " ;
$sqlD=$sqlD." and H.id_proyecto = " . $cualProyecto ;
//$cursorD = mssql_query($sqlD);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Programaci&oacute;n de Proyectos por Divisi&oacute;n</title>
</head>

<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <form name="form1" id="form1" method="post" action="">
      <tr>
        <td bgcolor="#FFFFFF">
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
			<td>Actividad</td>
            <td>Clase Tiempo </td>
            <td>Localizaci&oacute;n</td>
            <td>Cargo</td>
            <td>Horario</td>
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
			<td align="center">
			<select name="pActiv" class="CajaTexto" id="pActiv">
	         <? 
			 $cursorA = mssql_query($sqlA);
			 while ($regA=mssql_fetch_array($cursorA)) {  ?>			  
			  <option value="<? echo $regA[id_actividad] ; ?>"><? echo ucfirst(strtolower($regA[nombre])) ; ?></option>
			 <? } ?> 
		    </select></td>
            <td align="center">
			<select name="pClase" class="CajaTexto" id="pClase">
			<? 
			$cursorB = mssql_query($sqlB);
			while ($regB=mssql_fetch_array($cursorB)) { ?>
			  <option value="<? echo  $regB[clase_tiempo] ; ?>"><? echo  $regB[descripcion] ; ?></option>
			<? } ?>  
			</select>
			</td>
            <td align="center">
			<select name="pLocaliza" class="CajaTexto" id="pLocaliza">
			  <option value="1">1 - Oficina</option>
			  <option value="2">2 - Campo </option>
			  <option value="3">3 - Personal de planilla</option>
			</select>			
			</td>
            <td align="center">
			<select name="pCargo" class="CajaTexto" id="pCargo">
			<? 
			$cursorC = mssql_query($sqlC);
			while ($regC=mssql_fetch_array($cursorC)) { ?>
			  <option value="<? echo  $regC[cargos] ; ?>"><? echo  $regC[cargos] ; ?></option>
			<? } ?>
			</select>			
			</td>
            <td align="center">
			<select name="pHorario" class="CajaTexto" id="pHorario">
			<? 
			$cursorD = mssql_query($sqlD);
			while ($regD=mssql_fetch_array($cursorD)) { ?>
			  <option value="<? echo  $regD[IDhorario] ; ?>"><? echo strtoupper($regD[NomHorario]) . ":::" . $regD[Lunes] . "-" . $regD[Martes] . "-" . $regD[Miercoles] . "-". $regD[Jueves] .  "-" . $regD[Viernes]."-" .$regD[Sabado]. "-" . $regD[Domingo]  ; ?></option>
			<? } ?>
			</select>			
			</td>
          </tr>
		<? } ?>  
        </table>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="right" class="TxtTabla">
            <input name="Submit2" type="submit" class="Boton" value="Grabar" />		    </td>
          </tr>
        </table>

        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
          </tr>
        </table>        </td>
      </tr>
        </form>        			  
    </table>
</body>
</html>
