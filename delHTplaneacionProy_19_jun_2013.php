<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}



function cerrar_ventana()
{
	document.Form1.eliminar.value="1";
	document.Form1.submit();

	window.close();MM_openBrWindow('htPlanProyectos03.php?cualProyecto=<?=$miProyecto ?>&lstLC=<?=$fldLoteC ?>&lstLT=<?=$fldLoteT ?>&lstDiv=<?=$fldLoteDiv ?>&opcID=<?=$miActividad?>','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');
}

//if(document.Form1.eliminar.value==1)

//-->
</script>


<?php
session_start();

include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";
include "delHTplaneacionProy_correo.php";

//Carga la variable de la vigencia que viene de la página anterior
if (trim($lstVigencia) == "") {
	$lstVigencia = $cualVigencia;
}





if(trim($recarga) == "2"){
	$error="no";
	$cursorTran1 = mssql_query(" BEGIN TRANSACTION");
	for($i=1; $i<=$cantRegistros;$i++)
	{
		$ususs= "usu".$i; //ALMACENA EL VALOR DE LOS CHEK BOX SELECCIONADOS
		if(($$ususs==1)and($error=="no"))
		{
			$usu_eliminar="lstUnidadP".$i;
			$tipo_usu="pTipoUsu".$i;
//echo "ingreso: ". $i."  --- ";


				$sql_del="delete from PlaneacionProyectos where id_proyecto=".$cualProyecto." and id_actividad=".$cualAct."  and vigencia=".$cualVigencia." and unidad=".$$usu_eliminar." and esInterno='".$$tipo_usu."'";	

//echo $sql_del."".mssql_get_last_message()."<br><br>";
			$cur_del_planea=mssql_query($sql_del);

			if  (trim($cur_del_planea) == "") 
			{
				$error="si";
			}
		
			$usu_eliminar="";
			$tipo_usu="";
		}
	}

//echo $sql_del." --** ".mssql_get_last_message();
	if  ($error=="no")  {
		$cursorTran2 = mssql_query(" COMMIT TRANSACTION ");
		echo ("<script language='javascript' type='text/javascript'>alert('Operaci\xf3n realizada con exito. '); 	 </script>"); 
	} 
	else {
		$cursorTran2 = mssql_query(" ROLLBACK TRANSACTION ");
		echo ("<script language='javascript' type='text/javascript'>alert('Error en la operaci\xf3n');</script>");
	}

}


//--Trae la información de la Actividad que se seleccionó
$nombreActSel= "";
$nivelActSel= "" ;
$nivelesSupActSel= "" ;
$fechaIniActSel= "" ;
$fechaFinActSel= "" ;
$valorActSel= "" ;
$sql01="SELECT *  ";
$sql01=$sql01." FROM Actividades ";
$sql01=$sql01." WHERE id_proyecto = " . $cualProyecto ;
$sql01=$sql01." AND id_actividad = " . $cualAct ;
$cursor01 = mssql_query($sql01);
if ($reg01=mssql_fetch_array($cursor01)) {
	$nombreActSel= $reg01[nombre] ;
	$nivelActSel= $reg01[nivel] ;
	$nivelesSupActSel= $reg01[nivelesActiv] ;
	
	$fechaIniActSel= $reg01[fecha_inicio] ;
	$fechaFinActSel= $reg01[fecha_fin] ;
	
	$valorActSel= $reg01[valor] ;
	
	//3Abr2013
	//Definir la fecha inicio mínima y final máxima de todas las actividades que hacen parte del proyecto
	$minVigenciaP="";
	$maxVigenciaP="";
	$minMesP = "" ;
	$maxMesP = "" ;
	$cantMesesDibuja="";
	$sql03="SELECT YEAR(MIN(fecha_inicio)) fechaMin, YEAR(MAX(fecha_fin)) fechaMax, MONTH(MIN(fecha_inicio)) mesMin, MONTH(MAX(fecha_fin)) mesMax   ";
	$sql03=$sql03." FROM Actividades ";
	$sql03=$sql03." WHERE id_proyecto = " . $cualProyecto;
	$sql03=$sql03." AND id_actividad = " . $cualAct;
	$cursor03 = mssql_query($sql03);
	if ($reg03=mssql_fetch_array($cursor03)) {
		$minVigenciaP = $reg03[fechaMin] ;
		$maxVigenciaP = $reg03[fechaMax] ;
		$minMesP = $reg03[mesMin] ;
		$maxMesP = $reg03[mesMax] ;

		//Si la fecha de inicio y finalización tienen el mismo año se asume el minimo y máximo mes de la actividad		
		if ( ($lstVigencia == $minVigenciaP) AND ($lstVigencia == $maxVigenciaP) ) {
			//echo "entró al if 1 <br>";
			$minMesP = $minMesP ;
			$maxMesP = $maxMesP ;
		}
		
		//Si el año seleccionado es igual al año de la fecha de inicio pero es menor a la fecha de finalización
		//Se asume el mes de la fecha de inicio y 12 para el mes de finalización
		if ( ($lstVigencia == $minVigenciaP) AND ($lstVigencia < $maxVigenciaP) ) {
			//echo "entró al if 2 <br>";	
			$minMesP = $minMesP ;
			$maxMesP = 12 ;
		}

		//Si el año seleccionado es mayor al año de la fecha de inicio pero es menor a la fecha de finalización
		//Se asume el 1 como mes de inicio y 12 para el mes de finalización
		if ( ($lstVigencia > $minVigenciaP) AND ($lstVigencia < $maxVigenciaP) ) {
			//echo "entró al if 3 <br>";		
			$minMesP = 1 ;
			$maxMesP = 12 ;
		}
		
		//Si el año seleccionado es mayor al año de la fecha de inicio y es igual a la fecha de finalización
		//Se asume el 1 como mes de inicio y y el máximo mes de la fecha de finalización
		if ( ($lstVigencia > $minVigenciaP) AND ($lstVigencia == $maxVigenciaP) ) {
			//echo "entró al if 4 <br>";		
			$minMesP = 1 ;
			$maxMesP = $maxMesP ;
		}

		//Si la vigencia seleccionada es inferior a la fecha mínima del proyecto o superior a la fecha máxima del proyecto Saca un mensaje y cierra la ventana.
		if ( ($lstVigencia < $minVigenciaP) OR ($lstVigencia > $maxVigenciaP) ) {
			//echo "entró al if 5 <br>";		
			echo ("<script>alert('ATENCIÓN. La vigencia se encuentra fuera del rango de fechas [Inicio-Final] de la actividad. No hay nada para planear.');</script>");
			echo ("<script>window.close();</script>");
		}
		
		//Calcula la cantidad de mees a dibujar
		$cantMesesDibuja=($maxMesP-$minMesP) + 1; //Suma 1 porque le falta el mes desde donde dibuja
	}	
}

