<?
session_start();

//Si $cualAno viene vacio es porque no han cambiado las listas en la hoja de tiempo, 
//por lo tanto el mes activo es el actual
if (trim($cualAno) == "") {
	$anoAut=date("Y");
	$mesAut=date("m");
}
else {
	$anoAut=$cualAno;
	$mesAut=$cualMes;
}

//Establecer la conexión a la base de datos
include "funciones.php";
include "validaUsrBd.php";

//Verificar si el usuario ya existe para mostrar el jefe ya seleccionado
$sql="Select * from AutorizacionesHT ";
$sql=$sql." where vigencia = " . $anoAut;
$sql=$sql." and mes = " . $mesAut ;
//31Oct2007
//Si se ha cambiado el usuario, la variable de session $_SESSION["sesUnidadUsuario"] y 
//$laUnidad son diferentes, por lo tanto para la hoja de tiempo
//se continua trabajando con la Unidad.
if (trim($_SESSION["sesUnidadUsuario"]) != trim($laUnidad)  ) {
	$sql=$sql." and unidad = " . $laUnidad;
}
else {
	$sql=$sql." and unidad = " . $_SESSION["sesUnidadUsuario"];
}
$cursor = mssql_query($sql);
if ($reg=mssql_fetch_array($cursor)) {
	$elUsuarioJefe = $reg[unidadJefe];
}
//echo "La sesion=" . $_SESSION["sesUnidadUsuario"] . "<br>";
//echo "La unidad=" . $laUnidad . "<br>";

//Encontrar la categoria vigente para la selección de usuarios de la base de dato del portal
//@mssql_select_db("GestiondeInformacionDigital",$CONECTADO);
@mssql_select_db("GestiondeInformacionDigital");
$sql="Select * from CategoriaAutoriza";
$cursor = mssql_query($sql);
if ($reg=mssql_fetch_array($cursor)) {
	$laCategoria = $reg[id_categoria];
	}
else {
	$laCategoria= 0;
}

//04Mar2011
//PBM
//Validar que la persona no haya grabado facturación antes de su fecha de ingreso o despues de la fecha de retiro

//Encontrar la fecha de ingreso y retiro del usuario
$vSql01="Select unidad, nombre, apellidos, fechaIngreso, fechaRetiro, ";
$vSql01=$vSql01." DAY(fechaIngreso) diaIngreso, MONTH(fechaIngreso) mesIngreso, YEAR(fechaIngreso) anoIngreso, ";
$vSql01=$vSql01." DAY(fechaRetiro) diaRetiro, MONTH(fechaRetiro) mesRetiro, YEAR(fechaRetiro) anoRetiro ";
$vSql01=$vSql01." from HojaDeTiempo.dbo.Usuarios ";
if (trim($_SESSION["sesUnidadUsuario"]) != trim($laUnidad)  ) {
	$vSql01=$vSql01." where Unidad =" . $laUnidad;
}
else {
	$vSql01=$vSql01." where Unidad =" . $_SESSION["sesUnidadUsuario"];
}
$vCursor01 = mssql_query($vSql01);
if ($vReg01=mssql_fetch_array($vCursor01)) {
	$pdiaIngreso=$vReg01[diaIngreso];
	$pmesIngreso=$vReg01[mesIngreso];
	$panoIngreso=$vReg01[anoIngreso];
	$pdiaRetiro=$vReg01[diaRetiro];
	$pmesRetiro=$vReg01[mesRetiro];
	$panoRetiro=$vReg01[anoRetiro];
	$pfechaIngreso=$vReg01[fechaIngreso];
	$pfechaRetiro=$vReg01[fechaRetiro];
}


