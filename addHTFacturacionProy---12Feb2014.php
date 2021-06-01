<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
<?
session_start();

//Establecer la conexión a la base de datos
include "funciones.php";
include "validaUsrBd.php";

//Valida que no se haya perdido la sesión de la unidad del usuario.
if (trim($laUnidad) == "") {
	echo ("<script>alert('Usted ha perdido su sesión de trabajo. Por favor regístrese de nuevo.');</script>");
	echo ("<script>window.close();</script>");
}

//Verifica que la hoja de tiempo esté sin firmar por el jefe inmediato. Estar firmada significa que está cerrada
//--Verifica si la Hoja de tiempo ya tiene VoBo del jefe para impedir el registro de la facturación
$sql17="SELECT * FROM VoBoFirmasHT ";
$sql17=$sql17." WHERE vigencia = " . $cualVigencia;
$sql17=$sql17." AND mes = " . $cualMes; 
$sql17=$sql17." AND unidad = " . $laUnidad;
$sql17=$sql17." AND validaJefe = 1 ";
$cursor17 =	 mssql_query($sql17);
$numReg17 = mssql_num_rows($cursor17);
if($numReg17 > 0) {
	echo "<script>alert('Su hoja de tiempo se encuentra cerrada, por lo tanto no podrá modificarla. Para levantar los VoBo por favor recurra a la persona que firmó y/o a contratos.')</script>";
	echo ("<script>window.close();</script>");
}

//Inicializa la variable cantReg en 1 si es la primera vez que se carga la ventana
if ((trim($cantReg) == "") AND (trim($recarga) == "")) {
	$cantReg = 1;
}

//--Trae el listado de proyectos seleccionado para hacer la facturación
$sql01="SELECT *  ";
$sql01=$sql01." FROM Proyectos ";
$sql01=$sql01." WHERE id_proyecto = " . $cualProyecto;
$cursor01 =	 mssql_query($sql01);

//Define el array de meses a usar en la página
$vMeses= array("","Ene","Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"); 

//Define el array de días a usar en la página
$vSemana= array("","D","L", "M", "M", "J", "V", "S"); 

//--Traer la cantidad de días de un mes determinado
$cantElMes="";
$totalDiasMes = 0;
if (strlen($cualMes) == 1) {
	$cantElMes = "0" . $cualMes;
}
else {
	$cantElMes = "" . $cualMes;
}
$sql02="select  day(dateadd(d,-1,dateadd(m,1,convert(datetime, '".$cualVigencia."' + '".$cantElMes."' + '01')))) diasDelMes ";
$cursor02 =	 mssql_query($sql02);
if ($reg02 = mssql_fetch_array($cursor02)) {
	$totalDiasMes =  $reg02['diasDelMes'];
}

//--Trae el tipo de contrato y la categoría de la persona activa
$sql13="select U.* , substring(C.nombre,1,2) nomCategoria ";
$sql13=$sql13." from usuarios U, categorias C ";
$sql13=$sql13." where U.id_categoria = C.id_categoria ";
$sql13=$sql13." and U.unidad = " . $laUnidad ;
$cursor13 =	 mssql_query($sql13);
if ($reg13 = mssql_fetch_array($cursor13)) {
	$miTipoContrato = $reg13['TipoContrato'];
	$miCategoria = $reg13['nomCategoria'];
	$miDepartamentoUsu = $reg13['id_departamento'];
}
//echo "Cate=" . $miCategoria . "<br>";
/*
echo "miTipoContrato=" . $miTipoContrato . "<br>";
echo "usuTipoContrato =" . $usuTipoContrato . "<br>";
*/

//--Trae el salario de la persona activs
$sql14="select * from UsuariosSalario " ;
$sql14=$sql14." where unidad = " . $laUnidad ;
//$sql14=$sql14." where unidad = 12121212" ; //Esta línea es sólo para probar la validación
$sql14=$sql14." and fecha = (select MAX(fecha) from UsuariosSalario where unidad = ".$laUnidad." ) ";
$cursor14 =	 mssql_query($sql14);
if ($reg14 = mssql_fetch_array($cursor14)) {
	$miSalarioUsu = $reg14['salario'];
}
if (trim($miSalarioUsu) == "") {
	echo ("<script>alert('El usuario no tiene definido un salario, por favor contacte al departamento de personal para que lo asignen, una vez establecido el salario proceda a realizar la facturación.');</script>");
	echo ("<script>window.close();</script>");
}

//--Trae la cantidad de horas Segun contratos para el Horario Base, Horario de campo y Cat. 42.
$sql15="SELECT * ";
$sql15=$sql15." FROM horasydiaslaborales ";
$sql15=$sql15." WHERE vigencia = " . $cualVigencia;
$sql15=$sql15." AND mes = " . $cualMes; 
$cursor15 =	 mssql_query($sql15);
if ($reg15 = mssql_fetch_array($cursor15)) {
	$miHorasOficina = $reg15['hOficina'];
	$miHorasCampo = $reg15['hCampo'];
	$miHorasCat42 = $reg15['hCat42'];
}


//--Traer las Divisiones y Actividades de la EDT para un proyecto y unidad
/*
$sql04="SELECT * ";
$sql04=$sql04." FROM Actividades ";
$sql04=$sql04." WHERE id_Proyecto = " . $cualProyecto;
$sql04=$sql04." AND nivel IN (3, 4) ";
//Filtra la información si se trata de un proyecto con planeación
if ($hayPlaneacion==1) {
	$sql04=$sql04." AND id_actividad IN ";
	$sql04=$sql04." 	( ";
	$sql04=$sql04." 	SELECT DISTINCT id_actividad  ";
	$sql04=$sql04." 	FROM PlaneacionProyectos ";
	$sql04=$sql04." 	WHERE id_Proyecto = " . $cualProyecto;
	$sql04=$sql04." 	AND unidad = " . $laUnidad;
	$sql04=$sql04." 	) ";
}
$cursor04 =	 mssql_query($sql04);


//--Traer las Divisiones y Actividades de la EDT para un proyecto y unidad
$sql04="SELECT (valMacro *  factor) miOrden, *  ";
$sql04=$sql04." FROM ";
$sql04=$sql04." 	( ";
$sql04=$sql04." 	Select ";
$sql04=$sql04." 	CAST(REPLACE((REPLACE((SUBSTRING(A.macroactividad, 3, LEN(A.macroactividad))),'.','')),'A','') AS int) valMacro, ";
$sql04=$sql04." 	factor = ";
$sql04=$sql04." 		case nivel ";
$sql04=$sql04." 			when 1 then 100000 ";
$sql04=$sql04." 			when 2 then 10000 ";
$sql04=$sql04." 			when 3 then 1000 ";
$sql04=$sql04." 			when 4 then 100 ";
$sql04=$sql04." 		end, A.* ";
$sql04=$sql04." 	from Actividades A ";
$sql04=$sql04." 	where A.id_Proyecto = " . $cualProyecto;
$sql04=$sql04." 	AND A.nivel IN (3, 4) ";
//Filtra la información si se trata de un proyecto con planeación
if ($hayPlaneacion==1) {
	$sql04=$sql04." 	AND A.id_actividad IN ";
	$sql04=$sql04." 		( ";
	$sql04=$sql04." 		SELECT DISTINCT id_actividad  ";
	$sql04=$sql04." 		FROM PlaneacionProyectos ";
	$sql04=$sql04." 		WHERE id_Proyecto = " . $cualProyecto;
	$sql04=$sql04." 		AND unidad = " . $laUnidad;
	$sql04=$sql04." 		) ";
}
$sql04=$sql04." ) Z ";
$sql04=$sql04." order by (valMacro *  factor) ";
*/

//--Traer las Divisiones y Actividades de la EDT para un proyecto y unidad
//--tercera consulta ordena por macroactividad y muestra las que estan con planeación
//--Además trae sólo las que estan vigentes (FechaInicial >= MES-Vigencia <= FechaFinal )
$sql04="SELECT (valMacro *  factor) miOrden, B.id_actividad estaPlaneada, Z.*  ";
$sql04=$sql04." FROM ";
$sql04=$sql04." 	( ";
$sql04=$sql04." 	Select ";
$sql04=$sql04." 	CAST(REPLACE((REPLACE((SUBSTRING(A.macroactividad, 3, LEN(A.macroactividad))),'.','')),'A','') AS int) valMacro, ";
$sql04=$sql04." 	factor = ";
$sql04=$sql04." 		case nivel ";
$sql04=$sql04." 			when 1 then 100000 ";
$sql04=$sql04." 			when 2 then 10000 ";
$sql04=$sql04." 			when 3 then 1000 ";
$sql04=$sql04." 			when 4 then 100 ";
$sql04=$sql04." 		end, A.* ";
$sql04=$sql04." 	from Actividades A ";
$sql04=$sql04." 	where A.id_Proyecto =  " . $cualProyecto;
//$sql04=$sql04." 	AND A.nivel IN (3, 4) "; 
$sql04=$sql04." 	AND A.nivel IN (4) "; // Por instrucción de contratos la facturación debe relacionar SIEMPRE una macroactividad, por tal razón sólo se muestra el nivel 4

//Filtra la información si se trata de un proyecto con planeación
/*
//NO se usó el filtro porque GRM indicó mostrar todas las preguntas
if ($hayPlaneacion==1) {
	$sql04=$sql04." 	AND A.id_actividad IN ";
	$sql04=$sql04." 		( ";
	$sql04=$sql04." 		SELECT DISTINCT id_actividad  ";
	$sql04=$sql04." 		FROM PlaneacionProyectos ";
	$sql04=$sql04." 		WHERE id_Proyecto = " . $cualProyecto;
	$sql04=$sql04." 		AND unidad = " . $laUnidad;
	$sql04=$sql04." 		) ";
}
*/
$sql04=$sql04." ) Z, PlaneacionProyectos B ";
$sql04=$sql04." WHERE Z.id_proyecto *= B.id_proyecto ";
$sql04=$sql04." AND Z.id_actividad *= B.id_actividad ";
$sql04=$sql04." AND B.unidad = " . $laUnidad;
$sql04=$sql04." AND B.mes = " . $cualMes; 
$sql04=$sql04." AND B.vigencia = " . $cualVigencia;
$sql04=$sql04." AND '".$cualVigencia."-".$cualMes."-01' BETWEEN Z.fecha_inicio AND Z.fecha_fin ";
$sql04=$sql04." order by (valMacro *  factor)";