/*
echo $minMesP . "<br>";
echo $maxMesP . "<br>";
echo $cantMesesDibuja . "<br>";
*/

//Trae todas las actividades superiores a la seleccionada
//LC, LT, Div, Act
$nomLoteControl="";
$nivelLoteControl="";
$macroLoteControl="";
$nomLoteTrabajo="";
$nivelLoteTrabajo="";
$macroLoteTrabajo="";
$nomLoteDiv="";
$nivelLoteDiv="";
$macroLoteDiv="";
$fechaIniLoteDiv="";
$fechaFinLoteDiv="";
$nomLoteAct="";
$nivelLoteAct="";
$macroLoteAct="";
$fechaLoteAct="";
$fechaIniLoteAct="";
$fechaFinLoteAct="";
$sql02="SELECT *  ";
$sql02=$sql02." FROM Actividades ";
$sql02=$sql02." WHERE id_proyecto = " . $cualProyecto ;
$sql02=$sql02." AND id_actividad IN ( " . str_replace("A,", "", str_replace("-", ",", $nivelesSupActSel))  . ") " ;
$cursor02 = mssql_query($sql02);
while ($reg02=mssql_fetch_array($cursor02)) {
	if ($reg02[nivel] == 1) {
		$idLoteControl=$reg02[id_actividad];
		$nomLoteControl=$reg02[nombre];
		$nivelLoteControl=$reg02[nivel];
		$macroLoteControl=$reg02[macroactividad];
	}
	if ($reg02[nivel] == 2) {
		$idLoteTrabajo=$reg02[id_actividad];
		$nomLoteTrabajo=$reg02[nombre];
		$nivelLoteTrabajo=$reg02[nivel];
		$macroLoteTrabajo=$reg02[macroactividad];
	}
	if ($reg02[nivel] == 3) {
		$idLoteDiv=$reg02[id_actividad];
		$nomLoteDiv=$reg02[nombre];
		$nivelLoteDiv=$reg02[nivel];
		$macroLoteDiv=$reg02[macroactividad];
		$fechaIniLoteDiv=$reg02[fecha_inicio];
		$fechaFinLoteDiv=$reg02[fecha_fin];
	}
	if ($reg02[nivel] == 4) {
		$idLoteAct=$reg02[id_actividad];
		$nomLoteAct=$reg02[nombre];
		$nivelLoteAct=$reg02[nivel];
		$macroLoteAct=$reg02[macroactividad];
		$fechaIniLoteAct=$reg02[fecha_inicio];
		$fechaFinLoteAct=$reg02[fecha_fin];
	}
}



//--Trae las personas asociadas a la actividad
//Encargado de actividad, Programadores, Responsables delegados y participantes
$sql04="SELECT nombre,apellidos, usuarios.unidad, 'I' tipoUsuario FROM usuarios WHERE usuarios.unidad IN 
		(
			select unidad from PlaneacionProyectos where id_proyecto=".$cualProyecto." and id_actividad=".$cualAct."  and vigencia=".$cualVigencia." and esInterno='I'
		) 
		UNION SELECT nombre, apellidos, consecutivo as unidad, 'E' tipoUsuario FROM TrabajadoresExternos WHERE consecutivo IN 
		(
		   SELECT unidad FROM PlaneacionProyectos WHERE id_proyecto = ".$cualProyecto." AND id_actividad = ".$cualAct." AND esInterno = 'E' and vigencia=".$cualVigencia."
		) order by apellidos ";
$cursor04 = mssql_query($sql04);

//Define el array de meses a usar en la página
$vMeses= array("","Ene","Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"); 


//Define la cantidad de Horas laborales para el mes
$vHorasOfi=array("");
$sql06="SELECT * ";
$sql06=$sql06." FROM horasydiaslaborales ";
$sql06=$sql06." where vigencia = " . $lstVigencia ;
$sql06=$sql06." order by mes " ;
$cursor06 = mssql_query($sql06);
while ($reg06=mssql_fetch_array($cursor06)) {
	$vHorasOfi[$reg06[mes]]  = $reg06[hOficina] ;
}

//Trae el valor planeado de la actividad seleccionada
$vlrTotalPlaneado=0;
$sql07="SELECT coalesce(SUM(valorPlaneado), 0) vlPlaneado ";
$sql07=$sql07." FROM PlaneacionProyectos ";
$sql07=$sql07." 	WHERE id_proyecto = " . $cualProyecto;
$sql07=$sql07." 	AND id_actividad = "  . $cualAct;
$cursor07 = mssql_query($sql07);
if ($reg07=mssql_fetch_array($cursor07)) {
	$vlrTotalPlaneado=$reg07[vlPlaneado] ;
}




