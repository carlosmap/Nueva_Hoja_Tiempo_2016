<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
<?php
session_start();
//include("../verificaRegistro2.php");
//include('../conectaBD.php');

//Establecer la conexión a la base de datos
//$conexion = conectar();
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";


//------------------------

//10Jun2008
//Identificar si el usuario activo verá toda la información o sólo sus actividades
$esDC = 0 ;
$esProgP = 0;
$esOrdG = 0 ;
$todo= 0 ;
$verBoton="SI";

//El usuario es Director o Coordinador
$vSqlU="Select coalesce(count(*), 0) existeDir ";
$vSqlU=$vSqlU." from HojaDeTiempo.dbo.Proyectos ";
$vSqlU=$vSqlU." where (id_director = " . $laUnidad . " or id_coordinador = " . $laUnidad . " ) ";
$vSqlU=$vSqlU." and id_proyecto = " . $cualProyecto ;
$vCursorU = mssql_query($vSqlU);
if ($vRegU=mssql_fetch_array($vCursorU)) {
	$esDC =  $vRegU[existeDir] ;
}

//Si el usuarios es Programador del proyecto
$vSqlU="Select coalesce(count(*), 0) existeProg ";
$vSqlU=$vSqlU." from HojaDeTiempo.dbo.Programadores  ";
$vSqlU=$vSqlU." where unidad = " . $laUnidad ;
$vSqlU=$vSqlU." and id_proyecto = " . $cualProyecto ;
$vSqlU=$vSqlU." and progProyecto = 1 ";
$vCursorU = mssql_query($vSqlU);
if ($vRegU=mssql_fetch_array($vCursorU)) {
	$esProgP =  $vRegU[existeProg] ;
}


//Si el usuario es ordenador del gasto
$vSqlU="select coalesce(count(*), 0) existeOrd ";
$vSqlU=$vSqlU." from GestiondeInformacionDigital.dbo.OrdenadorGasto ";
$vSqlU=$vSqlU." where unidadOrdenador = ". $laUnidad ;
$vSqlU=$vSqlU." and id_proyecto =" . $cualProyecto ;
$vCursorU = mssql_query($vSqlU);
if ($vRegU=mssql_fetch_array($vCursorU)) {
	$esOrdG =  $vRegU[existeOrd] ;
}

//Si alguna de las variables es > 0 el usuario podrá ver todo
$todo= $esDC + $esProgP + $esOrdG ;
if ($todo > 0) {
	$verBoton="SI";
}
else {
	$verBoton="NO";
}

//--------------


//Cantidad de registros del formulario
if (trim($pCantReg) == "") {
	$pCantReg = 1;
}


$cur_fecha_ini_proyecto=mssql_query("select DAY(fechaInicio) as dia,  month(fechaInicio) as mes, YEAR(fechaInicio) as ano, fechaInicio from HojaDeTiempo.dbo.Proyectos where id_proyecto=".$cualProyecto);
$datos_fecha_ini_proyecto=mssql_fetch_array($cur_fecha_ini_proyecto);


if(trim($recarga) == "2"){
	$s = 1;
	$cur_tran=mssql_query("BEGIN TRANSACTION");
	$error="no";
	while ($s <= $pCantItems)
	{
		$fechaI = "fechaI" . $s;
		$fechaF = "fechaF" . $s;
		$IDacti = "id_activi" . $s;



	//	echo ${$IDacti}." - ".${$fechaI}." - ".${$fechaF}."  --- ".$s." <br>";

		if((trim(${$fechaI})!="")and(trim(${$fechaF})!=""))
		{

			//actualiza la fecha de inicio y de finalizacion de las actividades 
			$up_activi="update HojaDeTiempo.dbo.Actividades set fecha_inicio='".${$fechaI}."', fecha_fin='".${$fechaF}."',usuarioMod=".$_SESSION["sesUnidadUsuario"].",fechaMod=getdate() where id_proyecto=".$cualProyecto." and id_actividad=".${$IDacti}."";
			$cur_up_activi=mssql_query($up_activi);
//	echo $up_activi." --- ${$fechaF} - ".${$fechaI}."". mssql_get_last_message()." <br>";
			if(trim($cur_up_activi)=="")
			{
				$error="si";
			}
		}

		$s++;
	}

	//Si los cursores no presentaron problema
	//if  (trim($cursorIn1) != "")  {
	if (trim($error)== "no")  
	{
		$cursorTran2 = mssql_query(" COMMIT TRANSACTION ");	
		echo ("<script>alert('Informaci\xf3n actualizada satisfactoriamente');</script>"); 
	} 
	
	else
	{
		$cursorTran2 = mssql_query(" ROLLBACK TRANSACTION ");
		echo ("<script>alert('Error durante la grabaci\xf3n');</script>");
	} 
	echo ("<script>window.close();MM_openBrWindow('htPlanProyectos03.php?cualProyecto=".$cualProyecto."','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");
}

