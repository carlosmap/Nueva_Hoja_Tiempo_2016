<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
<?
session_start();
$usuarioPrinc=  $_SESSION["laUnidaddelUsuario"];

//Si $cualAno viene vacio es porque no han cambiado las listas en la hoja de tiempo, 
//por lo tanto el mes activo es el actual
if (trim($cualAno) == "") {
	$anoAut=date("Y");
	$mesAut=date("m");
}
else {
	$anoAut=$cualAno;
	$mesAut=$elMes;
}

//Establecer la conexión a la base de datos
include "funciones.php";
include "validaUsrBd.php";

//Verificar si el usuario ya existe para mostrar el jefe ya seleccionado
$sql="Select * from AutorizacionesHT ";
$sql=$sql." where vigencia = " . $anoAut;
$sql=$sql." and mes = " . $mesAut ;
$sql=$sql." and unidad = " . $cualUnidad;
$cursor = mssql_query($sql);
if ($reg=mssql_fetch_array($cursor)) {
	$pvalidaJefe = $reg[validaJefe] ;
	$pcomentaJefe = $reg[comentaJefe] ;
	$punidadJef = $reg[unidadJefe] ;
}



//Si se presionó el botón Grabar
if ($elAno != "") {
	//Verifica si el registro ya existe en la tabla AutorizacionsHT para 
	//Determinar si se inserta o se modifica.
	
	
if($pAprueba == 1)	
	{
// se realiza una validacion para ver que accion se va a ejecutar en este caso la aprobacion de la hoja de tiempo 	
	
	$query = "UPDATE  AutorizacionesHT SET "; 
	$query = $query . " validaJefe = '" . $pAprueba . "',  ";
	$query = $query . " comentaJefe = '" . $pComenta . "',  ";
	$query = $query . " fechaAprueba = '" . gmdate ("n/d/y") . "'  ";
	$query = $query . " WHERE vigencia = " . $elAno ;
	$query = $query . " AND mes = " . $elMes ;
	$query = $query . " AND unidad = " . $laUnidadUsu ;
	#
	$cursor = mssql_query($query) ;	

	
}

//primera validacion de regsitros vacios en los viaticos
$sqlp=" select COUNT(unidad)as numero
  from [HojaDeTiempo].[dbo].[AprobacionViaticosHT]
  where unidad=".$cualUnidad."
  and   vigencia=".$anoAut."
  and  mes =".$mesAut;
  $cursorp = mssql_query($sqlp) ;
  while ($regp=mssql_fetch_array($cursorp)) {
  
 
  $validarr=$regp[numero];
 
 
  }
if ($validarr =! 0)
{
$ii=0;
$rr=$recarga+1;
$vector=unserialize($vector); 


//se recorre el for buscando los viaticos


for ($n=1;$n<$rr;$n++)
{

$P=${"aVP".$n};

$vector1[$ii];


//valida si el viatico es aprobado o no 
if( $P == 1)
{
$Proyect=$vector[$ii];
$observa=${"observa".$n};

$query2 = "UPDATE  AprobacionViaticosHT SET "; 
			$query2 = $query2 . " validaEncargado = '1',  ";
			$query2 = $query2 . " comentaEncargado = '" . $observa. "',  ";
			$query2 = $query2 . " unidadEncargado = " . $usuarioPrinc .  ", ";
			$query2 = $query2 . " fechaAprueba = '" . gmdate ("m/d/Y") . "'  ";
			$query2 = $query2 . " WHERE id_proyecto =" . $Proyect . " ";
			$query2 = $query2 . " AND vigencia = " .$elAno ;
			$query2 = $query2 . " AND mes = " . $elMes ;
			$query2 = $query2 . " AND unidad = " . $cualUnidad ;		
		
		
		#
		$cursor2 = mssql_query($query2) ;*/
}






$ii=$ii+1;
}
}


	//Si los cursores no presentaron problema
	if  (trim($cursor) != "") {
		echo ("<script>alert('La Grabación se realizó con éxito.');</script>");
	} 
	else {
		echo ("<script>alert('Error durante la grabación');</script>");
	};
		echo ("<script>window.close();MM_openBrWindow('ApruebaHT.php?pMes=$elMes&pAno=$elAno','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=*,height=*');</script>");
	echo ("<script>window.close()</script>");
	
}


