<?
session_start();
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");

echo "<html>";
echo "<head>";
echo "<LINK REL='stylesheet' HREF='css/estilo.css' TYPE='text/css'>";
echo "</head>";

echo "<body bgcolor='#EAEAEA'>";

$nombrecomputador="sqlservidor";
include "funciones.php";

define('COLORNORMAL','#FFFFCC');//Pinta donde se escriben las horas
define('COLORFINSEMANA','#ff0000');
define('COLORFINMES','#FF0066');
define('COLORFESTIVO','#7c8d72');

//El login es la unidad del usuario
//if (isset($unidadvar))
//$laUnidad=$unidadvar;
//else
//$laUnidad=$laUnidad;

//Verifica que el usuario existe en la base de datos contrastandolo con la unidad
include "validaUsrBd.php";
$sql="SELECT Usuarios.nombre as nombre, Usuarios.apellidos as apelli, Categorias.nombre as categoria
		FROM Usuarios INNER JOIN Categorias ON Usuarios.id_categoria = Categorias.id_categoria
		WHERE     (Usuarios.unidad = '$laUnidad')";
if ($res=mssql_query($sql)) {
	$fil=mssql_fetch_array($res);
	$categoria=$fil[categoria];
	$nomb=$fil[nombre];
	$apel=$fil[apelli];
} else {
	alert("Usuario no registrado");
	exit();
}

//imprime el encabezado
echo "<table class='txtTabla'><tr ><td width=85%><h2>HOJA DE TIEMPO</h2></td><TD width=15%><img src='imagenes/LogoIngetecPNG2.png' heigth=200 width=150></TD></tr></table>";
echo "<table border=1 WIDTH='100%'>";
$elnombre=strtoupper($nomb." ".$apel);
echo "<tr ><td class='tituloTabla'><b>Nombre:</td><td class='txtTabla'>$elnombre</td></tr>";
echo "<tr ><td class='tituloTabla'><b>Unidad:</td><td class='txtTabla'>$laUnidad</td></tr>";
echo "<tr ><td class='tituloTabla'><b>Proyecto:</td><td class='txtTabla'>Porce IV. Segunda Etapa</td></tr>";

//Decide sobre las fechas ????
$MiMes=($verhoja=="Consultar"?$Flmes:date("m",time()));
$MiAnno=($verhoja=="Consultar"?$Flano:date("Y",time()));
$numdias=date("t", mktime(0,0,0,$MiMes,1,$MiAnno));
$cadfecha="'$MiMes/1/$MiAnno' and '$MiMes/$numdias/$MiAnno'";


// Agregado por Manuel Romero 2004-10-28

// ****************************************
$MiMes=7;
$MiAnno=2007;
$numdias=date("t", mktime(0,0,0,$MiMes,1,$MiAnno));
$cadfecha="'$MiMes/1/$MiAnno' and '$MiMes/$numdias/$MiAnno'";
// ****************************************

/***************decide la fecha que colocara en el encabezado de la hoja*******************************************/
/***************se refiere a la fecha a la cual corresponde la hoja************************************************/
$TmpFlmes=nombremes_completo($MiMes);
$fechasistema="del mes de $TmpFlmes de $MiAnno";
echo "<tr ><td class='tituloTabla'><b>Mes de facturación:</td><td class='txtTabla'>".strtoupper($TmpFlmes)." DE $MiAnno</td></tr>";
echo "<tr><td></td></tr>";
echo "</table>";

/******************************************************************************************************************/
/********Llena un vector con los dias festivos del mes ubicando un 1 en la posición del vector, cuando esta posición*/
/**************************************** es festivo ********************************************* ***************/
for ($i=0;$i<=$numdias;$i++) $festivos[$i]=0;
$sql="select day(fecha) as fest from festivos where year(fecha)=$MiAnno and month(fecha)=$MiMes order by fest";

if ($res=mssql_query($sql)) {
	$numreg=mssql_num_rows($res);

	if($numreg>0){
		while ($filas=mssql_fetch_array($res)) {
			$i=$filas[fest];
			$festivos[$i]=1;
		}
	}
}

/************ La siguiente corresponde a la consulta para extraer lo facvturado en x dia******************************/

//poner $cadfecha despues de between

$sql="SELECT RTRIM(localizacion) + '-' + RTRIM(cargo) AS codigoproyecto,
		 horas_registradas, clase_tiempo, resumen_trabajo, day(fecha) as dia, id_actividad, id_proyecto FROM Horas
		WHERE     (fecha BETWEEN $cadfecha) AND (unidad = '$laUnidad') and RTRIM(localizacion) + '-' + RTRIM(cargo) <> '1-1199' and id_proyecto = 34 order by dia";


