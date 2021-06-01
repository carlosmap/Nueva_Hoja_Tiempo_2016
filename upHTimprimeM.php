<script language="JavaScript" type="text/JavaScript">
function MM_openBrWindow(theURL,winName,features) { 
  window.open(theURL,winName,features);
}
</script>

<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";


//Valida que solo el personal de contratos y administrador de sistemas pueda ingresar a esta ventana
if ( ($_SESSION["sesPerfilUsuario"] != 16) AND ($_SESSION["sesPerfilUsuario"] != 1) ) {
	echo ("<script>alert('Usted no tiene permiso de acceder a esta ventana. Por favor solicite el cambio de perfil con el departamento de contratos.');</script>");
	echo ("<script>window.close();</script>");
	exit;
}


if($recarga==2)
{
	mssql_query("BEGIN TRANSACTION");
//	echo $cant_reg."----<br>";
	for($z=1; $z<$cant_reg; $z++)
	{
		$uni="unidad".$z;
		$pImpime="pImpime".$z;
		$error="no";
//echo $$uni." ---- ".$$pImpime." <br>";

		//Actualiza la tabla para mostrar en el sistema si ya se imprimió o no.
		$query = "UPDATE  VoBoFirmasHT SET "; 
		$query = $query . " seImprimio = '" . $$pImpime . "'  ";
		$query = $query . " WHERE vigencia = " . $pAno ;
		$query = $query . " AND mes = " . $pMes ;
		$query = $query . " AND unidad = ".$$uni ;
		$cursor = mssql_query($query) ;	

		if(trim($cursor)=="")
		{
			$error="si";
		}
//echo $query." <br><br>";

	}
	if($error=="no")
	{
		mssql_query(" COMMIT TRANSACTION");
		echo "<script>alert('La operación se realizó con éxito')</script>";
	}
	else
	{
		mssql_query(" ROLLBACK TRANSACTION");
		echo ("<script>alert('Error durante la grabación');</script>");
	} 

	echo ("<script>window.close();MM_openBrWindow('htContratosHT.php?pMes=".$pMes."&pAno=".$pAno."&pEmpresa=".$pEmpresa."&pDivision=".$pDivision."&pDepto=".$pDepto."&pUnidad=".$pUnidad."&pCategoria=".$pCategoria."&pRetirado=".$pRetirado."&pNombre=".$pNombre."','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=*,height=*');</script>");
}


//inicializa el valor de retirado
if (trim($pRetirado) == "") {
	$pRetirado = "0";
}
//Seleccionar los usuarios registrados a través de la Hoja de tiempo, que tienen aprobada la hoja de tiempo por parte del jefe 
//@mssql_select_db("HojaDeTiempo",$conexion);
$sql2="select u.fechaRetiro, u.retirado, u.unidad, u.nombre, u.apellidos, u.email , u.id_departamento, d.nombre as departamento, d.id_division,   ";
$sql2= $sql2. " v.nombre as division, v.id_dependencia, x.nombre as dependencia ,  ";
$sql2= $sql2. " u.id_categoria, c.nombre as categoria, VoBoFirmasHT.seImprimio ";
$sql2= $sql2. " from usuarios u 
inner join Departamentos d on u.id_departamento=d.id_departamento
inner join Divisiones v on d.id_division= v.id_division
inner join Dependencias x on v.id_dependencia=x.id_dependencia
inner join Categorias c on u.id_categoria=c.id_categoria


inner join VoBoFirmasHT on VoBoFirmasHT.unidad=u.unidad and VoBoFirmasHT.vigencia=".$pAno." and VoBoFirmasHT.mes=".$pMes." ";

//SI SE CONSULTA LOS USUARIOS CON VOBO (APROBADO / NO APROBADO)
/*6
if(($revision==2)||($revision==3)||($revision==5))
{
	$sql2= $sql2. " inner join VoBoFirmasHT on VoBoFirmasHT.unidad=u.unidad and VoBoFirmasHT.vigencia=".$pAno." and VoBoFirmasHT.mes=".$pMes;
}
*/

$sql2= $sql2. " where u.id_departamento = d.id_departamento ";
$sql2= $sql2. " and d.id_division = v.id_division  ";
$sql2= $sql2. " and v.id_dependencia = x.id_dependencia ";
$sql2= $sql2. " and u.id_categoria = c.id_categoria and VoBoFirmasHT.validaJefe=1 ";
//Para que muestre la Hojas de tiempo de los usuarios retirados

//SI SE CONSULTA LOS USUARIOS CON VOBO APROBADO
if($revision==2)
{
	$sql2= $sql2. " and VoBoFirmasHT.validaContratos=1 ";
}

