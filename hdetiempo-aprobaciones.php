<?
	session_start();
	
	header ("Cache-Control: no-cache, must-revalidate");
	header ("Pragma: no-cache");
	$nombrecomputador="sqlservidor";
	include "funciones.php";
	global $suma;
	define('COLORNORMAL','#FFFFCC');//Pinta donde se escriben las horas
	define('COLORFINSEMANA','#ff0000');
	define('COLORFINMES','#FF0066');
	define('COLORFESTIVO','#7c8d72');
	
	function cambiaDia($dd){
	switch ($dd) {
		case "01":
			$dd=1;
			break;
		case "02":
			$dd=2;
			break;
		case "03":
			$dd=3;
			break;
		case "04":
			$dd=4;
			break;
		case "05":
			$dd=5;
			break;
		case "06":
			$dd=6;
			break;
		case "07":
			$dd=7;
			break;
		case "08":
			$dd=8;
			break;
		case "09":
			$dd=9;
			break;	
		}
		return $dd;
	}
	
		
function consultaADP($lg,$fch, $crg){

	include "validaUsrBd.php";
	$sql = "SELECT adp FROM Adp
			WHERE (unidad = $lg) AND (fecha BETWEEN $fch) AND (cargo = '$crg')";
	
	$ap3 = mssql_query($sql);
	if(mssql_num_rows($ap3)>0){
		$reg1 = mssql_fetch_array($ap3);
		$adpUsr = $reg1[adp];
		return $adpUsr;
	}else{
		return -1;
	}
}
	
function consulta($arr, $sql){
	$suma=0;
	include "validaUsrBd.php";
	$ap3 = mssql_query($sql);
	//if(mssql_num_rows($ap3)>0){
		while($reg1 = mssql_fetch_array($ap3)){
			$fecha = $reg1[fecha];
			$fch = explode(" ",$fecha);
			$dia =$fch[1];
			$dia=cambiaDia($dia);
			$arr[$dia+1] = $reg1[horas_registradas];
			$suma = $suma+$reg1[horas_registradas];
		}
		$arr[33] = $suma;
	return $arr;
	//}
	
}

function sumarArreglos($arr2){
	$res=0;
	for($i=2;$i<=32;$i++){
		$res = $res + $arr2[$i];
	}
	return $res;
}
	/*COMENTARIOS*/
	/*El algoritmo funciona de la siguiente manera:
	Las consultas a SQL Server siempre se regresan ordenadas para garantizar que un codigo de proyecto
	no se vuelva a encontrar mas adelante, asi se logra que se imprima un codigo en una sola linea, no
	importa que se encuentren dos registros del mismo en diferentes fechas; se compara que no cambien
	tipo de tiempo y codigo, cuando cambian se imprime el renglon. De igual forma funciona la impresion de
	los dias no laborados, se ordenan los registros devueltos por el tipo de tiempo no laborado y tan pronto cambie
	este tipo de tiempo, se imprime el renglon y se totaliza el arreglo, el nombre del tipo de tiempo se almacena
	antes de cambiar de registro.
	
	Permite visualizar la hoja de otras fechas  mediante decisión documentada mas adelante
	
	Gonzalo
	*/
	
	//El login es la unidad del usuario
		
	//Viene de aprobaciones
		$MiUnidad=$uniUsr;
	
	//Verifica que el usuario existe en la base de datos contrastandolo con la unidad
	include "validaUsrBd.php";
	$sql="SELECT Usuarios.nombre as nombre, Usuarios.apellidos as apelli, Categorias.nombre as categoria
		FROM Usuarios INNER JOIN Categorias ON Usuarios.id_categoria = Categorias.id_categoria
		WHERE     (Usuarios.unidad = '$MiUnidad')";
	if ($res=mssql_query($sql)) {
		$fil=mssql_fetch_array($res);
		$categoria=$fil[categoria];
		$nomb=$fil[nombre];
		$apel=$fil[apelli];
	} else {
		alert("Usuario no registrado");
		exit();
	}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
<title>Hoja de tiempo</title>
	<link rel="stylesheet" type="text/css" href="estilo.css">
	<SCRIPT LANGUAGE="JavaScript">