//--traer los Horarios asociados al proyecto
$sql05="SELECT A.*, B.NomHorario, B.Lunes, B.Martes, B.Miercoles, B.Jueves, B.Viernes, B.Sabado, B.Domingo ";
$sql05=$sql05." FROM HorariosProy A, Horarios B ";
$sql05=$sql05." WHERE A.IDhorario = B.IDhorario ";
$sql05=$sql05." AND A.id_proyecto =  " . $cualProyecto;

//--Traer las clases de tiempo
$sql06="SELECT *  ";
$sql06=$sql06." FROM Clase_Tiempo ";

//--Traer las localizaciones
$sql07="SELECT *  ";
$sql07=$sql07." FROM TipoLocalizacion ";
//Si el usuario activo es categoría 52 a 62 la localización debe ser 3 = personal de planilla
if ($miCategoria >= 52) {
	$sql07=$sql07." WHERE localizacion = 3 ";
}

//Trae el cargo_defecto y los cargos_adicionales del proyeecto seleccionado	
$sql11="select id_proyecto, cargo_defecto cargos  " ;
$sql11=$sql11." from proyectos where id_proyecto = " . $cualProyecto ;
$sql11=$sql11." union " ;
$sql11=$sql11." select id_proyecto, cargos_adicionales cargos  " ;
$sql11=$sql11." from cargos where id_proyecto =" . $cualProyecto ;

//--Trae la cantidad de horas facturadas para oficina, campo y/o planilla
$sql16="SELECT * " ;
$sql16=$sql16." FROM " ;
$sql16=$sql16." ( " ;
$sql16=$sql16." SELECT COALESCE( SUM(horasMesF), 0) horasOfi " ;
$sql16=$sql16." FROM FacturacionProyectos " ;
$sql16=$sql16." WHERE unidad = " . $laUnidad;
$sql16=$sql16." AND mes = " . $cualMes; 
$sql16=$sql16." AND vigencia = " . $cualVigencia;
$sql16=$sql16." AND localizacion = 1 " ;
$sql16=$sql16." AND clase_tiempo IN (1, 2, 3) " ;
$sql16=$sql16." ) A," ;
$sql16=$sql16." ( " ;
$sql16=$sql16." SELECT COALESCE( SUM(horasMesF), 0) horasCampo " ;
$sql16=$sql16." FROM FacturacionProyectos " ;
$sql16=$sql16." WHERE unidad = " . $laUnidad;
$sql16=$sql16." AND mes = " . $cualMes; 
$sql16=$sql16." AND vigencia = " . $cualVigencia;
$sql16=$sql16." AND localizacion = 2 " ;
$sql16=$sql16." AND clase_tiempo IN (1, 2, 3) " ;
$sql16=$sql16." ) B, " ;
$sql16=$sql16." ( " ;
$sql16=$sql16." SELECT COALESCE( SUM(horasMesF), 0) horasPlanilla " ;
$sql16=$sql16." FROM FacturacionProyectos " ;
$sql16=$sql16." WHERE unidad = " . $laUnidad;
$sql16=$sql16." AND mes = " . $cualMes; 
$sql16=$sql16." AND vigencia = " . $cualVigencia;
$sql16=$sql16." AND localizacion = 3 " ;
$sql16=$sql16." AND clase_tiempo IN (1, 2, 3) " ;
$sql16=$sql16." ) C" ;
$cursor16 =	 mssql_query($sql16);
if ($reg16 = mssql_fetch_array($cursor16)) {
	$miOficinaFacturadas = $reg16['horasOfi'];
	$miCampoFacturadas = $reg16['horasCampo'];
	$miPlanillaFacturadas = $reg16['horasPlanilla'];
}

//Verifica que la fecha que está grabando no sea superior a la fecha de retiro
//Parte 1 - Encontrar la fecha de ingreso y retiro del usuario
$sql21="Select unidad, nombre, apellidos, fechaIngreso, fechaRetiro ";
$sql21=$sql21." from HojaDeTiempo.dbo.Usuarios ";
$sql21=$sql21." where Unidad =" . $laUnidad;
$cursor21 =	 mssql_query($sql21);
if ($reg21=mssql_fetch_array($cursor21)) {
	$pfechaIngreso=strtotime($reg21[fechaIngreso]);
	$pfechaRetiro=strtotime($reg21[fechaRetiro]);
}
echo "pfechaIngreso =" . $reg21[fechaIngreso] . "<br>";
echo "pfechaIngreso =" . $pfechaIngreso . "<br>";

echo "pfechaRetiro =" . $reg21[fechaRetiro] . "<br>";
echo "pfechaRetiro =" . $pfechaRetiro . "<br>";


//llama la función cuando recarga para verificar si debe o no cambiar la selección.
if (trim($recarga) == "1"){
	//Verifica si los registros presentados en pantalla ya se encuentran seleccionados y/o existen registros con la misma llave almacenados en FacturacionProyectos

/*	echo ("<script>alert('entro');</script>");*/
	
	//Aquí se realiza el recorrido de todas las actividades (Vertical)
	$av = 1;
	$mensajeError = "";
	$mensajeErrorBD = "";
//	echo "av= " . $av . "<br>";
//	echo "cantReg =" . $cantReg . "<br>";
	while ($av <= $cantReg) {
//		echo "Primer While " . $av . "<br>";

		//Recoger las variables
		$lalstActiv = "lstActiv" . $av;
		$lalstHorario = "lstHorario" . $av;
		$lalstClaseT = "lstClaseT" . $av;
		$lalstLocaliza = "lstLocaliza" . $av;
		$lalstCargo = "lstCargo" . $av;

//		echo "a=" . ${$lalstActiv} . "<br>";
//		echo "h=" . ${$lalstHorario} . "<br>";
//		echo "CT=" . ${$lalstClaseT} . "<br>";
//		echo "L=" . ${$lalstLocaliza} . "<br>";
//		echo "Cargo=" . ${$lalstCargo} . "<br>";

		//Realiza la validación para los registros en los que ya estan seleccionadas todas las listas
		if ((trim(${$lalstActiv}) != "") AND (trim(${$lalstHorario}) != "") AND (trim(${$lalstClaseT}) != "") AND (trim(${$lalstLocaliza}) != "") AND (trim(${$lalstCargo}) != "")) {
//			echo "Entró al if <br>";
			
			//Aquí se realiza el recorrido de todas las actividades (Vertical) y compara lista a lista
			$avComp = 1;
			while ($avComp <= $cantReg) {
//				echo "2do While " . $avComp . "<br>";
				$igualActividad = 0;
				$igualHorario = 0;
				$igualClaseT = 0;
				$igualLocaliza = 0;
				$igualCargo = 0;
/*
				echo "igualActividad " . $igualActividad . "<br>";
				echo "igualHorario " . $igualHorario . "<br>";
				echo "igualClaseT " . $igualClaseT . "<br>";
				echo "igualLocaliza " . $igualLocaliza . "<br>";
				echo "igualCargo " . $igualCargo . "<br>";
*/				
				//No compara con sí mismo
				if ($av != $avComp) {
//					echo "Entró al if comparación  <br>";
				
					//Recoger las variables
					$lalstActivComp = "lstActiv" . $avComp;
					$lalstHorarioComp = "lstHorario" . $avComp;
					$lalstClaseTComp = "lstClaseT" . $avComp;
					$lalstLocalizaComp = "lstLocaliza" . $avComp;
					$lalstCargoComp = "lstCargo" . $avComp;
/*					
					echo "2a=" . ${$lalstActivComp} . "<br>";
					echo "2h=" . ${$lalstHorarioComp} . "<br>";
					echo "2CT=" . ${$lalstClaseTComp} . "<br>";
					echo "2L=" . ${$lalstLocalizaComp} . "<br>";
					echo "2Cargo=" . ${$lalstCargoComp} . "<br>";
*/					
					//Sólo compara contra los registros que tienen seleccionadas todas las listas
					if ((trim(${$lalstActivComp}) != "") AND (trim(${$lalstHorarioComp}) != "") AND (trim(${$lalstClaseTComp}) != "") AND (trim(${$lalstLocalizaComp}) != "") AND (trim(${$lalstCargoComp}) != "")) {
//						echo "Entró al if diferentes de vacios  <br>";

						//Compara actividad
						if ( (trim(${$lalstActiv})) == (trim(${$lalstActivComp})) ) {
							$igualActividad = 1;
						}

						//Compara Horario
						if ( (trim(${$lalstHorario})) == (trim(${$lalstHorarioComp})) ) {
							$igualHorario = 1;
						}
						//Compara CT
						if ( (trim(${$lalstClaseT})) == (trim(${$lalstClaseTComp})) ) {
							$igualClaseT = 1;
						}
						//Compara Localización
						if ( (trim(${$lalstLocaliza})) == (trim(${$lalstLocalizaComp})) ) {
							$igualLocaliza = 1;
						}
						//Compara Cargo
						if ( (trim(${$lalstCargo})) == (trim(${$lalstCargoComp})) ) {
							$igualCargo = 1;
						}
						
					} // cierra if trim(${$lalstActivComp}
/*					
					echo "----------------------------- <br>";
					echo "igualActividad " . $igualActividad . "<br>";
					echo "igualHorario " . $igualHorario . "<br>";
					echo "igualClaseT " . $igualClaseT . "<br>";
					echo "igualLocaliza " . $igualLocaliza . "<br>";
					echo "igualCargo " . $igualCargo . "<br>";
*/					
					//Si todas las listas coinciden
					if ( ($igualActividad==1) AND ($igualHorario==1) AND ($igualClaseT==1) AND ($igualLocaliza==1) AND ($igualCargo==1)  ) {
						$mensajeError =  $mensajeError  . "El registro " . $av . " es igual al registro " . $avComp . "\\n" ;
					}
					
//					echo "mensajeError " . $mensajeError . "<br>";
					
				} //Cierra if av
				
				$avComp = $avComp + 1;
			}
		} 
		
		//--Verifica si un registro de facturación ya se encuentra grabado en la BD
		$sql12="SELECT COUNT(*) hayFact " ;
		$sql12=$sql12 . " FROM FacturacionProyectos " ;
		$sql12=$sql12 . " WHERE id_proyecto = " . $cualProyecto ;
		$sql12=$sql12 . " AND id_actividad = " . ${$lalstActiv} ;
		$sql12=$sql12 . " AND unidad = " . $laUnidad ;
		$sql12=$sql12 . " AND vigencia = " . $cualVigencia;
		$sql12=$sql12 . " AND mes = " . $cualMes; 
		$sql12=$sql12 . " AND esInterno = 'I' " ; //Por ahora todos los usuarios son Internos
		$sql12=$sql12 . " AND IDhorario = " . ${$lalstHorario} ;
		$sql12=$sql12 . " AND clase_tiempo = " . ${$lalstClaseT} ;
		$sql12=$sql12 . " AND localizacion = " . ${$lalstLocaliza} ;
		$sql12=$sql12 . " AND cargo = '". ${$lalstCargo} ."' " ;
		$cursor12 =	 mssql_query($sql12);
		if ($reg12 = mssql_fetch_array($cursor12)) {
			if ($reg12['hayFact'] > 0) {
				$mensajeErrorBD =  $mensajeErrorBD  . "El registro " . $av . " presenta la misma configuración de otro registro previamente grabado. "  . "\\n" ;
			}
		}
		
		$av = $av+1;
	} //Cierra While av

	$error01 = 0;
	if (trim($mensajeError) != "") {
		echo ("<script>alert('".$mensajeError."');</script>");
		$error01 = 1;
	}
	
	$error02 = 0;
	if (trim($mensajeErrorBD) != "") {
		echo ("<script>alert('".$mensajeErrorBD."');</script>");
		$error02 = 1;
	}
	
	
} //cierra if recarga = 1





