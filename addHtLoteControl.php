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


//Cantidad de registros del formulario
if (trim($pCantReg) == "") {
	$pCantReg = 1;
}


if(trim($recarga) == "2"){

	//CONSULTA EL MAXIMO VALOR DEL CAMPO erpLC, EN LOS LOTES DE CONTROL DEL PROYECTO
	$sql_cant_lotes="select MAX(erpLC) lc_total from Actividades where id_proyecto=".$cualProyecto." and nivel=1  ";
	$cur_cant_lotes=mssql_query($sql_cant_lotes);
	$datos_lotes=mssql_fetch_array($cur_cant_lotes);

	$can_lc=( (int) $datos_lotes["lc_total"] ) + $pCantReg;
	$num_lote_control=( (int) $datos_lotes["lc_total"] ) +1;
//echo $can_lc." -*********** ".$pCantReg;
	//LA CANTIDAD DE LOTES DE CONTROL, NO PUEDE SER SUPERIOR A 99
	if($can_lc<=99)
	{
		//Realiza la grabación en Muestra
		$msgGraba = "";
		$msgNOGraba = "";
		$s = 1;
		while ($s <= $pCantReg) {
			//Generar la secuencia  del lote de control
			//id_proyecto, codLoteControl, nomLoteControl, siglaLC, usuarioCrea, fechaCrea, usuarioMod, fechaMod
			//EnsayosProyLC
			$sigienteSec =0;
			$sqlId = " select MAX(id_actividad) as elMax from Actividades where id_proyecto=".$cualProyecto; //. $_SESSION["sesProyLaboratorio"] ;
			$cursorId = mssql_query($sqlId);
			if($regId = mssql_fetch_array($cursorId))
			{
				//y incrementamos en 1, el resultado, por que se asume, que este valore, sera el nuevo id_actividad, del registro a insertar
				$sigienteSec = $regId["elMax"] + 1;
			}
			


			//Recoger las variables
			$elpNombre = "pNombre" . $s;
			$pLC = "pLC" . $s;
			$elpJefe = "pJefe" . $s;

			//SI EL NUMERO ES MENOR A 10, SE AÑADE EL 0 , PARA EL AREA DE NEGOCIO (LC) EN EL ERP
			if( ((int) $num_lote_control )<10)
				$num_lote_control ='0'.$num_lote_control ;

//echo $num_lote_control." *-------------- ";
//echo strlen(${$pLC})." ------------ ".${$pLC}."<br>";
	//echo ${$elpJefe}."  ---<br>";
			//id_proyecto, codLoteControl, nomLoteControl, siglaLC, usuarioCrea, fechaCrea, usuarioMod, fechaMod
			//EnsayosProyLC
			$sqlIn1 = " INSERT INTO Actividades";
			$sqlIn1 = $sqlIn1 . "( id_proyecto, id_actividad,  nombre, macroactividad, ";
			if(trim(${$elpJefe})!="")
			{
					$sqlIn1=$sqlIn1."id_encargado,";
			}
			$sqlIn1=$sqlIn1."dependeDe, actPrincipal, tipoActividad, nivelesActiv, nivel,erpLC,usuarioCrea,fechaCrea ) ";
			$sqlIn1 = $sqlIn1 . " VALUES ( ";
			$sqlIn1 = $sqlIn1 . " ".$cualProyecto.", ";
			$sqlIn1 = $sqlIn1 . " " . $sigienteSec . ", ";
	
	
	
			$sqlIn1 = $sqlIn1 . "  UPPER('" . ${$elpNombre} . "'), ";
			$sqlIn1 = $sqlIn1 . " '" . ${$pLC} . "', ";
			if(trim(${$elpJefe})!="")
			{
				$sqlIn1 = $sqlIn1 . " " . ${$elpJefe} . ", ";
			}
			$sqlIn1 = $sqlIn1 . "0, ";
			$sqlIn1 = $sqlIn1 . " " . $sigienteSec . ", ";
			$sqlIn1 = $sqlIn1 . " '1', ";
	
			$sqlIn1 = $sqlIn1 . " '" . $sigienteSec . "', ";
			$sqlIn1 = $sqlIn1 . " '1' , ";

			$sqlIn1 = $sqlIn1 . " '".$num_lote_control."' , ";

			$sqlIn1 = $sqlIn1 .$_SESSION["sesUnidadUsuario"].",";
			$sqlIn1 = $sqlIn1 ."getdate()";
			$sqlIn1 = $sqlIn1 . " ) ";
	//		$sqlIn1 = $sqlIn1 . " '". gmdate ("n/d/Y") . "', ";
	//		$sqlIn1 = $sqlIn1 . " '" . $_SESSION["sesUnidadUsuario"] . "' ";
	
			$cursorIn1 = mssql_query($sqlIn1);
	
			if  (trim($cursorIn1) != "")  {
				//echo "entro eal if 2" . "<br>"; elpSigla
				$msgGraba=$msgGraba."[".${$pLC}."] " ;
			}
			else {
				//echo "entro al else " . "<br>";
				$msgNOGraba=$msgNOGraba."[".${$pLC}."] " ; 
			}
//echo $sqlIn1."<br>".mssql_get_last_message()."<br>"; 				
			$s = $s + 1;

			$num_lote_control++;
		}

		//Si los cursores no presentaron problema
		//if  (trim($cursorIn1) != "")  {
		if  (trim($msgNOGraba) != "")  {
			echo ("<script>alert('No se grabaron los siguientes Lotes de control: $msgNOGraba ');</script>"); 
		} 
		
		if  (trim($msgGraba) != "")  {
			echo ("<script>alert('Se grabaron las siguientes Lotes de control: $msgGraba ');</script>"); 
		} 
		else {
			echo ("<script>alert('Error durante la grabación');</script>");
		}
		echo ("<script>window.close();MM_openBrWindow('htPlanProyectos01.php?cualProyecto=".$cualProyecto."','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");

	}
	else //SI SE SUPERARON LA CANTIDAD DE LC, PERMITIDA (MAX 99)
	{
			echo ("<script>alert('No se puede registrar mas de 99 lotes de control.');</script>");
	}

}
	//CONSULTA EL VALOR MAXIMO DEL CAMPO erpLC EN LOS LOTES DE CONTROL DEL PROYETO
	$sql_cant_lotes="select MAX(erpLC) lc_total from Actividades where id_proyecto=".$cualProyecto." and nivel=1  ";