?>
 <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
<head>

<script language="JavaScript" >

var nav4 = window.Event ? true : false;
function acceptNum(evt)
{   
	var key = nav4 ? evt.which : evt.keyCode;   
	return (key <= 13 || (key>= 48 && key <= 57)||( key==47) );
}


function compare_fecha(fecha, fecha2,opc)  
 {  
//alert("Ingresa  ******************** "+fecha+ " ------------------ "+fecha2+" ////// "+opc);
    var xMonth= parseInt (( fecha.substring(0, fecha.indexOf("/")) )); 
    var xDay= parseInt ( ( fecha.substring( fecha.indexOf("/")+1,fecha.lastIndexOf("/")) ) ) ; 
    var xYear=parseInt( fecha.substring(fecha.lastIndexOf("/")+1,fecha.length));  

	//datos fecha fin
    var yMonth=( fecha2.substring(0, fecha2.indexOf("/")) );  
    var yDay=( fecha2.substring( fecha2.indexOf("/")+1,fecha2.lastIndexOf("/")) ); 
    var yYear=parseInt( fecha2.substring(fecha2.lastIndexOf("/")+1,fecha2.length) );  

//alert ("fecha incial: "+xMonth+" "+xDay+" "+xYear+" fecha final: "+yMonth+" "+yDay+" "+yYear);

	//si el año de la fecha ingresada es menor a la fecha actual
    if (xYear>yYear)  
    {  
        return(true)  
    }  
    else  
    {  
      if (xYear == yYear)  
      {  
		//si el mes de la fecha ingresada es menor  a la fecha actual
        if (xMonth> yMonth)  
        {  
            return(true)  
        }  
        else  
        {   
			//si el mes ingresado y el actual son iguales			
          if (xMonth == yMonth)  
          {  
			//si el dia de la fecha ingresada es menor a la de la fecha actual
            if (xDay> yDay)  
              return(true);  
            else  
              return(false);  
          }  
		  
          else  
            return(false);  
        }  
      }
	  //si el año de la fecha ingresada es mayor a la actual	  
      else  
        return(false);  
    }  
} 

//retorna la cantidad de dias del mes ingresado
function daysInMonth(humanMonth, year) {
  return new Date(year || new Date().getFullYear(), humanMonth, 0).getDate();
}