//************GRABACIÓN

//Si recarga es 2 realiza la grabación
if(trim($recarga) == "2"){

	
	

	$msgGraba = "";
	$msgNOGraba = "";
	
	//Aquí se realiza el recorrido de todas las actividades (Vertical)
	$s = 1;
	while ($s <= $cantReg) {
	
		//Recoger las variables
		$ellstActiv = "lstActiv" . $s;
		$ellstHorario = "lstHorario" . $s;
		$ellstClaseT = "lstClaseT" . $s;
		$ellstLocaliza = "lstLocaliza" . $s;
		$ellstCargo = "lstCargo" . $s;
		$elregDia = "regDia" . $s;
		$elregResumen = "regResumen" . $s;

/*		
		echo "Actividad= " . ${$ellstActiv} . "<br>";
		echo "Horario= " . ${$ellstHorario} . "<br>";
		echo "CT= " . ${$ellstClaseT} . "<br>";
		echo "Localiza= " . ${$ellstLocaliza} . "<br>";
		echo "dia = " . ${$elregDia} . "<br>";
		echo "Resumen= " . ${$elregResumen} . "<br>";
*/
		$rD = 1;
		$elHombreMes = 0;
		$elValFacturaUsu=0;
		while ($rD <= $totalDiasDinamicos) {
			$elregDia = $rD . "regDia" . $s;
			
			//o	HombresMesF = Horas facturadas / Cantidad de horas Oficina del mes correspondiente 
			$elHombreMes = ${$elregDia} / $mesHorasOfi;
			
			//o	Valor facturado = HombresMesF * Salario
			$elValFacturaUsu = $elHombreMes * $usuSalarioUsu ;
			
			//Realiza la grabación en FacturacionProyectos si se registró un valor en el día
			//dbo.FacturacionProyectos
			//id_proyecto, id_actividad, unidad, vigencia, mes, esInterno, fechaFacturacion, IDhorario, 
			//clase_tiempo, localizacion, cargo, hombresMesF, horasMesF, resumen, id_categoria, valorFacturado, 
			//salarioBase, tipoContrato, id_departamento, usuarioCrea, fechaCrea, usuarioMod, fechaMod

			echo $rD ."= " . ${$elregDia} . "<br>";
			echo "mesHorasOfi=" . $mesHorasOfi . "<br>";
			echo "usuCategoria=" . $usuCategoria . "<br>";
			
			echo "-usuTipoContrato=" . $usuTipoContrato . "<br>";
			echo "-ellstClaseT=" . ${$ellstClaseT} . "<br>";
			
			//Según el tipo de contrato verifica
			//Si la persona es TC y seleccionó Clase de tiempo = 2 entonces se le asigna automáticamente clase de tiempo 1
			//Si la persona es MT y seleccionó Clase de tiempo = 1 entonces se le asigna automáticamente clase de tiempo 2
			if ((trim($usuTipoContrato) == "TC") AND (${$ellstClaseT} == 2))  {
				${$ellstClaseT} = 1;
			}
			if ((trim($usuTipoContrato) == "MT") AND (${$ellstClaseT} == 1))  {
				${$ellstClaseT} = 2;
			}
			
			echo "usuTipoContrato=" . $usuTipoContrato . "<br>";
			echo "ellstClaseT=" . ${$ellstClaseT} . "<br>";
			
			
			$qryAdd = "INSERT INTO FacturacionProyectos ( ";
			$qryAdd = $qryAdd . " id_proyecto, id_actividad, unidad, vigencia, mes, esInterno, fechaFacturacion, IDhorario, ";
			$qryAdd = $qryAdd . " clase_tiempo, localizacion, cargo, hombresMesF, horasMesF, resumen, id_categoria, valorFacturado, ";
			$qryAdd = $qryAdd . " salarioBase, tipoContrato, id_departamento, usuarioCrea, fechaCrea ";
			$qryAdd = $qryAdd . " ) ";
			$qryAdd = $qryAdd . " VALUES( " ;
			$qryAdd = $qryAdd . " " . $cualProyecto . ", " ;
			$qryAdd = $qryAdd . " " . ${$ellstActiv} . ", " ;
			$qryAdd = $qryAdd . " " . $laUnidad . ", " ;
			$qryAdd = $qryAdd . " " . $cualVigencia . ", " ;
			$qryAdd = $qryAdd . " " . $cualMes . ", " ;
			$qryAdd = $qryAdd . " 'I' , " ; //Las personas que registran facturación desde aquí son Internas
			$qryAdd = $qryAdd . " '" . $cualMes . "/" . $rD . "/". $cualVigencia . "' , " ;
			$qryAdd = $qryAdd . " " . ${$ellstHorario} . ", " ;
			$qryAdd = $qryAdd . " " . ${$ellstClaseT} . ", " ;
			$qryAdd = $qryAdd . " " . ${$ellstLocaliza} . ", " ;
			$qryAdd = $qryAdd . " " . ${$ellstCargo} . ", " ;
			$qryAdd = $qryAdd . " " . $elHombreMes . ", " ;
			$qryAdd = $qryAdd . " " . ${$elregDia} . ", " ;
			$qryAdd = $qryAdd . " '" . ${$elregResumen} . "', " ;
			$qryAdd = $qryAdd . " " . $usuCategoria . ", " ;
			$qryAdd = $qryAdd . " " . $elValFacturaUsu . ", " ;
			$qryAdd = $qryAdd . " " . $usuSalarioUsu . ", " ;
			$qryAdd = $qryAdd . " '" . $usuTipoContrato . "', " ;
			$qryAdd = $qryAdd . " " . $usuDepartamentoUsu . ", " ;
			$qryAdd = $qryAdd . $laUnidad . ",  ";	
			$qryAdd = $qryAdd . " '" . gmdate ("n/d/Y")  . "' ";	
			$qryAdd = $qryAdd . " ) ";
			//$cursorAdd = mssql_query($qryAdd);
			
			echo $qryAdd . "<br>";
			
			

			$rD=$rD+1;
		}

		$s=$s+1;
	}

}

/*
//Realiza la grabación si recarga está en 1
if(trim($recarga) == "1"){
	
	//Variables del proceso
	$msgGraba = "";
	$msgSinGraba = "";
	$cuantasSinGrabar=0;
	$cuantasGrabo=0;


	$s = 1;
	while ($s <= $pCantidadItem) {
		//Recoger las variables
		$elcProyecto = "cProyecto" . $s;
		$elbtnSelecciona = "btnSelecciona" . $s;
		

		//Si seleccionar está en Si elimina el registro y vuelve y lo graba
		//dbo.ProyectosSinPlaneacion
		//id_proyecto, unidad, vigencia, mes, usuarioCrea, fechaCrea, usuarioMod, fechaMod
		if (trim(${$elbtnSelecciona}) == "S") {
			//Elimina el registro
			$qry01="DELETE FROM ProyectosSinPlaneacion ";
			$qry01=$qry01." WHERE unidad = " . $laUnidad ;
			$qry01=$qry01." AND mes = " . $miMes;
			$qry01=$qry01." AND vigencia = " . $miVigencia;
			$qry01=$qry01." AND id_proyecto = " . ${$elcProyecto} ;
			$cursorQry01 = mssql_query($qry01);
			
			//Realiza la inserción
			$qry02 = " INSERT INTO ProyectosSinPlaneacion ";
			$qry02 = $qry02 . " ( id_proyecto, unidad, vigencia, mes, usuarioCrea, fechaCrea) ";
			$qry02 = $qry02 . " VALUES ( ";
			$qry02 = $qry02 . " " . ${$elcProyecto} . ", ";		
			$qry02 = $qry02 . " " . $laUnidad . ", ";
			$qry02 = $qry02 . " " . $miVigencia . ", ";
			$qry02 = $qry02 . " " . $miMes . ", ";
			$qry02 = $qry02 . " " . $laUnidad . ", ";
			$qry02 = $qry02 . " '". gmdate ("n/d/Y") . "' ";
			$qry02 = $qry02 . " ) ";		
			$cursorQry02 = mssql_query($qry02);
			if  (trim($cursorQry02) != "")  {
				$cuantasGrabo=$cuantasGrabo+1;
			}
			
		}
		else {
			//Botta el registro. Por si acaso estaba grabado
			//Elimina el registro
			$qry01="DELETE FROM ProyectosSinPlaneacion ";
			$qry01=$qry01." WHERE unidad = " . $laUnidad ;
			$qry01=$qry01." AND mes = " . $miMes;
			$qry01=$qry01." AND vigencia = " . $miVigencia;
			$qry01=$qry01." AND id_proyecto = " . ${$elcProyecto} ;
			$cursorQry01 = mssql_query($qry01);
		}
		
		$s=$s+1;
	}
	
	//Acorde con las acciones realizadas muestra un mensaje y finaliza el proceso.	
	if ($cuantasGrabo > 0) {
		echo ("<script>alert('Se realizó la grabación de " . $cuantasGrabo . "  proyectos satisfactoriamente.');</script>");
	}
	else{
		echo ("<script>alert('No seleccionó ningún proyecto.');</script>");
	}
	echo ("<script>window.close();MM_openBrWindow('htFacturacion.php?pAno=$miVigencia&pMes=$miMes','winFacturacionHT','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");

}

*/
?>
<html>
<head>
<title>.:: Hoja de tiempo - Facturaci&oacute;n de Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script>
var nav4 = window.Event ? true : false;
function acceptNum(evt){
var key = nav4 ? evt.which : evt.keyCode; return (key <= 13 || (key>= 48 && key <= 57)); }
</script>

