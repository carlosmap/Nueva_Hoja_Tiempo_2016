<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

//echo "Proy=".  $cualProyecto . "<br>";
//echo "Act=" . $cualActividad . "<br>";
//exit;

//22Enero2008
//Trae el nombre de los proyectos en los que el usuario activo aparece como director y/o coordinador
$sql="SELECT P.*, D.nombre nombreD, D.apellidos apellidosD, C.nombre nombreC, C.apellidos apellidosC  ";
$sql=$sql." FROM proyectos P, Usuarios D, Usuarios C " ;
$sql=$sql." WHERE P.id_director *= D.unidad " ;
$sql=$sql." AND P.id_coordinador *= C.unidad " ;
$sql=$sql." AND P.id_proyecto = " . $cualProyecto ;
$cursor = mssql_query($sql);

if (trim($pOrdena)=="") {
	$pOrdena=1;
}

//11Sep2008
//Trae los registros de las divisiones
@mssql_select_db("HojaDeTiempo",$conexion);
$fDivSql="Select * from divisiones ";
$fDivSql=$fDivSql." where (nombre <> '' and nombre <> 'sd') ";
$fDivSql=$fDivSql."and estadoDiv = 'A' ";
$fDivSql=$fDivSql." order by nombre ";
$fDivCursor = mssql_query($fDivSql);

//01Feb2013
//Trae los RESPONSABLES de actividades, ResponsablesActividades (ingresados por los responsables delegando el tema
//y los ParticipantesActividades que se asignan como participantes en el tema 
$sql01="select consecutivo,nombre,apellidos from TrabajadoresExternos P
 where consecutivo in(select consecutivo from ParticipantesExternos where id_proyecto=683 )";
/*
if (trim($pfDivision) != "") {
	$sql01=$sql01." AND D.id_division =  " . trim($pfDivision) ;
}

if (trim($miDpto) != "") {
	$sql01=$sql01."  AND B.id_departamento =  " . trim($miDpto) ;
}
if (trim($pFiltro) != "") {
	$sql01=$sql01." AND B.id_categoria =  " . trim($pFiltro) ;
}

if (trim($pOrdena) == "1") {
	$sql01=$sql01." ORDER BY B.apellidos " ;
}
else {
	$sql01=$sql01." ORDER BY B.id_categoria " ;
}
*/
$cursor01 = mssql_query($sql01);





?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
<!--

window.name="winHojaTiempo";

function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>.:: Planeaci&oacute;n de Proyectos</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota2" style="position:absolute; left:3px; top:55px; width: 639px; height: 30px;">
Planeaci&oacute;n de proyectos - Participantes</div>
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
        <td>&nbsp;</td>
      </tr>
    </table>
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario"> .: PROYECTO</td>
  </tr>