//Encontrar la mínima y máxima fecha del mes y año de facturación activo
$vSql02="SELECT minFechaFac, maxFechaFac, ";
$vSql02=$vSql02." DAY(minFechaFac) diaMinFact, MONTH(minFechaFac) mesMinFact, YEAR(minFechaFac) anoMinFact, ";
$vSql02=$vSql02." DAY(maxFechaFac) diaMaxFact, MONTH(maxFechaFac) mesMaxFact, YEAR(maxFechaFac) anoMaxFact ";
$vSql02=$vSql02." FROM ";
$vSql02=$vSql02." ( ";
$vSql02=$vSql02." Select MIN(fecha) minFechaFac, MAX(fecha) maxFechaFac ";
$vSql02=$vSql02." from HojaDeTiempo.dbo.horas ";
if (trim($_SESSION["sesUnidadUsuario"]) != trim($laUnidad)  ) {
	$vSql02=$vSql02." where unidad = " . $laUnidad;
}
else {
	$vSql02=$vSql02." where unidad = " . $_SESSION["sesUnidadUsuario"];
}
$vSql02=$vSql02." and MONTH(fecha)=".$mesAut;
$vSql02=$vSql02." and YEAR(fecha)=".$anoAut;
$vSql02=$vSql02." ) A ";
$vCursor02 = mssql_query($vSql02);
if ($vReg02=mssql_fetch_array($vCursor02)) {
	$pminFechaFac=$vReg02[minFechaFac];
	$pmaxFechaFac=$vReg02[maxFechaFac];
	$pdiaMinFact=$vReg02[diaMinFact];
	$pmesMinFact=$vReg02[mesMinFact];
	$panoMinFact=$vReg02[anoMinFact];
	$pdiaMaxFact=$vReg02[diaMaxFact];
	$pmesMaxFact=$vReg02[mesMaxFact];
	$panoMaxFact=$vReg02[anoMaxFact];
}


//Verifica si el mes actual es igual al de ingreso para comparar primer día de facturación contra ingreso
$validaIngreso="NO";
if (($pmesIngreso == $mesAut) AND ($panoIngreso == $anoAut)) {
	//Valida que la facturación no esté antes del dia de ingreso
	if ($pdiaMinFact < $pdiaIngreso) {
		$validaIngreso="SI";
		$MensajeIngreso="No puede facturar antes de su fecha de ingreso ".date("d-M-Y ", strtotime($pfechaIngreso)).". Por favor corrija la información. ";
	}
	else {
		$MensajeIngreso="";
	}
}

//Verifica si el mes actual es igual al de retiro para comparar el ultimo día contra el retiro
$validaRetiro="NO";
if (($pmesRetiro == $mesAut) AND ($panoRetiro == $anoAut)) {
	//Valida que la facturación no esté despues del dia de retiro
	if ($pdiaMaxFact > $pdiaRetiro) {
		$validaRetiro="SI";
		$MensajeRetiro="No puede facturar despues de su fecha de retiro ".date("d-M-Y ", strtotime($pfechaRetiro))." . Por favor corrija la información. ";
	}
	else {
		$MensajeRetiro="";
	}
}

//Valida que no se hayan reportado horas antes de la fecha de ingreso o después de la fecha de retiro
$mensajeValida =  ""; 
if (($validaIngreso=="SI") OR ($validaRetiro=="SI")) {
	$mensajeValida =  $MensajeIngreso . " " . $MensajeRetiro;
	echo ("<script>alert('".$mensajeValida."');</script>");
	echo ("<script>window.close()</script>");
}



/*
echo "pmesIngreso" . $pmesIngreso . "<br>";
echo "pmesRetiro" . $pmesRetiro . "<br>";
echo "mesAut" . $mesAut . "<br>";

echo "panoIngreso" . $panoIngreso . "<br>";
echo "panoRetiro" . $panoRetiro . "<br>";
echo "anoAut" . $anoAut . "<br>";

echo "pdiaMinFact" . $pdiaMinFact . "<br>";
echo "pdiaIngreso" . $pdiaIngreso . "<br>";
echo "pdiaMaxFact" . $pdiaMaxFact . "<br>";
echo "pdiaRetiro" . $pdiaRetiro . "<br>";

echo "validaIngreso" . $validaIngreso . "<br>";
echo "validaRetiro" . $validaRetiro . "<br>";
*/
//OJO PENDIENTE VALIDAR Y CERRAR LA VENTANA
// Cierra 04Mar2011


