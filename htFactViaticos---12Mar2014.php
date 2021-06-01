

<?
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

//$cualProyecto =683;
 include("bannerArriba.php") ; 

$sql="SELECT P.*, D.nombre nombreD, D.apellidos apellidosD, C.nombre nombreC, C.apellidos apellidosC  ";
$sql=$sql." FROM proyectos P, Usuarios D, Usuarios C " ;
$sql=$sql." WHERE P.id_director *= D.unidad " ;
$sql=$sql." AND P.id_coordinador *= C.unidad " ;
$sql=$sql." AND P.id_proyecto = " . $cualProyecto ;
$cursor = mssql_query($sql);

?>


<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<title>.:: Planeaci&oacute;n de Proyectos ::.</title>
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

		var mensaje="";
		if(document.form1.pMes.value=="0")
		{
			mensaje="Seleccione un mes \n"
		}
		if(document.form1.vigencia.value=="")
		{
			mensaje=mensaje+"Seleccione una vigencia "
		}
		
		if(mensaje!="")	
			alert(mensaje);
		else
		{
			document.form1.recarga.value=2;
			document.form1.submit();			
		}
	}
</script>
</head>

<body class="TxtTabla">


<br>
<div class="TxtNota2" style="position:absolute; left:3px; top:55px; width: 448px; height: 30px;">
<br>
Planeaci&oacute;n de proyectos </div>


<?
	$cant_acti_apro=0;
	$can_activi=0;
?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="TxtTabla">&nbsp;</td>
      </tr>
    </table>
<table width="100%"  bgcolor="#FFFFFF" >
	  <tr class="TituloTabla2">
	    <td>Proyecto</td>
	    <td width="20%">C&oacute;digo</td>
	    <td width="20%">Encargados</td>
	    <td width="20%">Programadores</td>
      </tr>
	  <?
	  while ($reg=mssql_fetch_array($cursor)) {
		$uni_coor=$reg["id_coordinador"];
		$uni_dir=$reg["id_director"];

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
          </table></td>
	    <td width="20%"><? 
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
		?></td>
	    <td width="20%" align="right"><?
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
          </table></td>
      </tr>
	  <? }

		if($recarga==2)
		{




			//SI LA PERSONA QUE CONSULTA LA FACTURACION, ES DIRECTOR O CORRDINADOR, SE CONSULTA TODAS LA FACTURACION  DEL PROYECTO
			if ( ($uni_coor==$laUnidad) || ($uni_dir==$laUnidad) )
			{
				//CONSULTA LOS USUARIOS, QUE HA REGISTRADO FACTURACION EN EL PROYECTO
				$sql_usuarios="select distinct(FacturacionProyectos.unidad), upper (Usuarios.nombre +' '+Usuarios.apellidos) nombre, Usuarios.fechaRetiro,retirado from FacturacionProyectos 
								inner join Usuarios on FacturacionProyectos.unidad=Usuarios.unidad
								where FacturacionProyectos.id_proyecto=".$cualProyecto." and vigencia=".$vigencia." and esInterno='I' and mes=".$pMes;
			}
			else
			{
				//SI NO ES EL DIRECTOR O COORDINA, SE CONSULTA LAS PERSONAS, QUE REGISTRARON FACTURACION, EN LAS ACTIVIDADES, EN LAS QUE LA PERSONA ES RESPONSABLE

				//CONSULTA LOS USUARIOS, QUE HA REGISTRADO FACTURACION EN EL PROYECTO
				$sql_usuarios="select distinct(FacturacionProyectos.unidad), upper (Usuarios.nombre +' '+Usuarios.apellidos) nombre, Usuarios.fechaRetiro,retirado from FacturacionProyectos 
								inner join Usuarios on FacturacionProyectos.unidad=Usuarios.unidad
								inner join Actividades on Actividades.id_actividad=FacturacionProyectos.id_actividad and Actividades.id_proyecto=FacturacionProyectos.id_proyecto
								where FacturacionProyectos.id_proyecto=".$cualProyecto." and vigencia=".$vigencia." and esInterno='I' and mes=".$pMes." and Actividades.id_encargado=".$laUnidad;
			}

			//si se consulta por actividad
			if(trim($activi)!="")
			{
				$sql_usuarios=$sql_usuarios." and FacturacionProyectos.id_actividad=".$activi;
			}

			//si estado == "", es por que se estan consultando todos
			if(trim($estado)!="")
			{
			//si estado == 1, es por que se estan consultando la facturqacion que esta aprobada 
			//si estado == 0, es por que se estan consultando la facturqacion que no esta aprobada 
				if(($estado==1) || ($estado==0))
				{
					$sql_usuarios=$sql_usuarios."and FacturacionProyectos.id_actividad   in (								
					select id_actividad from VoBoFactuacionProyHT where VoBoFactuacionProyHT.id_proyecto=".$cualProyecto." and vigencia=".$vigencia." and esInterno='I' and mes=".$pMes."  
					and FacturacionProyectos.id_actividad=VoBoFactuacionProyHT.id_actividad and FacturacionProyectos.unidad=VoBoFactuacionProyHT.unidad
					and validaEncargado=".$estado.")";
				}												
			}
			//si estado == 2, es por que se estan consultando la facturqacion que no esta pendiente por aprobar
			if($estado==2)
			{
					$sql_usuarios=$sql_usuarios."and FacturacionProyectos.id_actividad  not  in (								
					select id_actividad from VoBoFactuacionProyHT where VoBoFactuacionProyHT.id_proyecto=".$cualProyecto." and vigencia=".$vigencia." and esInterno='I' and mes=".$pMes." 
					and FacturacionProyectos.id_actividad=VoBoFactuacionProyHT.id_actividad and FacturacionProyectos.unidad=VoBoFactuacionProyHT.unidad )";				
			}
