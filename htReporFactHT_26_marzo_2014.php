<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

?>

<?


?>
<html>
<head>

<title>.:: Facturaci&oacute;n por divisi&oacute;n  ::.</title>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
	function MM_callJS(jsStr) { //v2.0
	  return eval(jsStr)
	}

	window.name="winHojaTiempo";

	function envia0()
	{

		var mensaje="";
		if(document.form1.pDivision.value=="")
			mensaje="seleccione un división.\n";

		if(document.form1.mes.value=="")
			mensaje+="seleccione un mes.\n";

		if(document.form1.lstVigencia.value=="")
			mensaje+="seleccione una vigencia.";

		if(mensaje!="")
			alert(mensaje);
		else
			document.form1.submit();
	}

</script>
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0"  bgcolor="#EAEAEA">
<?PHP
/*
//CONSULTA SI EL USUARIO, TIENE PERFIL DE CONTRATOS, YA QUE ELLOS SON LOS UNICOS QUE PUEDEN DAR EL VOBO DE CONTRATOS
$sql_usu_contratos="select Usuarios.*  from Usuarios  
inner join GestiondeInformacionDigital.dbo.PerfilUsuarios on GestiondeInformacionDigital.dbo.PerfilUsuarios.unidad=Usuarios.unidad
where retirado is null
and GestiondeInformacionDigital.dbo.PerfilUsuarios.codPerfil=16 
or GestiondeInformacionDigital.dbo.PerfilUsuarios.codPerfil=1
and GestiondeInformacionDigital.dbo.PerfilUsuarios.unidad=".$laUnidad;


$cur_contratos=mssql_query($sql_usu_contratos);

//echo $sql_usu_contratos." **** ".mssql_num_rows($cur_contratos);
if( ( (int) mssql_num_rows($cur_contratos)) >0 )
{
*/
?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><? include("bannerArriba.php") ; ?>
<div style="position:absolute; left:3px; top:55px; width: 557px; height: 30px;" class="TxtNota2">
Reporte facturaci&oacute;n por divisi&oacute;n </div>
	</td>
  </tr>
</table>


<form name="form1" method="post" action="">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="Fecha"><? echo strtoupper($nombreempleado." ".$apellidoempleado); ?></td>
  </tr>
</table>
<table width="100%" border="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>

<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Criterios de consulta</td>
  </tr>
</table>
<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td bgcolor="#FFFFFF">
	<table width="100%" bgcolor="#FFFFFF"  border="0" align="center" cellpadding="0" cellspacing="1">
  <tr>
    <td class="TituloTabla">Divisi&oacute;n</td>
    <td class="TxtTabla"><select name="pDivision" class="CajaTexto" id="pDivision" onChange="MM_callJS('document.form1.submit();')">
		<option value="">Seleccione una Divisi&oacute;n</option>
	          <?
			$qSql1="Select * from divisiones where estadoDiv='A' " ;
			$qCursor1 = mssql_query($qSql1);
			while ($qReg1=mssql_fetch_array($qCursor1)) {
				if ($pDivision == $qReg1[id_division]) {
					$selDiv = "selected";
				}
				else {
					$selDiv = "";
				}
			?>
     <option value="<? echo $qReg1[id_division]; ?>" <? echo $selDiv; ?> ><? echo ucwords(strtolower($qReg1[nombre])); ?></option>
	 		<? } ?>
    </select>      </td>
  </tr>
  <tr>
    <td class="TituloTabla">Departamento</td>
    <td class="TxtTabla">
	<select name="pDepto" class="CajaTexto" id="pDepto" onChange="MM_callJS('document.form1.submit();')">
		<option value="">Seleccione un Departamento </option>
	          <?
			  if (trim($pDivision) == "") {
				$qSql2="Select * from departamentos where estadoDpto='A' and  id_division = 1 "  ;
			  }
			  else {
				$qSql2="Select * from departamentos where estadoDpto='A' and  id_division =" . $pDivision ;			  
			  }
			  
			$qCursor2 = mssql_query($qSql2);
			while ($qReg2=mssql_fetch_array($qCursor2)) {
				if ($pDepto == $qReg2[id_departamento]) {
					$selDep = "selected";
				}
				else {
					$selDep = "";
				}
			?>
     <option value="<? echo $qReg2[id_departamento]; ?>" <? echo $selDep; ?> ><? echo ucwords(strtolower($qReg2[nombre])); ?></option>
	 		<? } ?>
    </select>	</td>
  </tr>

 <tr>
    <td class="TituloTabla">Categor&iacute;a</td>
    <td class="TxtTabla"><select name="pCategoria" class="CajaTexto" id="pCategoria" >
			<option value="" >Seleccione una Categoria<? echo ""; ?></option>
          <?

			$sql2="Select * from Categorias  where estadoCat='A' order by nombre " ;
			$cursor2 = mssql_query($sql2);
			while ($reg2=mssql_fetch_array($cursor2)) {
				if ($pCategoria == $reg2[id_categoria]) {
					$selCat = "selected";
				}
				else {
					$selCat = "";
				}
			?>
          <option value="<? echo $reg2[id_categoria]; ?>" <? echo $selCat; ?> ><? echo $reg2[nombre]; ?></option>
          <? } ?>
    </select></td>
  </tr>