function ShowButton(objName, ImageName) {
	objName.src=ImageName
}

function limpiarSi(){
	document.reporte.si.checked=false	
}
function limpiarNo(){
	document.reporte.no.checked=false	
}

function PreloadImages() {
  if(document.images)
    { if (!document.tmpImages)
         document.tmpImages=new Array();
      with(document) {
       var
          i,j=tmpImages.length,
          a=PreloadImages.arguments;

       for(i=0; i<a.length; i++)
          if (a[i].indexOf("#")!=0) {
             tmpImages[j]=new Image;
             tmpImages[j++].src=a[i];
          }
      }
    }
}

</SCRIPT>

</head>
<style>
.texto {
	font-family:arial;
	font-size:12px;
}

.titulo {
	font-family:arial;
	font-size:12px;
	text-decoration:underline;
}

.lista {
	font-family:arial;
	font-size:12px;
	list-style-type:circle;
	display:none;
}
</style>
<body>



<form action="hdetiempo-aprobaciones.php" name=reporte method="post">

<?
	/***************llena un arreglo con la cabecera de la hoja de tiempo **************/
	$cabeceras[38];
	$cabeceras[0]="CODIGO";
	$cabeceras[1]="CT";
	$j=2;
	for($i=1;$i<=31;$i++){
		$cabeceras[$j]=$i;
		$j++;
	}
	$cabeceras[33]="TOTAL";
	$cabeceras[34]="VoBo";
	$cabeceras[35]="RESUMEN";
	
	/***************Inicializa arreglos arreglo con el final de la hoja de tiempo********************/
	
	$MiMes=($verhoja=="Consultar"?$Flmes:date("m",time()));
	$MiAnno=($verhoja=="Consultar"?$Flano:date("Y",time()));
	$numdias=date("t", mktime(0,0,0,$MiMes,1,$MiAnno));
	$cadfecha="'$MiMes/1/$MiAnno' and '$MiMes/$numdias/$MiAnno'";
	//$cadFechIni = "$MiMes/1/$MiAnno";
	//Consulta las vacaciones

	$vacaciones[38];
	$vacaciones[0]="VACACIONES";
	$vacaciones[1]="1";
	for($i=2;$i<=38;$i++){
		$vacaciones[$i]=" ";
	}
	
		$vacaciones[35]="ADP-VC/";

	//*******************
	$enfermedad[38];
	$enfermedad[0]="ENFERMEDAD";
	$enfermedad[1]="1";
	for($i=2;$i<=38;$i++){
		$enfermedad[$i]=" ";
	}
	
		$enfermedad[35]="ADP-INC/";

	//*********************
	
	$acciddetrabajo[38];
	$acciddetrabajo[0]="ACCID.TRABAJ";
	$acciddetrabajo[1]="1";
	for($i=2;$i<=38;$i++){
			$acciddetrabajo[$i]=" ";
	}
	
		$acciddetrabajo[35]="ADP-INC/";

	$permisospacto[38];
	$permisospacto[0]="PERM PACTO";
	$permisospacto[1]="1";
	for($i=2;$i<=38;$i++){
		$permisospacto[$i]=" ";
	}
	

		$permisospacto[35]="ADP-PR/";

	
	$licencias[38];
	$licencias[0]="LICENCIAS";
	$licencias[1]="1";
	for($i=2;$i<=38;$i++){
			$licencias[$i]=" ";
	}
	
	$licencias[35]="ADP-LC/";
	
	
		
	$sanciones[38];
	$sanciones[0]="SANCIONES";
	$sanciones[1]="1";
	for($i=2;$i<=38;$i++){
			$sanciones[$i]=" ";
	}
	
	

		$sanciones[35]="ADP-SD/";

	
	
	$ausencias[38];
	$ausencias[0]="AUSENCIAS";
	$ausencias[1]="1";
	for($i=2;$i<=38;$i++){
			$ausencias[$i]=" ";
	}
	
	
	
	$total[38];
	$total[0]="TOTAL";
	$total[1]=" ";
	for($i=2;$i<=38;$i++){
			$total[$i]="0";
	}
	
	$primas[37];
	$primas[0]="<center>CODIGO</center>";
	$primas[1]="<center>V</center>";
	$primas[2]="DIAS DE VIÁTICOS, PRIMA DE LOCALIZACION, AUXILIO DE TRASLADO O AUXILIO ALIMENTACION";
	for($i=3;$i<=37;$i++){
		$primas[$i]=" ";
	}
	
	
	
	
	/***************decide la fecha que colocara en el encabezado de la hoja*******************************************/
	/***************se refiere a la fecha a la cual corresponde la hoja************************************************/
	$TmpFlmes=nombremes_completo($MiMes);
	$fechasistema="del periodo $fiAprobacion a $ffAprobacion";
	/******************************************************************************************************************/
	
	echo "<br><br>";
	/***************************Dibuja el encabezado de la hoja de tiempo**********************************************/
	echo "<table border='1' bordercolor=black width=100%>";
	echo "<tr>";
	echo "<td colspan='8'><img src='pics/Image20783687.gif' width='150' heigth='75'></td>";
	echo "<td colspan='25'><font face=arial size=2><h3><center>HOJA DE TIEMPO<br>$fechasistema</center></h3></font></td>";
	echo "<td colspan='6'><font face=arial size=3><b>".strtoupper($nomb)." ".strtoupper($apel)."<br>Unidad: $MiUnidad<br>";
	echo "Categoria: $categoria</b></font></td></tr>";
	
	/**********Dibuja el arreglo de la cabecera (los dias numerados total, VoBo Proy, resumen trabajo******************/
	echo "<tr bgcolor=#6699ff>";
	for($i=0;$i<=32;$i++){
		echo "<td style='width:20px;text-align:center'>$cabeceras[$i]</td>";
	}
	echo "<td><center>$cabeceras[33]</center></td>";
	echo "<td><center>$cabeceras[34]</center></td>";
	echo "<td colspan='4'>$cabeceras[35]</td>";
	echo "</tr>";

	/********Llena un vector con los dias festivos del mes ubicando un 1 en la posición del vector, cuando esta posición*/
	/**************************************** es festivo ********************************************* ***************/
	for ($i=1;$i<=31;$i++) $festivos[$i]=0;
	$sql="select day(fecha) as fest from festivos where year(fecha)=$MiAnno and month(fecha)=$MiMes order by fest";

	if ($res=mssql_query($sql)) {
		while ($filas=mssql_fetch_array($res)) {
			$i=$filas[fest];
			$festivos[$i]=1;
		}
	}
	
	/************ La siguiente corresponde a la consulta para extraer lo facvturado en x dia******************************/
	
	$sql="SELECT RTRIM(localizacion) + '-' + RTRIM(cargo) AS codigoproyecto,estadoAprobacion, comentarios,
		 horas_registradas, clase_tiempo, resumen_trabajo, day(fecha) as dia, id_actividad, id_proyecto, fecha, localizacion, cargo
		 FROM Horas
		WHERE     (fecha BETWEEN '$fiAprobacion' and '$ffAprobacion') AND (unidad = '$MiUnidad') AND (cargo <> 'acc0')
		AND (cargo <> 'aus0') AND (cargo <> 'enf0') AND (cargo <> 'lic0') AND (cargo <> 'per0')
         AND (cargo <> 'san0') AND (cargo <> 'vac0') and id_proyecto = $codProyecto ORDER BY codigoproyecto, clase_tiempo, id_actividad";

	
	/***********inicializa el arreglo de horas en cada dia**********************************************/
	$j=2;
	for($i=1;$i<=31;$i++){
		$horasdia[$j]=" ";
		$j++;
	}
	/***********************Busca y organiza lo facturado por dia*************************************/
	if($resultado=mssql_query($sql)){
		$filas=mssql_fetch_array($resultado);
		$codproy=$filas[codigoproyecto];
		$id_proy=$filas[id_proyecto];
		$ttiempo=$filas[clase_tiempo];
		$mactividad=$filas[id_actividad];
		$hreg=$filas[horas_registradas];
		

/*********************************************IMPRIME LAS HORAS FACTURADAS***********************************************/

		while($filas){
			$j=0;
			//while(($codproy==$filas[codigoproyecto]) and ($ttiempo==$filas[clase_tiempo]) and ($hreg==$filas[horas_registradas])){
			while(($codproy==$filas[codigoproyecto])
				 and ($ttiempo==$filas[clase_tiempo])
				  and ($mactividad==$filas[id_actividad])){
					
					
					//Selecciona el nombre la actividad o la macroactividad
					$sqlAct="select macroactividad,nombre from actividades where id_actividad='$mactividad' and id_proyecto='$id_proy'";

					$resultadoAct=mssql_query($sqlAct);
					$filasAct=mssql_fetch_array($resultadoAct);
					
					if($filasAct[macroactividad] <> NULL){
						$act=$filasAct[macroactividad];
					}else{
						$act=substr($filasAct[nombre],0,6);
					}
					//fin de selecciona
					
				$dia=$filas[dia];
				//si el dia el devuelto como 01 se quita el 0, pues 01 y 1 es DIFERENTE EN LINUX
				$numind=substr($dia,0,1);
				if($numind==0){
					$indice=substr($dia,1,1);
				}else{
					$indice=$dia;
				}
				$horasdia[$indice]=$filas[horas_registradas];
				$resumentrabajo[$indice]=$filas[resumen_trabajo];
				$aprobacion[$indice] = $filas[estadoAprobacion];
				//$aprobDiv[$indice] = $filas[estadoAprobDivision];
				$comentario[$indice] = $filas[comentarios];
				
				//extrae los datos para las aprobaciones
				$ids_p = $filas[id_proyecto];
				$ids_a = $filas[id_actividad];
				$fechas = str_replace(" ","/",substr($filas[fecha],0,11));
				$locali = $filas[localizacion];
				$cargos = $filas[cargo];
				$ttiempos = $filas[clase_tiempo];	
				$lashoras = $filas[horas_registradas];
							
				if($aprobacion <> "SI"){
					$infDiaria[$j] = $ids_p."-".$ids_a."-".$MiUnidad."-".$fechas."-".$locali."-".$cargos."-".$ttiempos."-".$lashoras;
					$j++;
				}
				
				//$viaticos[$indice]=$filas[id_viatico];
				$filas=mssql_fetch_array($resultado);
				
			}
			
			$j--;
			
			
			for($k=0;$k<=$j;$k++){
				$DATOS =$DATOS."*".$infDiaria[$k];
				$infDiaria[$k] = "";
				
			}
			
			
			/************************imprime el renglon con el codigo encontrado ******************************/
			
			$color=COLORNORMAL;
			echo "<tr bgcolor=$color>";

			echo "<td nowrap>$codproy $act<br></td>";
			echo "<td><center>$ttiempo</center></td>";
			
			//sumahoras es el tiempo que se imprime en TIEMPO TOTAL
			$sumahoras=trim(array_sum($horasdia));
			
			for($i=1;$i<=31;$i++){
				// revision 2002-05-10. Si el dia es sabado o domingo, coloca un color especial
				$nombredia=date("w", mktime(0,0,0,$MiMes,$i,$MiAnno));
				$color='';
				if ($nombredia==0 or $nombredia==6) $color=COLORFINSEMANA;
				if ($festivos[$i]==1) $color=COLORFESTIVO;
				if ($i>$numdias) $color=COLORFINMES;
				// revision 2002-05-03 Totaliza horas por dia
				$total[$i+1]+=(int) $horasdia[$i];
				
				if($horasdia[$i]==0){
					$horasdia[$i]=" ";
					echo "<td bgcolor='$color'>$horasdia[$i]</td>\r";
				} else {
					//Aprobación
					$resumen=trim($resumentrabajo[$i]);
					$hora = $horasdia[$i];
					if($aprobacion[$i]=="SI"){
						$hora = "<font face='arial' size='2' color='green'><b>$horasdia[$i]</b></font>";
					}elseif($aprobacion[$i]=="NO"){
						$hora = "<font face='arial' size='2' color='red'><b>$horasdia[$i]</b></font>";
						$comentarioNoAprob =  $comentario[$i];
					}
					echo "<td bgcolor='$color' style=\"text-align:center;\"><a href=\"javascript:alert('$resumen');\">$hora</a></td>\r";
					$horasdia[$i]=" ";

				}
			}
			$color='';
			/***********************imprime el total de horas horizontal y cambia de codigo*******************/
			echo "<td><center>$sumahoras</center></td>";
			
			//La autorización - aprobaciones 448
			if($DATOS==""){
				echo "<td>APROBADO</td>";					
			}else{
				echo "<td><a href = aprobarFacturacion.php?datos=$DATOS>SI</a>  <a href = noaprobarFacturacion.php?datos=$DATOS>NO</a></td>";
				echo "<td colspan=3><a href = \"javascript:alert('$comentarioNoAprob');\"><img src='imagenes\comentarios.jpg' width=110 HEIGHT=15;></a> </td>";
			}
	
			echo "<td colspan=3> </td>";	
			$DATOS = "";
			$codproy=$filas[codigoproyecto];
			$ttiempo=$filas[clase_tiempo];
			$mactividad=$filas[id_actividad];
			$id_proy=$filas[id_proyecto];
			$hreg=$filas[horas_registradas];
			$fecha = $filas[fecha];
			$localizacion=$filas[localizacion];
			$cargo=$filas[cargo];
			
			// captura el valor de localizacion y compone el codigo del proyecto
			$cod=explode("-",$codproy);
			// Modificacion 2003-09-03 por Manuel Romero
			// si el codigo ya trae el cargo, se lo quita
			$cargos=separa_cargo($cod[1]);
			$codproyaux=$cod[0].$cargos["codigo"].$cod[2];
			echo "</tr>";
		}
		
				
		/**********************************FIN IMPRIME HORAS FACTURADAS*********************************************/

		/************************** VACACIONES , ENFERMEDAD, LICENCIAS, ETC*************************/
		echo "<tr>";
		for($i=0;$i<=33;$i++){
			echo "<td> </td>";
		}
		//boton de aprobacion
		echo "<td> </td>";
		echo "<td colspan=4> </td>";
		echo "</tr>";
		
		echo "<tr>";
		for($i=0;$i<=34;$i++){
			echo "<td><font face=Arial size=2 color=#000000><center>$vacaciones[$i]</center></td>";
		}
		echo "<td colspan=4><font face=Arial size=2 color=#000000>$vacaciones[35]</td>";
		echo "</tr>";

		//Enfermedad
		echo "<tr>";
		for($i=0;$i<=34;$i++){
			echo "<td><font face=Arial size=2 color=#000000><center>$enfermedad[$i]</center></td>";
		}
		echo "<td colspan=4><font face=Arial size=2 color=#000000>$enfermedad[35]</td>";
		echo "</tr>";
		
		//Accidentes de trabajo
		echo "<tr>";
		for($i=0;$i<=34;$i++){
			echo "<td><font face=Arial size=2 color=#000000><center>$acciddetrabajo[$i]</center></td>";
		}
		echo "<td colspan=4><font face=Arial size=2 color=#000000>$acciddetrabajo[35]</td>";
		echo "</tr>";
		
		echo "<tr>";
		for($i=0;$i<=34;$i++){
			echo "<td><font face=Arial size=2 color=#000000><center>$permisospacto[$i]</center></td>";
		}
		echo "<td colspan=4><font face=Arial size=2 color=#000000>$permisospacto[35]</td>";
		echo "</tr>";
		
		echo "<tr>";
		for($i=0;$i<=34;$i++){
			echo "<td><font face=Arial size=2 color=#000000><center>$licencias[$i]</center></td>";
		}
		echo "<td colspan=4><font face=Arial size=2 color=#000000>$licencias[35]</td>";
		echo "</tr>";
		
		echo "<tr>";
		for($i=0;$i<=34;$i++){
			echo "<td><font face=Arial size=2 color=#000000><center>$sanciones[$i]</center></td>";
		}
		echo "<td colspan=4><font face=Arial size=2 color=#000000>$sanciones[35]</td>";
		echo "</tr>";
		
		echo "<tr>";
		for($i=0;$i<=34;$i++){
			echo "<td><font face=Arial size=2 color=#000000><center>$ausencias[$i]</center></td>";
		}
		echo "<td colspan=4><font face=Arial size=2 color=#000000>$ausencias[35]</td>";
		echo "</tr>";

		//suma lo que tiene el arreglo $total + lo que cada arreglo de vacaciones, permisos, etc, tiene
		for($i=2;$i<=31;$i++){
			$total[$i]=$total[$i]+$vacaciones[$i]+$enfermedad[$i]+$acciddetrabajo[$i]+$permisospacto[$i]+$licencias[$i]+$sanciones[$i]+$ausencias[$i];
		}
		
		
			echo "<tr>";
			for($i=0;$i<=35;$i++) {
				switch ($i) {
					case 33:
						$tot = array_sum($total);
						echo "<td nowrap><center>$tot</center></td>";
						break;
					case 34:
						echo "<td>&nbsp;</td>";
						break;
					case 35:
						echo "<td colspan=3>&nbsp;</td>";
						break;
					default:
						// revision 2002-05-10. Si el dia es sabado o domingo, coloca un color especial
						if ($i>1) {
							$nombredia=date("w", mktime(0,0,0,$MiMes,$i-1,$MiAnno));
							$color='';
							if ($nombredia==0 or $nombredia==6) $color=COLORFINSEMANA;
							if ($i-1>$numdias) $color=COLORFINMES;
							if ($festivos[$i-1]==1) $color=COLORFESTIVO;
						}
						
						if ($i>0 && abs($total[$i])<0.001) $total[$i]="&nbsp;";
						//imprime el total vertical de cada uno de los dias del mes
						echo "<td nowrap bgcolor='$color'><center>$total[$i]</center></td>";
						break;
				}
			}
			echo "</tr>";


		/***********************imprime la seccion de prima de localizacion************************/
	
		echo "<tr>";
		echo "<td><font face=Arial size=1 color=#000000>$primas[0]</td>";
		echo "<td><font face=Arial size=1 color=#000000>$primas[1]</td>";
		echo "<td colspan='31'><font face=Arial size=1><center>$primas[2]</center></font></td>";
		echo "<td colspan='3'><table border='1' bordercolor=black>
			<tr>
			<td colspan=3><font face=Arial size=1 color=#000000><center>DIAS</center></td></tr>
			<tr>
			<td><font face=Arial size=1 color=#000000>Viaticos</td>
			<td><font face=Arial size=1 color=#000000>Prima</td>
			<td><font face=Arial size=1 color=#000000>Aux. Alim</td>
			</table>
			</td>";
		echo "<td colspan='3'><table border='1' bordercolor=black><tr><td colspan=3 width=150><font face=Arial size=1 color=#000000><center>DESCRIPCIÓN</center>
			</td></tr><tr><td><font face=Arial size=1 color=#000000>Proyecto:</td><td><font face=Arial size=1 color=#000000>Sitio:</td></table></td>";
		echo "</tr>";
		

			//imprime los viáticos
			$sql="SELECT rtrim(localizacion)+ '-' +rtrim(cargo) AS codigo, DAY(fecha) AS dia, id_viatico
					FROM Horas WHERE  (fecha BETWEEN $cadfecha) AND (unidad = '$MiUnidad') AND (id_viatico is not NULL) order by codigo";

			if($resultado=mssql_query($sql)){
			
				if(mssql_num_rows($resultado)>0){
					$filas=mssql_fetch_array($resultado);
					$codigo=$filas[codigo];
					while($filas){
						   while($codigo==$filas[codigo]){
								$dia=$filas[dia];
								//si el dia el devuelto como 01 se quita el 0, pues 01 y 1 es DIFERENTE EN LINUX
								$numind=substr($dia,0,1);
								if($numind==0){
									$indice=substr($dia,1,1);
								}else{
									$indice=$dia;
								}
								$viaticos[$indice]="X";
								$filas=mssql_fetch_array($resultado);
							}
								//imprime el código encontrado y cambia de código
								$viaticos[-1]=$codigo;
								echo "<tr>";
								for($i=-1;$i<=34;$i++){
									
									if($viaticos[$i]!=NULL){
										//echo "lo que trae $viaticos[$i] ". $viaticos[$i];
										echo "<td>$viaticos[$i]</td>";
										//Borra el valor para imprimir el siguiente viatico
										$viaticos[$i]="";
									}else{
										echo "<td> </td>";
									}
								}
								echo "<td colspan=4> </td>";
								echo "</tr>";
							$codigo=$filas[codigo];
					}
				}
			}

		/**********************imprime la seccion de firmas****************************************/

		echo "<tr><td colspan=4><font face=Arial size=1 color=#000000>CT CLASE DE TIEMPO</td><TD colspan=4><font face=Arial size=1 color=#000000>HORARIO</TD>
		<td colspan=9><font face=Arial size=1 color=#000000>CT CLASE DE TIEMPO</td>
		<TD COLSPAN=4><font face=Arial size=1 color=#000000>HORARIO</TD><TD COLSPAN=6><font face=Arial size=1 color=#000000>FIRMA DEL EMPLEADO</TD>
		<TD COLSPAN=5><font face=Arial size=1 color=#000000>Vo.Bo. JEFE INMEDIATO</TD>
		<TD colspan=5><font face=Arial size=1 color=#000000>Vo.Bo. JEFE DEPARTAMENTO</TD><TD><font face=Arial size=1 color=#000000>CONTRATOS</TD><TD>
		<font face=Arial size=1 color=#000000>PERSONAL</TD></tr>";
		
		echo "<tr><td colspan=4><font face=Arial size=1 color=#000000>1 Ordinario<br>2 Ordinario (1)<br>3 Nocturno Ordinario<br>4 Extra
		 Ordinario (2)<br>5 Extra nocturno</td><TD colspan=4><font face=Arial size=1 color=#000000>6 am-10 pm<br>6 am-10 pm<br>10 pm-6 am<br>6 am-10 pm<br>10 pm-6 am</TD>
		<td colspan=9><font face=Arial size=1 color=#000000>6 Descanso obligatorio <br>7 Extra descanso obligatorio (3)<br>8 Nocturno descanso obligatorio
		<br>9 Extra descanso obligatorio nocturno<br>Viático (Clase o Localidad)</td>
		<TD COLSPAN=4><font face=Arial size=1 color=#000000>6 am-10 pm<br>6 am-10 pm<br>0am-6am y 10pm-12pm<br>0am-6am y 10pm-12pm</TD>
		<TD COLSPAN=6><font face=Arial size=1 color=#000000> </TD>
		<TD COLSPAN=5><font face=Arial size=1 color=#000000> </TD>
		<TD colspan=5><font face=Arial size=1 color=#000000> </TD><TD><font face=Arial size=1 color=#000000> </TD><TD>
		<font face=Arial size=1 color=#000000> </TD></tr>";
		
	}
	echo "</table>";
	echo "<br>";
