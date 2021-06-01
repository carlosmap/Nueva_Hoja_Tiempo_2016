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
		//id_proyecto, codLoteControl, nomLoteControl, siglaLC, usuarioCrea, fechaCrea, usuarioMod, fechaMod
		//EnsayosProyLC
		$sigienteSec =0;
		$sqlId = " select MAX(id_actividad) as elMax from Actividades where id_proyecto=".$cualProyecto; //. $_SESSION["sesProyLaboratorio"] ;
		$cursorId = mssql_query($sqlId);
		if($regId = mssql_fetch_array($cursorId)){
			$sigienteSec = $regId["elMax"] + 1;
		}

		//Recoger las variables
		$elpJefe = "pJefe" . $s;
		$elpAC = "pAC" . $s; //identifica el identificador de la actividad
		$elpNombre = "pNombre" . $s;


		$Lote_Control=$LC;
		$Lote_Trabajo=$LT;
		$Division=$DI;

//echo ${$elpJefe}."  ---<br>";
		//id_proyecto, codLoteControl, nomLoteControl, siglaLC, usuarioCrea, fechaCrea, usuarioMod, fechaMod
		//EnsayosProyLC
		$sqlIn1 = " INSERT INTO Actividades";
		$sqlIn1 = $sqlIn1 . "( id_proyecto, id_actividad, fecha_inicio, fecha_fin, nombre, macroactividad, ";
		if(trim(${$elpJefe})!="")
		{
				$sqlIn1=$sqlIn1."id_encargado,";
		}
		$sqlIn1=$sqlIn1."dependeDe, actPrincipal, tipoActividad, nivelesActiv, nivel,id_division ) ";
		$sqlIn1 = $sqlIn1 . " VALUES ( ";
		$sqlIn1 = $sqlIn1 . " ".$cualProyecto.", ";
		$sqlIn1 = $sqlIn1 . " " . $sigienteSec . ", ";

		$sqlIn1 = $sqlIn1 . " getdate(), ";
		$sqlIn1 = $sqlIn1 . " getdate(), ";

		$sqlIn1 = $sqlIn1 . "  UPPER('" . ${$elpNombre} . "'), ";
		$sqlIn1 = $sqlIn1 . " '" . ${$elpAC} . "', ";
		if(trim(${$elpJefe})!="")
		{
			$sqlIn1 = $sqlIn1 . " " . ${$elpJefe} . ", ";
		}
		$sqlIn1 = $sqlIn1 . $DI.", ";
		$sqlIn1 = $sqlIn1 . " " . $LC . ", ";
		$sqlIn1 = $sqlIn1 . " '4', ";

		$sqlIn1 = $sqlIn1 . " '" . $LC. "-".$LT."-".$DI."-A-".$sigienteSec." ', ";
		$sqlIn1 = $sqlIn1 . " '4' ";
		$sqlIn1 = $sqlIn1 . " ,".$divi_selec.") ";
//		$sqlIn1 = $sqlIn1 . " '". gmdate ("n/d/Y") . "', ";
//		$sqlIn1 = $sqlIn1 . " '" . $_SESSION["sesUnidadUsuario"] . "' ";

		$cursorIn1 = mssql_query($sqlIn1);

		if  (trim($cursorIn1) != "")  {
			//echo "entro eal if 2" . "<br>"; elpSigla
			$msgGraba=$msgGraba."[".${$elpAC}."] " ;
		}
		else {
			//echo "entro al else " . "<br>";
			$msgNOGraba=$msgNOGraba."[".${$elpAC}."] " ; 
		}
//echo $sqlIn1."<br>".mssql_get_last_message()."<br>"; 				
		$s = $s + 1;
	}

	//Si los cursores no presentaron problema
	//if  (trim($cursorIn1) != "")  {
	if  (trim($msgNOGraba) != "")  {
		echo ("<script>alert('No se grabaron las siguientes Actividades: $msgNOGraba ');</script>"); 
	} 
	
	if  (trim($msgGraba) != "")  {
		echo ("<script>alert('Se grabaron las siguientes Actividades: $msgGraba ');</script>"); 
	} 
	else {
		echo ("<script>alert('Error durante la grabación');</script>");
	}
	echo ("<script>window.close();MM_openBrWindow('htProgProyectos01.php?cualProyecto=".$cualProyecto."','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");

}