<?
	$mess = array( '', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre' );
?>
      <tr class="TxtTabla" >
        <td class="TituloTabla">Mes</td>
        <td align="left"><select name="mes" class="CajaTexto" id="mes" >
          <option value="">::Seleccione Mes::</option>
          <? 
						if(!isset($mes))
							$mes=( (int) date('m') );

						for($i=1;$i<=12;$i++)
						{						
                                    if ($mes== $i) {
                                        $selMes = "selected";
                                    }
                                    else {
                                        $selMes = "";
                                    }
                                ?>
          <option value="<? echo $i; ?>" <? echo $selMes; ?> ><? echo $mess[$i]; ?></option>
          <?	} ?>
        </select></td>

      </tr>
      <tr class="TxtTabla" >
        <td width="15%" class="TituloTabla">Vigencia</td>
        <td align="left"><select name="lstVigencia" class="CajaTexto" id="lstVigencia" >
          <option value="">::Seleccione Vigencia::</option>
          <? 
								if(!isset($lstVigencia))
									$lstVigencia=date('Y');

                                for ($k=2000; $k<=2100; $k++) { 
                                    if ($lstVigencia == $k) {
                                        $selVig = "selected";
                                    }
                                    else {
                                        $selVig = "";
                                    }
                                ?>
          <option value="<? echo $k; ?>" <? echo $selVig; ?> ><? echo $k; ?></option>
          <? } ?>
          </select></td>
      </tr>
		 <tr class="TxtTabla" >
        	<td colspan="2" align="right" class="TxtTabla">
				<input type="hidden" name="recarga" value="0" id="recarga" />
		        <input name="Consultar" onclick="envia0();" type="button" class="Boton" id="Consultar" value="Consultar" />
			</td>
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
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Resumen estadistico de la facturaci&oacute;n de la Divisi&oacute;n </td>
  </tr>
</table>
<table width="100%"  border="0" cellpadding="0" cellspacing="1"  bgcolor="#FFFFFF" >
 


  <tr>
    <td colspan="2" class="TituloTabla">Usuarios</td>
  </tr>
<?php
	$filtros="";
	///FILTROS DE BUSQUEDA
	if(trim($pDepto)!="")
	{
	}
	if(trim($pCategoria)!="")
	{
	}

/*
	if(trim($pDivision)!="")
	{
		$filtros=$filtros." ";
	}	
*/
/*
	if(trim($mes)!="")
	{
	}
	if(trim($lstVigencia)!="")
	{
	}
*/
?> 
  <tr>
    <td width="20%" class="TxtTabla">Cantidad planeados </td>
    <td class="TxtTabla">
<?
	if( ( trim( $pDivision)!="") and ( trim( $mes)!="") and ( trim($lstVigencia)!="") )
	{
/*
		//CANT USUARIOS PLANEADOS, EN LAS ACITIVDADES DE LOS PORYECTOS, EN DONDE LA DIVISION TIENE PARTICIPACION
		$sql_usu_plane="select count(distinct(PlaneacionProyectos.unidad)) cant_planeado from PlaneacionProyectos 
		inner join Actividades on PlaneacionProyectos.id_proyecto=Actividades.id_proyecto and PlaneacionProyectos.id_actividad=Actividades.id_actividad
		inner join Usuarios on PlaneacionProyectos.unidad=Usuarios.unidad
		inner join Departamentos on Usuarios.id_departamento=Departamentos.id_departamento
		inner join Divisiones on Departamentos.id_division=Divisiones.id_division	
		where vigencia=".$lstVigencia." and PlaneacionProyectos.mes=".$mes." and PlaneacionProyectos.esInterno='I' and Actividades.id_division=".$pDivision;
*/
		//CANTIDAD DE USUARIOS DE LA DIVISION, QUE HAN SIDO PLANEADOS EN LOS PROYECTOS
		$sql_usu_plane="select count(distinct(PlaneacionProyectos.unidad)) cant_planeado from PlaneacionProyectos 
		inner join Usuarios on PlaneacionProyectos.unidad=Usuarios.unidad
		inner join Departamentos on Usuarios.id_departamento=Departamentos.id_departamento
		inner join Divisiones on Departamentos.id_division=Divisiones.id_division
		where vigencia=".$lstVigencia." and PlaneacionProyectos.mes=".$mes." and PlaneacionProyectos.esInterno='I' and Divisiones.id_division=".$pDivision;
	
		if(trim($pDepto)!="")
		{
			$sql_usu_plane=$sql_usu_plane." and Usuarios.id_departamento=".$pDepto;
		}

		if(trim($pCategoria)!="")
		{
			$sql_usu_plane=$sql_usu_plane." and Usuarios.id_categoria=".$pCategoria;
		}
		$dato_usu_planea=mssql_fetch_array(mssql_query($sql_usu_plane));
		echo $dato_usu_planea["cant_planeado"]; //." --- <br>".$sql_usu_plane;
	}
?>
	</td>
  </tr>
  <tr>
    <td class="TxtTabla">Cantidad facturados</td>
    <td class="TxtTabla">
<?
	if( ( trim( $pDivision)!="") and ( trim( $mes)!="") and ( trim($lstVigencia)!="") )
	{
/*
		//USUARIOS QUE FACTURAN A  LAS ACITIVDADES DE LOS PORYECTOS, EN DONDE LA DIVISION TIENE PARTICIPACION
        $sql_factu="	select count(distinct(FacturacionProyectos.unidad)) cant_facturados  from FacturacionProyectos 
		inner join Actividades on FacturacionProyectos.id_proyecto=Actividades.id_proyecto and FacturacionProyectos.id_actividad=Actividades.id_actividad
		inner join Usuarios on FacturacionProyectos.unidad=Usuarios.unidad
		inner join Departamentos on Usuarios.id_departamento=Departamentos.id_departamento
		inner join Divisiones on Departamentos.id_division=Divisiones.id_division	
        where vigencia=".$lstVigencia." and mes=".$mes." and FacturacionProyectos.esInterno='I'  and Actividades.id_division=".$pDivision;
*/
		//USUARIOS DE LA DIVISION, QUE FACTURARON EN ALGUN PROYECTOS
		$sql_factu=" select COUNT(distinct(FacturacionProyectos.unidad)) cant_facturados  from FacturacionProyectos 
        inner join Usuarios on FacturacionProyectos.unidad=Usuarios.unidad
        inner join Departamentos on Usuarios.id_departamento=Departamentos.id_departamento
        inner join Divisiones on Departamentos.id_division=Divisiones.id_division
        where FacturacionProyectos.vigencia=".$lstVigencia." and FacturacionProyectos.mes=".$mes." and FacturacionProyectos.esInterno='I'  and Divisiones.id_division=".$pDivision;

		if(trim($pDepto)!="")
		{
			$sql_factu=$sql_factu." and Usuarios.id_departamento=".$pDepto;
		}

		if(trim($pCategoria)!="")
		{
			$sql_factu=$sql_factu." and Usuarios.id_categoria=".$pCategoria;
		}

		$dato_usu_planea=mssql_fetch_array(mssql_query($sql_factu));
		echo $dato_usu_planea["cant_facturados"]." ";
	}
?>
	</td>
  </tr>
  <tr>
    <td class="TxtTabla">Planeados con facturaci&oacute;n</td>
    <td class="TxtTabla">
<?
	if( ( trim( $pDivision)!="") and ( trim( $mes)!="") and ( trim($lstVigencia)!="") )
	{
/*
		//CANT USUARIOS PLANEADOS CON FACTURACION EN  LAS ACITIVDADES DE LOS PORYECTOS, EN DONDE LA DIVISION TIENE PARTICIPACION
		$sql_planea_fac=" select  COUNT(distinct(PlaneacionProyectos.unidad) )cant_factu_planea from PlaneacionProyectos 
		inner join FacturacionProyectos on PlaneacionProyectos.id_proyecto=FacturacionProyectos.id_proyecto and PlaneacionProyectos.id_actividad=FacturacionProyectos.id_actividad
		and PlaneacionProyectos.unidad=FacturacionProyectos.unidad and PlaneacionProyectos.vigencia=FacturacionProyectos.vigencia and PlaneacionProyectos.mes=FacturacionProyectos.mes
		and PlaneacionProyectos.esInterno=FacturacionProyectos.esInterno
		inner join Actividades on FacturacionProyectos.id_proyecto=Actividades.id_proyecto and FacturacionProyectos.id_actividad=Actividades.id_actividad
		inner join Usuarios on PlaneacionProyectos.unidad=Usuarios.unidad
		inner join Departamentos on Usuarios.id_departamento=Departamentos.id_departamento
		inner join Divisiones on Departamentos.id_division=Divisiones.id_division
		where PlaneacionProyectos.vigencia=".$lstVigencia." and PlaneacionProyectos.mes=".$mes." and PlaneacionProyectos.esInterno='I'  and Actividades.id_division=".$pDivision."";
*/
		//USUARIOS DE LA DIVISION, QUE FUERON PLANEADOS EN ALGUN PROYECTO Y TIENEN FACTURACION
		$sql_planea_fac="select  count(distinct(PlaneacionProyectos.unidad)) cant_factu_planea from PlaneacionProyectos 
		inner join FacturacionProyectos on PlaneacionProyectos.id_proyecto=FacturacionProyectos.id_proyecto and PlaneacionProyectos.id_actividad=FacturacionProyectos.id_actividad
		and PlaneacionProyectos.unidad=FacturacionProyectos.unidad and PlaneacionProyectos.vigencia=FacturacionProyectos.vigencia and PlaneacionProyectos.mes=FacturacionProyectos.mes
		and PlaneacionProyectos.esInterno=FacturacionProyectos.esInterno
		inner join Usuarios on PlaneacionProyectos.unidad=Usuarios.unidad
		inner join Departamentos on Usuarios.id_departamento=Departamentos.id_departamento
		inner join Divisiones on Departamentos.id_division=Divisiones.id_division 
		where PlaneacionProyectos.vigencia=".$lstVigencia." and PlaneacionProyectos.mes=".$mes." and PlaneacionProyectos.esInterno='I'  and Divisiones.id_division=".$pDivision."";

		if(trim($pDepto)!="")
		{
			$sql_planea_fac=$sql_planea_fac." and Usuarios.id_departamento=".$pDepto;
		}


		if(trim($pCategoria)!="")
		{
			$sql_planea_fac=$sql_planea_fac." and Usuarios.id_categoria=".$pCategoria;
		}

		$dato_usu_planea=mssql_fetch_array(mssql_query($sql_planea_fac));
		echo $dato_usu_planea["cant_factu_planea"]." ";
	}
?>
	</td>
  </tr>
  <tr>
    <td class="TxtTabla">Facturaci&oacute;n sin planeaci&oacute;n</td>
    <td class="TxtTabla">
<?
	if( ( trim( $pDivision)!="") and ( trim( $mes)!="") and ( trim($lstVigencia)!="") )
	{
/*
		$sql_fac_sin_plan="	select count(distinct(FacturacionProyectos.unidad)) cant_facturado_sin_plan  from FacturacionProyectos 
		inner join Usuarios on FacturacionProyectos.unidad=Usuarios.unidad
		inner join Departamentos on Usuarios.id_departamento=Departamentos.id_departamento
		inner join Divisiones on Departamentos.id_division=Divisiones.id_division
		inner join Actividades on FacturacionProyectos.id_proyecto=Actividades.id_proyecto and FacturacionProyectos.id_actividad=Actividades.id_actividad
		
			where vigencia=".$lstVigencia." and mes=".$mes." and FacturacionProyectos.esInterno='I' and Actividades.id_division=".$pDivision." and FacturacionProyectos.unidad not in (
	
			select distinct(PlaneacionProyectos.unidad) unidad from PlaneacionProyectos 
			inner join Usuarios USU on PlaneacionProyectos.unidad=Usuarios.unidad
			inner join Departamentos DEP on Usuarios.id_departamento=DEP.id_departamento
			inner join Divisiones DIV on Departamentos.id_division=DIV.id_division
			inner join Actividades ACT on PlaneacionProyectos.id_proyecto=Actividades.id_proyecto and PlaneacionProyectos.id_actividad=Actividades.id_actividad
			where vigencia=FacturacionProyectos.vigencia and PlaneacionProyectos.mes=FacturacionProyectos.mes and PlaneacionProyectos.esInterno=FacturacionProyectos.esInterno	
			and USU.id_categoria=Usuarios.id_categoria	and ACT.id_division=Actividades.id_division				
		)	";
*/
		//USUARIOS DE LA DIVISION, QUE GENERARON FACTURACION EN ALGUNA DE LAS ACTIVIDADES DE UN PROYECTO Y NO TENIA PLANEACION
		$sql_fac_sin_plan=" select count(distinct(FacturacionProyectos.unidad)) cant_facturado_sin_plan  from FacturacionProyectos 
		inner join Usuarios on FacturacionProyectos.unidad=Usuarios.unidad
		inner join Departamentos on Usuarios.id_departamento=Departamentos.id_departamento
		inner join Divisiones on Departamentos.id_division=Divisiones.id_division
		where FacturacionProyectos.id_proyecto <>56 and vigencia=".$lstVigencia." and mes=".$mes." and FacturacionProyectos.esInterno='I' and Divisiones.id_division=".$pDivision." and FacturacionProyectos.unidad not in (
		
			select distinct(PlaneacionProyectos.unidad) unidad from PlaneacionProyectos 
			inner join Usuarios USU on PlaneacionProyectos.unidad=Usuarios.unidad
			inner join Departamentos DEP on Usuarios.id_departamento=DEP.id_departamento
			inner join Divisiones DIV on Departamentos.id_division=DIV.id_division
			where vigencia=FacturacionProyectos.vigencia and PlaneacionProyectos.mes=FacturacionProyectos.mes and PlaneacionProyectos.esInterno=FacturacionProyectos.esInterno	

			and FacturacionProyectos.id_proyecto=PlaneacionProyectos.id_proyecto and FacturacionProyectos.id_actividad=PlaneacionProyectos.id_actividad 

			and USU.id_categoria=Usuarios.id_categoria
			and DIV.id_division=Divisiones.id_division
			and Departamentos.id_departamento=DEP.id_departamento";

		$sql_fac_sin_plan=$sql_fac_sin_plan.")";

		if(trim($pDepto)!="")
		{
			$sql_fac_sin_plan=$sql_fac_sin_plan." and Usuarios.id_departamento=".$pDepto;
		}

		if(trim($pCategoria)!="")
		{
			$sql_fac_sin_plan=$sql_fac_sin_plan." and Usuarios.id_categoria=".$pCategoria;
		}

		$dato_usu_planea=mssql_fetch_array(mssql_query($sql_fac_sin_plan));
		echo $dato_usu_planea["cant_facturado_sin_plan"]; //." ---".mssql_get_last_message();
	}
		
?>
	</td>
  </tr>
  <tr>
    <td class="TxtTabla">Planeados que no facturaron</td>
    <td class="TxtTabla">
<?php
	if( ( trim( $pDivision)!="") and ( trim( $mes)!="") and ( trim($lstVigencia)!="") )
	{
/*
        $sql_plane_no_fac="		select count(distinct(PlaneacionProyectos.unidad)) cant_plane_no_fac from PlaneacionProyectos 
		inner join Usuarios on PlaneacionProyectos.unidad=Usuarios.unidad
		inner join Departamentos on Usuarios.id_departamento=Departamentos.id_departamento
		inner join Divisiones on Departamentos.id_division=Divisiones.id_division
		inner join Actividades on PlaneacionProyectos.id_proyecto=Actividades.id_proyecto and PlaneacionProyectos.id_actividad=Actividades.id_actividad	
		where vigencia=".$lstVigencia." and PlaneacionProyectos.mes=".$mes." and PlaneacionProyectos.esInterno='I' and Actividades.id_division=".$pDivision."  and PlaneacionProyectos.unidad not in (
	
			select distinct(FacturacionProyectos.unidad) uni_facturados  from FacturacionProyectos 	
			inner join Usuarios USU on FacturacionProyectos.unidad=Usuarios.unidad
			inner join Departamentos DEP on Usuarios.id_departamento=DEP.id_departamento
			inner join Divisiones DIV on Departamentos.id_division=DIV.id_division		
			inner join Actividades ACT on FacturacionProyectos.id_proyecto=ACT.id_proyecto and FacturacionProyectos.id_actividad=ACT.id_actividad
			
			where FacturacionProyectos.vigencia=PlaneacionProyectos.vigencia and PlaneacionProyectos.mes=FacturacionProyectos.mes and PlaneacionProyectos.esInterno=FacturacionProyectos.esInterno	
			and USU.id_categoria=Usuarios.id_categoria		
			and ACT.id_division=Actividades.id_division			
		)  ";
*/
		//USUARIOS DE LA DIVISION, QUE FUERON PLANEADOS EN LAS ACTIVIDADES DE UN PROYECTO, Y NO HAN FACTURADO
		$sql_plane_no_fac=" select count(distinct(PlaneacionProyectos.unidad)) cant_plane_no_fac from PlaneacionProyectos 
        inner join Usuarios on PlaneacionProyectos.unidad=Usuarios.unidad
        inner join Departamentos on Usuarios.id_departamento=Departamentos.id_departamento
        inner join Divisiones on Departamentos.id_division=Divisiones.id_division
        where vigencia=".$lstVigencia." and PlaneacionProyectos.mes=".$mes." and PlaneacionProyectos.esInterno='I'  and Divisiones.id_division=".$pDivision." and PlaneacionProyectos.unidad not in (
        
            select distinct(FacturacionProyectos.unidad) uni_facturados  from FacturacionProyectos 	
            inner join Usuarios USU on FacturacionProyectos.unidad=Usuarios.unidad
            inner join Departamentos DEP on Usuarios.id_departamento=DEP.id_departamento
            inner join Divisiones DIV on Departamentos.id_division=DIV.id_division
            where vigencia=FacturacionProyectos.vigencia and PlaneacionProyectos.mes=FacturacionProyectos.mes and PlaneacionProyectos.esInterno=FacturacionProyectos.esInterno	

			and FacturacionProyectos.id_proyecto=PlaneacionProyectos.id_proyecto and FacturacionProyectos.id_actividad=PlaneacionProyectos.id_actividad 

            and USU.id_categoria=Usuarios.id_categoria
            and DIV.id_division=Divisiones.id_division
            and Departamentos.id_departamento=DEP.id_departamento 
        )";

		if(trim($pDepto)!="")
		{
			$sql_plane_no_fac=$sql_plane_no_fac." and Usuarios.id_departamento=".$pDepto;
		}

		if(trim($pCategoria)!="")
		{
			$sql_plane_no_fac=$sql_plane_no_fac." and Usuarios.id_categoria=".$pCategoria;
		}

		$dato_usu_planea=mssql_fetch_array(mssql_query($sql_plane_no_fac));
		echo $dato_usu_planea["cant_plane_no_fac"]; //." ---";
	}
?>
	</td>
  </tr>
  <tr>
    <td class="TxtTabla">Usuarios que sobrepasan lo planeado</td>
    <td class="TxtTabla">
<?
/*
		$sql_abajo="
	select count(*) cant_abaj_planea from
	(
		select T1.*,
		(
			select SUM(hombresMesF) facturado  from FacturacionProyectos
			inner join Actividades on FacturacionProyectos.id_proyecto=Actividades.id_proyecto and FacturacionProyectos.id_actividad=Actividades.id_actividad				
			
			inner join PlaneacionProyectos on PlaneacionProyectos.id_proyecto=FacturacionProyectos.id_proyecto and PlaneacionProyectos.id_actividad=FacturacionProyectos.id_actividad
			and PlaneacionProyectos.unidad=FacturacionProyectos.unidad and PlaneacionProyectos.vigencia=FacturacionProyectos.vigencia and PlaneacionProyectos.mes=FacturacionProyectos.mes			
			and PlaneacionProyectos.esInterno=FacturacionProyectos.esInterno
			
			where FacturacionProyectos.vigencia=T1.vigencia and FacturacionProyectos.mes=T1.mes
			and FacturacionProyectos.esInterno='I' and FacturacionProyectos.unidad=T1.unidad		
			and Actividades.id_division=".$pDivision." 
		) total_facturacion
		from (
			select SUM(hombresMes) hombresMes , PlaneacionProyectos.unidad, mes, vigencia
			from PlaneacionProyectos		
				inner join Actividades on PlaneacionProyectos.id_proyecto=Actividades.id_proyecto and PlaneacionProyectos.id_actividad=Actividades.id_actividad
				inner join Usuarios on PlaneacionProyectos.unidad=Usuarios.unidad
				inner join Departamentos on Usuarios.id_departamento=Departamentos.id_departamento
				inner join Divisiones on Departamentos.id_division=Divisiones.id_division
			where PlaneacionProyectos.vigencia=".$lstVigencia."  and PlaneacionProyectos.mes=".$mes." and PlaneacionProyectos.esInterno='I' ";
			if(trim($pCategoria)!="")
			{
				$sql_abajo=$sql_abajo." and Usuarios.id_categoria=".$pCategoria;
			}
			 $sql_abajo=$sql_abajo." and Actividades.id_division=".$pDivision." 
			group by PlaneacionProyectos.unidad,mes, vigencia 
				
		) T1	
	) T2 where hombresMes<total_facturacion ";
*/
		//CONSOLIDA EL TOTAL DE LA FACTURACION Y PLANEACION GENERADA PARA CADA UNO DE LAS PERSONAS QUE HACEN PARTE DE LA DIVISION, EN TODOS LOS PROYECTOS, PARA LA VIGENCIA
		//Y MES SELECCIONADOS Y COMPARA LOS TOTALES 
		$sql_abajo="select count(*) cant_abaj_planea from
		(
			select T1.*,
			(
				select SUM(hombresMesF) facturado  from FacturacionProyectos	
				where FacturacionProyectos.vigencia=T1.vigencia and FacturacionProyectos.mes=T1.mes
				and FacturacionProyectos.esInterno='I' and FacturacionProyectos.unidad=T1.unidad			
			) total_facturacion
			from (
				select SUM(hombresMes) hombresMes , PlaneacionProyectos.unidad, mes, vigencia	
				from PlaneacionProyectos		
					inner join Usuarios on PlaneacionProyectos.unidad=Usuarios.unidad
					inner join Departamentos on Usuarios.id_departamento=Departamentos.id_departamento
					inner join Divisiones on Departamentos.id_division=Divisiones.id_division
				where PlaneacionProyectos.vigencia=".$lstVigencia." and PlaneacionProyectos.mes=".$mes."  and PlaneacionProyectos.esInterno='I' ";

				if(trim($pDepto)!="")
				{
					$sql_abajo=$sql_abajo." and Usuarios.id_departamento=".$pDepto;
				}
				if(trim($pCategoria)!="")
				{
					$sql_abajo=$sql_abajo." and Usuarios.id_categoria=".$pCategoria;
				}
				$sql_abajo=$sql_abajo."and Divisiones.id_division=".$pDivision."
				group by PlaneacionProyectos.unidad,mes, vigencia
			) T1	
		) T2 where hombresMes<total_facturacion ";

		$dato_usu_planea=mssql_fetch_array(mssql_query($sql_abajo));
		echo $dato_usu_planea["cant_abaj_planea"]; //." ---"; //.mssql_get_last_message()." <br><br>".$sql_abajo;
?>
	</td>
  </tr>
  <tr>
    <td class="TxtTabla">Usuarios que estan por debajo de lo planeado</td>
    <td class="TxtTabla">
<?
/*
		$sql_arriba="select count(*) cant_abaj_planea from
	(
		select T1.*,
		(
			select SUM(hombresMesF) facturado  from FacturacionProyectos
			inner join Actividades on FacturacionProyectos.id_proyecto=Actividades.id_proyecto and FacturacionProyectos.id_actividad=Actividades.id_actividad				
			
			inner join PlaneacionProyectos on PlaneacionProyectos.id_proyecto=FacturacionProyectos.id_proyecto and PlaneacionProyectos.id_actividad=FacturacionProyectos.id_actividad
			and PlaneacionProyectos.unidad=FacturacionProyectos.unidad and PlaneacionProyectos.vigencia=FacturacionProyectos.vigencia and PlaneacionProyectos.mes=FacturacionProyectos.mes			
			and PlaneacionProyectos.esInterno=FacturacionProyectos.esInterno
			
			where FacturacionProyectos.vigencia=T1.vigencia and FacturacionProyectos.mes=T1.mes
			and FacturacionProyectos.esInterno='I' and FacturacionProyectos.unidad=T1.unidad		
			and Actividades.id_division=".$pDivision." 
		) total_facturacion
		from (
			select SUM(hombresMes) hombresMes , PlaneacionProyectos.unidad, mes, vigencia
			from PlaneacionProyectos		
				inner join Actividades on PlaneacionProyectos.id_proyecto=Actividades.id_proyecto and PlaneacionProyectos.id_actividad=Actividades.id_actividad
				inner join Usuarios on PlaneacionProyectos.unidad=Usuarios.unidad
				inner join Departamentos on Usuarios.id_departamento=Departamentos.id_departamento
				inner join Divisiones on Departamentos.id_division=Divisiones.id_division
			where PlaneacionProyectos.vigencia=".$lstVigencia."  and PlaneacionProyectos.mes=".$mes." and PlaneacionProyectos.esInterno='I' ";
			if(trim($pCategoria)!="")
			{
				$sql_arriba=$sql_arriba." and Usuarios.id_categoria=".$pCategoria;
			}
			 $sql_arriba=$sql_arriba." and Actividades.id_division=".$pDivision." 
			group by PlaneacionProyectos.unidad,mes, vigencia 
				
		) T1	
	) T2 where hombresMes>total_facturacion ";
*/

		//CONSOLIDA EL TOTAL DE LA FACTURACION Y PLANEACION GENERADA PARA CADA UNO DE LAS PERSONAS QUE HACEN PARTE DE LA DIVISION, EN TODOS LOS PROYECTOS, PARA LA VIGENCIA
		//Y MES SELECCIONADOS Y COMPARA LOS TOTALES 
		$sql_arriba="select count(*) cant_abaj_planea from
		(
			select T1.*,
			(
				select SUM(hombresMesF) facturado  from FacturacionProyectos	
				where FacturacionProyectos.vigencia=T1.vigencia and FacturacionProyectos.mes=T1.mes
				and FacturacionProyectos.esInterno='I' and FacturacionProyectos.unidad=T1.unidad			
			) total_facturacion
			from (
				select SUM(hombresMes) hombresMes , PlaneacionProyectos.unidad, mes, vigencia	
				from PlaneacionProyectos		
					inner join Usuarios on PlaneacionProyectos.unidad=Usuarios.unidad
					inner join Departamentos on Usuarios.id_departamento=Departamentos.id_departamento
					inner join Divisiones on Departamentos.id_division=Divisiones.id_division
				where PlaneacionProyectos.vigencia=".$lstVigencia." and PlaneacionProyectos.mes=".$mes."  and PlaneacionProyectos.esInterno='I' ";

				if(trim($pDepto)!="")
				{
					$sql_arriba=$sql_arriba." and Usuarios.id_departamento=".$pDepto;
				}
				if(trim($pCategoria)!="")
				{
					$sql_arriba=$sql_arriba." and Usuarios.id_categoria=".$pCategoria;
				}
				$sql_arriba=$sql_arriba." and Divisiones.id_division=".$pDivision."
				group by PlaneacionProyectos.unidad,mes, vigencia
			) T1	
		) T2 where hombresMes>total_facturacion ";

		$dato_usu_planea=mssql_fetch_array(mssql_query($sql_arriba));
		echo $dato_usu_planea["cant_abaj_planea"];
?>
	</td>
  </tr>
  <tr>
    <td class="TxtTabla">Usuarios con viaticos </td>
    <td class="TxtTabla">
<?
		//USUARIOS QUE HACEN PARTE DE LA DIVISION, Y GENERARON VIATICOS EN LOS PROYECTOS
		$sql_viatic="select COUNT(distinct(ViaticosProyectosHT.unidad)) cant_viaticos from ViaticosProyectosHT
		inner join Usuarios on ViaticosProyectosHT.unidad=Usuarios.unidad
		inner join Departamentos on Usuarios.id_departamento=Departamentos.id_departamento
		inner join Divisiones on Departamentos.id_division=Divisiones.id_division
		where vigencia=".$lstVigencia." and ViaticosProyectosHT.mes=".$mes."  and ViaticosProyectosHT.esInterno='I' and Divisiones.id_division=".$pDivision." ";

		if(trim($pDepto)!="")
		{
			$sql_viatic=$sql_viatic." and Usuarios.id_departamento=".$pDepto;
		}
		if(trim($pCategoria)!="")
		{
			$sql_viatic=$sql_viatic." and Usuarios.id_categoria=".$pCategoria;
		}

/*
		$sql_viatic="select COUNT(distinct(ViaticosProyectosHT.unidad)) cant_viaticos from ViaticosProyectosHT
		inner join Usuarios on ViaticosProyectosHT.unidad=Usuarios.unidad
		inner join Departamentos on Usuarios.id_departamento=Departamentos.id_departamento
		inner join Divisiones on Departamentos.id_division=Divisiones.id_division
		inner join Actividades on ViaticosProyectosHT.id_proyecto=Actividades.id_proyecto and ViaticosProyectosHT.id_actividad=Actividades.id_actividad
		where vigencia=".$lstVigencia." and ViaticosProyectosHT.mes=".$mes."  and ViaticosProyectosHT.esInterno='I' and Actividades.id_division=".$pDivision." ";
		if(trim($pCategoria)!="")
		{
			$sql_viatic=$sql_viatic." and Usuarios.id_categoria=".$pCategoria;
		}
*/
//echo $sql_viatic." --------------- ";
		$dato_usu_planea=mssql_fetch_array(mssql_query($sql_viatic));
		echo  $dato_usu_planea["cant_viaticos"];//." ---"." ---".mssql_get_last_message()." <br><br>".$sql_viatic;;
?>
	</td>
  </tr>
  <tr>
    <td class="TxtTabla">D&iacute;as generados de vi&aacute;ticos</td>
    <td class="TxtTabla">
<?
/*/
		$sql_dias_via="select  sum(DATEDIFF(day,FechaIni,FechaFin)+1) cant_dia  from ViaticosProyectosHT
		inner join Usuarios on ViaticosProyectosHT.unidad=Usuarios.unidad
		inner join Departamentos on Usuarios.id_departamento=Departamentos.id_departamento
		inner join Divisiones on Departamentos.id_division=Divisiones.id_division
		inner join Actividades on ViaticosProyectosHT.id_proyecto=Actividades.id_proyecto and ViaticosProyectosHT.id_actividad=Actividades.id_actividad
		where vigencia=".$lstVigencia." and ViaticosProyectosHT.mes=".$mes." and ViaticosProyectosHT.esInterno='I'  and Actividades.id_division=".$pDivision." ";
*/
		//CANT DE DIAS EN LOS QUE LAS PERSONAS QUE HACEN PARTE DE LA DIVISION, HAN GENERADO, EN LOS DIFERENTES PROYECTOS
		$sql_dias_via="select  sum(DATEDIFF(day,FechaIni,FechaFin)+1) cant_dia  from ViaticosProyectosHT
		inner join Usuarios on ViaticosProyectosHT.unidad=Usuarios.unidad
		inner join Departamentos on Usuarios.id_departamento=Departamentos.id_departamento
		inner join Divisiones on Departamentos.id_division=Divisiones.id_division
		where vigencia=".$lstVigencia." and ViaticosProyectosHT.mes=".$mes." and ViaticosProyectosHT.esInterno='I'  and Divisiones.id_division=".$pDivision." ";

		if(trim($pDepto)!="")
		{
			$sql_dias_via=$sql_dias_via." and Usuarios.id_departamento=".$pDepto;
		}


		if(trim($pCategoria)!="")
		{
			$sql_dias_via=$sql_dias_via." and Usuarios.id_categoria=".$pCategoria;
		}
//echo $sql_dias_via." ---- ";
		$dato_usu_planea=mssql_fetch_array(mssql_query($sql_dias_via));
		if(trim($dato_usu_planea["cant_dia"])=="")
			echo "0";
		else
			echo  $dato_usu_planea["cant_dia"]; //." ---";"<br> $sql_dias_via  ---- <br>".mssql_get_last_message()
?>
	</td>
  </tr>
</table>
<br>
<table width="100%" border="0"  bgcolor="#FFFFFF">
  <tr>
    <td colspan="2" class="TituloTabla">Dinero</td>
  </tr>
  <tr>
<?
	$sql_val="select SUM(valorReal) valorReal ,SUM(valorAsignado) valorAsignado from AsignaValorDivision where id_division=".$pDivision." ";
	$dato_val=mssql_fetch_array(mssql_query($sql_val));
?>
    <td width="20%" class="TxtTabla">Valor real <img title="Valor real de la divisi&oacute;n en todos los proyectos." src="../NuevaHojaTiempo/imagenes/icoDetalleInf.gif"></td>
    <td width="80%" class="TxtTabla"><? echo  "$ " . number_format($dato_val["valorReal"], 2, ",", ".")." ";  ?></td>
  </tr>
  <tr>
    <td class="TxtTabla">Valor asignado <img title="Valor asignado a la divisi&oacute;n en todos los proyectos." src="../NuevaHojaTiempo/imagenes/icoDetalleInf.gif"></td>
    <td width="80%" class="TxtTabla"><? 		echo  "$ " . number_format($dato_val["valorAsignado"], 2, ",", ".")." "; ?></td>
  </tr>
  <tr>
    <td class="TxtTabla">Valor planeado <img title="Valor planeado para la divisi&oacute;n en todos los proyectos, durante año y mes seleccionados." src="../NuevaHojaTiempo/imagenes/icoDetalleInf.gif"></td>
    <td class="TxtTabla">
<?
		$sql_planeado=" select SUM(PlaneacionProyectos.valorPlaneado) val_planea 
		from PlaneacionProyectos
		inner join Usuarios on PlaneacionProyectos.unidad=Usuarios.unidad
		inner join Actividades on PlaneacionProyectos.id_proyecto=Actividades.id_proyecto and PlaneacionProyectos.id_actividad=Actividades.id_actividad
		where Actividades.id_division=".$pDivision." and PlaneacionProyectos.vigencia=".$lstVigencia." 
		and PlaneacionProyectos.mes=".$mes." ";
/*
		if(trim($pDepto)!="")
		{
			$sql_planeado=$sql_planeado." and Usuarios.id_departamento=".$pDepto;
		}
*/
/*
		if(trim($pCategoria)!="")
		{
			$sql_planeado=$sql_planeado." and Usuarios.id_categoria=".$pCategoria;
		}
*/
		$dato_usu_planea=mssql_fetch_array(mssql_query($sql_planeado));
		echo  "$ " . number_format($dato_usu_planea["val_planea"], 2, ",", ".")." ";
?>

	</td>
  </tr>
  <tr>
    <td class="TxtTabla">Valor facturado <img title="Valor facturado para la divisi&oacute;n en todos los proyectos, durante año y mes seleccionados." src="../NuevaHojaTiempo/imagenes/icoDetalleInf.gif"></td>
    <td class="TxtTabla">
<?
		$sql_factu="select SUM(FacturacionProyectos.valorFacturado) val_fac
		from FacturacionProyectos
		inner join Usuarios on FacturacionProyectos.unidad=Usuarios.unidad
		inner join Actividades on FacturacionProyectos.id_proyecto=Actividades.id_proyecto and FacturacionProyectos.id_actividad=Actividades.id_actividad
		where Actividades.id_division=".$pDivision." and FacturacionProyectos.vigencia=".$lstVigencia." 
		and FacturacionProyectos.mes=".$mes." ";
/*
		if(trim($pDepto)!="")
		{
			$sql_factu=$sql_factu." and Usuarios.id_departamento=".$pDepto;
		}
*/
		if(trim($pCategoria)!="")
		{
			$sql_factu=$sql_factu." and Usuarios.id_categoria=".$pCategoria;
		}
		$dato_usu_planea=mssql_fetch_array(mssql_query($sql_factu));
		echo  "$ " . number_format($dato_usu_planea["val_fac"], 2, ",", ".")." ";

?>
	</td>
  </tr>
</table>
<br>

<table width="100%" border="0"  bgcolor="#FFFFFF" >
  <tr>
    <td colspan="2" class="TituloTabla">Proyectos</td>
  </tr>
  <tr>
    <td width="10%" class="TxtTabla">Cantidad de proyecto</td>
    <td width="90%" class="TxtTabla">
<?
		$sql_can_proy="select COUNT(*) can_proy from AsignaValorDivision where id_division=".$pDivision;
		$datos=mssql_fetch_array(mssql_query($sql_can_proy));
		echo $datos["can_proy"];
?>
	</td>
  </tr>
  <tr>
    <td class="TxtTabla">Con VoBo</td>
    <td class="TxtTabla">
<?
	//CANTIDAD DE USUARIOS CON TODA LA FACTURACION APROBADA, EN LAS ACTIVIDADES EN DONDE SE HA DEFINIDO LA DIVISION, EN TODOS LOS PROYECTOS
	$sql_vobo="	select count(*) vobo from (
	--CANTIDAD DE PROYECTOS EN LOS QUE EL USUARIO HA REGISTRADO FACTURACION A LAS ACTIVIDADES, ASOCIADOS A LA DIVISION 
		select FacturacionProyectos.unidad, count(distinct(FacturacionProyectos.id_proyecto)) proyectoss,	
		isnull(
		(
			--CANTIDAD DE PROYECTOS EN LOS QUE SE HA APROBADO LA FACTURACION DEL USUARIO, ASOCIADO A LA DIVISION 
			select proyecto from(
				SELECT VoBoFactuacionProyHT.unidad, 
				count(distinct(VoBoFactuacionProyHT.id_proyecto)) proyecto FROM VoBoFactuacionProyHT
				inner join Actividades on Actividades.id_proyecto=VoBoFactuacionProyHT.id_proyecto and Actividades.id_actividad=VoBoFactuacionProyHT.id_actividad
				where VoBoFactuacionProyHT.vigencia=FacturacionProyectos.vigencia  and VoBoFactuacionProyHT.mes=FacturacionProyectos.mes 
				 and VoBoFactuacionProyHT.esInterno='I'  and Actividades.id_division=".$pDivision." and VoBoFactuacionProyHT.unidad=FacturacionProyectos.unidad
				group by VoBoFactuacionProyHT.unidad
			) aa	
		),0) 	
		pro	
		,FacturacionProyectos.vigencia ,FacturacionProyectos.mes
		from FacturacionProyectos 	
		inner join Actividades on Actividades.id_proyecto=FacturacionProyectos.id_proyecto and Actividades.id_actividad=FacturacionProyectos.id_actividad
		where FacturacionProyectos.vigencia=".$lstVigencia." and FacturacionProyectos.mes=".$mes." and FacturacionProyectos.esInterno='I'  and Actividades.id_division=".$pDivision."	
		group by FacturacionProyectos.unidad , FacturacionProyectos.vigencia ,FacturacionProyectos.mes 	
	)zzz 
	where proyectoss = pro";
	$dato_vobo=mssql_fetch_array(mssql_query($sql_vobo));
	echo $dato_vobo["vobo"]." <br>".mssql_get_last_message();
?>
	</td>
  </tr>
  <tr>
    <td class="TxtTabla">Sin VoBo</td>
    <td class="TxtTabla"><?
	//CANTIDAD DE USUARIOS Q QUIENES NO SE LE HA APROBADO TODA LA FACTURACION, EN LAS ACTIVIDADES EN DONDE SE HA DEFINIDO LA DIVISION, EN TODOS LOS PROYECTOS
	$sql_vobo="	select count(*) vobo from (
	--CANTIDAD DE PROYECTOS EN LOS QUE EL USUARIO HA REGISTRADO FACTURACION A LAS ACTIVIDADES, ASOCIADOS A LA DIVISION 
		select FacturacionProyectos.unidad, count(distinct(FacturacionProyectos.id_proyecto)) proyectoss,	
		isnull(
		(
			--CANTIDAD DE PROYECTOS EN LOS QUE SE HA APROBADO LA FACTURACION DEL USUARIO, ASOCIADO A LA DIVISION 
			select proyecto from(
				SELECT VoBoFactuacionProyHT.unidad, 
				count(distinct(VoBoFactuacionProyHT.id_proyecto)) proyecto FROM VoBoFactuacionProyHT
				inner join Actividades on Actividades.id_proyecto=VoBoFactuacionProyHT.id_proyecto and Actividades.id_actividad=VoBoFactuacionProyHT.id_actividad
				where VoBoFactuacionProyHT.vigencia=FacturacionProyectos.vigencia  and VoBoFactuacionProyHT.mes=FacturacionProyectos.mes 
				 and VoBoFactuacionProyHT.esInterno='I'  and Actividades.id_division=".$pDivision." and VoBoFactuacionProyHT.unidad=FacturacionProyectos.unidad
				group by VoBoFactuacionProyHT.unidad
			) aa	
		),0) 	
		pro	
		,FacturacionProyectos.vigencia ,FacturacionProyectos.mes
		from FacturacionProyectos 	
		inner join Actividades on Actividades.id_proyecto=FacturacionProyectos.id_proyecto and Actividades.id_actividad=FacturacionProyectos.id_actividad
		where FacturacionProyectos.vigencia=".$lstVigencia." and FacturacionProyectos.mes=".$mes." and FacturacionProyectos.esInterno='I'  and Actividades.id_division=".$pDivision."	
		group by FacturacionProyectos.unidad , FacturacionProyectos.vigencia ,FacturacionProyectos.mes 	
	)zzz 
	where proyectoss != pro";
	$dato_vobo=mssql_fetch_array(mssql_query($sql_vobo));
	echo $dato_vobo["vobo"]." <br>".mssql_get_last_message();