<script language="JavaScript" type="text/JavaScript">

function envia1(){ 
//alert ("Entro a envia 1");
document.Form1.recarga.value="1";
document.Form1.submit();
}

function aplicarResumen(){ 
var camposFijos, camposFijosEstaticos, camposDinamicosPre, camposDinamicos, camposDinamicosPost, totalCamposDinamicos, numPrimerResumen, CantCampos ;

//	alert (document.Form1.resumen.value);
//	alert (document.Form1.btnAplicaResumen[0].checked);
	
	//Encuentra la cantidad total de campos. Se recuerda que el primer campo se enumera como 0
	camposFijosEstaticos= 3;
	camposDinamicosPre= 5; //División,Horario, CT y localizacion y Cargo facturación
	camposDinamicos= document.Form1.totalDiasDinamicos.value; //Dias del mes
	camposDinamicosPost= 1; //Resumen
	totalCamposDinamicos=parseFloat(camposDinamicosPre)+parseFloat(camposDinamicos)+parseFloat(camposDinamicosPost)
	
	//Identifica el campo del promer campo resumen
	numPrimerResumen=parseFloat(camposFijosEstaticos)+parseFloat(camposDinamicosPre)+parseFloat(camposDinamicos) + 1;
	
	//Calcula la cantidad de campos totales en el formulario
	//CantCampos=12+(4*document.Form1.CantidadItem.value);
	CantCampos=parseFloat(camposFijosEstaticos)+(parseFloat(totalCamposDinamicos)*parseFloat(document.Form1.cantReg.value));
	
//	alert (totalCamposDinamicos);
//	alert (CantCampos);

	//Replica la descripción en todos los registros 
	//Si el botón de opción se encientra en SI
	if( document.Form1.btnAplicaResumen[0].checked ){
		for (i=numPrimerResumen;i<=CantCampos;i+=totalCamposDinamicos) {
			document.Form1.elements[i].value = document.Form1.resumen.value;
		}
	}
} //Cierra funcion aplicarResumen

function validaCTnormal(diaParaVerificar){ 
	var v1,v2,v3, totVar, i, CantCampos, msg1, msg2, msg3, mensaje;
	var camposFijosEstaticos, camposDinamicosPre, camposDinamicos, camposDinamicosPost, totalCamposDinamicos, numPrimerResumen, CantCampos ;
	v1='s';
	v2='s';
	v3='s';
	msg1 = '';
	msg2 = '';
	msg3 = '';
	mensaje = '';
	totVar = 0;
	
//	alert(diaParaVerificar) ;
	
	//Encuentra la cantidad total de campos. Se recuerda que el primer campo se enumera como 0
	camposFijosEstaticos= 3;
	camposDinamicosPre= 5; //División,Horario, CT, localizacion y cargoFacturacion
	camposDinamicos= document.Form1.totalDiasDinamicos.value; //Dias del mes
	camposDinamicosPost= 1; //Resumen
	totalCamposDinamicos=parseFloat(camposDinamicosPre)+parseFloat(camposDinamicos)+parseFloat(camposDinamicosPost);
//	alert (totalCamposDinamicos);
	
	registroVacio = 0;
//	alert (registroVacio);
//	alert (document.Form1.cantReg.value);
	
	//Campos a sumar para verificar si se ha registrado tiempo en ese día
	//parseFloat(2)= Localización, Cargo
	sumarCampos = parseFloat(2)+parseFloat(diaParaVerificar) ;
//	alert('sumarCampos='+sumarCampos);
	
	//Encontrar la clase de tiempo de todos los registros para verificar si se hace o no la comprobación
	//parseFloat(1) se suma 1 para tener en cuenta la totalidad de campos fisicos existentes
	rDesdeCT=parseFloat(1) + parseFloat(camposFijosEstaticos) + parseFloat(2);
	
//	alert('cantReg='+document.Form1.cantReg.value);
	for (fila=1; fila<=document.Form1.cantReg.value; fila++ ) {
		//Encuentra el campo que se requiere validar según el día seleccionado
		diaPorRegistro=parseFloat(rDesdeCT)+parseFloat(sumarCampos);		
//		alert ('rDesdeCT=' + rDesdeCT) ;
//		alert ('CT'+fila+'=' + document.Form1.elements[rDesdeCT].value) ;
		//Valida el registro para la clase de tiempo 1, 2 y 3
		if ( parseFloat(document.Form1.elements[rDesdeCT].value) <= 3 )  {
			//Valida el día en el registro segun la clase de tiempo
			if ((document.Form1.elements[diaPorRegistro].value) != "") {
				registroVacio = registroVacio + 1;
			}
		}

		//Encontrar el siguiente registro por clase de tiempo
		rDesdeCT=parseFloat(rDesdeCT) + parseFloat(totalCamposDinamicos) ;
//		alert ('diaPorRegistro=' + diaPorRegistro) ;
//		alert ('dia'+fila+'=' + document.Form1.elements[diaPorRegistro].value) ;
	}	

//	alert(registroVacio);
	return registroVacio;

} //Cierra funcion validaCTnormal 



