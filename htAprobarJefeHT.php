<script language="JavaScript" type="text/JavaScript">
function cerrar()
{
	window.close();
}
</script>

<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";
	

//CONSULTAR LOS usuarioS QUE  HAN ENVIADO LA  H.T. A REVISION DEL JEFE INMEDIATO
$sql="Select A.*, U.nombre, U.apellidos , U.retirado ";
$sql=$sql." from VoBoFirmasHT A  INNER JOIN usuarios U ON A.unidad=U.unidad " ;
$sql=$sql." where A.unidad = U.unidad " ;
//filtra el resultado de la consulta si la página se carga por primera vez con el mes y año actual
//sino con lo seleccionado en las listas mes y año
if ($pMes == "") {
	$sql= $sql. " AND A.mes = month(getdate()) ";
	$sql= $sql. " AND A.vigencia = year(getdate()) ";
}
else {
	$sql= $sql. " AND A.mes = " . $pMes;
	$sql= $sql. " AND A.vigencia =  " . $pAno;
}

//USUARIOS ACTIVOS 
if (($pRetirado == "0")) {
	$sql= $sql. " and u.retirado IS NULL ";
}

//SE CONSULTAN LOS USUARIOS RETIRADOS EN EL MES SELECCIONADO Y/O EL MES ACTUAL
if ($pRetirado == "1") {

		//USUARIOS RETIRADOS
		$sql= $sql. " and u.retirado IS NOT NULL ";

		if ($pMes == "") {
			$sql= $sql. " and (month(fechaRetiro)= ".date("m")." and year(fechaRetiro)=".date("Y").") ";
		}
		else {
			$sql= $sql. " and (month(fechaRetiro)= ".$pMes." and year(fechaRetiro)=".$pAno.") ";
		}
}


if (trim($unidades) != "") {
	$sql= $sql. " AND  A.unidad = " . $unidades;
}

if (trim($nombres) != "") {
	$sql= $sql. " AND  U.nombre like '%" . $nombres."%' OR  U.apellidos like '%" . $nombres."%'" ;
}

$sql=$sql." and A.unidadJefe = " . $laUnidad;

//COMPONE UNA NUEVA CONSULTA, CON EL FIN DE VERIFICAR, SI EXISTEN H.T, SIN APROBAR
$sql=$sql." and  ( validaJefe IS NULL or validaJefe=0 ) ";

//$sql=$sql." and  validaJefe IS NULL ";

$sql=$sql." order by U.apellidos  ";
$cursor = mssql_query($sql);


//echo " /*** ".$recarga."  **** ".$_GET["pMes"]." <br>";
if($recarga==2)
{
		$error="no";
		$cont=0;
		mssql_query("BEGIN TRANSACTION");
		for($i=1;$i<=$cant_usu_facturados;$i++)
		{

			$unis="unidad_".$i;
			$vobo="vobo_".$$unis;
//echo "Ingresa ".$$unis." --- ".$cant_usu_facturados."  *** $i ".$unidad_1." <br>";
			if($$vobo!='')
			{

				$comentarios="comentarios_".$i;
				$sql_update="update VoBoFirmasHT set validaJefe=".$$vobo." ,unidadJefe=".$laUnidad.", comentaJefe='".$$comentarios."' , fechaAprueba=getdate(), usuarioMod=".$laUnidad.", fechaMod=getdate() where vigencia=".$pAno." and mes=".$pMes." and  unidad=".$$unis;

				$cur_update=mssql_query($sql_update);
//echo mssql_get_last_message()." *** ".$sql_update."<br>";
				if(trim($cur_update)=="")
				{
						$error="si";
						$nom="nombre_".$i;
						$arr_act_error[$cont]="[".$$unis."] ".$$nom."";
						$cont++;
				}
			}
		}


		//SI SE PRESENTO ALGUN ERROR EN ALGUNA DE LAS OPERACIONES
		if($error=="si")
		{
	
			mssql_query(" ROLLBACK TRANSACTION");
			if(0<count($arr_act_error))
			{
				$mensaje='<script>alert("Los VoBo de las siguientes usuarios no se han podido grabar.\n \n';
		
					foreach($arr_act_error as $men )
						$mensaje=$mensaje.'  '.$men.'\n';
		
				$mensaje=$mensaje.' ")</script>';
				echo $mensaje;
			}
		}
	if($error=="no")
	{
				mssql_query(" COMMIT TRANSACTION");
		echo "<script>alert('La operación se realizó con éxito')</script>";
	}
			echo "	<script>window.close();  window.opener.location='htApruebaHT.php?pMes=".$pMes."&pAno=".$pAno."';</script>";
}


