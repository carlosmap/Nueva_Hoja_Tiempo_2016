<?php

	function cabecera($unidad, $mes, $vigencia){
		$meses = array( '', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre' );
		
		$sql = 'SELECT cat.nombre nCat, dpt.nombre nDpt, dep.nombre nDep, sec.nombre nSec, dv.nombre nDiv, A.* FROM Hojadetiempo.dbo.Usuarios A				
				INNER JOIN Hojadetiempo.dbo.Departamentos dpt ON A.id_departamento = dpt.id_departamento
				INNER JOIN Hojadetiempo.dbo.Divisiones dv ON dv.id_division = dpt.id_division
				INNER JOIN Hojadetiempo.dbo.Dependencias dep ON dep.id_dependencia = dv.id_dependencia
				LEFT JOIN Hojadetiempo.dbo.Secciones sec ON sec.id_departamento = dpt.id_departamento
				INNER JOIN Hojadetiempo.dbo.Categorias cat ON A.id_categoria = cat.id_categoria
				WHERE A.Unidad = '.$unidad;
				/*<br /><br />&nbsp;
						<br /><br />*/
		$sqlDiasLaborales = 'select * from horasydiaslaborales where vigencia = '.$vigencia.' AND mes = '.$mes;

		$qryDiasLaborales = mssql_fetch_array(mssql_query($sqlDiasLaborales));
		$info = mssql_fetch_array(mssql_query($sql));
		
		$sqlTipoContrato = 'select TipoContrato from Usuarios where unidad = '.$unidad;
		$qryTipoContrato = mssql_fetch_array(mssql_query($sqlTipoContrato));
		$head = '
			<table width="100%" style="border: double solid 2px;" cellspacing="1" cellpadding="0">
				<tr>
					<td width="15%" valign="middle">
						<br /><br />&nbsp;
						<br />&nbsp;
						<img src="pics/LogoIngetecPNG2.png" width=150 heigth="75" />
					</td>
					<td width="55%" align="center">
						<br />&nbsp;<br />
						<center>&nbsp;&nbsp;&nbsp; &nbsp; &nbsp; &nbsp;
						<table width="90%">
							<tr>
								<th width="100%" align="center">
								<h1>HOJA DE TIEMPO<br />
								'.$meses[$mes].' del '.$vigencia.'</h1>
								</th>
							</tr>
							<tr>
								<td width="100%">
									<table width="100%" style="border: double solid 2px;">
										<tr>
											<td width="20%">Horas MES:</td>
											<td width="20%">Oficina: '.trim($qryDiasLaborales['hOficina']).'</td>
											<td width="20%">Campo: '.trim($qryDiasLaborales['hCampo']).'</td>
											<td width="40%">Categoria 42: '.trim($qryDiasLaborales['hCat42']).'</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						</center>
					</td>
					<td width="30%">
						<table width="100%">
							<tr>
								<td width="100%">
								'.trim(ucwords( strtolower( utf8_encode($info['nombre'])))).' '.trim(ucwords( strtolower( utf8_encode($info['apellidos'])))).'<br />
								'.trim($info['unidad']).' - ['.trim($info['nCat']).'] - <strong>'.$qryTipoContrato['TipoContrato'].'</strong>
								</td>
							</tr>
							<tr><td><strong>DEP.</strong> '.trim(ucwords( strtoupper( utf8_encode($info['nDep'])))).'</td></tr>
							<tr><td><strong>DPT.</strong> '.trim(ucwords( strtoupper( utf8_encode($info['nDpt'])))).'</td></tr>
							<tr><td><strong>DIV.</strong> '.trim(ucwords( strtoupper( utf8_encode($info['nDiv'])))).'</td></tr>
							<tr><td><strong>SEC.</strong> '.trim(ucwords( strtoupper( utf8_encode($info['nSec'])))).'</td></tr>
							<tr><td><strong>S.C.</strong> '.trim(ucwords( strtoupper( utf8_encode($info['SitioContrato'])))).'</td></tr>
							<tr><td><strong>S.T.</strong> '.trim(ucfirst( strtoupper( utf8_encode($info['SitioTrabajo'])))).'</td></tr>
						</table>
					</td>
				</tr>
			</table>';

		return $head;
	}
	
	function infoHTUsuario($unidad, $mes, $vigencia)
	{
		#$dMeses = array( '', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre' );
		$totalDiasMes = date("t",$mes);
		#	unidad, mes, vigencia
		$sql02="SELECT DISTINCT A.id_proyecto, A.id_actividad, A.unidad, A.vigencia, A.mes, A.clase_tiempo, A.localizacion, A.cargo, A.IDhorario,  ";
		$sql02=$sql02." B.nombre, B.codigo, B.cargo_defecto, C.nombre nomActividad, C.macroactividad, D.descripcion " ;
		$sql02=$sql02." FROM FacturacionProyectos A, Proyectos B, Actividades C, Clase_Tiempo D " ;
		$sql02=$sql02." WHERE A.id_proyecto = B.id_proyecto " ;
		$sql02=$sql02." AND A.id_proyecto = C.id_proyecto " ;
		$sql02=$sql02." AND A.id_actividad = C.id_actividad " ;
		$sql02=$sql02." AND A.clase_tiempo = D.clase_tiempo " ;
		$sql02=$sql02." AND A.unidad = " . $unidad ;
		$sql02=$sql02." AND A.mes = " . $mes ;
		$sql02=$sql02." AND A.vigencia = " . $vigencia ;
		$sql02=$sql02." GROUP BY A.id_proyecto, A.id_actividad, A.unidad, A.vigencia, A.mes, A.clase_tiempo, A.localizacion, A.cargo, A.IDhorario,  " ;
		$sql02=$sql02." B.nombre, B.codigo, B.cargo_defecto, C.nombre, C.macroactividad, D.descripcion " ;
		$sql02=$sql02." ORDER BY B.nombre " ;
		$cursor02 =	 mssql_query($sql02);

		$tabla = '
				<style>
					.TituloTabla2 {
						font-family: Verdana, Arial, Helvetica, sans-serif;
						font-size: 10px;
						color: #333333;
						padding-top: 1px;
						padding-right: 1px;
						padding-bottom: 1px;
						padding-left: 1px;
						background-color: #CCCCCC;
						font-weight: bold;
						text-align: center;
						vertical-align: middle;
					}
					.TxtTabla {
						font-family: Verdana, Arial, Helvetica, sans-serif;
						font-size: 10px;
						color: #333366;
						padding-top: 1px;
						padding-right: 1px;
						padding-bottom: 1px;
						padding-left: 1px;
						background-color: #E9E9E9;
					}
					.tdFestivo {
						font-family: Verdana, Arial, Helvetica, sans-serif;
						font-size: 10px;
						color: #333366;
						text-align: center;
						vertical-align: middle;
						border-top-width: 1px;
						border-right-width: 1px;
						border-bottom-width: 1px;
						border-left-width: 1px;
						border-top-style: none;
						border-right-style: none;
						border-bottom-style: none;
						border-left-style: none;
						border-top-color: #376B9A;
						border-right-color: #376B9A;
						border-left-color: #376B9A;
						background-color: #FFA500;
					}
					.tdFinSemana {
						font-family: Verdana, Arial, Helvetica, sans-serif;
						font-size: 10px;
						color: #333366;
						text-align: center;
						vertical-align: middle;
						border-top-width: 1px;
						border-right-width: 1px;
						border-bottom-width: 1px;
						border-left-width: 1px;
						border-top-style: none;
						border-right-style: none;
						border-bottom-style: none;
						border-left-style: none;
						border-top-color: #376B9A;
						border-right-color: #376B9A;
						border-left-color: #376B9A;
						background-color: #A2C0DD;
					}
					

				</style>

				<table width="100%" rules="all" border="1" style="border: double solid 2px;" cellspacing="0" cellpadding="0">
					<tr class="TituloTabla2">
						<th width="21%">&nbsp;<strong>Proyecto</strong></th>						
						<th width="2%" align="center"><strong>CT</strong></th>';
		#	INICIA For para poner los dias
				
		for($d=1; $d<=$totalDiasMes; $d++)
		{
			/*
tdFinSemana, tdFestivo
			*/
			#$nn =$d.'/'.$mes;
		$tabla = $tabla .'<th width="2%" align="center"><strong>'.$d.'</strong></th>';
		} //for d
		#	CIERRA For para poner los dias	style="border: double solid 2px;" 
			  
		$tabla = $tabla .'<th width="3%" align="center"><strong>Total</strong></th>
						<th width="4%" align="center"><strong>VoBo</strong></th>
						<th width="7%">&nbsp;<strong>Observación</strong></th>
				</tr>';
		
		while( $reg02 = mssql_fetch_array($cursor02) )
		{
			$tabla = $tabla .'<tr>
							<td width="21%" align="left" class="TxtTabla"> 
							'.$reg02['localizacion'].'-'.$reg02['codigo'].''.$reg02['cargo_defecto'].'['.$reg02['macroactividad'].'] 
							'.substr( $reg02['nombre'], 0, 15).'
							</td>						
							<td width="2%" align="center" class="TxtTabla">'.$reg02['clase_tiempo'].'</td>';
			  //25Jul2013
			  //PBM
			  //Genera los dís del mes 
			  $totalHorasRegistro = 0; //Para calcular la cantidad de horas totales por registro
			  $totalResumenRegistro = ""; //Para relacionar el resumen de todos los días con facturación
			  for ($d2=1; $d2<=$totalDiasMes; $d2++) {
				$fechaAconsultar=$pAno."-".$pMes."-".$d2;
				$esFestivo=0;
				$esDia=0;
				$usarClase="";
				$sql05 = "SELECT COUNT(*) as hayFestivo , DATEPART ( dw , '".$fechaAconsultar."' ) diaSemana";
				$sql05 = $sql05 . " FROM Festivos ";
				$sql05 = $sql05 . " where fecha = '". $fechaAconsultar ."' ";
				$cursor05 =	 mssql_query($sql05);
				if ($reg05 = mssql_fetch_array($cursor05)) {
					$esFestivo=$reg05['hayFestivo'];
					$esDia=$reg05['diaSemana'];
				}
				
				//Es festivo	
				$dia = date( 'D', strtotime($mes.'/'.$d2) );
				
				#if ( (($esDia >=2) AND ($esDia <=6)) AND ($esFestivo > 0) ) {				Wed - Thu - Fri - Sat - Sun- Mon - Tue - We
				$sqlFestivo = 'select COUNT(*) cnt from Festivos where YEAR(fecha) = '.$vigencia.' and MONTH(fecha) = '.$mes.' and DAY(fecha) = '.$d2;
				$fst = mssql_fetch_array(mssql_query($sqlFestivo));

				#/*//Es dia Normal
				if ( (trim($dia)=='Mon') OR (trim($dia)=='Tue') OR (trim($dia)=='Wed') OR (trim($dia)=='Thu') OR (trim($dia)=='Fri') ) {
					$usarClase="TxtTabla";
				}
				#*/
				//Es sábado o domingo
				#/*
				if ( (trim($dia)=='Sat') OR (trim($dia)=='Sun') ) {
					$usarClase="tdFinSemana";
				}
				if($fst['cnt']==1)
				{
					$usarClase="tdFestivo";
				}
				#*/
				#	unidad, mes, vigencia
				$horasDia=0;
				$sql06="SELECT *  ";
				$sql06=$sql06." FROM FacturacionProyectos ";
				$sql06=$sql06." WHERE unidad = " . $unidad ;
				$sql06=$sql06." AND mes = " . $mes ;
				$sql06=$sql06." AND vigencia = " . $vigencia ;
				$sql06=$sql06." AND id_proyecto = " . $reg02['id_proyecto'] ;
				$sql06=$sql06." AND id_actividad = " . $reg02['id_actividad'] ;
				$sql06=$sql06." AND DAY(fechaFacturacion) = " . $d2 ;
				$sql06=$sql06." AND IDhorario = " . $reg02['IDhorario'] ;
				$sql06=$sql06." AND clase_tiempo = " . $reg02['clase_tiempo'] ;
				$sql06=$sql06." AND localizacion = " . $reg02['localizacion'] ;
				$sql06=$sql06." AND cargo = '" . $reg02['cargo'] . "' ";
				$cursor06 =	 mssql_query($sql06);
				if ($reg06 = mssql_fetch_array($cursor06)) {
					$horasDia=$reg06['horasMesF'];

					//Totaliza por registro
					$totalHorasRegistro = $totalHorasRegistro + $horasDia ;
					
					//Resumen total por registro
					$totalResumenRegistro = $totalResumenRegistro . "<br>". $reg06['resumen'] ; 
				}
				#	tdFinSemana
					$tabla = $tabla .'<td width="2%" align="right" class="'.$usarClase.'" align="center">';

  				if ($horasDia > 0) {
					$tabla = $tabla .number_format($horasDia, 0, ",", ".");
				}

			  $tabla = $tabla .'</td>';

			  } //cierra for $d2


			$tabla = $tabla .'<td align="center" width="3%" class="TxtTabla">'.number_format($totalHorasRegistro, 0, ",", ".").'</td>
							<td width="4%" align="center" class="TxtTabla"> </td>
							<td width="7%" class="TxtTabla"> </td>
					</tr>';
		}

		$tabla = $tabla .'</table>';   
		return $tabla;
	}
?>