function valida_fechas(fecha, fecha2)
{

    var xMonth=( fecha.substring(0, fecha.indexOf("/")) ); 

    var xDay=( fecha.substring( fecha.indexOf("/")+1,fecha.lastIndexOf("/")) ); 
    var xYear=parseInt( fecha.substring(fecha.lastIndexOf("/")+1,fecha.length));  

	//datos fecha fin
    var yMonth=( fecha2.substring(0, fecha2.indexOf("/")) );  
    var yDay=( fecha2.substring( fecha2.indexOf("/")+1,fecha2.lastIndexOf("/")) ); 
    var yYear=parseInt( fecha2.substring(fecha2.lastIndexOf("/")+1,fecha2.length) );  

//alert ("fecha incial: "+xMonth+" "+xDay+" "+xYear+" fecha final: "+yMonth+" "+yDay+" "+yYear);
//length


	//valida que la fecha ingresada no tenga menos de 10 caracteres 
	if((fecha.length<10) || (fecha.length<10))
		return(true);
	else if( (isNaN(xMonth))||(isNaN(yMonth)) || (isNaN(yYear)) || (isNaN(xYear)) || (isNaN(yDay)) || (isNaN(xDay)) )	//valida que las partes de la fecha ingresada sean numeros
		return(true);
	else if((xMonth>12) || (xYear.length>4) || (xYear.length<4) || (xDay>31) || (xMonth.length<2)|| (xDay.length<2))	//validamos las fechas ingresadas final e inicio/ y si los dias indicados, son inferiores o iguales a la cantidad de dias del mes
	{
		return(true);
	} 
	else if((yMonth>12) || (yYear.length>4) || (yYear.length<4) || (yDay>31)|| (yMonth.length<2)|| (yDay.length<2))
		return(true);
	else if((xMonth>12) || (xYear.length>4) || (xYear.length<4) || (xDay>31)|| (xMonth.length<2)|| (xDay.length<2))
		return(true);
	else if(xDay>daysInMonth(xMonth, xYear)) //si el dia ingresado es superior a la cantidad de dias del mes
			return(true);
	else if(yDay>daysInMonth(yMonth, yYear)) //si el dia ingresado es superior a la cantidad de dias del mes
			return(true);
	else
		return(false);

}
		
		function envia2()
		{ 

			var pCantItems=1*document.Form1.pCantItems.value;
			var z=1; // var fechaI[1]="fechaI1";
			var error="no"; //valida si alguna fehca esta mal 
			var fecha_inicio_div,fecha_final_div;

//alert(pCantItems+"----");

			for (i=1;i<=pCantItems;i++)
			{

				//generamos las variables fechaf y fechai, para referenciarlos al momento de comparar los valores
			 	window['fechaf']='fechaF'+i;
				window['fechai']='fechaI'+i;
				window['macros']='macro'+i;
				window['nivels']='nivel'+i;
//alert(i+" ----- "+document.getElementById(fechai).value+'*********************'+document.getElementById(fechaf).value);
/*
				//cuando el nivel de la actividad es 3, se almacena la fecha de ingreso y la de finalizacion, para validar la fecha de las actividades, que estan por debajo				if(document.getElementById(nivels).value==3)
				{
					fecha_inicio_div=document.getElementById(fechai).value;
					fecha_final_div=document.getElementById(fechaf).value;
				}
*/
				//si alguna de las fecha continen informacion entonces evalua los campos de las fechas
				if((document.getElementById(fechai).value!='') || (document.getElementById(fechaf).value!=""))
				{
	
					//compara si la fecha de inicio esta vacia y si la de finalizacion no lo esta
					if((document.getElementById(fechai).value=='') && (document.getElementById(fechaf).value!=""))
					{
							alert("Por favor diligencie la fecha de inicio  en la actividad "+document.getElementById(macros).value);
							error="si";
							break;
	
					}
					//compara si la fecha de fin esta vacia y si la de inicio no lo esta
					else if((document.getElementById(fechai).value!='') && (document.getElementById(fechaf).value==""))
					{
							alert("Por favor diligencie la fecha de finalizaci\xf3n  en la actividad "+document.getElementById(macros).value);
							error="si";
							break;
					}
					else
					{

						if (valida_fechas(document.getElementById(fechai).value, document.getElementById(fechaf).value))
						{  
						  alert("Por favor verifique las fechas de inicio y de finalizaci\xf3n de la actividad "+document.getElementById(macros).value+" ");
							error="si";
							break;
	//, por favor verifique las fechas ingresadas
						}
						//valida que la fecha de inicio, no sea superior a la fecha de finalizacion
						else if (compare_fecha(document.getElementById(fechai).value, document.getElementById(fechaf).value,1))
						{  
						  alert("La fecha indicada para el inicio del la actividad "+document.getElementById(macros).value+" no debe ser superior a la fecha de finalización");
							error="si";
							break;
	//, por favor verifique las fechas ingresadas
						}
						//valida que la fecha de inicio de la  actividad, no sea inferior a la fecha de inicio del proyecto
						else if (compare_fecha("<? echo $datos_fecha_ini_proyecto[mes]."/".$datos_fecha_ini_proyecto[dia]."/".$datos_fecha_ini_proyecto[ano]; ?>",document.getElementById(fechai).value,2))
						{  
						  alert("La fecha indicada para el inicio del la actividad "+document.getElementById(macros).value+" no debe ser inferior a la fecha de inicio del proyecto");
							error="si";
							break;
	//, por favor verifique las fechas ingresadas
						}
/*
						//si la actividade es de  nivel 4, compara las fechas de esta, contra el de las divisiones
						else if(document.getElementById(nivels).value==4)
						{
							if(compare_fecha(fecha_inicio_div,document.getElementById(fechai).value,3))
							{
								alert("La fecha indicada para el inicio del la actividad "+document.getElementById(macros).value+" no debe ser inferior a la fecha de inicio de la división");
								error="si";
								break;
							}


							if(compare_fecha(document.getElementById(fechaf).value,fecha_final_div,4))
							{
								alert("La fecha final de la actividad "+document.getElementById(macros).value+" no debe ser superior a la fecha de finalizaci\xf3n de la división");
								error="si";
								break;
							}
							
						}
*/
						else
						{

//							return true;
						}
	
						//valida si la fecha de inicio y de fin fue ingresada de forma correcta
					}
				}

			}

			if(error=="no")
			{

					document.Form1.recarga.value="2";
					document.Form1.submit();
			}

		}

