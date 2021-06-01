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
$sql04=$sql04." 	AND A.nivel IN (3, 4) ";
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



//Trae el cargo_defecto y los cargos_adicionales del proyeecto seleccionado	
$sql11="select id_proyecto, cargo_defecto cargos  " ;
$sql11=$sql11." from proyectos where id_proyecto = " . $cualProyecto ;
$sql11=$sql11." union " ;
$sql11=$sql11." select id_proyecto, cargos_adicionales cargos  " ;
$sql11=$sql11." from cargos where id_proyecto =" . $cualProyecto ;

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

	if (trim($mensajeError) != "") {
		echo ("<script>alert('".$mensajeError."');</script>");
	}
	if (trim($mensajeErrorBD) != "") {
		echo ("<script>alert('".$mensajeErrorBD."');</script>");
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
		$elregDia = "regDia" . $s;
		$elregResumen = "regResumen" . $s;

		
		echo "Actividad= " . ${$ellstActiv} . "<br>";
		echo "Horario= " . ${$ellstHorario} . "<br>";
		echo "CT= " . ${$ellstClaseT} . "<br>";
		echo "Localiza= " . ${$ellstLocaliza} . "<br>";
		echo "dia = " . ${$elregDia} . "<br>";
		echo "Resumen= " . ${$elregResumen} . "<br>";

		$rD = 1;
		while ($rD <= $totalDiasDinamicos) {
			$elregDia = $rD . "regDia" . $s;
			
			echo $rD ."= " . ${$elregDia} . "<br>";

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
}


function validaFila(fila, horasValidaDia, numDeDia){ 
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
	//Se resta 3 para que me encuentre el valor de la lista Horario
	campoHorario=parseFloat(rDdesde) - parseFloat(4);
//	alert (campoHorario);
//	alert (document.Form1.elements[campoHorario].value);
	if (document.Form1.elements[campoHorario].value == "") {
		alert("Se requiere que seleccione el Horario para validar las horas ingresadas");
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
					alert("El valor a facturas debe ser mayor que 0");
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
			} //cierra el if de comparación con 0
		} //Cierra el if de comparación contra vacio
	} //Cierra if campoHorario
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
var v1,v2, v3, v4, v5, v6, i, CantCampos, msg1, msg2, msg3, msg4, msg5, msg6, mensaje;
v1='s';
v2='s';
v3='s';
v4='s';
v5='s';
v6='s';
msg1 = '';
msg2 = '';
msg3 = '';
msg4 = '';
msg5 = '';
msg6 = '';
mensaje = '';

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
	for (i=numPrimerResumen;i<=CantCampos;i+=totalCamposDinamicos) {
		if (document.Form1.elements[i].value == '') {
			v6='n';
			msg6 = 'Resumen es un campo obligatorio. \n'
		}
	}

//Si todas las validaciones fueron correctas, el formulario hace submit y permite grabar
	if ( (v1=='s') && (v2=='s') && (v3=='s') && (v4=='s') && (v5=='s') && (v6=='s') ) {
		document.Form1.recarga.value="2";
		document.Form1.submit();
	}
	else {
		mensaje = msg1 + msg2 + msg3 + msg4 + msg5 + msg6;
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
						//Recoge las variables para poder dejar recargado el formulario
						if (${$milstClaseT} == $reg06['clase_tiempo'] ) {
							$sellstClaseT="selected";
						}
						else {
							$sellstClaseT="";
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
				

				?>
				<input name="<? echo $d2; ?>regDia<? echo $r; ?>" type="text" class="CajaTexto" id="<? echo $d2; ?>regDia<? echo $r; ?>" onBlur="validaFila(<? echo $r;?>, <? echo $horasAvalidarDia; ?>, <? echo $d2; ?>)" onKeyPress="return acceptNum(event)" value="<? echo ${$mitxtregDia}; ?>" size="1">
				
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
    <td align="right" class="TxtTabla"><input name="totalDiasDinamicos" type="hidden" id="totalDiasDinamicos" value="<? echo $totalDiasMes; ?>">    <input name="hayPlaneacion" type="hidden" id="hayPlaneacion" value="<? echo $hayPlaneacion; ?>">    <input name="cualProyecto" type="hidden" id="cualProyecto" value="<? echo $cualProyecto; ?>">    <input name="cualVigencia" type="hidden" id="cualVigencia" value="<? echo $cualVigencia; ?>">
      <input name="cualMes" type="hidden" id="cualMes" value="<? echo $cualMes;; ?>">      
	  <input name="recarga" type="hidden" id="recarga" value="1">
    </td>
  </tr>

</table>  	</td>
  </tr>
  </form>
</table>

</body>
</html>
