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
		if($regId = mssql_fetch_array($cursorId))
		{
			$sigienteSec = $regId["elMax"] + 1;
		}

		//Recoger las variables de las cajas de texto
		$ladivision= "division" . $s;
		$elvalor= "valor" . $s;
		$pD = "pD" . $s;
		$elpJefe = "pJefe" . $s;
		$Lote_Control=$LC;
		$Lote_Trabajo=$LT;
//echo ${$elpJefe}."  ---<br>";
		//id_proyecto, codLoteControl, nomLoteControl, siglaLC, usuarioCrea, fechaCrea, usuarioMod, fechaMod
		//EnsayosProyLC
		
		//co0nsultamos el nombre de la division, para incluirlo en el insert de cada actividad
		$sql_nom_div="select nombre from Divisiones where  estadoDiv='A' and id_division =".${$ladivision};
		$cursor_nom_div=mssql_query($sql_nom_div);
		if($datos_nom_div=mssql_fetch_array($cursor_nom_div))
		{
			$nom_division=$datos_nom_div["nombre"];
		}

		$sqlIn1 = " INSERT INTO Actividades";
		$sqlIn1 = $sqlIn1 . "( id_proyecto, id_actividad, fecha_inicio, fecha_fin, nombre,id_division, macroactividad, ";
		if(trim(${$elpJefe})!="")
		{
				$sqlIn1=$sqlIn1."id_encargado,";
		}
		$sqlIn1=$sqlIn1."dependeDe, actPrincipal, tipoActividad, nivelesActiv, nivel ) ";
		$sqlIn1 = $sqlIn1 . " VALUES ( ";
		$sqlIn1 = $sqlIn1 . " ".$cualProyecto.", ";
		$sqlIn1 = $sqlIn1 . " " . $sigienteSec . ", ";

		$sqlIn1 = $sqlIn1 . " getdate(), ";
		$sqlIn1 = $sqlIn1 . " getdate(), ";

		$sqlIn1 = $sqlIn1 . " '" . $nom_division . "', ";

		$sqlIn1 = $sqlIn1 . " '" . ${$ladivision} . "', ";
		$sqlIn1 = $sqlIn1 . " '" . ${$pD} . "', ";
		if(trim(${$elpJefe})!="")
		{
			$sqlIn1 = $sqlIn1 . " " . ${$elpJefe} . ", ";
		}
		$sqlIn1 = $sqlIn1 ."".$Lote_Trabajo." , ";
		$sqlIn1 = $sqlIn1 . " " . $Lote_Control . ", ";
		$sqlIn1 = $sqlIn1 . " '3', ";

		$sqlIn1 = $sqlIn1 . " '".$Lote_Control."-".$Lote_Trabajo."-".$sigienteSec."', ";
		$sqlIn1 = $sqlIn1 . " '3' ";
		$sqlIn1 = $sqlIn1 . " ) ";
