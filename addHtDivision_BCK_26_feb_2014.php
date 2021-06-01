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
	$mensaje= "";
	$ban_valor=0;

	//VALIDA LOS VALORES DE LAS DIVISIONES,  CON EL FIN DE COMPROBAR QUE EL VALOR DE ESTAS, NO SUPERE EL VALOR ASIGNADO
	while ($s <= $pCantReg) 
	{	
			$val_asig_div=0;
			$val_div_lt=0;


			$ladivision= "division".$s;
			$valor = "valor" . $s;
			$nom_division="division".$s;

			//CONSULTA EL VALOR ASIGANDO A LA DIVISION EN EL PROYECTO
			$sql_val_div="select valorAsignado from AsignaValorDivision where id_proyecto=".$cualProyecto." and id_division=".${$ladivision};
			$cur_val_div=mssql_query($sql_val_div);
			if($dato_val_div=mssql_fetch_array($cur_val_div))
				$val_asig_div=$dato_val_div["valorAsignado"];
//	echo $sql_val_div."<br>".mssql_get_last_message()." ***************** ".$val_asig_div."<br>";
	
			//CONSULTA LA SUMATORIA DE LAS DIVISION EN LOS DIFERENTES LOTES DE TRABAJO
			$sql_val_div_lt="select SUM(valor) as valor_divisiones from Actividades where id_proyecto=".$cualProyecto." and nivel=3 and id_division=".${$ladivision};
			$cur_val_div_lt=mssql_query($sql_val_div_lt);
			if($datos_val_lt=mssql_fetch_array($cur_val_div_lt))
				$val_div_lt=$datos_val_lt["valor_divisiones"];

//	echo $sql_val_div_lt."<br>".mssql_get_last_message()." --- ".$val_asig_div."-".$val_div_lt."<br>";	
			//SUMA EL VALOR TOTAL DE LAS DIVISIONES, MAS EL VALOR DE LA NUEVA DIVISIÓN
			$valor_divisiones_nuevo=$val_div_lt+trim(${$valor});

			//SI EL VALOR ASIGNADO A LA DIVISION ES INFERIOR A LA SUMATORIA TOTAL DE LA DIVISION EN LOS DIFERENTES LOTES DE TRABAJO, MUESTRA EL MENDAJE DE ERROR
			if($val_asig_div<$valor_divisiones_nuevo)
			{

				//co0nsultamos el nombre de la division
				$sql_nom_div="select nombre from Divisiones where  estadoDiv='A' and id_division =".${$ladivision};
				$cursor_nom_div=mssql_query($sql_nom_div);
				if($datos_nom_div=mssql_fetch_array($cursor_nom_div))
				{
					$nom_divisions=strtoupper($datos_nom_div["nombre"]);
				}

				//CALCULA EL VALOR QUE SE DEBE ASIGNAR, PARA LA DIVISION EN EL LOTE DE TRABAJO
				$val_asignar=$val_asig_div-$val_div_lt;
//echo $val_asignar."=".$val_asig_div." - ".$val_div_lt;
				//SI EL VALOR DISPONIBLE PARA LA DIVISIÓN ES 0, ES POR QUE SE HA EXCEDIDO, EL VALOR ASIGNADO
				if($val_asignar!=0)
				{
					$mensaje=$mensaje.'La sumatoria de la división '.$nom_divisions.' en los diferentes lotes de trabajo, no puede superar el valor asignado, asigne un valor igual o inferior a $'.$val_asignar.'.\n';

				}
				else
				{
					$mensaje=$mensaje.'La división '.$nom_divisions.' ha alcansado el tope del valor asignado, en la EDT. \n';						
				}
				$ban_valor=1;				
			}
		$s = $s + 1;
	}

	//SI ALGUNO DE LOS VALORES SUPERA EL DE LA DIVISIONES, NO SE PERMITE LA INSERCION DE LAS DEMAS DIVISIONES
	if($ban_valor==1)	
	{
		echo '<script type="text/javascript" language="JavaScript"> alert("'.$mensaje.'"); </script>';
		/*
		echo '<script type="text/javascript" language="JavaScript"> alert("';
		for($h=0;$h<$men;$h++)
			echo $mensaje[$h].'\n';
		echo '"); </script>';
		*/
	}
	//SI LOS VALORES DE LAS DIVISIONES CUMPLEN LAS CONDICIONES, SE REGISTRA LA INFORMACION DE TODAS LAS DIVISIONES
	else
	{	

				///PARA CAMBIAR O QUITAR 1
					//CONSULTA L AINFORMACION DEL LOTE DE TRABAJO, CORRESPONDIENTE A LA ERP
					$SQL_lt="SELECT erpLC,erpLT FROM Actividades where id_proyecto=".$cualProyecto." and id_actividad=".$LT." and nivel=2";
					$cursorlt = mssql_query($SQL_lt);
					if($reglt = mssql_fetch_array($cursorlt))
					{
						$erp_lc=$reglt["erpLC"];
						$erp_lt=$reglt["erpLT"];
					}
				///PARA CAMBIAR O QUITAR 2

		$s=1;	
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
			$valor = "valor" . $s;
			$Lote_Control=$LC;
			$Lote_Trabajo=$LT;
	
			//co0nsultamos el nombre de la division, para incluirlo en el insert de cada actividad
			$sql_nom_div="select nombre,id_division from Divisiones where  estadoDiv='A' and id_division =".${$ladivision};
			$cursor_nom_div=mssql_query($sql_nom_div);
			if($datos_nom_div=mssql_fetch_array($cursor_nom_div))
			{

				$nom_division=$datos_nom_div["nombre"];

				///PARA CAMBIAR O QUITAR 1
				$id_div=$datos_nom_div["id_division"];
				///PARA CAMBIAR O QUITAR 2
			}
	
	
	
			$sqlIn1 = " INSERT INTO Actividades";
			$sqlIn1 = $sqlIn1 . "( id_proyecto, id_actividad, nombre,id_division, macroactividad, ";
			if(trim(${$elpJefe})!="")
			{

					$sqlIn1=$sqlIn1."id_encargado,";
			}
			$sqlIn1=$sqlIn1."dependeDe, actPrincipal, tipoActividad, nivelesActiv, nivel,erpDiv, erpLC,erpLT ,usuarioCrea,fechaCrea ";
	
			if(trim(${$valor})!="")
			{
					$sqlIn1=$sqlIn1.",valor";			
			}
			$sqlIn1 = $sqlIn1 . " ) VALUES ( ";
			$sqlIn1 = $sqlIn1 . " ".$cualProyecto.", ";
			$sqlIn1 = $sqlIn1 . " " . $sigienteSec . ", ";
	
	
			$sqlIn1 = $sqlIn1 . "  UPPER('" . $nom_division . "'), ";
	
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
			$sqlIn1 = $sqlIn1 . " '3', '".$id_div."' ,'".$erp_lc."' ,'".$erp_lt."' ,".$_SESSION["sesUnidadUsuario"].",getdate()  ";
			if(trim(${$valor})!="")
			{
					$sqlIn1=$sqlIn1."," .trim(${$valor});			
			}
			$sqlIn1 = $sqlIn1 . " ) ";
	//		$sqlIn1 = $sqlIn1 . " '". gmdate ("n/d/Y") . "', ";
	//		$sqlIn1 = $sqlIn1 . " '" . $_SESSION["sesUnidadUsuario"] . "' ";
	
			$cursorIn1 = mssql_query($sqlIn1);


			if(trim(${$elpJefe})!="")
			{
					//SI SE HA SELECCIONADO A UN RESPONSABLE  ESTE SE INSERTA COMO PARTICIPANTE, 
					$sql_partici= " INSERT INTO ParticipantesActividad ( id_proyecto, unidad, id_actividad, estado, usuarioCrea, fechaCrea ) ";
					$sql_partici= $sql_partici. " VALUES ( ";
					$sql_partici= $sql_partici. " " . $cualProyecto . ", ";
					$sql_partici= $sql_partici. " " . ${$elpJefe} . ", ";	
		
					$sql_partici= $sql_partici. " " . $sigienteSec. ", ";
					$sql_partici= $sql_partici. "  'A' ,";
					$sql_partici= $sql_partici. " " . $_SESSION["sesUnidadUsuario"] . ", ";
					$sql_partici= $sql_partici. " '" . date("m/d/Y H:i:s") . "' ";
					$sql_partici= $sql_partici . " ) ";
					$cursorIn_part = mssql_query($sql_partici);

//					echo mssql_get_last_message()." **** ".$sql_partici."<br>***".mssql_num_rows($cursorIn_part);
			}

	
			//si  se le ha asignado  valor de la actividad, se almacen ek registro en actividades recursos
			if( ${$valor}!="")
			{
				$sql_acti_val="insert into  ActividadesRecursos (id_proyecto,id_actividad,secuencia,valorActiv,unidad,fecha,usuarioCrea,fechaCrea) values";
				$sql_acti_val=$sql_acti_val."(".$cualProyecto."," . $sigienteSec . ",1,". ${$valor}.",".$_SESSION["sesUnidadUsuario"].",getdate(),".$_SESSION["sesUnidadUsuario"].",getdate())";
				$cur_acti_val=mssql_query($sql_acti_val);
		
				if  (trim($cur_acti_val)=="")  
				{
					$error="si";
				}		
			}
	
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
			echo ("<script>alert('No se grabaron las siguientes Divisiones: $msgNOGraba ');</script>"); 
		} 
		
		if  (trim($msgGraba) != "")  {
			echo ("<script>alert('Se grabaron las siguientes Divisiones: $msgGraba ');</script>"); 
		} 
		else {
			echo ("<script>alert('Error durante la grabación');</script>");
		}
		echo ("<script>window.close();MM_openBrWindow('htPlanProyectos01.php?cualProyecto=".$cualProyecto."&aa=".$cualLT."','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600'); </script> ");


	}

}
	//consulta la cantidad de divisiones que existen el la compañia
	$sql_cant_div="select COUNT(*) as cant_div from HojaDeTiempo.dbo.Divisiones where estadoDiv='A'";
	$cur_cant_div=mssql_query($sql_cant_div);
	$datos_can_div=mssql_fetch_array($cur_cant_div);

	//consulta la cantidad de divisiones ingresadas registradas en el lote de trabajo
	$sql_cant_div_lt="select COUNT(*) as cant_div_lt from Actividades where id_proyecto = ".$cualProyecto." and nivel=3 and dependeDe=".$cualLT." and actPrincipal=".$cualLC;
	$cur_cant_div_lt=mssql_query($sql_cant_div_lt);
	$datos_can_div_lt=mssql_fetch_array($cur_cant_div_lt);
