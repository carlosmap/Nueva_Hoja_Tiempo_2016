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
	$sqlNombre = 'SELECT A.nombre, A.apellidos FROM Hojadetiempo.dbo.Usuarios A WHERE unidad ='.$laUnidad;
	$info = mssql_fetch_array(mssql_query($sqlNombre));

	$mesesFile = array( '', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre' );
	$fName = $laUnidad.'-'.ucfirst($info['nombre']).' '.ucfirst($info['apellidos']).'-'.$mesesFile[$cualMes].'_'.$cualVigencia;

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
	
	#/*
	// set default header data
	#$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
	#$pdf->SetHeaderData('','','',cabecera(), array(0,64,255), array(0,64,128));
	#$pdf->setFooterData(array(0,64,0), array(0,64,128));
	
	// set header and footer fonts
	#$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	#$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
	#*/
	// set default monospaced font
	#/*
	#$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
	
	// set margins
	#$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
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
	
	// Add a page
	// This method has several options, check the source code documentation for more information.
	#*/
	$pdf->AddPage();
	
	// set text shadow effect
	#$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
	
	
	##	***
	$html = cabecera($laUnidad, $cualMes, $cualVigencia);
	
	$pdf->writeHTML($html, true, false, true, false, '');

	$tabla = infoHTUsuario($laUnidad, $cualMes, $cualVigencia);
	$pdf->writeHTML($tabla, true, false, true, false, '');
	
	$firmas = firmasHt($laUnidad, $cualMes, $cualVigencia);
	$pdf->writeHTML($firmas, true, false, true, false, '');

	$resumen=resumen();
	$pdf->writeHTML($resumen, true, false, true, false, '');
	
	$pdf->AddPage();
	$html = cabecera($laUnidad, $cualMes, $cualVigencia);
	$pdf->writeHTML($html, true, false, true, false, '');

	$detalle = detalleHt($laUnidad, $cualMes, $cualVigencia);
	$pdf->writeHTML($detalle, true, false, true, false, '');
	

	$pdf->Output($fName.'.pdf', 'I');
#*/
?>
