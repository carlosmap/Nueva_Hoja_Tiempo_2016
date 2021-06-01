<?php
	session_start();
	//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
	include "funciones.php";
	include "validacion.php";
	include "validaUsrBd.php";
	
	//Trae los registros de las divisiones
	@mssql_select_db("HojaDeTiempo",$conexion);
	$sqlDiv="Select * from divisiones ";
	$sqlDiv=$sqlDiv." where (nombre <> '' and nombre <> 'sd') ";
	$sqlDiv=$sqlDiv." order by nombre ";
	$cursorDiv = mssql_query($sqlDiv);
	
	#	Consulta modificada para traer las hojas de tiempo con firmas
	$sql='';
	//Lista los usuarios por división para conocer su Hoja de tiempo del periodo seleccionado
	$sql=$sql.'SELECT A.* FROM (';
	$sql=$sql."SELECT 
				month(U.fechaIngreso) mesIngreso, year(U.fechaIngreso) anioIngreso, U.fechaIngreso, 
				month(U.fechaRetiro) mesRetiro, year(U.fechaRetiro) anioRetiro, U.fechaRetiro, U.retirado, 
				U.unidad, U.nombre, U.apellidos, U.id_departamento, U.id_categoria, ";
	$sql=$sql." C.nombre nomCategoria, D.nombre nomDpto, A.vigencia, A.mes, A.fechaEnvio,  " ;
	$sql=$sql." A.unidadJefe, A.validaJefe, A.unidadContratos, A.validaContratos, A.comentaContratos, A.comentaJefe " ;
#	$sql=$sql." from usuarios U, categorias C, departamentos D, AutorizacionesHT A " ;
	$sql=$sql." FROM usuarios U, categorias C, departamentos D, VoBoFirmasHT A " ;
	$sql=$sql." WHERE U.id_categoria = C.id_categoria " ;
	$sql=$sql." AND U.id_departamento = D.id_departamento " ;
	$sql=$sql." AND U.unidad *= A.unidad " ;
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
	
	//**********************	
	//filtra la primera vez para administrativo, luego para la división seleccionada en la lista
	if (trim($pDivision) == "" ) {
		$sql= $sql. " AND D.id_division = 11 ";
		if (trim($miDpto) != "") { 
			$sql= $sql. " AND D.id_departamento = " . $miDpto ;
		}
		$pDivision = 11;
	}
	else {
		if ($pDivision == "888") { 
			$sql=$sql. " AND D.id_division > 25 " ;
		}
		else {
			$sql=$sql." AND D.id_division =" . $pDivision;
	
			if (trim($miDpto) != "") { 
				$sql= $sql. " AND D.id_departamento = " . $miDpto ;
			}
		}
	}
	//***********************
	if (trim($pFiltro) != "") {
		$sql=$sql." AND U.id_categoria =  " . $pFiltro ;
	}
	#$sql=$sql." and U.retirado is null " ;
	$sql=$sql." ) A" ;
	
	if($envJefe!='')
	{
		$flt='';
		if($envJefe==1)
		{
			$flt=' NOT ';
		}
		$sql=$sql." WHERE A.unidadJefe IS ".$flt." NULL" ;
	}
	
	if($sJefe!='')
	{
		$fJefe=0;
		if($sJefe==1)
		{
			$fJefe=1;
		}

		if($envJefe!='')
		{
			$sql=$sql." AND A.validaJefe = ".$fJefe." AND A.unidadJefe IS NOT NULL";
		}
		else
		{
			#$sql=$sql." WHERE A.comentaContratos IS ".$fJefe." NULL AND A.unidadJefe IS NOT NULL" ;
			$sql=$sql." WHERE A.validaJefe = ".$fJefe." AND A.unidadJefe IS NOT NULL";
		}
	}
	
	if($sContratos!='')
	{
		$fContratos=0;
		if($sContratos==1)
		{
			$fContratos=1;
		}

		if($sJefe!=''||$envJefe!='')
		{
			$sql=$sql." AND A.validaContratos = ".$fContratos;
			#$sql=$sql." AND A.validaJefe = 1 AND A.unidadJefe IS NOT NULL " ;
		}
		else
		{
			$sql=$sql." WHERE A.validaContratos = ".$fContratos;
			$sql=$sql." AND A.validaJefe = 1 AND A.unidadJefe IS NOT NULL " ;
		}
	}
	#	sJefe sContratos
	$sql=$sql." ORDER BY A.apellidos  ";
	#echo $sql;
	$cursor = mssql_query($sql);
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
function fltProyecto()
{
	document.getElementById('fProyectos').value=1;
	document.form1.submit();
}
function enviar()
{
	document.getElementById('fProyectos').value='';
	document.form1.submit();
}

