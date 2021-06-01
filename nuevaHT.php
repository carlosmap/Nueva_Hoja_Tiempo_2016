<?php
	session_start();
	include "funciones.php";
	include "validaUsrBd.php";

$Flmes = 3;
$Flano = 2009;

//20ABR2009
//ENCABEZADO DE LA HOJA DE TIEMPO*****************************
//Para incrustar las horas laborales de oficina, campo y categoria 42 estipuladas para la vigencia seleccionada 
//Consulta ara traer las hora laborales en el mes seleccionado
$qSql="Select * from horasydiaslaborales ";
//Si se carga por primera vez la hoja de tiempo trae el mes y año actual
if (trim($Flmes) == "") {
	$qSql=$qSql." where vigencia = year(getdate()) ";
	$qSql=$qSql." and mes = month(getdate()) ";
	$Flmes = gmdate ("n");
	$Flano = gmdate ("Y");
}
// si no, trae la información correspondiente al mes y año seleccionados en la lista
else {
	$qSql=$qSql." where vigencia = " . $Flano ;
	$qSql=$qSql." and mes = " .$Flmes;
}
$qCursor=mssql_query($qSql);
if ($qReg=mssql_fetch_array($qCursor)) {
	$horasOficina=$qReg[hOficina];
	$horasCampo=$qReg[hCampo];
	$horasCat42=$qReg[hCat42];
}


//Información del usuario
$encabSql="Select U.* , D.nombre nomDepartamento, C.nombre nomCategoria, A.nombre nomDivision, B.nombre nomDependencia ";
$encabSql=$encabSql." from usuarios U, departamentos D, categorias C, divisiones A, dependencias B ";
$encabSql=$encabSql." where U.id_departamento = D.id_departamento ";
$encabSql=$encabSql." and U.id_categoria = C.id_categoria ";
$encabSql=$encabSql." and D.id_division = A.id_division ";
$encabSql=$encabSql." and A.id_dependencia = B.id_dependencia ";
$encabSql=$encabSql." and U.unidad =  "  . $laUnidad;
$encabCursor=mssql_query($encabSql);	
if ($encabReg=mssql_fetch_array($encabCursor)) {
	if (trim($encabReg[NombreCorto]) == "") {
		$eNombreCorto= trim(ucwords($encabReg[apellidos])) . " " . trim(ucwords($encabReg[nombre]));
	}
	else {
		$eNombreCorto=$encabReg[NombreCorto];
	}
	if (trim($encabReg[TipoContrato]) == "TC") {
		$eTipoContrato="";
	}
	else {
		$eTipoContrato=$encabReg[TipoContrato];
	}
	$eCategoria=$encabReg[nomCategoria];
	$eDependencia=$encabReg[nomDependencia];
	if (trim(strtoupper($encabReg[nomDivision])) == 'SD') {
		$eDivision='';
	}
	else {
		$eDivision=$encabReg[nomDivision];
	}
	
	if (trim(strtoupper($encabReg[nomDepartamento])) == 'SD') {
		$eDepartamento='';
	}
	else {
		$eDepartamento=$encabReg[nomDepartamento];
	}
	$eSeccion=$encabReg[Seccion];
	$eSitioC=$encabReg[SitioContrato];
	$eSitioT=$encabReg[SitioTrabajo];
}
//CIERRA ENCABEZADO DE LA HOJA DE TIEMPO*****************************

//21Abr2009
//Contenido Hoja de tiempo

//Trae el listado de proyectos registrados en la tabla Horas, para el usuario activo
//mes y año actual o seleccionado
$sqlP="SELECT H.id_proyecto, H.id_actividad, H.localizacion, H.cargo, H.clase_tiempo, P.nombre, sum(horas_registradas),  ";
$sqlP=$sqlP." A.macroactividad, A.nombre nomActiv ";
$sqlP=$sqlP." FROM horas H , proyectos P, actividades A ";
$sqlP=$sqlP." where H.id_proyecto = P.id_proyecto ";
$sqlP=$sqlP." and H.id_proyecto = A.id_proyecto ";
$sqlP=$sqlP." and H.id_actividad = A.id_actividad ";
$sqlP=$sqlP." and H.unidad = " . $laUnidad;
$sqlP=$sqlP." and month(H.fecha)= " . $Flmes ;
$sqlP=$sqlP." and year(H.fecha)= " . $Flano ;
$sqlP=$sqlP." group by H.id_proyecto, H.id_actividad, H.localizacion, H.cargo, H.clase_tiempo, P.nombre, A.macroactividad, A.nombre ";
$sqlP=$sqlP." order by  H.cargo ";
$CursorP=mssql_query($sqlP);