//echo $sql04 . "<br>";
//*************HASTA AQUI

/*
echo $nombreActSel . "<br>";
echo $nivelActSel . "<br>";
echo $nivelesSupActSel . "<br>";
echo str_replace("-", ",", $nivelesSupActSel) . "<br>";
echo str_replace("A,", "", $nivelesSupActSel) . "<br>";
echo str_replace("A,", "", str_replace("-", ",", $nivelesSupActSel)) . "<br>";

echo $cualProyecto . "<br>";
echo $cualVigencia . "<br>";
echo $cualAct . "<br>";

exit;
*/

//Cantidad de registros del formulario
if (trim($pCantReg) == "") {
	$pCantReg = 1;
}



?>

 <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
<head>
<script language="JavaScript" type="text/JavaScript">




var nav4 = window.Event ? true : false;
function acceptNum(evt){
var key = nav4 ? evt.which : evt.keyCode; return (key <= 13 || (key>= 48 && key <= 57) || (key == 46) ); }

function envia1(){ 
	//alert ("Entro a envia 1");
	document.Form1.recarga.value="1";
	document.Form1.submit();
}

function totalizaFac(){ 
var v1,v2,v3, totVar, i, CantCampos, msg1, msg2, msg3, mensaje, mesInicio, mesFin, totalF;
v1='s';
v2='s';
v3='s';
msg1 = '';
msg2 = '';
msg3 = '';
mensaje = '';
totVar = 0;
mesInicio = document.Form1.minimoMes.value;
mesFin = document.Form1.maximoMes.value; 
totalF=0;

	//Total de campos por fila
	//Cantidad de campos fijos + campos dinámicos
	//parseFloat(5) = 5 Campos fijos ANTES de la parte dinámica
	//parseFloat(document.Form1.cantMeses.value) = Campos dinámicos
	//parseFloat(3) = Campos fijos DESPUES de la parte dinámica
	totVar = parseFloat(5) + parseFloat(document.Form1.cantMeses.value) + parseFloat(3);
	
	CantCampos=parseFloat(document.Form1.cantRegistros.value);
	
	//Ciclo para hacer la réplica de la información
	for (i=1;i<=CantCampos;i++ )  {

//alert(document.getElementById("txtVlFact"+i).value+" -*** "+parseFloat(document.Form1.elements[18].value)+" i: "+i+" campos: "+CantCampos+" tota var: "+totVar);
		if (document.getElementById("txtVlFact"+i).value != "") {
			totalF = parseFloat(totalF) + parseFloat(document.getElementById("txtVlFact"+i).value) ;
		}
		document.Form1.txtTotalPlaneado.value = totalF.toFixed(2);
	}
/*	
	//Verifica que si se supera que lo planeado + lo que se está planeando supere el valor total del recurso
	if ( (parseFloat(document.Form1.txtTotalPlaneado.value) + parseFloat(document.Form1.fldValorTotalPlaneado.value) ) > parseFloat(document.Form1.fldValorRecurso.value) ) {
		alert("Valor total planeado + Valor que está planeándose supera el valor total asignado al recurso.");
		return "1";
	}
*/
}

function actualizaFac(fila){ 
var v1,v2,v3, totVar, i, CantCampos, msg1, msg2, msg3, mensaje, mesInicio, mesFin;
v1='s';
v2='s';
v3='s';
msg1 = '';
msg2 = '';
msg3 = '';
mensaje = '';
totVar = 0;
mesInicio = document.Form1.minimoMes.value;
mesFin = document.Form1.maximoMes.value; 

//Total de campos por fila
//Cantidad de campos fijos + campos dinámicos
//parseFloat(5) = 5 Campos fijos ANTES de la parte dinámica
//parseFloat(document.Form1.cantMeses.value) = Campos dinámicos
//parseFloat(3) = Campos fijos DESPUES de la parte dinámica
totVar = parseFloat(5) + parseFloat(document.Form1.cantMeses.value) + parseFloat(3);

//Encontrar el campo salario de la fila que se editó
//Cantidad de campos fijos + campos dinámicos
//parseFloat(1) = 1 Campos fijos ANTES de la parte dinámica
//(parseFloat(totVar) *  parseFloat(fila-1)) Total de registros antes de la fila seleccionada
//(parseFloat(5) + parseFloat(document.Form1.cantMeses.value)) Total de campos hasta el salario desde la fila anterior hasta la fila seleccionada
campoSalario =  parseFloat(1) + (parseFloat(totVar) *  parseFloat(fila-1)) + (parseFloat(5) + parseFloat(document.Form1.cantMeses.value));

//alert (campoSalario);

	rMdesde=parseFloat(1) + (parseFloat(totVar) *  parseFloat(fila-1)) + parseFloat(5);
	rMhasta=rMdesde + parseFloat(document.Form1.cantMeses.value)-parseFloat(1); //Se le resta uno porque incluye desde donde arranca
	mesActual = parseFloat(mesInicio);

	horasPlaneadas=0;
	valorPlaneado=0;
	valorTotalPlaneado=0;
	for (m=rMdesde; m<=rMhasta; m++) {
//		alert (document.Form1.elements[m].value);
		
		//Sólo calcula si la casilla no se encuentra vacia
		if (document.Form1.elements[m].value != "") {
		
			//Solo calcula si el valor ingresado es menor o igual 1 
			if (parseFloat(document.Form1.elements[m].value) <= 1) {			
				horasMesLab = parseFloat(document.getElementById('vHorasLabOfi'+mesActual).value);
				horasMesPlan = parseFloat(document.Form1.elements[m].value) * parseFloat(horasMesLab) ;
				
				valorPlaneado=(parseFloat(horasMesPlan)/parseFloat(horasMesLab)) * document.Form1.elements[campoSalario].value ;
				valorTotalPlaneado=valorTotalPlaneado+valorPlaneado;
			}
			else {
				document.Form1.elements[m].value = "";
				alert("La dedicación no puede ser mayor que 1");
			}
		}
			
		mesActual = parseFloat(mesActual) + parseFloat(1);
		
	} //Cierra for m

	//Asignar el valor calculado
	document.Form1.elements[campoSalario+1].value=valorTotalPlaneado.toFixed(2);
	
	//Actualiza el valor total de la facturación
	totalizaFac();
}

