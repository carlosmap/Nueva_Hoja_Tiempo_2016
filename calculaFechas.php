<?php
	function fecha2Meses( $fechaInicio, $cantidadDias, $adp, $vigencia, $mes, $proyecto, $unidad)
	{
		$band=$i=0;
		do{
			#echo '<h3>Ok</h3>';
			$fchFin = date( 'Y-m-d', strtotime($fechaInicio.'+'.$i.'days'));
			$i++;
			#echo 'Fch Fin : '.$fchFin.'<br />Fch Ini : '.$fechaInicio.'<br />Bandera : '.$band.'<br />';
			$mFin = date('m', strtotime($fchFin));
			$mIni = date('m', strtotime($fechaInicio));
			#echo $mFin.'>'.$mIni.'<br />';
			if($mFin>$mIni)
			{
				$band=1;
	#	***
				#echo '<h2>HOLA</h2>';
				$sqlPkAdp = "SELECT MAX(idAdp) idAdp FROM adpht ";
				$sqlPkAdp = $sqlPkAdp ." Where id_proyecto = ".$proyecto;
				$sqlPkAdp = $sqlPkAdp ." AND unidad = ".$unidad;
				$sqlPkAdp = $sqlPkAdp ." AND vigencia = ".$vigencia;
				$sqlPkAdp = $sqlPkAdp ." AND mes = ".$mes;
				echo $sqlPkAdp.'<br />';
				$idAdp = mssql_fetch_array(mssql_query($sqlPkAdp));
				$id = $idAdp[idAdp] + 1;
	#	***		
				$fchFin = date( 'Y-m-d', strtotime($fchFin.'-1days'));
				$sqlInsert = 'Insert Into adpht ( id_proyecto, unidad, vigencia, mes, idAdp, adp, fechaInicio, fechafinal, usuarioCrea, fechaCrea ) Values (';
				$sqlInsert = $sqlInsert . $proyecto.", ";
				$sqlInsert = $sqlInsert . $unidad.", ";
				$sqlInsert = $sqlInsert . $vigencia.", ";
				$sqlInsert = $sqlInsert . $mes.", ";
				$sqlInsert = $sqlInsert . $id.", ";
				$sqlInsert = $sqlInsert . "'".$adp."', ";
				$sqlInsert = $sqlInsert . "'".$fechaInicio."', ";
				$sqlInsert = $sqlInsert . "'".$fchFin."', ";
				
				$sqlInsert = $sqlInsert . $_SESSION['sesUnidadUsuario'].", ";
				$sqlInsert = $sqlInsert . "'".date('Y-m-d')."' )";
				
				#echo 'Insert primero : '.$sqlInsert.'<br />';
				$qryInsert = mssql_query($sqlInsert);
				if(!$qryInsert)
				{
					$error=1;
					#echo 'Error 1 : '.mssql_get_last_message().'<br />';
				}
			}
		#}while($i<$cantidadDias);#while($band!=0);
		}while($band!=1);#while($band!=0);
		$id += 1;
		#$cantidadDias -= $i;
		$fchIni2 = date( 'Y-m-d', strtotime($fchFin.'+1days'));
		$fchFin2 = date( 'Y-m-d', strtotime($fechaInicio.'+'.$cantidadDias.'days'));
		
		$sqlInsert = 'Insert Into adpht ( id_proyecto, unidad, vigencia, mes, idAdp, adp, fechaInicio, fechafinal, usuarioCrea, fechaCrea ) Values (';
		$sqlInsert = $sqlInsert . $proyecto.", ";
		$sqlInsert = $sqlInsert . $unidad.", ";
		$sqlInsert = $sqlInsert . $vigencia.", ";
		$sqlInsert = $sqlInsert . $mes.", ";
		$sqlInsert = $sqlInsert . $id.", ";
		$sqlInsert = $sqlInsert . "'".$adp."', ";
		$sqlInsert = $sqlInsert . "'".$fchIni2."', ";
		$sqlInsert = $sqlInsert . "'".$fchFin2."', ";
		
		$sqlInsert = $sqlInsert . $_SESSION['sesUnidadUsuario'].", ";
		$sqlInsert = $sqlInsert . "'".date('Y-m-d')."' )";
		$qryInsert = mssql_query($sqlInsert);
		#echo 'Insert segundo : '.$sqlInsert.'<br />';
		if(!$qryInsert)
		{
			$error=1;
			#echo 'Error 2 : '.mssql_get_last_message().'<br />';
		}
		return $error;
	}


	function cantFecha( $fechaInicio, $cantidadDias, $adp, $vigencia, $mes, $proyecto, $unidad)
	{
		$band=$i=0;
		do{
			#echo '<h3>Ok</h3>';
			$fchFin = date( 'Y-m-d', strtotime($fechaInicio.'+'.$i.'days'));
			$i++;
			#echo 'Fch Fin : '.$fchFin.'<br />Fch Ini : '.$fechaInicio.'<br />Bandera : '.$band.'<br />';
			$mFin = date('m', strtotime($fchFin));
			$mIni = date('m', strtotime($fechaInicio));
			#echo $mFin.'>'.$mIni.'<br />';
			if($mFin>$mIni)
			{
				#$band=1;
	#	***
				#echo '<h2>HOLA</h2>';
				$sqlPkAdp = "SELECT MAX(idAdp) idAdp FROM adpht ";
				$sqlPkAdp = $sqlPkAdp ." Where id_proyecto = ".$proyecto;
				$sqlPkAdp = $sqlPkAdp ." AND unidad = ".$unidad;
				$sqlPkAdp = $sqlPkAdp ." AND vigencia = ".$vigencia;
				$sqlPkAdp = $sqlPkAdp ." AND mes = ".$mes;
				echo $sqlPkAdp.'<br />';
				$idAdp = mssql_fetch_array(mssql_query($sqlPkAdp));
				$id = $idAdp[idAdp] + 1;
	#	***		
				$fchFin = date( 'Y-m-d', strtotime($fchFin.'-1days'));
				$sqlInsert = 'Insert Into adpht ( id_proyecto, unidad, vigencia, mes, idAdp, adp, fechaInicio, fechafinal, usuarioCrea, fechaCrea ) Values (';
				$sqlInsert = $sqlInsert . $proyecto.", ";
				$sqlInsert = $sqlInsert . $unidad.", ";
				$sqlInsert = $sqlInsert . $vigencia.", ";
				$sqlInsert = $sqlInsert . $mFin.", ";
				$sqlInsert = $sqlInsert . $id.", ";
				$sqlInsert = $sqlInsert . "'".$adp."', ";
				$sqlInsert = $sqlInsert . "'".$fechaInicio."', ";
				$sqlInsert = $sqlInsert . "'".$fchFin."', ";
				
				$sqlInsert = $sqlInsert . $_SESSION['sesUnidadUsuario'].", ";
				$sqlInsert = $sqlInsert . "'".date('Y-m-d')."' )";
				
				$fechaInicio = date( 'm-01-Y', strtotime($fchFin.'+1month'));
				#echo 'Insert primero : '.$sqlInsert.'<br />';
				$qryInsert = mssql_query($sqlInsert);
				if(!$qryInsert)
				{
					$error=1;
					#echo 'Error 1 : '.mssql_get_last_message().'<br />';
				}
			}
		}while($i<$cantidadDias);#while($band!=0);
		#}while($band!=1);#while($band!=0);
		$id += 1;
		#$cantidadDias -= $i;
		$fchIni2 = date( 'Y-m-d', strtotime($fchFin.'+1days'));
		$fchFin2 = date( 'Y-m-d', strtotime($fechaInicio.'+'.$cantidadDias.'days'));
		
		$sqlInsert = 'Insert Into adpht ( id_proyecto, unidad, vigencia, mes, idAdp, adp, fechaInicio, fechafinal, usuarioCrea, fechaCrea ) Values (';
		$sqlInsert = $sqlInsert . $proyecto.", ";
		$sqlInsert = $sqlInsert . $unidad.", ";
		$sqlInsert = $sqlInsert . $vigencia.", ";
		$sqlInsert = $sqlInsert . $mes.", ";
		$sqlInsert = $sqlInsert . $id.", ";
		$sqlInsert = $sqlInsert . "'".$adp."', ";
		$sqlInsert = $sqlInsert . "'".$fchIni2."', ";
		$sqlInsert = $sqlInsert . "'".$fchFin2."', ";
		
		$sqlInsert = $sqlInsert . $_SESSION['sesUnidadUsuario'].", ";
		$sqlInsert = $sqlInsert . "'".date('Y-m-d')."' )";
		$qryInsert = mssql_query($sqlInsert);
		#echo 'Insert segundo : '.$sqlInsert.'<br />';
		if(!$qryInsert)
		{
			$error=1;
			#echo 'Error 2 : '.mssql_get_last_message().'<br />';
		}
		return $error;
	}
?>