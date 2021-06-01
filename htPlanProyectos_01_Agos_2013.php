<?php


	if ($ban==2)
	{
		$pagina=1;
		header('Location: http://www.ingetec.com.co/NuevaHojaTiempo/htPlanProyectos.php?pagina=1&pNombre='.$pNombre.'&cod='.$cod.'&cargo='.$cargo.'&pOrden='.$pOrden.'&pProyecto='.$pProyecto);
/*	window.location="http://www.ingetec.com.co/NuevaHojaTiempo/htPlanProyectos.php?pagina=1&pNombre="+document.form1.pNombre.value+"&cod="+document.form1.cod.value+"&cargo="+document.form1.cargo.value+"&pOrden="+document.form1.pOrden.value+"&pProyecto="+document.form1.pProyecto.value+"";
*/
	}

session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";
//exit;	

//10jUN2008
//Si es perfil = 1 administrador y/o se trata de Camilo Marulanda muestra todos los proyectos
//de lo contrario sólo muestra los proyectos de la persona activa
//El listado de proyectos va a estar visible para
//Director
//Coordinador
//Ordenadores del gasto
//Programadores
//Responsables de actividad
//if (($_SESSION["sesPerfilUsuario"] == "1") OR ($laUnidad == "14384")) {
if ($_SESSION["sesPerfilUsuario"] == "1")  {
	$sql="SELECT P.*, D.nombre nombreD, D.apellidos apellidosD, C.nombre nombreC, C.apellidos apellidosC  ";
	$sql=$sql." FROM proyectos P, Usuarios D, Usuarios C " ;
	$sql=$sql." WHERE P.id_director *= D.unidad " ;
	$sql=$sql." AND P.id_coordinador *= C.unidad " ;

	$sqlTOP="SELECT top(30) P.*, D.nombre nombreD, D.apellidos apellidosD, C.nombre nombreC, C.apellidos apellidosC  ";
	$sqlTOP=$sqlTOP." FROM proyectos P, Usuarios D, Usuarios C " ;
	$sqlTOP=$sqlTOP." WHERE P.id_director *= D.unidad " ;
	$sqlTOP=$sqlTOP." AND P.id_coordinador *= C.unidad " ;
}
else {
	$sql="SELECT P.*, D.nombre nombreD, D.apellidos apellidosD, C.nombre nombreC, C.apellidos apellidosC  ";
	$sql=$sql." FROM ( " ;
	$sql=$sql." 	Select id_proyecto " ;
	$sql=$sql." 	from HojaDeTiempo.dbo.Proyectos " ;
	$sql=$sql." 	where id_director = " . $laUnidad . " or id_coordinador = " . $laUnidad ;
	$sql=$sql." 	and id_estado = 2 " ;
	$sql=$sql." 	UNION " ;
	$sql=$sql." 	Select id_proyecto " ;
	$sql=$sql." 	from HojaDeTiempo.dbo.Programadores " ;
	$sql=$sql." 	where unidad = " . $laUnidad ;
	$sql=$sql." 	UNION " ;
	$sql=$sql." 	select id_proyecto " ;
	$sql=$sql." 	from GestiondeInformacionDigital.dbo.OrdenadorGasto " ;
	$sql=$sql." 	where unidadOrdenador = " . $laUnidad ;
	$sql=$sql." 	and id_proyecto is not null " ;
	$sql=$sql." 	UNION " ;
	$sql=$sql." 	select id_proyecto  " ;
	$sql=$sql." 	from HojaDeTiempo.dbo.actividades " ;
	$sql=$sql." 	where id_encargado = " . $laUnidad ;
	$sql=$sql." 	UNION " ;
	$sql=$sql." 	select id_proyecto " ;
	$sql=$sql." 	from HojaDeTiempo.dbo.ResponsablesActividad " ;
	$sql=$sql." 	where unidad = " . $laUnidad ;
	$sql=$sql." ) A, Proyectos P, Usuarios D, Usuarios C " ;
	$sql=$sql." WHERE A.id_proyecto = P.id_proyecto " ;
	$sql=$sql." AND P.id_director *= D.unidad " ;
	$sql=$sql." AND P.id_coordinador *= C.unidad " ;
	$sql=$sql." AND P.id_estado = 2" ;

	$sqlTOP="SELECT TOP(30) P.*, D.nombre nombreD, D.apellidos apellidosD, C.nombre nombreC, C.apellidos apellidosC  ";
	$sqlTOP=$sqlTOP." FROM ( " ;
	$sqlTOP=$sqlTOP." 	Select id_proyecto " ;
	$sqlTOP=$sqlTOP." 	from HojaDeTiempo.dbo.Proyectos " ;
	$sqlTOP=$sqlTOP." 	where id_director = " . $laUnidad . " or id_coordinador = " . $laUnidad ;
	$sqlTOP=$sqlTOP." 	and id_estado = 2 " ;
	$sqlTOP=$sqlTOP." 	UNION " ;
	$sqlTOP=$sqlTOP." 	Select id_proyecto " ;
	$sqlTOP=$sqlTOP." 	from HojaDeTiempo.dbo.Programadores " ;
	$sqlTOP=$sqlTOP." 	where unidad = " . $laUnidad ;
	$sqlTOP=$sqlTOP." 	UNION " ;
	$sqlTOP=$sqlTOP." 	select id_proyecto " ;
	$sqlTOP=$sqlTOP." 	from GestiondeInformacionDigital.dbo.OrdenadorGasto " ;
	$sqlTOP=$sqlTOP." 	where unidadOrdenador = " . $laUnidad ;
	$sqlTOP=$sqlTOP." 	and id_proyecto is not null " ;
	$sqlTOP=$sqlTOP." 	UNION " ;
	$sqlTOP=$sqlTOP." 	select id_proyecto  " ;
	$sqlTOP=$sqlTOP." 	from HojaDeTiempo.dbo.actividades " ;
	$sqlTOP=$sqlTOP." 	where id_encargado = " . $laUnidad ;
	$sqlTOP=$sqlTOP." 	UNION " ;
	$sqlTOP=$sqlTOP." 	select id_proyecto " ;
	$sqlTOP=$sqlTOP." 	from HojaDeTiempo.dbo.ResponsablesActividad " ;
	$sqlTOP=$sqlTOP." 	where unidad = " . $laUnidad ;
	$sqlTOP=$sqlTOP." ) A, Proyectos P, Usuarios D, Usuarios C " ;
	$sqlTOP=$sqlTOP." WHERE A.id_proyecto = P.id_proyecto " ;
	$sqlTOP=$sqlTOP." AND P.id_director *= D.unidad " ;
	$sqlTOP=$sqlTOP." AND P.id_coordinador *= C.unidad " ;
	$sqlTOP=$sqlTOP." AND P.id_estado = 2" ;
}


	$sqlFilt=$sqlFilt." where id_proyecto <> ''  ";