//echo $sql_cant_div_lt;
?>

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
	//valida si no se han ingresado un valor superior, a la cantidad de divisiones activas
	if(<? echo $datos_can_div["cant_div"]; ?><document.Form1.pCantReg.value)
	{
		document.Form1.pCantReg.value=1;
		alert("La cantidad de divisiones activas en la compañia es "+<? echo  $datos_can_div["cant_div"]; ?>+" por favor ingrese un numero inferior");
	}
	else if(<? echo $datos_can_div["cant_div"]-$datos_can_div_lt["cant_div_lt"]; ?><document.Form1.pCantReg.value) //resta el valor de la (cantidad de divisiones activas-las registradas), y compara el valor con la cantidad  ingresada por el susuario
	{
		document.Form1.pCantReg.value=1;

		alert("La cantidad maxima de divisiones que se pueden registrar es  "+<? echo $datos_can_div["cant_div"]-$datos_can_div_lt["cant_div_lt"]; ?>+" por favor ingrese un numero inferior");		

	}
	else
	{

		//alert ("Entro a envia 1");
		document.Form1.recarga.value="1";
		document.Form1.submit();
	}
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
			msg2=' No se puede seleccionar más de una división con el mismo nombre. Por favor verifique la información suministrada en el campo División';
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

	if (parseInt(document.Form1.elements[i].value)==0) {
		v2='n';

		msg2 = 'El valor asignado a una division no puede ser 0.'; //'Asigne un valor a cada una de las divisiones. \n';
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
<title>.:: Planeaci&oacute;n de Proyectos</title>
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
					$sql_LC=$sql_LC." order by cast(reverse(substring(reverse(macroactividad),1,charindex('C', reverse(macroactividad))-1)) as int)";
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
					$sql_LT=$sql_LT." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
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
		$sql_max_div="select MAX(cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int) )  macroactividad   from Actividades where dependeDe=".$LT."  and tipoActividad=3 and id_proyecto=".$cualProyecto;
		$cursor_max_div=mssql_query($sql_max_div);
		if($datos_max_div=mssql_fetch_array($cursor_max_div))
		{
			$div_max=$datos_max_div["macroactividad"];
			//descomponemos la macro division, para almacenar, el ultimo numero, que identifica a el consecutivo de la division mas actual
		//	$div_max=substr($div_max,strrpos($div_max, ".")+1,strlen($div_max));
		}
		//si no se encontraron registros, es por que el lote de trabajo no contiene divisiones, y inicializamos la division para ese lote de trabajo
		if($div_max==null)
		{
			$div_max=0;
		}

?>
        <tr>
          <td width="20%" class="TituloTabla">Cantidad de Divisiones</td>
          <td class="TxtTabla"><input name="pCantReg" type="text" class="CajaTexto" id="pCantReg" value="<? echo $pCantReg; ?>" size="10" onKeyPress="return acceptNum(event)"   onChange="envia1()"  ></td>
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
								 select id_division from Actividades where  dependeDe=".$LT." and tipoActividad=3 and id_proyecto=".$cualProyecto.") order by nombre";
				$cursor_div=mssql_query($sql_divisiones);
				
				$div_sel= "division" . $r; //almacenamos la division seleccionada por el usuario, para utilizarla al momento de  cargar los usuario correspondientes a esa division
				while($datos_div=mssql_fetch_array($cursor_div))
				{
							if(${$div_sel}== $datos_div["id_division"])
							{
								$select2="selected";
							}

?>
					<option value="<?php  echo  $datos_div["id_division"]; ?>" <?php echo $select2; ?>><?php  echo  strtoupper($datos_div["nombre"]); ?> </option>

<?php
					$select2="";

				}

