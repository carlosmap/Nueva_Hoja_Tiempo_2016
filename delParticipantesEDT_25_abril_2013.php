<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
<?
session_start();



//Establecer la conexión a la base de datos (MS Sql Server)
include "funciones.php";
include "validaUsrBd.php";

$connMySql = conectarMySql();


/*
2010-10-25
Daniel Felipe Rentería Martínez
Adición Usuarios 
*/

/*
Datos de Proyecto
dbo.Proyectos
id_proyecto, codigo, cargo_defecto, nombre, id_director, 
id_coordinador, id_estado, especial, maxclase, codProyecto, 
idEmpresa, fechaCrea, descCargoDefecto
*/


$sql0 = " SELECT id_proyecto, codigo, cargo_defecto, nombre
FROM HojaDeTiempo.dbo.Proyectos ";
$sql0 = $sql0 . " WHERE id_proyecto = " . $cualProyecto;
$cursor0 = mssql_query($sql0);
if($reg0 = mssql_fetch_array($cursor0)){
	$nombreProyecto = $reg0['nombre'];
	$cargoDefecto = $reg0['codigo'] . "." . $reg0['cargo_defecto'];
	$idProyecto = $reg0['id_proyecto'];
}


/*
Divisiones de la Hoja de Tiempo
dbo.Divisiones
id_division, nombre, id_director, id_dependencia, id_subdirector, codigoDAF, estadoDiv
*/


/*
Departamentos de la Hoja de Tiempo
dbo.Departamentos
id_departamento, nombre, id_director, id_division, codDpto, codigoDAF, estadoDpto, claseDpto
*/



if($recarga==1)
{

		
		//	CONSULTA LOS USUARIOS ASOCIADOS A LA ACTIVIDAD SELECCIONADA
		$sql3 = " SELECT *,Usuarios.nombre as nom_usu FROM HojaDeTiempo.dbo.Usuarios";
		
		if($division != 0){
			$sql3 = $sql3 . " inner join Departamentos on Usuarios.id_departamento =Departamentos.id_departamento " ;
			$sql3 = $sql3 . " inner join Divisiones on Departamentos.id_division =Divisiones.id_division ";
		}
		
		$sql3=$sql3." WHERE retirado IS NULL ";
		
		if($division != 0){
			$sql3 = $sql3 . " AND Divisiones.id_division = " . $division;
		}
		
		if($dpto != 0){
			$sql3 = $sql3 . " AND Departamentos.id_departamento = " . $dpto;
		}
		if(isset($unidad) && trim($unidad) != ""){
			$sql3 = $sql3 . " AND unidad = " . $unidad;
		}
		if(isset($nombre) && trim($nombre) != ""){
			$sql3 = $sql3 . " AND ( nombre LIKE '%" . $nombre . "%' ";
			$sql3 = $sql3 . " OR apellidos LIKE '%" . $nombre . "%') ";
		}

			if(trim($Lote_control)!="")
			{
				$id_ac=$Lote_control;
				if(trim($Lote_trabajo)!="")
				{
					$id_ac=$Lote_trabajo;
					if(trim($Division)!="")
					{
						$id_ac=$Division;
						if(trim($Actividad)!="")
						{
							$id_ac=$Actividad;
						}
					}

				}
			}
		$sql3 = $sql3 . " and Usuarios.unidad in (
			select unidad from HojaDeTiempo.dbo.ParticipantesActividad  where id_proyecto=".$cualProyecto." AND id_actividad=".$id_ac."
			) ";
		/*
		$sql3 = $sql3 . " AND unidad NOT IN ";
		$sql3 = $sql3 . " ( SELECT unidad FROM HojaDeTiempo.dbo.AutorizadosImpresion WHERE id_proyecto = " . $idProyecto . ") ";
		*/
		$sql3 = $sql3 . " ORDER BY apellidos ";
