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
	//Realiza la grabación en Muestra
	$msgGraba = "";
	$msgNOGraba = "";
	$s = 1;
	
	while ($s <= $pCantReg) {
		//Generar la secuencia  del lote de trabajo
		//trahemos el id de la actividad mas actual, para asi incluirlos en le insert, y asociarlo en el campo niveles activ
		$sigienteSec =0;
		$sqlId = " select MAX(id_actividad) as elMax from Actividades where id_proyecto=".$cualProyecto; //. $_SESSION["sesProyLaboratorio"] ;
		$cursorId = mssql_query($sqlId);
		if($regId = mssql_fetch_array($cursorId))
		{
			//y incrementamos en 1, el resultado, por que se asume, que este valore, sera el nuevo id_actividad, del registro a insertar
			$sigienteSec = $regId["elMax"] + 1;
		}
//echo $sqlId."<br>".mssql_get_last_message()."<br>"; 				
		//Recoger las variables
		$elpNombre = "pNombre" . $s;
		$pLT = "pLT" . $s;
		$elpJefe = "pJefe" . $s;
		$Lote_Control=$LC;

		//extrehmos el numero del lote de control (LC1), para componer los nombres de los lotes de trabajo (LT1.N)
		$num_lote_control = substr($Lote_Control,2,strlen($Lote_Control));

//echo ${$elpJefe}."  ---<br>";
		//id_proyecto, codLoteControl, nomLoteControl, siglaLC, usuarioCrea, fechaCrea, usuarioMod, fechaMod
		//EnsayosProyLC
		$sqlIn1 = " INSERT INTO Actividades";
		$sqlIn1 = $sqlIn1 . "( id_proyecto, id_actividad, nombre, macroactividad, ";
		if(trim(${$elpJefe})!="")
		{
				$sqlIn1=$sqlIn1."id_encargado,";
		}
		$sqlIn1=$sqlIn1."dependeDe, actPrincipal, tipoActividad, nivelesActiv, nivel,usuarioCrea,fechaCrea ) ";
		$sqlIn1 = $sqlIn1 . " VALUES ( ";
		$sqlIn1 = $sqlIn1 . " ".$cualProyecto.", ";
		$sqlIn1 = $sqlIn1 . " " . $sigienteSec . ", ";



		$sqlIn1 = $sqlIn1 . "  UPPER('" . ${$elpNombre} . "'), ";
		$sqlIn1 = $sqlIn1 . " '" . ${$pLT} . "', ";
		if(trim(${$elpJefe})!="")
		{
			$sqlIn1 = $sqlIn1 . " " . ${$elpJefe} . ", ";
		}
		$sqlIn1 = $sqlIn1 .$Lote_Control.", ";
		$sqlIn1 = $sqlIn1 .$Lote_Control.", ";
		$sqlIn1 = $sqlIn1 . " '2', ";

		$sqlIn1 = $sqlIn1 . " '" .$Lote_Control."-".$sigienteSec . "', ";
		$sqlIn1 = $sqlIn1 . " '2', ";
		$sqlIn1 = $sqlIn1 .$_SESSION["sesUnidadUsuario"].",";
		$sqlIn1 = $sqlIn1 ."getdate()";
		$sqlIn1 = $sqlIn1 . " ) ";
//		$sqlIn1 = $sqlIn1 . " '". gmdate ("n/d/Y") . "', ";
//		$sqlIn1 = $sqlIn1 . " '" . $_SESSION["sesUnidadUsuario"] . "' ";

		$cursorIn1 = mssql_query($sqlIn1);

		if  (trim($cursorIn1) != "")  {
			//echo "entro eal if 2" . "<br>"; elpSigla
			$msgGraba=$msgGraba."[".${$pLT}."] " ;
		}
		else {
			//echo "entro al else " . "<br>";
			$msgNOGraba=$msgNOGraba."[".${$pLT}."] " ; 
		}
//echo $sqlIn1."<br>".mssql_get_last_message()."<br>"; 				
		$s = $s + 1;
	}

	//Si los cursores no presentaron problema
	//if  (trim($cursorIn1) != "")  {
	if  (trim($msgNOGraba) != "")  {
		echo ("<script>alert('No se grabaron los siguientes Lotes de control: $msgNOGraba ');</script>"); 
	} 
	
	if  (trim($msgGraba) != "")  {
		echo ("<script>alert('Se grabaron los siguientes Lotes de trabajo: $msgGraba ');</script>"); 
	} 
	else {
		echo ("<script>alert('Error durante la grabación');</script>");
	}
	echo ("<script>window.close();MM_openBrWindow('htPlanProyectos01.php?cualProyecto=".$cualProyecto."&aa=".$Lote_Control."','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");

}

?>

<html>
<head>
<script language="JavaScript" type="text/JavaScript">
<!--
/// no permite el ingreso de numevo en la cantidad de lotes de trabajo
var nav4 = window.Event ? true : false;
function acceptNum(evt)
{   
	var key = nav4 ? evt.which : evt.keyCode;   
	return (key <= 13 || (key>= 48 && key <= 57));
}

function acceptComilla(evt){   
var key = nav4 ? evt.which : evt.keyCode;   

return (key != 39);
}