//Si se presionó el botón Grabar
if ($elAno != "") {

//-----------------
	//Valida verticalmente la sumatoria. Debe dar 1.

	//Elimina la información de la unidad, mes y año indicados de la tabla ValidaFactUsuHT
	//donde se está almacenando la facturación de las personas.
	$vSqlv01="DELETE FROM HojaDeTiempo.dbo.ValidaFactUsuHT ";
	if (trim($_SESSION["sesUnidadUsuario"]) != trim($laUnidad)  ) {
		$vSqlv01=$vSqlv01." WHERE unidad = " . $laUnidad;
	}
	else {
		$vSqlv01=$vSqlv01." WHERE unidad = " . $_SESSION["sesUnidadUsuario"];
	}
	$vSqlv01=$vSqlv01." and MONTH(fecha)=" . $elMes ;
	$vSqlv01=$vSqlv01." and YEAR(fecha) =" . $elAno;
	$vCursorv01 = mssql_query($vSqlv01);
	echo "vSqlv01 <br>" . $vSqlv01 . "<br>";
	echo "vSqlv01 <br>" . $vCursorv01 . "<br>";

	//Busca la facturación de la persona y la inserta en ValidaFactUsuHT
	$vSqlv02="
		INSERT INTO HojaDeTiempo.dbo.ValidaFactUsuHT 
			(unidad, IDhorario, id_proyecto, id_actividad, clase_tiempo, localizacion, cargoFact, fecha, horas_registradas, 
			horasProyecto, horasGenerico, diaSemana, 
			horasLunes, horasMartes, horasMiercoles, horasJueves, horasViernes, horasSabado, horasDomingo, 
			hmFact, codigoProy, cargoAsig, fechaCrea, unidadCrea)
		
			SELECT DISTINCT A.unidad, B.IDhorario, A.id_proyecto, A.id_actividad, A.clase_tiempo, A.localizacion, A.cargo cargoFact,
			A.fecha, A.horas_registradas,
			(select CuantasHoras from HojaDeTiempo.dbo.FechasEspecialesProy
				where id_proyecto =A.id_proyecto
				and IDhorario = B.IDhorario
				and Fecha = A.fecha ) horasProyecto
			,
			(select CuantasHoras from HojaDeTiempo.dbo.FechasEspeciales
				where IDhorario = B.IDhorario
				and Fecha = A.fecha ) horasGenerico
			,
			DATEPART(dw, A.fecha) diaSemana
			,
			(select Lunes from HojaDeTiempo.dbo.horarios
			where IDhorario = B.IDhorario
			) horasLunes
			,
			(select Martes from HojaDeTiempo.dbo.horarios
			where IDhorario = B.IDhorario
			) horasMartes
			,
			(select Miercoles from HojaDeTiempo.dbo.horarios
			where IDhorario = B.IDhorario
			) horasMiercoles
			,
			(select Jueves from HojaDeTiempo.dbo.horarios
			where IDhorario = B.IDhorario
			) horasJueves
			,
			(select Viernes from HojaDeTiempo.dbo.horarios
			where IDhorario = B.IDhorario
			) horasViernes
			,
			(select Sabado from HojaDeTiempo.dbo.horarios
			where IDhorario = B.IDhorario
			) horasSabado
			,
			(select Domingo from HojaDeTiempo.dbo.horarios
			where IDhorario = B.IDhorario
			) horasDomingo, 
			'0', C.codigo, B.cargo, GETDATE(), 15712   
			FROM HojaDeTiempo.dbo.Horas A, HojaDeTiempo.dbo.Asignaciones B, HojaDeTiempo.dbo.Proyectos C
			WHERE A.id_proyecto = B.id_proyecto
			and A.id_actividad = B.id_actividad
			and A.unidad = B.unidad
			and A.clase_tiempo = B.clase_tiempo
			and A.localizacion = B.localizacion
			and A.cargo = C.codigo + B.cargo
			and A.id_proyecto = C.id_proyecto
			and (A.clase_tiempo = 1 or A.clase_tiempo = 2 or A.clase_tiempo = 3 or A.clase_tiempo = 11)		";
	
	//Se quitaron estas dos líneas de la connsulta para que se pueda encontrar lo facturado en proyectos que no se programan. 
	//Las línea estaban ubicadas despues de and A.localizacion = B.localizacion
	//and MONTH(A.fecha)= MONTH(B.fecha_inicial)
	//and YEAR(A.fecha)= YEAR(B.fecha_inicial)

		if (trim($_SESSION["sesUnidadUsuario"]) != trim($laUnidad)  ) {
			$vSqlv02=$vSqlv02." and A.unidad = " . $laUnidad;
		}
		else {
			$vSqlv02=$vSqlv02." and A.unidad = " . $_SESSION["sesUnidadUsuario"];
		}
	$vSqlv02=$vSqlv02." and MONTH(A.fecha)=" . $elMes ;
	$vSqlv02=$vSqlv02." and YEAR(A.fecha)= " . $elAno;
	$vCursorv02 = mssql_query($vSqlv02);
	echo "vSqlv02 <br>" . $vSqlv02 . "<br>";
	echo "vCursorv02 <br>" . $vCursorv02 . "<br>";

	//Recorre y calcular hombres mes facturado
	$vSqlv03="SELECT * FROM HojaDeTiempo.dbo.ValidaFactUsuHT ";
	if (trim($_SESSION["sesUnidadUsuario"]) != trim($laUnidad)  ) {
		$vSqlv03=$vSqlv03." WHERE unidad = ". $laUnidad;
	}
	else {
		$vSqlv03=$vSqlv03." WHERE unidad = ". $_SESSION["sesUnidadUsuario"];
	}
	$vSqlv03=$vSqlv03." and MONTH(fecha) =" . $elMes ;
	$vSqlv03=$vSqlv03." and YEAR(fecha) =" . $elAno;
	$vCursorv03 = mssql_query($vSqlv03);
//	echo "vSqlv03 <br>" . $vSqlv03 . "<br>";
//	echo "vCursorv03 <br>" . $vCursorv03 . "<br>";
	
	while ($vRegv03=mssql_fetch_array($vCursorv03)) {
		$calculoHM=0;
		//Si horasProyecto es diferente de NULL es por que si hay una cantidad de horas especial para ese horario en esa fecha. (Tomado de FechasEspecialesProy)
		//Los HombresMes = HorasFacturadas/HorasHorario 
//		echo "HorasRegistradas=". $vRegv03[horas_registradas] . "<br>"; 
//		echo "HorasRegistradas=". $vRegv03[horasGenerico] . "<br>"; 
//		echo "HorasRegistradas=". $vRegv03[horas_registradas] . "<br>"; 
		
		if (trim($vRegv03[horasProyecto]) != "" ) {
			$calculoHM=$vRegv03[horas_registradas] / $vRegv03[horasProyecto];
		}
		else {
			//Si horasProyecto = NULL se verifica el horario genérico (Cantidad de horas especiales). (Tomado de FechasEspeciales)
			if (trim($vRegv03[horasGenerico]) != "" ) {
				$calculoHM=$vRegv03[horas_registradas] / $vRegv03[horasGenerico];
			}
			else {
			//Si no hay horas especiales para el proyecto o el horario se toma la cantidad de horas definidas en el horario para el día de la semana que corresponda.
			//diaSemana equivaale a 2=Lunes, 3=Martes, 4=Miércoles, 5=Jueves, 6=Viernes, 7=Sábado, 1=Domingo
				switch ($vRegv03[diaSemana]) {
					case 1:
						$calculoHM=$vRegv03[horas_registradas] / $vRegv03[horasDomingo];
						break;
					case 2:
						$calculoHM=$vRegv03[horas_registradas] / $vRegv03[horasLunes];
						break;
					case 3:
						$calculoHM=$vRegv03[horas_registradas] / $vRegv03[horasMartes];
						break;
					case 4:
						$calculoHM=$vRegv03[horas_registradas] / $vRegv03[horasMiercoles];
						break;
					case 5:
						$calculoHM=$vRegv03[horas_registradas] / $vRegv03[horasJueves];
						break;
					case 6:
						$calculoHM=$vRegv03[horas_registradas] / $vRegv03[horasViernes];
						break;
					case 7:
						$calculoHM=$vRegv03[horas_registradas] / $vRegv03[horasSabado];
						break;
				}
			}
		}
		//echo "calculoHM =" . $calculoHM . "<br>";

		//Realiza la actualización del campo hmFact con el calculo de hombres mes facturado
		$vSqlv04="update HojaDeTiempo.dbo.ValidaFactUsuHT  ";
		$vSqlv04=$vSqlv04." set hmFact =" . number_format($calculoHM, '2', '.',  ',');
		if (trim($_SESSION["sesUnidadUsuario"]) != trim($laUnidad)  ) {
			$vSqlv04=$vSqlv04." WHERE unidad = ". $laUnidad;
		}
		else {
			$vSqlv04=$vSqlv04." WHERE unidad = ". $_SESSION["sesUnidadUsuario"];
		}
		$vSqlv04=$vSqlv04." and IDhorario = " . $vRegv03[IDhorario];
		$vSqlv04=$vSqlv04." and id_proyecto =" . $vRegv03[id_proyecto];
		$vSqlv04=$vSqlv04." and id_actividad =". $vRegv03[id_actividad];
		$vSqlv04=$vSqlv04." and clase_tiempo =". $vRegv03[clase_tiempo];
		$vSqlv04=$vSqlv04." and localizacion =". $vRegv03[localizacion];
		$vSqlv04=$vSqlv04." and cargoFact = '". $vRegv03[cargoFact] ."'";
		$vSqlv04=$vSqlv04." and fecha = '".$vRegv03[fecha]."' ";
		$vCursorv04 = mssql_query($vSqlv04);
		//echo "vSqlv04 <br>" . $vSqlv04 . "<br>";
	}
	
	//Encuentra el tipo de contrato de la persona activa
	$tSql="select * from HojaDeTiempo.dbo.usuarios ";
	if (trim($_SESSION["sesUnidadUsuario"]) != trim($laUnidad)  ) {
		$tSql=$tSql." where unidad = " . $laUnidad;
	}
	else {
		$tSql=$tSql." where unidad = " . $_SESSION["sesUnidadUsuario"];
	}
	$tCursor = mssql_query($tSql);
	if ($tReg=mssql_fetch_array($tCursor)) {
		$elTipoContrato=$tReg[TipoContrato];
	}
	echo "Tipo de contrato=" . $elTipoContrato . "<br>";
	
	//23May2011
	//Valida que la sumatoria por día sea igual a 1
	
	$estaMal=0;
	if (trim($elTipoContrato) == "TC" ) {
		$fechaAsumar="";
		$esDiaValido="";
		$mensajeDia="";
		$esFestivo="";
		$queGraba = "";
		$numeroDia="";
		$yaIngreso = "";
		$seRetiro = "";
		$vecFlag = array(32);
		$vecValor = array(32);
		$vecFlag[0] = 0;
		$vecValor[0] = 0;
		for ($d=1; $d<=31; $d++) {
			//Arma la fecha a validar
			$fechaAsumar=$elAno."-".$elMes."-".$d;
	
			//Consulta para traer la sumatoria de la fecha 
			$sVsql="SELECT ROUND(SUM(hmFact), 1) totalHM ";
			$sVsql=$sVsql." FROM HojaDeTiempo.dbo.ValidaFactUsuHT ";
			if (trim($_SESSION["sesUnidadUsuario"]) != trim($laUnidad)  ) {
				$sVsql=$sVsql." WHERE unidad = " . $laUnidad;
			}
			else {
				$sVsql=$sVsql." WHERE unidad = " . $_SESSION["sesUnidadUsuario"];
			}		
			$sVsql=$sVsql." AND Fecha = '".$fechaAsumar."' ";
			$sVCursor = mssql_query($sVsql);
			$totalDia=0;
			if ($sVreg=mssql_fetch_array($sVCursor)) {
				$totalDia=$sVreg[totalHM];
			}
	
			//Valida que la fecha sea válida, es decir no tiene en cuenta dias como 30 de febrero 
			if (checkdate($elMes, $d, $elAno)) {
				$esDiaValido="SI";
			}
			echo $d . " - " . $esDiaValido . "<br>";
	
			//Si el día es válido inicia el resto de verificaciones
			if ($esDiaValido=="SI") {
				//Valida si el día es festivo
				$vFsql="SELECT * FROM HojaDeTiempo.dbo.Festivos ";
				$vFsql=$vFsql." WHERE fecha = '".$fechaAsumar."'";
				$vFCursor = mssql_query($vFsql);
				if ($vFreg=mssql_fetch_array($vFCursor)) {
					$esFestivo="SI";
					echo "El día " . $d . " es Festivo.<br>";
					$queGraba = 'F';
				}
	
				//Si el mes y año de la autorización de la hoja de tiempo coinciden con el mes y año de ingreso, 
				//Permite identificar que se trata de día no válido, no había ingresado.
				if (($pmesIngreso == $elMes) AND ($panoIngreso == $elAno)) {
					//Valida que la facturación no esté antes del dia de ingreso
					if ($d < $pdiaIngreso) {
						$yaIngreso = "NO";
						echo "El día " . $d . " NO había ingresado.<br>";
						$queGraba = 'I';
					}
				}
				
				//Si el mes y año de la autorización de la hoja de tiempo coinciden con el mes y año de retiro,
				//permite identificar que se trata de día no valido, ya se retiró
				if (($pmesRetiro == $elMes) AND ($panoRetiro == $elAno)) {
					//Valida que la facturación no esté despues del dia de retiro
					if ($d > $pdiaRetiro) {
						$seRetiro = "SI";
						echo "El día " . $d . " YA se había retirado. <br>";
						$queGraba = 'R';
					}
				}
				
				//Trae el número de día para saber si es día sábado = 7 o domingo = 8 y no tenerlo en cuenta en la validación total
				$vDsql="SELECT DATEPART(dw, '".$fechaAsumar."') dia  ";
				$vDCursor = mssql_query($vDsql);
				if ($vDreg=mssql_fetch_array($vDCursor)) {
					$numeroDia=$vDreg[dia];
					if (($numeroDia == 7) OR ($numeroDia == 1)) {
						$queGraba = 'Z';
					}
				}
				if ($totalDia!=1) {
					//Validar si se trata de festivo, sábado o domingo
					//if (($esFestivo=="SI") OR ($numeroDia == 7) OR ($numeroDia == 1) ) {
					//Validar si se trata de festivo, sábado o domingo o no había ingresado aun
					if (($esFestivo=="SI") OR ($numeroDia == 7) OR ($numeroDia == 1) OR ($yaIngreso == "NO") OR ($seRetiro == "SI") ) {
						echo "No tener en cuenta el día " . $d . " sábado, domingo o festivo o No había ingresado o Ya se retiró <br>";
						//$queGraba = $totalDia;
					}
					else {
						$mensajeDia=$mensajeDia." El dia " . $d . " no cumple la validación de tiempo total. " . $totalDia . "<br>" ;
						$estaMal=$estaMal+1;
						$queGraba = 'M';
					}
				}
				else {
					echo "El día " . $d . " está bien.<br>";
					$queGraba = $totalDia;
				}
			}
			else {
				echo "No tener en cuenta el día " . $d . "<br>";
				$queGraba = 'X';
			}
	
			//Genera el vector con lo que debe grabar 
			$vecFlag[$d] = $queGraba;
			
			$fechaAsumar="";
			$esDiaValido="";
			$esFestivo="";
			$queGraba = "";
			$numeroDia="";
			$yaIngreso = "";
			$seRetiro = "";		
		}
		
		if (trim($mensajeDia) != "") {
			echo $mensajeDia ;
		}
		for ($f=1; $f<=31; $f++) {
			echo $f . "=" . $vecFlag[$f] . " - " ;
		}
	}
	else {
		echo "Pasa porque no es TC";
	}
	
	if ($estaMal > 0) {
		echo ("<script>alert('".$mensajeDia.".');</script>");
	}
	else {
//-----------------
		//Verifica si el registro ya existe en la tabla AutorizacionsHT para 
		//Determinar si se inserta o se modifica.
		@mssql_select_db("HojaDeTiempo");
		$cuantosHay = 0;
		$sql1="Select count(*) hayRegistros ";
		$sql1=$sql1." from AutorizacionesHT ";
		$sql1=$sql1." where vigencia = " . $elAno;
		$sql1=$sql1." and mes = " . $elMes ;
		//31Oct2007
		//Si se ha cambiado el usuario, la variable de session $_SESSION["sesUnidadUsuario"] y 
		//$laUnidad son diferentes, por lo tanto para la hoja de tiempo
		//se continua trabajando con la Unidad.
		if (trim($_SESSION["sesUnidadUsuario"]) != trim($laUnidad)  ) {
			$sql1=$sql1." and unidad = " . $laUnidad;
		}
		else {
			$sql1=$sql1." and unidad = " . $_SESSION["sesUnidadUsuario"];
		}
	
		$cursor1 = mssql_query($sql1);
		if ($reg1=mssql_fetch_array($cursor1)) {
			$cuantosHay = $reg1[hayRegistros];
		}
		
		if ($cuantosHay == 0) {
			$query = "INSERT INTO AutorizacionesHT(vigencia, mes, unidad, unidadJefe, fechaEnvio)  " ;
			$query = $query . " VALUES (" . $elAno . ", ";
			$query = $query . $elMes . " , ";
	
			//31Oct2007
			//Si se ha cambiado el usuario, la variable de session $_SESSION["sesUnidadUsuario"] y 
			//$laUnidad son diferentes, por lo tanto para la hoja de tiempo
			//se continua trabajando con la Unidad.
			if (trim($_SESSION["sesUnidadUsuario"]) != trim($laUnidad)  ) {
				$query = $query . $laUnidad . " , ";	
			}
			else {
				$query = $query . $_SESSION["sesUnidadUsuario"] . " , ";	
			}
			$query = $query . $pJefe . ", ";		
			$query = $query . " '" . gmdate ("n/d/y") . "' ";		
			$query = $query . " ) ";
		}
		else {
			$query = "UPDATE  AutorizacionesHT SET "; 
			$query = $query . " unidadJefe = " . $pJefe . ",  ";
			$query = $query . " fechaEnvio = '" . gmdate ("n/d/y") . "'  ";
			$query = $query . " WHERE vigencia = " . $elAno ;
			$query = $query . " AND mes = " . $elMes ;
			//31Oct2007
			//Si se ha cambiado el usuario, la variable de session $_SESSION["sesUnidadUsuario"] y 
			//$laUnidad son diferentes, por lo tanto para la hoja de tiempo
			//se continua trabajando con la Unidad.
			if (trim($_SESSION["sesUnidadUsuario"]) != trim($laUnidad)  ) {
				$query = $query . " AND unidad = " .  $laUnidad ;
			}
			else {
				$query = $query . " AND unidad = " . $_SESSION["sesUnidadUsuario"] ;
			}
		}
	//	$cursor = mssql_query($query) ;	
	exit;
	
		//Si los cursores no presentaron problema
		if  (trim($cursor) != "") {
			echo ("<script>alert('La Grabación se realizó con éxito.');</script>");
		} 
		else {
			echo ("<script>alert('Error durante la grabación');</script>");
		};
		echo ("<script>window.close()</script>");
	}
}