//echo $sql3 . "<br>".mssql_get_last_message();
//		 if($division != 0 || $dpto != 0 || trim($nombre) != "" || trim($unidad) != "")
			$cursor3 = mssql_query($sql3);

	#	echo "<script type='text/javascript'> document.Form1.recarga.value=0; <script>";
	#	$recarga="0";
}
/*
$sql4 = " SELECT *
FROM (
SELECT A.id_proyecto, A.nombre, A.codigo, A.cargo_defecto, A.descCargoDefecto, '0' esAdicional
FROM HojaDeTiempo.dbo.Proyectos A
where A.id_estado = 2
and A.id_proyecto not in (
	SELECT id_proyecto FROM Proyectos
	where especial = 1
	and LEN(codigo) > 2
)
UNION
select A.id_proyecto, A.nombre, A.codigo, B.cargos_adicionales, B.descripcion, '1' esAdicional
from HojaDeTiempo.dbo.Proyectos A, HojaDeTiempo.dbo.Cargos B
where A.id_proyecto = B.id_proyecto
and A.id_estado = 2
and A.id_proyecto not in (
	SELECT id_proyecto FROM Proyectos
	where especial = 1
	and LEN(codigo) > 2
) 
) X ";
$sql4 = $sql4 . " WHERE X.id_proyecto = " . $idProyecto . " ";
$sql4 = $sql4 . " ORDER BY X.id_proyecto ";
$cursor4 = mssql_query($sql4);
*/
/*****************
Grabación del Registro
*****************/
if($recarga == 2){
	
	$okGuardar = "Si";
	$okGuardarMySql = "Si";
	$queryLog = "";
	$queryLogMy = "";
	
	$cursorTran1 = mssql_query("BEGIN TRANSACTION");
	if(trim($cursorTran1) == ""){
		$okGuardar = "No";
	}
/*	
	$cursorTranMy1 = mysql_query("START TRANSACTION");
	if(trim($cursorTranMy1) == ""){
		$okGuardarMySql = "No";
	}
*/
	
	for($i = 1; $i <= $cantUsuarios; $i++)
	{
		
		$usuario = "usuario" . $i;
//		$mail = "mail" . $i;
		$aplicaUsuario = "aplicaUsuario" . $i;
	
		/*
		Usuarios Autorizados para Imprimir en los Proyectos
		dbo.AutorizadosImpresion
		id_proyecto, unidad, fechaAutoriza, fechaDesautoriza, estado, usuarioCrea, fechaCrea, usuarioMod, fechaMod
		*/
		if(($$aplicaUsuario == 1)and($okGuardar == "Si"))
		{
			//almancena el id de la actividad seleccionado en el ultimo nivel de actividad(LC/LT/DIV/ACT)
			if(trim($Lote_control)!="")
			{
				$id_ac=$Lote_control;
				if(trim($Lote_trabajo)!="")
				{
					$id_ac=$Lote_trabajo;
					if(trim($Division)!="")
					{
						$id_ac=$Division;
						if(trim($Actividad)!="")
						{
							$id_ac=$Actividad;
						}
					}

				}
			}
			$sql_planea="select * from  PlaneacionProyectos  where id_proyecto=".$cualProyecto." and id_actividad=".$id_ac." and unidad=".$$usuario;
			$cur_planea=mssql_query($sql_planea);

			//SI EL USUARIO TIENE PLANEACION, PARA ESA ACTIVIDAD, SE ACTUALIZA EL REGISTRO A INACTIVO, SI NO TIENE, SE ELIMINIA
			if(mssql_num_rows($cur_planea)==0)
			{
echo "0";
				//PARA CUANDO EL USUARIO NO TIENEN FACTURACION EN LA ACTIVIDAD
				$sqlDel = "DELETE FROM  HojaDeTiempo.dbo.ParticipantesActividad  
						   Where id_actividad = ".$id_ac." and unidad = ".$$usuario." AND id_proyecto = ".$cualProyecto;
				$qry = mssql_query( $sqlDel );
				if( trim($qry)=="" )
					$okGuardar = "no";
echo "<br>".$sqlDel;
			}
			else
			{
echo "1";
			
	//				$sqlIn1 = " DELETE FROM HojaDeTiempo.dbo.ParticipantesActividad  WHERE id_proyecto=" . $cualProyecto ." and unidad=".$$usuario." and id_actividad=".$id_ac;
					$sqlIn1 = " UPDATE HojaDeTiempo.dbo.ParticipantesActividad SET estado='I'  WHERE id_proyecto=" . $cualProyecto ." and unidad=".$$usuario." and id_actividad=".$id_ac;			
					$cursorIn1 = mssql_query($sqlIn1);
	//echo $sqlIn1 . "<br>".mssql_get_last_message();
				if(trim($cursorIn1) == ""){
					$okGuardar = "No";
				}
			}
		}
	}
	if($okGuardar == "Si"){

		$cursorTran2 = mssql_query(" COMMIT TRANSACTION ");
		echo "<script>alert('La operación se realizó con éxito')</script>";
	} else {

		$cursorTran2 = mssql_query(" ROLLBACK TRANSACTION ");
		echo "<script>alert('Error en la operación')</script>";
	}
	//exit;
	echo "<script>window.close();</script>";
	echo ("<script>MM_openBrWindow('htPlanProyectos02.php?cualProyecto=".$cualProyecto."','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=960,height=700');</script>");	
}