//ORDER BY codigoproyecto, clase_tiempo, id_actividad";
//echo $sql;
/***********inicializa el arreglo de horas en cada dia**********************************************/
$j=2;
for($i=1;$i<=31;$i++){
	$horasdia[$j]=" ";
	$j++;
}
//IMPRIME EL ENCABEZADO
echo "<table border=1 WIDTH='100%'><tr class='tituloTabla'><td width=3%><b><center>DIA</td><td width=3%><b><center>HR</td>
	<TD width=7%><b>M-ACT</TD>
	<TD><b><center>ACTIVIDAD</TD>
	<td><b><center>RESUMEN</td></tr>";

/***********************Busca y organiza lo facturado por dia*************************************/
if($resultado=mssql_query($sql)){
	/*$filas=mssql_fetch_array($resultado);
	$codproy=$filas[codigoproyecto];
	$id_proy=$filas[id_proyecto];
	$ttiempo=$filas[clase_tiempo];
	$mactividad=$filas[id_actividad];
	$hreg=$filas[horas_registradas];*/

	/*********************************************IMPRIME LAS HORAS FACTURADAS***********************************************/
	//echo "<table border=1 WIDTH='100%'>";
	$IndDia=1;
	while($filas=mssql_fetch_array($resultado)){
		$mactividad=$filas[id_actividad];
		$id_proy=$filas[id_proyecto];
		//se impide que se sumen los tiempos clase 10
		$tTiempo=$filas[clase_tiempo];
		//Selecciona el nombre la actividad o la macroactividad
		$sqlAct="select macroactividad,nombre from actividades where id_actividad='$mactividad' and id_proyecto='$id_proy'";

		$resultadoAct=mssql_query($sqlAct);
		$filasAct=mssql_fetch_array($resultadoAct);

		//fin de selecciona
		$dia=$filas[dia];
		$numind=substr($dia,0,1);
		if($numind==0){
			$indice=substr($dia,1,1);
		}else{
			$indice=$dia;
		}
		if($tTiempo==10){
			$horasdia[$indice]="[".$filas[horas_registradas]."]";
		}else{
			$horasdia[$indice]=$filas[horas_registradas];
		}
		$resumentrabajo[$indice]=$filas[resumen_trabajo];
		$mActiv[$indice]=$filasAct[macroactividad];
		$NombreAct[$indice]=$filasAct[nombre];


		/************************imprime el renglon con el codigo encontrado ******************************/
		$color=COLORNORMAL;

		//$sumahoras=trim(array_sum($horasdia));
		$sumahoras=$sumahoras+$horasdia[$indice];

		$nombredia=date("w", mktime(0,0,0,$MiMes,$i,$MiAnno));

		$color='';
		if ($nombredia==0 or $nombredia==6) $color=COLORFINSEMANA;
		if ($festivos[$i]==1) $color=COLORFESTIVO;
		if ($i>$numdias) $color=COLORFINMES;
		//Totaliza horas por dia
		$total[$indice+1]+=(int) $horasdia[$i];

		if($IndDia<$indice){
			for($j=$IndDia;$j<$indice;$j++){
				//Decide el color que le pondrá al fin de semana
				$nombredia=date("w", mktime(0,0,0,$MiMes,$j,$MiAnno));
				if ($nombredia==0 or $nombredia==6){
					$color=COLORFINSEMANA;
					echo "<tr class='txtTabla'><td bgcolor='$color'>$j</td><td> </td><td> </td><TD> </TD><TD> </TD></tr>";
				}else{
					if ($festivos[$j]==1) {
						$color=COLORFESTIVO;
						echo "<tr class='txtTabla'><td  bgcolor='$color'>$j</td><td> </td><td> </td><TD> </TD><TD> </TD></tr>";
					}else{
						echo "<tr class='txtTabla'><td>$j</td><td> </td><td> </td><TD> </TD><TD> </TD></tr>";
					}
				}
			}
			echo "<tr class='txtTabla'><td>$indice</td><td>$horasdia[$indice]</td><td>$mActiv[$indice]</td>
						<td>$NombreAct[$indice]</td><TD>$resumentrabajo[$indice]</TD></tr>";
			$IndDia=$indice+1;
		}else{
			echo "<tr class='txtTabla'><td>$indice</td><td>$horasdia[$indice]</td><td>$mActiv[$indice]</td>
					<td>$NombreAct[$indice]</td><TD>$resumentrabajo[$indice]</TD></tr>";
			$IndDia=$indice+1;
		}

	} //Cierra el while

	//Completa los dias en blanco
	if($indice<31){
		$indice++;
		for($j=$indice;$j<=31;$j++){
			$nombredia=date("w", mktime(0,0,0,$MiMes,$j,$MiAnno));
			if ($nombredia==0 or $nombredia==6){
				$color=COLORFINSEMANA;
				echo "<tr class='txtTabla'><td bgcolor='$color'>$j</td><td> </td><td> </td><TD> </TD><TD> </TD></tr>";
			}else{
				if ($festivos[$j]==1) {
					$color=COLORFESTIVO;
					echo "<tr class='txtTabla'><td bgcolor='$color'>$j</td><td > </td><td> </td><TD> </TD><TD> </TD></tr>";
				}else{
					echo "<tr class='txtTabla'><td width=30>$j</td><td width=55> </td><td width=200> </td><TD width=300> </TD><TD width=300> </TD></tr>";
				}
			}
		}
	}
	echo "</table>";
	$color='';
	/***********************imprime el total de horas horizontal y cambia de codigo*******************/
	echo "<table>";
	echo "<tr class='txtTabla'><td><B>TOTAL HORAS</b></td><td><b>($sumahoras)</b></td></tr>";
	echo "</table>";


	// captura el valor de localizacion y compone el codigo del proyecto
	$cod=explode("-",$codproy);
	// Modificacion 2003-09-03 por Manuel Romero
	// si el codigo ya trae el cargo, se lo quita
	$cargos=separa_cargo($cod[1]);
	$codproyaux=$cod[0].$cargos["codigo"].$cod[2];
	echo "</tr>";


	/**********************************FIN IMPRIME HORAS FACTURADAS*********************************************/
}