?></td>
  </tr>
  <tr>
    <td colspan="2" class="TxtTabla">&nbsp;</td>
    </tr>
  <tr>
    <td colspan="2" ><table width="100%" border="0">
      <tr class="TituloTabla">
        <td>Proyecto</td>
        <td>Valor real</td>
        <td>Valor asignado</td>
        <td>Valor planeado</td>
        <td>Valor facturado</td>
        <td>% Gastado</td>
        <td>% Disponible</td>
        <td>&nbsp;</td>
      </tr>
<?
	$sql_proys="select nombre,valorReal,valorAsignado ,codigo,cargo_defecto,AsignaValorDivision.id_proyecto from AsignaValorDivision
	inner join Proyectos on AsignaValorDivision.id_proyecto=Proyectos.id_proyecto 
	where id_division=".$pDivision;
	$cur_proy=mssql_query($sql_proys);
	$val_real=0;
	$val_asig=0;
	$val_plane=0;
	$val_fac=0;
	while($datos_proys=mssql_fetch_array($cur_proy))
	{
		$val_real+=$datos_proys["valorReal"];
		$val_asig+=$datos_proys["valorAsignado"];
?>
      <tr class="TxtTabla">

        <td><?="[".$datos_proys["codigo"].".".$datos_proys["cargo_defecto"]."] ".$datos_proys["nombre"] ?></td>
        <td><?="$ " . number_format($datos_proys["valorReal"], 2, ",", ".")." " ?></td>
        <td><?="$ " . number_format($datos_proys["valorAsignado"], 2, ",", ".")." " ?></td>
        <td>
			<?
                    $sql_planeado=" select SUM(PlaneacionProyectos.valorPlaneado) val_planea 
                    from PlaneacionProyectos
                    inner join Usuarios on PlaneacionProyectos.unidad=Usuarios.unidad
                    inner join Actividades on PlaneacionProyectos.id_proyecto=Actividades.id_proyecto and PlaneacionProyectos.id_actividad=Actividades.id_actividad
                    where Actividades.id_division=".$pDivision." and PlaneacionProyectos.vigencia=".$lstVigencia." 
                    and PlaneacionProyectos.mes=".$mes." and  PlaneacionProyectos.id_proyecto=".$datos_proys["id_proyecto"];
/*
                    if(trim($pCategoria)!="")
                    {
                        $sql_planeado=$sql_planeado." and Usuarios.id_categoria=".$pCategoria;
                    }
*/
                    $dato_usu_planea=mssql_fetch_array(mssql_query($sql_planeado));
                    echo  "$ " . number_format($dato_usu_planea["val_planea"], 2, ",", ".")." "; //.mssql_get_last_message()."<br>".$sql_planeado;
					$val_plane+=$dato_usu_planea["val_planea"];

            ?>
			</td>
        <td>
			<?
                    $sql_factu="select SUM(FacturacionProyectos.valorFacturado) val_fac
                    from FacturacionProyectos
                    inner join Usuarios on FacturacionProyectos.unidad=Usuarios.unidad
                    inner join Actividades on FacturacionProyectos.id_proyecto=Actividades.id_proyecto and FacturacionProyectos.id_actividad=Actividades.id_actividad
                    where Actividades.id_division=".$pDivision." and FacturacionProyectos.vigencia=".$lstVigencia." 
                    and FacturacionProyectos.mes=".$mes."  and  FacturacionProyectos.id_proyecto=".$datos_proys["id_proyecto"];
/*
                    if(trim($pCategoria)!="")
                    {
                        $sql_factu=$sql_factu." and Usuarios.id_categoria=".$pCategoria;
                    }
*/
                    $dato_usu_fac=mssql_fetch_array(mssql_query($sql_factu));
                    echo  "$ " . number_format($dato_usu_fac["val_fac"], 2, ",", ".")." ";
		            $val_fac+=$dato_usu_fac["val_fac"];

            ?>
		</td>
        <td><? 
//echo $dato_usu_planea["val_planea"]." --- ".$dato_usu_fac["val_fac"]."<br>";
				//SI VALOR PLANEADO ES = 0 SE MUESTRA EL ICONO DE COLOR ROJO, YA QUE AL EJECUTAR LA OPERACION, NO SE PUEDE DIVIDIR POR 0
				if( ( $dato_usu_planea["val_planea"]=="") &&($dato_usu_fac["val_fac"]!=""))
					echo '<IMG src="../portal/imagenes/ico1.gif" width=77 height=16 title="Facturación sin planeación" >';
				else
				{
					//SE CALCULA EL PORCENTAJO DEL VALOR GASTADO
					$por_gast=( (( (int) $dato_usu_fac["val_fac"])/ ( (int) $dato_usu_planea["val_planea"])) *100 );

					if($por_gast>100)
					 echo "<strong style='color:red;' > ".number_format($por_gast,2,'.','')."% </strong>";

					else
					 echo number_format($por_gast,2,'.','')."%";
				}


		?></td>
        <td><?
			$val_disp=( (int) $dato_usu_planea["val_planea"])-( (int) $dato_usu_fac["val_fac"]);

//			echo $val_disp."="."(".$dato_usu_planea["val_planea"].")-( ".$dato_usu_fac["val_fac"].")<br>";

//			echo  number_format($val_disp,2,'.','')."<br>".$val_disp;

			//SE CALCULA EL PORCENTAJO DEL VALOR DISPONIBLE
			$por_dis=( ( $val_disp/ ( (int) $dato_usu_planea["val_planea"])) *100 ) ;

			//SI EL RESULTADO DA EN NEGATIVO
			if($por_dis<0)
				echo "0.00%";			
			else
				echo  number_format($por_dis,2,'.','')."%";

		?></td>
        <td align="center">
          <input name="button" type="submit" class="Boton" id="button" value="Detalle"></td>
      </tr>
<?
	}