?>
<html>
<head>
<title>Planeaci&oacute;n de proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="javascript">

/*
Funcion que verifica que un campo numérico solo permita presionar las teclas numéricas, la tecla punto, la tecla backspace y la tecla tab
Funciona en IE y en Firefox
*/
function campoNumerico(evento){
	var tecla = (document.all)?evento.keyCode:evento.which;
	//alert(tecla);
	if(tecla != 8 && tecla != 0 && tecla != 46 && tecla != 13 && (tecla < 48 || tecla > 57)){
		return false;
	} else {
		return true;
	}
}

function seleccionarUsuarios(cantidad, opcion){
	var cantUsu = parseInt(cantidad);
	var expr1 = '';
	var expr2 = '';
	for(var i = 1; i <= cantUsu; i++){
		if(opcion == 1){
			expr1 = 'document.Form1.aplicaUsuario' + i + '[0].checked = true';
			expr2 = 'document.Form1.aplicaUsuario' + i + '[1].disabled = true';
		} else if(opcion == 0){
			expr1 = 'document.Form1.aplicaUsuario' + i + '[1].disabled = false';
			expr2 = 'document.Form1.aplicaUsuario' + i + '[1].checked = true';
		}
		eval(expr1);
		eval(expr2);
	}
}


function envia0()
{
	var error = 'n';
	var mensaje="";
	//si se ha ingresado informacion en el campo nombreo o unidad, no se permite consultar, con el campo y/o division seleccionado

	if(document.Form1.Lote_control.value == '')
	{
		error = 's';
		mensaje = mensaje + 'Seleccione almenos un lote de control. \n';
	}
	if(error=='s')
	{
		alert(mensaje);
	}
	else
	{
		document.Form1.recarga.value = 1;
		document.Form1.submit();
	}
}

function envia1()
{

		document.Form1.recarga.value = 1;
		document.Form1.submit();
}

function envia2(){
	var error = 'n';
	var mensaje = '';
	
	if(document.Form1.Lote_control.value == '')
	{
		error = 's';
		mensaje = mensaje + 'Seleccione almenos un lote de control. \n';
	}

		var cantUsu = parseInt(document.Form1.cantUsuarios.value);
		var expr1 = '';
		var cantSeleccionados = 0;
		for(var i = 1; i <= cantUsu; i++){
			expr1 = 'document.Form1.aplicaUsuario' + i + '[0].checked';
			if(eval(expr1) == true){
				cantSeleccionados++;
			}
		}
		if(cantSeleccionados == 0){
			error = 's';
			mensaje = mensaje + 'Seleccione al menos un participante. \n';
		}

	//Finalización de la validación
	if(error == 's'){
		alert(mensaje);
	} else {
		document.Form1.recarga.value = 2;
		document.Form1.submit();
	}
}

</script>
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="#EAEAEA">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Programación de Proyectos</td>
  </tr>
  <tr>
    <td class="TxtTabla">&nbsp;  </td>
  </tr>
</table>