//echo $sql." <br> ******** ".mssql_get_last_message();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
<!--
window.name="winHojaTiempos";
//-->
function limpiar()
{
		cant_usu_factura=document.vobo_facturacion.cant_usu_facturados.value
		var z=1;
		while(z<=cant_usu_factura)
		{

			unidad=document.getElementById("unidad_"+z).value;
			uni_pos="vobo_"+unidad;

			expr1='document.vobo_facturacion.'+uni_pos+'[0].checked=false';
			eval(expr1);
			expr1='document.vobo_facturacion.'+uni_pos+'[1].checked=false';
			eval(expr1);
			z++;
		}		
}

function valida()
{
	var unidad,cant_usu_factura=document.vobo_facturacion.cant_usu_facturados.value, radio, cont_no_selec=0,mensaje='',nombre;
	for(i=1; i<=cant_usu_factura ;i++)
	{
		unidad=document.getElementById("unidad_"+i).value;

		radio=document.getElementsByName("vobo_"+unidad);

			if( ( !(radio["0"].checked) )&& ( !(radio["1"].checked) ) )
			{
				cont_no_selec++;
			}
			else
			{

				if ( (radio["1"].checked) && ( (document.getElementById("comentarios_"+i).value)=="" ) )
				{
					if(mensaje=='')
						mensaje=mensaje+"No ha aprobado la facturación para los siguientes usuarios, por favor ingrese los comentarios respectivos.\n";

					mensaje=mensaje+"["+unidad+"] "+document.getElementById("nombre_"+i).value+" \n";
				}
			}
	}
	if(cont_no_selec==cant_usu_factura)
		 mensaje="Apruebe o desapruebe la hoja de tiempo de almenos un usuario.";

	if(mensaje!='')
		alert(mensaje);
	else
	{
		document.vobo_facturacion.recarga.value=2;
		document.vobo_facturacion.submit();

	}
}
</script>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>.:: Aprobaci&oacute;n de la Hoja de Tiempo ::.</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<? //include("bannerArriba.php") ; ?>
<!--
	<div class="TxtNota1" style="position:absolute; left:258px; top:11px; width: 365px;">
		<div align="center"> REVISIÓN HOJAS DE TIEMPO </div>
	</div>
-->
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table>
<!--
<table width="40%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Consulta de otros periodos </td>
  </tr>
</table>
-->
<form method="post" name="vobo_facturacion" id="vobo_facturacion" action="" >
<!--
<table width="40%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td bgcolor="#FFFFFF">
	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="1">

  <tr>
    <td width="10%" align="center" class="TituloTabla">Mes:&nbsp;</td>
    <td width="30%" class="TxtTabla">
	<? 
/*
	//Seleccionar el mes cuando se carga la página por primera vez
	//si no cuando se recarga la página
	if ($pMes == "") {
		$mesActual=date("m"); //el mes actual
	}
	else {
		$mesActual= $pMes; //el mes seleccionado
	}
*/
	?>

	<select name="pMes" class="CajaTexto" id="pMes">
      <option value="1"  <? /* if ($mesActual=='1'){ echo "selected"; } ?> >Enero</option>
      <option value="2" <? if ($mesActual=='2'){ echo "selected"; }  ?>>Febrero</option>
      <option value="3" <? if ($mesActual=='3'){ echo "selected"; } ?>>Marzo</option>
      <option value="4" <? if ($mesActual=='4'){ echo "selected"; }  ?>>Abril</option>
      <option value="5" <? if ($mesActual=='5'){ echo "selected"; }  ?>>Mayo</option>
      <option value="6" <? if ($mesActual=='6'){ echo "selected"; }  ?>>Junio</option>
      <option value="7" <? if ($mesActual=='7'){ echo "selected"; }  ?>>Julio</option>
      <option value="8" <? if ($mesActual=='8'){ echo "selected"; }  ?>>Agosto</option>
      <option value="9" <? if ($mesActual=='9'){ echo "selected"; }  ?>>Septiembre</option>
      <option value="10" <? if ($mesActual=='10'){ echo "selected"; }  ?>>Octubre</option>
      <option value="11" <? if ($mesActual=='11'){ echo "selected"; }  ?>>Noviembre</option>
      <option value="12" <? if ($mesActual=='12'){ echo "selected"; } */ ?>>Diciembre</option>
    </select>

		</td>
	</tr>
  <tr>
    <td width="10%" align="center" class="TituloTabla">A&ntilde;o:&nbsp;</td>
    <td class="TxtTabla">
      <select name="pAno" class="CajaTexto" id="pAno">
        <? 