//echo "SQL Usuarios <br>".$sql_usuarios."<BR><br>";
			$ban_consul=1;
		}
	 ?>
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
              <td width="5%" align="center" class="TituloTabla">Mes:&nbsp;</td>
              <td width="80%" class="TxtTabla"><?
	//Seleccionar el mes cuando se carga la página por primera vez
	//si no cuando se recarga la página
	if ($pMes == "") {
		$pMes=date("m"); //el mes actual
	}
	else {
		$pMes= $pMes; //el mes seleccionado
	}

	$mes = array( 'Seleccione Mes', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre' );
?>
                <select name="pMes" class="CajaTexto" id="pMes">
                  <?
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
?>
                </select></td>
			</tr>
			<tr>
              <td width="15%" align="center" class="TituloTabla">Vigencia:&nbsp;</td>
              <td class="TxtTabla"><?
	$cur_vigencia=mssql_query("select distinct(vigencia) vigencia from FacturacionProyectos where id_proyecto=".$cualProyecto." order by vigencia ");
	if ($vigencia == "") {
		$vigencia=date("Y"); //el mes actual
	}
	else {
		$vigencia= $vigencia; //el mes seleccionado
	}
//echo $vigencia."dddd";
?>
                <select name="vigencia" class="CajaTexto" id="vigencia">
                  <option value="" >Seleccione vigencia</option>
                  <?
		while($datos_vigen=mssql_fetch_array($cur_vigencia))
		{

				$sel="";
				if($vigencia==$datos_vigen["vigencia"])
					$sel=" selected ";
?>
                  <option value="<?=$datos_vigen["vigencia"] ?>"  <? echo $sel; ?> >
                    <?=$datos_vigen["vigencia"] ?>
                  </option>
                  <?
		}
?>
                </select></td>
			</tr>
			<tr>
              <td width="15%" align="center" class="TituloTabla">Actividad</td>

				<td width="15%" align="left" class="TxtTabla" >

<?

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
?>
			    </select></td>

			</tr>
			<tr>
              <td width="15%" align="left" class="TituloTabla2" >Estado Facturaci&oacute;n</td>
              <td width="15%" align="left" class="TxtTabla" >
                <select name="estado" class="CajaTexto" id="estado">
                  <option value="3" <? if(trim($estado)=="3") { echo "selected"; } ?>  >Todos</option>
                  <option value="1" <? if($estado==1) { echo "selected"; } ?>  >Aprobada</option>
                  <option value="0" <? if(trim($estado)=='0') { echo "selected"; } ?>   >No aprobada</option>
                  <option value="2" <? if($estado==2) { echo "selected"; } ?>   >Pendiente aprobación</option>
              </select></td>
<!--
              <td width="15%" align="right"  class="TituloTabla2">Codigo . Cargo</td>
              <td width="8%" class="TxtTabla"><input name="codigo" type="text" class="CajaTexto" id="codigo" value="" size="3"  />
                .
                <input name="cargo" type="text" class="CajaTexto" id="cargo" value=""  size="3" /></td>
-->
			</tr>
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
      <tr>
        <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla"><a href="htVoBoProyectos.php" class="menu">&lt;&lt; Regresar a la Lista de Proyectos </a></td>
          </tr>
        </table></td>
      </tr>
    </table>

<table width="100%"  border="0"  bgcolor="#FFFFFF" >

  <tr class="TituloUsuario">
    <td>Unidad</td>
    <td>Usuario</td>
    <td width="1%"></td>
    <td>Facturaci&oacute;n de los proyectos </td>
    <td colspan="2">&nbsp;</td>
  </tr>
<?

	$cur_usuarios=mssql_query($sql_usuarios);

	//si s4 ha consultado la facturacion, y no sse han encontrado registros
	if(($ban_consul==1)and(mssql_num_rows($cur_usuarios)==0))
	{
?>
  	            <tr class="TituloTabla2">
                        <td colspan="19" align="left" class="TxtTabla">&nbsp;</td>
  </tr>
  	            <tr class="TituloTabla2">

                        <td colspan="19" align="left" class="TituloTabla2">No se encontraron registros. </td>
              </tr>
<?
	}
	unset($ban_consul);


	while($datos_usu=mssql_fetch_array($cur_usuarios))
	{
			$cant_aprobaciones=0; //CONTADOR, QUE PERMITE SABER, CUANTAS ACTIVIDADES, TIENEN VoBo

			//CONSULTA EL TOTAL FACTURADO, Y LAS ACTIVIDADES ASOCIADAS, AL USUARIO QUE FACTURO
			$sql_fac="
				select distinct(FacturacionProyectos.id_actividad) id_actividad, SUM(horasMesF) total_fact, UPPER( Actividades.nombre) nombre ,Actividades.macroactividad from FacturacionProyectos 
				inner join Actividades on Actividades.id_actividad=FacturacionProyectos.id_actividad and Actividades.id_proyecto=FacturacionProyectos.id_proyecto ";

				//SI LA PERSONA QUE CONSULTA LA FACTURACION, NO ES EL DIRECTOR O CORRDINADOR, SE CONSULTA LAS ACTIVIDADES ENLA QUE SE ENCUENTRA COMO RESPONSABLE
				if ( ($uni_coor!=$laUnidad) && ($uni_dir!=$laUnidad) )
				{
					$sql_fac=$sql_fac." and Actividades.id_encargado=".$laUnidad;
				}

			$sql_fac=$sql_fac." 
				where FacturacionProyectos.unidad=".$datos_usu["unidad"]." and FacturacionProyectos.vigencia=".$vigencia." and FacturacionProyectos.mes=".$pMes." 
				and FacturacionProyectos.id_proyecto=".$cualProyecto." and FacturacionProyectos.esInterno='I' ";
				if(trim($activi)!="")
				{
					$sql_fac=$sql_fac." and FacturacionProyectos.id_actividad=".$activi;
				}


			//si estado == "", es por que se estan consultando todos
			if(trim($estado)!="")
			{
			//si estado == 1, es por que se estan consultando la facturqacion que esta aprobada 
			//si estado == 0, es por que se estan consultando la facturqacion que no esta aprobada 
				if(($estado==1) || ($estado==0))
				{
					$sql_fac=$sql_fac."and FacturacionProyectos.id_actividad   in (								
					select id_actividad from VoBoFactuacionProyHT where VoBoFactuacionProyHT.id_proyecto=".$cualProyecto." and vigencia=".$vigencia." and esInterno='I' and mes=".$pMes."  
					and FacturacionProyectos.id_actividad=VoBoFactuacionProyHT.id_actividad and FacturacionProyectos.unidad=VoBoFactuacionProyHT.unidad
					and validaEncargado=".$estado.")";
				}												
			}
			//si estado == 2, es por que se estan consultando la facturqacion que no esta pendiente por aprobar
			if($estado==2)
			{
					$sql_fac=$sql_fac."and FacturacionProyectos.id_actividad  not  in (								
					select id_actividad from VoBoFactuacionProyHT where VoBoFactuacionProyHT.id_proyecto=".$cualProyecto." and vigencia=".$vigencia." and esInterno='I' and mes=".$pMes." 
					and FacturacionProyectos.id_actividad=VoBoFactuacionProyHT.id_actividad and FacturacionProyectos.unidad=VoBoFactuacionProyHT.unidad )";				
			}

				$sql_fac=$sql_fac." group by FacturacionProyectos.id_actividad,Actividades.nombre,Actividades.macroactividad order by (macroactividad)";
			$cur_fac=mssql_query($sql_fac);
