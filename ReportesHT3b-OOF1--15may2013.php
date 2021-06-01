<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";
include('../conectaBDht.php');

//Trae la información de la divisiones que tiene a cargo
//16Jul2007

//validacion de usuarios delegados Omar Osuna 

 
$sql1="select unidadQueDelega,estado from DelegadosDivisionRpts 
	       where unidadDelegada = " . $laUnidad; 
$cursore = mssql_query($sql1);
if ($reg=mssql_fetch_array($cursore)) {
	$estado = $reg[estado];
	$unidad = $reg[unidadQueDelega];
}

if($estado=='A')
{
	$laUnidad=$unidad;
	}
 $laUnidad;

$sql="Select D.*, U.nombre nomDir, U.apellidos apeDir ";
$sql=$sql." from divisiones D, Usuarios U " ;
$sql=$sql." where D.id_director *= U.unidad " ;
//$sql=$sql." and D.id_director = " . $laUnidad; 
//14Ago2012
//PBM
//La anterior linea se cambió para que los subdirectores de división tambien tengan acceso a este reporte.
$sql=$sql." and (D.id_director = " . $laUnidad; 
$sql=$sql." or D.id_subdirector = " . $laUnidad . ") "; 

$cursor = mssql_query($sql);
if ($reg=mssql_fetch_array($cursor)) {
	$elIDDivision = $reg[id_division];
	$elNomDivision = $reg[nombre];
	$elNomDirector = $reg[nomDir] . " " . $reg[apeDir];
}


//16Jul2007
//ParaMostrar los botones del Reporte del director de proyecto y de división
$muestraDirProyecto = 0;
$sqlB="select count(*) esDirector from proyectos  ";
$sqlB=$sqlB." where (id_director = ". $laUnidad . " or id_coordinador = " . $laUnidad . " ) "; 
$cursorB = mssql_query($sqlB);
if ($regB=mssql_fetch_array($cursorB)) {
	$muestraDirProyecto = $regB[esDirector];
}



$sql="Select D.*, U.nombre nomDir, U.apellidos apeDir ";
$sql=$sql." from divisiones D, Usuarios U " ;
$sql=$sql." where D.id_director *= U.unidad " ;
//$sql=$sql." and D.id_director = " . $laUnidad; 
//14Ago2012
//PBM
//La anterior lknea se cambió para que los subdirectores de división tambien tengan acceso a este reporte.
$sql=$sql." and (D.id_director = " . $laUnidad; 
$sql=$sql." or D.id_subdirector = " . $laUnidad . ") "; 

$cursor = mssql_query($sql);
if ($reg=mssql_fetch_array($cursor)) {
	$elIDDivision = $reg[id_division];
	$elNomDivision = $reg[nombre];
	$elNomDirector = $reg[nomDir] . " " . $reg[apeDir];
}


//09Abr2012
//Facturación del personal de la división seleccionada con toda la información

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
<!--

window.name="winHojaTiempo";

function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}

function valida()
{
	var e="n",msg="";

    if(document.form1.pMes.value=="0")
	{
		e="s";

		msg=msg+"Seleccione un mes.\n ";
	}
	
	if(document.form1.pAno.value=="")
	{
		e="s";
		msg=msg+"Seleccione un ano.\n ";
	}
	
if(e=="s")
	{
		alert(msg);
	}	
else
	{
		document.form1.submit();
	}	
}

//-->
</script>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Reportes de Hoja de tiempo</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota1" style="position:absolute; left:200px; top:8px; width: 529px; height: 25px;">
		<div align="center"> 
		  Reportes Hoja de Tiempo <br> Director de división
		</div>
</div>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table><table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>

<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Consulta de otros periodos </td>
  </tr>
</table>
<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td bgcolor="#FFFFFF">
	<table width="100%"  border="0" align="center" cellpadding="0" cellspacing="1">
	<form name="form1">
  <tr>
    <td width="7%" align="right" class="TituloTabla">Mes:&nbsp;</td>
    <td width="14%" class="TxtTabla">
	<? 
	

 							 if($pAno==""){
							  $pAno=date("Y");}
							  if($pMes==""){
							    $pMes=date("n");}
							  