//SI SE CONSULTA LOS USUARIOS CON VOBO NO APROBADO
if($revision==3)
{
	$sql2= $sql2. " and VoBoFirmasHT.validaContratos=0 ";
}
/*
//USUARIOS QUE NO HAN ENVIADO LA H.T. AL JEFE O NO LO HAN ESPECIFICADO
if($revision==4)
{
	

	$sql2= $sql2. " and u.unidad not in ( select unidad from VoBoFirmasHT where  VoBoFirmasHT.unidad=u.unidad and VoBoFirmasHT.vigencia=".$pAno." and VoBoFirmasHT.mes=".$pMes.") ";

}

//USUARIOS QUE HAN ENVIADO LA H.T. AL JEFE, Y QUE NO HAN SIDO APROBADAS
if($revision==5)
{
	$sql2= $sql2. " and VoBoFirmasHT.validaJefe=0 ";
}
*/
//USUARIOS ACTIVOS 
if (($pRetirado == "") OR ($pRetirado == "0")) {
	$sql2= $sql2. " and u.retirado IS NULL ";
}

//SE CONSULTAN LOS USUARIOS RETIRADOS EN EL MES SELECCIONADO Y/O EL MES ACTUAL
if ($pRetirado == "1") {

		//USUARIOS RETIRADOS
		$sql2= $sql2. " and u.retirado IS NOT NULL ";

		if ($pMes == "") {
			$sql2= $sql2. " and (month(fechaRetiro)= ".date("m")." and year(fechaRetiro)=".date("Y").") ";
		}
		else {
			$sql2= $sql2. " and (month(fechaRetiro)= ".$pMes." and year(fechaRetiro)=".$pAno.") ";
		}
}


if(trim($pEmpresa)!="")
{
	$sql2= $sql2. " and idEmpresa=".$pEmpresa;
}

if (($pDivision != "") AND ($pDivision != "0")) {
	$sql2= $sql2. " and d.id_division = " . $pDivision;
}
if (($pDepto != "") AND ($pDepto != "0")) {
	$sql2= $sql2. " and u.id_departamento = " . $pDepto;
}

if ($pUnidad != "") {
	$sql2= $sql2. " and u.unidad = " . $pUnidad;
}
if ($pCategoria != "") {
	$sql2= $sql2. " and u.id_categoria = " . $pCategoria;
}
if ($pNombre != "") {
	$sql2= $sql2. " and (u.nombre LIKE '%".$pNombre."%' or u.apellidos LIKE '%".$pNombre."%')";
}

//SI SE SELECCIONO EN EL CAMPO  Resvisión H.T. contratos LA OPCION Usuarios que deben facturar Y Ver usuarios retirados ? CON LA OPCION Todos
//SE HACE UNA CONSULTA DE UNION, CON LOS USUARIOS ACTIVOS Y LO RETIRADOS EN LA FECHA SELECCIONADA
if (($pRetirado == "2") &&($revision==1))
{
	$sql21=" select * from ( ( ";
	$sql21=$sql21.$sql2." and u.retirado IS NOT NULL ";

	if ($pMes == "") {
		$sql21=$sql21. " and (month(fechaRetiro)= ".date("m")." and year(fechaRetiro)=".date("Y").") ";
	}
	else {
		$sql21=$sql21. " and (month(fechaRetiro)= ".$pMes." and year(fechaRetiro)=".$pAno.") ";
	}

	$sql21=$sql21.") union (";
	$sql2=$sql21.$sql2." and u.retirado IS NULL )) u";

}



//$sql2= $sql2. " order by u.apellidos ";
$sql2= $sql2. " order by categoria , u.unidad ";


$cursor = mssql_query($sql2);
//echo $sql2." <br> ******** ".mssql_get_last_message()." <br>retirad ".$pRetirado ." revision: ".$revision. " cant Reg: ".mssql_num_rows($cursor );


?>
<html>
<head>

<title>.:: Aprobaci&oacute;n de la Hoja de Tiempo - contratos ::.</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
window.name="winHojaTiempo";
</script>
<SCRIPT language=JavaScript>

function valida()
{
/*
	var cant=document.form1.cant_reg.value, cant_sel=0;

	for(var i=1; i<= cant; i++)
	{
		if(document.getElementById("pImpime"+i).value=="")
		{
			cant_sel++;
		}
	}

	if(cant_sel==cant)
	{
		alert("Por favor seleccione almenos un ");
	}
	else
	{
*/
		document.form1.recarga.value=2;
		document.form1.submit();
//	}

}
</SCRIPT>