//echo $sql_fac." <br>*******************************	".$activi." <br>";
			$cant_reg_fact=(mssql_num_rows($cur_fac));
?>
  <tr>
    <td class="TxtTabla"   ><?=$datos_usu["unidad"] ?></td>
    <td class="TxtTabla" ><?=$datos_usu["nombre"] ?></td>
    <td width="1%" class="TxtTabla" >
<?
				if(($reg04["retirado"]==1) and (trim($reg04["fechaRetiro"])!=""))
				{
?>
				   <img src="imagenes/Inactivo.gif" alt=" " title="Retirado de la compa&ntilde;ia" />
<?php
				}

			?>
	</td>
    <td><table width="100%" border="0">
      <tr class="TituloUsuario">
        <td width="50%" colspan="6">Facturacion </td>

        <td width="50%">Viaticos</td>
      </tr>

      <tr class="TituloTabla2">
        <td>Actividad</td>
        <td width="2%">&nbsp;</td>
        <td width="10%">Horas Planeadas</td>
        <td width="5%">Horas Facturadas</td>

        <td width="10%">Aprobado</td>
        <td>Comentarios</td>

        <td><table width="100%" border="0" bgcolor="#FFFFFF" >
        </table></td>
      </tr>
<?

			while($datos_fac=mssql_fetch_array($cur_fac))
			{
			


?>
      <tr >
        <td width="10%" class="TxtTabla"><?="[".$datos_fac["macroactividad"]."] ".$datos_fac["nombre"] ?></td>
<?
			//CONSULTA LA PLANEACION DEL USUARIO EN LA ACTIVIDAD
			$sql_planea="select SUM(horasmes) total_planea from PlaneacionProyectos
			where PlaneacionProyectos.unidad=".$datos_usu["unidad"]." and PlaneacionProyectos.vigencia=".$vigencia." and PlaneacionProyectos.mes=".$pMes."
			and PlaneacionProyectos.id_proyecto=".$cualProyecto." and PlaneacionProyectos.esInterno='I' and id_actividad=".$datos_fac["id_actividad"];
			$cur_planea=mssql_query($sql_planea);
			$datos_planea=mssql_fetch_array($cur_planea);

?>
        <td class="TxtTabla">
	<?
                if($datos_planea["total_planea"]=="")
                {
    ?>
    
                    <img src="img/images/alertaRojo.gif" width="15" height="16" title="Facturacion sin planeación" >
    <?
                }

				else
				{
?>
            <img src="imagenes/alertaInvisi.gif" width="15" height="16" >
<?
				}

    ?>

        </td>

        <td class="TxtTabla"><?=$datos_planea["total_planea"] ?></td>
        <td class="TxtTabla"><?=$datos_fac["total_fact"] ?></td>

<?

		//CONSULTA LOS VoBo DE LA FACTURACION
		$sql_voFac="select  DAY(fechaAprEnc) dia , MONTH(fechaAprEnc) mes,YEAR(fechaAprEnc) ano,comentaEncargado,validaEncargado,  Usuarios.nombre, Usuarios.apellidos  from VoBoFactuacionProyHT 
			INNER JOIN Usuarios on unidadEncargado=Usuarios.unidad
		where VoBoFactuacionProyHT.unidad=".$datos_usu["unidad"]."  and VoBoFactuacionProyHT.vigencia=".$vigencia." and VoBoFactuacionProyHT.mes=".$pMes." 
		and VoBoFactuacionProyHT.id_proyecto=".$cualProyecto."  and VoBoFactuacionProyHT.esInterno='I' and VoBoFactuacionProyHT.id_actividad=".$datos_fac["id_actividad"];
		$cur_vofac=mssql_query($sql_voFac);
		$datos_vofac=mssql_fetch_array($cur_vofac);

?>
        <td align="center" class="TxtTabla">

<?
			//FACTURACION APROBADA
			if($datos_vofac["validaEncargado"]==1)
			{
?>			
				<img src="img/images/Aprobado.gif" width="21" height="24" title="Aprobado" /><br />
<?
				echo $datos_vofac["nombre"]." ".$datos_vofac["apellidos"]."<br>";
				echo $mes[$datos_vofac["mes"]]." ".$datos_vofac["dia"]." ".$datos_vofac["ano"];
				$cant_aprobaciones++;
			}
?>			
<?
			//SIN INFO DE APROBACION
			if( trim( $datos_vofac["validaEncargado"])=="")
			{
			}
			else
			{
				//FACTURACION NO APROBADA
				if( trim( (int)  $datos_vofac["validaEncargado"])==0)
				{
	?>
					<img src="imagenes/Inactivo.gif" alt=" " title="No aprobado" /><br />
	<?
					echo $datos_vofac["nombre"]." ".$datos_vofac["apellidos"]."<br>";
					echo $mes[$datos_vofac["mes"]]." ".$datos_vofac["dia"]." ".$datos_vofac["ano"];
					$cant_aprobaciones++;
				}
			}
?>			
		</td>

        <td width="15%" class="TxtTabla"><?=$datos_vofac["comentaEncargado"] ?></td>



        <td class="TxtTabla">
<?
				$sql_viaticos="
					select ViaticosProyectosHT.id_actividad, NomTipoViatico, NomSitio, year(FechaIni) ano_i, MONTH(FechaIni) mes_i, DAY(FechaIni) dia_i, YEAR(FechaFin) ano_f, MONTH(FechaFin) mes_f, DAY(FechaFin) dia_f
					,viaticoCompleto, ViaticosProyectosHT.IDhorario,VoBoViaticosProyHT.unidadEncargado,VoBoViaticosProyHT.validaEncargado,VoBoViaticosProyHT.comentaEncargado ,ViaticosProyectosHT.*
, year(fechaAprueba) ano_a, MONTH(fechaAprueba) mes_a, DAY(fechaAprueba) dia_a, Usuarios.nombre, Usuarios.apellidos
 from ViaticosProyectosHT
					inner join TiposViaticoProy on ViaticosProyectosHT.id_proyecto=TiposViaticoProy.id_proyecto and ViaticosProyectosHT.IDTipoViatico=TiposViaticoProy.IDTipoViatico
					inner join TiposViatico on TiposViaticoProy.IDTipoViatico=TiposViatico.IDTipoViatico
					inner join SitiosTrabajo on  ViaticosProyectosHT.IDsitio=SitiosTrabajo.IDsitio and ViaticosProyectosHT.id_proyecto=SitiosTrabajo.id_proyecto


left join VoBoViaticosProyHT on ViaticosProyectosHT.id_proyecto=VoBoViaticosProyHT.id_proyecto and ViaticosProyectosHT.unidad=VoBoViaticosProyHT.unidad
 and ViaticosProyectosHT.vigencia=VoBoViaticosProyHT.vigencia and ViaticosProyectosHT.mes=VoBoViaticosProyHT.mes and ViaticosProyectosHT.esInterno=VoBoViaticosProyHT.esInterno
AND ViaticosProyectosHT.id_actividad=VoBoViaticosProyHT.id_actividad AND ViaticosProyectosHT.id_actividad=VoBoViaticosProyHT.id_actividad
AND ViaticosProyectosHT.IDhorario =VoBoViaticosProyHT.IDhorario
AND ViaticosProyectosHT.clase_tiempo=VoBoViaticosProyHT.clase_tiempo
AND ViaticosProyectosHT.localizacion=VoBoViaticosProyHT.localizacion
AND ViaticosProyectosHT.cargo=VoBoViaticosProyHT.cargo
AND ViaticosProyectosHT.IDsitio=VoBoViaticosProyHT.IDsitio
AND ViaticosProyectosHT.IDTipoViatico=VoBoViaticosProyHT.IDTipoViatico
left JOIN Usuarios on VoBoViaticosProyHT.unidadEncargado=Usuarios.unidad
					where ViaticosProyectosHT.id_proyecto=".$cualProyecto." and ViaticosProyectosHT.unidad=".$datos_usu["unidad"]."  and ViaticosProyectosHT.vigencia=".$vigencia." and ViaticosProyectosHT.mes=".$pMes."  and ViaticosProyectosHT.esInterno='I' 
					AND ViaticosProyectosHT.id_actividad=".$datos_fac["id_actividad"]." order by ViaticosProyectosHT.id_proyecto,ViaticosProyectosHT.id_actividad,ViaticosProyectosHT.unidad,ViaticosProyectosHT.vigencia,ViaticosProyectosHT.mes,ViaticosProyectosHT.esInterno,ViaticosProyectosHT.IDhorario,ViaticosProyectosHT.clase_tiempo,ViaticosProyectosHT.localizacion,ViaticosProyectosHT.cargo,ViaticosProyectosHT.IDsitio,ViaticosProyectosHT.IDTipoViatico, ViaticosProyectosHT.FechaIni,ViaticosProyectosHT.FechaFin  ";
//echo "**********".$sql_viaticos."<br><br>";
					$cur_viati=mssql_query($sql_viaticos);
			if(mssql_num_rows($cur_viati)>0)
			{
?>
			<table width="100%" border="0" bgcolor="#FFFFFF">
            <tr class="TituloTabla2">
              <td width="15%">Tipo de vi&aacute;tico </td>
              <td width="15%">Sitio</td>
              <td width="15%">Fecha Inicio </td>
              <td width="15%">Fecha finalizaci&oacute;n</td>
              <td width="5%">Dia de regreso</td>
              <td width="15%">Quien aprueba</td>

              <td width="15%"  class="TituloTabla2">Comentarios</td>
            </tr>
<?

					$con_via=1; //CONTADOR, QUE PERMITE DETERMINAR, SI LA CANTIDAD DE FILAS ASOCIADAS A LOS VIATICOS, QUE TIENEN LAS MIMAS CARACTERISTICAS, HA SIDO IMPRESA

					while($datos_viaticos=mssql_fetch_array($cur_viati))
					{
?>
                        <tr class="TxtTabla">

                          <td width="15%"><?=$datos_viaticos["NomTipoViatico"] ?></td>
                          <td width="15%"><?=$datos_viaticos["NomSitio"] ?></td>
                          <td width="15%"><?=$mes[$datos_viaticos["mes_i"]]." ".$datos_viaticos["dia_i"]." ".$datos_viaticos["ano_i"] ?></td>
                          <td width="15%"><?=$mes[$datos_viaticos["mes_f"]]." ".$datos_viaticos["dia_f"]." ".$datos_viaticos["ano_f"] ?></td>
                          <td width="15%"><? if($datos_viaticos["viaticoCompleto"]=="1") { echo "Si"; } if($datos_viaticos["viaticoCompleto"]=="2") { echo "No"; } ?></td>
<?
							if($con_via==1)
							{

								//CONSULTA LA CANTIDAD DE REGISTROS DE VIATICOS, QUE HAY CON LAS MISMAS CARACTERISTICAS, CON EL FIN DE PODER DEFINIR EL VALOR DEL
								//ROWSPAN ASOCIADO A LAS COLUMNAS DE APROVAR Y COMENTARIOS
								$dato_cant_via=mssql_fetch_array(mssql_query("SELECT COUNT(*) cant_reg FROM ViaticosProyectosHT WHERE ViaticosProyectosHT.id_proyecto=".$cualProyecto."  AND ViaticosProyectosHT.id_actividad=".$datos_fac["id_actividad"]."  and ViaticosProyectosHT.unidad=".$datos_usu["unidad"]." 
and ViaticosProyectosHT.vigencia=".$vigencia." and ViaticosProyectosHT.mes=".$pMes." and ViaticosProyectosHT.esInterno='I' and ViaticosProyectosHT.IDhorario=".$datos_viaticos["IDhorario"]."
and ViaticosProyectosHT.clase_tiempo=".$datos_viaticos["clase_tiempo"]." and ViaticosProyectosHT.localizacion=".$datos_viaticos["localizacion"]." and ViaticosProyectosHT.cargo=".$datos_viaticos["cargo"]." and ViaticosProyectosHT.IDsitio=".$datos_viaticos["IDsitio"]." and ViaticosProyectosHT.IDTipoViatico=".$datos_viaticos["IDTipoViatico"]." "));
								$cant_reg_via= $dato_cant_via["cant_reg"];
?>

                          <td width="15%" align="center"  rowspan="<?=$cant_reg_via ?>">
								<?
                                            //VIATICOS APROBADOS
                                            if($datos_viaticos["validaEncargado"]==1)
                                            {
                                ?>			
                                                <img src="img/images/Aprobado.gif" width="21" height="24" title="Aprobado" /><br />
                                <?
				                                echo $datos_viaticos["nombre"]." ".$datos_viaticos["apellidos"]."<br>";
                                                echo $mes[$datos_viaticos["mes_a"]]." ".$datos_viaticos["dia_a"]." ".$datos_viaticos["ano_a"];
                                            }
                                ?>			
                                <?
                                            //SIN INFO DE APROBACION
                                            if( trim( $datos_viaticos["validaEncargado"])=="")
                                            {
                                            }
                                            else
                                            {
                                                //VIATICOS NO APROBADOS
                                                if( trim( (int)  $datos_viaticos["validaEncargado"])==0)
                                                {
                                    ?>
                                                    <img src="imagenes/Inactivo.gif" alt=" " title="No aprobado" /><br />
                                    <?
					                                echo $datos_viaticos["nombre"]." ".$datos_viaticos["apellidos"]."<br>";
	                                                echo $mes[$datos_viaticos["mes_a"]]." ".$datos_viaticos["dia_a"]." ".$datos_viaticos["ano_a"];
                                                }
                                            }
                                ?>			
                          </td>
                          <td width="15%" rowspan="<?=$cant_reg_via ?>"><?=$datos_viaticos["comentaEncargado"] ?></td>
              </tr>
<?
							}
							//CUANDO SE HAN IMPRESO TODAS LAS ACTIVIDADES, QUE TIENEN LA MISMA CARACTERISTICA, SE REINICIA EL CONTADOR
							if($cant_reg_via==$con_via)
							{
								$con_via=0;
							}
							$con_via++;
					}
?>
       
	        </table>
<?
			}

?>

		  </td>

      </tr>



<?
// rowspan="<?=$cant_reg_fact 
			}
?>

    </table></td>
    <td align="center" class="TxtTabla">
<?
	//SI LA CANTIDAD DE ACTIVIDADES, ES INFERIOR A LA CANTIDAD DE ACTIVI QUE SE HAN APROBADO, SE MUESTRA EL BOTON, PARA APROBAR LA FACTURACION DE ACTIVIDADES
	if((( int ) mssql_num_rows($cur_fac))> $cant_aprobaciones)
	{
//echo mssql_num_rows($cur_fac)." ********* ".$cant_aprobaciones;
//cant_aprobaciones
?>
		<input name="Submit" type="submit" class="Boton" value="VoBo" onClick="MM_openBrWindow('htVoBoFactViaticos.php?pMes=<?=$pMes ?>&vigencia=<?=$vigencia ?>&cualProyecto=<?=$cualProyecto ?>&unidad_u=<?=$datos_usu["unidad"] ?><? if($activi!=""){	echo "&activi=".$activi; } ?>','winAddFV','scrollbars=yes,resizable=yes,width=1400,height=400')" />
							
<?
	}
	else
	{
?>
<img  src="img/images/actualizar.jpg" onClick="MM_openBrWindow('upVoBoFactViaticos.php?pMes=<?=$pMes ?>&vigencia=<?=$vigencia ?>&cualProyecto=<?=$cualProyecto ?>&unidad_u=<?=$datos_usu["unidad"] ?><? if($activi!=""){	echo "&activi=".$activi; } ?>','winAddFV','scrollbars=yes,resizable=yes,width=1400,height=400')" />
<?
	}
?>
	</td>
    <td class="TxtTabla"><img src="img/images/ver.gif" width="16" height="16"  onClick="MM_openBrWindow('htFacturacion_via_fac.php?pMes=<?=$pMes ?>&pAno=<?=$vigencia ?>&unidad_u=<?=$datos_usu["unidad"] ?>','winAddFV','scrollbars=yes,resizable=yes,width=1400,height=400')" ></td>
  </tr>
	<tr>
		<td colspan="7" class="TituloTabla">&nbsp;</td>
  </tr>
<?
//echo $cant_aprobaciones+" /***** ".(( int ) mssql_num_rows($cur_fac))."<br>";
		$cant_acti_apro+=$cant_aprobaciones;
		$can_activi+=(( int ) mssql_num_rows($cur_fac));
	}
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
    <td align="right">
<?
			if ( $cant_acti_apro>0)
			{
?>
		<input name="Submit2" type="submit" class="Boton" onClick="MM_openBrWindow('upVoBoFactViaticos.php?pMes=<?=$pMes ?>&vigencia=<?=$vigencia ?>&cualProyecto=<?=$cualProyecto ?><? if($activi!=""){	echo "&activi=".$activi; } ?>','winAddFV','scrollbars=yes,resizable=yes,width=1400,height=400')" value="Edición de la facturación" /> 

				&nbsp;
<?
			}
			//SI EXITEN ACTIVIDADES SIN APROBAR
///echo $can_activi." **** ".$cant_acti_apro;
			if ( $can_activi > $cant_acti_apro)
			{
?>
	 <input name="Submit2" type="submit" class="Boton" onClick="MM_openBrWindow('htVoBoFactViaticos.php?pMes=<?=$pMes ?>&vigencia=<?=$vigencia ?>&cualProyecto=<?=$cualProyecto ?> <? if($activi!=""){	echo "&activi=".$activi; } ?>','winAddFV','scrollbars=yes,resizable=yes,width=1400,height=400')" value="Aprobación de la facturación" />

<?
			}
?>
	</td>
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