/*
	//Generar los a&ntilde;os de 2006 a 2050
	for($i=2006; $i<=2050; $i++) { 
		
		//seleccionar el a&ntilde;o cuando se carga la p&aacute;gina por primera vez
		if ($pAno == "") {
			$AnoActual=date("Y"); //el a&ntilde;o actual
		}
		else {
			$AnoActual= $pAno; //el a&ntilde;o seleccionado
		}

	?>
        <option value="<? echo $i; ?>" <? if ($i == $AnoActual) {	echo  "selected"; } ?> ><? echo $i; ?></option>
        <? 
	 	
	 } //for 
*/	 
	 ?>
      </select></td>
  </tr>
  <tr>
    <td width="12%" align="center" class="TituloTabla">Ver usuarios retirados  ? </td>
    <td class="TxtTabla"><input name="pRetirado" id="pRetirado"  type="radio" value="1" 	<? 	if ($pRetirado == 1) { 	echo "checked"; } ?> />
      Retirados
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <input name="pRetirado" id="pRetirado"  type="radio" value="0"	<? //	if (($pRetirado == "0")) { 	echo "checked"; } ?> />
      Activos 
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <input name="pRetirado" id="pRetirado"  type="radio" value="2"	<? 	//if (($pRetirado == "") || ($pRetirado == 2)) { 	echo "checked"; } ?> />
      Todos</td>
  </tr>
	<tr>
	  <td width="10%" align="center" class="TituloTabla">Unidad</td>
	  <td class="TxtTabla">
	    <input name="unidades" type="text" class="CajaTexto" id="unidades" value="<? //$unidades ?>" /></td>
	  </tr>
	<tr>
	  <td width="10%" align="center" class="TituloTabla">Nombre</td>
	  <td class="TxtTabla">
	    <input name="nombres" type="text" class="CajaTexto" id="nombres" value="<? //$nombres ?>" /></td>
	  </tr>
	<tr>
	  <td width="15%" colspan="2" align="right" class="TxtTabla"><input name="Submit8" type="submit" class="Boton" value="Consultar"></td>
	  </tr>

</table>
	</td>
  </tr>
</table>
-->
	<? //include("bannerArriba.php") ; 
		//--Trae la cantidad de horas Segun contratos para el Horario Base, Horario de campo y Cat. 42.
		$sql15="SELECT * ";
		$sql15=$sql15." FROM horasydiaslaborales ";
		$sql15=$sql15." WHERE vigencia = " . $pAno;
		$sql15=$sql15." AND mes = " . $pMes; 
		$cursor15 =	 mssql_query($sql15);
		if ($reg15 = mssql_fetch_array($cursor15)) {
			$miHorasOficina = $reg15['hOficina'];
			$miHorasCampo = $reg15['hCampo'];
			$miHorasCat42 = $reg15['hCat42'];
		}
$vMeses= array("","Ene","Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"); 
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center">
		<table bgcolor="#FFFFFF" >
            <tr>
              <td colspan="2" class="TituloUsuario">Horas Mes</td>
            </tr>
            <tr>
              <td class="TituloTabla">Mes-Vigencia</td>
              <td class="TxtTabla">
			  <?  echo $vMeses[$pMes] . "-" . $pAno; ?>
			  </td>
            </tr>
            <tr>
              <td class="TituloTabla">Horas Mes-Vigencia </td>
              <td class="TxtTabla"><strong>Oficina</strong> [<? echo $miHorasOficina; ?>]
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Campo</strong> [<? echo $miHorasCampo; ?>]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>Categoría 42</strong> [<? echo $miHorasCat42 ?>]			  </td>
            </tr>
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
    <td class="TituloUsuario">Aprobaci&oacute;n Hojas de tiempo </td>
  </tr>
