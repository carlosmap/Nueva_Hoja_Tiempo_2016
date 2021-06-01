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
	$sql1 = " SELECT * FROM HojaDeTiempo.dbo.Divisiones
	WHERE estadoDiv = 'A' ";
	$cursor1 = mssql_query($sql1);
	if($division == 0){
		$dpto = 0;
	} 
	else {
		$nombre = "";
		$unidad = "";
	}
	
	/*
	Departamentos de la Hoja de Tiempo
	dbo.Departamentos
	id_departamento, nombre, id_director, id_division, codDpto, codigoDAF, estadoDpto, claseDpto
	*/
	$sql2 = " SELECT * FROM HojaDeTiempo.dbo.Departamentos
	WHERE estadoDpto = 'A' ";
	$sql2 = $sql2 . " AND id_division = " . $division;
	//echo $sql2 . "<br>";
	$cursor2 = mssql_query($sql2);
	
	/*
	Usuarios de la Hoja de Tiempo
	dbo.Usuarios
	unidad, nombre, apellidos, id_departamento, id_categoria, retirado, administrador, 
	email, ContadorFallas, FechaFalla, solo_usuarios, SitioContrato, SitioTrabajo, TipoContrato, 
	Seccion, NombreCorto, unidadJefe, fechaIngreso, fechaRetiro, idEmpresa, fechaNacimiento, 
	verTarjeta, codTipoDoc, numDocumento, sexo, codUbicacion, id_departamentoANT
	*/
	$sql3 = "SELECT * FROM HojaDeTiempo.dbo.Usuarios WHERE retirado IS NULL ";
	/*
	if($dpto != 0){ #	miDpto
		$sql3 = $sql3 . " AND id_departamento = " . $dpto;
	}
	#*/
	if( $miDpto != 0 ){ #	miDpto
		$sql3 = $sql3 . " AND id_departamento = " . $miDpto;
	}
	if(isset($unidad) && trim($unidad) != ""){
		$sql3 = $sql3 . " AND unidad = " . $unidad;
	}
	if(isset($nombre) && trim($nombre) != ""){
		$sql3 = $sql3 . " AND ( nombre LIKE '%" . $nombre . "%' ";
		$sql3 = $sql3 . " OR apellidos LIKE '%" . $nombre . "%') ";
	}
	/*
	$sql3 = $sql3 . " AND unidad NOT IN ";
	$sql3 = $sql3 . " ( SELECT unidad FROM HojaDeTiempo.dbo.AutorizadosImpresion WHERE id_proyecto = " . $idProyecto . ") ";
	*/
	$sql3 = $sql3 . " ORDER BY apellidos ";
	#echo $sql3 . "<br>";
	$cursor3 = mssql_query($sql3);
	
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

	/*****************
	Grabación del Registro
	*****************/
	if($recarga == 2){
		
		$okGuardar = "Si";
		$okGuardarMsSql = "Si";
		$queryLog = "";
		$queryLogMy = "";
		$reg = $m = 1;
		#echo "Cant User ".$cantUsuarios."<br />Actividad : ".$actividad."<br />";
		#/*
		#mssql_query( "BEGIN TRANSACTION" );
		while( $reg <= $cantUsuarios ){
			$und = "und".$reg;
			$opt = "aplicaUsuario".$reg;
			if( ${$opt} == 1 ){
				$sqlActRes = "Insert Into ParticipantesActividad ( id_proyecto, id_actividad, unidad, estado ) 
							  Values( ".$cualProyecto.", ".$actividad.", ".${$und}.", 1 )";
				$qryActRes = mssql_query( $sqlActRes );
				if( !$qryActRes )
					$okGuardarMsSql = "No";
				#echo $sqlActRes."<br />";
			}
			$reg++;	
		}
		if( $okGuardarMsSql == "No" ){
			echo "<script type='text/javascript' language='javascript'>
					alert('No se almaceno la información.');
				  </script>";
			#echo mssql_get_last_message();
		}
		else{
			echo "<script type='text/javascript' language='javascript'>
					alert('Informacion almacenada.');
					window.close();
					MM_openBrWindow( 'htProgProyectos02OML.php?cualProyecto=".$cualProyecto."', 'winHojaTiempo', '' )
				  </script>";
		}
			
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//ES" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Autorizaci&oacute;n de Impresiones</title>
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

function seleccionarCargos(cantidad, opcion){
	var cantCar = parseInt(cantidad);
	var expr1 = '';
	var expr2 = '';
	for(var i = 1; i <= cantCar; i++){
		if(opcion == 1){
			expr1 = 'document.Form1.aplicaCargo' + i + '[0].checked = true';
			expr2 = 'document.Form1.aplicaCargo' + i + '[1].disabled = true';
		} else if(opcion == 0){
			expr1 = 'document.Form1.aplicaCargo' + i + '[1].disabled = false';
			expr2 = 'document.Form1.aplicaCargo' + i + '[1].checked = true';
		}
		eval(expr1);
		eval(expr2);
	}
}

function envia1(){
	document.Form1.recarga.value = 1;
	document.Form1.submit();
}

function envia2(){
	
	var error = 'n';
	var mensaje = '';
	var v1, v2, v3, m1, m2, m3, msg;
	var registros = document.getElementById('cantUsuarios').value - 1;
	var i = 1;

	v1 = 1
	v2 = v3 = 0;
	m1 = "Debe seleccionar almenos una persona.\n";
	m2 = m3 = msg = "";
	for( i = 1; i <= registros; i++ ){				
		if( document.getElementById('aplicaUsuario' + i ).checked != false ){
			v1= 0;
			m1 = "";
		}
	}
	if( registros == -1 ){
		v2 = 1; 
		m2 = "Asegurece de hacer el filtro para elegir una persona.";
	}
	if( document.getElementById('actividad').value == "" ){
		v3 = 1;
		m3 = "Debe seleccionar una actividad.\n";
	}
	//alert( 'Superado. Campos en cero : ' + val + '; Registros : ' + registros );
	//Verifica que haya seleccionado una división y un departamento
	
	
	//Finalización de la validación
	/*
	if(error == 's'){
		
	} else {
	*/
	//alert( 'V1 : '+v1+'; V2 : '+v2+'; Registros : '+registros );
	if( ( v1 == 0 ) && ( v2 == 0 ) && ( v3 == 0 ) ){
		document.Form1.recarga.value = 2;
		document.Form1.submit();
	}
	else{
		msg = m1 + m2 + m3;
		alert( msg );
	}
	//}
}

</script>
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="#EAEAEA">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Programación de Proyectos - Autorizados para participan en el proyecto </td>
  </tr>
</table>

<form action="" method="post" name="Form1"  >
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="25%" class="TituloTabla">Proyecto</td>
    <td class="TxtTabla"><? echo $idProyecto . " - [" . $cargoDefecto . "] - " . ucwords(strtolower($nombreProyecto)); ?></td>
  </tr>
	<?
		$sqlPerfil = "select nivel from HojaDeTiempo.dbo.Actividades Where id_proyecto = ".$cualProyecto." AND id_encargado = ".$_SESSION['sesUnidadUsuario'];
		$qryPerfil = mssql_fetch_array( mssql_query( $sqlPerfil ) );		
			echo $sqlPerfil."<br />";
		#	Valida que el usuario corresponda al responsable del lote de control
		if( $qryPerfil[nivel] == 1 ){
	?>  
    <tr>
    <td class="TituloTabla">Lote de control</td>
    <td align="left" class="TxtTabla">
		<?
			$sqlLc = "select * from HojaDeTiempo.dbo.Actividades Where id_proyecto = ".$cualProyecto." AND nivel = 1 AND id_encargado = ".$_SESSION['sesUnidadUsuario'];
			$qryLc = mssql_query( $sqlLc );	
			#echo $sqlLc."<br />";		
		?>
      <select name="lc" class="CajaTexto" id="lc" onChange="document.Form1.submit();" >
        <option value="" >:::Todos los Lotes de control:::</option>
        <? while ( $rwLc = mssql_fetch_array( $qryLc )) { 	
					if ($lc == $rwLc[id_actividad]) 
						$selLc = "selected";
					else 
						$selLc = "";
			?>
        <option value="<?= $rwLc[id_actividad]; ?>" <? echo $selLc; ?> >
          <?= "[ ".$rwLc[macroactividad]." ]".ucwords(strtolower($rwLc[nombre])) ; ?>
          </option>
        <? } ?>
      </select></td>
  </tr>
  <?	}
  		#	Verifica que corresponda al lote de trabajo
		if( $qryPerfil[nivel] == 2 || $qryPerfil[nivel] == 1 ){ 
  ?>
  <tr>
    <td class="TituloTabla">Lote de trabajo</td>
    <td align="left" class="TxtTabla"><?			
			$sqlLt = "select * from HojaDeTiempo.dbo.Actividades Where id_proyecto = ".$cualProyecto." AND nivel = 2 ";
			if( $qryPerfil[nivel] == 2 )
				$sqlLt .= " AND id_encargado = ".$_SESSION['sesUnidadUsuario'];
				
			if( trim($lc) != "" )
				$sqlLt .= "AND dependeDe = ".$lc;			
			#echo $sqlLt."<br />";
			$qryLt = mssql_query( $sqlLt );			
		?>
      <select name="lt" class="CajaTexto" id="lt" onChange="document.Form1.submit();" >
        <option value="" >:::Todas los Lotes de trabajo:::</option>
        <? while ($rwLt=mssql_fetch_array($qryLt)) { 	
					if ($lt == $rwLt[id_actividad]) 
						$selLt = "selected";
					else 
						$selLt = "";
			?>
        <option value="<?= $rwLt[id_actividad]; ?>" <? echo $selLt; ?> >
          <?= "[ ".$rwLt[macroactividad]." ] ".ucwords(strtolower($rwLt[nombre])) ; ?>
          </option>
        <? } ?>
      </select></td>
  </tr>
  <?	}
  		#	Verifica que corresponda al lote de trabajo
		if( $qryPerfil[nivel] == 3 || $qryPerfil[nivel] == 2 || $qryPerfil[nivel] == 1 ){ 
  ?>
  
  <tr>
    <td class="TituloTabla">Divisi&oacute;n</td>
    <td align="left" class="TxtTabla">
	<?
	
			$sqlDv = "select distinct macroactividad, id_division, nombre from HojaDeTiempo.dbo.Actividades Where id_proyecto = ".$cualProyecto." AND nivel = 3";
			if( $qryPerfil[nivel] == 3 )
				$sqlLt .= " AND id_encargado = ".$_SESSION['sesUnidadUsuario'];

			if( trim($lt) != "" )
				$sqlDv .= "AND dependeDe = ".$lt;
			#echo $sqlDv."<br />";		
			$qryDv = mssql_query( $sqlDv );			
		?>
      <select name="pfDivision" class="CajaTexto" id="pfDivision" onChange="document.Form1.submit();" >
        <option value="" >:::Todas las Divisiones:::</option>
        <?	while ($fDivReg=mssql_fetch_array($qryDv)) { 	
                	if ($pfDivision == $fDivReg[id_division]) 
						$selDiv = "selected";
					else 
						$selDiv = "";            
            ?>
        <option value="<? echo $fDivReg[id_division]; ?>" <? echo $selDiv; ?> >
          <?= "[ ".$fDivReg[macroactividad]." ] ".ucwords(strtolower($fDivReg[nombre])) ; ?>
          </option>
        <? } ?>
      </select></td>
  </tr>
  <?	}	?>
  <tr>
    <td class="TituloTabla">Departamento</td>
    <td align="left" class="TxtTabla"><?
	//Trae los departamentos asociados la divisi&oacute;n seleccionada
	$dTSql="Select * from departamentos where id_division = " . $pfDivision . " and estadoDpto = 'A' order by nombre" ;
	$dTcursor = mssql_query($dTSql);
	
	?>
      <select name="miDpto" class="CajaTexto" id="miDpto" onChange="document.Form1.submit();" >
        <? if ($miDpto == "") { 
			$selItem="selected";
		}
	?>
        <option value="" <? echo $selItem; ?> >:::Todos:::</option>
        <? while ($regdT=mssql_fetch_array($dTcursor)) { 
			if ($miDpto == $regdT[id_departamento]) {
				$selIt="selected";
			}
			else {
				$selIt="";
			}
	?>
        <option value="<?= $regdT[id_departamento]; ?>" <? echo $selIt; ?> ><?= ucwords(strtolower($regdT[nombre])) ; ?></option>
        <? } ?>
      </select></td>
  </tr>
  <tr>
    <td class="TituloTabla">Actividad</td>
    <td class="TxtTabla">
      <?
		$sqlActividad = "SELECT macroactividad, id_actividad, nombre, id_proyecto, id_encargado FROM Actividades 
						 WHERE dependeDe in( Select id_actividad from Actividades where id_division = ".$pfDivision." AND id_proyecto = ".$cualProyecto." ) 
						 AND id_proyecto = ".$cualProyecto;
		$qryActividad = mssql_query( $sqlActividad );
		#echo $sqlActividad;
	?>
      <select name="actividad" class="CajaTexto" id="actividad" onChange="document.Form1.submit();">      
        <option value="">::: Seleccione una Actividad :::</option>
        <?
	  
		while($reg2 = mssql_fetch_array($qryActividad)){ 
			$optDpto = "";
			if($actividad == $reg2[id_actividad]){
				$optDpto = "selected";
			}
			else
				$optDpto = "";
		?>
        <option value="<?= $reg2[id_actividad] ?>" <?= $optDpto ?> ><?= "[ ". $reg2[macroactividad]." ]".$reg2[nombre]; ?></option>
        <? } ?>
        </select></td>
  </tr>
  <tr>
    <td class="TituloTabla">Nombre</td>
    <td class="TxtTabla"><input name="nombre" type="text" class="CajaTexto" id="nombre" value="<? echo $nombre; ?>" size="50"></td>
  </tr>
  <tr>
    <td class="TituloTabla">Unidad</td>
    <td class="TxtTabla">
    <input name="unidad" type="text" class="CajaTexto" id="unidad" onKeyPress="return campoNumerico(event);" value="<? echo $unidad; ?>" size="10"></td>
  </tr>
  <tr align="right">
    <td colspan="2" class="TxtTabla"><input name="Submit2" type="button" class="Boton" onClick="envia1();" value="Consultar"></td>
    </tr>
  <tr>
    <td colspan="2"><table width="100%"  cellspacing="1">
      <tr>
        <td colspan="3" class="TituloTabla2">Usuarios</td>
        </tr>
      <tr>
        <td colspan="3" class="TxtTabla"><table width="50%"  cellspacing="1" class="fondo">
          <tr>
            <td class="TituloTabla">Seleccionar todos los usuarios </td>
            <td class="TxtTabla">Si 
              <input name="selUsu" type="radio" onClick="seleccionarUsuarios(<? echo mssql_num_rows($cursor3); ?>, 1)" value="1"></td>
            <td class="TxtTabla">No 
              <input name="selUsu" type="radio" onClick="seleccionarUsuarios(<? echo mssql_num_rows($cursor3); ?>, 0)" value="0" checked></td>
          </tr>
        </table></td>
      </tr>
      <tr class="TituloTabla2">
        <td rowspan="2">Nombre</td>
        <td colspan="2">Aplica</td>
        </tr>
      <tr class="TituloTabla2">
        <td width="1%">Si</td>
        <td width="1%">No</td>
      </tr>
      <? 
	  if($miDpto != 0 || trim($nombre) != "" || trim($unidad) != ""){
		  $i = 1;
		  while($reg3 = mssql_fetch_array($cursor3)){
			  $sqlRA = "select COUNT(*) registrado from HojaDeTiempo.dbo.ResponsablesActividad where id_proyecto = ".$cualProyecto." and unidad = ".$reg3['unidad']." and id_actividad = ".$actividad;
			  
			  $qryRA = mssql_fetch_array( mssql_query( $sqlRA ) );			  
			  #echo "<tr><td>".$sqlRA." - ".$qryRA[registrado]."</td></tr>";
			  if( $qryRA[registrado] == 0 ){
	  ?>
                  <tr class="TxtTabla">
                    <td><?= ucwords(strtolower($reg3['unidad'] . " - " . $reg3['apellidos'] . " " . $reg3['nombre'])); ?>
                      <input name="usuario<? echo $i; ?>" type="hidden" id="usuario<? echo $i; ?>" value="<? echo $reg3['unidad']; ?>">
                      <input name="mail<? echo $i; ?>" type="hidden" id="mail<? echo $i; ?>" value="<? echo $reg3['email']; ?>">
                      <input name="und<? echo $i; ?>" type="hidden" id="und<? echo $i; ?>" value="<? echo $reg3['unidad']; ?>">
                      </td>
                    <td align="center"><input name="aplicaUsuario<?= $i ?>" type="radio" value="1"></td>
                    <td align="center"><input name="aplicaUsuario<?= $i ?>" type="radio" value="0" checked></td>
                  </tr>
	  <? 
			  }
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
    <td colspan="2" align="right" class="TxtTabla">
	<input name="cantCargos" type="hidden" id="cantCargos" value="<? echo mssql_num_rows($cursor4); ?>">
	<!-- <input name="cantUsuarios" type="hidden" id="cantUsuarios" value="<? echo mssql_num_rows($cursor3); ?>">	-->
    <input name="cantUsuarios" type="hidden" id="cantUsuarios" value="<?= $i ?>">	
    <input name="recarga" type="hidden" id="recarga" value="1">
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