function validaFila(fila, horasValidaDia, numDeDia, diaSemanaF, horasFactPrevias, horasFact1o2o11, horasMaxHorarios){ 
	var v1,v2,v3, totVar, i, CantCampos, msg1, msg2, msg3, mensaje;
	var camposFijosEstaticos, camposDinamicosPre, camposDinamicos, camposDinamicosPost, totalCamposDinamicos, numPrimerResumen, CantCampos ;
	v1='s';
	v2='s';
	v3='s';
	msg1 = '';
	msg2 = '';
	msg3 = '';
	mensaje = '';
	totVar = 0;
	nuevoTotal = 0;
	
//	alert('usuCategoria='+document.Form1.usuCategoria.value);
//	alert('horasMaxHorarios='+horasMaxHorarios);
	
//	alert(diaSemanaF);
//	alert(horasFactPrevias);
	
//	alert('Fila='+fila);
//	alert('horasValidaDia='+horasValidaDia);
//	alert('numDeDia='+numDeDia);
	
	//Encuentra la cantidad total de campos. Se recuerda que el primer campo se enumera como 0
	camposFijosEstaticos= 3;
	camposDinamicosPre= 5; //División,Horario, CT, localizacion y cargoFacturacion
	camposDinamicos= document.Form1.totalDiasDinamicos.value; //Dias del mes
	camposDinamicosPost= 1; //Resumen
	totalCamposDinamicos=parseFloat(camposDinamicosPre)+parseFloat(camposDinamicos)+parseFloat(camposDinamicosPost);
	
//	alert (totalCamposDinamicos);
	//Verifica que el valor de la facturación ingresada por día no supere el valor del horario para ese día
	//parseFloat(1) se suma 1 para tener en cuenta la totalidad de campos fisicos existentes
	rDdesde=parseFloat(1) + parseFloat(camposFijosEstaticos) + parseFloat(camposDinamicosPre) + (parseFloat(totalCamposDinamicos) *  parseFloat(fila-1)) ;
//	alert(rDdesde);
	
	rDhasta = rDdesde + parseFloat(camposDinamicos) - parseFloat(1); //Se le resta uno porque incluye desde donde arranca
//	alert(rDhasta);

	//Para enviar el día puntual que se está validándose
	d= rDdesde+numDeDia-1;

	//Verifica que el campo Horario se encuentre seleccionado
	//Se resta 4 para que me encuentre el valor de la lista Horario
	campoHorario=parseFloat(rDdesde) - parseFloat(4);
	//alert (campoHorario);
	
	//Verifica que el campo Clase de tiempo se encuentre seleccionado
	//Se resta 3 para que me encuentre el valor de la lista Horario
	campoCT=parseFloat(rDdesde) - parseFloat(3);
	
//	alert (document.Form1.elements[campoHorario].value);
//	if (document.Form1.elements[campoHorario].value == "") {
	if ((document.Form1.elements[campoHorario].value == "") || (document.Form1.elements[campoCT].value == "") ) {
		//alert("Se requiere que seleccione el Horario para validar las horas ingresadas");
		alert("Se requiere que seleccione el Horario y la Clase de tiempo para validar las horas ingresadas");
		document.Form1.elements[d].value="";
	}
	else {
	
		//alert(document.Form1.elements[campoHorario].value);
		
		//Sólo valida si la casilla no se encuentra vacia
		//alert(parseFloat(document.Form1.elements[d].value));
//		alert(d);

		if (document.Form1.elements[d].value != "") {
			if (document.Form1.elements[d].value <= "0") { 
					document.Form1.elements[d].value = "";
					alert("El valor a facturar debe ser mayor que 0");
			}
			else {
				//Solo calcula si el valor ingresado es menor o igual a lo que debe reportar segun el horario, si es festivo, si es fechaEspecial o fechaEspecialProy 
				if (parseFloat(document.Form1.elements[d].value) <= horasValidaDia) {			
	//				msg1="La cantidad de horas para el dia " + numDeDia + " es " + document.Form1.elements[d].value;
	//				alert(msg1);
				}
				else {
					document.Form1.elements[d].value = "";
					alert("La cantidad de horas no puede ser mayor que lo indicado por el horario o las fechas especiales del proyecto");
				}
				
				//Valida si puede registrar tiempo extra
				//Verifica si la clase de tiempo > 4 excepto CT 11
//				alert ('CT=' + parseFloat(document.Form1.elements[campoCT].value));

				if ( (parseFloat(document.Form1.elements[campoCT].value) >= 4) && (parseFloat(document.Form1.elements[campoCT].value) != 11)) {
//					alert ("entro al if CT");
					//Verifica si el día es Lunes=2 a viernes=5 ("", 1=Domingo, 2=Lunes, 3=Martes, 4=Miércoles, 5=Jueves, 6=Sábado)
					if ((parseFloat(diaSemanaF) >= 2) || (parseFloat(diaSemanaF) <= 5) ) {
//						alert (diaSemanaF);
						// Parte 1 if --> Verifica si hay registro de facturación para Clase de tiempo 1, 2 y/o 3 
						// Parte 2 If --> Verifica en la grilla que esté seleccionada la Clase de tiempo 1, 2 o 3 y que haya registrado horas en el día donde se está relacionado tiempo extra.
						// Parte 2 If --> Valida que en la grilla haya clase de tiempo 1, 2 o 3 con facturación para porder registrar tiempo extra
						regTiempoNormal = validaCTnormal(numDeDia);	
						if ( (parseFloat(horasFactPrevias) == 0) && (parseFloat(regTiempoNormal) == 0) ) {
//							alert (horasFactPrevias);
							document.Form1.elements[d].value = "";
							alert("No puede grabar tiempo extra si previamente no se ha registrado tiempo normal. Por favor corrija la información. Para registrar tiempo extra debe: \n -Registrar previamente facturación en clase de tiempo 1, 2 y/o 3 para registrar tiempo extra o \n -Registrar simultáneamente Clase de tiempo 1, 2 y/o 3 con otras clases de tiempo en el formulario.");
						} //Cierra if 
					} //Cierra if diaSemanaF
				} //Cierra if de comparación de clase de tiempo parseFloat(document.Form1.elements[campoCT].value) > 4
				
				//Valida que la sumatoria de horas en una fecha no supere la cantidad de horas máxima de los horarios en que se encuentra involucrado un usuario en un mes y año dados
				//Parte 3
				//Valida que el total de horas ya registradas en la facturación + las horas que está ingresando para la facturación no supere la cantidad de horas máxima de todos los horarios
				nuevoTotal = parseFloat(horasFact1o2o11) + parseFloat(document.Form1.elements[d].value) ;
				
				//6Feb2009
				//Por instrucción telefónica de Enrique Piñeros, las categorías 53 a 62 pueden facturar hasta 10 horas
				if ( (parseFloat(document.Form1.usuCategoria.value) >= 53) && (parseFloat(document.Form1.usuCategoria.value) >= 53) ) {
					if ( parseFloat(nuevoTotal) > 10 ) {
						document.Form1.elements[d].value = '';
						alert('Por su categoría la cantidad de horas máxima que puede registrar para este día es 10 y está tratando de registrar ' + nuevoTotal + '. Por favor corrija la información.');
					}
				}
				else {
					if ( parseFloat(nuevoTotal) > parseFloat(horasMaxHorarios) ) {
						document.Form1.elements[d].value = '';
						alert('La cantidad de horas máxima que puede registrar para este día es ' + horasMaxHorarios + ' y está tratando de registrar ' + nuevoTotal + '. Por favor corrija la información.');
					}
				}


			} //cierra el if de comparación con 0
		} //Cierra el if de comparación contra vacio
	} //Cierra if campoHorario, campoCT
} //Cierra funcion validaFila

function limpiaHorasFila(fila){ 
	var v1,v2,v3, totVar, i, CantCampos, msg1, msg2, msg3, mensaje;
	var camposFijosEstaticos, camposDinamicosPre, camposDinamicos, camposDinamicosPost, totalCamposDinamicos, numPrimerResumen, CantCampos ;
	v1='s';
	v2='s';
	v3='s';
	msg1 = '';
	msg2 = '';
	msg3 = '';
	mensaje = '';
	totVar = 0;
	
	//Encuentra la cantidad total de campos. Se recuerda que el primer campo se enumera como 0
	camposFijosEstaticos= 3;
	camposDinamicosPre= 5; //División,Horario, CT, localizacion y cargoFacturacion
	camposDinamicos= document.Form1.totalDiasDinamicos.value; //Dias del mes
	camposDinamicosPost= 1; //Resumen
	totalCamposDinamicos=parseFloat(camposDinamicosPre)+parseFloat(camposDinamicos)+parseFloat(camposDinamicosPost);
	
//	alert (totalCamposDinamicos);
	//Verifica que el valor de la facturación ingresada por día no supere el valor del horario para ese día
	//parseFloat(1) se suma 1 para tener en cuenta la totalidad de campos fisicos existentes
	rDdesde=parseFloat(1) + parseFloat(camposFijosEstaticos) + parseFloat(camposDinamicosPre) + (parseFloat(totalCamposDinamicos) *  parseFloat(fila-1)) ;
//	alert(rDdesde);
	
	rDhasta = rDdesde + parseFloat(camposDinamicos) - parseFloat(1); //Se le resta uno porque incluye desde donde arranca
//	alert(rDhasta);

	for (d=rDdesde; d<=rDhasta; d++) {
		document.Form1.elements[d].value = "";
	} //Cierra for d		
} //Cierra funcion limpiaHorasFila 

function validaFacturacionFilas(){ 
	var v1,v2,v3, totVar, i, CantCampos, msg1, msg2, msg3, mensaje;
	var camposFijosEstaticos, camposDinamicosPre, camposDinamicos, camposDinamicosPost, totalCamposDinamicos, numPrimerResumen, CantCampos ;
	v1='s';
	v2='s';
	v3='s';
	msg1 = '';
	msg2 = '';
	msg3 = '';
	mensaje = '';
	totVar = 0;
	
	
	//Encuentra la cantidad total de campos. Se recuerda que el primer campo se enumera como 0
	camposFijosEstaticos= 3;
	camposDinamicosPre= 5; //División,Horario, CT, localizacion y cargoFacturacion
	camposDinamicos= document.Form1.totalDiasDinamicos.value; //Dias del mes
	camposDinamicosPost= 1; //Resumen
	totalCamposDinamicos=parseFloat(camposDinamicosPre)+parseFloat(camposDinamicos)+parseFloat(camposDinamicosPost);
//	alert (totalCamposDinamicos);
	
	registroVacio = 0;
//	alert (registroVacio);
//	alert (document.Form1.cantReg.value);
	
	for (fila=1; fila<=document.Form1.cantReg.value; fila++ ) {
		//Verifica que el valor de la facturación ingresada por día no supere el valor del horario para ese día
		//parseFloat(1) se suma 1 para tener en cuenta la totalidad de campos fisicos existentes
		rDdesde=parseFloat(1) + parseFloat(camposFijosEstaticos) + parseFloat(camposDinamicosPre) + (parseFloat(totalCamposDinamicos) *  parseFloat(fila-1)) ;
//		alert(rDdesde);
//		alert(document.Form1.elements[rDdesde].value);
		
		rDhasta = rDdesde + parseFloat(camposDinamicos) - parseFloat(1); //Se le resta uno porque incluye desde donde arranca
//		alert(rDhasta);
//		alert(document.Form1.elements[rDhasta].value);
	
		sumaCampos=0;
		for (d=rDdesde; d<=rDhasta; d++) {
			if (document.Form1.elements[d].value == "") {
				sumaCampos = sumaCampos+1;
			} //cierra if
		} //Cierra for d		
		
//		alert(camposDinamicos);
//		alert(sumaCampos);
		
		if (parseFloat(camposDinamicos) == parseFloat(sumaCampos)) {
			registroVacio = registroVacio + 1;
		}
	} //cierra for fila
	
//	alert(registroVacio);
	return registroVacio;
} //Cierra funcion validaFacturacionFilas 


//****

//validaFilaV1 primera versión si funciona pero no acepta errores en la grilla porque borra todo
/*
function validaFilaV1(fila, horasValidaDia){ 

	var v1,v2,v3, totVar, i, CantCampos, msg1, msg2, msg3, mensaje;
	var camposFijosEstaticos, camposDinamicosPre, camposDinamicos, camposDinamicosPost, totalCamposDinamicos, numPrimerResumen, CantCampos ;
	v1='s';
	v2='s';
	v3='s';
	msg1 = '';
	msg2 = '';
	msg3 = '';
	mensaje = '';
	totVar = 0;
	
	//Encuentra la cantidad total de campos. Se recuerda que el primer campo se enumera como 0
	camposFijosEstaticos= 3;
	camposDinamicosPre= 5; //División,Horario, CT, localizacion y cargoFacturacion
	camposDinamicos= document.Form1.totalDiasDinamicos.value; //Dias del mes
	camposDinamicosPost= 1; //Resumen
	totalCamposDinamicos=parseFloat(camposDinamicosPre)+parseFloat(camposDinamicos)+parseFloat(camposDinamicosPost);
	
//	alert (totalCamposDinamicos);
	//Verifica que el valor de la facturación ingresada por día no supere el valor del horario para ese día
	//parseFloat(1) se suma 1 para tener en cuenta la totalidad de campos fisicos existentes
	rDdesde=parseFloat(1) + parseFloat(camposFijosEstaticos) + parseFloat(camposDinamicosPre) + (parseFloat(totalCamposDinamicos) *  parseFloat(fila-1)) ;
//	alert(rDdesde);
	
	rDhasta = rDdesde + parseFloat(camposDinamicos) - parseFloat(1); //Se le resta uno porque incluye desde donde arranca
//	alert(rDhasta);

	//Verifica que el campo Horario se encuentre seleccionado
	//Se resta 3 para que me encuentre el valor de la lista Horario
	campoHorario=parseFloat(rDdesde) - parseFloat(4);
//	alert (campoHorario);
//	alert (document.Form1.elements[campoHorario].value);
	if (document.Form1.elements[campoHorario].value == "") {
		alert("Se requiere que seleccione el Horario para validar las horas ingresadas");
	}
	else {
		//alert(document.Form1.elements[campoHorario].value);
		
		zDia=1;
		for (d=rDdesde; d<=rDhasta; d++) {
			//Sólo valida si la casilla no se encuentra vacia
			//alert(parseFloat(document.Form1.elements[d].value));
	
			if (document.Form1.elements[d].value != "") {
		
				//Solo calcula si el valor ingresado es menor o igual a lo que debe reportar segun el horario, si es festivo, si es fechaEspecial o fechaEspecialProy 
				if (parseFloat(document.Form1.elements[d].value) <= horasValidaDia) {			
	//				msg1="La cantidad de horas para el dia " + zDia + " es " + document.Form1.elements[d].value;
	//				alert(msg1);
				}
				else {
					document.Form1.elements[d].value = "";
					alert("La cantidad de horas no puede ser mayor que lo indicado por el horario o las fechas especiales del proyecto");
				}
			}
			zDia=zDia+1;
		} //Cierra for d		
	} //Cierra if campoHorario
} //Cierra funcion validaFilaV1 Funciona
*/
//****