echo "</table>";


//lee los viaticos y los presenta

/*$sqlViat="SELECT     Viaticos.sitio, Horas.resumen_trabajo, ViaticosHoras.horallegada, ViaticosHoras.horasalida, Horas.fecha
FROM         ViaticosHoras INNER JOIN
Horas ON ViaticosHoras.id_proyecto = Horas.id_proyecto AND ViaticosHoras.id_viatico = Horas.id_viatico INNER JOIN
Viaticos ON Horas.id_proyecto = Viaticos.id_proyecto AND Horas.id_viatico = Viaticos.id_viatico AND
Horas.fecha = ViaticosHoras.fecha
WHERE     (Horas.fecha BETWEEN '08/1/2004' and '08/31/2004') AND
(ViaticosHoras.unidad = '$laUnidad') AND (ViaticosHoras.id_proyecto = 34)";*/
$sqlViat=" SELECT  distinct   Horas.resumen_trabajo, ViaticosHoras.horallegada, ViaticosHoras.horasalida, Horas.unidad, Viaticos.sitio, Horas.fecha
			FROM  Viaticos INNER JOIN
            Horas ON Viaticos.id_proyecto = Horas.id_proyecto AND Viaticos.id_viatico = Horas.id_viatico RIGHT OUTER JOIN
            ViaticosHoras ON Horas.unidad = ViaticosHoras.unidad AND Horas.id_viatico = ViaticosHoras.id_viatico AND
            Horas.fecha = ViaticosHoras.fecha
			WHERE (Horas.fecha BETWEEN $cadfecha) AND (ViaticosHoras.unidad = '$laUnidad') AND (ViaticosHoras.id_proyecto = 34) order by Horas.fecha";


$resultadoViat=mssql_query($sqlViat);

$numreg=mssql_num_rows($resultadoViat);
if($numreg>0){
	echo "<center>";
	echo "<table>";
	echo "<tr><td width=600><b>DIAS DE VIÁTICOS</b></td></tr>";
	echo "</table>";
	echo "</center>";
	//Abre una nueva tabla
	echo "<table border=1>";
	echo "<tr><td width=10%><b>DIA</td><td width=15%><b>LUGAR</td><td width=75%><b>RECORRIDO (horas)</td><tr>";
}

while($filasViat=mssql_fetch_array($resultadoViat)){
	$fech=substr($filasViat[fecha],0,6);
	$sitio=$filasViat[sitio];
	$hs=$filasViat[horasalida];
	$hl=$filasViat[horallegada];
	$rt=$filasViat[resumen_trabajo];

	echo "<tr><td>$fech</td><td>$sitio</td><td>Salida: $hs Llegada: $hl</td><tr>";

}



echo "</table>";

//escribe la parte final de la hoja
echo "<table border=1>";
echo "<tr class='txtTabla'><td width=33%><b>FIRMA EMPLEADO</td><td width=33%><b>FIRMA JEFE DEL LOTE</td><td width=33%><b>FIRMA DIRECTOR DEL PROYECTO</td></tr>";
echo "</table>";
echo "<table>";
echo "<tr><td width=33%><b>      </td><td width=33%><b>      </td><td width=33%><b>      </td></tr>";
echo "<tr><td width=33%><b>      </td><td width=33%><b>      </td><td width=33%><b>      </td></tr>";
echo "<tr><td width=100%><hr></tr>";

echo "</table>";


echo "</table>";

//FINALIZA EL CÓDIGO. ES OBVIO NO?


?>
<table width=""></table>
</body>
