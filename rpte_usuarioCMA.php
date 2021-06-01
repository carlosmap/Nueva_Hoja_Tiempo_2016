<?php
 	session_start();
	include "funciones.php";
	include "validacion.php";
	include "validaUsrBd.php";
	
	//se realiza el conteo de los usuarios activos
	$sql="select COUNT(*) as cant_activos from Usuarios where fechaRetiro  is   NULL and fechaIngreso is not null";		
	$valores=mssql_query($sql);
	if($dato=mssql_fetch_array($valores))
	{
		$usu_activos=$dato["cant_activos"];
	
	}
	
	//Trahemos los años almacenados en la BD
	$indi=0;
	$sql2="select distinct Year(fechaIngreso) as ano  from Usuarios where fechaIngreso is not null and '1990'<fechaIngreso order by (ano)";
	$valores2=mssql_query($sql2);
	while($dato2=mssql_fetch_array($valores2))
	{		
		$anos[$indi]=$dato2["ano"];
		$indi++;
	}



$meses_ano=array(" ","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

//Trahemos el año actual

if(!(isset($Anoss))) //verificamos si la variable ha sido creada para cargar los datos predefinidos que se cargaran al consultar la pagina
{
	$sql3="select distinct year(getdate()) as ano from Usuarios";		
	$valores3=mssql_query($sql3);
	if($dato3=mssql_fetch_array($valores3))
	{
		$ano=$dato3["ano"];
	
	}
}
else { $ano=$Anoss; }  //sino asignamos el año seleccionado por el usuario en la busqueda 

//Trahemos los meses del año actual
	$mes=1;
	$sql4="select distinct MONTH(fechaIngreso) as meses from Usuarios where fechaIngreso is not null and YEAR(fechaIngreso)=".$ano;
	$valores4=mssql_query($sql4);
	while($dato4=mssql_fetch_array($valores4))
	{
		$mes++;		
		$meses[$mes]=$dato4["meses"];

	}

  
	//Busca el nombre del usuario que se le solicitan las actividades
/*	
	include "validaUsrBd.php";
	$sql="SELECT Usuarios.nombre as nombre, Usuarios.apellidos as apelli, Categorias.nombre as categoria
		FROM Usuarios INNER JOIN Categorias ON Usuarios.id_categoria = Categorias.id_categoria
		WHERE     (Usuarios.unidad = '$und')";
	if ($res=mssql_query($sql)) {
		$fil=mssql_fetch_array($res);
		$categ = $fil[categoria];
		$nombUsrConsultado=$fil[nombre];
		$apelUsrConsultado=$fil[apelli];
	}
	*/
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Reporte Hoja de Tiempo</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script>
var newwindow;
function vermuestraventana(url)
{
	newwindow=window.open(url,'name','height=500,width=550, resizable=yes,scrollbars=yes');
	if (window.focus) {newwindow.focus()}
}
</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><? include("bannerArriba.php") ; ?></td>
  </tr>
</table>
	<div class="TxtNota1" style="position:absolute; left:258px; top:8px; width: 365px;">
		<div align="center"> REPORTE HOJA DE TIEMPO </div>
	</div>
<p>

</p>
<form action="" name="form1">
<table width="100%">
	<tr><td class="fondo">
<table align="center" border=0 width="100%">
	<tr><td class="TituloUsuario" colspan="4">Grerencia</td></tr>
  <tr> 
    <td colspan="3" class="TituloTabla">Cantidad de Usuarios Activos</td>
    <td class='TxtTabla' align='center'> <?php echo number_format($usu_activos, 0, "", "."); ?></td>
  </tr>
     <tr>
     	<td class="TituloTabla">Busqueda por a&ntilde;o</td>
        <td class="TxtTabla" align="center"><select name="Anoss" id="Anoss" class="CajaTexto" onChange="document.form1.submit();">
        <?php	

		for($a=0;$a<$indi;$a++)
		{
			$sel="";			
			if($anos[$a]==$ano)
			{
				$sel="selected";
			}
			echo "<option ".$sel."  >".$anos[$a]."</option>";
		}
		?>
        </select>
        </td>
        <td colspan=2 class="TxtTabla"></td>
     </tr>
  <tr>
  	<td class="TituloTabla2"  >A&ntilde;o</td>
    <td class="TituloTabla2">Mes</td>
    <td class="TituloTabla2">Ingresos</td>
    <td class="TituloTabla2">Retiros</td>
  </tr>
  <tr><td rowspan=13 class='TxtTabla' align='center'><?php echo $ano; ?></td>
	<?php	
		$mes2=1;
		while($mes2<=12)
		{ ?>
				<tr class='TxtTabla' align='center'><td > <?php echo $meses_ano[$mes2]; ?></td>
          <?php  
			//trahemos la cantidad de ingresos de un mes especifico
			
			$sql5="select COUNT(fechaIngreso)as canti_ingresos from Usuarios where year(fechaIngreso)=".$ano." and MONTH(fechaIngreso)=".$mes2;
			$valores5=mssql_query($sql5);
			if($dato5=mssql_fetch_array($valores5))
			{ ?>
				<td class='TxtTabla' align='center'><?php echo $dato5["canti_ingresos"]; ?></td>
		<?php	
			}
			
			//trahemos la cantidad de retiros de un mes especifico
			$sql6="select count(fechaRetiro) as canti_retiro from Usuarios where YEAR(fechaRetiro)=".$ano." and MONTH(fechaRetiro)=".$mes2." and fechaIngreso is not null";
			$valores6=mssql_query($sql6);
			if($dato6=mssql_fetch_array($valores6))
			{
			?>	
				<td class='TxtTabla' align='center'><?php echo $dato6["canti_retiro"]; ?></td></tr>				
             <?php   
			}
			$cantidad_ingresos +=$dato5["canti_ingresos"];
			$cantidad_retiros +=$dato6["canti_retiro"];
			$mes2++;
		}
		?>
		
		<tr> <td class='TxtTabla'>&nbsp;</td> 
		<td class='TituloTabla2' align='center'>Total</td> <td class='TxtTabla' align='center'><?php echo $cantidad_ingresos; ?></td>
		<td class='TxtTabla' align='center'><?php echo $cantidad_retiros; ?></td></tr>


</table>
</td></tr></table>
</body>
</html>