?>
<html>
<head>
<title>Autorizaci&oacute;n Hoja de tiempo</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="#EAEAEA">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Hoja de tiempo -  Aprobaci&oacute;n</td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	<table width="100%"  border="0" cellspacing="1" cellpadding="0">
<form action="" method="post" name="Form1" id="Form1">
  <tr>
    <td width="25%" class="TituloTabla">A&ntilde;o</td>
    <td class="TxtTabla">
	<? echo $anoAut ; ?>
	<input name="elAno" type="hidden" id="elAno" value="<? echo $anoAut ; ?>">
	</td>
  </tr>
  <tr>
    <td width="25%" class="TituloTabla">Mes</td>
    <td class="TxtTabla">
	<? echo $mesAut ; ?>
	<input name="elMes" type="hidden" id="elMes" value="<? echo $mesAut ; ?>">
	</td>
  </tr>
  <tr>
    <td class="TituloTabla">Unidad</td>
    <td class="TxtTabla"><? echo $cualUnidad; ?></td>
  </tr>
  <tr>
    <td width="25%" class="TituloTabla">Usuario</td>
    <td class="TxtTabla">
	<?
		$miUsuario = "";
		//Consulta para traer el nombre del jefe que autoriza
//		@mssql_select_db("HojaDeTiempo",$conexion);
		$sql0 = "select * from usuarios where unidad =" . $cualUnidad ;
		$cursor0 = mssql_query($sql0);
		if ($reg0=mssql_fetch_array($cursor0)) {
			$miUsuario = $reg0[nombre] . " " . $reg0[apellidos];
		}
		?>
		<? echo strtoupper($miUsuario); ?>
		<input name="laUnidadUsu" type="hidden" id="laUnidadUsu" value="<? echo $cualUnidad; ?>"></td>
  </tr>
  <tr>
    <td class="TituloTabla">Hoja de tiempo aprobada? </td>
    <td class="TxtTabla">
	<?
	//Si ya esta o no aprobada la hoja de tiempo
	if ($pvalidaJefe == "1") {
		$selSI = "checked";
		$selNo = "";
	}
	if ($pvalidaJefe == "0") {
		$selSI = "";
		$selNo = "checked";
	}
	?>
	<input name="pAprueba" type="radio" class="CajaTexto" value="1" <? echo $selSI; ?> >
      Si&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;      <input name="pAprueba" type="radio" class="CajaTexto" value="0" <? echo $selNo; ?>>
      No</td>
  </tr>
  <tr>
    <td class="TituloTabla">Comentarios.</td>
    <td class="TxtTabla"><textarea name="pComenta" cols="50" rows="4" class="CajaTexto" id="pComenta"><? echo $pcomentaJefe; ?></textarea></td>
  </tr>
  <tr>
    <td colspan="2" class="TxtTabla"><table width="100%"  border="0">
      <tr>
        <td class="TituloUsuario"> Aprovaci&oacute;n de Vi&aacute;ticos</td>
      </tr>
    </table>
      <?
//2 de julio 2013
//consulta que me trae los proyectos en los cuales el usuario es director o cordinador o ordenador de gasto o coordinador