?>
<html>
<head>
<title>Autorizaci&oacute;n Hoja de tiempo</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="#EAEAEA">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Hoja de tiempo - Envio a Autorizaci&oacute;n del Jefe </td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	<table width="100%"  border="0" cellspacing="1" cellpadding="0">
<form action="" method="post" name="Form1" id="Form1">
  <tr>
    <td width="25%" class="TituloTabla">A&ntilde;o</td>
    <td class="TxtTabla">
	<? echo $anoAut ; ?>
	<input name="elAno" type="hidden" id="elAno" value="<? echo $anoAut ; ?>">
	</td>
  </tr>
  <tr>
    <td width="25%" class="TituloTabla">Mes</td>
    <td class="TxtTabla">
	<? echo $mesAut ; ?>
	<input name="elMes" type="hidden" id="elMes" value="<? echo $mesAut ; ?>">
	</td>
  </tr>
  <tr>
    <td class="TituloTabla">Unidad</td>
    <td class="TxtTabla">
	<?
	if (trim($_SESSION["sesUnidadUsuario"]) != trim($laUnidad)  ) {
		echo $laUnidad;
	}
	else {
		echo $_SESSION["sesUnidadUsuario"];
	}
	?>
	</td>
  </tr>
  <tr>
    <td width="25%" class="TituloTabla">Usuario</td>
    <td class="TxtTabla">
	<? echo strtoupper($nombreempleado." ".$apellidoempleado); ?></td>
  </tr>
  <tr>
    <td width="25%" class="TituloTabla">Jefe que autoriza </td>
    <td class="TxtTabla"><select name="pJefe" class="CajaTexto" id="pJefe"  >
            <?
		@mssql_select_db("HojaDeTiempo");
		//Muestra todos los usuarios. 
//		$sql2="Select * from Usuarios where id_categoria <= " . $laCategoria ;
		$sql2="Select * from Usuarios where id_categoria <= 40 "  ;
		$sql2=$sql2." and retirado is null ";
		$sql2=$sql2." order by apellidos ";
		$cursor2 = mssql_query($sql2);
		while ($reg2=mssql_fetch_array($cursor2)) {
			if ($elUsuarioJefe == $reg2[unidad]) {
				$selJefe = "selected";
			}
			else {
				$selJefe = "";
			}
		?>
            <option value="<? echo $reg2[unidad]; ?>" <? echo $selJefe; ?> ><? echo ucwords(strtolower($reg2[apellidos])) . " " . ucwords(strtolower($reg2[nombre]));  ?></option>
            <? } ?>
          </select></td>
  </tr>
  <tr>
    <td colspan="2" align="right" class="TxtTabla"><input name="Submit" type="submit" class="Boton" value="Grabar"></td>
    </tr>
  </form>
</table>
	</td>
  </tr>
</table>

</body>
</html>
