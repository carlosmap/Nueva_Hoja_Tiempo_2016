<?
	session_start();
	
	error_reporting(E_ALL);	
	date_default_timezone_set('Europe/London');	

	//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
	include "funciones.php";
	include "validacion.php";
	include "validaUsrBd.php";

	/** PHPExcel_IOFactory */
	require_once 'phpExcel/PHPExcel.php';
	#require_once 'phpExcel/PHPExcel/IOFactory.php';
	
	$objPHPExcel = new PHPExcel();

	#	***********************************************
	$sql2="select u.fechaRetiro, u.retirado, u.unidad, u.nombre, u.apellidos, u.email , u.id_departamento, d.nombre as departamento, d.id_division,   ";
	$sql2= $sql2. " v.nombre as division, v.id_dependencia, x.nombre as dependencia ,  ";
	$sql2= $sql2. " u.id_categoria, c.nombre as categoria ";
	$sql2= $sql2. " from usuarios u 
					inner join Departamentos d on u.id_departamento=d.id_departamento
					inner join Divisiones v on d.id_division= v.id_division
					inner join Dependencias x on v.id_dependencia=x.id_dependencia
					inner join Categorias c on u.id_categoria=c.id_categoria";
	
	//SI SE CONSULTA LOS USUARIOS CON VOBO (APROBADO / NO APROBADO)
	if(($revision==2)||($revision==3)||($revision==5))
	{
		$sql2= $sql2. " inner join VoBoFirmasHT on VoBoFirmasHT.unidad=u.unidad and VoBoFirmasHT.vigencia=".$cualVigencia." and VoBoFirmasHT.mes=".$cualMes;
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
		$sql2= $sql2. " and u.unidad not in ( select unidad from VoBoFirmasHT where  VoBoFirmasHT.unidad=u.unidad and VoBoFirmasHT.vigencia=".$cualVigencia." and VoBoFirmasHT.mes=".$cualMes.") ";
	
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
	if ($pRetirado == "1") {
		//USUARIOS RETIRADOS
		$sql2= $sql2. " and u.retirado IS NOT NULL ";

		if ($cualMes == "") {
			$sql2= $sql2. " and (month(fechaRetiro)= ".date("m")." and year(fechaRetiro)=".date("Y").") ";
		}
		else {
			$sql2= $sql2. " and (month(fechaRetiro)= ".$cualMes." and year(fechaRetiro)=".$cualVigencia.") ";
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
	
		if ($cualMes == "") {
			$sql21=$sql21. " and (month(fechaRetiro)= ".date("m")." and year(fechaRetiro)=".date("Y").") ";
		}
		else {
			$sql21=$sql21. " and (month(fechaRetiro)= ".$cualMes." and year(fechaRetiro)=".$cualVigencia.") ";
		}
		$sql21=$sql21.") union (";
		$sql2=$sql21.$sql2." and u.retirado IS NULL )) u";
	}

	//$sql2= $sql2. " order by u.apellidos ";
	$sql2= $sql2. " order by categoria , u.unidad ";
	
	#echo $sql2;
	$cursor = mssql_query($sql2);
		
	#	***********************************************	
	#	NEGRILLA
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);

	$objPHPExcel->getActiveSheet()->getStyle('A5')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B5')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('C5')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('D5')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('E5')->getFont()->setBold(true);
	
	#	AJUSTE TEXTO
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	#	*******************************************

	// Set document properties
	$objPHPExcel->getProperties()->setCreator("Ingetec SA")
								 ->setTitle("Reporte de usuarios")
								 ->setSubject("Office 2007 XLSX Test Document")
								 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
								 ->setKeywords("office 2007 openxml php")
								 ->setCategory("Test result file");
	
	# TITULO ARCHIVO
	switch($revision)
	{
		case 1: $titulo = 'USUARIOS QUE DEBE FACTURAR';	break;
		case 2: $titulo = 'USUARIOS CON APROBACIÓN DE CONTRATOS';	break;
		case 3: $titulo = 'USUARIOS SIN APROBACIÓN DE CONTRATOS';	break;
		case 4: $titulo = 'USUARIOS SIN ENVIO A JEFE';	break;
		case 5: $titulo = 'USUARIOS SIN APROBACIÓN DEL JEFE';	break;
	}
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:E1');
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1', $titulo);
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	#	MES Y VIGENCIA
	$nMeses = array( '', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A2', 'MES')
				->setCellValue('B2', $nMeses[$cualMes])
				->setCellValue('A3', 'VIGENCIA')
				->setCellValue('B3', $cualVigencia);
	
	$objPHPExcel->getActiveSheet(0)->getStyle('B2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet(0)->getStyle('B3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
	#	Cabecera
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A5', 'Unidad')
				->setCellValue('B5', 'Usuario')
				->setCellValue('C5', 'Categoría')
				->setCellValue('D5', 'División')
				->setCellValue('E5', 'Departamento');
	$fl = 5;
	while ($reg=mssql_fetch_array($cursor)) {
		$fl++;
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A'.$fl, $reg[unidad])
					->setCellValue('B'.$fl, ucwords(strtolower(utf8_encode($reg[apellidos]))) . " " . ucwords(strtolower(utf8_encode($reg[nombre]))))
					->setCellValue('C'.$fl, strtoupper(utf8_encode($reg[categoria])))
					->setCellValue('D'.$fl, ucwords(strtolower(utf8_encode($reg[division]))))
					->setCellValue('E'.$fl, ucwords(strtolower(utf8_encode($reg[departamento]))));
	}
	#	FILTROS
	#$objPHPExcel->getActiveSheet(0)->setAutoFilter($objPHPExcel->getActiveSheet()->calculateWorksheetDimension());
	if($fl>15)
	{
		$objPHPExcel->getActiveSheet()->setAutoFilter('A5:E'.$fl);
	}

	
	$objPHPExcel->getActiveSheet()->setTitle('Simple');
	
	
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);
	
	
	// Redirect output to a client’s web browser (Excel2007)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="HTContratos_'.date('Y-m-d').'.xlsx"');
	header('Cache-Control: max-age=0');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;	
	
?>