?>

	        </select>

		</td>
        <td align="center">
		<select name="pJefe<? echo $r; ?>" class="CajaTexto" id="pJefe<? echo $r; ?>" >
		<option value="" ><? echo "   ";  ?></option>
            <?
/*
		//Muestra todos los usuarios que podrían ser jefes, Categoria soobre 40. 
		$sql2="select U.*, C.nombre nomCategoria from usuarios U
				inner join Categorias C on U.id_categoria=C.id_categoria
				inner join Departamentos as dep on dep.id_departamento=U.id_departamento
				inner join Divisiones as div on dep.id_division=div.id_division
where  U.retirado is null and left(C.nombre,2) < 40 and C.id_categoria <= 5 and div.id_division=".${$div_sel}." order by U.apellidos 
";
*/
		//Muestra todos los usuarios que podrían ser jefes, Categoria soobre 40. 
		$sql2="select U.*, C.nombre nomCategoria  " ;
		$sql2=$sql2." from usuarios U, Categorias C ";
		$sql2=$sql2." where U.id_categoria = C.id_categoria  ";
		$sql2=$sql2." and U.retirado is null ";
		$sql2=$sql2." and left(C.nombre,2) < 40 ";
		$sql2=$sql2." and  C.id_categoria <= 5 ";
		$sql2=$sql2." order by U.apellidos ";

		$cursor2 = mssql_query($sql2);
		$res_sel= "pJefe" . $r;
		while ($reg2=mssql_fetch_array($cursor2)) 
			{

							if(${$res_sel}== $reg2["unidad"])
							{
								$select2="selected";
							}
		?>
            <option value="<? echo $reg2[unidad]; ?>" <?php echo $select2; ?>><? echo ucwords(strtolower($reg2[apellidos])) . ", " . ucwords(strtolower($reg2[nombre]));  ?></option>
            <? 
				$select2="";
			} 
			?>
          </select>
		</td>
        <td align="center">$<input type="text" name="valor<? echo $r; ?>" value="<? echo ${"valor".$r}; ?>" id="valor<? echo $r; ?>"  onKeyPress="return acceptNum(event)" class="CajaTexto"  ></td>
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