$sql=" select  a.*,b.nombre as proyecto, c.nombre as actividad,d.NomSitio
   from ViaticosProyecto a, Proyectos b, Actividades c,SitiosTrabajo d

   	 	where unidad = ".$cualUnidad."
		and a.id_actividad=c.id_actividad
   	 	and a.id_proyecto=b.id_proyecto
   	 	and a.id_proyecto=c.id_proyecto
   	 	and a.localizacion=d.IDsitio
   	 	and a.id_proyecto=d.id_proyecto
   	 	and MONTH(fechaIni) =".$elMes."
   	 	and YEAR(fechaIni) = ".$cualAno."
   	 	and a.id_Proyecto in (
			SELECT A.id_proyecto
			FROM 
				( Select id_proyecto from HojaDeTiempo.dbo.Proyectos 
				where id_director = ". $usuarioPrinc."  or id_coordinador = ". $usuarioPrinc."  and id_estado = 2 
				UNION
				 Select id_proyecto from HojaDeTiempo.dbo.Programadores where unidad = ". $usuarioPrinc." 
				 UNION 
				 select id_proyecto from GestiondeInformacionDigital.dbo.OrdenadorGasto 
				 where unidadOrdenador = ". $usuarioPrinc."  and id_proyecto is not null 
				 UNION 
				 select id_proyecto from HojaDeTiempo.dbo.actividades where id_encargado =". $usuarioPrinc."   
				 UNION 
				 select id_proyecto from HojaDeTiempo.dbo.ResponsablesActividad where unidad = ". $usuarioPrinc."   )  A,
				  Proyectos P, Usuarios D, Usuarios C 
			  WHERE   A.id_proyecto = P.id_proyecto
			   AND P.id_director *= D.unidad 
			   AND   P.id_coordinador *= C.unidad 
			   AND P.id_estado = 2 	)
		 
		 ";	
		 
	
		 $cursor = mssql_query($sql);  
	  
	  
	  // Aqui van las validaciones
	  ?>
	  <table width="100%"  border="0">
        <tr class="TituloTabla2">
          <td rowspan="2">Proyectos</td>
          <td  rowspan="2">Viatico Actividad</td>
          <td  rowspan="2">Localizaci&oacute;n</td>
          <td  rowspan="2">Cargo</td>
          <td  rowspan="2">Fecha inicial</td>
          <td  rowspan="2">Fecha final</td>
          <td  rowspan="2">Viatico completo </td>
		  <td colspan="2">Aprobaci&oacute;n</td>
		  <td  rowspan="2">Descripci&oacute;n </td>
		  </tr>
		  <tr class="TituloTabla2">
          <td> si</td>
          <td>no</td>
        </tr>
		<?
		 $i=1;
		 $s=0;
		 while ($reg=mssql_fetch_array($cursor)) {?>
		
		<? 

		?>
		
		
        <tr class="TxtTabla">
		<td> <? echo $reg[proyecto] ; ?></td>
          <td><? echo  $reg[actividad] ; ?></td>
          <td><? echo  $reg[NomSitio]; ?></td>
		  <td> <? echo  $reg[descCargoDefecto]; ?></td>
          <td><? echo date("M d Y ", strtotime($reg[FechaIni]));?></td>
          <td><? echo date("M d Y ", strtotime($reg[FechaFin])); ?></td>
          <td><? if($reg[viaticoCompleto]==1) {echo "Si";}else{echo "No";} ?></td>
		  <td width="5%" align="center">
        <?	
		$laAprobacion = 0; 
		$comentaAprueba = "" ;

		$sqlA="SELECT * ";
		$sqlA=$sqlA." FROM HojaDeTiempo.dbo.AprobacionViaticosHT ";
		$sqlA=$sqlA." WHERE unidad = " .$cualUnidad  ;
		$sqlA=$sqlA." and id_proyecto =" . $reg[id_proyecto] ;
		//filtra el resultado de la consulta si la página se carga por primera vez con el mes y año actual
		//sino con lo seleccionado en las listas mes y año
		if ($elMes == "") {
			$sqlA=$sqlA." and mes = month(getdate()) " ;
			$sqlA=$sqlA." and vigencia = year(getdate()) " ;
		}
		else {
			$sqlA=$sqlA." and mes = " . $elMes;
			$sqlA=$sqlA." and vigencia = " . $cualAno;
		}
		
		$cursorA = mssql_query($sqlA);
		$CantRegistros = mssql_num_rows($cursorA);
		if ($regA=mssql_fetch_array($cursorA)) {
			$laAprobacion = $regA[validaEncargado] ; 
			$comentaAprueba = $regA[comentaEncargado] ;
		}

		?>
		<? 
		if($aprobado==''){
		if ($laAprobacion == "1") {
			$selSi = "checked";
			$selNo = "";
		}
		if (($laAprobacion == "0") OR (trim($laAprobacion) == ""))  {
			$selSi = "";
			$selNo = "checked";
		}}
		else
		{
		if($aprobado=="1")
		{ $selSi = "checked";}}
		
		
		?>
	
		<input name="aVP<? echo $i; ?>" type="radio" class="CajaTexto" value="1" <? echo $selSi; ?>  ></td>
        <td width="5%" align="center">
		<input name="aVP<? echo $i; ?>" type="radio" class="CajaTexto" value="0" <? echo $selNo; ?> >
		<input name="pOperacion<? echo $i ; ?>" type="hidden" id="pOperacion<? echo $i ; ?>"  value="<? echo $CantRegistros; ?>" />
		</td>
	<td width="27%" class="TxtTabla"><textarea name="observa<? echo $i;?>"  cols="70" rows="4"  class="CajaTexto" id="observa<? echo $i;?>"></textarea> </td>
         
        </tr>
     
	  <? 
	
	 $Pro[$s]=$reg[id_proyecto];
	  	   $i=$i+1;
		    $s=$s+1;
	  }// cierre del primero while
	  
	  //Cierra las validaciones
	  ?>
	  

	  <input name="recarga" type="hidden" id="recarga" value="<? echo $s;  ?>"> 
	 <? 
	 // se convierte un arreglo para poderlo enviar 
	 $serializado=serialize($Pro);?>
	  <input name="vector" type="hidden" id="vector" value="<? echo $serializado; ?>"> 
	 
	   </table></td>
  </tr>
  <tr>
    <td colspan="2" align="right" class="TxtTabla"><input name="Submit" type="submit" class="Boton" value="Grabar"></td>
    </tr>
  </form>
</table>

	</td>
  </tr>
</table>

</body>
</html>