</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0"  bgcolor="E6E6E6">
<?PHP
/*
//CONSULTA SI EL USUARIO, TIENE PERFIL DE CONTRATOS, YA QUE ELLOS SON LOS UNICOS QUE PUEDEN DAR EL VOBO DE CONTRATOS
$sql_usu_contratos="select Usuarios.*  from Usuarios  
inner join GestiondeInformacionDigital.dbo.PerfilUsuarios on GestiondeInformacionDigital.dbo.PerfilUsuarios.unidad=Usuarios.unidad
where retirado is null
and ( GestiondeInformacionDigital.dbo.PerfilUsuarios.codPerfil=16 
or GestiondeInformacionDigital.dbo.PerfilUsuarios.codPerfil=1 )
and GestiondeInformacionDigital.dbo.PerfilUsuarios.unidad=".$laUnidad;


$cur_contratos=mssql_query($sql_usu_contratos);

//echo $sql_usu_contratos." **** ".mssql_num_rows($cur_contratos);
if( ( (int) mssql_num_rows($cur_contratos)) >0 )
{
*/
?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>



<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Consulta de otros periodos 
	<?
		if ($pMes == "") {
			$miMesHT = gmdate ("n");
			$MiAnnoHT = gmdate ("Y");
		}
		else {
			$miMesHT = $pMes;
			$MiAnnoHT = $pAno;
		}
	?>
	</td>
  </tr>
</table>
<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td bgcolor="#FFFFFF">
	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="1">


  <tr>
    <td width="15%" align="center" class="TituloTabla">Mes:&nbsp;</td>
    <td width="30%" class="TxtTabla">
	<? 
	//Seleccionar el mes cuando se carga la página por primera vez
	//si no cuando se recarga la página
	if ($pMes == "") {
		$mesActual=date("m"); //el mes actual
	}
	else {
		$mesActual= $pMes; //el mes seleccionado
	}

	$selMes1 = "";
	$selMes2 = "";
	$selMes3 = "";
	$selMes4 = "";
	$selMes5 = "";
	$selMes6 = "";
	$selMes7 = "";
	$selMes8 = "";
	$selMes9 = "";
	$selMes10 = "";
	$selMes11 = "";
	$selMes12 = "";
	for($m=1; $m<=12; $m++) {
		if (($m == $mesActual) AND ($m == 1)) {
			$selMes1 = "selected";
		}
		if (($m == $mesActual) AND ($m == 2)) {
			$selMes2 = "selected";
		}
		if (($m == $mesActual) AND ($m == 3)) {
			$selMes3 = "selected";
		}
		if (($m == $mesActual) AND ($m == 4)) {
			$selMes4 = "selected";
		}
		if (($m == $mesActual) AND ($m == 5)) {
			$selMes5 = "selected";
		}
		if (($m == $mesActual) AND ($m == 6)) {
			$selMes6 = "selected";
		}
		if (($m == $mesActual) AND ($m == 7)) {
			$selMes7 = "selected";
		}
		if (($m == $mesActual) AND ($m == 8)) {
			$selMes8 = "selected";
		}
		if (($m == $mesActual) AND ($m == 9)) {
			$selMes9 = "selected";
		}
		if (($m == $mesActual) AND ($m == 10)) {
			$selMes10 = "selected";
		}
		if (($m == $mesActual) AND ($m == 11)) {
			$selMes11 = "selected";
		}
		if (($m == $mesActual) AND ($m == 12)) {
			$selMes12 = "selected";
		}



	}
	
	?>
      <select name="pMes" class="CajaTexto" id="pMes" disabled >
      <option value="1" <? echo $selMes1; ?> >Enero</option>
      <option value="2" <? echo $selMes2; ?>>Febrero</option>
      <option value="3" <? echo $selMes3; ?>>Marzo</option>
      <option value="4" <? echo $selMes4; ?>>Abril</option>
      <option value="5" <? echo $selMes5; ?>>Mayo</option>
      <option value="6" <? echo $selMes6; ?>>Junio</option>
      <option value="7" <? echo $selMes7; ?>>Julio</option>
      <option value="8" <? echo $selMes8; ?>>Agosto</option>
      <option value="9" <? echo $selMes9; ?>>Septiembre</option>
      <option value="10" <? echo $selMes10; ?>>Octubre</option>
      <option value="11" <? echo $selMes11; ?>>Noviembre</option>
      <option value="12" <? echo $selMes12; ?>>Diciembre</option>
    </select></td>
	</tr>

	<tr>
    <td width="15%" align="center" class="TituloTabla">A&ntilde;o:&nbsp;</td>
    <td class="TxtTabla">

	<select name="pAno" class="CajaTexto" id="pAno" disabled > 
	<? 
	//Generar los años de 2006 a 2050
	for($i=2006; $i<=2050; $i++) { 
		
		//seleccionar el año cuando se carga la página por primera vez
		if ($pAno == "") {
			$AnoActual=date("Y"); //el año actual
		}
		else {
			$AnoActual= $pAno; //el año seleccionado
		}
		
		if ($i == $AnoActual) {
			$selAno = "selected";
		}
		else {
			$selAno = "";
		}
	?>
      <option value="<? echo $i; ?>" <? echo $selAno; ?> ><? echo $i; ?></option>
	 <? 
	 	
	 } //for 
	 
	 ?>

    </select>	</td>
	</tr>