$sql00=" select distinct
a.[unidad]
,b.nombre as nombreu
,b.apellidos
,e.id_proyecto
,e.codigo
,e.nombre as nombrep
,e.cargo_defecto
,a.cargo
,b.id_categoria
,c.nombre
,a.clase_tiempo
 FROM [Horas] a, Usuarios b,Departamentos c,UsuariosSalario d,Proyectos e  where (";


if($id_proyectos=="")
    {
	$a=1;
	$sqlproesp=" select * from Proyectos where especial=1";
	$queryproesp = mssql_query($sqlproesp);
	while ($curproesp = mssql_fetch_array($queryproesp)){
		if($a==1){
			$sql0 = " e.id_proyecto=".$curproesp[id_proyecto];
		}
		else{
			$sql0 = $sql0." OR e.id_proyecto=".$curproesp[id_proyecto];
		}		
		$a++;
	}
	$sql00=$sql00.$sql0;
	}
	
	
	
else
	{
	$sql00=$sql00." (e.id_proyecto='".$id_proyectos."')"; 
	}
 
 
  
 
  $sql00=$sql00.") and a.unidad=b.unidad and"; 
if($pUnidad<>"")
{
	$sql00=$sql00." a.unidad=".$pUnidad." and";
	}  
  $sql00=$sql00." a.id_proyecto=e.id_proyecto 
  and  b.id_departamento=c.id_departamento
  and b.unidad=d.unidad  
  and c.id_division='".$elIDDivision."'
  and month(a.fecha) = ".$pMes."
  and year(a.fecha) = ".$pAno."
  and year(d.fecha) = ".$pAno."
  
order by a.unidad
";
 //  echo $sql00;
	//Seleccionar el mes cuando se carga la pigina por primera vez
	//si no cuando se recarga la pigina
	$selMesTodos= "";
	if ($pMes == "") {
		$mesActual=date("m"); //el mes actual
	}
	else {
		if ($pMes == "TODOS") {
			$selMesTodos= "selected"; //el mes seleccionado
		}
		else {
			$mesActual= $pMes; //el mes seleccionado
		}
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
	&nbsp;      <select name="pMes" class="CajaTexto" id="pMes">
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
	  <option value="TODOS" <? echo $selMesTodos; ?>>::: Todos :::</option>
    </select></td>
    <td width="10%" align="right" class="TituloTabla">A&ntilde;o
    </td>
    <td width="60%" class="TxtTabla">
	&nbsp;
	<select name="pAno" class="CajaTexto" id="pAno">
	<? 
	//Generar los anos de 2006 a 2050
	for($i=2006; $i<=2050; $i++) { 
		
		//seleccionar el ano cuando se carga la pigina por primera vez
		if ($pAno == "") {
			$AnoActual=date("Y"); //el ano actual
		}
		else {
			$AnoActual= $pAno; //el ano seleccionado
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

    </select>
    	</td>
   <td class="TxtTabla">&nbsp; </td>
    
  </tr>

  <tr>
  
    <td align="right" class="TituloTabla">Unidad</td>
    <td colspan="3" class="TxtTabla">&nbsp;      <input name="pUnidad" type="text" class="CajaTexto" id="pUnidad" onKeyPress="if (event.keyCode < 48 || event.keyCode > 57) event.returnValue = false;" value="<? echo $pUnidad; ?>" /> 
    </td>
    <td width="0%" class="TxtTabla"></td>
    </tr>
    <tr>
    <td class="TituloTabla">
               Proyecto </td>
    <td colspan="3" class="TxtTabla">&nbsp;
    <? $sqlproyecto="select * from Proyectos where especial=1"; 
	   $lstCursorProy = mssql_query($sqlproyecto);?>
       <select name="id_proyectos" class="CajaTexto" id="id_proyectos">
	  		<option value="" selected>Seleccione un proyecto</option>
	  <? 
	  while ($lstRegProy=mssql_fetch_array($lstCursorProy)) { 
	  if ($lstRegProy[id_proyecto] == $id_proyectos) {
	  		$selProyecto = "selected";
	  }
	  else {
			$selProyecto = "";
	  }
	  
	  
	  ?>
        <option value="<? echo $lstRegProy[id_proyecto]; ?>" <? echo $selProyecto; ?> ><? echo $lstRegProy[nombre];echo"[";   echo$lstRegProy[codigo];    echo"]"; ?></option>
	  <? }?>
        
      </select></td>
    
   
    <td width="9%" class="TxtTabla"><input  type="button" class="Boton" value="Consultar" onClick="valida()"></td>
  </tr>
	</form>
</table>
	</td>
  </tr>
</table>



<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="TituloUsuario">Informaci&oacute;n de la Divisi&oacute;n</td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF"><table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr class="TituloTabla2">
        <td>Divisi&oacute;n</td>
        <td width="40%">Director</td>
        </tr>
      <tr class="TxtTabla">
        <td><? echo ucwords(strtolower($elNomDivision)) ; ?></td>
        <td width="40%"><? echo ucwords(strtolower($elNomDirector)) ; ?></td>
        </tr>
    </table>
	  </td>
      </tr>
    </table>
	
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr>
  
        <td>&nbsp;</td>
      </tr
      ><tr>
        <td height="1" colspan="5" class="TituloUsuario"> </td>
      </tr>
</table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Facturaci&oacute;n de la divisi&oacute;n - Horas y valor facturado por usuario y por proyecto </td>
  </tr>
</table>


    
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="right" class="TituloTabla">Valor Facturado = (Salario / 185) * Horas Facturadas </td>
      </tr>
    </table>
    <table width="100%"  border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
      <tr class="TituloTabla2">
        <td width="8%">Unidad</td>
        <td>Nombre</td>
        <td width="3%">Categor&iacute;a</td>
        <td width="10%">Departamento</td>
        <td width="25%">Proyecto</td>
        <td width="5%">Clase de tiempo </td>
        <td width="10%">Horas facturadas </td>
        <td width="10%">Salario base</td>
        <td width="10%">Valor Facturado</td>
      </tr>
	  <? 
	  $cursor00 = mssql_query($sql00);
	  $vlrTotalFacturado = 0;
	  while ($reg00=mssql_fetch_array($cursor00)) { 
	  $elNomUsuario = "";
	  $elhFacturadas = "";
	  $elsalarioBase = "";
	  $elvlrFacturado = "";
	  ?>
      <tr class="TxtTabla">
        <td width="8%">
		<? echo $reg00[unidad]; ?>
		
		</td>
        <td><? echo ucwords(strtolower($reg00[apellidos] . " " . $reg00[nombreu] )); ?>
		<? 
		$elNomUsuario= ucwords(strtolower($reg00[apellidos] . " " . $reg00[nombreu] )); 
		
		?>
		</td>
        <td width="3%"><? echo $reg00[id_categoria]; ?>
		
		</td>
        <td width="10%"><? echo $reg00[nombre]; ?>
		
		</td>
        <td width="25%"><? echo "[" . $reg00[codigo] . "."  . $reg00[cargo_defecto] . "] " . $reg00[nombrep] . " (" . $reg00[cargo] . ") ";  ?>

		</td>
        <td width="5%" align="right"><? echo $reg00[clase_tiempo]; ?>
	
		</td>
        <td width="10%" align="right"><? 
		$sumSql = "select sum(H.horas_registradas) horasFact from HojaDeTiempo.dbo.horas H, HojaDeTiempo.dbo.Proyectos P 
		where H.id_proyecto = P.id_proyecto";
		
		$sumSql=$sumSql." and p.id_proyecto=".$reg00[id_proyecto];
		$sumSql=$sumSql." and H.unidad = ".$reg00[unidad]." and month(H.fecha) = ".$pMes." and year(H.fecha) = ".$pAno;
	//echo $sumSql;
				$querySum = mssql_fetch_array( mssql_query( $sumSql ) );
				echo $querySum[horasFact]; ?>
		<? 
		
		?>
		</td>
        <td width="10%" align="right">$ <?
		$salSql = "select  MAX(salario)as salario from  UsuariosSalario where
year(fecha) <= ".$pAno."  and unidad=".$reg00[unidad]." and  month(fecha) <= ".$pMes;
		

	//echo $sumSql;
				$querySal = mssql_fetch_array( mssql_query( $salSql ) );
				 $querySal[salario]; 
		
		 echo number_format($querySal[salario], 2, ",", ".") ; ?>
		<? 
		$elsalarioBase = number_format($querySal[salario], 2, ",", ".");
		 ?>
		</td>
        <td width="10%" align="right">
		$ <? 
		$vlrFacturado = 0;
		if (trim($querySal[salario]) != "") {
			$vlrFacturado = (($querySal[salario] / 185) *  $querySum[horasFact]) ;
			$vlrTotalFacturado = $vlrTotalFacturado + $vlrFacturado; 
			echo number_format($vlrFacturado, 2, ",", ".");
		} 
		else {
			echo "0";
		}
		?>
		<? 
		$elvlrFacturado = number_format($vlrFacturado, 2, ",", ".");
		$excel.="$elvlrFacturado\n"; 
		?>		
		</td>
      </tr>
	  <? } ?>
      <tr class="TituloTabla2">
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td width="3%">&nbsp;</td>
        <td>&nbsp;</td>
        <td width="25%">&nbsp;</td>
        <td align="right">&nbsp;</td>
        <td align="right">&nbsp;</td>
        <td align="right">TOTAL FACTURADO </td>
        <td align="right">$ <? echo number_format($vlrTotalFacturado, 2, ",", "."); ?></td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
	<form action="rptXlsFactura01.php" method="post">
     
	  </form>
    </table>
	
		
	
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="right" class="TxtTabla">&nbsp;</td>
          </tr>
        </table>		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
        
        </table></td>
      </tr>
    </table>
</body>
</html>