function calcularVal(){ 
var v1,v2,v3, v4,v5,v6, v7,v8,v9, totVar, i, CantCampos, msg1, msg2, msg3, msg4, msg5, msg6, msg7, msg8, msg9, msg10, msg11, msg12, msg13, msg14, msg15, mensaje, mesInicio, mesFin;
v1='s';
v2='s';
v3='s';
v4='s';
v5='s';
v6='s';
v7='s';
v8='s';
v9='s';
v10='s';
v11='s';
v12='s';
v13='s';
v14='s';
v15='s';
msg1 = '';
msg2 = '';
msg3 = '';
msg4 = '';
msg5 = '';
msg6 = '';
msg7 = '';
msg8 = '';
msg9 = '';
msg10 = '';
msg11 = '';
msg12 = '';
msg13 = '';
msg14 = '';
msg15 = '';
mensaje = '';
totVar = 0;
mesInicio = document.Form1.minimoMes.value;
mesFin = document.Form1.maximoMes.value; 


totVar = parseFloat(5) + parseFloat(document.Form1.cantMeses.value) + parseFloat(3);

//alert (totVar);

//Encontrar la cantidad de elementos
CantCampos=1+(parseFloat(totVar)*parseFloat(document.Form1.cantRegistros.value));

for (i=4;i<=CantCampos;i=(i+totVar)+1) {

	campoSalario = parseFloat(i) + parseFloat(2) + parseFloat(document.Form1.cantMeses.value) + parseFloat(1);
	
	//Determina si el campo Hombres/Mes
    if (document.Form1.elements[i].value != "")  {
	
		//Verifica que el valor sea menor o igual a 1 de lo contrario genera un error
		if (parseFloat(document.Form1.elements[i].value) <= 1)  {
//			alert (parseFloat(document.Form1.elements[i].value));
//			alert ('Es menor que 1');
				
			//Solo replica si la casilla de verificación se encuentra activa
			if (document.Form1.elements[i-1].checked) {
	
				//alert(document.Form1.elements[i+1].value);
				//alert(document.Form1.elements[i+2].value);
							
				//Replica en todas las celdas si no hay mes seleccionado y cuantas veces está en blanco
				if ((document.Form1.elements[i+1].value == "") && (document.Form1.elements[i+2].value == "")) {
					//Desde dónde arrancaría el ciclo para replicar todo
					rMdesde=parseFloat(i)+parseFloat(3);
					rMhasta=rMdesde + parseFloat(document.Form1.cantMeses.value)-parseFloat(1); //Se le resta uno porque incluye desde donde arranca
					mesActual = parseFloat(mesInicio);
					
					horasPlaneadas=0;
					valorPlaneado=0;
					valorTotalPlaneado=0;
					for (m=rMdesde; m<=rMhasta; m++) {
						document.Form1.elements[m].value = document.Form1.elements[i].value;
//						alert (mesActual);
//						alert (document.getElementById('vHorasLabOfi'+mesActual).value);
						horasMesLab = parseFloat(document.getElementById('vHorasLabOfi'+mesActual).value);
						horasMesPlan = parseFloat(document.Form1.elements[m].value) * parseFloat(horasMesLab) ;
						
						valorPlaneado=(parseFloat(horasMesPlan)/parseFloat(horasMesLab)) * document.Form1.elements[campoSalario].value ;
						valorTotalPlaneado=valorTotalPlaneado+valorPlaneado;
						
						mesActual = parseFloat(mesActual) + parseFloat(1);

					}
					
					//Busca donde se almacena el campo salario
					//i=Hombres/Mes, 2=A partir de y Cuántas veces, Cantidad de meses que se dibujam + 1= Salario
					//el valor del campola columna Hombres
					campoSalario = parseFloat(i) + parseFloat(2) + parseFloat(document.Form1.cantMeses.value) + parseFloat(1);
//					alert(campoSalario);
//					alert(document.Form1.elements[campoSalario].value);
					
					//Asignar el valor calculado
					document.Form1.elements[campoSalario+1].value=valorTotalPlaneado.toFixed(2);
					
				}
				
				//Replica en las celdas donde el mes es mayor o igual al seleccionado en la lista A partir de (mes)
				if ((document.Form1.elements[i+1].value != "") && (document.Form1.elements[i+2].value == "")) {
					//Desde dónde arrancaría el ciclo para replicar todo
					rMdesde=parseFloat(i)+parseFloat(3);
					rMhasta=rMdesde + parseFloat(document.Form1.cantMeses.value)-parseFloat(1); //Se le resta uno porque incluye desde donde arranca
					
					mesActual = parseFloat(mesInicio);
					horasPlaneadas=0;
					valorPlaneado=0;
					valorTotalPlaneado=0;
					for (m=rMdesde; m<=rMhasta; m++) {
						var parteMes = 	document.Form1.elements[m].name ;
						var numMes = parteMes.split('txtPlan');
						//alert (document.Form1.elements[i+1].value);
						//alert (numMes[0]);
						if (parseFloat(numMes[0]) >= parseFloat(document.Form1.elements[i+1].value) ) {
							document.Form1.elements[m].value = document.Form1.elements[i].value;
							//alert (document.getElementById('vHorasLabOfi'+m).value);
								
							horasMesLab = parseFloat(document.getElementById('vHorasLabOfi'+mesActual).value);
							horasMesPlan = parseFloat(document.Form1.elements[m].value) * parseFloat(horasMesLab) ;
							
							valorPlaneado=(parseFloat(horasMesPlan)/parseFloat(horasMesLab)) * document.Form1.elements[campoSalario].value ;
							valorTotalPlaneado=valorTotalPlaneado+valorPlaneado;
							
							mesActual = parseFloat(mesActual) + parseFloat(1);							
						}
						else {
							document.Form1.elements[m].value = '';
						}
					} // for
					
					//Busca donde se almacena el campo salario
					//i=Hombres/Mes, 2=A partir de y Cuántas veces, Cantidad de meses que se dibujam + 1= Salario
					//el valor del campola columna Hombres
					campoSalario = parseFloat(i) + parseFloat(2) + parseFloat(document.Form1.cantMeses.value) + parseFloat(1);
//					alert(campoSalario);
//					alert(document.Form1.elements[campoSalario].value);
					
					//Asignar el valor calculado

					document.Form1.elements[campoSalario+1].value=valorTotalPlaneado.toFixed(2);
				}
				
				//Replica en las celdas donde el mes es mayor o igual al seleccionado en la lista A partir de (mes)
				//y la cantidad de veces indicada en Cuántas veces
				if ((document.Form1.elements[i+1].value != "") && (document.Form1.elements[i+2].value != "")) {
					//Desde dónde arrancaría el ciclo para replicar todo
					rMdesde=parseFloat(i)+parseFloat(3);
					rMhasta=rMdesde + parseFloat(document.Form1.cantMeses.value)-parseFloat(1); //Se le resta uno porque incluye desde donde arranca
					t=1;
					
					mesActual = parseFloat(mesInicio);
					horasPlaneadas=0;
					valorPlaneado=0;
					valorTotalPlaneado=0;
					for (m=rMdesde; m<=rMhasta; m++) {
						var parteMes = 	document.Form1.elements[m].name ;
						var numMes = parteMes.split('txtPlan');
						//alert (document.Form1.elements[i+1].value);
						//alert (numMes[0]);
						if (parseFloat(numMes[0]) >= parseFloat(document.Form1.elements[i+1].value) ) {
							if (parseFloat(t) <= parseFloat(document.Form1.elements[i+2].value) ) {
								document.Form1.elements[m].value = document.Form1.elements[i].value;
								//alert (document.getElementById('vHorasLabOfi'+m).value);
								t=t+1;

								horasMesLab = parseFloat(document.getElementById('vHorasLabOfi'+mesActual).value);
								horasMesPlan = parseFloat(document.Form1.elements[m].value) * parseFloat(horasMesLab) ;
								
								valorPlaneado=(parseFloat(horasMesPlan)/parseFloat(horasMesLab)) * document.Form1.elements[campoSalario].value ;
								valorTotalPlaneado=valorTotalPlaneado+valorPlaneado;
								
								mesActual = parseFloat(mesActual) + parseFloat(1);							
								
								
							}
						}
						else {
							document.Form1.elements[m].value = '';
						}
					} //for

					//Busca donde se almacena el campo salario
					//i=Hombres/Mes, 2=A partir de y Cuántas veces, Cantidad de meses que se dibujam + 1= Salario
					//el valor del campola columna Hombres
					campoSalario = parseFloat(i) + parseFloat(2) + parseFloat(document.Form1.cantMeses.value) + parseFloat(1);
//					alert(campoSalario);
//					alert(document.Form1.elements[campoSalario].value);
					
					//Asignar el valor calculado
					document.Form1.elements[campoSalario+1].value=valorTotalPlaneado.toFixed(2);
				}
			}
		}		
		else {
//			alert (parseFloat(document.Form1.elements[i].value));
//			alert('Hombres/Mes corresponde a un dato numérico y menor o igual a 1.');
		}

/*
		alert ('i=' + i);
		alert ('rMdesde=' + rMdesde);
		alert ('rMhasta='+rMhasta);
*/		
	
	
	} //If del Replica 
	else {
//		alert("Replica No activo");
		rMdesde=parseFloat(i)+parseFloat(3);
		rMhasta=rMdesde + parseFloat(document.Form1.cantMeses.value)-parseFloat(1); //Se le resta uno porque incluye desde donde arranca
		mesActual = parseFloat(mesInicio);
					
		horasPlaneadas=0;
		valorPlaneado=0;
		valorTotalPlaneado=0;
		for (m=rMdesde; m<=rMhasta; m++) {
//			alert (document.Form1.elements[m].value);
			
			//Sólo calcula si la casilla no se encuentra vacia
			if (document.Form1.elements[m].value != "") {
				horasMesLab = parseFloat(document.getElementById('vHorasLabOfi'+mesActual).value);
				horasMesPlan = parseFloat(document.Form1.elements[m].value) * parseFloat(horasMesLab) ;
				
				valorPlaneado=(parseFloat(horasMesPlan)/parseFloat(horasMesLab)) * document.Form1.elements[campoSalario].value ;
				valorTotalPlaneado=valorTotalPlaneado+valorPlaneado;
			}
				
			mesActual = parseFloat(mesActual) + parseFloat(1);
			
		} //Cierra for m

		//Asignar el valor calculado
		document.Form1.elements[campoSalario+1].value=valorTotalPlaneado.toFixed(2);
	}
}

	//Actualiza el valor total de la facturación
	totalizaFac();


} //fin funcion CalculaVal