if (trim($pNombre) != "") {
	$sqlTOP=$sqlTOP." and P.nombre like '%".trim($pNombre)."%' " ;
	$sql=$sql." and P.nombre like '%".trim($pNombre)."%' " ;
	$sqlFilt=$sqlFilt." and P.nombre like '%".trim($pNombre)."%' " ;

}



if (trim($cargo) != "") {
	$sqlTOP=$sqlTOP." and P.cargo_defecto= '".$cargo."' " ;
	$sql=$sql." and P.cargo_defecto= '".$cargo."' " ;
	$sqlFilt=$sqlFilt." and P.cargo_defecto= '".$cargo."' " ;

}

if (trim($cod) != "") {
	$sqlTOP=$sqlTOP." and P.codigo= '".$cod."' " ;
	$sql=$sql." and P.codigo= '".$cod."' " ;
	$sqlFilt=$sqlFilt." and P.codigo= '".$cod."' " ;

}

if (trim($pProyecto) == 2) {
	$sqlTOP=$sqlTOP." AND P.especial is not null " ;
	$sql=$sql." AND P.especial is not null " ;
	$sqlFilt=$sqlFilt." AND P.especial is not null " ;

}
/*
if ($pOrden == 1) {
	$sqlTOP=$sqlTOP." ORDER BY P.nombre  " ;
	$sql=$sql." ORDER BY P.nombre  " ;
//	$sqlFilt=$sqlFilt." ORDER BY P.nombre  " ;
}
else {
	$sqlTOP=$sqlTOP." ORDER BY P.codigo, P.cargo_defecto " ;
	$sql=$sql." ORDER BY P.codigo, P.cargo_defecto " ;
}
*/

if(trim($pagina) == ""){
	$pagina = 1;
	$inicio = 0;
}
else{
	$inicio = 30*($pagina - 1);
}
$sqlTOP = $sqlTOP." AND P.id_proyecto NOT IN";
$sqlTOP = $sqlTOP. " ( SELECT TOP " . $inicio . " P.id_proyecto FROM HojaDeTiempo.dbo.Proyectos P ".$sqlFilt."  ORDER BY P.nombre )";
$sqlTOP = $sqlTOP. "  ORDER BY P.nombre ";