?>
 <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
<head>
<script language="JavaScript" type="text/JavaScript">
<!--

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

//CantCampos=1+(4*document.Form1.pCantReg.value);
CantCampos=3+(4*document.Form1.pCantReg.value);

//Valida que el campo valor no esté vacio
for (i=7;i<=CantCampos;i+=4) {

	if (document.Form1.elements[i].value == '') {
		v2='n';

		msg2 = 'El valor asignado a cada actividad es obligatorio.'; //'Asigne un valor a cada una de las divisiones. \n';
//+document.Form1.pLCi.value
	}
}	

//Valida que el campo Nombre no esté vacio
for (i=5;i<=CantCampos;i+=4) {
	if (document.Form1.elements[i].value == '') {
		v2='n';
		msg2 = 'El nombre de cada uno de los lotes es obligatorio. \n';
	}
}



if(document.Form1.DI.value=="")
{
	msg2 = 'Seleccione una división.';
	v2='n';
}

if(document.Form1.LT.value=="")
{
	msg2 = 'Seleccione un lote de trabajo.';
	v2='n';
}

if(document.Form1.LC.value=="")
{
	msg2 = 'Seleccione un lote de control.';
	v2='n';
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
}
//-->
</script>

<title>Programaci&oacute;n de Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">

<script language="JavaScript" src="calendar.js"></script>
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<form action="" method="post"  name="Form1">

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Lote de control - Lote de trabajo - Divisi&oacute;n - Actividad</td>
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
          <td class="TituloTabla">Lote de control </td>

<?php

					//si la variable no esta definida, es por que es la primera vez que se carga la pagina, y asi, el selecct quedara por defecto, con el LC al cual pertenece el LT seleccionado por el usuario, en la
					//ventana anterior, la cual trahe como parametro el valor del LC, perteneciente al LT
					if(!isset($cualLC2))
					{
							$cualLC2=$cualLC;
					}
					//Cargamos el valor del LT, que se trahe como parametro, esta sentencia es verdadera, cuando se accede a la pagina por primera vez
					if(!isset($cualLT2))
					{
							$cualLT2=$cualLT;
					}
					//validamos si la variable que se forma del  select, esta definida, si no lo esta, es por que es la primera vez que se accede a la pagina
					//entonces  cargamos la variable, enviada como parametro, para cargar el select, del los lotes de trabajo 
					if(!isset($LC))
					{
							$LC=$cualLC;
					}
					if(!isset($LT))
					{
							$LT=$cualLT;
					}
					if(!isset($DI))
					{
							$DI=$cualDiv;
					}
