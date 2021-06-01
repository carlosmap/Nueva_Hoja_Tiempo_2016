<script language="JavaScript" type="text/JavaScript">


function cerrar()
{
	window.close();
}


function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}

</script>

<?
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

//$cualProyecto =683;
// include("bannerArriba.php") ; 


$sql="SELECT P.*, D.nombre nombreD, D.apellidos apellidosD, C.nombre nombreC, C.apellidos apellidosC  ";
$sql=$sql." FROM proyectos P, Usuarios D, Usuarios C " ;
$sql=$sql." WHERE P.id_director *= D.unidad " ;
$sql=$sql." AND P.id_coordinador *= C.unidad " ;
$sql=$sql." AND P.id_proyecto = " . $cualProyecto ;
$cursor = mssql_query($sql);

while ($reg=mssql_fetch_array($cursor)) 
{
	$uni_coor=$reg["id_coordinador"];
	$uni_dir=$reg["id_director"];

}

/*
if($recarga==2)
{
*/
			//SI LA PERSONA QUE CONSULTA LA FACTURACION, ES DIRECTOR O CORRDINADOR, SE CONSULTA TODAS LA FACTURACION  DEL PROYECTO
			if ( ($uni_coor==$laUnidad) || ($uni_dir==$laUnidad) )
			{
				//CONSULTA LOS USUARIOS, QUE HA REGISTRADO FACTURACION EN EL PROYECTO, Y LOS CUALES TIENEN ACTIVIDADES PENDIENTES POR DAR VoBo
				$sql_usuarios="select distinct(FacturacionProyectos.unidad), upper (Usuarios.nombre +' '+Usuarios.apellidos) nombre, Usuarios.fechaRetiro,retirado from FacturacionProyectos 
								inner join Usuarios on FacturacionProyectos.unidad=Usuarios.unidad
								where id_proyecto=".$cualProyecto." and vigencia=".$vigencia." and mes=".$pMes ." ";
			
				if(trim($unidad_u)!="")
				{
					$sql_usuarios=$sql_usuarios." and FacturacionProyectos.unidad=".$unidad_u." ";
				}
				$sql_usuarios=$sql_usuarios."
			 AND id_actividad in 
								(
			select id_actividad from VoBoFactuacionProyHT where id_proyecto=FacturacionProyectos.id_proyecto and unidad=FacturacionProyectos.unidad and esInterno='I' and vigencia=FacturacionProyectos.vigencia and mes=FacturacionProyectos.mes					
								)"; 
			}
			else
			{
				//SI NO ES EL DIRECTOR O COORDINA, SE CONSULTA LAS PERSONAS, QUE REGISTRARON FACTURACION, EN LAS ACTIVIDADES, EN LAS QUE LA PERSONA ES RESPONSABLE

				//CONSULTA LOS USUARIOS, QUE HA REGISTRADO FACTURACION EN EL PROYECTO, Y LOS CUALES TIENEN ACTIVIDADES PENDIENTES POR DAR VoBo
				$sql_usuarios="select distinct(FacturacionProyectos.unidad), upper (Usuarios.nombre +' '+Usuarios.apellidos) nombre, Usuarios.fechaRetiro,retirado from FacturacionProyectos 
								inner join Usuarios on FacturacionProyectos.unidad=Usuarios.unidad
								inner join Actividades on Actividades.id_actividad=FacturacionProyectos.id_actividad and Actividades.id_proyecto=FacturacionProyectos.id_proyecto
								where FacturacionProyectos.id_proyecto=".$cualProyecto." and FacturacionProyectos.vigencia=".$vigencia." and FacturacionProyectos.mes=".$pMes ." and Actividades.id_encargado=".$laUnidad;
			
				if(trim($unidad_u)!="")
				{
					$sql_usuarios=$sql_usuarios." and FacturacionProyectos.unidad=".$unidad_u." ";
				}
				$sql_usuarios=$sql_usuarios."
			 AND FacturacionProyectos.id_actividad  in 
								(
			select id_actividad from VoBoFactuacionProyHT where id_proyecto=FacturacionProyectos.id_proyecto and unidad=FacturacionProyectos.unidad and esInterno='I' and vigencia=FacturacionProyectos.vigencia and mes=FacturacionProyectos.mes					
								)"; 
			}

			if(trim($activi)!="")
			{
				$sql_usuarios=$sql_usuarios." and FacturacionProyectos.id_actividad=".$activi;
			}
//echo $sql_usuarios." <br> ************** <br>".mssql_get_last_message();