<form action="" method="post" name="Form1"  >
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
 <tr>
		<td colspan="2">
			<table width="100%">
				<tr>
				  <td colspan="2" class="TituloUsuario">Actividades asociadas a la EDT del proyecto</td>
			    </tr>
                  <tr>
                    <td width="25%" class="TituloTabla">Proyecto</td>
                    <td class="TxtTabla"><? echo $idProyecto . " - [" . $cargoDefecto . "] - " . ucwords(strtolower($nombreProyecto)); ?></td>
                  </tr>
				<tr>
				  <td width="25%" class="TituloTabla">Lote de Control</td>
				  <td class="TxtTabla"><span class="TxtTabla">
				    <select name="Lote_control" class="CajaTexto" id="Lote_control" onChange="document.Form1.submit();">
				      <option value="">::Seleccione un Lote de control::</option>
<?
						$sql_lote_control="SELECT * from Actividades where id_proyecto=".$cualProyecto. "and nivel=1 order by cast(reverse(substring(reverse(macroactividad),1,charindex('C', reverse(macroactividad))-1)) as int)";
						$cur_lote_lc=mssql_query($sql_lote_control);
						while($datos_lote_lc=mssql_fetch_array($cur_lote_lc))
						{
							$sel="";
							if($datos_lote_lc["id_actividad"]==$Lote_control)
								$sel="selected";
?>
							<option value="<? echo $datos_lote_lc["id_actividad"]; ?>" <? echo $sel; ?>><? echo strtoupper("[".$datos_lote_lc["macroactividad"]."] ".$datos_lote_lc["nombre"]); ?></option>
<?
						}

?>
			        </select>
<?
	//echo $sql_lote_control."<br>".mssql_get_last_message();
?>
				  </span></td>
			    </tr>
				<tr>
				  <td class="TituloTabla">Lote de Trabajo</td>
				  <td class="TxtTabla"><span class="TxtTabla">
				    <select name="Lote_trabajo" class="CajaTexto" id="Lote_trabajo" onChange="document.Form1.submit();">
				      <option value="">::Seleccione un lote de trabajo::</option>
<?
						$sql_lote_trabajo="SELECT * from Actividades where id_proyecto=".$cualProyecto. "and nivel=2 and dependeDe=".$Lote_control;
						$sql_lote_trabajo=$sql_lote_trabajo." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
						$cur_lote_lt=mssql_query($sql_lote_trabajo);
						while($datos_lote_lt=mssql_fetch_array($cur_lote_lt))
						{
							$sel="";
							if($datos_lote_lt["id_actividad"]==$Lote_trabajo)
								$sel="selected";
?>
							<option value="<? echo $datos_lote_lt["id_actividad"]; ?>" <? echo $sel; ?>><? echo strtoupper("[".$datos_lote_lt["macroactividad"]."] ".$datos_lote_lt["nombre"]); ?></option>
<?
						}
?>

			        </select>
				  </span></td>
			    </tr>
				<tr>
				  <td class="TituloTabla">Divisi&oacute;n</td>
				  <td class="TxtTabla"><span class="TxtTabla">

				    <select name="Division" class="CajaTexto" id="Division" onChange="document.Form1.submit();" >
				      <option value="">::Seleccione una division::</option>
<?
						$sql_div="SELECT * from Actividades where id_proyecto=".$cualProyecto. "and nivel=3 and dependeDe=".$Lote_trabajo ." and actPrincipal=".$Lote_control;
						$sql_div=$sql_div." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
						$cur_div=mssql_query($sql_div);
						while($datos_div=mssql_fetch_array($cur_div))
						{
							//almacena el id de la division, este valor se utiliza para cargar la division seleccionada en (Asociacion de participanes)

							$sel="";
							if($datos_div["id_actividad"]==$Division)
							{
								$sel="selected";
								$divis=$datos_div["id_division"]; 
								if($division==0) //si la dvision no esta seleccionada, se carga el select de la division con la division del LT
								{
									$division=$datos_div["id_division"]; 
//									echo"<script type='text/javascript'> document.Form1.submit() <script>";
								}
							}
?>
							<option value="<? echo $datos_div["id_actividad"]; ?>"  <? echo $sel; ?> ><? echo strtoupper("[".$datos_div["macroactividad"]."] ".$datos_div["nombre"]); ?>
</option>
<?
						}
?>
			        </select>
<?

// echo $division; ?>
				  </span></td>
			    </tr>
				<tr>
					<td class="TituloTabla">Actividad</td>
					<td class="TxtTabla"><span class="TxtTabla">


					  <select name="Actividad" class="CajaTexto" id="Actividad">
					    <option value="">::Seleccione una actividad::</option>