</script>
<? //echo $datos_fecha_ini_proyecto[mes]."/".$datos_fecha_ini_proyecto[dia]."/".$datos_fecha_ini_proyecto[ano];
?>
<title>.:: Planeaci&oacute;n de Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript" src="calendar.js"></script>
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<form action="" method="post"  name="Form1">

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Divisi&oacute;n / Actividad</td>
  </tr>
</table>




<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">

  <tr>
    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
	<td	class="TituloTabla2" width="40%" >
		Fecha de Inicio del proyecto (mes/dia/a&ntilde;o)
	</td>

	<td class="TxtTabla"><?  if($datos_fecha_ini_proyecto[fechaInicio]!=""){ echo date("m/d/Y", strtotime( $datos_fecha_ini_proyecto[mes]."/".$datos_fecha_ini_proyecto[dia]."/".$datos_fecha_ini_proyecto[ano])); } ?></td>
  </tr>
        <tr>
          <td></td>
        </tr>
      </table>      
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">

          <tr class="TituloTabla2">
            <td width="3%">ID</td>
            <td width="5%">Macroactividad</td>
            <td>Lote de control / Lote de trabajo / Actividad Vs Divisi&oacute;n </td>
            <td width="15%">Responsable</td>
            <td width="10%">Valor Presupuestado </td>

            <td width="8%" colspan="2">Fecha  Inicio <br>
            (mm/dd/aaaa) </td>
            <td width="8%" colspan="2">Fecha  Fin <br>
            (mm/dd/aaaa) </td>
          </tr>
			
<?php
			//permite identificar si el usuario logueado, es el director, coordinador, ordenador
			$valor=0;

			//consulta la unidad del director y coordinador de proyecto
			$sql_coor_direc="SELECT id_director,id_coordinador FROM  HojaDeTiempo.dbo.proyectos where id_proyecto = ".$cualProyecto;
			$cur_coor_direc=mssql_query($sql_coor_direc);
			while($datos_coor_direc=mssql_fetch_array($cur_coor_direc))
			{
				//si la unidad de session corresponde a la unidad del director o coordinador
				if($datos_coor_direc["unidad"]==$_SESSION["sesUnidadUsuario"])
					$valor=1;

			}

			if($valor==0)
			{
				//consulta los ordenadores de gasto del proyecto
				$sql_ordenador="select unidadOrdenador from GestiondeInformacionDigital.dbo.OrdenadorGasto where id_proyecto=".$cualProyecto;
				$cur_ordenador=mssql_query($sql_ordenador);
				while($datos_ordenador=mssql_fetch_array($cur_ordenador))
				{
					//si la unidad de session corresponde a la unidad de un ordenadro
					if($datos_ordenador["unidad"]==$_SESSION["sesUnidadUsuario"])
						$valor=1;
				}	
			}



			//consulta las actividades del proyecto ,fecha_inicio
			/*
			$sql_actividad="Select A.*, convert(varchar, fecha_fin, 101) as fecha_f,convert(varchar, fecha_inicio, 101) as fecha_i, U.nombre nomUsu, U.apellidos apeUsu  from Actividades A, Usuarios U  where A.id_encargado *= U.unidad  
						and A.id_proyecto =".$cualProyecto."  ";
			*/

			//------------------
			$sql_actividad="SELECT (valMacro *  factor) miOrden, *, convert(varchar, fecha_fin, 101) as fecha_f,convert(varchar, fecha_inicio, 101) as fecha_i ";
			$sql_actividad=$sql_actividad." FROM ";
			$sql_actividad=$sql_actividad." 	( ";
			$sql_actividad=$sql_actividad." 	Select ";
			$sql_actividad=$sql_actividad." 	CAST(REPLACE((REPLACE((SUBSTRING(A.macroactividad, 3, LEN(A.macroactividad))),'.','')),'A','') AS int) valMacro, ";
			$sql_actividad=$sql_actividad." 	factor = ";
			$sql_actividad=$sql_actividad." 		case nivel ";
			$sql_actividad=$sql_actividad." 			when 1 then 100000 ";
			$sql_actividad=$sql_actividad." 			when 2 then 10000 ";
			$sql_actividad=$sql_actividad." 			when 3 then 1000 ";
			$sql_actividad=$sql_actividad." 			when 4 then 100 ";
			$sql_actividad=$sql_actividad." 		end, A.*, U.nombre nomUsu, U.apellidos apeUsu ";
			$sql_actividad=$sql_actividad." 	from Actividades A, Usuarios U ";
			$sql_actividad=$sql_actividad." 	where A.id_encargado *= U.unidad ";
			$sql_actividad=$sql_actividad." 	and A.id_proyecto = " . $cualProyecto;
			if (trim($lstLC) != "") {
				$sql_actividad=$sql_actividad." and A.actPrincipal = " . $lstLC;
			}
			$sql_actividad=$sql_actividad." 	) Z ";
			if ($verBoton=="NO") {
				$sql_actividad=$sql_actividad." WHERE id_actividad in ";
				$sql_actividad=$sql_actividad." 	( ";
				$sql_actividad=$sql_actividad." 	SELECT id_actividad ";
				$sql_actividad=$sql_actividad." 	FROM Actividades ";
				$sql_actividad=$sql_actividad." 	WHERE id_proyecto =  " . $cualProyecto;
				$sql_actividad=$sql_actividad." 	and id_encargado = " . $laUnidad ;
				$sql_actividad=$sql_actividad." 	UNION ";
				$sql_actividad=$sql_actividad." 	SELECT id_actividad ";
				$sql_actividad=$sql_actividad." 	FROM ResponsablesActividad ";
				$sql_actividad=$sql_actividad." 	WHERE id_proyecto =  " . $cualProyecto;
				$sql_actividad=$sql_actividad." 	AND unidad = " . $laUnidad ;
				$sql_actividad=$sql_actividad." 	) ";
			}
			$sql_actividad=$sql_actividad." order by (valMacro *  factor) ";