$cursorTOP = mssql_query($sqlTOP);
$cursor =	 mssql_query($sql);
//echo $sqlTOP."",mssql_get_last_message();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
<!--
window.name="winHojaTiempo";

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
<title>.:: Planeaci&oacute;n de Proyectos</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota2" style="position:absolute; left:3px; top:55px; width: 448px; height: 30px;">
Planeaci&oacute;n de proyectos</div>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table><table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
    <table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td class="TituloUsuario">Criterios de consulta </td>
      </tr>
    </table>


    <table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellpadding="0" cellspacing="1">
    <form name="form1" id="form1" method="post" action="">	



      <tr>
        <td width="20%" class="TituloTabla">Ordenar por </td>
        <td class="TxtTabla">
		<?
		if ($pOrden == 1) {
			$selOrden1 = "checked";
			$selOrden2 = "";
		}
		else {
			$selOrden1 = "";
			$selOrden2 = "checked";
		}
		?>
		<input name="pOrden" type="radio" value="1" <? echo $selOrden1; ?>  onClick="document.form1.submit();" />
          Nombre 
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <input name="pOrden" type="radio" value="2" <? echo $selOrden2; ?> onClick="document.form1.submit();" />
          C&oacute;digo</td>
        <td width="2%" class="TxtTabla">&nbsp;</td>
      </tr>
      <tr>
        <td class="TituloTabla">Proyectos</td>
        <td class="TxtTabla"><?
		if (($pProyecto == 1) or (trim($pProyecto) == "")) {
			$selP1 = "checked";
			$selP2 = "";
		}
		else {
			$selP1 = "";
			$selP2 = "checked";
		}
		?>
          <input name="pProyecto" type="radio" value="1" <? echo $selP1; ?>  onClick="document.form1.submit();" />
Todos &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input name="pProyecto" type="radio" value="2" <? echo $selP2; ?>   onClick="document.form1.submit();" />
Especial</td>
        <td class="TxtTabla">&nbsp;</td>
      </tr>
      <tr>
        <td class="TituloTabla">Nombre</td>
        <td class="TxtTabla"><input name="pNombre" type="text" class="CajaTexto" value="<? echo $pNombre; ?>" id="pNombre" size="70" /> </td>
        <td width="2%" class="TxtTabla"></td>
      </tr>

      <tr>
        <td class="TituloTabla">Codigo</td>
        <td class="TxtTabla"><input name="cod" type="text" value="<? echo $cod; ?>" class="CajaTexto" id="cod" size="20" /> .
							<input name="cargo" type="text" class="CajaTexto" value="<? echo $cargo; ?>" id="cargo" size="20" />

		 </td>
        <td width="2%" class="TxtTabla"><input name="submit"  type="submit" onclick=" envia();" class="Boton" value="Consultar" /></td>
<?
	echo $ban;
?>
<input type="hidden"  name="ban" id="ban"  value="1" />

      </tr>
    </form>	  
    </table></td>
      </tr>
    </table>

<script type="text/javascript" language="javascript">

function envia()
{
	document.form1.ban.value=2;


}

//-->
</script>



<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
	<td height="1" align="center" class="TituloTabla"> </td>
  </tr>
  <tr>
	<td align="center" class="TxtTabla">
	<?
	//$pSql="SELECT * FROM CSEFichaPredio " ;
	//$pCursor = mssql_query($pSql) ;

	$totalRegistros = mssql_num_rows($cursor);
	$totalPaginas = ceil($totalRegistros/30);
	for ($p=1; $p<= $totalPaginas; $p++) 
	{
		if($p==$pagina){
			$clase = "menu3";
		}
		else{
			$clase = "menu";
		}
		echo "<a href='htPlanProyectos.php?pagina=".$p."&pNombre=".$pNombre."&cod=".$cod."&cargo=".$cargo."&pOrden=".$pOrden."&pProyecto=".$pProyecto."' class='".$clase."'>".$p."</a> | ";
	}
	?>
	</td>
  </tr>
  <tr>
	<td height="1" align="center" class="TituloTabla"> </td>
  </tr>
</table>
	

    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="right"><a href="admHorarios.php" target="_blank" class="menu">.: Administraci&oacute;n Sistemas :. </a> </td>
      </tr>
    </table>
<!-- No. de Registros -->
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TxtTabla">&nbsp;
	</td>
  </tr>
  <tr>
    <td class="TxtTabla"><strong>Número de resultados:&nbsp;<? echo mssql_num_rows($cursor); ?></strong>
