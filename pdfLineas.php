<?php
	// Include the main TCPDF library (search for installation path).
	require_once('tcpdf/tcpdf.php');
	// create new PDF document
	#$pdf = new TCPDF('l', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);#A12	
	#$pdf = new TCPDF('l', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	#$pdf = new TCPDF('l', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', true);
	$pdf = new TCPDF();
	$pdf->SetPrintHeader(false);
	$pdf->SetPrintFooter(false);
	#$pdf = new TCPDF('l', 'cm', PDF_PAGE_FORMAT, true, 'UTF-8', false);
	// set document information
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
	$pdf->SetMargins(2, 2, 2);
	#$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	#$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	
	// set auto page breaks
	#$pdf->SetAutoPageBreak(false, PDF_MARGIN_BOTTOM);
	
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
	
	$pdf->SetFont('helvetica', '', 8, '', false);
	
	// Add a page
	// This method has several options, check the source code documentation for more information.
	#*/
	$pdf->AddPage();
	
	// set text shadow effect
	#$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
	
	
	##	***
	$html = '<center><h1>Oscar</h1><h2>Mauricio</h2><h3>López</h3><h4>Segura</h4></center>';
	$pdf->writeHTML($html, true, false, true, false, '');


	$pdf->Output('example_001.pdf', 'I');
#*/
?>