<?
						$sql_act="SELECT * from Actividades where id_proyecto=".$cualProyecto. "and nivel=4 and dependeDe=".$Division ." and actPrincipal=".$Lote_control;
						$sql_act=$sql_act." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int) ";
						$cur_act=mssql_query($sql_act);
						while($datos_act=mssql_fetch_array($cur_act))
						{
							$sel="";
							if($datos_act["id_actividad"]==$Actividad)
								$sel="selected";
?>
							<option value="<? echo $datos_act["id_actividad"]; ?>"  <? echo $sel; ?>><? echo strtoupper("[".$datos_act["macroactividad"]."] ".$datos_act["nombre"]); ?>
</option>
<?
						}
?>
			        </select>
					</span></td>
				</tr>
			</table>
		</td>
  </tr>
  <tr align="right">
    <td colspan="2" class="TxtTabla"><input name="Submit2" type="button" class="Boton" onClick="envia0();" value="Consultar"></td>
    </tr>
  <tr>
    <td class="TxtTabla">&nbsp;  </td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF">
	  <table width="100%"  border="0" cellspacing="1" cellpadding="0">

  <tr>
    <td><table width="100%"  cellspacing="1">
      <tr>
        <td colspan="3" class="TituloTabla2">Usuarios</td>
        </tr>
      <tr>
        <td colspan="3" class="TxtTabla"><table width="50%"  cellspacing="1" class="fondo">
          <tr>
            <td class="TituloTabla">Seleccionar todos los participantes</td>
            <td class="TxtTabla">Si 
              <input name="selUsu" type="radio" onClick="seleccionarUsuarios(<? echo mssql_num_rows($cursor3); ?>, 1)" value="1"></td>
            <td class="TxtTabla">No 
              <input name="selUsu" type="radio" onClick="seleccionarUsuarios(<? echo mssql_num_rows($cursor3); ?>, 0)" value="0" checked></td>
          </tr>
        </table></td>
      </tr>
      <tr class="TituloTabla2">
        <td rowspan="2">Nombre</td>
        <td colspan="2">Desasociar</td>
        </tr>
      <tr class="TituloTabla2">
        <td width="1%">Si</td>
        <td width="1%">No</td>
      </tr>
      <? 
	  if($Lote_control!=0 || $Lote_trabajo!=0 || $division != 0 || $dpto != 0 || trim($nombre) != "" || trim($unidad) != ""){
		  $i = 1;
		  while($reg3 = mssql_fetch_array($cursor3)){ 
	  ?>
	  <tr class="TxtTabla">
        <td><? echo ucwords(strtolower($reg3['unidad'] . " - " . $reg3['apellidos'] . " " . $reg3['nom_usu'])); ?>
          <input name="usuario<? echo $i; ?>" type="hidden" id="usuario<? echo $i; ?>" value="<? echo $reg3['unidad']; ?>">
        <!--  <input name="mail<? echo $i; ?>" type="hidden" id="mail<? //echo $i; ?>" value="<? //echo $reg3['email']; ?>"> --></td>
        <td align="center"><input name="aplicaUsuario<? echo $i; ?>" type="radio" value="1"></td>
        <td align="center"><input name="aplicaUsuario<? echo $i; ?>" type="radio" value="0" checked></td>
      </tr>
	  <? 
		  $i++;
		  } 
	  }
	  ?>
      <tr class="TxtTabla">
        <td colspan="3">&nbsp;</td>
        </tr>
    </table></td>
  </tr>
 
  <tr>
    <td align="right" class="TxtTabla">
	<input name="cantCargos" type="hidden" id="cantCargos" value="<? //echo mssql_num_rows($cursor4); ?>">
	<input name="cantUsuarios" type="hidden" id="cantUsuarios" value="<? echo mssql_num_rows($cursor3); ?>">	<input name="recarga" type="hidden" id="recarga" value="0">
    <input name="Submit" type="button" class="Boton" onClick="envia2();" value="Grabar"></td>
  </tr>
</table>
  	</td>
  </tr>
</table>
</form>

<?
//Finaliza las conexiones a MySql y a SQL Server
mssql_close();
mysql_close();
?>

</body>
</html>