$cur_cant_lotes=mssql_query($sql_cant_lotes);
$datos_lotes=mssql_fetch_array($cur_cant_lotes);
if($datos_lotes["lc_total"]==99)
{
		echo ("<script>alert('No se puede registrar mas de 99 lotes de control.');</script>"); 

		echo ("<script>window.close();MM_openBrWindow('htPlanProyectos01.php?cualProyecto=".$cualProyecto."','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");
}

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"> 
<script language="JavaScript" type="text/JavaScript">
<!--

/// no permite el ingreso de numevo en la cantidad de lotes de control
var nav4 = window.Event ? true : false;
function acceptNum(evt)
{   
	var key = nav4 ? evt.which : evt.keyCode;   
	return (key <= 13 || (key>= 48 && key <= 57));
}


function envia1(){ 
//alert ("Entro a envia 1");
document.Form1.recarga.value="1";
document.Form1.submit();
}

function acceptComilla(evt){   
var key = nav4 ? evt.which : evt.keyCode;   

return (key != 39);
}

function envia2(){ 
var v1,v2,v3, v4,v5,v6, v7,v8,v9, i, CantCampos, msg1, msg2, msg3, msg4, msg5, msg6, msg7, msg8, msg9, msg10, msg11, msg12, msg13, msg14, msg15, mensaje;
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

CantCampos=1+(2*document.Form1.pCantReg.value);

	
//Valida que el campo Nombre no esté vacio
for (i=2;i<=CantCampos;i+=3) {
	if (document.Form1.elements[i].value == '') {
		v2='n';
		msg2 = 'El nombre de cada uno de los lotes es obligatorios. \n';
	}
}

/*
//Valida que el campo Sigla no esté vacio
for (i=2;i<=CantCampos;i+=2) {
	if (document.Form1.elements[i].value == '') {
		v3='n';
		msg3 = 'Sigla es obligatorio. \n'
	}
}
*/


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
//-->
</script>
<?php 
//$_SESSION["sesProyLaboratorio"]
	//trahemos el lote de control mas actual asociado a el  proyecto .$cualProyecto

	$sql_max_lc="select MAX(lote) as lc_max from (SELECT str(SUBSTRING(macroactividad,3, LEN(macroactividad))) AS lote FROM Actividades WHERE id_proyecto=".$cualProyecto." and nivel = 1 ) A";
	$cursor_max_lc=mssql_query($sql_max_lc);
	if($datos_max_lc=mssql_fetch_array($cursor_max_lc))
	{
		$lc_max=$datos_max_lc["lc_max"];
	}
	//si no se encontraron registros, es por que es el primer lote de control que se creara en el proyecto
	else
	{
		$lc_max=0;
	}
	
?>
<title>.:: Planeaci&oacute;n de Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">

<script language="JavaScript" src="calendar.js"></script>
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<form action="" method="post"  name="Form1">

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Lotes de control </td>
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
          <td width="20%" class="TituloTabla">Cantidad de Lotes de Control</td>
          <td class="TxtTabla"><input name="pCantReg" type="text" class="CajaTexto" id="pCantReg" value="<? echo $pCantReg; ?>" size="10" onKeyPress="return acceptNum(event)" onChange="envia1()"></td>
        </tr>
      </table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td height="5" class="TituloTabla"> </td>
        </tr>
      </table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td class="TituloTabla2">LOTES DE CONTROL </td>
        </tr>
      </table>      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td width="5%">Identificador</td>
        <td>Nombre</td>
        <td>Responsable</td>
        </tr>
	  <?
	  $r = 1;
	  $nuevoCodigo= 38;
	  while ($r <= $pCantReg) {
	  ?>
      <tr class="TxtTabla">
        <td width="5%" align="center"><input name="pLC<? echo $r; ?>" type="text" class="CajaTexto" id="pLC<? echo $r; ?>"  size="13" readonly value="<?php echo "LC".++$lc_max; ?>" ></td>
        <td align="center"><input name="pNombre<? echo $r; ?>" type="text" class="CajaTexto" id="pNombre<? echo $r; ?>"  size="70" onKeyPress=" return acceptComilla(event)" ></td>
        <td align="center">
		<select name="pJefe<? echo $r; ?>" class="CajaTexto" id="pJefe<? echo $r; ?>" >
		<option value="" ><? echo "   ";  ?></option>
            <?
		//Muestra todos los usuarios que podrían ser jefes, Categoria soobre 40. 
		$sql2="select U.*, C.nombre nomCategoria  " ;
		$sql2=$sql2." from usuarios U, Categorias C ";
		$sql2=$sql2." where U.id_categoria = C.id_categoria  ";
		$sql2=$sql2." and U.retirado is null ";
/*
		$sql2=$sql2." and left(C.nombre,2) < 40 ";
		$sql2=$sql2." and  C.id_categoria <= 5 ";
*/
		$sql2=$sql2." order by U.apellidos ";
		$cursor2 = mssql_query($sql2);
		while ($reg2=mssql_fetch_array($cursor2)) {
		?>
            <option value="<? echo $reg2[unidad]; ?>" ><? echo ucwords(strtolower($reg2[apellidos])) . ", " . ucwords(strtolower($reg2[nombre]));  ?></option>
            <? } ?>
          </select>

		</td>
        </tr>
		<? 
		$r = $r + 1;
		$nuevoCodigo = $nuevoCodigo + 1;

		} ?>
    </table>
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td align="right" class="TxtTabla">
  		    <input name="recarga" type="hidden" id="recarga" value="1">
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
</body>
</html>

<? mssql_close ($conexion); ?>	