if($recarga2==2)
{
//echo "toodos ".$todos_fac."<br> ".$fac_15306_0. " vigeeeeennncia ".$vigencia."  <br>";
	$error="no";
	$cont=0;
	$cont2=0;
	mssql_query("BEGIN TRANSACTION");
	//RECORRE LAS UNIDADES DE LAS PERSONAS
	for($i=1;$i<=$zz;$i++)
	{
		$z=0;
		$unidad="unis_".$i;
		$cantidad_reg_unidad="cant_reg_".$$unidad;
		while($z<$$cantidad_reg_unidad)
		{				
				$uni_pos="fac_".$$unidad."_".$z;

				$coment_fact="comen_fac_".$$unidad."_".$z;
				$di_act="act_".$$unidad."_".$z;

				if($$uni_pos!='')
				{
					//trahe el Vobo y el comentario, que se ha dado con anterioridad, en la facturacion
					$vobo_ant="vobo_".$$unidad."_".$z;
					$coment="comen_fac_ant_".$$unidad."_".$z;

					if($$uni_pos=='si')
					{
						$aprobo="1";
					}
					if($$uni_pos=='no')
					{
						$aprobo="0";
					}

					//SI EL USUARIO HA CAMBIADO, EL VOBO Y/O EL COMENTARIO DE LA FACTURACION ANTERIOR
					if(($$vobo_ant!=$aprobo)||((trim($$coment))!=(trim($$coment_fact))))
					{
						$sql_upda="UPDATE VoBoFactuacionProyHT SET validaEncargado=".$aprobo.", comentaEncargado='".$$coment_fact."', unidadEncargado=".$laUnidad.", usuarioMod=".$laUnidad.", fechaMod=getdate(), fechaAprEnc=getdate() 
						  where id_proyecto=".$cualProyecto."  AND id_actividad= ".$$di_act." AND unidad=".$$unidad." AND vigencia= ".$vigencia." AND mes=".$pMes." AND esInterno='I' ";
					}
				}
				//SI NO SE HA DADO VOBO SOBRE LA FACTURACION, SE ELIMINA EL REGISTRO DEL VOBO
				//ESTO PARA LOS CASOS, EN DONDE SE TIENE QUE LEVANTAR EL VOBO DE LA FACTURACION
				if($$uni_pos=='')
				{
					$sql_upda="delete from  VoBoFactuacionProyHT 
					  where id_proyecto=".$cualProyecto."  AND id_actividad= ".$$di_act." AND unidad=".$$unidad." AND vigencia= ".$vigencia." AND mes=".$pMes." AND esInterno='I' ";
				}

				$cur_update=mssql_query($sql_upda);

//echo $sql_upda." <br>".mssql_get_last_message()."<br><br>";
				if(trim($cur_update)=="")
				{
					$error="si";
					break;
				}
				$z++;
		}

	}

//echo $error." *** <br>";
	//SI SE PRESENTO ALGUN ERROR EN ALGUNA DE LAS OPERACIONES
	if($error=="si")
	{

		mssql_query(" ROLLBACK TRANSACTION");
		$mensaje='<script>alert("Se ha presentado un error, al momento de actualizar la informaci�n.\n"); </script>';
	}

	if($error=="no")
	{
//		mssql_query(" ROLLBACK TRANSACTION");
		mssql_query(" COMMIT TRANSACTION");
		echo "<script>alert('La operaci�n se realiz� con �xito')</script>";
	}
			echo "	<script type='text/javascript'>window.close(); window.opener.location='htFactViaticos.php?cualProyecto=".$cualProyecto."&pMes=".$pMes."&vigencia=".$vigencia."&recarga=2'; </script>";

}
?>


<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<title>.:: Planeaci&oacute;n de Proyectos ::.</title>


<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>