</script>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Revisión de hojas de tiempo</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota1" style="position:absolute; left:258px; top:11px; width: 365px;">
		<div align="center">Hojas de tiempo por División</div>
	</div>
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
    <td class="TituloUsuario">Consulta de otros periodos </td>
  </tr>
</table>
<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td bgcolor="#FFFFFF">
	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="1">
	<form name="form1" id="form1" method="post" action="">
  <tr>
    <td width="25%" align="right" class="TituloTabla">Divisi&oacute;n:</td>
    <td colspan="3" class="TxtTabla">
	<select name="pDivision" class="CajaTexto" onChange="document.form1.submit();" >
	<? while ($regDiv=mssql_fetch_array($cursorDiv)) { 	
			if ($pDivision == $regDiv[id_division]) {
				$selDiv = "selected";
			}
			else {
				$selDiv = "";
			}
	
	?>
      	<option value="<? echo $regDiv[id_division]; ?>" <? echo $selDiv; ?> ><? echo ucwords(strtolower($regDiv[nombre])) ; ?></option>
	<? } ?> 
	
	<? if ($pDivision == "888") { 
			$selDiv = "selected";
		}
	?>
	<option value="888" <? echo $selDiv; ?> ><? echo ":::Sin División:::" ; ?></option>
	
    </select>
	</td>
    <td class="TxtTabla">&nbsp;</td>
  </tr>
  <tr>
    <td width="25%" align="right" class="TituloTabla">Departamento:</td>
    <td colspan="3" class="TxtTabla">
	<?
	//Trae los departamentos asociados la división seleccionada
	$dTSql="SELECT * FROM departamentos WHERE id_division = " . $pDivision;
	$dTSql=$dTSql." AND estadoDpto LIKE 'A' ORDER BY nombre";
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
    </select>
	</td>
    <td class="TxtTabla">&nbsp;</td>
  </tr>
  <tr>
    <td width="25%" align="right" class="TituloTabla">Categor&iacute;a:</td>
    <td colspan="3" class="TxtTabla"><? 
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
    <td class="TxtTabla">&nbsp;</td>
  </tr>
  <tr>
    <td align="right" class="TituloTabla">Env&iacute;ada a Jefe</td>
    <td colspan="3" class="TxtTabla">
    <select name="envJefe" id="envJefe" class="CajaTexto" onChange="document.form1.submit();">
    	<option value="">:::Todos:::</option>
        <?
			$selEJefe1 = $selEJefe2 = '';
			switch($envJefe)
			{
				case 1:
					$selEJefe1 = 'selected';
					$selEJefe2 = '';
				break;
				
				case 2:
					$selEJefe1 = '';
					$selEJefe2 = 'selected';
				break;
				
			}
			#selected
		?>
        <option <?= $selEJefe1 ?> value="1">Sí</option>
        <option <?= $selEJefe2 ?> value="2">No</option>
    </select>    
    </td>
    <td class="TxtTabla">&nbsp;</td>
  </tr>
  <tr>
    <td width="25%" align="right" class="TituloTabla">Aprobaci&oacute;n Jefe</td>
    <td colspan="3" class="TxtTabla">
    <select name="sJefe" id="sJefe" class="CajaTexto" onChange="document.form1.submit();">
    	<option value="">:::Todos:::</option>
        <?
			$selJefe1 = $selJefe2 = '';
			switch($sJefe)
			{
				case 1:
					$selJefe1 = 'selected';
					$selJefe2 = '';
				break;
				
				case 2:
					$selJefe1 = '';
					$selJefe2 = 'selected';
				break;
				
			}
			#selected
		?>
        <option <?= $selJefe1 ?> value="1">Sí</option>
        <option <?= $selJefe2 ?> value="2">No</option>
    </select>
    </td>
    <td class="TxtTabla">&nbsp;</td>
  </tr>
  <tr>
    <td width="25%" align="right" class="TituloTabla">Aprobaci&oacute;n Contratos</td>
    <td colspan="3" class="TxtTabla">
    <select name="sContratos" id="sContratos" class="CajaTexto" onChange="document.form1.submit();">
         <?
			$selCon1 = $selCon2 = '';
			switch($sContratos)
			{
				case 1:
					$selCon1 = 'selected';
					$selCon2 = '';
				break;
				
				case 2:
					$selCon1 = '';
					$selCon2 = 'selected';
				break;
				
			}
			#selected
		?>
   	<option value="">:::Todos:::</option>
        <option <?= $selCon1 ?> value="1">Sí</option>
        <option <?= $selCon2 ?> value="2">No</option>
    </select>
    </td>
    <td class="TxtTabla">&nbsp;</td>
  </tr>
  <tr>
    <td width="25%" align="right" class="TituloTabla">Mes:&nbsp;</td>
    <td width="30%" class="TxtTabla">
	<? 
	//Seleccionar el mes cuando se carga la página por primera vez
	//si no cuando se recarga la página
	if ($pMes == "") {
		$mesActual=date("m"); //el mes actual
	}
	else {
		$mesActual= $pMes; //el mes seleccionado
	}

	$selMes1 = "";
	$selMes2 = "";
	$selMes3 = "";
	$selMes4 = "";
	$selMes5 = "";
	$selMes6 = "";
	$selMes7 = "";
	$selMes8 = "";
	$selMes9 = "";
	$selMes10 = "";
	$selMes11 = "";
	$selMes12 = "";
	for($m=1; $m<=12; $m++) {
		if (($m == $mesActual) AND ($m == 1)) {
			$selMes1 = "selected";
		}
		if (($m == $mesActual) AND ($m == 2)) {
			$selMes2 = "selected";
		}
		if (($m == $mesActual) AND ($m == 3)) {
			$selMes3 = "selected";
		}
		if (($m == $mesActual) AND ($m == 4)) {
			$selMes4 = "selected";
		}
		if (($m == $mesActual) AND ($m == 5)) {
			$selMes5 = "selected";
		}
		if (($m == $mesActual) AND ($m == 6)) {
			$selMes6 = "selected";
		}
		if (($m == $mesActual) AND ($m == 7)) {
			$selMes7 = "selected";
		}
		if (($m == $mesActual) AND ($m == 8)) {
			$selMes8 = "selected";
		}
		if (($m == $mesActual) AND ($m == 9)) {
			$selMes9 = "selected";
		}
		if (($m == $mesActual) AND ($m == 10)) {
			$selMes10 = "selected";
		}
		if (($m == $mesActual) AND ($m == 11)) {
			$selMes11 = "selected";
		}
		if (($m == $mesActual) AND ($m == 12)) {
			$selMes12 = "selected";
		}
	}
	
	?>
	&nbsp;      <select name="pMes" class="CajaTexto" id="pMes">
      <option value="1" <? echo $selMes1; ?> >Enero</option>
      <option value="2" <? echo $selMes2; ?>>Febrero</option>
      <option value="3" <? echo $selMes3; ?>>Marzo</option>
      <option value="4" <? echo $selMes4; ?>>Abril</option>
      <option value="5" <? echo $selMes5; ?>>Mayo</option>
      <option value="6" <? echo $selMes6; ?>>Junio</option>
      <option value="7" <? echo $selMes7; ?>>Julio</option>
      <option value="8" <? echo $selMes8; ?>>Agosto</option>
      <option value="9" <? echo $selMes9; ?>>Septiembre</option>
      <option value="10" <? echo $selMes10; ?>>Octubre</option>
      <option value="11" <? echo $selMes11; ?>>Noviembre</option>
      <option value="12" <? echo $selMes12; ?>>Diciembre</option>
    </select></td>
    <td width="15%" align="right" class="TituloTabla">A&ntilde;o:&nbsp;</td>
    <td class="TxtTabla">
	&nbsp;
	<select name="pAno" class="CajaTexto" id="pAno">
	<? 
	//Generar los años de 2006 a 2050
	for($i=2006; $i<=2050; $i++) { 
		
		//seleccionar el año cuando se carga la página por primera vez
		if ($pAno == "") {
			$AnoActual=date("Y"); //el año actual
		}
		else {
			$AnoActual= $pAno; //el año seleccionado
		}
		
		if ($i == $AnoActual) {
			$selAno = "selected";
		}
		else {
			$selAno = "";
		}
	?>
      <option value="<? echo $i; ?>" <? echo $selAno; ?> ><? echo $i; ?></option>
	 <? 
	 	
	 } //for 
	 
	 ?>

    </select>	
    <input type="hidden" name="fProyectos" id="fProyectos" value="" />
    </td>
    <td width="10%" class="TxtTabla">
    <!--
    <input name="Submit8" type="submit" class="Boton" value="Consultar">
    -->
    <input name="Submit8" type="button" class="Boton" value="Consultar" onclick="enviar();" />
    </td>
  </tr>
	</form>
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
    <td align="left" class="TxtTabla">
    <!--
    MM_openBrWindow('pdfHtUsuario.php?laUnidad=<? echo $reg[unidad]; ?>&cualMes=<? echo $reg[mes]; ?>&cualVigencia=<? echo $reg[vigencia]; ?>', 'winHTPdf', 'scrollbars=yes, resizable=yes, width=620, height=500')
    
      <input name="consultar" type="submit" class="Boton" id="consultar" value="Proyectos de aprobaci&oacute;n" onclick="fltProyecto();" />
      -->
      <?
	  	$cReg=0;
		$cntCursor=mssql_query($sql);
	  	while($cnt=mssql_fetch_array($cntCursor))
		{
			if($cnt[retirado]=='')
			{
				$cReg++;
			}
		}
		#	  mssql_num_rows($cursor) 
	  ?>
      <strong>Cantidad de usuarios que cumplen el criterio de consulta</strong>: <?= $cReg ?> </td>
    <td width="1%" align="right" class="TxtTabla"><?
	  	$url = '';
		if($pDivision!='')
		{
			$url=$url.'&div='.$pDivision;
		}
		if($miDpto!='')
		{
			$url=$url.'&dpt='.$miDpto;
		}
		if($pFiltro!='')
		{
			$url=$url.'&cat='.$pFiltro;
		}
		/*
		if($envJefe!='')
		{
			$url=$url.'&ejef='.$envJefe;
		}
		if($pDivision!='')
		{
			$url=$url.'&div='.$pDivision;
		}
		if($pDivision!='')
		{
			$url=$url.'&div='.$pDivision;
		}
		#*/
	  ?>
    <input name="consultar" type="submit" class="Boton" id="consultar" value="Proyectos pendientes por aprobar" onclick="MM_openBrWindow('lstHTProyectosVBPendiente.php?cualMes=<? echo $pMes; ?>&amp;cualVigencia=<? echo $pAno; ?>','winHTPdf','scrollbars=yes,resizable=yes')" /></td>
  </tr>
  <tr>
    <td colspan="2" class="TituloUsuario"> Estado Hojas de tiempo por Divisi&oacute;n </td>
  </tr>