//Cierra Contenido Hoja de tiempo


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<script language="JavaScript" src="ts_picker.js"></script>
<title>Hoja de tiempo</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body leftmargin="10" topmargin="3" rightmargin="10" bottommargin="0"  >
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td><table width="100%" border="0" cellspacing="1" cellpadding="0">
      <tr class="TxtTabla">
        <td width="25%"><img src="imagenes/imgLogo.gif"></td>
        <td align="center" class="TxtNota3" >
		HOJA DE TIEMPO <br>
            Mes <? echo nombremes_completo($Flmes) ; ?> de <? echo $Flano; ?>              
			<table width="100%" border="1" cellpadding="0" cellspacing="1" bordercolor="#FFFFFF">
              <tr align="center" class="TxtTabla">
                <td width="25%"><strong>Horas MES </strong></td>
                <td width="25%"><strong>Oficina <? echo $horasOficina; ?></strong></td>
                <td width="25%"><strong>Campo <? echo $horasCampo; ?></strong></td>
                <td width="25%"><strong>Categor&iacute;a 42 <? echo $horasCat42; ?></strong></td>
              </tr>
            </table>
          <br>          </td>
        <td width="30%" class="Titulos"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="Titulos"><? echo strtoupper($eNombreCorto) . "    " . strtoupper($eTipoContrato) . "   " . $laUnidad . "-" . strtoupper($eCategoria) ; ?>&nbsp;</td>
          </tr>
        </table>                    
          <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr class="Titulos">
              <td>DEP. <? echo strtoupper($eDependencia)  ; ?></td>
              <td>DIV. <? echo strtoupper($eDivision) ; ?></td>
            </tr>
            <tr class="Titulos">
              <td>DPT. <? echo strtoupper($eDepartamento)  ; ?></td>
              <td>SEC. <? echo strtoupper($eSeccion)  ; ?></td>
            </tr>
			<tr class="Titulos">
              <td>S.C. <? echo strtoupper($eSitioC)  ; ?></td>
              <td>S.T. <? echo strtoupper($eSitioT)  ; ?></td>
            </tr>
          </table>          </td>
      </tr>
    </table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td height="5"> </td>
        </tr>
      </table>
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr class="TituloTabla">
          <td><strong>C&oacute;digo</strong></td>
          <td width="2%" align="center"><strong>CT</strong></td>
		  <? for ($i=1; $i<=31; $i++) { ?>
          <td width="2%" align="center"><strong><? echo $i; ?></strong></td>
		  <? } ?>
          <td width="2%" align="center"><strong>Total</strong></td>
          <td width="2%" align="center"><strong>VoBo</strong></td>
          <td width="10%" align="center"><strong>Resumen</strong></td>
        </tr>
	  <?  while ($regP=mssql_fetch_array($CursorP)) {	  
	  				if($regP[macroactividad] <> NULL){
						$MActiv=$regP[macroactividad];
					}else{
						$MActiv=substr($regP[nombre],0,6);
					}
					
					//Trae las horas registradas por día
					$sqlH="SELECT day(fecha) dia, horas_registradas ";
					$sqlH=$sqlH." FROM horas ";
					$sqlH=$sqlH." where unidad = " . $laUnidad;
					$sqlH=$sqlH." and month(fecha)=" . $Flmes ;
					$sqlH=$sqlH." and year(fecha)=" . $Flano ;
					$sqlH=$sqlH." and id_proyecto =". $regP[id_proyecto];
					$sqlH=$sqlH." and id_actividad =". $regP[id_actividad];
					$sqlH=$sqlH." and clase_tiempo = ". $regP[clase_tiempo];
					$sqlH=$sqlH." and localizacion = ". $regP[localizacion];
					$sqlH=$sqlH." and cargo = '". $regP[cargo] . "' ";
					$sqlH=$sqlH." order by fecha ";
					$CursorH=mssql_query($sqlH);

					//Define el array de los dias del mes
					$horasDia = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
					while ($regH=mssql_fetch_array($CursorH)) {	  
						$horasDia[$regH[dia]] = $regH[horas_registradas]; ;
					}
					
						

	  ?>

        <tr class="TxtTabla">
          <td><? echo $regP[localizacion] . "-" . $regP[cargo] . " [".$MActiv."]" .  ucwords(substr($regP[nombre],0,14));  ?></td>
          <td width="2%" align="center"><? echo $regP[clase_tiempo] ;  ?></td>
		  <? for ($a=1; $a<=31; $a++) { ?>
          <td width="2%"><? echo $horasDia[$a] ; ?></td>
		  <?  } ?>
        </tr>
		<? 
			$MActiv = "";
		} ?>
      </table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td height="10">&nbsp;</td>
        </tr>
      </table>
      <table width="1024" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
      </table></td>
  </tr>
</table>
</body>
</html>
