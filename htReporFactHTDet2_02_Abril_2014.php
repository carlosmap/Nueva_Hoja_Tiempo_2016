

<?
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

//$cualProyecto =683;
 include("bannerArriba.php") ; 

$mensaje= array('Cantidad planeados ','Cantidad facturados','Planeados con facturación','Facturación sin planeación','Planeados que no facturaron','Usuarios que sobrepasan lo planeado','Usuarios que estan por debajo de lo planeado');

		//(CONSULTA 1) CANTIDAD DE USUARIOS, QUE HAN SIDO PLANEADOS EN LAS ACTIVIDADES EN LAS QUE SE HA DEFINIDO LA DIVISION ( O DEPENDEN DE ELLA-NIVEL 4)  EN LOS PROYECTOS
		$sql_usu_plane="select distinct PlaneacionProyectos.unidad, Usuarios.nombre, Usuarios.apellidos,Usuarios.fechaRetiro,Usuarios.retirado from PlaneacionProyectos 
			inner join Actividades on PlaneacionProyectos.id_proyecto=Actividades.id_proyecto and PlaneacionProyectos.id_actividad=Actividades.id_actividad
			inner join Usuarios on PlaneacionProyectos.unidad=Usuarios.unidad
			inner join Departamentos on Usuarios.id_departamento=Departamentos.id_departamento
			inner join Divisiones on Departamentos.id_division=Divisiones.id_division
		where vigencia=".$vigencia." and PlaneacionProyectos.mes=".$pMes." and PlaneacionProyectos.esInterno='I' and Actividades.id_division=".$pDivision;
		if(trim($cualProyecto)!="")
		{
			$sql_usu_plane=$sql_usu_plane." and PlaneacionProyectos.id_proyecto=".$cualProyecto;
		}
		if(trim($pDepto)!="")
		{
			$sql_usu_plane=$sql_usu_plane." and Usuarios.id_departamento=".$pDepto;
		}
		if(trim($pCategoria)!="")
		{
			$sql_usu_plane=$sql_usu_plane." and Usuarios.id_categoria=".$pCategoria;
		}
		$sqls[0]=$sql_usu_plane;
//echo $sql_usu_plane." <br>";

		// (CONSULTA 2) USUARIOS, QUE FACTURARON EN LAS ACTIVIDADES DONDE SE HA DEFINIDO LA DIVISION ( O DEPENDEN DE ELLA-NIVEL 4) EN LOS PROYECTOS
		$sql_factu=" select  distinct FacturacionProyectos.unidad, Usuarios.nombre, Usuarios.apellidos,Usuarios.fechaRetiro,Usuarios.retirado  from FacturacionProyectos 
		inner join Actividades on FacturacionProyectos.id_proyecto=Actividades.id_proyecto and FacturacionProyectos.id_actividad=Actividades.id_actividad
        inner join Usuarios on FacturacionProyectos.unidad=Usuarios.unidad
        inner join Departamentos on Usuarios.id_departamento=Departamentos.id_departamento
        inner join Divisiones on Departamentos.id_division=Divisiones.id_division
        where FacturacionProyectos.id_proyecto not in (61,65,71,60,63,62,64,56) and FacturacionProyectos.vigencia=".$vigencia." and FacturacionProyectos.mes=".$pMes." and FacturacionProyectos.esInterno='I'  and Actividades.id_division=".$pDivision;
		if(trim($cualProyecto)!="")
		{
			$sql_factu=$sql_factu." and FacturacionProyectos.id_proyecto=".$cualProyecto;
		}
		if(trim($pDepto)!="")
		{
			$sql_factu=$sql_factu." and Usuarios.id_departamento=".$pDepto;
		}
		if(trim($pCategoria)!="")
		{
			$sql_factu=$sql_factu." and Usuarios.id_categoria=".$pCategoria;
		}