?>
<!--<h5>Unidad <input type=text size=6 name=unidadvar value='<? echo $MiUnidad; ?>'>&nbsp;-->
<?	//include("fechahoja.php"); ?>
<!--<input type=submit name=verhoja value=Consultar>
<input type=button id=bt value='Instrucciones para imprimir la hoja de tiempo' onclick='mostrarinstr()'>
</h5>
</form>-->
<?
	/*if ($hay_errores==1) {
		$cadtmp="<b style='font-size:12px;'>ADVERTENCIA: </b><span style='font-size:12px;'>Usted tiene registrados uno o mas dias con con dos viaticos simultaneos. ";
		$cadtmp.="Por favor comuniquese con el encargado del sistema para corregir esta incongruencia.<br>";
		$cadtmp.="Las casillas marcadas en rojo corresponden a dias que tienen reportados viaticos mas de una vez.</span>";
		echo "<hr>$cadtmp<hr>";
	}*/
?>
<script>
function mostrarinstr() {
	if (xx.style.display=='none')
		xx.style.display='block';
	else
		xx.style.display='none';
}
</script>
<div id=xx style='display:none;'>
<p class=titulo>Instrucciones para imprimir la hoja de tiempo</p>
	1. Coloque los margenes de impresi&oacute;n en 10 mm por todos lados<br>
	2. Presione el bot&oacute;n que muestra las instrucciones de impresi&oacute;n para ocultar este texto.<br>
	3. Haga clic con el bot&oacute;n secundario del mouse (generalmente el derecho, si el usuario es diestro) sobre la hoja de tiempo.<br>
	4. Seleccione la opcion Imprimir.<br>
	5. Seleccione Propiedades...<br>
	6. Escoja la hoja tamaño carta y coloquela en sentido apaisado.<br>
	7. Presione el bot&oacute;n &lt;OK> dos veces para enviar el trabajo a la impresora.<br>