</table>
	</td>
  </tr>
</table>
<form name="form1" method="post" action="">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TxtTabla">&nbsp;</td>
  </tr>
<td class="TxtTabla"><strong>Cantidad de usuarios que cumplen el criterio de consulta</strong>: <?=mssql_num_rows($cursor); ?></td>
  <tr>
    <td class="TituloUsuario">.:: Aprobaci&oacute;n de la Hoja de tiempo - Contratos </td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF"><table width="100%" border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td width="10%" rowspan="2">Unidad</td>
        <td width="30%" rowspan="2">Usuario</td>
        <td rowspan="2">&nbsp;</td>
        <td rowspan="2">Categor&iacute;a</td>
        <td rowspan="2">Divisi&oacute;n</td>
        <td rowspan="2">Departamento</td>
        <td colspan="2">Hoja de tiempo impresa en contratos?</td>
        </tr>
      <tr class="TituloTabla2">
        <td width="2%">Si</td>
        <td width="1%">No</td>
        </tr>
	  <?
	  $i=1;
	  while ($reg=mssql_fetch_array($cursor)) {
	  ?>
      <tr class="TxtTabla">
        <td width="10%"><? echo $reg[unidad]; ?><a name="ancla<? echo $reg[unidad]; ?>" id="ancla<? echo $reg[unidad]; ?>"></a>
          <input type="hidden" name="unidad<?=$i ?>" id="unidad<?=$i ?>" value="<? echo $reg[unidad]; ?>" ></td>
        <td width="30%"><? echo ucwords(strtolower($reg[apellidos])) . " " . ucwords(strtolower($reg[nombre])) ; ?></td>
        <td width="2%" align="center">
<?
			if($reg[retirado]==1)
			{
?>

			<img src="imagenes/Inactivo.gif" title="Retirado de la compa&ntilde;ia" />
<?
			}
?>
		</td>

        <td><? echo strtoupper($reg[categoria])  ; ?></td>
        <td><? echo ucwords(strtolower($reg[division]))  ; ?></td>
        <td><? echo ucwords(strtolower($reg[departamento]))  ; ?></td>
        <td width="2%" align="center">

          <input name="pImpime<?=$i ?>" type="radio" id="pImpime<?=$i ?>" <? if($reg["seImprimio"]==1){ echo "checked"; } ?> value="1" >
		</td>
        <td width="1%"><input type="radio" name="pImpime<?=$i ?>" id="pImpime<?=$i ?>" <? if( ((int) $reg["seImprimio"])==0){ echo "checked"; } ?> value="0">
		</td>
        </tr>
	  <?
		$i++;
	  }
	  ?>
    </table>
    </td>
  </tr>
  <tr>
    <td align="right" class="TxtTabla" >&nbsp;</td>
  </tr>
  <tr>
    <td align="right" class="TxtTabla" >  <input name="grabar" type="submit" class="Boton" id="grabar" onClick="valida()" value="Grabar"> <input name="cerrar" type="submit" class="Boton" id="cerrar" value="Cancelar" onClick="window.close();" >
		<input type="hidden" name="cant_reg" id="cant_reg" value="<?=$i ?>" >
		<input type="hidden" name="recarga" id="recarga" value="1" >

	</td>
    </tr>
</table>
        </form>
<? 
/*
}
else
{
	echo '<table width="100%" border="0" cellspacing="0" cellpadding="0">

			  <tr>
				<td class="TxtTabla">&nbsp;</td>
			
			  </tr>
			  <tr>
				<td class="TituloUsuario">.:: Atenci&oacute;n</td>
			
			  </tr>

			  <tr>
				<td align="center" class="TxtTabla"  ><BR>
				<b>Usted no est&aacute; autorizado, para acceder a la informaci&oacute;n de esta p&aacute;gina. </b><BR><BR>
				</td>
			  </tr>
			  <tr>
				<td align="center" class="TituloTabla2"  >
					<input type="button" value="Cerrar" class="Boton" onClick="window.close()" >
				</td>
			  </tr>
			</table>';
}
*/
mssql_close ($conexion); ?>	
    <p>&nbsp;</p>
</body>
</html>


