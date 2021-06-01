<?php
	session_start();
	//include("../verificaRegistro2.php");
	//include('../conectaBD.php');
	#<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
	#echo '<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">';

	#include "css/estilo.css";
	//Establecer la conexión a la base de datos
	//$conexion = conectar();
	include "funciones.php";
	include "validacion.php";
	include "validaUsrBd.php";
	include "tmpPdfHt.php";	

	// Include the main TCPDF library (search for installation path).
	require_once('tcpdf/tcpdf.php');
	// create new PDF document
	#$pdf = new TCPDF('l', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);#A12
	#$sqlNombre = 'SELECT A.nombre, A.apellidos FROM Hojadetiempo.dbo.Usuarios A WHERE unidad ='.$laUnidad;
	#$info = mssql_fetch_array(mssql_query($sqlNombre));
	$fName = '';
	if(($pDivision != "") AND ($pDivision != "0")) {
		$sqlDivision= "Select * from Divisiones where estadoDiv LIKE 'A' AND id_division = ".$pDivision;
		$qryDivision= mssql_fetch_array(mssql_query($sqlDivision));
		$fName = $fName.strtolower($qryDivision[nombre]).'-';
	}
	if (($pDepto != "") AND ($pDepto != "0")) {
		$sqlDepartamento= "Select * from departamentos where  estadoDpto LIKE 'A' AND id_departamento = ".$pDepto." AND id_division = ".$pDivision;
		$qryDepartamento= mssql_fetch_array(mssql_query($sqlDepartamento));		
		$fName = $fName.strtolower($qryDepartamento[nombre]).'-';
	}

	if ($pUnidad != "") {
		$sqlUsuario= "SELECT nombre, apellidos FROM Usuarios WHERE unidad = ".$pUnidad;
		$qryUsuario= mssql_fetch_array(mssql_query($sqlUsuario));
		$fName = $fName.strtolower(utf8_encode($qryUsuario[nombre].' '.$qryUsuario[apellidos])).'-';
	}


	$mesesFile = array( '', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre' );
	#$fName = $laUnidad.'-'.ucfirst($info['nombre']).' '.ucfirst($info['apellidos']).'-'.$mesesFile[$cualMes].'_'.$cualVigencia;
	$fName = $fName . $mesesFile[$cualMes].'_'.$cualVigencia;

	$pdf = new TCPDF('l', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	$pdf->SetPrintHeader(false);
	$pdf->SetPrintFooter(false);
	#$pdf = new TCPDF('l', 'cm', PDF_PAGE_FORMAT, true, 'UTF-8', false);
	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor($fName);
	$pdf->SetTitle($fName);
	$pdf->SetSubject($fName);
	#$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
	
	
	$pdf->SetMargins(2, 3, 2);
	#$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	#$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	
	// set auto page breaks
	$pdf->SetAutoPageBreak(false, PDF_MARGIN_BOTTOM);
	
	// set image scale factor
	#$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	// set some language-dependent strings (optional)
	/*
	if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
		require_once(dirname(__FILE__).'/lang/eng.php');
		$pdf->setLanguageArray($l);
	}
	*/
	// ---------------------------------------------------------
	
	// set default font subsetting mode
	#$pdf->setFontSubsetting(true);
	
	$pdf->SetFont('helvetica', '', 7, '', true);
	
	
	#	**********
	$sql2="select u.fechaRetiro, u.retirado, u.unidad, u.nombre, u.apellidos, u.email , u.id_departamento, d.nombre as departamento, d.id_division,   ";
	$sql2= $sql2. " v.nombre as division, v.id_dependencia, x.nombre as dependencia ,  ";
	$sql2= $sql2. " u.id_categoria, c.nombre as categoria ";
	$sql2= $sql2. " from usuarios u 
					inner join Departamentos d on u.id_departamento=d.id_departamento
					inner join Divisiones v on d.id_division= v.id_division
					inner join Dependencias x on v.id_dependencia=x.id_dependencia
					inner join Categorias c on u.id_categoria=c.id_categoria
					";
	
	//SI SE CONSULTA LOS USUARIOS CON VOBO (APROBADO / NO APROBADO)
	if(($revision==2)||($revision==3)||($revision==5))
	{
		$sql2= $sql2. " inner join VoBoFirmasHT on VoBoFirmasHT.unidad=u.unidad and VoBoFirmasHT.vigencia=".$pAno." and VoBoFirmasHT.mes=".$pMes;
	}
	
	$sql2= $sql2. " where u.id_departamento = d.id_departamento ";
	$sql2= $sql2. " and d.id_division = v.id_division  ";
	$sql2= $sql2. " and v.id_dependencia = x.id_dependencia ";
	$sql2= $sql2. " and u.id_categoria = c.id_categoria ";
	//Para que muestre la Hojas de tiempo de los usuarios retirados
	
	//SI SE CONSULTA LOS USUARIOS CON VOBO APROBADO
	if($revision==2)
	{
		$sql2= $sql2. " and VoBoFirmasHT.validaContratos=1 ";
	}
	
	//SI SE CONSULTA LOS USUARIOS CON VOBO NO APROBADO
	if($revision==3)
	{
		$sql2= $sql2. " and VoBoFirmasHT.validaContratos=0 ";
	}
	
	//USUARIOS QUE NO HAN ENVIADO LA H.T. AL JEFE O NO LO HAN ESPECIFICADO
	if($revision==4)
	{
		$sql2= $sql2. " and u.unidad not in ( select unidad from VoBoFirmasHT where  VoBoFirmasHT.unidad=u.unidad and VoBoFirmasHT.vigencia=".$pAno." and VoBoFirmasHT.mes=".$pMes.") ";
	
	}
	
	//USUARIOS QUE HAN ENVIADO LA H.T. AL JEFE, Y QUE NO HAN SIDO APROBADAS
	if($revision==5)
	{
		$sql2= $sql2. " and VoBoFirmasHT.validaJefe=0 ";
	}
	
	//USUARIOS ACTIVOS 
	if (($pRetirado == "") OR ($pRetirado == "0")) {
		$sql2= $sql2. " and u.retirado IS NULL ";
	}
	
	//SE CONSULTAN LOS USUARIOS RETIRADOS EN EL MES SELECCIONADO Y/O EL MES ACTUAL
	if ($pRetirado == "1") 
	{
		//USUARIOS RETIRADOS
		$sql2= $sql2. " and u.retirado IS NOT NULL ";
	
		if ($pMes == "")
		{
			$sql2= $sql2. " and (month(fechaRetiro)= ".date("m")." and year(fechaRetiro)=".date("Y").") ";
		}
		else
		{
			$sql2= $sql2. " and (month(fechaRetiro)= ".$pMes." and year(fechaRetiro)=".$pAno.") ";
		}
	}
	
	
	if(trim($pEmpresa)!="")
	{
		$sql2= $sql2. " and idEmpresa=".$pEmpresa;
	}
	
	if (($pDivision != "") AND ($pDivision != "0")) {
		$sql2= $sql2. " and d.id_division = " . $pDivision;
	}
	if (($pDepto != "") AND ($pDepto != "0")) {
		$sql2= $sql2. " and u.id_departamento = " . $pDepto;
	}
	
	if ($pUnidad != "") {
		$sql2= $sql2. " and u.unidad = " . $pUnidad;
	}
	if ($pCategoria != "") {
		$sql2= $sql2. " and u.id_categoria = " . $pCategoria;
	}
	if ($pNombre != "") {
		$sql2= $sql2. " and (u.nombre LIKE '%".$pNombre."%' or u.apellidos LIKE '%".$pNombre."%')";
	}
	
	//SI SE SELECCIONO EN EL CAMPO  Resvisión H.T. contratos LA OPCION Usuarios que deben facturar Y Ver usuarios retirados ? CON LA OPCION Todos
	//SE HACE UNA CONSULTA DE UNION, CON LOS USUARIOS ACTIVOS Y LO RETIRADOS EN LA FECHA SELECCIONADA
	if (($pRetirado == "2") &&($revision==1))
	{
		$sql21=" select * from ( ( ";
		$sql21=$sql21.$sql2." and u.retirado IS NOT NULL ";
		
		if ($pMes == "") {
		$sql21=$sql21. " and (month(fechaRetiro)= ".date("m")." and year(fechaRetiro)=".date("Y").") ";
		}
		else {
		$sql21=$sql21. " and (month(fechaRetiro)= ".$pMes." and year(fechaRetiro)=".$pAno.") ";
		}
		
		$sql21=$sql21.") union (";
		$sql2=$sql21.$sql2." and u.retirado IS NULL )) u";
		
	}
	
	
	
	//$sql2= $sql2. " order by u.apellidos ";
	$sql2= $sql2. " order by categoria , u.unidad ";
	$qry = mssql_query($sql2);
	while($rw=mssql_fetch_array($qry))
	{	
		#	**********
		$pdf->AddPage();
		$html = cabecera($rw['unidad'], $cualMes, $cualVigencia);
		
		$pdf->writeHTML($html, true, false, true, false, '');
	
		$tabla = infoHTUsuario($rw['unidad'], $cualMes, $cualVigencia);
		$pdf->writeHTML($tabla, true, false, true, false, '');
		
		$firmas = firmasHt($rw['unidad'], $cualMes, $cualVigencia);
		$pdf->writeHTML($firmas, true, false, true, false, '');
	
		$resumen=resumen();
		$pdf->writeHTML($resumen, true, false, true, false, '');
		
		$pdf->AddPage();
		$html = cabecera($rw['unidad'], $cualMes, $cualVigencia);
		$pdf->writeHTML($html, true, false, true, false, '');
	
		$detalle = detalleHt($rw['unidad'], $cualMes, $cualVigencia);
		$pdf->writeHTML($detalle, true, false, true, false, '');
	}

	$pdf->Output($fName.'.pdf', 'I');
#*/
?>