function envia1(){ 
//alert ("Entro a envia 1");
document.Form1.recarga.value="1";
document.Form1.submit();
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


CantCampos=2+(2*document.Form1.pCantReg.value);

	
//Valida que el campo Nombre no esté vacio
for (i=3;i<=CantCampos;i+=3) {
	if (document.Form1.elements[i].value == '') {
		v2='n';
		msg2 = 'El nombre de cada uno de los lotes es obligatorio. \n';
	}
	if(document.Form1.elements[i+1].value=="")
	{
		v2='n';
		msg2 =msg2+'Seleccione un responsable en cada uno de los lotes \n';
	}


}
if(document.Form1.LC.value=='')
{
	msg2='Seleccione un lote de control.';
//	alert('Seleccione un lote de control.');
	v2='n';
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


	
?>
<title>.:: Planeaci&oacute;n de Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">

<script language="JavaScript" src="calendar.js"></script>
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<form action="" type="post"   name="Form1">

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Lotes de Trabajo</td>

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
          <td class="TituloTabla">Lote de Control</td>
          <td class="TxtTabla">
<?php
					//si la variable no esta definida, es por que es la primera vez que se carga la pagina, y asi, el selecct quedara por defecto, con el LC seleccionado por el usuario, en la
					//ventana anterior
					if(!isset($cualLC2))
					{
							$cualLC2=$cualLC;
					}
					//validamos si la variable si la variable que se forma del  select, esta definida, si no lo esta, es por que es la primera vez que se accede a la pagina
					//entonces  cargamos la variable para cargar el select, del los lotes de trabajo 
					if(!isset($LC))
					{
							$LC=$cualLC;
					}
?>
				<select name="LC" id="LC"   class="CajaTexto" onChange="document.Form1.submit();">
				<option value=""> </option>

<?php
					$LC_selec=""; //almacenamos la macro actividad del lote de control seleccionado, para despues usarlo, para compner las macros de los lotes de trabajo
					$sql_LC="SELECT  id_actividad,nombre,macroactividad FROM Actividades WHERE id_proyecto = ".$cualProyecto." and nivel = 1 ";
					$sql_LC=$sql_LC." order by cast(reverse(substring(reverse(macroactividad),1,charindex('C', reverse(macroactividad))-1)) as int)";
					$cursor_sql_LC=mssql_query($sql_LC);

					while($datos_sql_LC=mssql_fetch_array($cursor_sql_LC))
					{
						//pertmite determinar el LC seleccionado por el usuario en la pagina, y seleccionarlo en la lista de forma automatica, esto en el momento de abrir la pagina
						//y despues, se seleccionara el que el usuario escoga en el select
						if($cualLC2==$datos_sql_LC["id_actividad"])
						{
							$cualLC2=-1;  //modifiacmos el valor, para que al momento de seleccionar otro elemento de la lista, este me lo deje  seleccionado
							$select="selected";
							$LC_selec=$datos_sql_LC["macroactividad"];
						}
						else
						{
							if($LC==$datos_sql_LC["id_actividad"])
							{
								$select="selected";
								$LC_selec=$datos_sql_LC["macroactividad"];
							}
						}
						echo "<option value=".$datos_sql_LC["id_actividad"]." $select >".$datos_sql_LC["macroactividad"]." - ".$datos_sql_LC["nombre"]."</option>";
						$select="";
					}

 ?>
				</select>
		  </td>
        </tr>
        <tr>
          <td width="20%" class="TituloTabla">Cantidad de Lotes de Trabajo</td>
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
          <td class="TituloTabla2">LOTES DE TRABAJO </td>
        </tr>
      </table>      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td width="5%">Identificador</td>
        <td>Nombre</td>
        <td>Responsable</td>
        </tr>
	  <?
		//Obtenemos el numero del lote de control seleccionado, para componer los nuevos lotes de trabajo
		$num_lote_control = substr($LC_selec,2,strlen($LC_selec));
		//consultamos el lote de trabajo mas actual de el lote de control seleccionado, y trahemos el numero despues del ".", para incrementarlo, y asi ajustarlos a los nuevos lotes de trabajo que se crearan
		$sql_num_max_lt="select MAX(lote) as lt_max from ( select CAST( SUBSTRING(macroactividad, CHARINDEX ('.',macroactividad)+1, LEN(macroactividad)) as int) AS lote FROM Actividades WHERE id_proyecto=".$cualProyecto." and dependeDe=".$LC." and nivel = 2 ) A";
		$cursor_num_max_lt=mssql_query($sql_num_max_lt);
//echo $sql_num_max_lt."<br>";
		if($dato_num_max_lt=mssql_fetch_array($cursor_num_max_lt))
			$num_max_lt=$dato_num_max_lt["lt_max"];
		else 
			$num_max_lt=0;

	  $r = 1;
	  $nuevoCodigo= 38;
	  while ($r <= $pCantReg) {
	  ?>
      <tr class="TxtTabla">
        <td width="5%" align="center"><input name="pLT<? echo $r; ?>" type="text" class="CajaTexto" id="pLT<? echo $r; ?>"  size="13" readonly value="<?php echo "LT".$num_lote_control.".".++$num_max_lt; ?>" ></td>
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
		$sql2=$sql2." and left(C.nombre,2) < 40 ";
		$sql2=$sql2." and  C.id_categoria <= 5 ";
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
  		    <input name="cualLC2" type="hidden" id="cualLC2" value="-1">
  		    <input name="cualProyecto" type="hidden" id="cualProyecto" value="<?php echo $cualProyecto; ?>">


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