function validaFormulario(){ 
var camposFijos, camposFijosEstaticos, camposDinamicosPre, camposDinamicos, camposDinamicosPost, totalCamposDinamicos, numPrimerResumen, CantCampos ;
var v1,v2, v3, v4, v5, v6, v7, v8, v9, i, CantCampos, msg1, msg2, msg3, msg4, msg5, msg6, mensaje, filaVacia;
v1='s';
v2='s';
v3='s';
v4='s';
v5='s';
v6='s';
v7='s';
v8='s';
v9='s';
msg1 = '';
msg2 = '';
msg3 = '';
msg4 = '';
msg5 = '';
msg6 = '';
msg7 = '';
msg8 = '';
msg9 = '';
mensaje = '';
filaVacia = 0;

//	alert (document.Form1.resumen.value);
//	alert (document.Form1.btnAplicaResumen[0].checked);
	
	//Encuentra la cantidad total de campos. Se recuerda que el primer campo se enumera como 0
	camposFijosEstaticos= 3;
	camposDinamicosPre= 5; //División,Horario, CT y localizacion y Cargo facturación
	camposDinamicos= document.Form1.totalDiasDinamicos.value; //Dias del mes
	camposDinamicosPost= 1; //Resumen
	totalCamposDinamicos=parseFloat(camposDinamicosPre)+parseFloat(camposDinamicos)+parseFloat(camposDinamicosPost)
	
	//Identifica el campo del promer campo resumen
	numPrimerResumen=parseFloat(camposFijosEstaticos)+parseFloat(camposDinamicosPre)+parseFloat(camposDinamicos) + 1;
	
	//Calcula la cantidad de campos totales en el formulario
	CantCampos=parseFloat(camposFijosEstaticos)+(parseFloat(totalCamposDinamicos)*parseFloat(document.Form1.cantReg.value));
	
	
	//Verifica que la División No se haya quedado vacía
	for (i=4;i<=CantCampos;i+=totalCamposDinamicos) {
		if (document.Form1.elements[i].value == '') {
			v1='n';
			msg1 = 'División - Actividad es un campo obligatorio. \n'
		}
	}

	//Verifica que el Horario no esté vacio
	for (i=5;i<=CantCampos;i+=totalCamposDinamicos) {
		if (document.Form1.elements[i].value == '') {
			v2='n';
			msg2 = 'Horario es un campo obligatorio. \n'
		}
	}

	//Verifica que la Calse de tiempo no esté vacio
	for (i=6;i<=CantCampos;i+=totalCamposDinamicos) {
		if (document.Form1.elements[i].value == '') {
			v3='n';
			msg3 = 'Clase de tiempo es un campo obligatorio. \n'
		}
	}

	//Verifica que la localización no esté vacio
	for (i=7;i<=CantCampos;i+=totalCamposDinamicos) {
		if (document.Form1.elements[i].value == '') {
			v4='n';
			msg4 = 'Localización es un campo obligatorio. \n'
		}
	}

	//Verifica que el cargo no esté vacio
	for (i=8;i<=CantCampos;i+=totalCamposDinamicos) {
		if (document.Form1.elements[i].value == '') {
			v5='n';
			msg5 = 'Cargo de facturación es un campo obligatorio. \n'
		}
	}

	//Verifica que el resumen no esté vacio
	regResumenVal=1
	for (i=numPrimerResumen;i<=CantCampos;i+=totalCamposDinamicos) {
		if (document.Form1.elements[i].value == '') {
			v6='n';
			msg6 = msg6 + 'Resumen es un campo obligatorio. \n'
		}
		else {
			//Valida que no se escriban más de 300 caracteres en el resumen de trabajo
			if (document.Form1.elements[i].value.length > 300) {
				v6='n';
				msg6 = msg6 + 'El campo Resumen de trabajo del registro ' + regResumenVal + ' no debe tener más de 300 caracteres. \n';
			}
		}
		regResumenVal=regResumenVal+1;
	}
	
	//Valida que no existan registros repetidos en la ventana de facturación
	if (document.Form1.frmErr01.value == '1') {
		v7='n';
		msg7 = 'Varios registros de esta ventana presentan la misma configuración. \n'
	}
	
	//Valida que no existan registros repetidos en la ventana de facturación
	if (document.Form1.frmErr02.value == '1') {
		v8='n';
		msg8 = 'Algun registros de esta ventana presentan la misma configuración de una facturación previamente grabada. \n';
	}
	
	filaVacia = validaFacturacionFilas();
//	alert ("-----") ;
//	alert(filaVacia);
	if (filaVacia > 0) {
		v9='n';
		msg9 = 'No se ha relacionado facturación en algún registros de esta ventana, por favor complete la información.  \n';
	}
	

//Si todas las validaciones fueron correctas, el formulario hace submit y permite grabar
	if ( (v1=='s') && (v2=='s') && (v3=='s') && (v4=='s') && (v5=='s') && (v6=='s') && (v7=='s') && (v8=='s') && (v9=='s')) {
		document.Form1.recarga.value="2";
		document.Form1.submit();
	}
	else {
		mensaje = msg1 + msg2 + msg3 + msg4 + msg5 + msg6 + msg7 + msg8 + msg9;
		alert (mensaje);
	}
}

//-->
</script>



</head>

<body leftmargin="0" topmargin="0" rightmargin="0" bgcolor="#EAEAEA">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">.: FACTURACI&Oacute;N DEL PROYECTO </td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
<form name="Form1" method="post" action="">
  <tr>
    <td bgcolor="#FFFFFF">
	  
	  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><table width="100%"  border="0" cellspacing="1" cellpadding="0">
            <tr>
              <td width="15%" class="TituloTabla">Proyecto</td>
              <td class="TxtTabla">
			  <? 
			  if ($reg01 = mssql_fetch_array($cursor01)) {
			  	 echo " [" . $reg01['codigo'] . "." . $reg01['cargo_defecto'] . "] " . strtoupper($reg01['nombre']) ; 
			  }
		  	  ?>			  </td>
            </tr>
            <tr>
              <td class="TituloTabla">Mes-Vigencia</td>
              <td class="TxtTabla">
			  <? echo $vMeses[$cualMes] . "-" . $cualVigencia; ?>
			  </td>
            </tr>
            <tr>
              <td class="TituloTabla">Horas Mes-Vigencia </td>
              <td class="TxtTabla"><strong>Oficina</strong> [<? echo $miHorasOficina; ?>]
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Campo</strong> [<? echo $miHorasCampo; ?>]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong>Categoría 42</strong> [<? echo $miHorasCat42 ?>]			  </td>
            </tr>
            <tr>
              <td class="TituloTabla">Total de Horas facturadas <br>
                Mes-Vigencia </td>
              <td class="TxtTabla">
			  <strong>Oficina</strong> = <? echo $miOficinaFacturadas; ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Campo</strong> = <? echo $miCampoFacturadas; ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Planilla</strong> = <? echo $miPlanillaFacturadas; ?> </td>
            </tr>
            <tr>
              <td width="15%" class="TituloTabla">Cantidad de registros </td>
              <td class="TxtTabla"><input name="cantReg" type="text" class="CajaTexto" id="cantReg" value="<? echo $cantReg; ?>" size="10" onKeyPress="return acceptNum(event)" onChange="envia1()"></td>
            </tr>
            <tr>
              <td width="15%" class="TituloTabla">Resumen de trabajo </td>
              <td class="TxtTabla">                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td><textarea name="resumen" cols="100" rows="3" class="CajaTexto" id="textarea" onChange="aplicarResumen();"><? echo $resumen;  ?></textarea></td>
                    </tr>
                  <tr>
                    <td class="TxtTabla"><strong>El resumen aplica para todos los d&iacute;a</strong>s?
					<?
					//Define qué valor trae el botón de opción btnAplicaResumen
					//Si es la primera vez que se carga el formulario inicializa en No
					//Solo cuando el usuario ha seleccionado Si replica el resumen
					if (trim($btnAplicaResumen) == "") {
						$btnAplicaResumen="N";
					}
//					else {
						if (trim($btnAplicaResumen) == "N") {
							$selAplicaResumenNo="checked";
							$selAplicaResumenSi="";
						}
						else {
							$selAplicaResumenNo="";
							$selAplicaResumenSi="checked";
						}	