</table>


    
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <!--<td width="1%" rowspan="2">&nbsp;</td>-->
        <td width="3%" rowspan="2">Unidad</td>
        <td rowspan="2">Categoria</td>
		<? if ($ocultaColumna == "SI") { ?>
        <td rowspan="2">Salario</td>
		<? } ?>
        <td rowspan="2">Usuarios que solicita la revisi&oacute;n </td>
        <td width="5%" rowspan="2">Activo/Retirado</td>
        <td width="5%" rowspan="2" align="left">Fecha de ingreso (FI) <br />
          Fecha de retiro (FR)</td>
        <td width="5%" rowspan="2">&iquest;Envi&oacute;<br />
HT?</td>
        <td width="20%" rowspan="2">Aprobaci&oacute;n por proyecto</td>
        <td colspan="2">Aprobaci&oacute;n del Jefe </td>
        <td colspan="2">Contratos</td>
        <td width="5%" rowspan="2">&nbsp;</td>
        </tr>
      <tr class="TituloTabla2">
        <td width="5%">Aprobado</td>
        <td width="15%">Quien firma </td>
        <td width="5%">Aprobado</td>
        <td width="15%">Quien aprueba </td>
      </tr>
       <?
	   $inc = 0;
	  while ($reg=mssql_fetch_array($cursor)) {
		$inc++;
		$ing = 0;
		if( $pMes != '' && $pAno != '' ){
			#	Fecha de ingreso tambien  
			if( ( $reg[anioIngreso] <= $pAno && $reg[mesIngreso] <= $pMes ) and ( $reg[anioIngreso] != '' ) )
				$ing = 1;
			else if( $reg[anioIngreso] < $pAno )#&& $reg[mesIngreso] <= $pMes )
				$ing = 1;
			#	los usuarios que tiene fecha de ingreso en NULL
			if( $reg[anioIngreso] == '' )
				$ing = 0;
			#	Retirados		  
			if( $reg[retirado] != '' || $reg[fechaRetiro] != ''  ){
				if( $reg[mes] == $pMes && $reg[vigencia] == $pAno )
					$ing = 1;
				else
					$ing = 0;
			}
		}
		else{
			#	Fecha de ingreso
			if( ( $reg[anioIngreso] <= date('Y') && $reg[mesIngreso] <= date('m') ) and ( $reg[anioIngreso] != '' ) )
				$ing = 1;
			else if( $reg[anioIngreso] < date('Y') )#&& $reg[mesIngreso] <= $pMes )
				$ing = 1;
			#	 filtra los usuarios que tiene fecha de ingreso en NULL
			if( $reg[anioIngreso] == '' )
				$ing = 0;
			#	Retirados		  
			if( $reg[retirado] != '' || $reg[fechaRetiro] != ''  ){
				if( $reg[mes] == date('m') && $reg[vigencia] == date('Y') )
					$ing = 1;
				else
					$ing = 0;
			}
		}
		  #	Filtro para las personas retiradas
		  #	month(U.fechaRetiro) mesRetiro, year(U.fechaRetiro) anioRetiro, 
		  #if( $reg[anioIngreso] <= $pAno && ( $reg[mesIngreso] <= $pMes ) ){
		  if( $ing == 1 ){			  
	  ?>

	  <tr valign="top" class="TxtTabla">
	    <!--
        <td width="1%">
		<?=	"Inc : ".$inc."; rtr : ".$rtr."<br />Retiro : ".$reg[mesRetiro]."; Ingreso : ".$reg[mesIngreso]."<br />Mes vigencia : ".$reg[mes]	?>
        </td>
        -->
        <td width="3%"><? echo $reg[unidad]; ?></td>
        <td><? echo $reg[nomCategoria]; ?></td>
		
		<? if ($ocultaColumna == "SI") { ?>
        <td>
		<?
		$miSalario = 0;
		//Mostrar el ultimo salario asignado
		$sSql="select * from usuariosSalario ";
		$sSql=$sSql." where unidad = ".$reg[unidad];
		$sSql=$sSql." and fecha = (select max(fecha) from UsuariosSalario where unidad = ".$reg[unidad].") ";
		$sCursor = mssql_query($sSql);
		if ($sReg=mssql_fetch_array($sCursor)) {
			$miSalario = $sReg[salario] ;
		}
		echo "$ " . number_format($miSalario, 0, '','.') 

		?>
		</td>
        <? } ?>
		<td><? echo ucwords(strtolower($reg[apellidos]  . " " . $reg[nombre])); ?></td>
		<td width="5%" align="center"><?	
			if( $reg[retirado] != '' || $reg[fechaRetiro] != '' )	
				echo "Retirado";
			else
				echo "Activo";
		?></td>
		<td width="10%" align="left"><b>FI: </b>
		  <?= date("d/m/Y ", strtotime($reg[fechaIngreso])) ?>
		  <br />
          <?	if( $reg[fechaRetiro] != '' ){	?>
          <b>FR: </b>
          <?= date("d/m/Y ", strtotime($reg[fechaRetiro])) ?>
          <?	}	?></td>
		<td width="5%" align="center">
		<?
		if($reg[unidadJefe] != "")
		{
			echo "SI";
		}
		else
		{
			echo "NO";
		}
		?></td>
        <td width="20%" valign="top">
        <?
			/*
			SELECT DISTINCT B.nombre, A.id_proyecto, A.id_actividad, A.unidad, A.vigencia, A.mes, A.esInterno
			FROM FacturacionProyectos A, Proyectos B
			WHERE 
			A.id_proyecto = B.id_proyecto
			AND A.unidad = 15712
			AND A.vigencia = 2014
			AND A.mes = 1
			*/
			#$sqlNomProye = 'SELECT DISTINCT B.nombre, A.id_proyecto, A.unidad, A.vigencia, A.mes, A.esInterno, A.id_actividad
			#echo 'Filtro : '.$fProyectos.'<br />';
			if($fProyectos=='')
			{
			$sqlNomProye = 'SELECT DISTINCT B.nombre, A.id_proyecto, A.unidad, A.vigencia, A.mes, A.esInterno
							FROM FacturacionProyectos A, Proyectos B
							WHERE 
							A.id_proyecto = B.id_proyecto AND A.unidad = '.$reg[unidad].' AND A.mes = '.$pMes.' AND A.vigencia = '.$pAno;
			#echo $sqlNomProye.'<br />';			
			#	*****
			$qryNomProye = mssql_query($sqlNomProye);
			$cntRowProyectos = mssql_num_rows($qryNomProye);
			$mstCol = 0;
			if($cntRowProyectos>0)
			{
		?>
        <table width="100%" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
        <tr><td>
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr class="TituloTabla2">
            <td width="40%">Proyecto</td>
            <td width="1%">VB</td>
            <td width="40%">Quien aprueba</td>
          </tr>
			<?
				while( $rwNomProye = mssql_fetch_array($qryNomProye))
				{
            ?>
                    <tr class="TxtTabla">
                    <td width="40%" valign="top"><?=	$rwNomProye[nombre].'.<br />'	?></td>
                    <td width="1%" valign="top">
						<?
							/*
							$sqlVbProyec = 'SELECT COUNT(B.validaEncargado) cnt, A.id_proyecto, A.unidad, A.vigencia, A.mes, A.esInterno, A.id_actividad
							FROM FacturacionProyectos A
							LEFT JOIN VoBoFactuacionProyHT B ON A.id_proyecto = B.id_proyecto AND A.esInterno = B.esInterno AND A.unidad = B.unidad AND 
							A.id_actividad = B.id_actividad AND A.vigencia = B.vigencia AND A.mes = B.mes AND B.validaEncargado = 1
							WHERE 
							A.unidad = '.$reg[unidad].'
							AND A.vigencia = '.$pAno.'
							AND A.mes = '.$pMes.'
							AND A.id_proyecto = '.$rwNomProye[id_proyecto].'
							GROUP BY A.id_proyecto, A.id_actividad, A.unidad, A.vigencia, A.mes, A.esInterno';
							#*/
							$sqlVbProyec='SELECT COUNT(B.id_proyecto) cnt, A.id_proyecto, A.id_actividad, A.unidad, A.vigencia, A.mes FROM ';
							$sqlVbProyec=$sqlVbProyec.'( SELECT DISTINCT id_proyecto, id_actividad, unidad, vigencia, mes ';
							$sqlVbProyec=$sqlVbProyec.' FROM FacturacionProyectos A ';
							$sqlVbProyec=$sqlVbProyec.' WHERE A.unidad = '.$reg[unidad];
							$sqlVbProyec=$sqlVbProyec.' AND A.vigencia = '.$pAno;
							$sqlVbProyec=$sqlVbProyec.' AND A.mes = '.$pMes;
							$sqlVbProyec=$sqlVbProyec.' AND A.id_proyecto = '.$rwNomProye[id_proyecto].' ) A, ';
							$sqlVbProyec=$sqlVbProyec.'( SELECT id_proyecto, id_actividad, unidad, vigencia, mes';
							$sqlVbProyec=$sqlVbProyec.' FROM VoBoFactuacionProyHT A ';
							$sqlVbProyec=$sqlVbProyec.' WHERE A.unidad = '.$reg[unidad];
							$sqlVbProyec=$sqlVbProyec.' AND A.vigencia = '.$pAno;
							$sqlVbProyec=$sqlVbProyec.' AND A.mes = '.$pMes;
							$sqlVbProyec=$sqlVbProyec.' AND A.id_proyecto = '.$rwNomProye[id_proyecto].' AND A.validaEncargado = 1 )B ';
							$sqlVbProyec=$sqlVbProyec.' WHERE ';
							$sqlVbProyec=$sqlVbProyec.' A.id_proyecto = B.id_proyecto AND A.id_actividad *= B.id_actividad AND ';
							$sqlVbProyec=$sqlVbProyec.' A.unidad = B.unidad AND A.vigencia = B.vigencia AND A.mes = B.mes ';
							$sqlVbProyec=$sqlVbProyec.' GROUP BY A.id_proyecto, A.id_actividad, A.unidad, A.vigencia, A.mes';
							#echo $sqlVbProyec.'<br />';
							$qryVbProyec = mssql_query($sqlVbProyec);
							#$cntPro = $cntVb = 0;
							$Vb = 1;
							#	Pone el icono si todas las actividades del proyecto tienen el VB del responsable del proyecto
							while($rwVbProyec=mssql_fetch_array($qryVbProyec))
							{
								if($rwVbProyec[cnt]==0)
								{
									$Vb=0;
								}
								$cntPro++;
							}
							#echo 'Cantidad : '.$cntPro.'<br />Vb : '.$cntVb.'<br />';
							#	Si la cantidad de proyecto y de VB son iguales es por que tiene VB el proyecto
							#if($cntPro==$cntVb&&$cntPro!=0)
							if($Vb==1)
							{
                        ?>
                                <img src="img/images/Si.gif" />
                        <?
							}
							#	
							#if( ($cntPro>0&&$cntVb>0) && $cntPro!=$cntVb)
							else
							{
                        ?>
                                <img src="img/images/No.gif" />
                        <?
							}
                        ?>
                    </td>
                    <td width="40%" valign="top">
                    <?
						$mstCol=1;
						#	Trae las personas que son los responsables de dar los VB de las actividades en el proyecto.
						$sqlEncProye = 'SELECT DISTINCT B.nombre, B.apellidos 
										FROM VoBoFactuacionProyHT A, Usuarios B
										WHERE A.unidadEncargado = B.unidad ';
						#$sqlEncProye = $sqlEncProye.' AND validaEncargado = 1 ';
						$sqlEncProye = $sqlEncProye.' AND A.unidad = '.$reg[unidad].'
										AND A.id_proyecto = '.$rwNomProye[id_proyecto].'
										AND A.vigencia = '.$pAno.'
										AND A.mes = '.$pMes;
						$qryEncProye = mssql_query($sqlEncProye);
						#echo $sqlEncProye.'<br />';
						while($rwEncProye = mssql_fetch_array($qryEncProye))
						{
							echo strtolower(trim($rwEncProye[nombre])).' '.strtolower(trim($rwEncProye[apellidos])).'<br />';
						}
                    ?>
                    </td>
				</tr>
			<?		
				}
            ?>
        </table>
        </td></tr>
        </table>
        <?
			}
			}
			else if($fProyectos==1)
			{
				$r=0;
				$sqlNomProye = 'SELECT DISTINCT B.nombre, A.id_proyecto, A.unidad, A.vigencia, A.mes, A.esInterno
								FROM FacturacionProyectos A, Proyectos B
								WHERE 
								A.id_proyecto = B.id_proyecto AND A.unidad = '.$reg[unidad].' AND A.mes = '.$pMes.' AND A.vigencia = '.$pAno;
				$qryNomProye = mssql_query($sqlNomProye);
				while($rw=mssql_fetch_array($qryNomProye))
				{
					$sqlVbProyec2='SELECT COUNT(B.id_proyecto) cnt, A.id_proyecto, A.id_actividad, A.unidad, A.vigencia, A.mes FROM ';
					$sqlVbProyec2=$sqlVbProyec2.'( SELECT DISTINCT id_proyecto, id_actividad, unidad, vigencia, mes ';
					$sqlVbProyec2=$sqlVbProyec2.' FROM FacturacionProyectos A ';
					$sqlVbProyec2=$sqlVbProyec2.' WHERE A.unidad = '.$reg[unidad];
					$sqlVbProyec2=$sqlVbProyec2.' AND A.vigencia = '.$pAno;
					$sqlVbProyec2=$sqlVbProyec2.' AND A.mes = '.$pMes;
					$sqlVbProyec2=$sqlVbProyec2.' AND A.id_proyecto = '.$rw[id_proyecto].' ) A, ';
					$sqlVbProyec2=$sqlVbProyec2.'( SELECT id_proyecto, id_actividad, unidad, vigencia, mes';
					$sqlVbProyec2=$sqlVbProyec2.' FROM VoBoFactuacionProyHT A ';
					$sqlVbProyec2=$sqlVbProyec2.' WHERE A.unidad = '.$reg[unidad];
					$sqlVbProyec2=$sqlVbProyec2.' AND A.vigencia = '.$pAno;
					$sqlVbProyec2=$sqlVbProyec2.' AND A.mes = '.$pMes;
					$sqlVbProyec2=$sqlVbProyec2.' AND A.id_proyecto = '.$rw[id_proyecto].' )B ';
					$sqlVbProyec2=$sqlVbProyec2.' WHERE ';
					$sqlVbProyec2=$sqlVbProyec2.' A.id_proyecto = B.id_proyecto AND A.id_actividad *= B.id_actividad AND ';
					$sqlVbProyec2=$sqlVbProyec2.' A.unidad = B.unidad AND A.vigencia = B.vigencia AND A.mes = B.mes ';
					$sqlVbProyec2=$sqlVbProyec2.' GROUP BY A.id_proyecto, A.id_actividad, A.unidad, A.vigencia, A.mes';
					#echo $sqlVbProyec.'<br />';
					$qryVbProyec2 = mssql_query($sqlVbProyec2);
					#$cntPro = $cntVb = 0;
					$Vb = 1;
					#	Pone el icono si todas las actividades del proyecto tienen el VB del responsable del proyecto
					while($rwVbProyec2=mssql_fetch_array($qryVbProyec2))
					{
						if($rwVbProyec2[cnt]==0)
						{
							$Vb=0;							
						}
						$cntPro++;
					}
					if($Vb==0)
					{
						$nomProyecto[$r][0]=$rw[nombre];
						$nomProyecto[$r][1]=$rw[id_proyecto];
						$r++;
					}
				}
				$cntRegistros = count($nomProyecto);
				if($cntRegistros>0)
				{
		?>
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr class="TituloTabla2">
            <td width="40%">Proyecto</td>
            <td width="1%">VB</td>
            <td width="40%">Quien aprueba</td>
          </tr>
			<?	
				$rNombre = 0;
				while( $rNombre < $cntRegistros)
				{
            ?>
          <tr class="TxtTabla">
            <td width="40%" valign="top"><?=	$nomProyecto[$rNombre][0].'.<br />'	?></td>
            <td width="1%" valign="top"><img src="img/images/No.gif" alt="" /></td>
            <td width="40%" valign="top">
			<?
				$mstCol=1;
				#	Trae las personas que son los responsables de dar los VB de las actividades en el proyecto.
				$sqlEncProye = 'SELECT DISTINCT B.nombre, B.apellidos FROM VoBoFactuacionProyHT A, Usuarios B
								WHERE A.unidadEncargado = B.unidad ';
				#$sqlEncProye = $sqlEncProye.' AND validaEncargado = 1 ';
				$sqlEncProye = $sqlEncProye.' AND A.unidad = '.$reg[unidad].'
								AND A.id_proyecto = '.$nomProyecto[$rNombre][1].'
								AND A.vigencia = '.$pAno.'
								AND A.mes = '.$pMes;
				$qryEncProye = mssql_query($sqlEncProye);
				#echo $sqlEncProye.'<br />';
				while($rwEncProye = mssql_fetch_array($qryEncProye))
				{
					echo strtolower(trim($rwEncProye[nombre])).' '.strtolower(trim($rwEncProye[apellidos])).'<br />';
				}
            ?>
            </td>
          </tr>
          <?	
		  			$rNombre++;	
				}
            ?>
        </table>
        <?
				unset($nomProyecto);
				}
			}
		?>
        </td>
        <td width="5%" align="center">
		<? 
		 #echo $reg[validaJefe].' == "0") AND ('.trim($reg[comentaJefe]).' != "")<br />';
		 if ($reg[validaJefe] == "1")
		 {
		?>
			<img src="img/images/Si.gif" />
		<? 
		 } 
		 if (($reg[validaJefe] == "0") AND (trim($reg[comentaJefe]) != ""))
		 { 
		?>
			<img src="img/images/No.gif" />
		<? 
		 }
		?>		
        </td>
        <td width="15%">
		<? 
		$uJsql="select * from usuarios where unidad = " . $reg[unidadJefe];
		$uJcursor=mssql_query($uJsql);
		if($uJreg=mssql_fetch_array($uJcursor))
		{
			echo ucwords(strtolower($uJreg[apellidos]  . ", " . $uJreg[nombre]));
		}
		?>		
        </td>
        <td width="5%">
		<? 
			if($reg[validaContratos] == "1")
			{ 
		?>
				<img src="img/images/Si.gif" />
		<? 
			}
		?>

		<? if (($reg[validaContratos] == "0") AND (trim($reg[comentaContratos]) != "")) { ?>
			<img src="img/images/No.gif" />
		<? } ?>		</td>
        <td width="15%">
		<? 
		$uJsql="select * from usuarios where unidad = " . $reg[unidadContratos] ;
		$uJcursor = mssql_query($uJsql);
		if ($uJreg=mssql_fetch_array($uJcursor)) { 
			echo ucwords(strtolower($uJreg[apellidos]  . ", " . $uJreg[nombre]));
		}
		
		?>		</td>
        <td width="5%">
        <!--<input name="Submit" type="submit" class="Boton" onclick="MM_goToURL('parent','verHTDivision.php?zUnidad=<? echo $reg[unidad]; ?>&Flmes=<? echo $reg[mes]; ?>&Flano=<? echo $reg[vigencia]; ?>');return document.MM_returnValue" value="Ver Hoja" />
        
        -->
        <!--
	        Se cambio el nombre de las variables por las que se trabaja en el archivo que genera el PDF de la hoja de tiempo de los usuarios.
        -->
        <!--
        <input name="Submit" type="submit" class="Boton" onclick="MM_goToURL('parent','pdfHtUsuario.php?laUnidad=<? echo $reg[unidad]; ?>&amp;cualMes=<? echo $reg[mes]; ?>&amp;cualVigencia=<? echo $reg[vigencia]; ?>');MM_openBrWindow('pdfHtUsuario.php','winHTPdf','scrollbars=yes,resizable=yes,width=620,height=500');return document.MM_returnValue" value="Ver Hoja" />
        
        pMes	pAno
        <input name="Submit" type="button" class="Boton" onclick="MM_openBrWindow('pdfHtUsuarioDivision.php?unidad=<? echo $reg[unidad]; ?>&cualMes=<? echo $reg[mes]; ?>&cualVigencia=<? echo $reg[vigencia]; ?>', 'winHTPdf', 'scrollbars=yes, resizable=yes, width=720, height=500')" value="Generar PDF" />
        -->
        <?
			if($pMes=='')
			{
				$pMes=date('m');
			}
			if($pAno=='')
			{
				$pAno=date('Y');
			}
		?>
        <input name="Submit" type="button" class="Boton" onclick="MM_openBrWindow('pdfHtUsuarioDivision.php?unidad=<? echo $reg[unidad]; ?>&cualMes=<?= $pMes ?>&cualVigencia=<?= $pAno ?>', 'winHTPdf', 'scrollbars=yes, resizable=yes, width=720, height=500')" value="Generar PDF" />
        </td>
        </tr>
        <? }	#Cierrra if	?>
	  <? } ?>
        </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla"><input name="BotonReg" type="submit" class="Boton" id="BotonReg" onclick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value="P&aacute;gina Principal Hoja de tiempo" /></td>
          </tr>
        </table></td>
      </tr>
    </table>
</body>
</html>
