<?
/*
2010-11-26
Daniel Felipe Rentería Martínez
Crea un archivo HTML con las Directrices de la tabla en Excel creada para el reporte de facturación por proyecto
*/


/*
Funcion que ejecuta el query que genera el reporte y crea un string
con la tabla que va a ser escrita en el archivo
*/
function ejecutarQuery($query, $nombreProyecto){
	$cursor = mssql_query($query);
	$numRegs = mssql_num_rows($cursor);
	
	
	$contenido = "<html>\n";
	
	//Encabezado
	$contenido = $contenido . "<head>\n";
	$contenido = $contenido . "<title>Facturaci&oacute;n de Proyectos - Proyecto: " . $nombreProyecto . "</title>\n";
	$contenido = $contenido . "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\n";
	$contenido = $contenido . "</head>\n";
	
	//Cuerpo
	$contenido = $contenido . "<body leftmargin='0' topmargin='0' rightmargin='0' bottommargin='0' >\n";
	
	$contenido = $contenido . "<table width=\"100%\"  cellspacing=\"1\" border=\"1\">\n";
	
	if($numRegs == 0){
		$contenido = $contenido . "<tr><td colspan='9'>La Consulta no produjo Resultados.</td></tr>\n";
	} else {
		$contenido = $contenido . "<tr><td colspan='9'><strong>Reporte de Facturación. - Proyecto: " . $nombreProyecto . "<strong></td></tr>\n";
		$contenido = $contenido . "<tr>\n";
		$contenido = $contenido . "<td><strong>Unidad</strong></td>\n";
		$contenido = $contenido . "<td><strong>Nombre Completo</strong></td>\n";
		$contenido = $contenido . "<td><strong>Categoria</strong></td>\n";
		$contenido = $contenido . "<td><strong>Actividad</strong></td>\n";
		$contenido = $contenido . "<td><strong>Cargo</strong></td>\n";
		$contenido = $contenido . "<td><strong>Clase Tiempo </strong></td>\n";
		$contenido = $contenido . "<td><strong>Horas Facturadas </strong></td>\n";
		$contenido = $contenido . "<td><strong>Departamento</strong></td>\n";
		$contenido = $contenido . "<td><strong>Divisi&oacute;n</strong></td>\n";
		$contenido = $contenido . "</tr>\n";
		while($reg = mssql_fetch_array($cursor)){
		  	$contenido = $contenido . "<tr>";
			$contenido = $contenido . "<td>" . $reg['unidad'] . "</td>\n";
			$contenido = $contenido . "<td>" . ucwords(strtolower($reg['nombre'] . " " . $reg['apellidos'])) . "</td>\n";
			$contenido = $contenido . "<td>" . $reg['nombreCat'] . "</td>\n";
			$contenido = $contenido . "<td>" . strtoupper($reg['nomActividad']) . "</td>\n";
			$contenido = $contenido . "<td>" . $reg['cargo'] . "</td>\n";
			$contenido = $contenido . "<td>" . $reg['clase_tiempo'] . "</td>\n";
			$contenido = $contenido . "<td>" . $reg['horasFacturadas'] . "</td>\n";
			$contenido = $contenido . "<td>" . strtoupper($reg['nomDepto']) . "</td>\n";
			$contenido = $contenido . "<td>" . strtoupper($reg['nomDivision']) . "</td>\n";
			$contenido = $contenido . "</tr>\n";
		}
	}
	$contenido = $contenido . "</table>\n";
	$contenido = $contenido . "</body>\n";
	$contenido = $contenido . "</html>\n";
	
	
	return $contenido;
	
}

/*
Crea y guarda el archivo de html de acuerdo a los parámetros recibidos
*/
function crearArchivo($nombreArchivo, $elQuery, $nombreProyecto){
	
	//Ruta de archivos de Reportes
	$path = $_SERVER['DOCUMENT_ROOT'] . "/NuevaHojaTiempo/ReportesFact";
	//echo $path . "<br>";
	//echo $path . "/" . $nombreArchivo . "<br>";
	
	//Crea el directorio de Reportes, si no existe
	if(is_dir($path) == false){
		if(mkdir($path, 0777)){
			echo "Directorio de Reportes Creado <br>";
		} else {
			echo "No se pudo crear el Directorio <br>";
		}
	} 
	
	//Crea el Archivo del Reporte
	if($archivo = fopen($path . "/" . $nombreArchivo, "w+")){
	
		//Llena el archivo con el texto del query
		$contenido = ejecutarQuery($elQuery, $nombreProyecto);
		//Escribe el contenido
		if (fwrite($archivo, $contenido) === FALSE) {
			echo "No se puede escribir en el archivo " . $nombreArchivo . "<br>";
		}
		fclose($archivo);
		
	} else {
		//El archivo no pudo ser creado
		echo "Archivo de reporte no pudo ser creado <br>";
	}
	
}



?>