</table>

    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
	<form name="form1" id="form1" method="post" action="">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellspacing="1" cellpadding="1">
      <tr class="TituloTabla2">
        <td>Proyecto</td>
        <td width="20%">C&oacute;digo</td>
        <td width="20%">Encargados</td>
        <td width="20%">Programadores</td>
      </tr>
       <?
	  while ($reg=mssql_fetch_array($cursor)) {
	  ?>

	  <tr valign="top" class="TxtTabla">
	    <td><? echo  ucwords(strtolower($reg[nombre])) ; ?></td>
        <td width="20%"><?
		//27Ene2009
		//Traer los cargos adicionales del proyecto
		$sqlCargos="SELECT * FROM HojaDeTiempo.dbo.Cargos ";
		$sqlCargos=$sqlCargos." where id_proyecto = " . trim($reg[id_proyecto]) ;
		$cursorCargos = mssql_query($sqlCargos);
		
		?>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="3%"><strong><? echo  trim($reg[codigo])  ; ?></strong></td>
            <td width="1%"><strong>.</strong></td>
            <td width="5%"><strong><? echo  $reg[cargo_defecto] ; ?></strong></td>
            <td>[<? echo  $reg[descCargoDefecto] ; ?>]</td>
            </tr>
		<? while ($regCargos=mssql_fetch_array($cursorCargos)) { ?>
          <tr>
            <td width="3%">&nbsp;</td>
            <td width="1%">.</td>
            <td width="5%"><? echo $regCargos[cargos_adicionales]; ?></td>
            <td>[<? echo $regCargos[descripcion]; ?>]</td>
            </tr>
		<? } ?>
        </table>
		</td>
        <td width="20%">
		<? 
		echo "<B>Director: </B><br>" . ucwords(strtolower($reg[nombreD])) . " " . ucwords(strtolower($reg[apellidosD])) . "<br>" ;
		echo "<B>Coordinador: </B><br>" . ucwords(strtolower($reg[nombreC])) . " " . ucwords(strtolower($reg[apellidosC])) . "<br>"; 
		$DirectorNombre =  ucwords(strtolower($reg[nombreD])) . " " . ucwords(strtolower($reg[apellidosD]));
		$DirectorUnidad = $reg[id_director];
		?>
		<? 
		$oSql="select O.*, U.nombre, U.apellidos ";
		$oSql=$oSql." from GestiondeInformacionDigital.dbo.OrdenadorGasto O, HojaDeTiempo.dbo.Usuarios U  ";
		$oSql=$oSql." where O.id_proyecto =" . $reg[id_proyecto] ;
		$oSql=$oSql." and O.unidadOrdenador = U.unidad ";
		$oCursor = mssql_query($oSql);
		echo "<br><strong>Ordenadores</strong><br>" ;
		while ($oReg=mssql_fetch_array($oCursor)) {
			echo  ucwords(strtolower($oReg[nombre])) . " " . ucwords(strtolower($oReg[apellidos])) . "<br>";
		}
		?>		</td>
        <td width="20%" align="right">
		<?
		//Lista los programadores del proyecto
		$pSql="Select P.* , U.nombre, U.apellidos ";
		$pSql=$pSql." from programadores P, Usuarios U ";
		$pSql=$pSql." where P.unidad = U.unidad ";
		$pSql=$pSql." and P.id_proyecto = " . $reg[id_proyecto] ;
		$pSql=$pSql." and P.progProyecto = 1 ";
		$pCursor = mssql_query($pSql);
		?>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
		<? while ($pReg=mssql_fetch_array($pCursor)) { ?>
          <tr>
            <td align="left"><? echo ucwords(strtolower($pReg[apellidos])). ", " . ucwords(strtolower($pReg[nombre]))   ; ?></td>
            </tr>
		<? } ?>
        </table>				</td>
	  </tr>
	  <? } ?>
    </table>
		
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
          </tr>
        </table>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla"><a href="htPlanProyectos.php" class="menu">&lt;&lt; Regresar a la Lista de Proyectos </a></td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr >
        <td width="15%" height="20" class="FichaInAct"><a href="htPlanProyectos01.php?cualProyecto=<? echo $cualProyecto; ?>" class="FichaInAct1" >EDT</a></td>
        <td width="15%" height="20" class="FichaAct">Participantes</td>
        <td width="15%" height="20" class="FichaInAct"><a href="htPlanProyectos03.php?cualProyecto=<? echo $cualProyecto; ?>" class="FichaInAct1" >Planeaci&oacute;n</a></td>
        <td width="15%" height="20" class="FichaInAct"><a href="htPlanProyectos04.php?cualProyecto=<? echo $cualProyecto; ?>" class="FichaInAct1" >Resumen</a></td>
        <td height="20" class="TxtTabla">&nbsp;</td>
      </tr>
      <tr>
        <td height="2" colspan="5" class="TituloUsuario"> </td>
        </tr>
    </table>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="center" class="TxtTabla"><table width="60%"  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td bgcolor="#FFFFFF"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td class="TituloUsuario">Criterios de consulta </td>
                  </tr>
                </table>
                  <table width="100%"  border="0" align="center" cellpadding="0" cellspacing="1">

      <tr>
        <td class="TituloTabla">Divisi&oacute;n</td>
        <td align="left" class="TxtTabla">
		<select name="pfDivision" class="CajaTexto" id="pfDivision" onChange="document.form1.submit();" >
		<? if (trim($pfDivision) == "") { 
			$selDiv = "selected";
			}
		?>
		<option value="" <? echo $selDiv; ?> ><? echo ":::Todas las Divisiones:::" ; ?></option>
	<? while ($fDivReg=mssql_fetch_array($fDivCursor)) { 	
			if ($pfDivision == $fDivReg[id_division]) {
				$selDiv = "selected";
			}
			else {
				$selDiv = "";
			}
	
	?>
      	<option value="<? echo $fDivReg[id_division]; ?>" <? echo $selDiv; ?> ><? echo ucwords(strtolower($fDivReg[nombre])) ; ?></option>
	<? } ?> 
	
    </select></td>
      </tr>
      <tr>
        <td class="TituloTabla">Departamento</td>
        <td align="left" class="TxtTabla">
		<?
	//Trae los departamentos asociados la divisi�n seleccionada
	$dTSql="Select * from departamentos where id_division = " . $pfDivision . " and estadoDpto = 'A' order by nombre" ;
	$dTcursor = mssql_query($dTSql);
	
	?>
	<select name="miDpto" class="CajaTexto" id="miDpto" onChange="document.form1.submit();" >
	<? if ($miDpto == "") { 
			$selItem="selected";
		}
	?>
		<option value="" <? echo $selItem; ?> >:::Todos:::</option>
	<? while ($regdT=mssql_fetch_array($dTcursor)) { 
			if ($miDpto == $regdT[id_departamento]) {
				$selIt="selected";
			}
			else {
				$selIt="";
			}
	?>
	  	<option value="<? echo $regdT[id_departamento]; ?>" <? echo $selIt; ?> ><? echo ucwords(strtolower($regdT[nombre])) ; ?></option>
	<? } ?>
    </select>		</td>
      </tr>
      <tr>
        <td width="20%" class="TituloTabla">Filtro Categor&iacute;a </td>
        <td align="left" class="TxtTabla">
		<? 
		$fSql="SELECT * FROM categorias";
		$fCursor = mssql_query($fSql);
		if (trim($pFiltro) == "") {
			$selFiltro = "selected";
		}
		?>		<select name="pFiltro" class="CajaTexto" id="pFiltro" onChange="document.form1.submit();" >
		<option value="" <? echo $selFiltro ; ?> >:::Todas:::</option>
	   <? 
	   	while ($fReg=mssql_fetch_array($fCursor)) {
	   		if ($pFiltro == $fReg[id_categoria]) {
				$selFiltro="selected";
			}
			else {
				$selFiltro="";
			}
	    ?>
          <option value="<? echo $fReg[id_categoria] ; ?>" <? echo $selFiltro; ?> ><? echo $fReg[nombre] ; ?></option>
	   <? } ?>
        </select></td>
      </tr>
      <tr>
        <td width="20%" class="TituloTabla">Ordenar por </td>
        <td align="left" class="TxtTabla">
		<?
		if ($pOrdena == 1) {
			$opc01="checked";
			$opc02="";
		}
		else {
			$opc01="";
			$opc02="checked";
		}
		?>
		<input name="pOrdena" type="radio" value="1" onClick="document.form1.submit();" <? echo $opc01; ?> />
          Alfab&eacute;ticamente por nombre 
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <input name="pOrdena" type="radio" value="2" onClick="document.form1.submit();" <? echo $opc02; ?> />
          Categor&iacute;a</td>
      </tr>
    </table>
                  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td height="2" class="TituloUsuario"> </td>
                    </tr>
                  </table></td>
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
            <td class="TituloUsuario">Participantes asociados a los Lotes de control / Lotes de trabajo / Divisiones / Actividades del proyecto </td>
          </tr>
        </table>
        <table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr class="TituloTabla2">
            <td width="8%">Unidad</td>
            <td width="20%">Nombre</td>

            <td>Actividades en las que se encuentra </td>
            <td width="10%">Categoria</td>
            <td width="1%">&nbsp;</td>
          </tr>
		  <? while ($reg01=mssql_fetch_array($cursor01)) { ?>
          <tr class="TxtTabla">
            <td><? echo $reg01[consecutivo] ; ?></td>
            <td><? echo strtoupper($reg01[apellidos]) . " " . strtoupper($reg01[nombre])  ; ?></td>


            <td colspan="2">
			<?

			//--Trae las actividades asociadas a un Encargado, Responsable o Participante
			$sql02="SELECT (valMacro * factor) miOrden, *  
			 FROM 
			( Select CAST(REPLACE((REPLACE((SUBSTRING(A.macroactividad, 3, LEN(A.macroactividad))),'.','')),'A','') AS int) valMacro,  
			factor =  
			case nivel 
				when 1 then 100000 
				when 2 then 10000 
				when 3 then 1000 
				when 4 then 100 
			end, A.* 
			from Actividades A 
			where A.id_proyecto = 683
			 ) Z 
			 WHERE id_actividad in 
			( 
			SELECT id_actividad 
			FROM ParticipantesExternos 
			WHERE id_proyecto = 683
			AND consecutivo =	".$reg01[consecutivo].") ";
			$sql02=$sql02." order by (valMacro * factor) ";						
			$cursor02 = mssql_query($sql02);			

			?>
			<table width="100%"  border="0" cellspacing="1" cellpadding="0">
			<? while ($reg02=mssql_fetch_array($cursor02)) { ?>
              <tr>
                <td width="10%"><strong><? echo strtoupper($reg02[macroactividad]); ?></strong></td>
                <td width="76%"><? echo strtoupper($reg02[nombre]); ?></td>
                <td width="14%">
<?php

					$sql_cat="select nombre from ParticipantesExternos  
					inner join Categorias on ParticipantesExternos.id_categoria=Categorias.id_categoria
					where id_proyecto=683 and id_actividad=".$reg02[id_actividad]."  and consecutivo=".$reg01[consecutivo];
					$cur_cat=mssql_query($sql_cat);
					while($datos_cat=mssql_fetch_array($cur_cat))
					{
						echo $datos_cat["nombre"];
					}


				//Verificar si es participante
				$esParticipante = "NO";
				$miEstado="";
				$sql03="SELECT *  ";
				$sql03=$sql03." FROM ParticipantesActividad ";
				$sql03=$sql03." WHERE id_proyecto =" . $cualProyecto;
				$sql03=$sql03." AND id_actividad =" . $reg02[id_actividad] ;
				$sql03=$sql03." AND unidad =" . $reg01[unidad] ;
				$cursor03 = mssql_query($sql03);	
				if ($reg03=mssql_fetch_array($cursor03)) {
					$esParticipante = "SI";
					$miEstado=$reg03[estado];
				}
				
				if ($esParticipante == "SI") {
				if (trim($miEstado) == "A") { ?>
				<img title="Activo" src="img/images/alertaAzul.gif"  width="15" height="16" />
				<?
				}
				if (trim($miEstado) == "I") {
				?>
				<img src="img/images/alertaRojo.gif" title="Inactivo" width="15" height="16" />
				<? }
				
				} //if es participante
				 ?>
				</td>
              </tr>
			<? } //while reg02 ?>
            </table>			</td>
            <td>
			<? if ($esParticipante == "SI") { ?>
			<img onclick="MM_openBrWindow('delHTAutParticipante.php?cualProyecto=<? echo $cualProyecto ; ?>&idAct=<?= $reg01[unidad] ?>', 'addAAT', 'scrollbars=yes,resizable=yes,width=620,height=400')" src="imagenes/actualizar2.gif" alt="" style="cursor: hand;" />
			<? } ?>
			</td>
          </tr>
		  <? } ?>
        </table>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="right" class="TxtTabla">