</table>


    
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td width="5%">Unidad</td>
        <td>Usuarios que solicita la revisi&oacute;n </td>
        <td width="2%">&nbsp;</td>
        <td>&nbsp;</td>
        </tr>

	<?php
        //si s4 ha consultado la facturacion, y no sse han encontrado registros
        if((mssql_num_rows($cursor)==0))
        {
    ?>
                    <tr class="TituloTabla2">
                            <td colspan="20" align="left" class="TxtTabla">&nbsp;</td>
      </tr>
                    <tr class="TituloTabla2">
    
                            <td colspan="20" align="left" class="TituloTabla2">No se encontraron registros. </td>
                  </tr>
    <?
        }

	  $cont_usu=1;
	  while ($reg=mssql_fetch_array($cursor)) {
	  ?>

	  <tr class="TxtTabla">
        <td width="5%"><? echo $reg[unidad]; ?></td>

        <td width="30%"><? echo ucwords(strtolower($reg[apellidos]  . " " . $reg[nombre])); ?>
          <input type="hidden" name="nombre_<?=$cont_usu ?>" id="nombre_<?=$cont_usu ?>" value="<?=ucwords(strtolower($reg[apellidos]  . " " . $reg[nombre])) ?>"  /></td>
        <td width="2%" align="center"><?
			if($reg[retirado]==1)
			{
?>          <img src="imagenes/Inactivo.gif" align="middle" title="Retirado de la compa&ntilde;ia"  />
          <?
			}
?></td>
        <td width="63%" align="center"><table width="100%" border="0" bgcolor="#FFFFFF">
          <tr class="TituloTabla2">
            <td colspan="5">&nbsp;</td>
            <td colspan="2">Aprobaci&oacute;n</td>
            </tr>
          <tr class="TituloTabla2">
            <td>Proyecto</td>
            <td width="5%">C.T.</td>
            <td width="10%"> <p>Horas <br /> Facturadas</p></td>
            <td width="5%">VoBo</td>
            <td width="10%">Total Horas</td>
            <td width="15%">VoBo</td>
            <td width="20%">Comentarios</td>
          </tr>

<?
			//CONSULTA LOS PROYECTOS, EN DONDE EL USUARIO HA FACTURADO, TOTALIZANDO LA CANTIDAD DE HORAS REGISTRADAS EN TODAS LAS ACTIVIDADES,  AGRUPANDO ESTA 
			//INFORMACION POR LA CLASE DE TIEMPO EN CADA PROYECTO
			//ADEMAS CONSULTA EL TOTAL DE HORAS FACTURADAS POR EL USUARIO, EN TODO LOS PROYECTOS, PARA LAS CLASES DE TIEMPO (1,2,3,11)
			$sql_fac_proy="select  Proyectos.nombre proy,clase_tiempo, SUM(horasMesF) horas_facturadas,unidad,FacturacionProyectos.id_proyecto
			,(select SUM(horasMesF) from FacturacionProyectos where mes=".$pMes." and vigencia=".$pAno." and unidad=".$reg[unidad]." and esInterno='I' and clase_tiempo in(1,2,3,11) ) total_horas_fact_CT
			from FacturacionProyectos 
			inner join Proyectos on Proyectos.id_proyecto=FacturacionProyectos.id_proyecto
			where mes=".$pMes." and vigencia=".$pAno."
			and unidad=".$reg[unidad]." and esInterno='I'
			group by unidad,clase_tiempo,FacturacionProyectos.id_proyecto,Proyectos.nombre 
			order by FacturacionProyectos.id_proyecto,unidad";

			$cur_fac_proy=mssql_query($sql_fac_proy);
//echo $sql_fac_proy." //*** ";
			$cant_usu_proy=mssql_num_rows($cur_fac_proy);
			$cont_reg_impreso=1; //CONTADOR INTERNO
			while($datos_fac_proy=mssql_fetch_array($cur_fac_proy))
			{
?>
                  <tr class="TxtTabla">
                    <td align="center"><?=$datos_fac_proy["proy"] ?></td>
                    <td width="5%" align="center"><?=$datos_fac_proy["clase_tiempo"] ?></td>
                    <td align="center"  ><?=$datos_fac_proy["horas_facturadas"] ?></td>
					<td width="5%" align="center">
					<?

                        //CONSULTA LA CANTIDAD DE ACTIVIDADES EN LAS QUE EL USUARIO  FACTURO
						// AL PROYECTO, Y QUE NO TIENEN VOBO (APROBADO), POR PARTE DEL DIRECTOR Y/O RESPONSABLE DE ACTIVIDAD EN EL PROYECTO
                        $sql_vob_proy="
                            select COUNT(*) cant_Acti_sin_vobo , (select hOficina from horasydiaslaborales where mes=".$pMes."  and vigencia=".$pAno.") cant_horas_mes  from FacturacionProyectos where mes=".$pMes." and vigencia=".$pAno." and unidad=".$reg[unidad]." and id_proyecto=".$datos_fac_proy["id_proyecto"]." 
                             and clase_tiempo=".$datos_fac_proy["clase_tiempo"]." and esInterno='I' and id_actividad not in (
                            select id_actividad from VoBoFactuacionProyHT where VoBoFactuacionProyHT.id_proyecto=FacturacionProyectos.id_proyecto
                            and VoBoFactuacionProyHT.id_actividad=FacturacionProyectos.id_actividad 
                            and VoBoFactuacionProyHT.unidad=FacturacionProyectos.unidad and VoBoFactuacionProyHT.vigencia=FacturacionProyectos.vigencia
                            and VoBoFactuacionProyHT.mes=FacturacionProyectos.mes and VoBoFactuacionProyHT.esInterno='I' and VoBoFactuacionProyHT.validaEncargado=1 )";
                    
                        $cur_vob_proy=mssql_query($sql_vob_proy);
                        $datos_vob_proy=mssql_fetch_array($cur_vob_proy);
//echo $datos_vob_proy["cant_Acti_sin_vobo"]." ** <BR> ".$sql_vob_proy;
						//SI TODAS LAS ACTIVIDADES EN LAS QUE EL USUARIO FACTURO EN EL PROYECTO, ESTAN APROBADAS, SE MUESTRA EL ICONO
                        if( ( (int) $datos_vob_proy["cant_Acti_sin_vobo"] )==0 )
                        {
                    ?>
		                      <img src="img/images/Si.gif" width="16" height="14" title="Facturación Aprobada" />
                    <?
                        }
                        if( ( (int) $datos_vob_proy["cant_Acti_sin_vobo"] )>0 )
						{
                    ?>
		                      <img src="img/images/icoAlerta.gif" width="16" height="14" title="Hay facturacion del usuario, sin aprobar en el proyecto." />
                    <?
						}


                    ?>
					</td>
<?
					//SI ES EL PRIMER REGISTRO QUE SE IMPRIME, SE MUESTRA LA CANTIDA DE HORAS QUE DEBE FACTURAR EN OFICINAS
					if($cont_reg_impreso==1)
					{
						
?>
	                    <td width="10%"  rowspan="<?=$cant_usu_proy ?>" align="center" id="total_horas_factu" 
<?
						//SI LA CANTIDAD DE HORAS FACTURADAS POR EL USUARIO EN TODOS LOS PROYECTOS, CON CLASE DE TIEMPO (1,2,3,11)
						//ES INFERIOR A LA CANTIDAD DE HORAS A FACTURAR EN OFICINA, ENTONCES IMPRIME EL TEXTO EN COLOR ROJO
						if( ((int) $datos_fac_proy["total_horas_fact_CT"])< ((int) $datos_vob_proy["cant_horas_mes"]) )
							{ echo 'style="color:#FF0000"'; echo "title='La sumatoria de horas facturadas en las clases de tiempo 1, 2, 3 y 11 son inferiores a las Horas mes, que debe registrar para el mes seleccionado.'"; } ?>  >
								<?=$datos_fac_proy["total_horas_fact_CT"]; ?>
								<img src="/NuevaHojaTiempo/imagenes/icoDetalleInf.gif" title="Se totaliza la facturaci&oacute;n, para las clases de tiempo 1, 2, 3 y 11." /></td>

                        <td align="center" rowspan="<?=$cant_usu_proy ?>">Si
                          <input type="radio" name="vobo_<?=$reg[unidad] ?>" id="vobo_<?=$reg[unidad] ?>" value="1" /> 
                          No
                          <input type="radio" name="vobo_<?=$reg[unidad] ?>" id="vobo_<?=$reg[unidad] ?>" value="0" />
    <label for="vobo"></label></td>
                    <td align="center" rowspan="<?=$cant_usu_proy ?>" ><label for="comentarios"></label>
                      <textarea name="comentarios_<?=$cont_usu ?>" cols="30" rows="3" class="CajaTexto" id="comentarios_<?=$cont_usu ?>"></textarea>
					  <input type="hidden" name="unidad_<?=$cont_usu ?>" id="unidad_<?=$cont_usu ?>" value="<?=$reg[unidad] ?>"  />

					</td>
<?
					}
?>

              </tr>

<?
				$cont_reg_impreso++;

			}
			$cont_usu++;

?>

        </table></td>
        </tr>
	  <? } ?>
    </table>
	<?php
        //si s4 ha consultado la facturacion, y no sse han encontrado registros
        if((mssql_num_rows($cursor)!=0))
        {
    ?>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">


          <tr>
            <td align="right" class="TxtTabla"><br /><input name="limpia" type="button" class="Boton" id="limpia" value="Limpiar" onClick="limpiar();" /> <input type="hidden"  value="<?=mssql_num_rows($cursor); ?>" name="cant_usu_facturados" id="cant_usu_facturados"  />
            <input type="hidden"  value="1" name="recarga" id="recarga"  /> <input name="grabar" type="button" class="Boton" id="grabar" value="Grabar" onClick="valida();" /></td>
          </tr>

        </table>
<?
		}
?>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
          </tr>
        </table></td>
      </tr>
    </table>
</form>
</body>
</html>