</div>


</body>
</html>

<?
	function imprime_viaticos($viat) {
		global $verhoja,$sitant,$errores;
		global $MiMes,$MiAnno;
		
		// imprime los dias
		$cant=0;
		for ($i=1;$i<=31;$i++) {
			$nombredia=date("w", mktime(0,0,0,$MiMes,$i,$MiAnno));
			$numdias=date("t", mktime(0,0,0,$MiMes,$i,$MiAnno));
			$color='';
			if ($nombredia==0 or $nombredia==6) $color=COLORFINSEMANA;
			if ($i>$numdias) $color=COLORFINMES;
			
			$cadtmp="";
			if ($errores[$i]=='1') $cadtmp="class='error'";
			echo "<td bgcolor='$color' $cadtmp><center>";
			if ($viat[$i]=='1') {
				echo "X";
				$cant++;
			} else echo "&nbsp;";
			echo "</center></td>";
		}
		// imprime los totales
		echo "<td bgcolor=''><center>$cant</center></td>";
		echo "<td colspan=4>SITIO: $sitant</td>";
		echo "</tr>";
	}
?>

<?php


	//Imprime el lado posterior de la hoja de tiempo
	//Identifica los cargos a los cuales ha facturado el usuario en un rango de tiempo determinado
	$sql = "SELECT DISTINCT cargo AS Cargo FROM Horas
	WHERE (unidad = $MiUnidad) AND (fecha BETWEEN '$fiAprobacion' and '$ffAprobacion') order by cargo";
	
	$ap3 = mssql_query($sql);
	$i=0;
	$j=0;
	
	while($reg = mssql_fetch_array($ap3)){
		$arregloM[$i] = $reg[Cargo];
		$i++;
	}

	
	
	
	//Se extrae lo programado
	$sql="SELECT     distinct Horas.cargo, Proyectos.nombre AS NombreProyecto,  Horas.fecha,
	Horas.id_proyecto,Horas.resumen_trabajo
	FROM Horas INNER JOIN Proyectos ON Horas.id_proyecto = Proyectos.id_proyecto
	WHERE     (Horas.unidad = $MiUnidad) AND (Horas.fecha BETWEEN '$fiAprobacion' and '$ffAprobacion') and horas.id_proyecto='$codProyecto'
	ORDER BY Horas.fecha asc";

	$ap4 = mssql_query($sql);

		while($reg = mssql_fetch_array($ap4)){
			$fech = substr($reg[fecha],0,11);
			$fech2 = explode(" ",$fech);
			$dia = cambiaDia($fech2[1]);
			
			if($fech == substr($reg[fecha],0,11)){
					//$arreglo[$dia] = $arreglo[$dia].". ".$reg[resumen_trabajo].". "."<a href='javascript:alert(\"$reg[NombreProyecto]\")'>P</a>";
					$arreglo[$dia] = $arreglo[$dia].". "."<a href='javascript:alert(\"$reg[NombreProyecto]\")'>$reg[resumen_trabajo]</a>";
			}else{
				//$arreglo[$dia] = $reg[resumen_trabajo];
			}
		}
	
	
	//Imprime
	echo "<table width=100% border = 1>";
	echo "<tr><td width=4%><b>DIA</b></td><td width=96%><b><center>TRABAJO REALIZADO</b></center></td></tr>";
		foreach($arreglo as $key => $valor){
			$valor=substr($valor,1);
			echo "<tr><td>$key</td><td>$valor</td>";				
		}
	echo "</table>";
	
	
	?>
	

	