//echo $sql_factu." <br>";
		$sqls[1]=$sql_factu;

		// (CONSULTA 3) USUARIOS, QUE FUERON PLANEADOS EN LAS ACTIVIDADES  DONDE SE HA DEFINIDO LA DIVISION ( O DEPENDEN DE ELLA-NIVEL 4) EN LOS PROYECTOS Y TIENEN FACTURACION
		$sql_planea_fac="select  distinct PlaneacionProyectos.unidad, Usuarios.nombre, Usuarios.apellidos,Usuarios.fechaRetiro,Usuarios.retirado  from PlaneacionProyectos 
		inner join Actividades on PlaneacionProyectos.id_proyecto=Actividades.id_proyecto and PlaneacionProyectos.id_actividad=Actividades.id_actividad

		inner join FacturacionProyectos on PlaneacionProyectos.id_proyecto=FacturacionProyectos.id_proyecto and PlaneacionProyectos.id_actividad=FacturacionProyectos.id_actividad
		and PlaneacionProyectos.unidad=FacturacionProyectos.unidad and PlaneacionProyectos.vigencia=FacturacionProyectos.vigencia and PlaneacionProyectos.mes=FacturacionProyectos.mes

		and PlaneacionProyectos.esInterno=FacturacionProyectos.esInterno
		inner join Usuarios on PlaneacionProyectos.unidad=Usuarios.unidad
		inner join Departamentos on Usuarios.id_departamento=Departamentos.id_departamento
		inner join Divisiones on Departamentos.id_division=Divisiones.id_division 
		where PlaneacionProyectos.vigencia=".$vigencia." and PlaneacionProyectos.mes=".$pMes." and PlaneacionProyectos.esInterno='I'  and Actividades.id_division=".$pDivision."";
		if(trim($cualProyecto)!="")
		{
			$sql_planea_fac=$sql_planea_fac." and PlaneacionProyectos.id_proyecto=".$cualProyecto;
		}
		if(trim($pDepto)!="")
		{
			$sql_planea_fac=$sql_planea_fac." and Usuarios.id_departamento=".$pDepto;
		}
		if(trim($pCategoria)!="")
		{
			$sql_planea_fac=$sql_planea_fac." and Usuarios.id_categoria=".$pCategoria;
		}
		$sqls[2]=$sql_planea_fac;

		// (CONSULTA 4) USUARIOS, QUE GENERARON FACTURACION EN ALGUNA DE LAS ACTIVIDADES DEONDE SE HA DEFINIDO LA DIVISION (O DEPENDEN DE ELLA NIVEL 4) Y NO ESTABAN PLANEADOS
		$sql_fac_sin_plan=" select distinct FacturacionProyectos.unidad , Usuarios.nombre, Usuarios.apellidos,Usuarios.fechaRetiro,Usuarios.retirado from FacturacionProyectos 

		inner join Actividades on FacturacionProyectos.id_proyecto=Actividades.id_proyecto and FacturacionProyectos.id_actividad=Actividades.id_actividad

		inner join Usuarios on FacturacionProyectos.unidad=Usuarios.unidad
		inner join Departamentos on Usuarios.id_departamento=Departamentos.id_departamento
		inner join Divisiones on Departamentos.id_division=Divisiones.id_division
		where FacturacionProyectos.id_proyecto not in (61,65,71,60,63,62,64,56) and vigencia=".$vigencia." and mes=".$pMes." and FacturacionProyectos.esInterno='I' and Actividades.id_division=".$pDivision." and FacturacionProyectos.unidad not in (
		
			select distinct(PlaneacionProyectos.unidad) unidad from PlaneacionProyectos 

			inner join Actividades on PlaneacionProyectos.id_proyecto=Actividades.id_proyecto and PlaneacionProyectos.id_actividad=Actividades.id_actividad

			inner join Usuarios USU on PlaneacionProyectos.unidad=Usuarios.unidad
			inner join Departamentos DEP on Usuarios.id_departamento=DEP.id_departamento
			inner join Divisiones DIV on Departamentos.id_division=DIV.id_division
			where vigencia=FacturacionProyectos.vigencia and PlaneacionProyectos.mes=FacturacionProyectos.mes and PlaneacionProyectos.esInterno=FacturacionProyectos.esInterno	

			and FacturacionProyectos.id_proyecto=PlaneacionProyectos.id_proyecto and FacturacionProyectos.id_actividad=PlaneacionProyectos.id_actividad 

			and USU.id_categoria=Usuarios.id_categoria
			and DIV.id_division=Divisiones.id_division
			and Departamentos.id_departamento=DEP.id_departamento";
		$sql_fac_sin_plan=$sql_fac_sin_plan.")";
		if(trim($cualProyecto)!="")
		{
			$sql_fac_sin_plan=$sql_fac_sin_plan." and FacturacionProyectos.id_proyecto=".$cualProyecto;
		}
		if(trim($pDepto)!="")
		{
			$sql_fac_sin_plan=$sql_fac_sin_plan." and Usuarios.id_departamento=".$pDepto;
		}
		if(trim($pCategoria)!="")
		{
			$sql_fac_sin_plan=$sql_fac_sin_plan." and Usuarios.id_categoria=".$pCategoria;
		}

//echo "<br>".$sql_fac_sin_plan."---- <br>";
		$sqls[3]=$sql_fac_sin_plan;

		//(CONSULTA 5) USUARIOS, QUE FUERON PLANEADOS EN LAS ACTIVIDADES  ASOCIADAS A LA DIVISION EN LOS DIFERENTES PROYECTOS, Y NO HAN FACTURADO
		$sql_plane_no_fac="select distinct PlaneacionProyectos.unidad , Usuarios.nombre, Usuarios.apellidos,Usuarios.fechaRetiro,Usuarios.retirado from PlaneacionProyectos 
        inner join Usuarios on PlaneacionProyectos.unidad=Usuarios.unidad

		inner join Actividades on PlaneacionProyectos.id_proyecto=Actividades.id_proyecto and PlaneacionProyectos.id_actividad=Actividades.id_actividad

        inner join Departamentos on Usuarios.id_departamento=Departamentos.id_departamento
        inner join Divisiones on Departamentos.id_division=Divisiones.id_division
        where vigencia=".$vigencia."  and PlaneacionProyectos.mes=".$pMes." and PlaneacionProyectos.esInterno='I'  and Actividades.id_division=".$pDivision." and PlaneacionProyectos.unidad not in (
        
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
		if(trim($cualProyecto)!="")
		{
			$sql_plane_no_fac=$sql_plane_no_fac." and PlaneacionProyectos.id_proyecto=".$cualProyecto;
		}
		if(trim($pDepto)!="")
		{
			$sql_plane_no_fac=$sql_plane_no_fac." and Usuarios.id_departamento=".$pDepto;
		}
		if(trim($pCategoria)!="")
		{
			$sql_plane_no_fac=$sql_plane_no_fac." and Usuarios.id_categoria=".$pCategoria;
		}
		$sqls[4]=$sql_plane_no_fac;

		//USUARIOS CUYA FACTURACION, SOBREPASA LO PLANEADO
		$sql_abajo="select T2.*  from
		(
			select T1.*,
			(
				select SUM(hombresMesF) facturado  from FacturacionProyectos

				inner join Actividades on FacturacionProyectos.id_proyecto=Actividades.id_proyecto and FacturacionProyectos.id_actividad=Actividades.id_actividad

				inner join PlaneacionProyectos on PlaneacionProyectos.id_proyecto=FacturacionProyectos.id_proyecto and PlaneacionProyectos.id_actividad=FacturacionProyectos.id_actividad
				and PlaneacionProyectos.unidad=FacturacionProyectos.unidad and PlaneacionProyectos.vigencia=FacturacionProyectos.vigencia and PlaneacionProyectos.mes=FacturacionProyectos.mes
				and PlaneacionProyectos.esInterno=FacturacionProyectos.esInterno	
	
				where FacturacionProyectos.vigencia=T1.vigencia and FacturacionProyectos.mes=T1.mes
				and FacturacionProyectos.esInterno='I' and FacturacionProyectos.unidad=T1.unidad	";
				if(trim($cualProyecto)!="")
				{
					$sql_abajo=$sql_abajo." AND FacturacionProyectos.id_proyecto=T1.id_proyecto";
				}		
			$sql_abajo=$sql_abajo." and Actividades.id_division=".$pDivision." ";

			$sql_abajo=$sql_abajo."		) total_facturacion
			from (
				select SUM(hombresMes) hombresMes , PlaneacionProyectos.unidad, mes, vigencia, Usuarios.nombre, Usuarios.apellidos,Usuarios.fechaRetiro,Usuarios.retirado ";
				if(trim($cualProyecto)!="")
				{
					$sql_abajo=$sql_abajo." ,PlaneacionProyectos.id_proyecto ";
				}
				$sql_abajo=$sql_abajo." from PlaneacionProyectos		
				inner join Actividades on PlaneacionProyectos.id_proyecto=Actividades.id_proyecto and PlaneacionProyectos.id_actividad=Actividades.id_actividad
					inner join Usuarios on PlaneacionProyectos.unidad=Usuarios.unidad
					inner join Departamentos on Usuarios.id_departamento=Departamentos.id_departamento
					inner join Divisiones on Departamentos.id_division=Divisiones.id_division
				where PlaneacionProyectos.vigencia=".$vigencia." and PlaneacionProyectos.mes=".$pMes."  and PlaneacionProyectos.esInterno='I' ";
				if(trim($cualProyecto)!="")
				{
					$sql_abajo=$sql_abajo." and PlaneacionProyectos.id_proyecto=".$cualProyecto;
				}
				if(trim($pDepto)!="")
				{
					$sql_abajo=$sql_abajo." and Usuarios.id_departamento=".$pDepto;
				}
				if(trim($pCategoria)!="")
				{
					$sql_abajo=$sql_abajo." and Usuarios.id_categoria=".$pCategoria;
				}
				$sql_abajo=$sql_abajo." and Actividades.id_division=".$pDivision."
				and PlaneacionProyectos.id_actividad in( 
					select distinct(id_actividad) from FacturacionProyectos  where FacturacionProyectos.vigencia=PlaneacionProyectos.vigencia and FacturacionProyectos.mes=PlaneacionProyectos.mes  and FacturacionProyectos.esInterno='I' and FacturacionProyectos.unidad=PlaneacionProyectos.unidad and id_proyecto=PlaneacionProyectos.id_proyecto	and FacturacionProyectos.id_actividad=PlaneacionProyectos.id_actividad 
				)

				group by PlaneacionProyectos.unidad,mes, vigencia, Usuarios.nombre, Usuarios.apellidos,Usuarios.fechaRetiro,Usuarios.retirado ";
				if(trim($cualProyecto)!="")
				{
					$sql_abajo=$sql_abajo." ,PlaneacionProyectos.id_proyecto ";
				}

		$sql_abajo=$sql_abajo." ) T1	
		) T2 where hombresMes<total_facturacion ";

		$sqls[5]=$sql_abajo;
//echo $sql_abajo;
		//USUARIOS CUYA FACTURACION, ESTA POR DEBAJO DE LO PLANEADO
		$sql_abajo="select T2.*  from
		(
			select T1.*,
			(
				select SUM(hombresMesF) facturado  from FacturacionProyectos
				inner join Actividades on FacturacionProyectos.id_proyecto=Actividades.id_proyecto and FacturacionProyectos.id_actividad=Actividades.id_actividad		
				inner join PlaneacionProyectos on PlaneacionProyectos.id_proyecto=FacturacionProyectos.id_proyecto and PlaneacionProyectos.id_actividad=FacturacionProyectos.id_actividad
				and PlaneacionProyectos.unidad=FacturacionProyectos.unidad and PlaneacionProyectos.vigencia=FacturacionProyectos.vigencia and PlaneacionProyectos.mes=FacturacionProyectos.mes
				and PlaneacionProyectos.esInterno=FacturacionProyectos.esInterno	
	
				where FacturacionProyectos.vigencia=T1.vigencia and FacturacionProyectos.mes=T1.mes
				and FacturacionProyectos.esInterno='I' and FacturacionProyectos.unidad=T1.unidad	";
				$sql_abajo=$sql_abajo." and Actividades.id_division=".$pDivision." ";
				if(trim($cualProyecto)!="")
				{
					$sql_abajo=$sql_abajo." AND FacturacionProyectos.id_proyecto=T1.id_proyecto";
				}		
			$sql_abajo=$sql_abajo."		) total_facturacion
			from (
				select SUM(hombresMes) hombresMes , PlaneacionProyectos.unidad, mes, vigencia, Usuarios.nombre, Usuarios.apellidos,Usuarios.fechaRetiro,Usuarios.retirado ";
				if(trim($cualProyecto)!="")
				{
					$sql_abajo=$sql_abajo." ,PlaneacionProyectos.id_proyecto ";
				}
				$sql_abajo=$sql_abajo." from PlaneacionProyectos
				inner join Actividades on PlaneacionProyectos.id_proyecto=Actividades.id_proyecto and PlaneacionProyectos.id_actividad=Actividades.id_actividad		
					inner join Usuarios on PlaneacionProyectos.unidad=Usuarios.unidad
					inner join Departamentos on Usuarios.id_departamento=Departamentos.id_departamento
					inner join Divisiones on Departamentos.id_division=Divisiones.id_division
				where PlaneacionProyectos.vigencia=".$vigencia." and PlaneacionProyectos.mes=".$pMes."  and PlaneacionProyectos.esInterno='I' ";
				if(trim($cualProyecto)!="")
				{
					$sql_abajo=$sql_abajo." and PlaneacionProyectos.id_proyecto=".$cualProyecto;
				}
				if(trim($pDepto)!="")
				{
					$sql_abajo=$sql_abajo." and Usuarios.id_departamento=".$pDepto;
				}
				if(trim($pCategoria)!="")
				{
					$sql_abajo=$sql_abajo." and Usuarios.id_categoria=".$pCategoria;
				}
				$sql_abajo=$sql_abajo." and Actividades.id_division=".$pDivision." 
				and PlaneacionProyectos.id_actividad in( 
					select distinct(id_actividad) from FacturacionProyectos  where FacturacionProyectos.vigencia=PlaneacionProyectos.vigencia and FacturacionProyectos.mes=PlaneacionProyectos.mes  and FacturacionProyectos.esInterno='I' and FacturacionProyectos.unidad=PlaneacionProyectos.unidad and id_proyecto=PlaneacionProyectos.id_proyecto	and FacturacionProyectos.id_actividad=PlaneacionProyectos.id_actividad 
				)

				group by PlaneacionProyectos.unidad,mes, vigencia, Usuarios.nombre, Usuarios.apellidos,Usuarios.fechaRetiro,Usuarios.retirado ";
				if(trim($cualProyecto)!="")
				{
					$sql_abajo=$sql_abajo." ,PlaneacionProyectos.id_proyecto ";
				}

		$sql_abajo=$sql_abajo." ) T1	
		) T2 where hombresMes>total_facturacion ";

		$sqls[6]=$sql_abajo;
//echo $sql_abajo;
?>


<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title>.:: Planeaci&oacute;n y Facturaci&oacute;n de la divisi&oacute;n en los Proyectos ::.</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript" type="text/JavaScript">
window.name='winFacturacionHT';

function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
<script type="text/javascript"> 
	function valida()
	{
			document.form1.recarga.value=2;
			document.form1.submit();			
	}
</script>
</head>

<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<div class="TxtNota2" style="position:absolute; left:3px; top:55px; width: 848px; height: 30px;">
Reporte facturación por división </div>


<?
	$cant_acti_apro=0;
	$can_activi=0;
?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="TxtTabla">&nbsp;</td>
      </tr>
</table>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
        <td>&nbsp;</td>
  </tr>
</table>

    <table width="50%"  border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td class="TituloUsuario">Criterios de consulta</td>
      </tr>
      <tr>
        <td bgcolor="#FFFFFF"><table width="100%"  border="0" align="center" cellpadding="0" cellspacing="1">
          <form method="post" name="form1" id="form1">
            <tr>
              <td class="TituloTabla">Divisi&oacute;n</td>
              <td class="TxtTabla">
<?
			$qSql1="Select * from divisiones where estadoDiv='A' and id_division=".$pDivision ;
			$qCursor1 = mssql_query($qSql1);
			$qReg1=mssql_fetch_array($qCursor1);
			echo ucwords(strtolower($qReg1[nombre]));
?>
	</td>
            </tr>
<?

			if(trim($pCategoria)!="")
			{
?>
          <tr>
            <td class="TituloTabla">Categor&iacute;a</td>
            <td class="TxtTabla"><?
			$sql2="Select * from Categorias  where estadoCat='A' and id_categoria=".$pCategoria." " ;
			$cursor2 = mssql_query($sql2);
			$qReg1=mssql_fetch_array($cursor2);
			echo ucwords(strtolower($qReg1[nombre]));
?>			</td>
          </tr>
<?
			}
?>
            <tr>
              <td width="5%" class="TituloTabla">Mes:&nbsp;</td>
              <td width="80%" class="TxtTabla"><?
	//Seleccionar el mes cuando se carga la página por primera vez
	//si no cuando se recarga la página
/*
	if ($pMes == "") {
		$pMes=date("m"); //el mes actual
	}
	else {
		$pMes= $pMes; //el mes seleccionado
	}
*/
	$mes = array( 'Seleccione Mes', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre' );
	echo $mes[$pMes];
?>
<!--
                <select name="pMes" class="CajaTexto" id="pMes">
                  <?
/*
	$i=0;
	foreach ($mes as $val)
	{
		$sel="";
		if($pMes==$i)
			$sel=" selected ";
?>
                  <option value="<?=$i; ?>" <? echo $sel; ?> >
                    <?=$val ?>
                  </option>
                  <?

		$i++;
	}
*/
?>
                </select>
-->
			  </td>
			</tr>
			<tr>
			  <td class="TituloTabla">Vigencia:&nbsp;</td>
			  <td class="TxtTabla"><?
				echo $vigencia;
/*
	$cur_vigencia=mssql_query("select distinct(vigencia) vigencia from FacturacionProyectos where id_proyecto=".$cualProyecto." order by vigencia ");
	if ($vigencia == "") {
		$vigencia=date("Y"); //el mes actual
	}
	else {
		$vigencia= $vigencia; //el mes seleccionado
	}
*/
//echo $vigencia."dddd";
?></td>
		    </tr>
			<tr>
              <td width="15%" class="TituloTabla">Proyecto</td>
              <td class="TxtTabla">
<?
	$sql_proy="select * from Proyectos where id_proyecto=".$cualProyecto;
	$cur_proy=mssql_query($sql_proy);

	$datos_proys=mssql_fetch_array($cur_proy);
	echo "[".$datos_proys["codigo"].".".$datos_proys["cargo_defecto"]."] ".$datos_proys["nombre"];

?>
				</td>
			</tr>
<!--
			<tr>
              <td width="15%" align="center" class="TituloTabla">Actividad</td>

				<td width="15%" align="left" class="TxtTabla" >

<?
/*
				$sql_activi="select nombre,macroactividad,id_actividad";

				if(($pMes!="")&&($vigencia!=""))
				{
								$sql_activi=$sql_activi." ,(
								 select COUNT(*) from (select distinct(FacturacionProyectos.unidad) from FacturacionProyectos 
								where FacturacionProyectos.id_proyecto=".$cualProyecto."  and FacturacionProyectos.vigencia=".$vigencia." and FacturacionProyectos.mes=".$pMes." and id_actividad=Actividades.id_actividad ) aa
								) cant_pers_factu ";
				}

				$sql_activi=$sql_activi." from  Actividades where id_proyecto=".$cualProyecto." and nivel in (3,4)";
				//SI LA PERSONA QUE CONSULTA LA FACTURACION, ES DIRECTOR O CORRDINADOR, SE CONSULTA TODAS LA FACTURACION  DEL PROYECTO
				if ( ($uni_coor!=$laUnidad) && ($uni_dir!=$laUnidad) )
				{
					$sql_activi=$sql_activi."and id_encargado=".$laUnidad;
				}
				$cur_activi=mssql_query($sql_activi);
				$cant_re_activi=mssql_num_rows($cur_activi);
				$cants=1;
				if(( (int) $cant_re_activi)<10 )
					$cants=5;
				if(( (int) $cant_re_activi)>10 )
					$cants=10;
?>
					<select name="activi" class="CajaTexto" id="activi" size="<?=$cants ?>" >
	                  <option value="" <? if(trim($activi)==""){ echo "selected"; } ?>  >Todas</option>
<?
						while($datos_acti=mssql_fetch_array($cur_activi) )
						{
							$sel="";
							if($activi==$datos_acti["id_actividad"])
								$sel="selected";
?>				
    		                  <option value="<?=$datos_acti["id_actividad"]; ?>" <? echo $sel; ?> >
                            <?="[".$datos_acti["macroactividad"]."] ".$datos_acti["nombre"] ?>
<?
							if($datos_acti["cant_pers_factu"]>0)
							{
								echo "  [".$datos_acti["cant_pers_factu"]."]";
							}
?>
	                          </option>
				  <?
						}
*/
?>
			    </select></td>

			</tr>
-->
<!--
			<tr>
              <td width="15%" align="left" class="TituloTabla2" >Estado Facturaci&oacute;n</td>
              <td width="15%" align="left" class="TxtTabla" >
                <select name="estado" class="CajaTexto" id="estado">
                  <option value="3" <? // if(trim($estado)=="3") { echo "selected"; } ?>  >Todos</option>
                  <option value="1" <? //if($estado==1) { echo "selected"; } ?>  >Aprobada</option>
                  <option value="0" <? //if(trim($estado)=='0') { echo "selected"; } ?>   >No aprobada</option>
                  <option value="2" <? //if($estado==2) { echo "selected"; } ?>   >Pendiente aprobación</option>
              </select></td>
-->
<!--
              <td width="15%" align="right"  class="TituloTabla2">Codigo . Cargo</td>
              <td width="8%" class="TxtTabla"><input name="codigo" type="text" class="CajaTexto" id="codigo" value="" size="3"  />
                .
                <input name="cargo" type="text" class="CajaTexto" id="cargo" value=""  size="3" /></td>
-->
<!--
			</tr>
-->
			<tr>
              <td width="10%" colspan="2" align="right" class="TxtTabla"><input name="Submit8" type="button" class="Boton" value="Consultar" onClick="valida()" />
                <input name="recarga" id="recarga" type="hidden" value="1" /></td>
            </tr>
          </form>
        </table></td>
      </tr>
    </table>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td>&nbsp;</td>
          </tr>
<!--
          <tr>
            <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="TxtTabla"><a href="htVoBoProyectos.php" class="menu">&lt;&lt; Regresar a la Lista de Proyectos </a></td>
              </tr>
            </table></td>
          </tr>
-->
</table>
<?
//echo count($sqls)." ---- ";
	//RECORRE EL ARRAY DE CONSULTAS, Y MUESTRA LA INFORMACION EXTRAHIDA DE CADA UNA DE ELLAS 
	for($i=0; $i<count($sqls); $i++)
	{
?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TituloTabla"><?=$mensaje[$i] ?></td>
          </tr>
        </table>
<table width="100%"  border="0"  bgcolor="#FFFFFF" >
          <tr class="TituloUsuario">
            <td width="10%">Unidad</td>
            <td width="24%">Usuario</td>
            <td width="2%"></td>
            <td width="63%">Planeaci&oacute;n y facturaci&oacute;n en los proyectos </td>
            <td colspan="2" width="1%">&nbsp;</td>
          </tr>
<?
		//REALIZA LAS CONSULTAS QUE ESTAN ALMACENANDAS EN E ARRAY $sqls
		$cur_usu=mssql_query($sqls[$i]);
//echo "<br><br>".$sqls[$i]."<br>".mssql_get_last_message()." *******--------------------$i-".mssql_num_rows($cur_usu);
		if(mssql_num_rows($cur_usu)==0)
		{
?>

  <tr class="TituloTabla2">
            <td colspan="19" align="left" class="TituloTabla2">No se encontraron registros. </td>
          </tr>
  <?
		}
		else
		{
			while($datos_usu=mssql_fetch_array($cur_usu))
			{
?>
                <tr>
                    <td width="10%" class="TxtTabla"   ><?=$datos_usu["unidad"] ?></td>

                    <td width="24%" class="TxtTabla" ><?=$datos_usu["apellidos"]." ".$datos_usu["nombre"] ?></td>
                    <td width="2%" class="TxtTabla" ><?
                                if(($datos_usu["retirado"]==1) and (trim($datos_usu["fechaRetiro"])!=""))
                                {
                ?>
                      <img src="imagenes/Inactivo.gif" alt=" " title="Retirado de la compañia" />
                      <?php
                                }
                
                            ?></td>
                    <td width="63%"><table width="100%" border="0">
                      <tr class="TituloUsuario">
                        <td ></td>
                        <td width="50%" colspan="2">Planeaci&oacute;n</td>
                        <td width="50%" colspan="2">Facturaci&oacute;n</td>
	                    <td width="3%" align="center" class="TituloUsuario">&nbsp;</td>
                      </tr>
                      <tr class="TituloTabla2">
                        <td width="37%">Proyecto</td>
                        <td width="10%">Hombres/Mes</td>
                        <td width="20%">Valor Planeado</td>
                        <td width="10%">Hombres/Mes</td>
                        <td width="20%">Valor Facturado</td>
	                    <td width="3%" align="center" >&nbsp;</td>

                      </tr>
                      <?
						//SI SE ESTAN CONSULTANDO LOS USUARIOS PLANEADOS
						if($i==0)
						{
//--distinct PlaneacionProyectos.unidad, Usuarios.nombre, Usuarios.apellidos,Usuarios.fechaRetiro,Usuarios.retirado 
							//CONSULTA LOS PROYECTOS, EN LOS CUALES EL USUARIO TIENE PLANEACION
							$sql="select sum(PlaneacionProyectos.hombresMes) total_planeacion_h_mes , SUM(PlaneacionProyectos.valorPlaneado) total_valor_planeado ,Proyectos.nombre ,Proyectos.id_proyecto
							from PlaneacionProyectos 
							inner join Proyectos on PlaneacionProyectos.id_proyecto=Proyectos.id_proyecto
							inner join Actividades on PlaneacionProyectos.id_proyecto=Actividades.id_proyecto and PlaneacionProyectos.id_actividad=Actividades.id_actividad
							inner join Usuarios on PlaneacionProyectos.unidad=Usuarios.unidad inner join Departamentos on Usuarios.id_departamento=Departamentos.id_departamento 
							inner join Divisiones on Departamentos.id_division=Divisiones.id_division
							where vigencia=".$vigencia." and PlaneacionProyectos.mes=".$pMes." and PlaneacionProyectos.esInterno='I' and Actividades.id_division=".$pDivision;
							if(trim($cualProyecto)!="")
							{
								$sql=$sql." and PlaneacionProyectos.id_proyecto=".$cualProyecto;
							}
							if(trim($pDepto)!="")
							{
								$sql=$sql." and Usuarios.id_departamento=".$pDepto;
							}
					
							if(trim($pCategoria)!="")
							{
								$sql=$sql." and Usuarios.id_categoria=".$pCategoria;
							}
							$sql=$sql." and PlaneacionProyectos.unidad=".$datos_usu["unidad"]." group by Proyectos.nombre, Proyectos.id_proyecto ";
							$cur=mssql_query($sql);
//echo $sql."----".mssql_get_last_message();
						}
						//SI SE ESTAN CONSULTANDO LOS PROYECTOS EN LOS QUE EL USUARIO HA  FACTURADO
						if($i==1)
						{
							$sql="select  sum(FacturacionProyectos.hombresMesF) total_facturado_h_mes , SUM(FacturacionProyectos.valorFacturado) total_valor_facturado ,Proyectos.nombre ,Proyectos.id_proyecto
							from FacturacionProyectos 
							inner join Proyectos on FacturacionProyectos.id_proyecto=Proyectos.id_proyecto
							inner join Actividades on FacturacionProyectos.id_proyecto=Actividades.id_proyecto and FacturacionProyectos.id_actividad=Actividades.id_actividad
							inner join Usuarios on FacturacionProyectos.unidad=Usuarios.unidad
							inner join Departamentos on Usuarios.id_departamento=Departamentos.id_departamento
							inner join Divisiones on Departamentos.id_division=Divisiones.id_division 
							where vigencia=".$vigencia." and FacturacionProyectos.mes=".$pMes." and FacturacionProyectos.esInterno='I' and Actividades.id_division=".$pDivision;
							if(trim($cualProyecto)!="")
							{
								$sql=$sql." and FacturacionProyectos.id_proyecto=".$cualProyecto;
							}
							if(trim($pDepto)!="")
							{
								$sql=$sql." and Usuarios.id_departamento=".$pDepto;
							}					
							if(trim($pCategoria)!="")
							{
								$sql=$sql." and Usuarios.id_categoria=".$pCategoria;
							}
							$sql=$sql." and FacturacionProyectos.unidad=".$datos_usu["unidad"]."
							--and FacturacionProyectos.id_proyecto not in (61,65,71,60,63,62,64,56)	 
							group by Proyectos.nombre,Proyectos.id_proyecto ";
							$cur=mssql_query($sql);
//echo "------------********************** <BR>".$sql;
						}
						if($i==2)
						{
							$sql="select T1.*,
								(
									select SUM(valorFacturado) facturado  from FacturacionProyectos	

									inner join Actividades on FacturacionProyectos.id_proyecto=Actividades.id_proyecto and FacturacionProyectos.id_actividad=Actividades.id_actividad

									inner join PlaneacionProyectos on PlaneacionProyectos.id_proyecto=FacturacionProyectos.id_proyecto and PlaneacionProyectos.id_actividad=FacturacionProyectos.id_actividad and PlaneacionProyectos.unidad=FacturacionProyectos.unidad and PlaneacionProyectos.vigencia=FacturacionProyectos.vigencia and PlaneacionProyectos.mes=FacturacionProyectos.mes and PlaneacionProyectos.esInterno=FacturacionProyectos.esInterno			

									where FacturacionProyectos.vigencia=T1.vigencia and FacturacionProyectos.mes=T1.mes
									and FacturacionProyectos.esInterno='I' and FacturacionProyectos.unidad=T1.unidad
									AND FacturacionProyectos.id_proyecto=T1.id_proyecto  and Actividades.id_division=".$pDivision."
								)total_valor_facturado ,
								(
									select SUM(hombresMesF) facturado  from FacturacionProyectos	
									inner join Actividades on FacturacionProyectos.id_proyecto=Actividades.id_proyecto and FacturacionProyectos.id_actividad=Actividades.id_actividad

									inner join PlaneacionProyectos on PlaneacionProyectos.id_proyecto=FacturacionProyectos.id_proyecto and PlaneacionProyectos.id_actividad=FacturacionProyectos.id_actividad and PlaneacionProyectos.unidad=FacturacionProyectos.unidad and PlaneacionProyectos.vigencia=FacturacionProyectos.vigencia and PlaneacionProyectos.mes=FacturacionProyectos.mes and PlaneacionProyectos.esInterno=FacturacionProyectos.esInterno			

									where FacturacionProyectos.vigencia=T1.vigencia and FacturacionProyectos.mes=T1.mes
									and FacturacionProyectos.esInterno='I' and FacturacionProyectos.unidad=T1.unidad	and Actividades.id_division=".$pDivision." ";
//									if(trim($cualProyecto)!="")				{
										$sql=$sql." AND FacturacionProyectos.id_proyecto=T1.id_proyecto";
//									}		
								$sql=$sql."		) total_facturado_h_mes
								from (
									select SUM(hombresMes) total_planeacion_h_mes , PlaneacionProyectos.unidad, mes, vigencia , Proyectos.nombre , 		SUM(PlaneacionProyectos.valorPlaneado) total_valor_planeado ";
//									if(trim($cualProyecto)!="")									{
										$sql=$sql." ,PlaneacionProyectos.id_proyecto ";
//									}
									$sql=$sql." from PlaneacionProyectos		
										inner join Proyectos on PlaneacionProyectos.id_proyecto=Proyectos.id_proyecto

										inner join Actividades on PlaneacionProyectos.id_proyecto=Actividades.id_proyecto and PlaneacionProyectos.id_actividad=Actividades.id_actividad

										inner join Usuarios on PlaneacionProyectos.unidad=Usuarios.unidad
										inner join Departamentos on Usuarios.id_departamento=Departamentos.id_departamento
										inner join Divisiones on Departamentos.id_division=Divisiones.id_division
									where PlaneacionProyectos.vigencia=".$vigencia." and PlaneacionProyectos.mes=".$pMes."  and PlaneacionProyectos.esInterno='I' ";
									$sql=$sql." and PlaneacionProyectos.unidad=".$datos_usu["unidad"]." 

									and PlaneacionProyectos.id_actividad in( select distinct(id_actividad) from FacturacionProyectos 
									where FacturacionProyectos.vigencia=PlaneacionProyectos.vigencia and FacturacionProyectos.mes=PlaneacionProyectos.mes  and FacturacionProyectos.esInterno='I'
									and FacturacionProyectos.unidad=PlaneacionProyectos.unidad and id_proyecto=PlaneacionProyectos.id_proyecto
									and FacturacionProyectos.id_actividad=PlaneacionProyectos.id_actividad ) ";
									if(trim($cualProyecto)!="")	
									{
										$sql=$sql." and PlaneacionProyectos.id_proyecto=".$cualProyecto;
									}
									if(trim($pDepto)!="")
									{
										$sql=$sql." and Usuarios.id_departamento=".$pDepto;
									}
									if(trim($pCategoria)!="")
									{
										$sql=$sql." and Usuarios.id_categoria=".$pCategoria;
									}
									$sql=$sql."and Actividades.id_division=".$pDivision."
									group by PlaneacionProyectos.unidad,mes, vigencia , Proyectos.nombre";
//									if(trim($cualProyecto)!="")									{
										$sql=$sql." ,PlaneacionProyectos.id_proyecto ) T1 ";
/*
//////////////ANTERIOR
							$sql="select  sum(PlaneacionProyectos.hombresMes) total_planeacion_h_mes , SUM(PlaneacionProyectos.valorPlaneado) total_valor_planeado
							,sum(FacturacionProyectos.hombresMesF) total_facturado_h_mes , SUM(FacturacionProyectos.valorFacturado) total_valor_facturado ,Proyectos.nombre 
							from PlaneacionProyectos 
									inner join FacturacionProyectos on PlaneacionProyectos.id_proyecto=FacturacionProyectos.id_proyecto and PlaneacionProyectos.id_actividad=FacturacionProyectos.id_actividad
									and PlaneacionProyectos.unidad=FacturacionProyectos.unidad and PlaneacionProyectos.vigencia=FacturacionProyectos.vigencia and PlaneacionProyectos.mes=FacturacionProyectos.mes
									and PlaneacionProyectos.esInterno=FacturacionProyectos.esInterno
									inner join Proyectos on PlaneacionProyectos.id_proyecto=Proyectos.id_proyecto
									inner join Usuarios on PlaneacionProyectos.unidad=Usuarios.unidad
									inner join Departamentos on Usuarios.id_departamento=Departamentos.id_departamento
									inner join Divisiones on Departamentos.id_division=Divisiones.id_division 
							where PlaneacionProyectos.vigencia=".$vigencia." and PlaneacionProyectos.mes=".$pMes." and PlaneacionProyectos.esInterno='I' and Divisiones.id_division=".$pDivision;
							if(trim($cualProyecto)!="")
							{
								$sql=$sql." and PlaneacionProyectos.id_proyecto=".$cualProyecto;
							}
							if(trim($pDepto)!="")
							{
								$sql=$sql." and Usuarios.id_departamento=".$pDepto;
							}					
							if(trim($pCategoria)!="")
							{
								$sql=$sql." and Usuarios.id_categoria=".$pCategoria;
							}
							$sql=$sql." and PlaneacionProyectos.unidad=".$datos_usu["unidad"]." group by(Proyectos.nombre) ";
*/
							$cur=mssql_query($sql);
//echo $sql;
						}

						if($i==3)
						{
							$sql="select sum(FacturacionProyectos.hombresMesF) total_facturado_h_mes , SUM(FacturacionProyectos.valorFacturado) total_valor_facturado ,Proyectos.nombre 
							 from FacturacionProyectos  
							inner join Proyectos on FacturacionProyectos.id_proyecto=Proyectos.id_proyecto

							inner join Actividades on FacturacionProyectos.id_proyecto=Actividades.id_proyecto and FacturacionProyectos.id_actividad=Actividades.id_actividad

							inner join Usuarios on FacturacionProyectos.unidad=Usuarios.unidad inner join Departamentos on Usuarios.id_departamento=Departamentos.id_departamento
							inner join Divisiones on Departamentos.id_division=Divisiones.id_division 
							where FacturacionProyectos.id_proyecto not in (61,65,71,60,63,62,64,56) and vigencia=".$vigencia." and mes=".$pMes." and FacturacionProyectos.esInterno='I' 
							and Actividades.id_division=".$pDivision." and FacturacionProyectos.unidad not in ( 
								select distinct(PlaneacionProyectos.unidad) unidad from PlaneacionProyectos 
								 inner join Usuarios USU on PlaneacionProyectos.unidad=Usuarios.unidad
								 inner join Departamentos DEP on Usuarios.id_departamento=DEP.id_departamento 
								 inner join Divisiones DIV on Departamentos.id_division=DIV.id_division where vigencia=FacturacionProyectos.vigencia
								  and PlaneacionProyectos.mes=FacturacionProyectos.mes and PlaneacionProyectos.esInterno=FacturacionProyectos.esInterno 
								  and FacturacionProyectos.id_proyecto=PlaneacionProyectos.id_proyecto and FacturacionProyectos.id_actividad=PlaneacionProyectos.id_actividad
								  and USU.id_categoria=Usuarios.id_categoria and DIV.id_division=Divisiones.id_division and Departamentos.id_departamento=DEP.id_departamento
								  ) 
							 and FacturacionProyectos.unidad=".$datos_usu["unidad"]." ";
							if(trim($cualProyecto)!="")
							{
								$sql=$sql." and FacturacionProyectos.id_proyecto=".$cualProyecto;
							}
							if(trim($pDepto)!="")
							{
								$sql=$sql." and Usuarios.id_departamento=".$pDepto;

							}					
							if(trim($pCategoria)!="")
							{
								$sql=$sql." and Usuarios.id_categoria=".$pCategoria;
							}
							$sql=$sql." group by(Proyectos.nombre) ";
							$cur=mssql_query($sql);
//echo mssql_get_last_message()." <br>".$sql." ******************** ";
						}
						if($i==4)
						{
//sum(PlaneacionProyectos.hombresMes) total_planeacion_h_mes , SUM(PlaneacionProyectos.valorPlaneado) total_valor_planeado
							$sql="select  sum(PlaneacionProyectos.hombresMes) total_planeacion_h_mes , SUM(PlaneacionProyectos.valorPlaneado) total_valor_planeado,Proyectos.nombre  from PlaneacionProyectos
							inner join Proyectos on PlaneacionProyectos.id_proyecto=Proyectos.id_proyecto 

							inner join Actividades on PlaneacionProyectos.id_proyecto=Actividades.id_proyecto and PlaneacionProyectos.id_actividad=Actividades.id_actividad

							inner join Usuarios on PlaneacionProyectos.unidad=Usuarios.unidad
							inner join Departamentos on Usuarios.id_departamento=Departamentos.id_departamento
							inner join Divisiones on Departamentos.id_division=Divisiones.id_division
							where vigencia=".$vigencia."  and PlaneacionProyectos.mes=".$pMes." and PlaneacionProyectos.esInterno='I'  and Actividades.id_division=".$pDivision." and PlaneacionProyectos.unidad not in (
							
								select distinct(FacturacionProyectos.unidad) uni_facturados  from FacturacionProyectos 	
								inner join Usuarios USU on FacturacionProyectos.unidad=Usuarios.unidad
								inner join Departamentos DEP on Usuarios.id_departamento=DEP.id_departamento
								inner join Divisiones DIV on Departamentos.id_division=DIV.id_division
								where vigencia=FacturacionProyectos.vigencia and PlaneacionProyectos.mes=FacturacionProyectos.mes and PlaneacionProyectos.esInterno=FacturacionProyectos.esInterno	
					
								and FacturacionProyectos.id_proyecto=PlaneacionProyectos.id_proyecto and FacturacionProyectos.id_actividad=PlaneacionProyectos.id_actividad 
					
								and USU.id_categoria=Usuarios.id_categoria
								and DIV.id_division=Divisiones.id_division
								and Departamentos.id_departamento=DEP.id_departamento 
							)
							and PlaneacionProyectos.unidad=".$datos_usu["unidad"]." ";
							if(trim($cualProyecto)!="")
							{
								$sql=$sql." and PlaneacionProyectos.id_proyecto=".$cualProyecto;
							}
							if(trim($pDepto)!="")
							{
								$sql=$sql." and Usuarios.id_departamento=".$pDepto;
							}					
							if(trim($pCategoria)!="")
							{
								$sql=$sql." and Usuarios.id_categoria=".$pCategoria;
							}
							$sql=$sql." group by(Proyectos.nombre) ";
							$cur=mssql_query($sql);
//echo "<br>--".mssql_get_last_message();
						}
						if($i==5)
						{
							$sql="select T2.*  from
							(
								select T1.*,
								(
									select SUM(valorFacturado) facturado  from FacturacionProyectos	
										inner join Actividades on FacturacionProyectos.id_proyecto=Actividades.id_proyecto and FacturacionProyectos.id_actividad=Actividades.id_actividad	
									inner join PlaneacionProyectos on PlaneacionProyectos.id_proyecto=FacturacionProyectos.id_proyecto and PlaneacionProyectos.id_actividad=FacturacionProyectos.id_actividad and PlaneacionProyectos.unidad=FacturacionProyectos.unidad and PlaneacionProyectos.vigencia=FacturacionProyectos.vigencia and PlaneacionProyectos.mes=FacturacionProyectos.mes and PlaneacionProyectos.esInterno=FacturacionProyectos.esInterno			

									where FacturacionProyectos.vigencia=T1.vigencia and FacturacionProyectos.mes=T1.mes
									and FacturacionProyectos.esInterno='I' and FacturacionProyectos.unidad=T1.unidad
									AND FacturacionProyectos.id_proyecto=T1.id_proyecto 

									and Actividades.id_division=".$pDivision."

								)total_valor_facturado ,
								(
									select SUM(hombresMesF) facturado  from FacturacionProyectos	
										inner join Actividades on PlaneacionProyectos.id_proyecto=Actividades.id_proyecto and PlaneacionProyectos.id_actividad=Actividades.id_actividad	
									inner join PlaneacionProyectos on PlaneacionProyectos.id_proyecto=FacturacionProyectos.id_proyecto and PlaneacionProyectos.id_actividad=FacturacionProyectos.id_actividad and PlaneacionProyectos.unidad=FacturacionProyectos.unidad and PlaneacionProyectos.vigencia=FacturacionProyectos.vigencia and PlaneacionProyectos.mes=FacturacionProyectos.mes and PlaneacionProyectos.esInterno=FacturacionProyectos.esInterno			

									where FacturacionProyectos.vigencia=T1.vigencia and FacturacionProyectos.mes=T1.mes
									and FacturacionProyectos.esInterno='I' and FacturacionProyectos.unidad=T1.unidad 
									and Actividades.id_division=".$pDivision."	";
//									if(trim($cualProyecto)!="")				{
										$sql=$sql." AND FacturacionProyectos.id_proyecto=T1.id_proyecto";
//									}		
								$sql=$sql."		) total_facturado_h_mes
								from (
									select SUM(hombresMes) total_planeacion_h_mes , PlaneacionProyectos.unidad, mes, vigencia , Proyectos.nombre , 		SUM(PlaneacionProyectos.valorPlaneado) total_valor_planeado ";
//									if(trim($cualProyecto)!="")									{
										$sql=$sql." ,PlaneacionProyectos.id_proyecto ";
//									}
									$sql=$sql." from PlaneacionProyectos	
										inner join Actividades on PlaneacionProyectos.id_proyecto=Actividades.id_proyecto and PlaneacionProyectos.id_actividad=Actividades.id_actividad	
										inner join Proyectos on PlaneacionProyectos.id_proyecto=Proyectos.id_proyecto
										inner join Usuarios on PlaneacionProyectos.unidad=Usuarios.unidad
										inner join Departamentos on Usuarios.id_departamento=Departamentos.id_departamento
										inner join Divisiones on Departamentos.id_division=Divisiones.id_division
									where PlaneacionProyectos.vigencia=".$vigencia." and PlaneacionProyectos.mes=".$pMes."  and PlaneacionProyectos.esInterno='I' ";
									$sql=$sql." and PlaneacionProyectos.unidad=".$datos_usu["unidad"]." 

									and PlaneacionProyectos.id_actividad in( select distinct(id_actividad) from FacturacionProyectos 
									where FacturacionProyectos.vigencia=PlaneacionProyectos.vigencia and FacturacionProyectos.mes=PlaneacionProyectos.mes  and FacturacionProyectos.esInterno='I'
									and FacturacionProyectos.unidad=PlaneacionProyectos.unidad and id_proyecto=PlaneacionProyectos.id_proyecto
									and FacturacionProyectos.id_actividad=PlaneacionProyectos.id_actividad ) ";
									if(trim($cualProyecto)!="")	
									{
										$sql=$sql." and PlaneacionProyectos.id_proyecto=".$cualProyecto;
									}
									if(trim($pDepto)!="")
									{
										$sql=$sql." and Usuarios.id_departamento=".$pDepto;
									}
									if(trim($pCategoria)!="")
									{
										$sql=$sql." and Usuarios.id_categoria=".$pCategoria;
									}
									$sql=$sql."and Actividades.id_division=".$pDivision."
									group by PlaneacionProyectos.unidad,mes, vigencia , Proyectos.nombre";
//									if(trim($cualProyecto)!="")									{
										$sql=$sql." ,PlaneacionProyectos.id_proyecto ";
//									}
					
							$sql=$sql." ) T1	
							) T2 where total_planeacion_h_mes<total_facturado_h_mes ";
							$cur=mssql_query($sql);
//echo "<br>--".mssql_get_last_message();
						}
						if($i==6)
						{
							$sql="select T2.*  from
							(
								select T1.*,
								(
									select SUM(valorFacturado) facturado  from FacturacionProyectos	
										inner join Actividades on FacturacionProyectos.id_proyecto=Actividades.id_proyecto and FacturacionProyectos.id_actividad=Actividades.id_actividad	

									inner join PlaneacionProyectos on PlaneacionProyectos.id_proyecto=FacturacionProyectos.id_proyecto and PlaneacionProyectos.id_actividad=FacturacionProyectos.id_actividad and PlaneacionProyectos.unidad=FacturacionProyectos.unidad and PlaneacionProyectos.vigencia=FacturacionProyectos.vigencia and PlaneacionProyectos.mes=FacturacionProyectos.mes and PlaneacionProyectos.esInterno=FacturacionProyectos.esInterno			

									where FacturacionProyectos.vigencia=T1.vigencia and FacturacionProyectos.mes=T1.mes
									and FacturacionProyectos.esInterno='I' and FacturacionProyectos.unidad=T1.unidad
									AND FacturacionProyectos.id_proyecto=T1.id_proyecto 
									and Actividades.id_division=".$pDivision."
								)total_valor_facturado ,
								(
									select SUM(hombresMesF) facturado  from FacturacionProyectos	
									inner join Actividades on FacturacionProyectos.id_proyecto=Actividades.id_proyecto and FacturacionProyectos.id_actividad=Actividades.id_actividad	
									inner join PlaneacionProyectos on PlaneacionProyectos.id_proyecto=FacturacionProyectos.id_proyecto and PlaneacionProyectos.id_actividad=FacturacionProyectos.id_actividad and PlaneacionProyectos.unidad=FacturacionProyectos.unidad and PlaneacionProyectos.vigencia=FacturacionProyectos.vigencia and PlaneacionProyectos.mes=FacturacionProyectos.mes and PlaneacionProyectos.esInterno=FacturacionProyectos.esInterno			

									where FacturacionProyectos.vigencia=T1.vigencia and FacturacionProyectos.mes=T1.mes
									and FacturacionProyectos.esInterno='I' and FacturacionProyectos.unidad=T1.unidad
									and Actividades.id_division=".$pDivision."	";
//									if(trim($cualProyecto)!="")				{
										$sql=$sql." AND FacturacionProyectos.id_proyecto=T1.id_proyecto";
//									}		
								$sql=$sql."		) total_facturado_h_mes
								from (
									select SUM(hombresMes) total_planeacion_h_mes , PlaneacionProyectos.unidad, mes, vigencia , Proyectos.nombre , 		SUM(PlaneacionProyectos.valorPlaneado) total_valor_planeado ";
//									if(trim($cualProyecto)!="")									{
										$sql=$sql." ,PlaneacionProyectos.id_proyecto ";
//									}
									$sql=$sql." from PlaneacionProyectos	
										inner join Actividades on PlaneacionProyectos.id_proyecto=Actividades.id_proyecto and PlaneacionProyectos.id_actividad=Actividades.id_actividad		
										inner join Proyectos on PlaneacionProyectos.id_proyecto=Proyectos.id_proyecto
										inner join Usuarios on PlaneacionProyectos.unidad=Usuarios.unidad
										inner join Departamentos on Usuarios.id_departamento=Departamentos.id_departamento
										inner join Divisiones on Departamentos.id_division=Divisiones.id_division
									where PlaneacionProyectos.vigencia=".$vigencia." and PlaneacionProyectos.mes=".$pMes."  and PlaneacionProyectos.esInterno='I' ";
									$sql=$sql." and PlaneacionProyectos.unidad=".$datos_usu["unidad"]." 

									and PlaneacionProyectos.id_actividad in( select distinct(id_actividad) from FacturacionProyectos 
									where FacturacionProyectos.vigencia=PlaneacionProyectos.vigencia and FacturacionProyectos.mes=PlaneacionProyectos.mes  and FacturacionProyectos.esInterno='I'
									and FacturacionProyectos.unidad=PlaneacionProyectos.unidad and id_proyecto=PlaneacionProyectos.id_proyecto
									and FacturacionProyectos.id_actividad=PlaneacionProyectos.id_actividad ) ";
									if(trim($cualProyecto)!="")	
									{
										$sql=$sql." and PlaneacionProyectos.id_proyecto=".$cualProyecto;
									}
									if(trim($pDepto)!="")
									{
										$sql=$sql." and Usuarios.id_departamento=".$pDepto;
									}
									if(trim($pCategoria)!="")
									{
										$sql=$sql." and Usuarios.id_categoria=".$pCategoria;
									}
									$sql=$sql." and Actividades.id_division=".$pDivision."
									group by PlaneacionProyectos.unidad,mes, vigencia , Proyectos.nombre";
//									if(trim($cualProyecto)!="")									{
										$sql=$sql." ,PlaneacionProyectos.id_proyecto ";
//									}
					
							$sql=$sql." ) T1	
							) T2 where total_planeacion_h_mes>total_facturado_h_mes ";
							$cur=mssql_query($sql);
//echo "<br>-***********************************-".mssql_get_last_message()."<br>".$sql." --- <br>".$i;
						}
//echo $sql;
						while($datos_fac=mssql_fetch_array($cur))
						{               
							if($i==0)
							{


								$sql_fact="select FacturacionProyectos.unidad, SUM(hombresMesF) total_facturado_h_mes , SUM(valorFacturado) total_valor_facturado from FacturacionProyectos 
								inner join Actividades on FacturacionProyectos.id_proyecto=Actividades.id_proyecto and FacturacionProyectos.id_actividad=Actividades.id_actividad		

								inner join Usuarios on FacturacionProyectos.unidad=Usuarios.unidad 
								inner join Departamentos on Usuarios.id_departamento=Departamentos.id_departamento 
								inner join Divisiones on Departamentos.id_division=Divisiones.id_division
								where vigencia=".$vigencia." and mes=".$pMes." and Actividades.id_division=".$pDivision." AND FacturacionProyectos.id_proyecto=".$datos_fac["id_proyecto"]."
								and FacturacionProyectos.unidad=".$datos_usu["unidad"] ."  
								group by FacturacionProyectos.unidad";
								$datos_facts=mssql_fetch_array(mssql_query($sql_fact));
//echo mssql_get_last_message()." --- ".$sql_fact;
								$datos_fac["total_facturado_h_mes"]=$datos_facts["total_facturado_h_mes"];
								$datos_fac["total_valor_facturado"]=$datos_facts["total_valor_facturado"];
								
							}
							if($i==1)
							{
								$sql_fact="select PlaneacionProyectos.unidad, SUM(hombresMes) total_facturado_h_mes , SUM(valorPlaneado)total_valor_facturado from PlaneacionProyectos 
								inner join Actividades on PlaneacionProyectos.id_proyecto=Actividades.id_proyecto and PlaneacionProyectos.id_actividad=Actividades.id_actividad		
								inner join Usuarios on PlaneacionProyectos.unidad=Usuarios.unidad 
								inner join Departamentos on Usuarios.id_departamento=Departamentos.id_departamento 
								inner join Divisiones on Departamentos.id_division=Divisiones.id_division
								where vigencia=".$vigencia." and mes=".$pMes." and Actividades.id_division=".$pDivision." AND PlaneacionProyectos.id_proyecto=".$datos_fac["id_proyecto"]."
								and PlaneacionProyectos.unidad=".$datos_usu["unidad"] ."  
								group by PlaneacionProyectos.unidad";
								$datos_facts=mssql_fetch_array(mssql_query($sql_fact));
//echo mssql_get_last_message()." --- ".$sql_fact;
								$datos_fac["total_planeacion_h_mes"]=$datos_facts["total_facturado_h_mes"];
								$datos_fac["total_valor_planeado"]=$datos_facts["total_valor_facturado"];
							}

							$ico="";
							if( ( trim($datos_fac["total_planeacion_h_mes"])!="") && (trim($datos_fac["total_facturado_h_mes"])!="" ) )
							{
								//SI LA FACTURACION EXCEDE LA PLANEACION
								if( ((float) $datos_fac["total_planeacion_h_mes"]) < ( (float) $datos_fac["total_facturado_h_mes"] ) )
								{
									$ico="img/arrAlta.gif";
									$mensa="Facturación que excede la planeación";
								}
								//SI LA PLANEACION EXCEDE LA FACTURACION
								if( ((float) $datos_fac["total_planeacion_h_mes"]) > ( (float) $datos_fac["total_facturado_h_mes"] ) )
								{
									$ico="img/arrBaja.gif";
									$mensa="Facturación inferior a la planeación";
								}
								//SI LA FACTURACION ES IGUAL A LA PLANEACION
								if( ((float) $datos_fac["total_planeacion_h_mes"]) == ( (float) $datos_fac["total_facturado_h_mes"] ) )
								{
									$ico="img/images/Si.gif";	
									$mensa="Facturación equivalente a la planeación";
								}
							}
                ?>
                      <tr >
                        <td class="TxtTabla"><?=$datos_fac["nombre"] ?></td>
                        <td width="10%" class="TxtTabla"><?=$datos_fac["total_planeacion_h_mes"] ?></td>
                        <td class="TxtTabla"><?
							if(trim($datos_fac["total_valor_planeado"])!="")
								echo "$ " . number_format($datos_fac["total_valor_planeado"], 2, ",", ".")." " ;
					 ?></td>
                        <td class="TxtTabla"><?=$datos_fac["total_facturado_h_mes"] ?></td>
                        <td class="TxtTabla"><?
							if(trim($datos_fac["total_valor_facturado"])!="")
								echo "$ ".number_format($datos_fac["total_valor_facturado"], 2, ",", ".")." ";
					  ?></td>
	                    <td width="3%" align="center" class="TxtTabla">
<?
							if($ico!="")
								echo "<img src='".$ico."' title='".$mensa."' >";
?>
						</td>

                        <?
/*                
                        //CONSULTA LOS VoBo DE LA FACTURACION
                        $sql_voFac="select  DAY(fechaAprEnc) dia , MONTH(fechaAprEnc) mes,YEAR(fechaAprEnc) ano,comentaEncargado,validaEncargado,  Usuarios.nombre, Usuarios.apellidos  from VoBoFactuacionProyHT 
                            INNER JOIN Usuarios on unidadEncargado=Usuarios.unidad
                        where VoBoFactuacionProyHT.unidad=".$datos_usu["unidad"]."  and VoBoFactuacionProyHT.vigencia=".$vigencia." and VoBoFactuacionProyHT.mes=".$pMes." 
                        and VoBoFactuacionProyHT.id_proyecto=".$cualProyecto."  and VoBoFactuacionProyHT.esInterno='I' and VoBoFactuacionProyHT.id_actividad=".$datos_fac["id_actividad"];
                        $cur_vofac=mssql_query($sql_voFac);
                        $datos_vofac=mssql_fetch_array($cur_vofac);
*/                
                ?>
                      </tr>
                      <?
                // rowspan="<?=$cant_reg_fact 
                            }
                ?>
                    </table></td>

                    <td class="TxtTabla" width="1%"><img src="img/images/ver.gif" width="16" height="16"  onclick="MM_openBrWindow('htFacturacion_via_fac.php?pMes=<?=$pMes ?>&amp;pAno=<?=$vigencia ?>&amp;unidad_u=<?=$datos_usu["unidad"] ?>','winAddFV','scrollbars=yes,resizable=yes,width=1400,height=400')" /></td>
                  </tr>

<?
			}//del while

		}// del else
?>
		  <tr>
			<td colspan="12" align="right" class="TxtTabla">&nbsp;</td>
		  </tr>
<?
	}//del for
?>
        </table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="right">&nbsp;</td>
  </tr>
<?


		//SI LA CANTIDAD DE USUARIOS CONSULTADOS, ES SUPERIOR A CERO
		if( ( (int) mssql_num_rows($cur_usuarios))>0 )
		{
?>
  <tr>
    <td align="right">&nbsp;</td>
  </tr>
<?
		
	}
?>
  <tr>
    <td align="right">&nbsp;</td>
  </tr>
</table>
</body>
</html>