function sele_todos()
{

		var cantUsu = parseInt(document.Form1.cantRegistros.value);
		var expr1 = '';
//alert(document.Form1.todos.checked);
		for(var i = 1; i <= cantUsu; i++)
		{
	
			if(document.Form1.todos.checked== true)
			{	
				document.getElementById('usu' + i).checked=true;
			}
			else
				document.getElementById('usu' + i).checked=false;
		}
	
}
</script>
<title>.:: Planeaci&oacute;n de Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript" src="calendar.js"></script>
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<form action="" method="post"  name="Form1">

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Planeaci&oacute;n de recursos </td>
  </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><img src="../images/Pixel.gif" width="4" height="2"></td>
        </tr>
      </table>      
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td width="20%" class="TituloTabla">Lote de control </td>
          <td class="TxtTabla">
		  <?
		  echo "<B>" . " [" . $macroLoteControl . "] " .  strtoupper($nomLoteControl) . "</B>" ;
		  ?>
		  </td>
        </tr>
        <tr>
          <td width="20%" class="TituloTabla">Lote de trabajo </td>
          <td class="TxtTabla"><?
		  echo " [" . $macroLoteTrabajo . "] " .  strtoupper($nomLoteTrabajo) ;
		  ?></td>
        </tr>
        <tr>
          <td width="20%" class="TituloTabla">Divisi&oacute;n </td>
          <td class="TxtTabla"><?
		  echo " [" . $macroLoteDiv . "] " .  strtoupper($nomLoteDiv) ;
		  ?>
            <br>
		  <?
		  if ( (trim($fechaIniLoteDiv) != "" ) AND (trim($fechaFinLoteDiv) != "" )) {
			echo "FI [" . date("M d Y ", strtotime($fechaIniLoteDiv)) . "] - FF [" . date("M d Y ", strtotime($fechaFinLoteDiv)) . "] "; 
		  }
		  ?>
		  </td>
        </tr>