<script type="text/javascript"> 


	function valida()
	{
		var mensaje="";
		if(document.form1.pMes.value==0)
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

<!--
<br>
<div class="TxtNota2" style="position:absolute; left:3px; top:55px; width: 448px; height: 30px;">
<br>
 Planeaci&oacute;n de proyectos
 </div>
-->
<?
	if(trim($unidad_u)=="")
	{
?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="TxtTabla">&nbsp;</td>
      </tr>
    </table>
<?
	}
?>
<!--
<table width="100%"  bgcolor="#FFFFFF" >
	  <tr class="TituloTabla2">
	    <td>Proyecto</td>
	    <td width="20%">C&oacute;digo</td>
	    <td width="20%">Encargados</td>
	    <td width="20%">Programadores</td>
      </tr>
	  <?
/*
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
*/
	?>
</table>	
-->
<?

//	if(trim($unidad_u)=="")
//	{
?>
<!--
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
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
        <td bgcolor="#FFFFFF"><table width="100%"  border="0" align="center" cellpadding="0" cellspacing="1">
          <form action="" method="post" name="form1" id="form1">
            <tr>
              <td width="15%" align="right" class="TituloTabla">Mes:&nbsp;</td>
              <td width="20%" class="TxtTabla"><?
/*
	//Seleccionar el mes cuando se carga la p�gina por primera vez
	//si no cuando se recarga la p�gina
	if ($pMes == "") {
		$pMes=date("m"); //el mes actual
	}
	else {
		$pMes= $pMes; //el mes seleccionado
	}

	
*/
$mes = array( 'Seleccione Mes', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre' );
?>
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
                </select></td>
              <td width="15%" align="right" class="TituloTabla">Vigencia:&nbsp;</td>
              <td class="TxtTabla"><?
/*
	$cur_vigencia=mssql_query("select distinct(vigencia) vigencia from FacturacionProyectos order by vigencia ");
	if ($vigencia == "") {
		$vigencia=date("Y"); //el mes actual
	}
	else {
		$vigencia= $vigencia; //el mes seleccionado
	}
*/
//echo $vigencia."dddd";
?>
                <select name="vigencia" class="CajaTexto" id="vigencia">
                  <option value="" >Seleccione vigencia</option>
                  <?
/*
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
*/
?>
                </select></td>

              <td width="10%" class="TxtTabla"><input name="Submit8" type="button" class="Boton" value="Consultar" onClick="valida()" />
                <input name="recarga" id="recarga" type="hidden" value="1" /></td>
            </tr>
          </form>
        </table></td>
      </tr>
    </table>

-->
<?
//	}
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table>
<form action="" method="post" name="form2" id="form2">
<table width="100%"  border="0"  bgcolor="#FFFFFF" >

  <tr class="TituloUsuario">
    <td>Unidad</td>
    <td>Usuario</td>
    <td width="1%"></td>
    <td colspan="2">Facturaci&oacute;n de los proyectos </td>
    <td colspan="2">&nbsp;</td>
  </tr>
<?
	//si solo se esta consultando la facturacion de una persona
	if(trim($unidad_u)=="")
	{
?>
  <tr class="TituloUsuario">
    <td colspan="3">&nbsp;</td>
    <td width="50%" height="20%" align="center"><table width="53%"  border="0" cellspacing="0" cellpadding="0">
      <tr align="center" class="TxtTabla">
        <td height="5%">Aprobar toda la facturaci&oacute;n</td>
        <td class="TituloTabla2">Si</td>
        <td><input name="todos_fac" id="todos_fac" type="radio" value="0"  onClick="activar_todos_fact(0);" /></td>
        <td class="TituloTabla2">No</td>
        <td><input name="todos_fac" type="radio" id="todos_fac" onClick="activar_todos_fact(1);" value="1"  /></td>
      </tr>
    </table></td>
    <td width="50%" align="center"><table width="53%"  border="0" cellspacing="0" cellpadding="0">
      <tr align="center" class="TxtTabla">
        <td>Aprobar todos los viaticos</td>
        <td class="TituloTabla2">Si</td>
        <td><input name="todos_viat" id="todos_viat" type="radio" value="0"  onClick="activar_todos_viat(0);" disabled /></td>
        <td class="TituloTabla2">No</td>
        <td><input name="todos_viat" id="todos_viat" type="radio" value="1" onClick="activar_todos_viat(1);" disabled /></td>
      </tr>
    </table></td>
    <td colspan="2">&nbsp;</td>
  </tr>
<?
	}

	$cur_usuarios=mssql_query($sql_usuarios);
	$array_unidades= array(); //PERMITE ALMACENAR LAS UNIDADES, QUE FACTURARON EN EL PROYECTO, EN LE MES Y VIGENCIA SELECCIONADOS, ESTO SE UTILIZARA AL MOMENTO
								//DE ACTIVAR O DESCTIVAR LOS RADIO BUTTON
	$z=0; 
	while($datos_usu=mssql_fetch_array($cur_usuarios))
	{
			$z++;
			//CONSULTA EL TOTAL FACTURADO, Y LAS ACTIVIDADES ASOCIADAS, AL USUARIO QUE FACTURO, Y QUE HAN SIDO APROBADAS Y/O DESAPROBADAS
			$sql_fac="
				select distinct(FacturacionProyectos.id_actividad) id_actividad, SUM(horasMesF) total_fact, UPPER( Actividades.nombre) nombre ,Actividades.macroactividad from FacturacionProyectos 
				inner join Actividades on Actividades.id_actividad=FacturacionProyectos.id_actividad and Actividades.id_proyecto=FacturacionProyectos.id_proyecto";

				//SI LA PERSONA QUE CONSULTA LA FACTURACION, NO ES EL DIRECTOR O CORRDINADOR, SE CONSULTA LAS ACTIVIDADES ENLA QUE SE ENCUENTRA COMO RESPONSABLE
				if ( ($uni_coor!=$laUnidad) && ($uni_dir!=$laUnidad) )
				{
					$sql_fac=$sql_fac." and Actividades.id_encargado=".$laUnidad;
				}

			$sql_fac=$sql_fac."  
				where FacturacionProyectos.unidad=".$datos_usu["unidad"]." and FacturacionProyectos.vigencia=".$vigencia." and FacturacionProyectos.mes=".$pMes." 
				and FacturacionProyectos.id_proyecto=".$cualProyecto." and FacturacionProyectos.esInterno='I' ";

				//SI SE ESTA CONSULTANDO UNA ACTIVIDAD ESPECIFICA
				if(trim($activi)!="")
				{
					$sql_fac=$sql_fac." and FacturacionProyectos.id_actividad=".$activi;
				}

				$sql_fac=$sql_fac."   and FacturacionProyectos.id_actividad  in (
				select id_actividad from VoBoFactuacionProyHT where id_proyecto=".$cualProyecto." and unidad=".$datos_usu["unidad"]." and esInterno='I' and vigencia=".$vigencia." and mes=".$pMes." 				
								)
				group by FacturacionProyectos.id_actividad,Actividades.nombre,Actividades.macroactividad
				order by (macroactividad)";
			$cur_fac=mssql_query($sql_fac);
//echo $sql_fac." <br><br>";
			$cant_reg_fact=(mssql_num_rows($cur_fac));

			$array_unidades[$z]=$datos_usu["unidad"] ;
?>
  <tr>
    <td class="TxtTabla"   ><?=$datos_usu["unidad"] ?>
      <input type="hidden" name="unis_<?=$z ?>" id="unis_<?=$z ?>" value="<?=$datos_usu["unidad"] ?>" ></td>
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
    <td colspan="2"><table width="100%" border="0">
      <tr class="TituloUsuario">
        <td colspan="6" width="50%">Facturacion </td>

        <td width="50%">Viaticos</td>
      </tr>

      <tr class="TituloTabla2">
        <td>Actividad</td>
        <td>&nbsp;</td>
        <td width="10%">Horas Planeadas</td>
        <td>Horas Facturadas</td>

        <td>Aprobar
          <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr align="center" class="TxtTabla">
              <td width="100%">Todos </td>
              <td class="TituloTabla2">Si</td>
              <td><input name="<?="fac_".$datos_usu["unidad"]."" ?>" id="<?="fac_".$datos_usu["unidad"]."" ?>" type="radio" value="radiobutton" onClick="activar_factu(<?="".$datos_usu["unidad"]."" ?>,0)" /></td>
              <td class="TituloTabla2">No</td>
              <td><input  name="<?="fac_".$datos_usu["unidad"]."" ?>" id="<?="fac_".$datos_usu["unidad"]."" ?>" type="radio" value="radiobutton"  onClick="activar_factu(<?="".$datos_usu["unidad"]."" ?>,1)" /></td>
            </tr>
          </table></td>
        <td>Comentarios</td>

        <td><table width="100%" border="0" bgcolor="#FFFFFF" >
        </table></td>
      </tr>
<?
			$i=0;	//CONTADOR, CON EL CUAL SE CONFORMARAN, EL NOMBRE DE LOS RADIOBUTTON DE LA APROBACION DE FACTURACION
			while($datos_fac=mssql_fetch_array($cur_fac))
			{
			


?>
      <tr >
        <td width="10%" class="TxtTabla"><?="[".$datos_fac["macroactividad"]."] ".$datos_fac["nombre"] ?>
          <input type="hidden" name="act_<?=$datos_usu["unidad"]."_".$i ?>" id="act_<?=$datos_usu["unidad"]."_".$i ?>" value="<?=$datos_fac["id_actividad"] ?>" >
          <input type="hidden" name="nom_act_<?=$datos_usu["unidad"]."_".$i ?>" id="nom_act_<?=$datos_usu["unidad"]."_".$i ?>" value="<?="[".$datos_fac["macroactividad"]."] ".$datos_fac["nombre"] ?>" ></td>
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
    
                    <img src="img/images/alertaRojo.gif" width="15" height="16" title="Facturacion sin planeaci�n" >
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


		$sql_voFac="select  DAY(fechaAprEnc) dia , MONTH(fechaAprEnc) mes,YEAR(fechaAprEnc) ano,comentaEncargado,validaEncargado,  Usuarios.nombre, Usuarios.apellidos  from VoBoFactuacionProyHT 
			INNER JOIN Usuarios on unidadEncargado=Usuarios.unidad
		where VoBoFactuacionProyHT.unidad=".$datos_usu["unidad"]."  and VoBoFactuacionProyHT.vigencia=".$vigencia." and VoBoFactuacionProyHT.mes=".$pMes." 
		and VoBoFactuacionProyHT.id_proyecto=".$cualProyecto."  and VoBoFactuacionProyHT.esInterno='I' and VoBoFactuacionProyHT.id_actividad=".$datos_fac["id_actividad"];
		$cur_vofac=mssql_query($sql_voFac);
		$datos_vofac=mssql_fetch_array($cur_vofac);
//echo $sql_voFac." ********* ";
?>
        <td align="center" class="TxtTabla">
			<br>
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
			if( trim( (int)  $datos_vofac["validaEncargado"])==0)
			{
?>
				<img src="imagenes/Inactivo.gif" alt=" " title="No aprobado" /><br />
<?
				echo $datos_vofac["nombre"]." ".$datos_vofac["apellidos"]."<br>";
				echo $mes[$datos_vofac["mes"]]." ".$datos_vofac["dia"]." ".$datos_vofac["ano"];
				$cant_aprobaciones++;
			}
?>			<br>
			<table width="53%"  border="0" cellspacing="0" cellpadding="0">
          <tr align="center" class="TxtTabla">


            <td class="TxtTabla">Si</td>
            <td><input  name="<?="fac_".$datos_usu["unidad"]."_".$i ?>" id="<?="fac_".$datos_usu["unidad"]."_".$i ?>" type="radio" value="si" onClick="activa_inacti_via('<?="".$datos_usu["unidad"]."_".$i ?>',0);" <? if($datos_vofac["validaEncargado"]==1){ echo "checked"; } ?> /></td>

            <td class="TxtTabla">No</td>
            <td><input  name="<?="fac_".$datos_usu["unidad"]."_".$i ?>" id="<?="fac_".$datos_usu["unidad"]."_".$i ?>" type="radio" value="no" onClick="activa_inacti_via('<?="".$datos_usu["unidad"]."_".$i ?>',1);" <? if($datos_vofac["validaEncargado"]==0){ echo "checked"; } ?>  /></td>
          </tr>
        </table>
			<input type="hidden" value=" <?=$datos_vofac["validaEncargado"] ?>" name="vobo_<?=$datos_usu["unidad"]."_".$i ?>" id="vobo_<?=$datos_usu["unidad"]."_".$i ?>"  >
		</td>

        <td class="TxtTabla"><textarea name="<?="comen_fac_".$datos_usu["unidad"]."_".$i ?>"  cols="15" rows="3" class="CajaTexto"  id="<?="comen_fac_".$datos_usu["unidad"]."_".$i ?>"><?=$datos_vofac["comentaEncargado"];  ?></textarea>

				<input type="hidden" value="<?=$datos_vofac["comentaEncargado"];  ?>" name="<?="comen_fac_ant_".$datos_usu["unidad"]."_".$i ?>" id="<?="comen_fac_ant_".$datos_usu["unidad"]."_".$i ?>" >
		</td>



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
					AND ViaticosProyectosHT.id_actividad=".$datos_fac["id_actividad"];
//echo "**********".$sql_viaticos."<br><br>";
					$cur_viati=mssql_query($sql_viaticos);
?>
			<table width="100%" border="0" bgcolor="#FFFFFF">
            <tr class="TituloTabla2">
              <td width="15%">Tipo de vi&aacute;tico </td>
              <td width="15%">Sitio</td>
              <td width="15%">Fecha Inicio </td>
              <td width="15%">Fecha finalizaci&oacute;n</td>
              <td width="5%">Dia de regreso<br><img title="1=Dia Completo, 2=Dia de Regreso." src="../NuevaHojaTiempo/imagenes/icoDetalleInf.gif"/></td>
              <td width="15%">Aprobar
                <table width="53%"  border="0" cellspacing="0" cellpadding="0">
                  <tr align="center" class="TxtTabla">

                    <td width="100%">Todos </td>
                    <td class="TituloTabla2">Si</td>
                    <td>

						<input name="<?="via_".$datos_usu["unidad"]."_".$i ?>" id="<?="via_".$datos_usu["unidad"]."_".$i ?>"  type="radio" value="radiobutton"  onClick=" activar_via('<?="via_".$datos_usu["unidad"]."_".$i ?>',0);" disabled /></td>
                    <td class="TituloTabla2">No</td>
                    <td><input name="<?="via_".$datos_usu["unidad"]."_".$i ?>" id="<?="via_".$datos_usu["unidad"]."_".$i ?>" type="radio"onClick=" activar_via('<?="via_".$datos_usu["unidad"]."_".$i ?>',1);" value="radiobutton"  disabled  /></td>

<?
			//si no hay registros de viaticos, se desactiva el boton radio que permite seleccionar los viaticos por actividad
			if(mssql_num_rows($cur_viati)==0)
			{
?>
<script type="text/javascript">
				document.form2.<?="via_".$datos_usu["unidad"]."_".$i ?>[0].disabled=true;
				document.form2.<?="via_".$datos_usu["unidad"]."_".$i ?>[1].disabled=true;
</script>
<?
			}	

	
?>
                  </tr>
                </table></td>
              <td width="15%"  class="TituloTabla2">Quien aprueba</td>
              <td width="15%"  class="TituloTabla2">Comentarios</td>
            </tr>
<?
			$j=0;
			if(mssql_num_rows($cur_viati)>0)
			{


					while($datos_viaticos=mssql_fetch_array($cur_viati))
					{
?>
                        <tr class="TxtTabla">

                          <td width="15%"><?=$datos_viaticos["NomTipoViatico"] ?></td>
                          <td width="15%"><?=$datos_viaticos["NomSitio"] ?></td>
                          <td width="15%"><?=$mes[$datos_viaticos["mes_i"]]." ".$datos_viaticos["dia_i"]." ".$datos_viaticos["ano_i"] ?></td>
                          <td width="15%"><?=$mes[$datos_viaticos["mes_f"]]." ".$datos_viaticos["dia_f"]." ".$datos_viaticos["ano_f"] ?></td>
                          <td width="15%"><?=$datos_viaticos["viaticoCompleto"] ?></td>




                          <td width="5%" align="center">

							<table width="53%"  border="0" cellspacing="0" cellpadding="0">
                            <tr align="center" class="TxtTabla">

                              <td class="TxtTabla">Si</td>
                              <td><input  name="<?="via_".$datos_usu["unidad"]."_".$i."_".$j ?>" id="<?="via_".$datos_usu["unidad"]."_".$i."_".$j ?>" type="radio" value="si" <? if($datos_viaticos["validaEncargado"]==1){ echo "checked"; } ?>   <? if($datos_vofac["validaEncargado"]==0){ echo "disabled"; } ?>/></td>
                              <td class="TxtTabla">No</td>
                              <td><input  name="<?="via_".$datos_usu["unidad"]."_".$i."_".$j ?>" id="<?="via_".$datos_usu["unidad"]."_".$i."_".$j ?>" type="radio" value="no" <? if(trim($datos_viaticos["validaEncargado"])==""){ } else { if($datos_viaticos["validaEncargado"]==0){ echo "checked"; } } ?>  <? if($datos_vofac["validaEncargado"]==0){ echo "disabled"; } ?>   /></td>
                            </tr>
                          </table></td>
                          <td width="15%" align="center">
					<?
                                //FACTURACION APROBADA
                                if($datos_viaticos["validaEncargado"]==1)
                                {
                    ?>			
                                    <img src="img/images/Aprobado.gif" width="21" height="24" title="Aprobado" /><br />
                    <?
                                    echo $datos_viaticos["nombre"]." ".$datos_viaticos["apellidos"]."<br>";
                                    echo $mes[$datos_viaticos["mes_a"]]." ".$datos_viaticos["dia_a"]." ".$datos_viaticos["ano_a"];
                                    $cant_aprobaciones++;
                                }
								//SIN INFO DE APROBACION
								if( trim( $datos_viaticos["validaEncargado"])=="")
								{
								}
								else
								{
									if( trim( (int)  $datos_viaticos["validaEncargado"])==0)
									{
						?>
										<img src="imagenes/Inactivo.gif" alt=" " title="No aprobado" /><br />
						<?
										echo $datos_viaticos["nombre"]." ".$datos_viaticos["apellidos"]."<br>";
										echo $mes[$datos_viaticos["mes_a"]]." ".$datos_viaticos["dia_a"]." ".$datos_viaticos["ano_a"];
										$cant_aprobaciones++;
									}
								}
                    ?>	
						  </td>
                          <td>
                            <label for="comentario"></label>
                          <textarea name="comentario<?="_via_".$datos_usu["unidad"]."_".$i."_".$j ?>" cols="15" rows="3" class="CajaTexto" id="comentario<?="_via_".$datos_usu["unidad"]."_".$i."_".$j ?>"> <?=$datos_viaticos["comentaEncargado"];  ?></textarea>


					<input type="hidden" name="fechas_via<?=$datos_usu["unidad"]."_".$i."_".$j ?>" id="fechas_via<?=$datos_usu["unidad"]."_".$i."_".$j ?>" value=" F.I.  <?=$mes[$datos_viaticos["mes_i"]]." ".$datos_viaticos["dia_i"]." ".$datos_viaticos["ano_i"] ?> - F.F. <?=$mes[$datos_viaticos["mes_f"]]." ".$datos_viaticos["dia_f"]." ".$datos_viaticos["ano_f"] ?> " >       

					<input type="hidden" name="IDhorario<?=$datos_usu["unidad"]."_".$i."_".$j ?>" id="IDhorario<?=$datos_usu["unidad"]."_".$i."_".$j ?>" value="<?=$datos_viaticos["IDhorario"];  ?>" >       

			<input type="hidden" name="clase_tiempo<?=$datos_usu["unidad"]."_".$i."_".$j ?>" id="clase_tiempo<?=$datos_usu["unidad"]."_".$i."_".$j ?>" value="<?=$datos_viaticos["clase_tiempo"];  ?>" >       
			<input type="hidden" name="localizacion<?=$datos_usu["unidad"]."_".$i."_".$j ?>" id="localizacion<?=$datos_usu["unidad"]."_".$i."_".$j ?>" value="<?=$datos_viaticos["localizacion"];  ?>" >       
			<input type="hidden" name="cargo<?=$datos_usu["unidad"]."_".$i."_".$j ?>" id="cargo<?=$datos_usu["unidad"]."_".$i."_".$j ?>" value="<?=$datos_viaticos["cargo"];  ?>" >       
			<input type="hidden" name="IDsitio<?=$datos_usu["unidad"]."_".$i."_".$j ?>" id="IDsitio<?=$datos_usu["unidad"]."_".$i."_".$j ?>" value="<?=$datos_viaticos["IDsitio"];  ?>" >       
			<input type="hidden" name="IDTipoViatico<?=$datos_usu["unidad"]."_".$i."_".$j ?>" id="IDTipoViatico<?=$datos_usu["unidad"]."_".$i."_".$j ?>" value="<?=$datos_viaticos["IDTipoViatico"];  ?>" > 
							</td>
                        </tr>

      

<?
						$j++;
					}

					//total registros de viaticos, por cada registro de facturacion
?>

<?
			}
			else
			{
					echo '
                          <td width="15%" class="TxtTabla">&nbsp;</td>
                          <td width="15%" class="TxtTabla">&nbsp;</td>
                          <td width="15%" class="TxtTabla">&nbsp;</td>
                          <td width="15%" class="TxtTabla">&nbsp;</td>
                          <td width="15%" class="TxtTabla">&nbsp;</td>
                          <td width="15%" class="TxtTabla">&nbsp;</td>
                          <td width="15%" class="TxtTabla">&nbsp;</td>
                          <td width="15%" class="TxtTabla">&nbsp;</td>
						';
			}

?>
					<input type="hidden" name="cant_reg_via_<?=$datos_usu["unidad"]."_".$i ?>" id="cant_reg_via_<?=$datos_usu["unidad"]."_".$i ?>" value="<?=$j ?>" >       
	        </table>


		  </td>

      </tr>



<?
				$i++;
// rowspan="<?=$cant_reg_fact 
			}
?>


    </table><input type="hidden" name="cant_reg_<?=$datos_usu["unidad"] ?>" id="cant_reg_<?=$datos_usu["unidad"] ?>" value="<?=$i ?>" ></td>
    <td class="TxtTabla"><img src="img/images/ver.gif" width="16" height="16" /></td>
  </tr>
	<tr>
		<td colspan="8" class="TxtTabla">&nbsp;</td>
  </tr>
<?
	}
?>

</table>

<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="right">&nbsp;</td>
  </tr>
  <tr>
    <td align="right">
	<input type="button" class="Boton" value="Cancelar" onClick="cerrar()" >
	<input type="hidden" name="recarga2" id="recarga2" value="1" >
	<input type="hidden" name="zz" id="zz" value="<?=$z ?>" >

		<input name="Submit2" type="button" class="Boton" onClick="valida_2()" value="Grabar" /></td>
  </tr>
  <tr>
    <td align="right">&nbsp;</td>
  </tr>
</table>
</form>

</body>
<script type="text/javascript">
		var expr1;
		var esta="";
		var uni,i=1,valor='',cantidad_reg_unidad=0,uni_pos,m=0;
		//crea el array con las unidades, que se asociaron en la consulta de la pagina
			var unidades= new Array();
<?
			$ba=0;
			$can=count($array_unidades);
			foreach($array_unidades as $val)
			{
				echo "unidades[".$ba."]=".$val."; \n";
				$ba++;
			}
?>


	function valida_2()
	{
		var mensaje="";
/*		var z=0, ban_sele="no"; //ban_sel bandera que permite identificar, si se ha seleccionado SI/NO en la aprobacion de la facturacion, para almenos una actividad

			for(i=0;i<(unidades.length);i++)
			{
				z=0;
				//almacena la cantidad de registros que hay por cada unidad con facturacion
				cantidad_reg_unidad=document.getElementById("cant_reg_"+unidades[i]).value;
				while(z<cantidad_reg_unidad)
				{				

					uni_pos="fac_"+unidades[i]+"_"+z;
					expr1='document.form2.'+uni_pos+'[0].checked';
					expr2='document.form2.'+uni_pos+'[1].checked';;

					//si selecciono si/no en la aprobacion de la facturacion, para almenos una actividad, ban_sel cambia su valor por si
					if(  ((eval(expr1))) ||  ((eval(expr2)))  ) 
					{
						ban_sele="si";
					}

					
					z++;
				}
			}	
			if(ban_sele=="no")
				mensaje="Apruebe o desapruebe la facturaci�n de almenos una actividad";
*/

		if(mensaje!="")	
			alert(mensaje);
		else
		{
			if(mensaje!="")	
				alert(mensaje);
			else
			{
				document.form2.recarga2.value=2;
				document.form2.submit();			
			}		
		}
	}

	//selecciona si/no en todos los radibuttons de la facturacion			
	function activar_todos_fact(acti_desac)
	{

		esta="";
		if(acti_desac==0)
		{
			esta=false;
		}
		if(acti_desac==1)
		{
			esta=true;
		}
				document.form2.todos_viat[0].checked=false;
				document.form2.todos_viat[1].checked=false;

				document.form2.todos_viat[0].disabled=esta;
				document.form2.todos_viat[1].disabled=esta;


			for(i=0;i<(unidades.length);i++)
			{
				//selecciona en si/no, todos los radios de las cabeceras de la factueacion, en cada una actividad
				uni="fac_"+unidades[i]+"";
				expr1 = 'document.form2.'+uni+'['+acti_desac+'].checked = true';

				eval(expr1);



				//almacena la cantidad de registros que hay por cada unidad con facturacion
				cantidad_reg_unidad=document.getElementById("cant_reg_"+unidades[i]).value;
				var z=0;
				while(z<cantidad_reg_unidad)
				{
					activa_inacti_via(''+unidades[i]+"_"+z,acti_desac);

					uni_pos="fac_"+unidades[i]+"_"+z;
					expr1='document.form2.'+uni_pos+'['+acti_desac+'].checked=true';
					eval(expr1);
					z++;
				}
//				alert(cantidad_reg_unidad);
//.form2.cant_reg_+"unidades[i]"
			}	

	}

	//selecciona si/no los radio button de la facturacion asociados a cada actividad
	function activar_factu(uni,acti_desac)
	{
				//almacena la cantidad de registros que hay por cada unidad con facturacion
				cantidad_reg_unidad=document.getElementById("cant_reg_"+uni).value;
				var z=0;
				while(z<cantidad_reg_unidad)
				{
					uni_pos="fac_"+uni+"_"+z;
					expr1='document.form2.'+uni_pos+'['+acti_desac+'].checked=true';
					eval(expr1);
		
					//si no se aprueba la facturacion, se inabilitara los campos de los viaticos, ya que no se puede viaticar, si no se han aprobado los viaticos
					//se activan o inactivan, todos los campos de los viaticos, asociados a cada actividad en donde el  usuario ha facturado
					activa_inacti_via(''+uni+"_"+z,acti_desac);
					z++;
				}		

	}

	//selecciona si/no en todos los radibuttons de los viaticos
	function activar_todos_viat(acti_desac)
	{
			//recorre todas las unidades asocadas a la facturacion
			for(i=0;i<(unidades.length);i++)
			{
				//almacena la cantidad de registros de actividades, que hay por usuario
				cantidad_reg_unidad=document.getElementById("cant_reg_"+unidades[i]).value;
				m=0;

				while(m<cantidad_reg_unidad)
				{
					//selecciona en si/no, todos los radios asociados a los viaticos, que se encuentran en la cabecera de cada tabla de viaticos
					uni="via_"+unidades[i]+"_"+m;
					expr1 = 'document.form2.'+uni+'['+acti_desac+'].checked = true';
					eval(expr1);

					///almacena el valor de la cantidad de registros de viaticos, por cada tabla de viaticos de una actividad
					cantidad_reg_unidad_viati=document.getElementById("cant_reg_via_"+unidades[i]+"_"+m).value;
					var z=0;
					while(z<cantidad_reg_unidad_viati)
					{
						uni_pos="via_"+unidades[i]+"_"+m+"_"+z;
						expr1='document.form2.'+uni_pos+'['+acti_desac+'].checked=true';
						eval(expr1);
						z++;
					}

					m++;
				}

			}	
	}

	//selecciona si/no los radio button de los viaticos asociados a cada actividad
	function activar_via(uni,acti_desac)
	{

				//almacena la cantidad de registros de viaticos que hay por cada actividad
				cantidad_reg_unidad_viati=document.getElementById("cant_reg_"+uni).value;
				var z=0;
				//recorre los radio button, referenciandolos por la 'via_'unidad_'conse_acti'_'conse_viati'
				while(z<cantidad_reg_unidad_viati)
				{
					uni_pos=uni+"_"+z;
					expr1='document.form2.'+uni_pos+'['+acti_desac+'].checked=true';
					eval(expr1);
					z++;
				}		

	}

	function activa_inacti_via(uni_act,esta)
	{

				//almacena la cantidad de registros de viaticos que hay por cada actividad
				cantidad_reg_unidad_viati=document.getElementById("cant_reg_via_"+uni_act).value;
//alert(uni_act+" *** estad: "+esta+" canti: "+cantidad_reg_unidad_viati);
				var z=0;
//alert(uni_act+" --- "+esta+" *** "+cantidad_reg_unidad_viati);

				//recorre los radio button, referenciandolos por la 'via_'unidad_'conse_acti'_'conse_viati'
				while(z<cantidad_reg_unidad_viati)
				{
					//compone el ID de los viaticos, asociados a la actividad
					uni_pos="via_"+uni_act+"_"+z;

					//si se selecciona en no aprobar facturacion, se desabilita los botones radio de los viaticos asociados a la actividad
					if(esta==1)
					{
						//limpia los radios de la cabecera de la tabla de los viaticos
						expr1='document.form2.via_'+uni_act+'[0].checked=false';
						eval(expr1);
						expr1='document.form2.via_'+uni_act+'[1].checked=false';
						eval(expr1);
						//desabilita los radios de la cabecera de la tabla de los viaticos
						 expr1='document.form2.via_'+uni_act+'[0].disabled=true';
						eval(expr1);
						 expr1='document.form2.via_'+uni_act+'[1].disabled=true';
						eval(expr1);


						expr1='document.form2.'+uni_pos+'[0].checked=false';
						eval(expr1);
						expr1='document.form2.'+uni_pos+'[1].checked=false';
						eval(expr1);
						//desabilita los radios
						 expr1='document.form2.'+uni_pos+'[0].disabled=true';
						eval(expr1);
						 expr1='document.form2.'+uni_pos+'[1].disabled=true';
						eval(expr1);
						//desabilita el campo de texto de los viaticos
						document.getElementById('comentario_'+uni_pos).value="";
						document.getElementById('comentario_'+uni_pos).disabled=true;

					}
	
					//si se selecciona en si, se activan todos los radios de los viaticos
					if(esta==0)
					{
						//limpia los radios de la cabecera de la tabla de los viaticos
						expr1='document.form2.via_'+uni_act+'[0].checked=false';
						eval(expr1);
						expr1='document.form2.via_'+uni_act+'[1].checked=false';
						eval(expr1);
						//desabilita los radios de la cabecera de la tabla de los viaticos
						 expr1='document.form2.via_'+uni_act+'[0].disabled=false';
						eval(expr1);
						 expr1='document.form2.via_'+uni_act+'[1].disabled=false';
						eval(expr1);

						expr1='document.form2.'+uni_pos+'[0].checked=false';
						eval(expr1);
						expr1='document.form2.'+uni_pos+'[1].checked=false';
						eval(expr1);
						//desabilita los radios
						 expr1='document.form2.'+uni_pos+'[0].disabled=false';
						eval(expr1);
						 expr1='document.form2.'+uni_pos+'[1].disabled=false';
						eval(expr1);
						//desabilita el campo de texto de los viaticos
						document.getElementById('comentario_'+uni_pos).value="";
						document.getElementById('comentario_'+uni_pos).disabled=false;

					}

					z++;
				}				

	}
</script>
</html>