//		$sqlIn1 = $sqlIn1 . " '". gmdate ("n/d/Y") . "', ";
//		$sqlIn1 = $sqlIn1 . " '" . $_SESSION["sesUnidadUsuario"] . "' ";

		$cursorIn1 = mssql_query($sqlIn1);

		if  (trim($cursorIn1) != "")  {
			//echo "entro eal if 2" . "<br>"; elpSigla
			$msgGraba=$msgGraba."[".${$pD}."] " ;
		}
		else {
			//echo "entro al else " . "<br>";
			$msgNOGraba=$msgNOGraba."[".${$pD}."] " ; 
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
		echo ("<script>alert('Se grabaron las siguientes Lotes de control: $msgGraba ');</script>"); 
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

CantCampos=2+(4*document.Form1.pCantReg.value);

//almacenamos la cantidad de registros, para validar los selects de division y  responsable
var can_reg=document.Form1.pCantReg.value;
var can_campos=CantCampos-2;


//valida que el usuario no seleccione la division, mas de una vez


cant_reg2=can_campos;
//inicializamos en la posicion 4 (por que es la posicion del campo division), para que arranque en el primer select de la division, y lo aumentamos en 4, para ubicarnos en el siguiente
for(i=4;i<=cant_reg2;i+=4)
{
	//la variable 'a', es el que se mueve por los otros registros, para realizar la validacion 
	a=i+4;
	//cuando 'a' es mayor a la cantidad de registros, es por que llego al ultimo registro, y se ubicara en el primer regisro
	if(cant_reg2<a)
	{
		a=4;
	}

	//recorremos los demas registros, de 4 en 4, para validarlos, y hasta que la cantidad de registros sea -1, ya que se valida un registro especifico contra todos los demas 
	for(z=4;z<=cant_reg2-1;z+=4)
	{
		//la pocision de la variable 'i', es estatica, mientras que 'a', es dnamica, ya que esta recorreo los demas registros, comparandolos con el valor de 'i'
		if(document.Form1.elements[i].value==document.Form1.elements[a].value)
		{
			msg2=' No se puede seleccionar mas de una división con el mismo nombre. Por favor verifique la informacion, suministrada en el campo División';
			v2='n';
			break;
		}
		//si la se llego al limite de los registros, se inicializa, para que se pocisione en el primero, y se compare este, contra el que se esta validando
		if(a==cant_reg2)
		{
			a=0;
		}
		a+=4;
	}

}
/*
for (i=1;i<=can_reg;i++) 
{
	for(f=1;f<=can_campos)
	if (document.Form1.elements[i].value == '') 
	{
		v2='n';

		msg2 = 'Asigne un valor a cada una de las divisiones. \n';
	}
}
*/

//Valida que el campo valor no esté vacio
for (i=6;i<=CantCampos;i+=4) {
	if (document.Form1.elements[i].value == '') {
		v2='n';

		msg2 = 'El valor asignado a cada division es obligatorio.'; //'Asigne un valor a cada una de las divisiones. \n';
//+document.Form1.pLCi.value
	}
}

//Valida que el campo Division no esté vacio
for (i=4;i<=CantCampos;i+=4) {
//alert (document.Form1.elements[i].value+'-'+i);
	if (document.Form1.elements[i].value == '') {
		v2='n';

		msg2 = 'Seleccione la division perteneciente a cada identificador. \n';
//+document.Form1.pLCi.value
	}
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
<title>Programaci&oacute;n de Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">

<script language="JavaScript" src="calendar.js"></script>
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<form action="" method="post"  name="Form1">

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Divisiones </td>
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
					//validamos si la variable si la variable que se forma del  select, esta definida, si no lo esta, es por que es la primera vez que se accede a la pagina
					//entonces  cargamos la variable para cargar el select, del los lotes de trabajo 
					if(!isset($LC))
					{
							$LC=$cualLC;
					}
					if(!isset($LT))
					{
							$LT=$cualLT;
					}

?>
          <td class="TxtTabla"><select name="LC" id="LC"   class="CajaTexto"  onChange="document.Form1.submit();">
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
          </select></td>
        </tr>


        <tr>
          <td class="TituloTabla">Lote de trabajo </td>
          <td class="TxtTabla"><select name="LT" id="LT"   class="CajaTexto"  onChange="document.Form1.submit();">
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
          </select></td>
        </tr>
<?php

			//Obtenemos el numero del lote de control seleccionado, para componer las divisiones
		$num_lote_control = substr($LC_selec,2,strlen($LC_selec));

			//Obtenemos el numero del lote de trabajo seleccionado, para componer las divisiones			
	 	$num_lote_trabajo=substr($LT_selec,strrpos($LT_selec, ".")+1,strlen($LT_selec));
		
		//consultamos la division mas actual del lote de trabajo seleccionado
		$sql_max_div="select MAX(macroactividad)  macroactividad  from Actividades where dependeDe=".$LT."  and tipoActividad=3 and id_proyecto=".$cualProyecto;
		$cursor_max_div=mssql_query($sql_max_div);
		if($datos_max_div=mssql_fetch_array($cursor_max_div))
		{
			$div_max=$datos_max_div["macroactividad"];
			//descomponemos la macro division, para almacenar, el ultimo numero, que identifica a el consecutivo de la division mas actual
			$div_max=substr($div_max,strrpos($div_max, ".")+1,strlen($div_max));
		}
		//si no se encontraron registros, es por que el lote de trabajo no contiene divisiones, y inicializamos la division para ese lote de trabajo
		if($div_max==null)
		{
			$div_max=0;
		}

?>
        <tr>
          <td width="20%" class="TituloTabla">Cantidad de Divisiones</td>
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
          <td class="TituloTabla2">DIVISIONES </td>
        </tr>
      </table>      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td width="5%">Identificador</td>
        <td>Divisi&oacute;n</td>
        <td>Responsable</td>
        <td>Valor</td>
      </tr>
	  <?
	  $r = 1;
	  $nuevoCodigo= 38;
	  while ($r <= $pCantReg) {
	  ?>
      <tr class="TxtTabla">
        <td width="5%" align="center"><input name="pD<? echo $r; ?>" type="text" class="CajaTexto" id="pD<? echo $r; ?>"  size="13" readonly value="<?php echo "LT".$num_lote_control.".".$num_lote_trabajo.".".++$div_max; ?>" ></td>
        <td align="center">
			<select name="division<? echo $r; ?>" id="division<? echo $r; ?>" class="CajaTexto">
				<option value=""> </option>
<?php
				$sql_divisiones="select * from Divisiones where  estadoDiv='A' and id_division  not in(
								 select id_division from Actividades where  dependeDe=".$LT." and tipoActividad=3 and id_proyecto=".$cualProyecto.")";
				$cursor_div=mssql_query($sql_divisiones);
				while($datos_div=mssql_fetch_array($cursor_div))
				{
?>
					<option value="<?php  echo  $datos_div["id_division"]; ?>"><?php  echo  strtoupper($datos_div["nombre"]); ?> </option>
<?php
				}
?>

	        </select>
		</td>
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
        <td align="center">$<input type="text" name="valor<? echo $r; ?>" id="valor<? echo $r; ?>"  onKeyPress="return acceptNum(event)" class="CajaTexto"  ></td>
      </tr>
		<? 
echo $sql2;
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