<?php
		if((trim($nomLoteAct)!="")and(trim($macroLoteAct)!="" ))
		{
?>
        <tr>
          <td width="20%" class="TituloTabla">Actividad</td>
          <td class="TxtTabla"><?
		  echo " [" . $macroLoteAct . "] " .  strtoupper($nomLoteAct) ;
		  ?>
		    <br>
		    <?
		  	if ( (trim($fechaIniLoteAct) != "" ) AND (trim($fechaFinLoteAct) != "" )) {
				echo "FI [" . date("M d Y ", strtotime($fechaIniLoteAct)) . "] - FF [" . date("M d Y ", strtotime($fechaFinLoteAct)) . "] "; 
			}
		  ?>
		  </td>
        </tr>
<?php
		}
?>
        <tr>
          <td class="TituloTabla">Valor del recurso</td>
          <td class="TxtTabla"><strong>$ <? echo number_format($valorActSel, "2", ",", ".") ?> </strong></td>
        </tr>
        <tr>
          <td class="TituloTabla">Valor total planeado</td>
          <td class="TxtTabla">$ <? echo number_format($vlrTotalPlaneado, "2", ",", ".") ?> </td>
        </tr>
        <tr>
          <td class="TituloTabla">Vigencia</td>
          <td class="TxtTabla">
		  <select name="lstVigencia" class="CajaTexto" id="lstVigencia" onChange="document.form1.submit();" disabled >
		<? 
		for ($k=$minVigenciaP; $k<=$maxVigenciaP; $k++) { 
			if ($lstVigencia == $k) {
				$selVig = "selected";
			}
			else {
				$selVig = "";
			}
		?>
          <option value="<? echo $k; ?>" <? echo $selVig; ?> ><? echo $k; ?></option>
		<? } ?>
        </select>
		  </td>
        </tr>
      </table>
	  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td height="5" class="TituloUsuario"> </td>
        </tr>
      </table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td class="TituloTabla2">PLANEACI&Oacute;N DE RECURSOS </td>
        </tr>
      </table>
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr class="TituloTabla2">
          <td width="10%">Participantes</td>
          <td width="5%">Acci&oacute;n</td>
          <td width="3%">Hombres / Mes </td>
          <td width="5%">A partir de<br>(mes) </td>
          <td width="3%">Cu&aacute;ntas veces</td>
          <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td align="center" class="TituloTabla2"><? echo $lstVigencia; ?></td>
            </tr>
          </table>
		  <table width="100%"  border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
		  <tr class="TituloTabla2">
		  <? for ($m=$minMesP; $m<=$maxMesP; $m++) {		   ?>
			<td width="5%"><? echo $vMeses[$m] . "<br>" . $vHorasOfi[$m]; ?></td>
			<? } // for ?>
		  </tr>
		</table>