<?php
	$sql_divs_dir_sub="select * from Divisiones where id_director=".$laUnidad." or id_subdirector=".$laUnidad;
//	$cur_divs_dir_sub=mssql_query($sql_divs_dir_sub);
	//if(mssql_num_rows($cur_divs_dir_sub)!="")
	{
?>
		</td><td width="50%" align="right">   <input name="consolidado" type="button" class="Boton" value="Consolidados por división" onclick="MM_goToURL('parent','htPlanProyectoConsolidadoDiv.php');return document.MM_returnValue" />
<?php
	}
?>
	</td>
  </tr>
  <tr>
    <td class="TxtTabla">
	</td>
  </tr>
</table>
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">   Planeaci&oacute;n de proyectos para <? echo strtoupper($nombreempleado." ".$apellidoempleado); 	?></td>
  </tr>
  <tr>
    <td align="right" class="TxtTabla">

	</td>
  </tr>
</table>


    
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellspacing="2" cellpadding="1">
      <tr class="TituloTabla2">
        <td width="1%">&nbsp;</td>
        <td width="5%">ID</td>
        <td width="25%">Proyectos</td>
        <td>C&oacute;digo</td>
        <td width="20%">Encargados</td>
        <td width="5%">Planeaci&oacute;n Vs Facturaci&oacute;n </td>
        <td width="5%">&nbsp;</td>
        <td width="3%">&nbsp;</td>
        </tr>
       <?
	  while ($reg=mssql_fetch_array($cursorTOP)) {
	  ?>
	   <tr class="TxtTabla">
	     <td width="1%" valign="top">
            <?
			//La opción solo aparece para el perfil del Administrador, Perfil = 1
			if ($_SESSION["sesPerfilUsuario"] == 1 ) {
			?>
		    <a href="#"><img src="img/images/actualizar.jpg" alt="Modificar" width="19" height="17" border="0" onclick="MM_openBrWindow('upProyecto.php?cualProyecto=<? echo $reg[id_proyecto]; ?>','upProg','scrollbars=yes,resizable=yes,width=500,height=150')" /></a>
		<? } ?>		 
		 </td>
	    <td width="5%" valign="top"><? echo  $reg[id_proyecto] ; ?></td>
        <td width="25%" valign="top"><? echo  ucwords(strtolower($reg[nombre])) ; ?></td>
        <td valign="top">
		<?
		//27Ene2009
		//Traer los cargos adicionales del proyecto
		$sqlCargos="SELECT * FROM HojaDeTiempo.dbo.Cargos ";
		$sqlCargos=$sqlCargos." where id_proyecto = " . trim($reg[id_proyecto]) ;
		$cursorCargos = mssql_query($sqlCargos);
//		while ($regCargos=mssql_fetch_array($cursorCargos)) {
//			echo  "<br>". "." . $regCargos[cargos_adicionales] . " [" . $regCargos[descripcion] . "] " ;
//		}
		
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
        <td width="20%" valign="top">
		<? echo  ucwords(strtolower($reg[nombreD])) . " " . ucwords(strtolower($reg[apellidosD])) . "<br>" ; ?>
		<? echo  ucwords(strtolower($reg[nombreC])) . " " . ucwords(strtolower($reg[apellidosC])) . "<br>" ; ?>
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
		
		?>
		</td>
        <td width="5%"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr class="TituloUsuario">
                <td colspan="2">VALORES DEL PROYECTO </td>
              </tr>
              <tr class="TxtTabla">
                <td width="50%">Total</td>
                <td align="right">$<? echo number_format($reg["valorProyecto"],0,",",".") ; ?></td>
              </tr>
              <tr class="TxtTabla">
                <td width="50%">Asignado</td>
                <td align="right">
				<?
					$valor_asign=0;
					//Trae la información de la Asignación de los valores por división.
					$sql06="Select SUM(valorAsignado) as val_div_asig ";
					$sql06=$sql06." from AsignaValorDivision ";
					$sql06=$sql06." where id_proyecto = ".$reg[id_proyecto] ;
					$cursor06 = mssql_query($sql06);
					if($datos_val_asig=mssql_fetch_array($cursor06 ))
						$valor_asign=$datos_val_asig["val_div_asig"];

					echo "$".number_format($valor_asign,0,",",".");
				?>
				</td>
				</tr>
				<tr>
                <td width="50%">Planeado</td>
                <td align="right">
				<?
					$valor_asign=0;
					//Trae la información de la Asignación de los valores por división.
					$sql06="Select SUM(ValorPlaneado) as val_div_plane ";
					$sql06=$sql06." from PlaneacionProyectos ";
					$sql06=$sql06." where id_proyecto = ".$reg[id_proyecto] ;
					$cursor06 = mssql_query($sql06);
					if($datos_val_asig=mssql_fetch_array($cursor06 ))
						$valor_asign=$datos_val_asig["val_div_plane"];

					echo "$".number_format($valor_asign,0,",",".");
				?>
				</td>
              </tr>
              <tr class="TxtTabla">
                <td height="3" colspan="2" class="TituloUsuario"> </td>
              </tr>
          </table>          <img src="imagenes/imgPvsE.jpg" width="274" height="200" /></td>
        <td width="5%"><input name="Submit9" type="submit" class="Boton" value="Configuraci&oacute;n" onclick="MM_goToURL('parent','htPlanProyectoConfig.php?cualProyecto=<? echo $reg[id_proyecto]; ?>');return document.MM_returnValue"  style='width:150px; '   />		
		<input name="Submit" type="submit" class="Boton" onclick="MM_goToURL('parent','htPlanProyectos01.php?cualProyecto=<? echo $reg[id_proyecto]; ?>');return document.MM_returnValue" value="Gesti&oacute;n del Proyecto"  style='width:150px; ' />
          <br />
          <input name="Submit5" type="submit" class="Boton" onclick="MM_goToURL('parent','ProgProyectosActiv.php?cualProyecto=<? echo $reg[id_proyecto]; ?>');return document.MM_returnValue" value="Actividades" />          
          <br />
          </td>
        <td width="3%" align="center">
		<?
		$sqlValida = " SELECT COUNT(*) AS estaAutorizado FROM ( ";
		$sqlValida = $sqlValida . " SELECT id_director AS unidad FROM HojaDetiempo.dbo.Proyectos ";
		$sqlValida = $sqlValida . " WHERE id_proyecto = " . $reg['id_proyecto'];
		$sqlValida = $sqlValida . " UNION ";
		$sqlValida = $sqlValida . " SELECT id_coordinador AS unidad FROM HojaDetiempo.dbo.Proyectos ";
		$sqlValida = $sqlValida . " WHERE id_proyecto = " . $reg['id_proyecto'];
		$sqlValida = $sqlValida . " UNION ";
		$sqlValida = $sqlValida . " SELECT unidad FROM HojaDetiempo.dbo.Programadores ";
		$sqlValida = $sqlValida . " WHERE id_proyecto = " . $reg['id_proyecto'];
		$sqlValida = $sqlValida . " UNION ";
		$sqlValida = $sqlValida . " SELECT unidadOrdenador AS unidad FROM GestiondeInformacionDigital.dbo.OrdenadorGasto ";
		$sqlValida = $sqlValida . " WHERE id_proyecto = " . $reg['id_proyecto'];
		$sqlValida = $sqlValida . " ) AS A ";
		$sqlValida = $sqlValida . " WHERE A.unidad = " . $_SESSION["sesUnidadUsuario"];
		$cursorValida = mssql_query($sqlValida);
		if($regValida = mssql_fetch_array($cursorValida)){
			$estaAutorizado = $regValida['estaAutorizado'];
		} else {
			$estaAutorizado = 0;
		}
		
		//Para volver a activar, se debe comentariar esta línea y quitar el comentario de la siguiente
		//if($_SESSION["sesUnidadUsuario"] == 16374 or $_SESSION["sesUnidadUsuario"] == 15712){
		if($estaAutorizado != 0 || $_SESSION["sesPerfilUsuario"] == 1){
		?>
		<a href="#"><img src="imagenes/imgPrint2.gif" alt="Autorizar usuarios para impresi&oacute;n" width="20" height="19" border="0" onclick="MM_goToURL('parent','ProgProyectosImpresion.php?cualProyecto=<? echo $reg[id_proyecto]; ?>');return document.MM_returnValue" /></a>
		<? } ?>
		</td>
	    </tr>
	  <? } ?>
	<? 
	//Para que este proyecto siempre le aparezca a Olga Lucia
	if ($laUnidad == 15320) { ?>
	 <? } ?> 
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
    <td><input name="Submit2" type="submit" class="Boton" onclick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value="P&aacute;gina principal Hoja de tiempo" /></td>
    <td align="right">&nbsp;
	</td>
  </tr>
</table>
</body>
</html>