echo mssql_get_last_message();
?>
      <tr>
        <td class="TituloTabla">Total</td>
        <td class="TxtTabla"><?="$ " . number_format($val_real, 2, ",", ".")." "; ?></td>
        <td class="TxtTabla"><?="$ " . number_format($val_asig, 2, ",", ".")." "; ?></td>
        <td class="TxtTabla"><?="$ " . number_format($val_plane, 2, ",", ".")." "; ?></td>
        <td class="TxtTabla"><?="$ " . number_format($val_fac, 2, ",", ".")." "; ?></td>
        <td class="TxtTabla">&nbsp;</td>
        <td class="TxtTabla">&nbsp;</td>
        <td align="center" class="TxtTabla">&nbsp;</td>
      </tr>
    </table></td>

  </tr>
</table>
</form>
<? 
/*
}
else
{
	echo '<table width="100%" border="0" cellspacing="0" cellpadding="0">

			  <tr>
				<td class="TxtTabla">&nbsp;</td>
			
			  </tr>
			  <tr>
				<td class="TituloUsuario">.:: Atenci&oacute;n</td>
			
			  </tr>

			  <tr>
				<td align="center" class="TxtTabla"  ><BR>
				<b>Usted no est&aacute; autorizado, para acceder a la informaci&oacute;n de esta p&aacute;gina. </b><BR><BR>
				</td>
			  </tr>
			  <tr>
				<td align="center" class="TituloTabla2"  >
					<input type="button" value="Cerrar" class="Boton" onClick="window.close()" >
				</td>
			  </tr>
			</table>';
}
*/
mssql_close ($conexion); ?>	
<p>&nbsp;</p>
</body>
</html>