</td>
          <td width="5%" align="center">Salario</td>
          <td>Valor Planeaci&oacute;n</td>
          <td><p>Seleccionar todos 
              <input type="checkbox" name="todos" id="todos"  onClick="sele_todos();"  >
            </p></td>
        </tr>
		<? 	
		$r = 1;
		while ($reg04=mssql_fetch_array($cursor04)) { 	

			$datos_participante; //ALMACENA EL MES Y EL HOMBRE MES PLANEADO PARA COADA PARTICIPANTE
			$fila=0; $col=0;			
			//Salario de cada persona
			$salarioPersona = 0;
			//Trae el salario de la persona segun se trate de persona Interna o externa
			if (trim($reg04[tipoUsuario]) == 'I') {
				//Salario persona Interna
				$sql05="SELECT * ";
				$sql05=$sql05 . " FROM usuariosSalario " ;
				$sql05=$sql05 . " WHERE unidad = " . $reg04[unidad] ;
				$sql05=$sql05 . " AND fecha = (" ;
				$sql05=$sql05 . " 	SELECT MAX(fecha) " ;
				$sql05=$sql05 . "  	FROM usuariosSalario " ;
				$sql05=$sql05 . " 	WHERE unidad = " . $reg04[unidad] ;
				$sql05=$sql05 . " ) " ;
				$cursor05 = mssql_query($sql05);
				if ($reg05=mssql_fetch_array($cursor05)) {
					$salarioPersona = $reg05[salario];
				}
	

				//CONSULTA LA INFORMACION DE LA PLANEACIÓN 
				$sql051="select * from PlaneacionProyectos where id_proyecto=".$cualProyecto." and id_actividad=".$cualAct."  and vigencia=".$lstVigencia." and unidad=". $reg04[unidad]. " order by (mes)" ;
				$cursor051 = mssql_query($sql051);
//echo $sql051."<br>",mssql_get_last_message();
				while ($reg051=mssql_fetch_array($cursor051)) {
					$datos_participante[$fila][0]= $reg051[mes];
					$datos_participante[$fila][1]= $reg051[hombresMes];
//echo "<br><br>".$datos_participante[$fila][0]." --- ".$datos_participante[$fila][1]."<br>";
					$fila++;
				}
			}	
			else {
				//Salario de la persona externa
				$sql05="SELECT * ";
				$sql05=$sql05 . " FROM ParticipantesExternos " ;
				$sql05=$sql05." WHERE id_proyecto = " . $cualProyecto;
				$sql05=$sql05." AND id_actividad = "  . $cualAct;
				$sql05=$sql05 . " AND consecutivo = " . $reg04[unidad] ;
				$cursor05 = mssql_query($sql05);
				if ($reg05=mssql_fetch_array($cursor05)) {
					$salarioPersona = $reg05[salario];
				}

				//CONSULTA LA INFORMACION DE LA PLANEACIÓN 
				$sql051="select * from PlaneacionProyectos where id_proyecto=".$cualProyecto." and id_actividad=".$cualAct."  and vigencia=".$lstVigencia." and unidad=". $reg04[unidad]. " order by (mes)" ;
				$cursor051 = mssql_query($sql051);
//echo $sql051."<br>",mssql_get_last_message();
				while ($reg051=mssql_fetch_array($cursor051)) {
					$datos_participante[$fila][0]= $reg051[mes];
					$datos_participante[$fila][1]= $reg051[hombresMes];
//echo "<br><br>".$datos_participante[$fila][0]." --- ".$datos_participante[$fila][1]."<br>";
					$fila++;
				}
			}	

		
		?>
        <tr class="TxtTabla">
          <td width="10%">		  
<!--
			<input name="lstUnidadP<? echo $r; ?>" type="hidden"  id="lstUnidadP<? //echo $r; ?>" >
            <input type="text" class="CajaTexto" style='width:200px; ' value="<? //echo "[" . $reg04[unidad] . "] " . ucwords(strtolower($reg04[apellidos])) . ", " . ucwords(strtolower($reg04[nombre])) ;; ?> " >
-->
		  <select name="lstUnidadP<? echo $r; ?>" class="CajaTexto" id="lstUnidadP<? echo $r; ?>" style='width:200px; '   >
            <option value="<? echo $reg04[unidad]; ?>"><? echo "[" . $reg04[unidad] . "] " . ucwords(strtolower($reg04[apellidos])) . ", " . ucwords(strtolower($reg04[nombre])) ;; ?></option>
          </select></td>
          <td width="5%"><input  disabled name="chkReplica<? echo $r; ?>" type="checkbox" id="chkReplica<? echo $r; ?>" value="1">
            Replicar<br></td>
          <td width="3%" align="center"><input readonly name="txtHomMes<? echo $r; ?>" type="text" class="CajaTexto" id="txtHomMes<? echo $r; ?>"  size="10" onKeyPress="return acceptNum(event)" ></td>
          <td width="5%" align="center">
		<select name="lstPartirMes<? echo $r; ?>" class="CajaTexto" id="lstPartirMes<? echo $r; ?>" disabled >
			<option value="">..:: &nbsp;</option>
		  <? for ($m=$minMesP; $m<=$maxMesP; $m++) { ?>
            <option value="<? echo $m; ?>"><? echo $vMeses[$m]; ?></option>
			<? } // for ?>
          </select></td>
          <td width="3%" align="center"><input readonly name="txtRepite<? echo $r; ?>" type="text" class="CajaTexto" id="txtRepite<? echo $r; ?>"  size="10" onKeyPress="return acceptNum(event)" ></td>
          <td>
			<table width="100%"  border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
			  <tr class="TituloTabla2">
			  <?
				$f=0; 
				for ($m=$minMesP; $m<=$maxMesP; $m++) { 

?>
					<?
						if($datos_participante[$f][0]==$m)
						{	
							//IMPRIME EL VLARO DEL HOMBRE MES, PLANEADO PARA EL MES 
							$val_planea= $datos_participante[$f][1];
							$f++;
						}

					 ?>
				<td width="5%" class="TxtTabla"><input readonly name="<? echo $m; ?>txtPlan<? echo $r; ?>" type="text" class="CajaTexto" id="<? echo $m; ?>txtPlan<? echo $r; ?>" size="10" value="<?=$val_planea; ?>" >

	</td>
				<? 		$val_planea="";
					} 
						unset($datos_participante);
			// for ?>
			  </tr>
			</table>
			</td>
          <td width="5%" align="center"><input  name="txtSalario<? echo $r; ?>" type="text" class="CajaTexto" id="txtSalario<? echo $r; ?>" value="<? echo $salarioPersona; ?>" size="12" readonly ></td>
          <td><input name="txtVlFact<? echo $r; ?>" type="text" class="CajaTexto" id="txtVlFact<? echo $r; ?>" size="15" readonly>
            <input name="pTipoUsu<? echo $r; ?>" type="hidden" id="pTipoUsu<? echo $r; ?>" value="<? echo $reg04[tipoUsuario]; ?>">			</td>
          <td align="center">

			<input type="checkbox" name="usu<? echo $r; ?>" id="usu<? echo $r; ?>" value="1"  >

<!--
			<img width="14" height="13" border="0" onclick="envia2(<? //echo $reg04[unidad]; ?>,'<? //echo ucwords(strtolower($reg04[apellidos])) . " " . ucwords(strtolower($reg04[nombre])) ; ?>','<? // echo $reg04[tipoUsuario]; ?>')" src="img/images/Del.gif" title="Eliminar planeaci&oacute;n" >
-->

		</td>

        </tr>
		<? 
		$r = $r + 1;
		} // Cierra While cursor04 ?>
        <tr>
          <td colspan="7" class="TituloTabla2">TOTAL</td>
          <td class="TxtTabla"><input name="txtTotalPlaneado" type="text" class="CajaTexto" id="txtTotalPlaneado" size="15" onBlur="totalizaFac()" readonly ></td>
          <td class="TxtTabla">&nbsp;</td>
        </tr>
      </table>