//--------------

						

			//si no es el director, coordinador, ordenador de proyecto, pueden ver solo las actividades a las que esta sociado

//			if($valor==0)  /***TEMPORAL PARA ACTIVAR
			{
//				$sql_actividad=$sql_actividad." and id_encargado=".$_SESSION["sesUnidadUsuario"];
			}
// /***TEMPORAL PARA ACTIVAR
			//$sql_actividad=$sql_actividad."order by SUBSTRING(A.macroactividad, 3, LEN(A.macroactividad))";
			$cur_actividad=mssql_query($sql_actividad);
			while($datos_actividad=mssql_fetch_array($cur_actividad))
			{				

?>
                  <tr class="TxtTabla">
                    <td width="3%"  ><?php echo $datos_actividad["id_actividad"] ?></td>
                    <td width="5%" >
<?php
					//solo a las actividades se le puede asignar la fecha de inicio y de fin
//					if(($datos_actividad["nivel"]==3)or ($datos_actividad["nivel"]==4))
					if(($datos_actividad["nivel"]==4))
					{
						$w++;
?>
				        <input name="macro<? echo $w; ?>" type="hidden" id="macro<? echo $w; ?>" value="<?php echo $datos_actividad["macroactividad"] ?>">
				        <input name="id_activi<? echo $w; ?>" type="hidden" id="id_activi<? echo $w; ?>" value="<?php echo $datos_actividad["id_actividad"] ?>">
						<?php
					}		 echo $datos_actividad["macroactividad"] ?>
					</td>
                    <td>

                        <table width="100%"  border="0" cellspacing="1" cellpadding="0">
                          <tr>
                            <? if (trim($datos_actividad[nivel]) == 2) { ?>
                            <td width="3%">&nbsp;</td>
                            <? } ?>
                            <? if (trim($datos_actividad[nivel]) == 3) { ?>
                            <td width="3%">&nbsp;</td>
                            <td width="3%">&nbsp;</td>
                            <? } ?>
                            <? if (trim($datos_actividad[nivel]) >= 4) { ?>
                            <td width="3%">&nbsp;</td>
                            <td width="3%">&nbsp;</td>
                            <td width="3%">&nbsp;</td>
                            <? } ?>
                            <td class="TxtTabla">

<?php
						if($datos_actividad["nivel"]==1)
						{
	?>
							<B><?php echo $datos_actividad["nombre"] ?></B>
	<?php
						}
						else
						{
							if ( (trim($datos_actividad[nivel]) == 2)) {
								echo strtoupper($datos_actividad[nombre]) ; 
							}
							else if(trim($datos_actividad[nivel]) == 3)
							{
								echo strtoupper($datos_actividad[nombre]) ; 
							}
							else {
								echo ucfirst(strtolower($datos_actividad[nombre]) ) ; 
							}
						}
?>
                            </td>
                          </tr>
                        </table>	

					</td>
                    <td width="15%">
					<?php
						//consulta la informacion del encargado
						$sql_encargado="select nombre,apellidos,unidad from HojaDeTiempo.dbo.Usuarios where unidad=".$datos_actividad["id_encargado"]." ";
						$cur_encargado=mssql_query($sql_encargado);
						while($datos_encargado=mssql_fetch_array($cur_encargado))
						{
							echo "[".$datos_encargado["unidad"]."]"." ".$datos_encargado["nombre"]." ".$datos_encargado["apellidos"];
						}
					?>
					</td>
                    <td width="10%" align="right">
<?php
					//solo a las actividades y divisiones, son las unicas a las que se les asinga dinero
						if(($datos_actividad["nivel"]==3)or ($datos_actividad["nivel"]==4))
									if($datos_actividad["valor"]!="")
									 echo "$ ". number_format($datos_actividad["valor"], "2", ",", "."); 
?>
					</td>


<?php
					//solo a las actividades, se le puede asignar la fecha de inicio y de fin
//					if(($datos_actividad["nivel"]==3)or ($datos_actividad["nivel"]==4))
					if(($datos_actividad["nivel"]==4))
					{
						$r++;
?>
                        <td width="8%" align="center" class="TxtTabla">
                          <input name="fechaI<?php echo $r; ?>" id="fechaI<?php echo $r; ?>" type="text" class="CajaTexto" onKeyPress="return acceptNum(event)" maxlength="10" value="<?php echo $datos_actividad["fecha_i"]; ?>" ></td>
                        <td width="8%" align="center" class="TxtTabla">
						<a href="javascript:cal<? echo $r; ?>.popup();">
						
						<img src="imagenes/cal.gif" alt="Calendario" width="16" height="16" border="0" ></a></td>
                        <td width="8%" align="center" class="TxtTabla"><input name="fechaF<?php echo $r; ?>" id="fechaF<?php echo $r; ?>" onKeyPress="return acceptNum(event)" maxlength="10" type="text" class="CajaTexto" value="<?php echo $datos_actividad["fecha_f"]; ?>" ></td>
                    <td width="8%" align="center" class="TxtTabla">
							<a href="javascript:calb<? echo $r; ?>.popup();"><img src="imagenes/cal.gif" alt="Calendario" width="16" height="16" border="0" ></a>

				        <input name="nivel<? echo $r; ?>" type="hidden" id="nivel<? echo $r; ?>" value="<?php echo $datos_actividad["nivel"] ?>">
					</td>          
<?php
					}
					else
					{
						echo "        			
                        <td width='8%' align='center' class='TxtTabla'> <input type='hidden' name='hiddenField' id='hiddenField'> </td>
                        <td width='8%' align='center' class='TxtTabla'></td>
                        <td width='8%' align='center' class='TxtTabla'></td>
                        <td width='8%' align='center' class='TxtTabla'></td>          
<?php
							";
					}
?>
          </tr>
<?php
			}
	
?>
        </table>
        <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td align="right" class="TxtTabla"><?php //echo $w."  --  ".$r; ?>
  		    <input name="recarga" type="hidden" id="recarga" value="1">
            <input name="pCantItems" type="hidden" id="pCantItems" value="<? echo $r; ?>">
  		    <input name="Submit" type="button" class="Boton" value="Guardar" onClick="envia2()" ></td>
        </tr>
      </table>
      </td>
  </tr>
</table>

	     </td>
         </tr>
         </table>
</form> 
<script language="JavaScript">
	 <? for ($i=1; $i <= $r; $i++) { ?>
		 var cal<? echo $i; ?> = new calendar2(document.forms['Form1'].elements['fechaI<? echo $i; ?>']);
		 cal<? echo $i; ?>.year_scroll = true;
		 cal<? echo $i; ?>.time_comp = false;
		 
		 var calb<? echo $i; ?> = new calendar2(document.forms['Form1'].elements['fechaF<? echo $i; ?>']);
		 calb<? echo $i; ?>.year_scroll = true;
		 calb<? echo $i; ?>.time_comp = false;      
    <? } ?>
	  
</script>
</body>
</html>

<? mssql_close ($conexion); ?>	
