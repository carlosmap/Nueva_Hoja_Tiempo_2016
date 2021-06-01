<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>


<?php
session_start();

include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

include "sobre_HTplaneacionProy.php";

include("fncEnviaMailPEAR.php");

//Carga la variable de la vigencia que viene de la página anterior
if (trim($lstVigencia) == "") {
	$lstVigencia = $cualVigencia;
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
$sql04="SELECT nombre,apellidos, usuarios.unidad, 'I' tipoUsuario,retirado, fechaRetiro  FROM usuarios WHERE usuarios.unidad IN 
		(
			select unidad from PlaneacionProyectos 
			where id_proyecto=".$cualProyecto." and id_actividad=".$cualAct."  and vigencia=".$cualVigencia." and esInterno='I'
		) 
		UNION SELECT nombre, apellidos, consecutivo as unidad, 'E' tipoUsuario,NULL,NULL  FROM TrabajadoresExternos WHERE consecutivo IN 
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
$sql07=$sql07." 	AND id_actividad = " . $cualAct . " and vigencia <> ".$cualVigencia;
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


if(trim($recarga) == "2"){

	//Mes de inicio y Mes de finalización
	$elMesMinimo=$minimoMes;
	$elMesMaximo=$maximoMes;

	$cur_usu=mssql_query('select UPPER(nombre) nombre , UPPER(apellidos) apellidos from Usuarios where unidad='.$_SESSION["sesUnidadUsuario"] );
	if($dato_usu=mssql_fetch_array($cur_usu))
		{
			$nom=$dato_usu["nombre"];
			$apelli=$dato_usu["apellidos"];
		}	

	$cur_proy=mssql_query('select nombre,id_proyecto from Proyectos where id_proyecto='.$cualProyecto);
	if($dato_proy=mssql_fetch_array($cur_proy))
	{
		$nom_proy=$dato_proy["nombre"];
		$id_proy=$dato_proy["id_proyecto"];
	}

	$ff=0;
	$cur_proy=mssql_query('select SUM(valorPlaneado) as v_planeado from PlaneacionProyectos where id_proyecto='.$cualProyecto.' and id_actividad='.$cualAct.' --valor total
			union
		select SUM(valorPlaneado)  from PlaneacionProyectos where id_proyecto='.$cualProyecto.' and id_actividad='.$cualAct.' and vigencia='.$cualVigencia.' -- valor planeado vigencia

		 ');
	if($dato_proy=mssql_fetch_array($cur_proy))
	{

		$valor_planeado[$ff]=$dato_proy["v_planeado"];
		$ff++;
	}
	$pTema = '<table width="100%" border="0">
	  <tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	  </tr>
	  <tr class="Estilo2">
		<td width="20%">Asunto:</td>
		<td  >Planeaci&oacute;n de actividades</td>
	  </tr>

	  <tr class="Estilo2">
		<td width="20%">Proyecto:</td>
		<td >['.$id_proy.'] '.$nom_proy.'</td>
	  </tr>

	  <tr class="Estilo2">
		<td width="20%">Actividad:</td>
		<td >['.$macroLoteControl.'] '.$nomLoteControl.' - ['.$macroLoteTrabajo.'] '.$nomLoteTrabajo.' - ['.$macroLoteDiv.'] '.$nomLoteDiv;


	if($nivelActSel==4)
		$pTema = $pTema.' - ['.$macroLoteAct.'] '.$nomLoteAct.'</td>';

	$pTema = $pTema.'
	  </tr>
	  <tr class="Estilo2">
		<td width="20%">Fecha de planeaci&oacute;n:</td>
		<td >'.gmdate ("d").'/'.$vMeses[gmdate ("n")].'/' .gmdate ("Y").' </td>
	  </tr>
	  <tr class="Estilo2">
		<td width="20%">Usuario que realiza la planeaci&oacute;n:</td>
		<td >'.$nom.' '.$apelli.'</td>
	  </tr>
	  <tr class="Estilo2">
		<td width="20%">Valor planeado total:</td>
		<td >$'. number_format($valor_planeado[0], "2", ",", "." ).'</td>
	  </tr>
	  <tr class="Estilo2">
		<td width="20%">valor planeado en el '.$cualVigencia.': </td>
		<td >$';

		if(trim($valor_planeado[1])=="")
			$valor_planeado[1]=$valor_planeado[0];

	$pTema = $pTema.  number_format($valor_planeado[1], "2", ",", "." ).'</td>
	  </tr>
	  <tr>
		<td colspan="2">&nbsp;</td>
	  </tr>';


/*
	$pTema = $pTema . '  <tr>
    	<td colspan="15" class="Estilo2">Los siguientes participantes, han sido planeados en la actividad ['.$macroLoteAct1.'] '.$nomLoteAct1.', del proyecto '.$id_proy.' '.$nom_proy.', para el año '.$cualVigencia.' .</td>
		</tr>';
*/
	$cols=($maximoMes-$minimoMes)+1;

	$pTema = $pTema.'
		<tr><td>&nbsp;</td></tr>

		<tr><td colspan="2">
		<table width="100%"  border="1" cellspacing="1" cellpadding="0">
		<tr class="Estilo2">
			<td colspan="5" >Planeaci&oacute;n a fecha: '.gmdate ("d").'/'.$vMeses[gmdate ("n")].'/' .gmdate ("Y").' </td>
			<td align="center" colspan="'.$cols.'">'.$cualVigencia.'</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>

		  <tr class="Estilo2">
			<td>Unidad</td>
			<td>Nombre</td>
			<td>&nbsp;</td>
			<td>División</td>
			<td>Departamento</td>';



	for ($m=$minimoMes; $m<=$maximoMes; $m++) {		   
			$pTema = $pTema.'<td width="1%">'.$vMeses[$m] .'</td>';
	 }
	$pTema = $pTema.'
			<td>Salario</td>
			<td>Valor planeación</td>
		  </tr>';
	$msgGraba = "";
	$msgNOGraba = "";

	$cursorTran1 = mssql_query(" BEGIN TRANSACTION");
		$sql_del="delete from PlaneacionProyectos where id_proyecto=".$cualProyecto." and id_actividad=".$cualAct."  and vigencia=".$cualVigencia;
		$cur_del=mssql_query($sql_del);

		if(trim($cur_del)=="")
		{
			$error_del="si";
//echo $sql_del." ---- <br>".mssql_get_last_message();
		}
		else
			$error_del="no";


//echo $sql_del." ---- <br>".mssql_get_last_message();
	
	//Aquí se realiza el recorrido de todas las personas (Vertical)
	$s = 1;

	$ind=0;

//	$unidades
	while ($s <= $cantRegistros) {

		//Recoger las variables
		$ellstUnidadP = "lstUnidadP" . $s;
		$elpTipoUsu = "pTipoUsu" . $s;

		//ALMACENA LAS UNIDADES, PARA ENVIARLAS, AL MOMENTO DE CONSULTAR LA SOBREPLANEACION
		$unids[$ind]=${$ellstUnidadP};
		//ALMACENA EL TIPO DE PARTICIPANTE (I/E) ASOCIADO A LAS UNIDADES
		$tipu_usus[$ind]=${$elpTipoUsu};
		$ind++;



		$elchkReplica = "chkReplica" . $s;

		$eltxtHomMes = "txtHomMes" . $s;
		$ellstPartirMes = "lstPartirMes" . $s;
		$eltxtRepite = "txtRepite" . $s;

		
		$eltxtSalario = "txtSalario" . $s;
		$eltxtVlFact = "txtVlFact" . $s;
		$salario_u="txtSalario".$s;
		$valor_fac_u="txtVlFact".$s;
		/*
		echo "Unidad= " . ${$ellstUnidadP} . "<br>";
		echo "elchkReplica= " . ${$elchkReplica} . "<br>";
		echo "eltxtHomMes= " . ${$eltxtHomMes} . "<br>";
		echo "ellstPartirMes= " . ${$ellstPartirMes} . "<br>";
		echo "eltxtRepite= " . ${$eltxtRepite} . "<br>";
		echo "elpTipoUsu= " . ${$elpTipoUsu} . "<br>";
		*/

		//Aqui se realiza la grabación de la dedicación de las personas (Horizontal)
		$lcantHorasMes=0;
		$lCategoriaUsu="";
		$lValPlanUsuMes=0;
		$ban=0;
		$d = $elMesMinimo;
		$division="";
		$departamento="";
		while ($d <= $elMesMaximo) {
			$eltxtPlan = $d . "txtPlan" . $s;
			
			$elHorasLabOfi = "vHorasLabOfi" . $d ;
			
			/*
			echo "mes= " . $d . "<br>";
			echo "HorasLaboralesMes= " . ${$elHorasLabOfi} . "<br>";
			echo "Dedicación= " . ${$eltxtPlan} . "<br>";
			*/
			
			//LLeva a cabo el RT si el campo es diferente de vacio o 0
			if ( (trim(${$eltxtPlan}) != "") AND (${$eltxtPlan} != 0) ) {


				//Calcula la cantidad de horas a partir de la dedicación definida.
				$lcantHorasMes =  ${$elHorasLabOfi} * ${$eltxtPlan} ;
//echo ${$elHorasLabOfi} .'/'. ${$eltxtPlan}.'= '.$lcantHorasMes .'<br>';
				
				//Trae la categoría de la persona acorde si es Interno / Externo
				if ( trim(${$elpTipoUsu}) == 'I') {
					$sqlIn00=" select unidad, id_categoria, UPPER(Usuarios.nombre) nombre, UPPER(Usuarios.apellidos) apellidos, UPPER( Departamentos.nombre) dep, UPPER(Divisiones.nombre ) div from Usuarios
								 inner join Departamentos on Departamentos.id_departamento=Usuarios.id_departamento
								 inner join Divisiones on Divisiones.id_division=Departamentos.id_division
								  where unidad=".${$ellstUnidadP};

				}
				else {
					$sqlIn00="select UPPER(TrabajadoresExternos.nombre) nombre , UPPER(TrabajadoresExternos.apellidos) apellidos , ParticipantesExternos.id_categoria ,TrabajadoresExternos.consecutivo from ParticipantesExternos 
							 inner join TrabajadoresExternos on TrabajadoresExternos.consecutivo=ParticipantesExternos.consecutivo ";
					$sqlIn00=$sqlIn00." where id_proyecto = " . $miProyecto  ;
					$sqlIn00=$sqlIn00." and id_actividad = " .  $miActividad ;

//					$sqlIn00=$sqlIn00." and ParticipantesExternos.estado='A' " ;

					$sqlIn00=$sqlIn00." and ParticipantesExternos.consecutivo = " . ${$ellstUnidadP};
				}
				$cursorSqlIn00 = mssql_query($sqlIn00);
//echo $sqlIn00." *** <br>".mssql_get_last_message()."<br><br>";
				if ($regSqlIn00=mssql_fetch_array($cursorSqlIn00)) {

					$lCategoriaUsu=$regSqlIn00[id_categoria] ;
					$nombre=$regSqlIn00[nombre];
					$apellido=$regSqlIn00[apellidos];

					if ( trim(${$elpTipoUsu}) == 'I')
					{
						$unidad=$regSqlIn00[unidad] ;
						$division=$regSqlIn00[div] ;
						$departamento=$regSqlIn00[dep] ;
					}
					else
						$unidad=$regSqlIn00[consecutivo] ;

				}

				if($ban==0)
				{

					if((trim($division)=="")and(trim($departamento)=="" ))
					{
						$division="&nbsp;";
						$departamento="&nbsp;";
					}

					$pTema = $pTema.'<tr class="Estilo2">
						<td>'.$unidad.'</td>
						<td>'.$apellido.' '.$nombre.'</td>
						<td> ';
							if ( trim(${$elpTipoUsu}) != 'I')
								$pTema = $pTema.'E';
							else
								$pTema = $pTema.'&nbsp;';
			
					$pTema = $pTema.' </td>
						<td>'.$division.'</td>
						<td>'.$departamento.'</td>';
				}					
				//Calcular el valor planeado

//				$lValPlanUsuMes =  ${$ellstUnidadP} * ${$eltxtPlan};
				$lValPlanUsuMes =  ${$eltxtSalario} * ${$eltxtPlan};
	
				//Realiza la grabación de la información en 
				$sqlIn1 = " INSERT INTO PlaneacionProyectos ";
				$sqlIn1 = $sqlIn1 . " (id_proyecto, id_actividad, unidad, vigencia, mes, esInterno,  ";
				$sqlIn1 = $sqlIn1 . " hombresMes, horasMes, id_categoria, valorPlaneado, salarioBase, fechaPlaneacion, unidadPlaneacion,   ";
				$sqlIn1 = $sqlIn1 . " usuarioCrea, fechaCrea )  ";
				$sqlIn1 = $sqlIn1 . " VALUES ( ";
				$sqlIn1 = $sqlIn1 . " " . $miProyecto . ", ";
				$sqlIn1 = $sqlIn1 . " " . $miActividad . ", ";
				$sqlIn1 = $sqlIn1 . " " . ${$ellstUnidadP} . ", ";
				$sqlIn1 = $sqlIn1 . " " . $lstVigencia . ", ";
				$sqlIn1 = $sqlIn1 . " " . $d . ", ";
				$sqlIn1 = $sqlIn1 . " '" . ${$elpTipoUsu} . "', ";			
				$sqlIn1 = $sqlIn1 . " " . ${$eltxtPlan} . ", ";
				$sqlIn1 = $sqlIn1 . " " . $lcantHorasMes . ", ";
				$sqlIn1 = $sqlIn1 . " " . $lCategoriaUsu . ", ";
				$sqlIn1 = $sqlIn1 . " " . $lValPlanUsuMes . ", ";
				$sqlIn1 = $sqlIn1 . " " . ${$eltxtSalario} . ", ";
				$sqlIn1 = $sqlIn1 . " '". gmdate ("n/d/Y") . "', ";
				$sqlIn1 = $sqlIn1 . " '" . $_SESSION["sesUnidadUsuario"] . "', ";
				$sqlIn1 = $sqlIn1 . " '" . $_SESSION["sesUnidadUsuario"] . "', ";
				$sqlIn1 = $sqlIn1 . " '". gmdate ("n/d/Y") . "' ";
				$sqlIn1 = $sqlIn1 . " ) ";
				$cursorIn1 = mssql_query($sqlIn1);
echo "<br><br>".$sqlIn1." *** <br>".mssql_get_last_message()."<br><br>";
				if  (trim($cursorIn1) != "")  {
					//echo "entro eal if 2" . "<br>";
					$msgGraba=$msgGraba."[".${$ellstUnidadP}."] " ;
				}
				else {
					//echo "entro al else " . "<br>";
					$msgNOGraba=$msgNOGraba."[".${$ellstUnidadP}."] " ; 
				}

				//IDENTIFICA, SI ES LA PRIMERA VEZ QUE SE VA A IMPRIMIR EL H/M DEL PRIMER MES PLANEADO, ESTO PARA CADA USUARIO
				if($ban==0)
				{
					$Z=$elMesMinimo;
					$pTema = $pTema.'';
				}

				if($Z==$d)
					$ban=1;

				//IGUALAMOS EL VALOR DE Z AL DEL MES PLANEADO, E IMPRIME ESPACIOS EN BLANCO
				while(($Z<$d) )
				{		   

						$Z++;
						$pTema = $pTema.'<td width="1%">&nbsp;</td>';
						$ban=1;
				 }
		
				if($Z==$d)
				{	
						$pTema = $pTema.'<td width="1%" align="right" >&nbsp;'.${$eltxtPlan}.'</td>';	
						$ultimo_valor_d=$d;	//ALMACENA EL ULTIMO VALOR DE LA POSICION, EN LA QUE SE IMPRIMIO LA INFORMACION DE UN HOMBRE MES
						$Z++;
				}
				
			} //Cierra el if de la validación eltxtPlan
			$d = $d + 1;

		}		//Cierra While $d

//echo "vlaro dddd ".$d."<br>";
		//IGUALAMOS A $d AL MES DOCE, ESTO PARA AJUSTAR EL CORREO, CON TODOS LOS MESES
		while($ultimo_valor_d<$maximoMes)
		{
			$pTema = $pTema.'<td width="1%">&nbsp;</td>';
			$ultimo_valor_d++;
		}


//		$pTema = $pTema.'</tr></table></td>';

		$pTema = $pTema.'<td width="1%" align="right">&nbsp;$'.number_format(${$salario_u}, "2", ",", "." ).'</td>';
		$pTema = $pTema.'<td width="1%" align="right">&nbsp;$'. number_format(${$valor_fac_u}, "2", ",", "." ).'</td></tr>';
		$s = $s + 1;
	} //Cierra While

	$pTema = $pTema.'</table></td></tr></table>';
//echo $pTema;

//////PARA DESCOMENTARIAR ENVIA CORREO DIRECTOR COORDINADOR, ETC. POR EL MOMENTO LO ENVIA A EL CORREO carlosmap
/*
		//consulta la unidad del director y el coordinador de proyecto
		$sql="SELECT id_director,id_coordinador FROM  HojaDeTiempo.dbo.proyectos where id_proyecto = " . $cualProyecto." " ;
		$eCursorMsql=mssql_query($sql);
		$usu_correo= array(); //almacena la unidad de los usuarios a los que se le enviara el correo
		$i=1;
		while($datos_dir_cor=mssql_fetch_array($eCursorMsql))
		{
			$usu_correo[$i]=$datos_dir_cor["id_coordinador"];
			$i++;
			$usu_correo[$i]=$datos_dir_cor["id_director"];
			$i++;
		}

		//consulta la unidad porgramadores y ordenadores de gasto			
//select unidad from HojaDeTiempo.dbo.Programadores where id_proyecto=".$cualProyecto." union
		$sql_pro_orde=" select unidadOrdenador from GestiondeInformacionDigital.dbo.OrdenadorGasto where id_proyecto=".$cualProyecto;
		$cur_pro_orde=mssql_query($sql_pro_orde);
		while($datos_pro_orde=mssql_fetch_array($cur_pro_orde))
		{
			$usu_correo[$i]=$datos_pro_orde["unidad"];
			$i++;
		}			

		$i=0;
		//consulta el correo de los usuarios(director,cordinador,ordenadroes de G, y programadores) asociados al proyecto
		$sql_usu=" select email from HojaDeTiempo.dbo.Usuarios where unidad in(";
		foreach($usu_correo as $unid)
		{
			if($i==0)
			{
				$sql_usu=$sql_usu." ".$unid;		
				$i=1;
			}
			else
				$sql_usu=$sql_usu." ,".$unid;
		}
		$sql_usu=$sql_usu.") and retirado is null";
		$cur_usu=mssql_query($sql_usu);				

		//se envia el correo a el director, cordinador, orenadores de gasto, y programadores del proyecto	
		while($eRegMsql = mssql_fetch_array($cur_usu))
		{		
		   $miMailUsuarioEM = $eRegMsql[email] ;
	
		   //***EnviarMailPEAR	
		   $pPara= trim($miMailUsuarioEM) . "@ingetec.com.co";
	
		   enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);
	
		   //***FIN EnviarMailPEAR
		   $miMailUsuarioEM = "";
	
		}
*/


	//Si los cursores no presentaron problema
	//if  (trim($cursorIn1) != "")  {



$eror_msg="no";
	if  (trim($msgNOGraba) != "")  {
			$eror_msg="si";
//		$cursorTran2 = mssql_query(" ROLLBACK TRANSACTION ");	
//		echo ("<script>alert('Error durante la operación');
// /script>");
	} 
	
	if  ((trim($msgGraba) != "") and (trim($error_del=="no")and (trim($eror_msg)=="no")) ) {
			///////////////////////////**********************************************************PARA QUITAR
				   $miMailUsuarioEM = 'carlosmaguirre'; //$eRegMsql[email] ;	
					$pAsunto='Planeación de proyectos';
				   //***EnviarMailPEAR	
				   $pPara= trim($miMailUsuarioEM) . "@ingetec.com.co";	
				   enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);	
				   //***FIN EnviarMailPEAR
				   $miMailUsuarioEM = "";
			///////////////////////////**********************************************************	

			//LLAMA LA FUNCION, QUE VERIFICA, LA SOBREPLANEACION, DE LOS USUARIOS EN LOS DIFERENTES PROYECTOS
			sobre_planeacion(1,12,$lstVigencia,$unids,$tipu_usus);

			$cursorTran2 = mssql_query(" COMMIT TRANSACTION ");
		echo ("<script>alert('Operación realizada con exito. ');</script>"); 
	} 
	else {
		$cursorTran2 = mssql_query(" ROLLBACK TRANSACTION ");	
		echo ("<script>alert('Error durante la operación');</script>");
	};
	echo ("<script>window.close();MM_openBrWindow('htPlanProyectos03.php?cualProyecto=$miProyecto&lstLC=$fldLoteC&lstLT=$fldLoteT&lstDiv=$fldLoteDiv&opcID=$miActividad&cualAct=$cualAct','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600'); </script>");
}

?>
 <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
<head>
<script language="JavaScript" type="text/JavaScript">
<!--

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
	
	CantCampos=1+(parseFloat(totVar)*parseFloat(document.Form1.cantRegistros.value));
	
	//Ciclo para hacer la réplica de la información
	for (i=totVar;i<=CantCampos;i+=totVar) {
		if (document.Form1.elements[i-1].value != "") {
			totalF = parseFloat(totalF) + parseFloat(document.Form1.elements[i-1].value) ;
		}
		document.Form1.txtTotalPlaneado.value =totalF.toFixed(2);
	}
	
	//Verifica que si se supera que lo planeado + lo que se está planeando supere el valor total del recurso
	if ( (parseFloat(document.Form1.txtTotalPlaneado.value) + parseFloat(document.Form1.fldValorTotalPlaneado.value) ) > parseFloat(document.Form1.fldValorRecurso.value) ) {
		alert("Valor total planeado + Valor que está planeándose supera el valor total asignado al recurso. ("+document.Form1.txtTotalPlaneado.value+" + "+document.Form1.fldValorTotalPlaneado.value+") ** "+document.Form1.fldValorRecurso.value);
		return "1";
	}
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


//alert ("LLegó....");

/*
alert (document.Form1.cantMeses.value);
alert (document.Form1.cantRegistros.value);
alert (document.Form1.recarga.value);

*/



//Cantidad de campos fijos + campos dinámicos
//parseFloat(5) = 5 Campos fijos ANTES de la parte dinámica
//parseFloat(document.Form1.cantMeses.value) = Campos dinámicos
//parseFloat(3) = Campos fijos DESPUES de la parte dinámica
totVar = parseFloat(5) + parseFloat(document.Form1.cantMeses.value) + parseFloat(3);

//alert (totVar);

//Encontrar la cantidad de elementos
CantCampos=1+(parseFloat(totVar)*parseFloat(document.Form1.cantRegistros.value));

for (i=3;i<=CantCampos;i+=totVar) {

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
						
//						alert(horasMesPlan);
						
//						alert(m);
//						window["dd"]="vHorasLabOfi"+1;
//						alert (document.getElementById('dd').value);
						//alert (document.getElementById('vHorasLabOfi'+m).value);
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
							else {
								document.Form1.elements[m].value = '';
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
			alert('Hombres/Mes corresponde a un dato numérico y menor o igual a 1.');
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


function envia2(){ 
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


//alert ("LLegó....");

/*
alert (document.Form1.cantMeses.value);
alert (document.Form1.cantRegistros.value);
alert (document.Form1.recarga.value);

*/

//Cantidad de campos fijos + campos dinámicos
//parseFloat(5) = 5 Campos fijos ANTES de la parte dinámica
//parseFloat(document.Form1.cantMeses.value) = Campos dinámicos
//parseFloat(3) = Campos fijos DESPUES de la parte dinámica
totVar = parseFloat(5) + parseFloat(document.Form1.cantMeses.value) + parseFloat(3);

//alert (totVar);

//Encontrar la cantidad de elementos
CantCampos=1+(parseFloat(totVar)*parseFloat(document.Form1.cantRegistros.value));

//alert (CantCampos);

//Validar que los campos esten marcados o no.
/*
for (i=2;i<=CantCampos;i+=totVar) {
    if (document.Form1.elements[i].checked) 
     alert("Marcado"); 
    else 
     alert("Desmarcado"); 
}
*/

/*

alert (document.getElementById('vHorasLabOfi'+1).value);
alert (document.Form1.vHorasLabOfi2.value);
alert (document.Form1.vHorasLabOfi3.value);
alert (document.Form1.vHorasLabOfi4.value);
alert (document.Form1.vHorasLabOfi12.value);

*/
//Ciclo para hacer la réplica de la información
for (i=3;i<=CantCampos;i+=totVar) {

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
						
//						alert(horasMesPlan);
						
//						alert(m);
//						window["dd"]="vHorasLabOfi"+1;
//						alert (document.getElementById('dd').value);
						//alert (document.getElementById('vHorasLabOfi'+m).value);
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
							else {
								document.Form1.elements[m].value = '';
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
			alert (parseFloat(document.Form1.elements[i].value));
			alert('Hombres/Mes corresponde a un dato numérico y menor o igual a 1.');
		}

	
	}
}

	totVar = parseFloat(5) + parseFloat(document.Form1.cantMeses.value) + parseFloat(3);	
	//Encontrar la cantidad de elementos
	CantCampos=1+(parseFloat(totVar)*parseFloat(document.Form1.cantRegistros.value));
	
	for (i=3;i<=CantCampos;i+=totVar) 
	{
			rMdesde=parseFloat(i)+parseFloat(3);
			rMhasta=rMdesde + parseFloat(document.Form1.cantMeses.value)-parseFloat(1); //Se le resta uno porque incluye desde donde arranca
			for (m=rMdesde; m<=rMhasta; m++) 
			{
				if (parseFloat(document.Form1.elements[m].value)<parseFloat(0.01))
				{
					msg1=msg1+"El hombre/mes asignados, no puede ser inferior a 0.01 ";
//+document.Form1.elements[m].value+"\n"

					v1='n';
				}
			}
									  
	}

	//si el valor
	if((totalizaFac())!=1)
	{

	
	//Si todas las validaciones fueron correctas, el formulario hace submit y permite grabar
	  if ((v1=='s') && (v2=='s') && (v3=='s') && (v4=='s') && (v5=='s') && (v6=='s') && (v7=='s') && (v8=='s') && (v9=='s') && (v10=='s') && (v11=='s') && (v12=='s') && (v13=='s') && (v14=='s') && (v15=='s')) {
			document.Form1.recarga.value="2";
			document.Form1.submit();
		}
		else {
			mensaje = msg1 + msg2 + msg3 + msg4 + msg5 + msg6 + msg7 + msg8 + msg9 + msg10 + msg11 + msg12 + msg13 + msg14 + msg15;
			alert (mensaje);
		}
	}


/*


	
//Valida que el campo Nombre no esté vacio
for (i=1;i<=CantCampos;i+=2) {
	if (document.Form1.elements[i].value == '') {
		v2='n';
		msg2 = 'Nombre es obligatorio. \n'
	}
}

//Valida que el campo Sigla no esté vacio
for (i=2;i<=CantCampos;i+=2) {
	if (document.Form1.elements[i].value == '') {
		v3='n';
		msg3 = 'Sigla es obligatorio. \n'
	}
}



//Si todas las validaciones fueron correctas, el formulario hace submit y permite grabar
	if ((v1=='s') && (v2=='s') && (v3=='s') && (v4=='s') && (v5=='s') && (v6=='s') && (v7=='s') && (v8=='s') && (v9=='s') && (v10=='s') && (v11=='s') && (v12=='s') && (v13=='s') && (v14=='s') && (v15=='s')) {
		document.Form1.recarga.value="2";
		document.Form1.submit();
	}
	else {
		mensaje = msg1 + msg2 + msg3 + msg4 + msg5 + msg6 + msg7 + msg8 + msg9 + msg10 + msg11 + msg12 + msg13 + msg14 + msg15;
		alert (mensaje);
	}
	*/
}
//-->
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
		  <select name="lstVigencia" class="CajaTexto" id="lstVigencia" onChange="document.form1.submit();">
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
          <td width="10%"></td>
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
          <td>Valor Planeaci&oacute;n </td>
          <td>&nbsp;</td>
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
			<?
//echo $reg04["retirado"]." --- ".$reg04["fechaRetiro"];

				if(($reg04["retirado"]==1) and (trim($reg04["fechaRetiro"])!=""))
				{
?>
				   <img src="imagenes/Inactivo.gif" alt=" " title="Retirado de la compa&ntilde;ia" />
<?php
				}
			?>
		 </td>
          <td width="10%">		  
<!--
			<input name="lstUnidadP<? echo $r; ?>" type="hidden"  id="lstUnidadP<? //echo $r; ?>" >
            <input type="text" class="CajaTexto" style='width:200px; ' value="<? //echo "[" . $reg04[unidad] . "] " . ucwords(strtolower($reg04[apellidos])) . ", " . ucwords(strtolower($reg04[nombre])) ;; ?> " >
-->
		  <select name="lstUnidadP<? echo $r; ?>" class="CajaTexto" id="lstUnidadP<? echo $r; ?>" style='width:200px; ' >
            <option value="<? echo $reg04[unidad]; ?>"><? echo "[" . $reg04[unidad] . "] " . ucwords(strtolower($reg04[apellidos])) . ", " . ucwords(strtolower($reg04[nombre])) ;; ?></option>
          </select></td>
          <td width="5%"><input name="chkReplica<? echo $r; ?>" type="checkbox" id="chkReplica<? echo $r; ?>" value="1">
            Replicar<br></td>
          <td width="3%" align="center"><input name="txtHomMes<? echo $r; ?>" type="text" class="CajaTexto" id="txtHomMes<? echo $r; ?>"  size="10" onKeyPress="return acceptNum(event)" ></td>
          <td width="5%" align="center">
		<select name="lstPartirMes<? echo $r; ?>" class="CajaTexto" id="lstPartirMes<? echo $r; ?>">
			<option value="">..:: &nbsp;</option>
		  <? for ($m=$minMesP; $m<=$maxMesP; $m++) { ?>
            <option value="<? echo $m; ?>"><? echo $vMeses[$m]; ?></option>
			<? } // for ?>
          </select></td>
          <td width="3%" align="center"><input name="txtRepite<? echo $r; ?>" type="text" class="CajaTexto" id="txtRepite<? echo $r; ?>"  size="10" onKeyPress="return acceptNum(event)" ></td>
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
				<td width="5%" class="TxtTabla"><input name="<? echo $m; ?>txtPlan<? echo $r; ?>" type="text" class="CajaTexto" id="<? echo $m; ?>txtPlan<? echo $r; ?>" size="10"  onKeyPress="return acceptNum(event)" onBlur="actualizaFac(<? echo $r;?>)" value="<?=$val_planea; ?>" >

	</td>
				<? 		$val_planea="";
					} 
						unset($datos_participante);
			// for ?>
			  </tr>
			</table>
			</td>
          <td width="5%" align="center"><input name="txtSalario<? echo $r; ?>" type="text" class="CajaTexto" id="txtSalario<? echo $r; ?>" value="<? echo $salarioPersona; ?>" size="12" readonly ></td>
          <td><input name="txtVlFact<? echo $r; ?>" type="text" class="CajaTexto" id="txtVlFact<? echo $r; ?>" size="15" readonly>
            <input name="pTipoUsu<? echo $r; ?>" type="hidden" id="pTipoUsu<? echo $r; ?>" value="<? echo $reg04[tipoUsuario]; ?>">			</td>
          <td>
<?
			$cur_estado=mssql_query("select estado from ParticipantesActividad where id_proyecto=".$cualProyecto." and id_actividad=".$cualAct."  and  unidad=" . $reg04[unidad]);
			$datos_estado=mssql_fetch_array($cur_estado);

				if(trim($datos_estado["estado"]) == "A") { ?>
				<img title="Activo" src="img/images/alertaAzul.gif"  width="15" height="13" />
				<?
				}
				if(trim($datos_estado["estado"]) == "I") {
				?>
				<img src="img/images/alertaRojo.gif" title="Inactivo" width="15" height="13" />
				<? }


?>
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
  		    <input name="recarga" type="hidden" id="recarga" value="1">
  		    <input type="button" name="Submit2" class="Boton" value="Calcular" onClick="calcularVal()" >
  		    <input name="Submit" type="button" class="Boton" value="Guardar" onClick="envia2()" ></td>
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
<?
	if(!isset($b))
	{
		$b=1;
		echo "<script type='text/javascript'> calcularVal(); </script> ";
	}
?>

<? mssql_close ($conexion); ?>	