//					}
					?>                      
					<input name="btnAplicaResumen" type="radio" value="S" <? echo $selAplicaResumenSi; ?> onClick="aplicarResumen();" >
                      Si 
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                      <input name="btnAplicaResumen" type="radio" value="N" <? echo $selAplicaResumenNo; ?> onClick="aplicarResumen();" >
                      No </td>
                    </tr>
                </table></td>
            </tr>
          </table>
            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td height="5" class="TituloTabla"> </td>
              </tr>
            </table>
            <table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr class="TituloTabla2">
                <td width="15%">
				<?
				$txtAyuda="División - Actividad \n- Listado de actividades vigentes en el proyecto \n- *Actividades con planeación";
				?>
				<img src="../portal/images/icoDetalleInf.gif"  style="cursor: hand" alt="<? echo $txtAyuda; ?>" width="14" height="16">Divisi&oacute;n - Actividad
				</td>
                <td width="7%">Horario</td>
                <td width="7%">Clase de tiempo </td>
                <td width="7%">Localizaci&oacute;n</td>
			    <td width="7%">Cargo</td>
			    <?
			  //25Jul2013
			  //PBM
			  //Genera los dís del mes
			  for ($d=1; $d<=$totalDiasMes; $d++) {
			  ?>
                <td width="1%"><? echo $d; ?></td>
				<?
				} //Cierra for d
				?>
                <td>Resumen de trabajo </td>
              </tr>
		  	  <?
			  $r = 1;
			  while ($r <= $cantReg) {
			  ?>
              <tr class="TxtTabla">
                <td width="15%"><select name="lstActiv<? echo $r; ?>" class="CajaTexto" id="lstActiv<? echo $r; ?>" style='width:200px; ' onChange="envia1()">
                  <option value="" selected >  </option>
				  <?
				  	$cursor04 =	 mssql_query($sql04);
					$milstActiv = "lstActiv" . $r;
					while ($reg04 = mssql_fetch_array($cursor04)) {
						//Verifica si la actividad es planeada o no 
						if (trim($reg04['estaPlaneada']) != "") {
							$marcaPlaneada="*";
						}
						else {
							$marcaPlaneada="";
						}
					
						//Recoge las variables para poder dejar recargado el formulario
						if (${$milstActiv} == $reg04['id_actividad'] ) {
							$sellstActiv="selected";
						}
						else {
							$sellstActiv="";
						}
				  ?>
				  <option value="<? echo $reg04['id_actividad']; ?>" <? echo $sellstActiv; ?> ><? echo "[" . $reg04['macroactividad'] . "]  - " . strtoupper($reg04['nombre']) . $marcaPlaneada ; ?></option>
				  <? } // cierra while reg04 ?>
                </select></td>
                <td width="7%"><select name="lstHorario<? echo $r; ?>" class="CajaTexto" id="lstHorario<? echo $r; ?>" style='width:100px; ' onChange=" limpiaHorasFila(<? echo $r; ?>); envia1();" >
                  <option value=""> </option>
				  <?
				  	$cursor05 =	 mssql_query($sql05);
					$milstHorario = "lstHorario" . $r;
					while ($reg05 = mssql_fetch_array($cursor05)) {
						//Recoge las variables para poder dejar recargado el formulario
						if (${$milstHorario} == $reg05['IDhorario'] ) {
							$sellstHorario="selected";
						}
						else {
							$sellstHorario="";
						}
					
				  ?>
				  <option value="<? echo $reg05['IDhorario']; ?>" <? echo $sellstHorario; ?> ><? echo "[" . $reg05['Lunes'] . "-" . $reg05['Martes'] . "-" . $reg05['Miercoles'] . "-" . $reg05['Jueves'] . "-" . $reg05['Viernes'] . "-" . $reg05['Sabado'] . "-" . $reg05['Domingo'] . "]" . strtoupper($reg05['NomHorario']) ; ?></option>
				  <? } // cierra while reg05 ?>
                </select>
				<?
				//--Trae las horas del horario seleccionado
				$sql08="SELECT * ";
				$sql08=$sql08." FROM Horarios ";
				$sql08=$sql08." WHERE IDhorario = " . ${$milstHorario} ;
				$cursor08 =	 mssql_query($sql08);
				if ($reg08 = mssql_fetch_array($cursor08)) {
					//Define el array horas segun el horario seleccionado
					$vHorasHorario= array("", $reg08['Domingo'], $reg08['Lunes'], $reg08['Martes'], $reg08['Miercoles'], $reg08['Jueves'], $reg08['Viernes'], $reg08['Sabado']); 
				}
	
				?>
				</td>
                <td width="7%"><select name="lstClaseT<? echo $r; ?>" class="CajaTexto" id="lstClaseT<? echo $r; ?>" style='width:100px; ' onChange="envia1()" >
                  <option value=""> </option>
				  <?
				  	$cursor06 =	 mssql_query($sql06);
					$milstClaseT = "lstClaseT" . $r;
					while ($reg06 = mssql_fetch_array($cursor06)) {
					
						//Según tipo de contrato sugerir clase de tiempo 
						//Sólo aplica si el formulario se está cargando por primera vez
						if (trim($recarga) == ""){
							if (strtoupper($miTipoContrato) == "TC") {
								$itemTC="1";
							}
							if (strtoupper($miTipoContrato) == "MT") {
								$itemTC="2";
							}
							//Recoge las variables para poder dejar recargado el formulario
							if ($itemTC == $reg06['clase_tiempo'] ) {
								$sellstClaseT="selected";
							}
							else {
								$sellstClaseT="";
							}							
							
						}
						else {
							//Recoge las variables para poder dejar recargado el formulario
							if (${$milstClaseT} == $reg06['clase_tiempo'] ) {
								$sellstClaseT="selected";
							}
							else {
								$sellstClaseT="";
							}
						}
					
				  ?>
				  <option value="<? echo $reg06['clase_tiempo']; ?>" <? echo $sellstClaseT; ?> ><? echo $reg06['descripcion'] ; ?></option>
				  <? } // cierra while reg06 ?>
                </select></td>
                <td width="7%"><select name="lstLocaliza<? echo $r; ?>" class="CajaTexto" id="lstLocaliza<? echo $r; ?>" style='width:100px; ' onChange="envia1()">
                  <option value=""> </option>
				  <?
				  	$cursor07 =	 mssql_query($sql07);
					$milstLocaliza = "lstLocaliza" . $r;
					while ($reg07 = mssql_fetch_array($cursor07)) {
						//Recoge las variables para poder dejar recargado el formulario
						if (${$milstLocaliza} == $reg07['localizacion'] ) {
							$sellstLocaliza="selected";
						}
						else {
							$sellstLocaliza="";
						}
					
				  ?>
				  <option value="<? echo $reg07['localizacion']; ?>" <? echo $sellstLocaliza; ?> ><? echo $reg07['nomLocalizacion'] ; ?></option>
				  <? } // cierra while reg07 ?>
                </select></td>
				<td width="7%">
				<select name="lstCargo<? echo $r; ?>" class="CajaTexto" id="lstCargo<? echo $r; ?>" style='width:100px; ' onChange="envia1()">
                  <option value=""> </option>
				  <?
				  	$cursor11 = mssql_query($sql11);
					$milstCargo = "lstCargo" . $r;
					while ($reg11 = mssql_fetch_array($cursor11)) {
						//Recoge las variables para poder dejar recargado el formulario
						if (${$milstCargo} == $reg11['cargos'] ) {
							$sellstCargo="selected";
						}
						else {
							$sellstCargo="";
						}
					
				  ?>
				  <option value="<? echo $reg11['cargos']; ?>" <? echo $sellstCargo; ?> ><? echo $reg11['cargos'] ; ?></option>
				  <? } // cierra while reg11 ?>
                </select>
				</td>
				<?
			  //25Jul2013
			  //PBM
			  //Genera los dís del mes 
			  for ($d2=1; $d2<=$totalDiasMes; $d2++) {
			  
			  	//--Determina si el día es sábado, domingo, festivo o dia normal
			  	//--Domingo=1, Lunes = 2..., Sabado=7
				$fechaAconsultar=$cualVigencia."-".$cualMes."-".$d2;
				$esFestivo=0;
				$esDia=0;
				$usarClase="";
				$horasAvalidarDia=0;
				$sql03 = "SELECT COUNT(*) as hayFestivo , DATEPART ( dw , '".$fechaAconsultar."' ) diaSemana";
				$sql03 = $sql03 . " FROM Festivos ";
				$sql03 = $sql03 . " where fecha = '". $fechaAconsultar ."' ";
				$cursor03 =	 mssql_query($sql03);
				if ($reg03 = mssql_fetch_array($cursor03)) {
					$esFestivo=$reg03['hayFestivo'];
					$esDia=$reg03['diaSemana'];
				}
				
				//Es festivo
				if ( (($esDia >=2) AND ($esDia <=6)) AND ($esFestivo > 0) ) {
					$usarClase="tdFestivo";
					$horasAvalidarDia=0; //Si es festivo No se deben reportar horas
				}
				
				//Es dia Normal
				if ( (($esDia >=2) AND ($esDia <=6)) AND ($esFestivo == 0) ) {
					$usarClase="TxtTabla";
					$horasAvalidarDia=$vHorasHorario[$esDia]; //Si es dia Normal se reportan las horas indicadas por el horario
				}
				
				//Es sábado o domingo
				if ( ($esDia == 1) OR ($esDia ==7) ) {
					$usarClase="tdFinSemana";
					$horasAvalidarDia=$vHorasHorario[$esDia]; //Si es dia Normal se reportan las horas indicadas por el horario
				}
				
			  ?>
                <td width="1%" class="<? echo $usarClase; ?>">
				<?
				//--Trae la cantidad de horas de un horario en una fecha especifica
				$horasFechasEspeciales = "";
				$sql09="SELECT * ";
				$sql09=$sql09." FROM FechasEspeciales ";
				$sql09=$sql09." WHERE IDhorario = " . ${$milstHorario} ;
				$sql09=$sql09." AND Fecha = '".$cualVigencia."-".$cualMes."-".$d2."' ";
				$cursor09 =	 mssql_query($sql09);
				if ($reg09 = mssql_fetch_array($cursor09)) {
					$horasFechasEspeciales=$reg09['CuantasHoras'];
					$horasAvalidarDia=$horasFechasEspeciales; //Si el día del horario tiene un horario especial asume la cantidad de horas de la fechaEspecial
				}
				echo $horasFechasEspeciales . "<br>"; 

				//--Trae la cantidad de horas de un horario para un proyecto en una fecha especifica
				$horasFechasEspecialesProy = "";
				$sql10="SELECT * ";
				$sql10=$sql10." FROM FechasEspecialesProy ";
				$sql10=$sql10." WHERE id_proyecto = " . $cualProyecto ;
				$sql10=$sql10." AND IDhorario = " . ${$milstHorario} ;
				$sql10=$sql10." AND Fecha = '".$cualVigencia."-".$cualMes."-".$d2."' ";
				$cursor10 =	 mssql_query($sql10);
				if ($reg10 = mssql_fetch_array($cursor10)) {
					$horasFechasEspecialesProy=$reg10['CuantasHoras'];
					$horasAvalidarDia=$horasFechasEspecialesProy; //Si el día del horario para el proyecto tiene un horario especial asume la cantidad de horas de la FechasEspecialesProy
				}
				echo $horasFechasEspecialesProy . "<br>"; 
				//Si no se ha seleccionado horario la variable $horasAvalidarDia podría venir vacia
				if (trim($horasAvalidarDia) == "") {
					$horasAvalidarDia = 0;
				}

				
				echo "*-" . $horasAvalidarDia . "<br>"; 
				
				$mitxtregDia = $d2 . "regDia" . $r;
				
				//Valida que no se pueda ingresar tiempo extra (clase_tiempo <> 1 o 2) si no se ha registrado previamente tiempo normal (clase_tiempo = 1 o 2)
				//Verificar que de lunes a viernes se haya registrado facturación en CT= 1, 2 o 3 si se está tratando de registrar clase de tiempo extra
				//Para la clase de tiempo > 4 excepto la 11 valida si puede registrarse tiempo extra
				$horasFactCT_1_2_3=0;
				$sql18="SELECT COALESCE(SUM(horasMesF), 0) totalHorasR ";
				$sql18=$sql18." FROM FacturacionProyectos ";
				$sql18=$sql18." WHERE unidad =  " . $laUnidad ;
				$sql18=$sql18." AND (clase_tiempo = 1 or clase_tiempo = 2 or clase_tiempo = 3) ";
				$sql18=$sql18." AND vigencia = " . $cualVigencia ;
				$sql18=$sql18." AND mes = " . $cualMes;
				$sql18=$sql18." AND DAY(fechaFacturacion) = " . $d2;
				$cursor18 =	 mssql_query($sql18);
				if ($reg18 = mssql_fetch_array($cursor18)) {
					$horasFactCT_1_2_3 = $reg18['totalHorasR'];
					echo "-->" . $horasFactCT_1_2_3 . "<br>";
					echo "dia-->" . $esDia . "<br>";
					echo "CT-->" . (${$milstClaseT}) . "<br>";
				}
				
				//Valida que la sumatoria de horas en una fecha no supere la cantidad de horas máxima de los horarios en que se encuentra involucrado un usuario en un mes y año dados
				//Parte 1
				//Totaliza horas por día para clase de tiempo 1, 2 u 11
				$horasFactCT_1o2o11=0;
				$sql19="SELECT COALESCE(SUM(horasMesF), 0) totalHorasF ";
				$sql19=$sql19." FROM FacturacionProyectos ";
				$sql19=$sql19." WHERE unidad =  " . $laUnidad ;
				$sql19=$sql19." AND (clase_tiempo = 1 or clase_tiempo = 2 or clase_tiempo = 11) ";
				$sql19=$sql19." AND vigencia = " . $cualVigencia ;
				$sql19=$sql19." AND mes = " . $cualMes;
				$sql19=$sql19." AND DAY(fechaFacturacion) = " . $d2;
				$cursor19 =	 mssql_query($sql19);
				if ($reg19 = mssql_fetch_array($cursor19)) {
					$horasFactCT_1o2o11 = $reg19['totalHorasF'];
					echo "2-->" . $horasFactCT_1o2o11 . "<br>";
				}
				
				//Parte2
				//Busca los horarios en los que el usuario tiene asignación para la fecha 
				$diaFecha="";
				$sql20a="SELECT  B.* , DATEPART ( dw , '".$cualVigencia."-".$cualMes."-".$d2."' ) diaSemana, ";
				$sql20a=$sql20a." CASE DATEPART ( dw , '".$cualVigencia."-".$cualMes."-".$d2."' )   ";
				$sql20a=$sql20a." 	WHEN 1 THEN 'Domingo' ";
				$sql20a=$sql20a." 	WHEN 2 THEN 'Lunes' ";
				$sql20a=$sql20a." 	WHEN 3 THEN 'Martes' ";
				$sql20a=$sql20a." 	WHEN 4 THEN 'Miercoles' ";
				$sql20a=$sql20a." 	WHEN 5 THEN 'Jueves' ";
				$sql20a=$sql20a." 	WHEN 6 THEN 'Viernes' ";
				$sql20a=$sql20a." 	WHEN 7 THEN 'Sabado' ";
				$sql20a=$sql20a." END dia ";
				$sql20a=$sql20a." FROM FacturacionProyectos A, Horarios B ";
				$sql20a=$sql20a." WHERE A.IDhorario = B.IDhorario ";
				$sql20a=$sql20a." AND A.unidad = " . $laUnidad ;
				$sql20a=$sql20a." AND A.vigencia = " . $cualVigencia ;
				$sql20a=$sql20a." AND A.mes = " . $cualMes;
				$sql20a=$sql20a." AND DAY(A.fechaFacturacion)= " . $d2;
				$cursor20a =	 mssql_query($sql20a);
				if ($reg20a = mssql_fetch_array($cursor20a)) {
					$diaFecha = $reg20a['dia'];
				}
				
				//Parte2
				//Encuentra la cantidad de horas máximas según los horarios usados en el día
				$maxCantHrHorario=0;
				$sql20b="SELECT MAX(". $diaFecha .") maxHorasHorario ";
				$sql20b=$sql20b." FROM FacturacionProyectos A, Horarios B ";
				$sql20b=$sql20b." WHERE A.IDhorario = B.IDhorario ";
				$sql20b=$sql20b." AND A.unidad = "  . $laUnidad ;
				$sql20b=$sql20b." AND A.vigencia = " . $cualVigencia ;
				$sql20b=$sql20b." AND A.mes = " . $cualMes;
				$sql20b=$sql20b." AND DAY(A.fechaFacturacion)= " . $d2;
				$cursor20b = mssql_query($sql20b);
				if ($reg20b = mssql_fetch_array($cursor20b)) {
					$maxCantHrHorario=$reg20b['maxHorasHorario'];
					echo "maxHr-->" . $maxCantHrHorario . "<br>";
				}
				
				$pFechaFacturacion = $cualVigencia . "-" . $cualMes . "-" . $d2 ;
				echo $pFechaFacturacion ; 

				?>
				
				<input name="<? echo $d2; ?>regDia<? echo $r; ?>" type="text" class="CajaTexto" id="<? echo $d2; ?>regDia<? echo $r; ?>" onBlur="validaFila(<? echo $r;?>, <? echo $horasAvalidarDia; ?>, <? echo $d2; ?>, <? echo $esDia; ?>, <? echo $horasFactCT_1_2_3; ?>, <? echo $horasFactCT_1o2o11; ?>, <? echo $maxCantHrHorario; ?> )" onKeyPress="return acceptNum(event)" value="<? echo ${$mitxtregDia}; ?>" size="1">
				<? 
				echo $vSemana[$esDia] . "<br>"; 
				echo $vHorasHorario[$esDia] . "<br>"; 
				?>
				</td>
				<? 
				} //cierra for d2
				?>
                <td>
				<?
				$miResumen = "regResumen" . $r;
				?>
				<textarea name="regResumen<? echo $r; ?>" cols="30" rows="4" class="CajaTexto" id="regResumen<? echo $r; ?>"><? echo  ${$miResumen}; ?></textarea></td>
              </tr>
			  <?
			  unset($vHorasHorario);
			  $r=$r+1;
			  }
			  ?>
            </table></td>
        </tr>
      </table>
	  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td align="right" class="TxtTabla"><input name="Submit" type="button" class="Boton" value="Grabar" onClick="validaFormulario()"></td>
        </tr>
      </table>
	  <table width="100%"  border="0" cellspacing="1" cellpadding="0">

  <tr>
    <td align="right" class="TxtTabla">
	<input name="frmErr02" type="hidden" id="frmErr02" value="<? echo $error02; ?>">	<input name="frmErr01" type="hidden" id="frmErr01" value="<? echo $error01; ?>">
    <input name="usuDepartamentoUsu" type="hidden" id="usuDepartamentoUsu" value="<? echo $miDepartamentoUsu; ?>">    <input name="mesHorasOfi" type="hidden" id="mesHorasOfi" value="<? echo $miHorasOficina; ?>">    <input name="usuSalarioUsu" type="hidden" id="usuSalarioUsu" value="<? echo $miSalarioUsu ; ?>">
      <input name="usuCategoria" type="hidden" id="usuCategoria" value="<? echo $miCategoria ; ?>">	  <input name="usuTipoContrato" type="hidden" id="usuTipoContrato" value="<? echo $miTipoContrato ; ?>">
    <input name="totalDiasDinamicos" type="hidden" id="totalDiasDinamicos" value="<? echo $totalDiasMes; ?>">    <input name="hayPlaneacion" type="hidden" id="hayPlaneacion" value="<? echo $hayPlaneacion; ?>">    <input name="cualProyecto" type="hidden" id="cualProyecto" value="<? echo $cualProyecto; ?>">    <input name="cualVigencia" type="hidden" id="cualVigencia" value="<? echo $cualVigencia; ?>">
      <input name="cualMes" type="hidden" id="cualMes" value="<? echo $cualMes; ?>">      
	  <input name="recarga" type="hidden" id="recarga" value="1">
    </td>
  </tr>

</table>  	</td>
  </tr>
  </form>
</table>

</body>
</html>