<table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr>
            <td  class="TituloTabla2"><b> Nota: Solo se puede eliminar la planeaci&oacute;n que no tenga facturaci&oacute;n asociada. </b> </td>
        </tr>
        <tr>
		
          <td align="right" class="TxtTabla">
<input name="fldLoteDiv" type="hidden" id="fldLoteDiv" value="<? echo $idLoteDiv; ?>">
<input name="fldLoteT" type="hidden" id="fldLoteT" value="<? echo $idLoteTrabajo; ?>">		    
<input name="fldLoteC" type="hidden" id="fldLoteC" value="<? echo $idLoteControl; ?>">
		    <input name="fldValorRecurso" type="hidden" id="fldValorRecurso" value="<? echo $valorActSel; ?>">
		    <input name="fldValorTotalPlaneado" type="hidden" id="fldValorTotalPlaneado" value="<? echo $vlrTotalPlaneado; ?>">
		    <input name="miProyecto" type="hidden" id="miProyecto" value="<? echo $cualProyecto; ?>">
		    <input name="miActividad" type="hidden" id="miActividad" value="<? echo $cualAct; ?>">
		  <? 
		  //Crea el vector de variables de las horas laborales
		  for ($miHL=1; $miHL<=12; $miHL++) {
		  ?>
  		    <input name="vHorasLabOfi<? echo $miHL; ?>" type="hidden" id="vHorasLabOfi<? echo $miHL; ?>" value="<? echo $vHorasOfi[$miHL]; ?>">
		  <? } ?>	  		  
		    <input name="minimoMes" type="hidden" id="minimoMes" value="<? echo $minMesP; ?>">
		    <input name="maximoMes" type="hidden" id="maximoMes" value="<? echo $maxMesP; ?>">
		    <input name="cantMeses" type="hidden" id="cantMeses" value="<? echo $cantMesesDibuja ; ?>">
  		    <input name="cantRegistros" type="hidden" id="cantRegistros" value="<? echo ($r - 1)  ; ?>">

  		    <input name="tipo_usu" type="hidden" id="tipo_usu" value="">
  		    <input name="usu_eliminar" type="hidden" id="usu_eliminar" value="">

  		    <input name="recarga" type="hidden" id="recarga" value="1">
  		    <input name="eliminar" type="hidden" id="eliminar" value="0">

  		    <input name="Eliminar planeación" type="button" class="Boton" id="Cerrar ventana" onClick=" envia2(); " value="Eliminar planeaci&oacute;n">
  		    <input name="Cerrar ventana" type="button" class="Boton" id="Cerrar ventana" onClick=" cerrar_ventana()" value="Cerrar ventana"></td>

        </tr>
      </table>
      </td>
  </tr>
</table>

	     <table width="100%"  border="0">
           <tr>
             <td height="5" class="TituloTabla"> </td>
           </tr>
         </table>
	     </td>
         </tr>
         </table>
</form> 
</body>
</html>
<script type='text/javascript'> 

function envia2()
{

		var cantUsu = parseInt(document.Form1.cantRegistros.value);
		var expr1 = '';
		var cantSeleccionados = 0;
		var mensaje="";
		for(var i = 1; i <= cantUsu; i++){

			expr1 = 'document.Form1.usu' + i + '.checked';
//			alert(eval(expr1));
//			if((document.Form1.usu1.checked ))
			if(eval(expr1) == true)
			{
				cantSeleccionados++;
			}
		}
		if(cantSeleccionados == 0){
			mensaje = mensaje + 'Seleccione al menos un participante a eliminar. \n';
			alert(mensaje);
		}
		else
		{
			//Si todas las validaciones fueron correctas, el formulario hace submit y permite grabar
			  if (confirm("Desea eliminar la planeaci\xf3n para los participantes seleccionados") )
				{
					document.Form1.recarga.value="2";
					document.Form1.submit();
				}	
		}
		



}

</script>
<?
	if(!isset($b))
	{
		$b=1;
		echo "<script type='text/javascript'> calcularVal(); </script> ";
	}
?>

<? mssql_close ($conexion); ?>	