<!---
		<input name="Submit" type="submit" class="Boton" onclick="MM_openBrWindow('addHTAutParticipante.php?cualProyecto=<? echo $cualProyecto ; ?>','addAAT','scrollbars=yes,resizable=yes,width=500,height=400')" value="Ingresar Participante" />
-->
		<input name="Submit" type="submit" class="Boton" onclick="MM_openBrWindow('addParticipantesEDT.php?cualProyecto=<? echo $cualProyecto ; ?>','addAAT','scrollbars=yes,resizable=yes,width=500,height=600')" value="Ingresar Participantes" />

		<input name="Submit" type="submit" class="Boton" onclick="MM_openBrWindow('participante_ex.php?cualProyecto=<? echo $cualProyecto ; ?>','addAAT','scrollbars=yes,resizable=yes,width=600,height=400')" value="Ingresar Participantes Externos" />


		<input name="Submit" type="submit" class="Boton" onclick="MM_openBrWindow('delParticipantesEDT.php?cualProyecto=<? echo $cualProyecto ; ?>','addAAT','scrollbars=yes,resizable=yes,width=500,height=600')" value="Desasociar Participantes" />

<input name="Submit" type="submit" class="Boton" onclick="MM_openBrWindow('delEDTParticipantes.php?cualProyecto=<? echo $cualProyecto ; ?>','addAAT','scrollbars=yes,resizable=yes,width=500,height=400')" value="Eliminar Participantes Total" />
		</td>
          </tr>
        </table>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="right" class="TxtTabla">&nbsp;
			</td>
          </tr>
        </table>
        </td>
      </tr>
	  </ form >
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="TxtTabla">&nbsp;</td>
      </tr>
    </table>	</td>
  </tr>
</table>

</body>
</html>
