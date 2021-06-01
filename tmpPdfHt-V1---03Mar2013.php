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
				<tr class="TxtTabla">
					<td width="15%" valign="middle">
						<br /><br />&nbsp;
						<br />&nbsp;
						<img src="pics/LogoIngetecPNG2.png" width=150 heigth="75" />
					</td>
					<td width="60%" align="center">
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
					<td width="25%">
						<table width="100%" class="TxtTabla">
							<tr>
								<td width="100%">
								'.trim(ucwords( strtoupper( utf8_encode($info['nombre'])))).' '.trim(ucwords( strtoupper( utf8_encode($info['apellidos'])))).'<br />
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
		$sql02="SELECT DISTINCT A.esInterno, A.id_proyecto, A.id_actividad, A.unidad, A.vigencia, A.mes, A.clase_tiempo, A.localizacion, A.cargo, A.IDhorario,  ";
		$sql02=$sql02." B.nombre, B.codigo, B.cargo_defecto, C.nombre nomActividad, C.macroactividad, D.descripcion " ;
		$sql02=$sql02." FROM FacturacionProyectos A, Proyectos B, Actividades C, Clase_Tiempo D " ;
		$sql02=$sql02." WHERE A.id_proyecto = B.id_proyecto " ;
		$sql02=$sql02." AND A.id_proyecto = C.id_proyecto " ;
		$sql02=$sql02." AND A.id_actividad = C.id_actividad " ;
		$sql02=$sql02." AND A.clase_tiempo = D.clase_tiempo " ;
		$sql02=$sql02." AND A.unidad = " . $unidad ;
		$sql02=$sql02." AND A.mes = " . $mes ;
		$sql02=$sql02." AND A.vigencia = " . $vigencia ;
		$sql02=$sql02." GROUP BY A.esInterno, A.id_proyecto, A.id_actividad, A.unidad, A.vigencia, A.mes, A.clase_tiempo, A.localizacion, A.cargo, A.IDhorario,  " ;
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
					<tr>
						<th width="17%">&nbsp;<strong>Proyecto</strong></th>						
						<th width="2%" align="center"><strong>CT</strong></th>';
		#	INICIA For para poner los dias			
		for($d=1; $d<=$totalDiasMes; $d++)
		{
			$tabla = $tabla .'<th width="2%" align="center"><strong>'.$d.'</strong></th>';
		} //for d
		#	CIERRA For para poner los dias	style="border: double solid 2px;" 

		$tabla = $tabla .'<th width="3%" align="center"><strong>Total</strong></th>
						<th width="4%" align="center"><strong>VoBo</strong></th>
						<td width="12%">&nbsp;<strong>Observación</strong></td>
				</tr>';
		$total = 0;
		while( $reg02 = mssql_fetch_array($cursor02) )
		{	
				/*
			$info = $reg02['localizacion'].'-'.$reg02['codigo'].''.$reg02['cargo_defecto'].'['.$reg02['macroactividad'].'] '.substr( $reg02['nombre'], 0, 15);
			$tabla = $tabla .'<tr>
							<td width="17%" align="left" > 
							'.substr($info, 0, 33).'
							</td>						
							<td width="2%" align="center" >'.$reg02['clase_tiempo'].'</td>';
				#*/
				$tabla = $tabla .'<tr>
								<td width="17%" align="left" > 
								'.$reg02['localizacion'].'-'.$reg02['codigo'].''.$reg02['cargo_defecto'].'['.$reg02['macroactividad'].'] '.substr( $reg02['nombre'], 0, 15).'
								</td>						
								<td width="2%" align="center" >'.$reg02['clase_tiempo'].'</td>';
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
	
					$sqlFestivo = 'select COUNT(*) cnt from Festivos where YEAR(fecha) = '.$vigencia.' and MONTH(fecha) = '.$mes.' and DAY(fecha) = '.$d2;
					$fst = mssql_fetch_array(mssql_query($sqlFestivo));
					$usarClase="";
	
					#	Es sábado o domingo
					if ( (trim($dia)=='Sat') OR (trim($dia)=='Sun') ) {
						$usarClase="tdFinSemana";
					}
					#	Es festivo
					if($fst['cnt']==1)
					{
						$usarClase="tdFestivo";
					}
					#*/
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
	
					if ($horasDia > 0){
						$tabla = $tabla .number_format($horasDia, 0, ",", ".");
					}
					$tabla = $tabla .'</td>';
			} //cierra for $d2
			#$total += $totalHorasRegistro;

			$tabla = $tabla .'<td align="center" width="3%"><strong>'.number_format($totalHorasRegistro, 0, ",", ".").'</strong></td>';
			
			$tieneVBproy="";
			$fechaVBproy="";
			$encargadoVBproy="";

			$sql13 = "SELECT A.*, B.nombre, B.apellidos, B.NombreCorto ";
			$sql13 = $sql13 . " FROM VoBoFactuacionProyHT A, Usuarios B ";
			$sql13 = $sql13 . " WHERE A.unidadEncargado = B.unidad ";
			$sql13 = $sql13 . " AND A.id_proyecto = " . $reg02['id_proyecto'] ;
			$sql13 = $sql13 . " AND A.id_actividad = " . $reg02['id_actividad'] ;
			$sql13 = $sql13 . " AND A.unidad = " . $unidad ;
			$sql13 = $sql13 . " AND A.vigencia = " . $vigencia ;
			$sql13 = $sql13 . " AND A.mes = " . $mes ;
			$sql13 = $sql13 . " AND A.esInterno = '" . $reg02['esInterno'] . "'";
			
			/*
			$sql13 = "SELECT A.*, B.nombre, B.apellidos, B.NombreCorto ";
			$sql13 = $sql13 . " FROM VoBoFactuacionProyHT A, Usuarios B ";
			$sql13 = $sql13 . " WHERE A.unidadEncargado = B.unidad ";
			$sql13 = $sql13 . " AND A.id_proyecto = " . $reg02['id_proyecto'] ;
			$sql13 = $sql13 . " AND A.id_actividad = " . $reg02['id_actividad'] ;
			$sql13 = $sql13 . " AND A.unidad = " . $unidad ;
			$sql13 = $sql13 . " AND A.vigencia = " . $vigencia;
			$sql13 = $sql13 . " AND A.mes = " . $mes ;
			$sql13 = $sql13  . " AND A.esInterno = '" . $reg02['esInterno'] . "'";
			#*/
			$cursor13 =	 mssql_query($sql13);
		    if ($reg13 = mssql_fetch_array($cursor13)) {
			  	$tieneVBproy = $reg13['validaEncargado'];
	 			$fechaVBproy = date("M d Y ", strtotime($reg13['fechaAprEnc'])) ;
			  	//$encargadoVBproy = $reg13['apellidos'] . " " . $reg13['nombre'] ;
				$encargadoVBproy = $reg13['NombreCorto']  ;
			}
			$img = '';
			if ($tieneVBproy == '1') {
            	$img = '<img src="img/images/Aprobado.gif" width="10" height="12" />';
			} 

			$tabla = $tabla .'<td width="4%" align="center">&nbsp;'.$img.'</td>
							  <td width="12%">';
			$sql10="SELECT * FROM AdpHT ";
			$sql10=$sql10." WHERE id_proyecto = " . $reg02['id_proyecto'];
			$sql10=$sql10." AND unidad = " . $unidad ;
			$sql10=$sql10." AND vigencia = " . $vigencia ;
			$sql10=$sql10." and mes = " . $mes ;
			$cursor10 =	 mssql_query($sql10);
			$cant = mssql_num_rows($cursor10);
			$rCant = 0;
			$slinea = 0;
			if($cant>0)
			{
				while ($reg10 = mssql_fetch_array($cursor10)) {
					$rCant++;
					$slLinea = '';
					$tabla = $tabla .'&nbsp;'.trim($reg10['adp']).';';# . "<br />";
					if($slinea==3&&$rCant<$cant)
					#if($slinea==3)
					{
						$slinea==0;
						$slLinea = "<br />";
						#$tabla = $tabla . "<br />";
					}
					$tabla = $tabla . $slLinea;
					$slinea++;
				}
			}
			else
			{
				$tabla = $tabla .'&nbsp;'.utf8_encode($encargadoVBproy);#.'</td></tr>';
			}
			$tabla = $tabla .'</td></tr>';
		}
		
		$tabla = $tabla .'<tr >
						<th width="19%" colspan="2">&nbsp;<strong>Total Clases de Tiempo 1 - 2 - 3 Y 11</strong></th>';
		#	INICIA For para poner los dias			
		for($d=1; $d<=$totalDiasMes; $d++)
		{
			$sql05 = "SELECT COUNT(*) as hayFestivo , DATEPART ( dw , '".$fechaAconsultar."' ) diaSemana";
			$sql05 = $sql05 . " FROM Festivos ";
			$sql05 = $sql05 . " where fecha = '". $fechaAconsultar ."' ";
			$cursor05 =	 mssql_query($sql05);
			if ($reg05 = mssql_fetch_array($cursor05)) {
				$esFestivo=$reg05['hayFestivo'];
				$esDia=$reg05['diaSemana'];
			}
			
			//Es festivo	
			$dia = date( 'D', strtotime($mes.'/'.$d) );
			
			#if ( (($esDia >=2) AND ($esDia <=6)) AND ($esFestivo > 0) ) {				Wed - Thu - Fri - Sat - Sun- Mon - Tue - We
			$sqlFestivo = 'select COUNT(*) cnt from Festivos where YEAR(fecha) = '.$vigencia.' and MONTH(fecha) = '.$mes.' and DAY(fecha) = '.$d;
			$fst = mssql_fetch_array(mssql_query($sqlFestivo));
			$usarClase="";

			if ( (trim($dia)=='Sat') OR (trim($dia)=='Sun') ) {
				$usarClase="tdFinSemana";
			}
			if($fst['cnt']==1)
			{
				$usarClase="tdFestivo";
			}
			#	*****
			$totalDiario=0;
			$sql07="SELECT SUM(horasMesF) totDia  ";
			$sql07=$sql07." FROM FacturacionProyectos ";
			$sql07=$sql07." WHERE unidad =" . $unidad ;
			$sql07=$sql07." AND mes = " . $mes ;
			$sql07=$sql07." AND vigencia = " . $vigencia ;
			$sql07=$sql07." AND DAY(fechaFacturacion) = " . $d ;
			$sql07=$sql07." AND clase_tiempo IN (1, 2, 3, 11) ";
			$cursor07 =	 mssql_query($sql07);
			if ($reg07 = mssql_fetch_array($cursor07)) {
				$totalDiario=$reg07['totDia'];
				$totalHorasMensual=$totalHorasMensual+$totalDiario;
			}
			$hr = '';
			if ($totalDiario > 0) {
				$hr = number_format($totalDiario, 0, ",", ".");
			}
			#	*****
			$tabla = $tabla .'<th width="2%" align="center" class="'.$usarClase.'"><strong>'.$hr.'</strong></th>';
			$total += $hr;
		} 
		#	CIERRA For para poner los dias	style="border: double solid 2px;" 
		$tabla = $tabla .'<th width="3%" align="center"><strong>'.$total.'</strong></th>
						<th width="4%" align="center"><strong></strong></th>
				</tr>';
		
		#	*****	*****	*****	*****	*****	*****	*****	*****	*****	*
		# ***	*****	*****	*****	*****	*****	*****	*****	*****	*****
		#	*****	*****	*****	*****	*****	*****	*****	*****	*****	*
		#	*****	*****	EMPIEZA LA RELACIÓN DE LOS VIÁTICOS 	*****	*****	*
		#	*****	*****	EMPIEZA LA RELACIÓN DE LOS VIÁTICOS 	*****	*****	*
		#	*****	*****	*****	*****	*****	*****	*****	*****	*****	*
		# ***	*****	*****	*****	*****	*****	*****	*****	*****	*****
		#	*****	*****	*****	*****	*****	*****	*****	*****	*****	*

		$col = 5 + $totalDiasMes;
		$tabla = $tabla .'
		<tr class="TituloTabla2"><th colspan="'.$col.'"> RELACI&Oacute;N DE VIÁTICOS</th></tr>';
			
			$sql11 = "SELECT DISTINCT A.id_proyecto, A.id_actividad, A.unidad, A.vigencia, A.mes, A.esInterno, A.IDhorario, A.clase_tiempo, A.localizacion,  ";
			$sql11 = $sql11 . " A.cargo, A.IDsitio, A.IDTipoViatico, ";
			$sql11 = $sql11 . " B.nombre nomProyecto, B.codigo, B.cargo_defecto, ";
			$sql11 = $sql11 . " C.nombre nomActividad, C.macroactividad, ";
			$sql11 = $sql11 . " D.Lunes, D.Martes, D.Miercoles, D.Jueves, D.Viernes, D.Sabado, D.Domingo, ";
			$sql11 = $sql11 . " E.NomSitio, ";
			$sql11 = $sql11 . " F.NomTipoViatico ";
			$sql11 = $sql11 . " FROM ViaticosProyectosHT A, Proyectos B, Actividades C, Horarios D, SitiosTrabajo E, TiposViatico F ";
			$sql11 = $sql11 . " WHERE A.id_proyecto = B.id_proyecto ";
			$sql11 = $sql11 . " AND A.id_proyecto = C.id_proyecto ";
			$sql11 = $sql11 . " AND A.id_actividad = C.id_actividad ";
			$sql11 = $sql11 . " AND A.IDhorario = D.IDhorario ";
			$sql11 = $sql11 . " AND A.id_proyecto = E.id_proyecto ";
			$sql11 = $sql11 . " AND A.IDsitio = E.IDsitio ";
			$sql11 = $sql11 . " AND A.IDTipoViatico = F.IDTipoViatico ";
			$sql11 = $sql11 . " AND A.vigencia = " . $vigencia ;
			$sql11 = $sql11 . " AND A.mes = " . $mes;
			$sql11 = $sql11 . " AND A.unidad = " . $unidad ;
			/*
			$sql11 = "SELECT DISTINCT A.id_proyecto, A.id_actividad, A.unidad, A.vigencia, A.mes, A.esInterno, A.IDhorario, A.clase_tiempo, A.localizacion,  ";
			$sql11 = $sql11 . " A.cargo, A.IDsitio, A.IDTipoViatico, ";
			$sql11 = $sql11 . " B.nombre nomProyecto, B.codigo, B.cargo_defecto, ";
			$sql11 = $sql11 . " C.nombre nomActividad, C.macroactividad, ";
			$sql11 = $sql11 . " D.Lunes, D.Martes, D.Miercoles, D.Jueves, D.Viernes, D.Sabado, D.Domingo, ";
			$sql11 = $sql11 . " E.NomSitio, ";
			$sql11 = $sql11 . " F.NomTipoViatico ";
			$sql11 = $sql11 . " FROM ViaticosProyectosHT A, Proyectos B, Actividades C, Horarios D, SitiosTrabajo E, TiposViatico F ";
			$sql11 = $sql11 . " WHERE A.id_proyecto = B.id_proyecto ";
			$sql11 = $sql11 . " AND A.id_proyecto = C.id_proyecto ";
			$sql11 = $sql11 . " AND A.id_actividad = C.id_actividad ";
			$sql11 = $sql11 . " AND A.IDhorario = D.IDhorario ";
			$sql11 = $sql11 . " AND A.id_proyecto = E.id_proyecto ";
			$sql11 = $sql11 . " AND A.IDsitio = E.IDsitio ";
			$sql11 = $sql11 . " AND A.IDTipoViatico = F.IDTipoViatico ";
			$sql11 = $sql11 . " AND A.vigencia = " . $vigencia ;
			$sql11 = $sql11 . " AND A.mes = " . $mes;
			#*/
			$cursor11 =	 mssql_query($sql11);
			while ($reg11 = mssql_fetch_array($cursor11)) {
				//--Trae los viáticos de una configutación dada ordenado por fecha de inicio
				
				$sql12 = " SELECT *, DAY(FechaIni) diaIniV,  DAY(FechaFin) diaFinV ";
				$sql12 = $sql12 . " FROM ViaticosProyectosHT ";
				$sql12 = $sql12 . " WHERE id_proyecto = " . $reg11['id_proyecto'];
				$sql12 = $sql12 . " AND id_actividad = " . $reg11['id_actividad'];
				$sql12 = $sql12 . " AND vigencia = " . $reg11['vigencia'];
				$sql12 = $sql12 . " AND mes = " . $reg11['mes'];
				$sql12 = $sql12 . " AND esInterno = '" . $reg11['esInterno'] . "' ";
				$sql12 = $sql12 . " AND IDhorario = " . $reg11['IDhorario'];
				$sql12 = $sql12 . " AND clase_tiempo =" . $reg11['clase_tiempo'];
				$sql12 = $sql12 . " AND localizacion = " . $reg11['localizacion'];
				$sql12 = $sql12 . " AND cargo = '" . $reg11['cargo'] . "' ";
				$sql12 = $sql12 . " AND IDsitio = " . $reg11['IDsitio'];
				$sql12 = $sql12 . " AND IDTipoViatico = " . $reg11['IDTipoViatico'];
				$sql12 = $sql12 . " AND unidad = " . $unidad;
				$sql12 = $sql12 . " order by fechaIni ";

				/*
				$sql12 = " SELECT *, DAY(FechaIni) diaIniV,  DAY(FechaFin) diaFinV ";
				$sql12 = $sql12 . " FROM ViaticosProyectosHT ";
				$sql12 = $sql12 . " WHERE id_proyecto = " . $reg11['id_proyecto'] ;
				$sql12 = $sql12 . " AND id_actividad = " . $reg11['id_actividad'] ;
				$sql12 = $sql12 . " AND vigencia = " . $reg11['vigencia'] ;
				$sql12 = $sql12 . " AND mes = " . $reg11['mes'] ;
				$sql12 = $sql12 . " AND esInterno = '" . $reg11['esInterno'] . "' ";
				$sql12 = $sql12 . " AND IDhorario = " . $reg11['IDhorario'] ;
				$sql12 = $sql12 . " AND clase_tiempo =" . $reg11['clase_tiempo'] ;
				$sql12 = $sql12 . " AND localizacion = " . $reg11['localizacion'] ;
				$sql12 = $sql12 . " AND cargo = '" . $reg11['cargo'] . "' ";
				$sql12 = $sql12 . " AND IDsitio = " . $reg11['IDsitio'] ;
				$sql12 = $sql12 . " AND IDTipoViatico = " . $reg11['IDTipoViatico'] ;
				$sql12 = $sql12 . " AND unidad = " . $unidad ;
				$sql12 = $sql12 . " order by fechaIni ";
				#*/
				$cursor12 =	 mssql_query($sql12);
				$arrayViaticos = array();
				$aV=0;
				$totCantViaticos=0;
				//Llenar el array con 0
				for($aV=0; $aV<=$totalDiasMes; $aV++) {
					$arrayViaticos[$aV] = '&nbsp;';
				}
				while ($reg12 = mssql_fetch_array($cursor12)) {
					for($aV2=$reg12['diaIniV']; $aV2<=$reg12['diaFinV']; $aV2++) {
					 	$arrayViaticos[$aV2] =  $reg12['viaticoCompleto'] ;
						$totCantViaticos = $totCantViaticos + 1;
					}
				}
				$tabla = $tabla .'<tr>
								<th colspan="2">&nbsp;'.substr( $reg11['nomProyecto'], 0, 15).' ['.$reg11['macroactividad'].'] 
								</th>';
				for ($d3=1; $d3<=$totalDiasMes; $d3++) {
					$dia = date( 'D', strtotime($mes.'/'.$d3) );
					
					$sqlFestivo = 'select COUNT(*) cnt from Festivos where YEAR(fecha) = '.$vigencia.' and MONTH(fecha) = '.$mes.' and DAY(fecha) = '.$d3;
					$fst = mssql_fetch_array(mssql_query($sqlFestivo));
					$usarClase="";
					//Es sábado o domingo
					if ( (trim($dia)=='Sat') OR (trim($dia)=='Sun') ) {
						$usarClase="tdFinSemana";
					}
					#	Festivos
					if($fst['cnt']==1)
					{
						$usarClase="tdFestivo";
					}
					$tabla = $tabla .'<th width="2%" align="center" class="'.$usarClase.'">&nbsp;'.$arrayViaticos[$d3].'</th>';
				}
				$nDias = 0;
				if ($totalHorasMensual > 0) {
					$nDias = number_format($totalHorasMensual, 0, ",", ".");
				}
				#	*****
				$tieneVBviatico="";
				$fechaVBviatico="";
				$encargadoVBviatico="";
				$imgViatico = '';
				
			   $sql14="SELECT A.*, B.nombre, B.apellidos, B.NombreCorto ";
			   $sql14=$sql14." FROM VoBoViaticosProyHT A, Usuarios B ";
			   $sql14=$sql14." WHERE A.unidad = B.unidad ";
			   $sql14=$sql14." AND A.id_proyecto = " . $reg11['id_proyecto'] ;
			   $sql14=$sql14." AND A.id_actividad = " . $reg11['id_actividad'] ;
			   $sql14=$sql14." AND A.unidad = " . $unidad ;
			   $sql14=$sql14." AND A.vigencia = " . $reg11['vigencia'] ;
			   $sql14=$sql14." AND A.mes = " . $reg11['mes'] ;
			   $sql14=$sql14." AND A.esInterno = '" . $reg11['esInterno'] . "' ";
			   $sql14=$sql14." AND A.IDhorario = " . $reg11['IDhorario'] ;
			   $sql14=$sql14." AND A.clase_tiempo = " . $reg11['clase_tiempo'] ;
			   $sql14=$sql14." AND A.localizacion = " . $reg11['localizacion'] ;
			   $sql14=$sql14." AND A.cargo = '" . $reg11['cargo'] . "' ";
			   $sql14=$sql14." AND A.IDsitio = " . $reg11['IDsitio'] ;
			   $sql14=$sql14." AND A.IDTipoViatico = " . $reg11['IDTipoViatico'] ;

				/*
				$sql14="SELECT A.*, B.nombre, B.apellidos, B.NombreCorto ";
				$sql14=$sql14." FROM VoBoViaticosProyHT A, Usuarios B ";
				$sql14=$sql14." WHERE A.unidad = B.unidad ";
				$sql14=$sql14." AND A.id_proyecto = " . $reg11['id_proyecto'] ;
				$sql14=$sql14." AND A.id_actividad = " . $reg11['id_actividad'] ;
				$sql14=$sql14." AND A.unidad = " . $unidad ;
				$sql14=$sql14." AND A.vigencia = " . $reg11['vigencia'] ;
				$sql14=$sql14." AND A.mes = " . $reg11['mes'] ;
				$sql14=$sql14." AND A.esInterno = '" . $reg11['esInterno'] . "' ";
				$sql14=$sql14." AND A.IDhorario = " . $reg11['IDhorario'] ;
				$sql14=$sql14." AND A.clase_tiempo = " . $reg11['clase_tiempo'] ;
				$sql14=$sql14." AND A.localizacion = " . $reg11['localizacion'] ;
				$sql14=$sql14." AND A.cargo = '" . $reg11['cargo'] . "' ";
				$sql14=$sql14." AND A.IDsitio = " . $reg11['IDsitio'] ;
				$sql14=$sql14." AND A.IDTipoViatico = " . $reg11['IDTipoViatico'] ;
				#*/
				$cursor14 =	 mssql_query($sql14);
				if ($reg14 = mssql_fetch_array($cursor14))
				{
					$tieneVBviatico=$reg14['validaEncargado'];
					$fechaVBviatico= date("M d Y ", strtotime($reg14['fechaAprueba'])) ;
					//$encargadoVBviatico = $reg14['apellidos'] . " " . $reg14['nombre'] ;
					$encargadoVBviatico=$reg14['NombreCorto']  ;
					$imgViatico = '<img src="img/images/Aprobado.gif" width="10" height="12" />';
				}
				
				#	*****
				$tabla = $tabla .'<th width="3%" align="center"><strong>'.$totCantViaticos.'</strong></th>';
				
				$tabla = $tabla .'<th width="4%" align="center">'.$imgViatico.'</th>';
				
				$tabla = $tabla .'<th width="12%">&nbsp;<strong>'.$reg11['NomTipoViatico'].'</strong>';
				if($reg11['NomSitio']!='')
				{
					$tabla = $tabla.'<br />&nbsp;<strong>Sitio: </strong>'.utf8_encode($reg11['NomSitio']);
				}
				$tabla = $tabla .'</th></tr>';
				
			}
		#	*****	
		$tabla = $tabla .'</table>
			<br />&nbsp;
			
		';   
		
		return $tabla;
	}
	
	function detalleHt($unidad, $mes, $vigencia)
	{
		$sqlInfo = '';
		$detalle = '
				<table rules="all" border="1" width="100%" cellpadding="3" cellspacing="0" style="border: solid double 1px;">
					<tr><td width="5%">&nbsp;<strong>Dia</strong></td><td width="95%">&nbsp;<strong>Resumen</strong></td></tr>';
		$totalDiasMes = date("t",$mes);
		
		for($d=1; $d<=$totalDiasMes; $d++)
		{							
			$sqlFestivo = 'select COUNT(*) cnt from Festivos where YEAR(fecha) = '.$vigencia.' and MONTH(fecha) = '.$mes.' and DAY(fecha) = '.$d;
			$fst = mssql_fetch_array(mssql_query($sqlFestivo));
			$dia = date( 'D', strtotime($mes.'/'.$d) );

			$mostrar=1;
			if ( (trim($dia)=='Sat') OR (trim($dia)=='Sun') ) {
				$mostrar=0;
			}
			if($fst['cnt']==1)
			{
				$mostrar=0;
			}
			if($mostrar==1)
			{
				$sqlResumen = 'select resumen from FacturacionProyectos where 
							   unidad = '.$unidad.' AND mes = '.$mes.' AND vigencia = '.$vigencia.' AND
							   YEAR(FECHAFACTURACION) = '.$vigencia.' AND MONTH(FECHAFACTURACION)='.$mes.' AND DAY(FECHAFACTURACION)='.$d;
				$qryResumen = mssql_query($sqlResumen);

				$detalle = $detalle .'
					<tr>
						<td width="5%" align="center"><strong>'.$d.'</strong></td>
						<td width="95%">';
				while($row=mssql_fetch_array($qryResumen))
				{
					$detalle = $detalle . '&nbsp;*'.trim($row['resumen']).'.';
				}
				$detalle = $detalle .'</td>
					</tr>';
			}
		}
		$detalle = $detalle . '</table>';
		
		return $detalle;
	}
	
	function resumen($unidad, $mes, $vigencia)
	{
		$sqlFirmaUsuario = ''.$unidad.''.$mes.''.$vigencia;
		$qryFirmaContratos = mssql_query($sqlFirmaUsuario);
				
		$sqlFirmaJefe = ''.$unidad.''.$mes.''.$vigencia;
		$qryFirmaContratos = mssql_query($sqlFirmaJefe);
				
		$sqlFirmaDepartamento = ''.$unidad.''.$mes.''.$vigencia;
		$qryFirmaContratos = mssql_query($sqlFirmaDepartamento);
				
		$sqlFirmaContratos = ''.$unidad.''.$mes.''.$vigencia;
		$qryFirmaContratos = mssql_query($sqlFirmaContratos);
		
		$resumen = '
		<table width="100%" rules="all" border="1" style="border: double solid 2px;" cellspacing="0" cellpadding="0">
				<tr>
					<td width="25%" align="center">CT CLASE DE TIEMPO</td>						
					<td width="25%" align="center">HORARIO</td>					
					<td width="25%" align="center">CT CLASE DE TIEMPO</td>						
					<td width="25%" align="center">HORARIO</td>					
				</tr>
				<tr>
					<th>
						&nbsp;1 Ordinario<br />
						&nbsp;2 Ordinario (1)<br />
						&nbsp;3 Nocturno Ordinario<br />
						&nbsp;4 Extra Ordinario (2)<br />
						&nbsp;5 Extra nocturno
					</th>						
					<th>
						&nbsp;6 am-10 pm<br />
						&nbsp;6 am-10 pm<br />
						&nbsp;10 pm-6 am<br />
						&nbsp;6 am-10 pm<br />
						&nbsp;10 pm-6 am
					</th>					
					<th>
						&nbsp;6 Descanso obligatorio <br />
						&nbsp;7 Extra descanso obligatorio (3)<br />
						&nbsp;8 Nocturno descanso obligatorio <br />
						&nbsp;9 Extra descanso obligatorio nocturno <br />
						&nbsp;10 Por compensar <br />
						&nbsp;11 Compensado <br />
						&nbsp;Viático (Clase o Localidad) 
					</th>						
					<th>
						&nbsp;6 am-10 pm<br />
						&nbsp;6 am-10 pm<br />
						&nbsp;0am-6am y 10pm-12pm<br />
						&nbsp;0am-6am y 10pm-12pm
					</th>					
				</tr>
			</table>';
		return $resumen;
	}
	
	function firmasHt($unidad, $mes, $vigencia)
	{
		#	******
		$tieneFechaEnvio="";
		$tieneVBJefe="";
		$tieneFechaJefe="";
		$tieneNombreJefe = "";
		$tieneVBContratos="";
		$tieneFechaContratos="";
		$tieneNombreContratos="";
		$sql15 = "SELECT A.*, B.nombre nomJefe, B.apellidos apeJefe, B.NombreCorto nomCortoJefe, C.nombre nomContratos, C.apellidos apeContratos, C.NombreCorto nomCortoContratos ";
		$sql15 = $sql15 . " FROM VoBoFirmasHT A, Usuarios B, Usuarios C  ";
		$sql15 = $sql15 . " WHERE A.unidadJefe *= B.unidad ";
		$sql15 = $sql15 . " AND A.unidadContratos *= C.unidad ";
		$sql15 = $sql15 . " AND A.unidad = " . $unidad ;
		$sql15 = $sql15 . " AND A.vigencia = " . $vigencia ;
		$sql15 = $sql15 . " AND A.mes = " . $mes ;
		$cursor15 = mssql_query($sql15);
		#/*
		if ($reg15 = mssql_fetch_array($cursor15))
		{
			$tieneFechaEnvio = date("M d Y ", strtotime($reg15['fechaEnvio'])) ;
		
			$tieneVBJefe=$reg15['validaJefe'];
			if (trim($reg15['fechaAprueba']) != "")
			{
				$tieneFechaJefe = date("M d Y ", strtotime($reg15['fechaAprueba'])) ;
			}
			$tieneNombreJefe = utf8_encode( strtoupper( trim($reg15['nomJefe']) . " " . trim($reg15['apeJefe']) ) );
			
			$tieneVBContratos=$reg15['validaContratos'];
			if (trim($reg15['fechaContratos']) != "")
			{
				$tieneFechaContratos= date("M d Y ", strtotime($reg15['fechaContratos'])) ;
			}
			$tieneNombreContratos = utf8_decode( strtoupper(trim($reg15['nomContratos'])). " " . trim($reg15['apeContratos']));
		}
		#*/
		
		#	******
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

		<table width="100%" rules="all" style="border: double solid 1px;" border="1" cellspacing="0" cellpadding="0">
            <tr> <td class="TituloTabla2">RELACI&Oacute;N DE FIRMAS </td> </tr>
          </table>';
		$tabla = $tabla .'<table rules="all" style="border: double solid 1px;" width="100%" border="1" cellspacing="0" cellpadding="0">
            <tr class="">
              <td align="center"><strong>FIRMA DEL EMPLEADO</strong></td>
              <td align="center"><strong>JEFE INMEDIATO</strong></td>
              <td align="center"><strong>CONTRATOS</strong></td>
            </tr>
            <tr align="center" >
              <td align="center">'.$tieneFechaEnvio.'</td>
              <td align="center">';
		if($tieneVBJefe == '1')
		{ 
			#	 width="10" height="12"
        	#$tabla = $tabla .'<img src="img/images/Si.gif" width="16" height="14" /> <br>';
			$tabla = $tabla .'<img src="img/images/Si.gif" width="12" height="10" /> <br>';
		}
		
		if ($tieneVBJefe == '0')
		{ 
			#$tabla = $tabla .'<img src="img/images/icoAlerta.gif" width="16" height="16" /> <br>';
			$tabla = $tabla .'<img src="img/images/icoAlerta.gif" width="12" height="12" /> <br>';
		}
		
		$tabla = $tabla .$tieneNombreJefe . '<br />';
		$tabla = $tabla .$tieneFechaJefe . '<br />';
		$tabla = $tabla .'</td>
              <td align="center">';
		if ($tieneVBContratos == '1')
		{
			$tabla = $tabla .'<img src="img/images/Si.gif" width="12" height="10" /> <br>';
		}
		
		if ($tieneVBContratos == '0') 
		{
			$tabla = $tabla .'<img src="img/images/icoAlerta.gif" width="12" height="12" /> <br />';
		} 
		$tabla = $tabla .$tieneNombreContratos .'<br />';
		$tabla = $tabla .$tieneFechaContratos . '<br />';
		$tabla = $tabla .'	  </td>
            </tr>
          </table>';
		return $tabla;
	}
?>