//echo $LT." - ".$LC." - ".$cualLC2." - ".$cualLT2;
?>

          <td class="TxtTabla">
		<select name="LC" id="LC"   class="CajaTexto"  onChange="document.Form1.submit();">
            <option value=""> </option>
            <?php
					//consultamos los lotes de control asociados a la EDT del proyecto
					$sql_LC="SELECT  id_actividad,nombre,macroactividad FROM Actividades WHERE id_proyecto = ".$cualProyecto." and nivel = 1";
					$cursor_sql_LC=mssql_query($sql_LC);
					while($datos_sql_LC=mssql_fetch_array($cursor_sql_LC))
					{
						//pertmite determinar el LC del LT, seleccionado  por el usuario en la pagina, y seleccionarlo en la lista de forma automatica, esto en el momento de abrir la pagina
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
          <td class="TituloTabla">Lote de trabajo </td>
          <td class="TxtTabla">
		<select name="LT" id="LT"   class="CajaTexto"  onChange="document.Form1.submit();">
            <option value=""> </option>
            <?php
					//consultamos los lotes de control asociados a el lote de control seleccionado
					$sql_LT="SELECT  id_actividad,nombre,macroactividad FROM Actividades WHERE id_proyecto =".$cualProyecto." and dependeDe=".$LC." and nivel = 2";
					$cursor_sql_LT=mssql_query($sql_LT);
					while($datos_sql_LT=mssql_fetch_array($cursor_sql_LT))
					{

						//pertmite determinar el LT seleccionado por el usuario en la pagina, y seleccionarlo en la lista de forma automatica, esto en el momento de abrir la pagina
						//y despues, se seleccionara el que el usuario escoga en el select
						if($cualLT2==$datos_sql_LT["id_actividad"])
						{
							$cualLT2=-1;  //modifiacmos el valor, para que al momento de seleccionar otro elemento de la lista, este me lo deje  seleccionado
							$select2="selected";
							$LT_selec=$datos_sql_LT["macroactividad"];
						}
						else
						{
							if($LT==$datos_sql_LT["id_actividad"])
							{
								$select2="selected";
								$LT_selec=$datos_sql_LT["macroactividad"];
							}
						}
						echo "<option value=".$datos_sql_LT["id_actividad"]." $select2>".$datos_sql_LT["macroactividad"]." - ".$datos_sql_LT["nombre"]."</option>";
						$select2="";
					}
 ?>
          </select>
		  </td>
        </tr>
        <tr>
          <td class="TituloTabla">Lote de Trabajo - Divisi&oacute;n</td>
          <td class="TxtTabla">
		<select name="DI" id="DI"   class="CajaTexto"  onChange="document.Form1.submit();">
            <option value=""> </option>
            <?php
					$divi_selec=""; //almacenamos la division (Hoja de tiempo) correspondiente a la Division (EDT) asociada al lote de trabajo, para almacenarla en el campo id_division, y asi referenciar las actividades por division
					//consultamos las divisiones  asociados al lote de trabajo
					$sql_DI="SELECT  id_actividad,upper(nombre) as nombre,macroactividad,id_division FROM Actividades WHERE id_proyecto = ".$cualProyecto."  and dependeDe=".$LT." and actPrincipal=".$LC." and nivel = 3";
					$cursor_sql_DI=mssql_query($sql_DI);
					while($datos_sql_DI=mssql_fetch_array($cursor_sql_DI))
					{
						//pertmite determinar la division, seleccionada  por el usuario en la pagina, y seleccionarlo en la lista de forma automatica, esto en el momento de abrir la pagina
						//y despues, se seleccionara el que el usuario escoga en el select
						if($cualLDI==$datos_sql_DI["id_actividad"])
						{
							$cualLDI=-1;  //modifiacmos el valor, para que al momento de seleccionar otro elemento de la lista, este me lo deje  seleccionado
							$select="selected";
							$DI_selec=$datos_sql_DI["macroactividad"];

							$divi_selec=$datos_sql_DI["id_division"]; //almacenamos el nombre de la division, para utilizarlo en el momento de traher lo responsalbes, ya que solo se mostraran los poertenecientes a la division seleccionada
						}
						else
						{
							if($DI==$datos_sql_DI["id_actividad"])
							{
								$select="selected";
								$DI_selec=$datos_sql_DI["macroactividad"];
								$divi_selec=$datos_sql_DI["id_division"];//almacenamos el id de la division, para utilizarlo en el momento de traher lo responsalbes, ya que solo se mostraran los poertenecientes a la division seleccionada
							}
						}
						echo "<option value=".$datos_sql_DI["id_actividad"]." $select >".$datos_sql_DI["macroactividad"]." - ".$datos_sql_DI["nombre"]."</option>";
						$select="";
					}
 ?>
          </select>
		  </td>
        </tr>
<?php

			//Obtenemos el numero del lote de control seleccionado, para componer las actividades
		$num_lote_control = substr($LC_selec,2,strlen($LC_selec));

			//Obtenemos el numero del lote de trabajo seleccionado, para componer las actividades
	 	$num_lote_trabajo=substr($LT_selec,strrpos($LT_selec, ".")+1,strlen($LT_selec));

			//Obtenemos el numero del la division seleccionada, para componer las actividades
	 	$num_div=substr($DI_selec,strrpos($DI_selec, ".")+1,strlen($DI_selec));

		//consultamos la actividad con el ultimo numeto de la macro mas actual LT1.1.2.A.(5)

		$sql_max_act="select MAX( cast (reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int ) ) as num_max_macro 
						from Actividades where dependeDe=".$DI." and tipoActividad=4 and id_division=".$divi_selec." and id_proyecto=".$cualProyecto;
/*		$sql_max_act="select macroactividad from Actividades where id_actividad =(
					  select MAX(id_actividad) as actividad from Actividades where dependeDe=".$DI." and tipoActividad=4 and id_division=".$divi_selec." and   id_proyecto=".$cualProyecto." )
					  and dependeDe=".$DI." and tipoActividad=4  and id_proyecto=".$cualProyecto;
*/
//echo $sql_max_act;

		$cursor_max_act=mssql_query($sql_max_act);
		if($datos_max_act=mssql_fetch_array($cursor_max_act))
		{
			$act_max=$datos_max_act["num_max_macro"];
			//descomponemos la macro division, para almacenar, el ultimo numero, que identifica a el consecutivo de la actividad mas actual
//			$act_max=substr($act_max,strrpos($act_max, ".")+1,strlen($act_max));
//echo $act_max;
		}
		//si no se encontraron registros, es por que la division no contiene actividades, y inicializamos la actividad para esa division
		if($act_max==null)
		{
			$act_max=0;
		}
?>
        <tr>
          <td width="20%" class="TituloTabla">Cantidad de Actividades </td>
          <td class="TxtTabla"><input name="pCantReg" type="text" class="CajaTexto" id="pCantReg" value="<? echo $pCantReg; ?>" size="10" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" onChange="envia1()"></td>
        </tr>
      </table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td height="5" class="TituloTabla"> </td>
        </tr>
      </table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td class="TituloTabla2">ACTIVIDADES</td>
        </tr>
      </table>      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td width="5%">Identificador</td>
        <td>Nombre</td>
        <td>Responsable</td>
        <td>Valor</td>
        </tr>
	  <?
	  $r = 1;
	  $nuevoCodigo= 38;
	  while ($r <= $pCantReg) {
	  ?>
      <tr class="TxtTabla">
        <td width="5%" align="center"><input name="pAC<? echo $r; ?>" type="text" class="CajaTexto" id="pAC<? echo $r; ?>"  size="13" readonly value="<?php echo "LT".$num_lote_control.".".$num_lote_trabajo.".".$num_div.".A.".++$act_max; ?>" ></td>
        <td align="center"><input name="pNombre<? echo $r; ?>" type="text" class="CajaTexto" id="pNombre<? echo $r; ?>"  size="70" onKeyPress=" return acceptComilla(event)" ></td>
        <td align="center">
		<select name="pJefe<? echo $r; ?>" class="CajaTexto" id="pJefe<? echo $r; ?>" >
		<option value="" ><? echo "   ";  ?></option>
            <?
		//Muestra todos los usuarios que podrían ser jefes, Categoria soobre 40. 

		$sql2="select U.*
				from usuarios U				
				inner join Departamentos as dep on dep.id_departamento=U.id_departamento
				inner join Divisiones as div on dep.id_division=div.id_division
				 where div.id_division='".$divi_selec."' 
				 and retirado is null
				  order by U.apellidos  " ;

		$cursor2 = mssql_query($sql2);
		while ($reg2=mssql_fetch_array($cursor2)) {
		?>
            <option value="<? echo $reg2[unidad]; ?>" ><? echo ucwords(strtolower($reg2[apellidos])) . ", " . ucwords(strtolower($reg2[nombre]));  ?></option>
            <? } ?>
          </select>

		</td>
        <td align="center">$<input type="text" name="valor<? echo $r; ?>" id="valor<? echo $r; ?>"  onKeyPress="return acceptNum(event)" class="CajaTexto"  ></td>
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
  		    <input name="cualLC2" type="hidden" id="cualLC2" value="<?php echo $cualLC2; ?>">
  		    <input name="cualLT2" type="hidden" id="cualLT2" value="<?php echo $cualLT2; ?>">
  		    <input name="cualLDI" type="hidden" id="cualLDI" value="<?php echo $cualLDI; ?>">
  		    <input name="divi_selec" type="hidden" id="divi_selec" value="<?php echo $divi_selec; ?>">

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
