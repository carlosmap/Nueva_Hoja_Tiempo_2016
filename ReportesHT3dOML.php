<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

//Trae la información de la divisiones que tiene a cargo
//16Jul2007
$sql="Select D.*, U.nombre nomDir, U.apellidos apeDir ";
$sql=$sql." from divisiones D, Usuarios U " ;
$sql=$sql." where D.id_director *= U.unidad " ;
$sql=$sql." and D.id_director = " . $laUnidad; 
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


//17Abr2012
//--PROYECTOS en los que ha participado la división AMBIENTAL Valor facturado por proyecto
$sql00="SELECT DISTINCT id_proyecto, nomProyecto, SUM(vlrFacturado) vlrTotalFacturado, codigo, cargo_defecto ";
$sql00=$sql00." FROM ( ";
$sql00=$sql00." 	Select H.unidad, U.nombre, U.apellidos, D.nombre nomDpto, D.id_departamento, Z.nombre nomDiv, Z.id_division, ";
$sql00=$sql00." 	H.id_proyecto, P.nombre nomProyecto, H.clase_tiempo, sum(H.horas_registradas) hFacturadas, A.salarioBase,  ";
$sql00=$sql00." 	((A.salarioBase/185)*sum(H.horas_registradas)) vlrFacturado, P.codigo, P.cargo_defecto ";
$sql00=$sql00." 	from Horas H, Proyectos P, Asignaciones A, Usuarios U, Departamentos D, Divisiones Z ";
$sql00=$sql00." 	where H.id_proyecto = P.id_proyecto  ";
$sql00=$sql00." 	and H.id_proyecto = A.id_proyecto ";
$sql00=$sql00." 	and H.id_actividad = A.id_actividad ";
$sql00=$sql00." 	and H.unidad = A.unidad ";
$sql00=$sql00." 	and H.clase_tiempo = A.clase_tiempo ";
$sql00=$sql00." 	and H.localizacion = A.localizacion ";
$sql00=$sql00." 	and H.cargo = (P.codigo + A.cargo) ";
$sql00=$sql00." 	and MONTH(H.fecha)= MONTH(A.fecha_inicial) ";
$sql00=$sql00." 	and YEAR(H.fecha)= YEAR(A.fecha_inicial) ";
$sql00=$sql00." 	and H.unidad = U.unidad ";
$sql00=$sql00." 	and U.id_departamento = D.id_departamento ";
$sql00=$sql00." 	and D.id_division = Z.id_division ";
$sql00=$sql00." /*	and D.id_division = " . $elIDDivision ." */ ";

if( trim($mes1) !=  "" )
	$sql00=$sql00." and MONTH(H.fecha) >= " . $mes1;
if( trim($anio1) !=  "" )
	$sql00=$sql00." and YEAR(H.fecha) >= " . $anio1;
	
if( trim($mes2) !=  "" )
	$sql00=$sql00." and MONTH(H.fecha) <= " . $mes2;
if( trim($anio2) !=  "" )
	$sql00=$sql00." and YEAR(H.fecha) <= " . $anio2;


/*
if ($pMes == "") {
	$sql00=$sql00." and MONTH(H.fecha) = month(getdate()) " ;
	$sql00=$sql00." and YEAR(H.fecha)= year(getdate())";
}
else {
	if ($pMes == "TODOS") {
		$sql00=$sql00." and YEAR(H.fecha)= " . $pAno;
	}
	else {
		$sql00=$sql00." and MONTH(H.fecha) =  " . $pMes;
		$sql00=$sql00." and YEAR(H.fecha)= ". $pAno;
	}
}
*/
/*
if (trim($pUnidad) != "") {
	$sql00=$sql00." and H.unidad = " . $pUnidad;
}
*/
#	MODIFICADO OSCAR MAURICIO LOPEZ SEGURA
#	13-08-2012

#	FILTRO POR DIVISIONES
if ( trim($cDivisiones) != "" ) {
	$sql00=$sql00." and Z.id_division = " . $cDivisiones;
}

#	FILTRO POR DEPARTAMENTOS
if ( trim($cDepartamento) != "" ) {
	$sql00=$sql00." and D.id_departamento = " . $cDepartamento;
}
if (trim($plstProyecto) != "") {
	$sql00=$sql00." and H.id_proyecto = " . $plstProyecto;
}
$sql00=$sql00." 	group by H.unidad, U.nombre, U.apellidos, D.nombre, D.id_departamento, Z.nombre, Z.id_division, H.id_proyecto, P.nombre, ";
$sql00=$sql00." 	H.clase_tiempo, A.salarioBase, P.codigo, P.cargo_defecto ";
$sql00=$sql00." ) A ";
$sql00=$sql00." group by id_proyecto, nomProyecto, codigo, cargo_defecto ";
$sql00=$sql00." order by nomProyecto ";
$cursor00 = mssql_query($sql00);

//--PROYECTOS en los que ha participado la división AMBIENTAL
$lstSqlProy="Select H.id_proyecto, P.nombre nomProyecto ";
$lstSqlProy=$lstSqlProy . " from Horas H, Proyectos P, Usuarios U, Departamentos D, Divisiones Z ";
$lstSqlProy=$lstSqlProy . " where H.id_proyecto = P.id_proyecto ";
$lstSqlProy=$lstSqlProy . " and H.unidad = U.unidad ";
$lstSqlProy=$lstSqlProy . " and U.id_departamento = D.id_departamento ";
$lstSqlProy=$lstSqlProy . " and D.id_division = Z.id_division ";
$lstSqlProy=$lstSqlProy . " and D.id_division = " . $elIDDivision ;
if ($pMes == "") {
	$lstSqlProy=$lstSqlProy . " and MONTH(H.fecha)= month(getdate()) " ;
	$lstSqlProy=$lstSqlProy . " and YEAR(H.fecha)= year(getdate()) " ;
}
else {
	if ($pMes == "TODOS") {
		$lstSqlProy=$lstSqlProy . " and YEAR(H.fecha)= ". $pAno;
	}
	else {
		$lstSqlProy=$lstSqlProy . " and MONTH(H.fecha)=  " . $pMes;
		$lstSqlProy=$lstSqlProy . " and YEAR(H.fecha)= ". $pAno;
	}
}
$lstSqlProy=$lstSqlProy . " group by H.id_proyecto, P.nombre ";
$lstSqlProy=$lstSqlProy . " ORDER BY P.nombre ";
$lstCursorProy = mssql_query($lstSqlProy);

//Generar el archivo xls
$excel="";
$excel.="Proyecto\t";
$excel.="Valor facturado\n";

#	FILTROS OSCAR LOPEZ
#	13-08-2012



 

#	LISTAR DEPENDENCIAS ACTIVAS
#	select *from HojaDeTiempo.dbo.Departamentos dep where estadoDpto = 'A' and id_division = 9 order by id_division

#	Proyectos
#	select distinct A.id_proyecto, B.nombre nomProyecto from horas A, Proyectos B, Usuarios C, Departamentos D, Divisiones E 
#	where A.id_proyecto = B.id_proyecto and A.unidad = C.unidad and C.id_departamento = D.id_departamento and 
#	D.id_division = E.id_division and D.id_division =9 and MONTH(A.fecha)=6 and YEAR(A.fecha)=2012 order by B.nombre

#	Divisiones
#	select distinct D.id_division, E.nombre nomDivision from horas A, Proyectos B, Usuarios C, Departamentos D, Divisiones E 
#	where A.id_proyecto = B.id_proyecto and A.unidad = C.unidad and C.id_departamento = D.id_departamento and D.id_division = E.id_division and 
#	MONTH(A.fecha)=6 and YEAR(A.fecha)=2012 and A.id_proyecto = 19
#	--and D.id_division =9
#	ORDER BY E.nombre

 

 

#	Deparmanetos
#	select distinct C.id_departamento, D.nombre nomDepartamento from horas A, Proyectos B, Usuarios C, Departamentos D, Divisiones E
#	where A.id_proyecto = B.id_proyecto
#	and A.unidad = C.unidad
#	and C.id_departamento = D.id_departamento
#	and D.id_division = E.id_division
#	and MONTH(A.fecha)=6
#	and YEAR(A.fecha)=2012
#	and A.id_proyecto = 19
#	and D.id_division =1
#	--and C.id_departamento = XX
#	ORDER BY D.nombre

###



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
//	AGREGAR TABLA DINAMICA
function tblDinamic(){
			//
			var a1 = document.getElementById('anio1').selectedIndex;
			var a2 = document.getElementById('anio2').selectedIndex;
			
			var m1 = document.getElementById('mes1').selectedIndex;
			var m2 = document.getElementById('mes2').selectedIndex;
			var anio;
			var mes, ma1, ma2, mesf;			
			var tcol;
			
			if( ( ( a1 > a2 ) && ( m1 > m2 ) ) || ( a1 == a2 && m1 > m2 ) ) {
				//&& ( ( m1 > m2 ) || ( m1 < m2 ) )  ){
				//if( m1 > m2 ){
					alert ( 'No puede validar esas fechas' );
				//}
			}
			else{				
				anio = ( a2 - a1 ) - 1 ;
				ma1 = 12 - m1;
				mes = ma1 + m2;
				if( mes > 12 ){
					mesf = mes - 12;
					anio = anio + 1;
					tcol = (anio * 12 ) + mesf;
				}
				else{
					tcol = (anio * 12 ) + mes;
				}
				var tbl = document.getElementById('tblDm');
				var tblDs = '';
				var rc;
				rc = tcol + 1;
	//			alert ( tcol);
				tblDs = "<table width='100%'  border='0' cellspacing='1' cellpadding='0'>"+
						"<tr class='TituloTabla2'><td width='1%' colspan='"+rc+"'>Facturación mes a mes por proyecto</td></tr>"+
						"<tr class='TituloTabla2'><td>Proyecto</td>";

				var nMes = [ 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre' ];
				
				for( var i = 0; i < tcol; i++ ){
					if( i == 0 )
						tblDs += "<td> "+ nMes[a1] +" </td>";
					else
						tblDs += "<td> "+ nMes[i] +" </td>";
				}
				tblDs += "</tr></table><br />";
				tbl.innerHTML = "";
				tbl.innerHTML += tblDs;
			}
			return true;
		}
//	AGREGAR TABLA DINAMICA
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
	<form name="form1" method="post" action="">
  <tr>
    <td rowspan="2" align="right" class="TituloTabla">Fechas:</td>
    <td width="7%" class="TxtTabla">
      Inicio:      </td>
    <td width="68%" class="TxtTabla"><select name="mes1" id="mes1" class="CajaTexto">
      <?php for ( $m2 = 1; $m2 <= 12; $m2++) {?>
      <option value="<?php echo $m2;	?>"><?php echo $m2;	?></option>
      <?php }?>
    </select>
Mes
      -
<select name="anio1" id="anio1" onchange="document.form1.submit();tblDinamic();" class="CajaTexto">
  <?php 
  	for ( $a2 = 2007; $a2 <= date( 'Y' ); $a2++) {
		if( trim($anio1) == $a2 ){			
  ?>
  			<option value="<?php echo $a2;	?>" selected="selected"><?php echo $a2;	?></option>
  <?php	}	
  		else{
  ?>
        	<option value="<?php echo $a2;	?>"><?php echo $a2;	?></option>
  <?php 
		}
	}  
  ?>
</select>
A&ntilde;o</td>
    <td class="TxtTabla">&nbsp;</td>
  </tr>
  <tr>
    <td class="TxtTabla">Fin:      </td>
    <td class="TxtTabla"><select name="mes2" id="mes2" class="CajaTexto">
      <?php 
				for ( $m2 = 1; $m2 <= 12; $m2++) {
					if ( date( 'm') == $m2 ){
			?>
      <option value="<?php echo $m2;	?>" selected="selected"><?php echo $m2;	?></option>
      <?php	}	?>
      <option value="<?php echo $m2;	?>"><?php echo $m2;	?></option>
      <?php }?>
    </select>
Mes -
<select name="anio2" id="anio2" onchange="document.form1.submit();tblDinamic();" class="CajaTexto">
  <?php 
				for ( $a2 = 2007; $a2 <= date( 'Y' ); $a2++) {
						if ( date( 'Y') == $a2 ){
			?>
  <option value="<?php echo $a2;	?>" selected="selected"><?php echo $a2;	?></option>
  <?php	
						}	
						else{
					?>
  <option value="<?php echo $a2;	?>"><?php echo $a2;	?></option>
  <?php }	}	?>
</select>
A&ntilde;o</td>
    <td class="TxtTabla">&nbsp;</td>
  </tr>
    
  <tr>
    <td align="right"  class="TituloTabla">Divisiones</td>
    <td colspan="2" class="TxtTabla">
    <select id="cDivisiones" name="cDivisiones"  class="CajaTexto" onchange="document.form1.submit();">
    	<option value="">::: Todo :::</option>
    	<?php
			#	FILTROS OSCAR LOPEZ
			#	13-08-2012
			#	LISTAR DIVISIONES ACTIVAS
			
			$sqlDiv = "select *from HojaDeTiempo.dbo.Divisiones div where estadoDiv = 'A'";
			$qryDiv = mssql_query ( $sqlDiv );
			while( $rowDiv = mssql_fetch_array( $qryDiv ) ){ 
				if( trim($cDivisiones) == $rowDiv[id_division] ){
		?>
		        	<option value="<?php	echo $rowDiv[id_division];	?>" selected="selected" ><?php	echo $rowDiv[nombre];	?></option>
        <?php	
				}
				else{
			?>            
        	<option value="<?php	echo $rowDiv[id_division];	?>"><?php	echo $rowDiv[nombre];	?></option>
        <?php	
				}
			}
		?>
    </select>
    </td>
    <td class="TxtTabla">&nbsp;</td>
  </tr>
  <tr>
    <td align="right"  class="TituloTabla">Departamentos:</td>
    <td colspan="2" class="TxtTabla">
    <select name="cDepartamento" id="cDepartamento"  class="CajaTexto" onchange="document.form1.submit();">
    	<option value="">::: Todo :::</option>
    	<?php
			#	OSCAR MAURICIO LOPEZ SEGURA
			#	13-08-2012
			#	LISTAR DEPENDENCIAS ACTIVAS
			
			$sqlDep = "select *from HojaDeTiempo.dbo.Departamentos dep where estadoDpto = 'A'";
			if( trim($cDivisiones) != "" )
				$sqlDep .= " and dep.id_division = ".$cDivisiones." order by dep.id_division";
			
			$qryDep = mssql_query( $sqlDep );
			while( $rowDep = mssql_fetch_array( $qryDep ) ){ #, 
			if( trim($cDepartamento) == $rowDep[id_departamento] ){
		?>
		        	<option value="<?php	echo $rowDep[id_departamento];	?>" selected="selected" ><?php	echo $rowDep[nombre];	?></option>
        <?php	
				}
				else{
			?>         
	        <option value="<?php	echo $rowDep[id_departamento];	?>"><?php	echo $rowDep[nombre];	?></option>        
        <?php
				}
			}
		?>
    </select></td>
    <td class="TxtTabla">&nbsp;</td>
  </tr>
  <tr>
    <td width="15%" align="right" class="TituloTabla">Proyecto:</td>
    <td colspan="2" class="TxtTabla">
      <select name="plstProyecto" class="CajaTexto" id="plstProyecto" onchange="document.form1.submit();">
        <option value="">::: Todo :::</option>
        <? 
			$sqlPro = "select distinct A.id_proyecto, B.nombre nomProyecto, B.id_proyecto 
					   from HojaDeTiempo.dbo.horas A, HojaDeTiempo.dbo.Proyectos B, HojaDeTiempo.dbo.Usuarios C, 
					   HojaDeTiempo.dbo.Departamentos D, HojaDeTiempo.dbo.Divisiones E 
					   where A.id_proyecto = B.id_proyecto and A.unidad = C.unidad and C.id_departamento = D.id_departamento and 
					   D.id_division = E.id_division order by B.nombre";
			if( trim($cDivisiones) != "" ){
				$sqlPro = "select distinct A.id_proyecto, B.nombre nomProyecto, B.id_proyecto 
				 		   from HojaDeTiempo.dbo.horas A, HojaDeTiempo.dbo.Proyectos B, HojaDeTiempo.dbo.Usuarios C, 
						   HojaDeTiempo.dbo.Departamentos D, HojaDeTiempo.dbo.Divisiones E 
						   where A.id_proyecto = B.id_proyecto and A.unidad = C.unidad and C.id_departamento = D.id_departamento and 
						   D.id_division = E.id_division and D.id_division = ".$cDivisiones." order by B.nombre";
			}
			if( trim ($cDepartamento) != "" ){
				$sqlPro = "select distinct A.id_proyecto, B.nombre nomProyecto, B.id_proyecto 
						   from HojaDeTiempo.dbo.horas A, HojaDeTiempo.dbo.Proyectos B, HojaDeTiempo.dbo.Usuarios C, 
						   HojaDeTiempo.dbo.Departamentos D, HojaDeTiempo.dbo.Divisiones E 
						   where A.id_proyecto = B.id_proyecto and A.unidad = C.unidad and C.id_departamento = D.id_departamento and 
						   D.id_departamento = ".$cDepartamento." order by B.nombre";
						   #D.id_division = E.id_division and D.id_division = ".$cDivisiones." order by B.nombre";
			}
			$qryPro = mssql_query( $sqlPro );
			while( $rqwPro = mssql_fetch_array( $qryPro ) ){
#	  while ($lstRegProy=mssql_fetch_array($lstCursorProy)) { 
#	  if ($lstRegProy[id_proyecto] == $pltProyecto) {
#	  		$selProyecto = "selected";
#	  }
#	  else {
#			$selProyecto = "";
#	  } 
				if( trim($plstProyecto) == $rqwPro[id_proyecto] ){
		  ?>
      				<option value="<? echo $rqwPro[id_proyecto]; ?>" <? echo $selProyecto; ?> selected="selected" ><? echo $rqwPro[nomProyecto]; ?></option>
    	  <?php	
	  			}	
				else{
	  		?>
			        <option value="<? echo $rqwPro[id_proyecto]; ?>" <? echo $selProyecto; ?> ><? echo $rqwPro[nomProyecto]; ?></option>
	        <?php	
				}
			}
			?>
        </select></td>
    <td width="10%" class="TxtTabla"><input name="Submit8" type="submit" class="Boton" value="Consultar" /></td>
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
<?php	#echo $sql00."<br />";  ?>
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
        <td>Reporte de Fernando Manjarr&eacute;s </td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr>
        <td width="15%" class="FichaInAct"><a href="ReportesHT3.php" class="FichaInAct1">Horas facturada por usuario y Proyecto </a> </td>
        <td width="15%" class="FichaInAct"><a href="ReportesHT3b.php" class="FichaInAct1">Horas y valor facturado <br />
        por usuario y proyecto </a></td>
        <td width="15%" class="FichaAct">Proyectos <br />
  con facturaci&oacute;n </td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td height="1" colspan="4" class="TituloUsuario"> </td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Facturaci&oacute;n de la divisi&oacute;n - Proyectos con facturaci&oacute;n</td>
  </tr>
</table>

<div id="tblDm"></div>
    
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="right" class="TituloTabla">Valor Facturado = (Salario Personal Divisi&oacute;n Administrativa/ 185) * Horas Facturadas </td>
      </tr>
    </table>
    <table width="100%"  border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
      <tr class="TituloTabla2">
        <td>Proyecto</td>
        <td width="10%">Valor Facturado</td>
      </tr>
      <? 
	  $vlrTotalFacturado = 0;		
	  while ($reg00=mssql_fetch_array($cursor00)) { 
	  ?>
      <tr class="TxtTabla">
        <td>
		<? 
		#$vlrTotalFacturado = 0;		
		#while ($reg00=mssql_fetch_array($cursor00)) { 
			echo $reg00[nomProyecto] .  " [" . trim($reg00[codigo]) . "." . trim($reg00[cargo_defecto]) . "] "; 
			$elNomProyecto = $reg00[nomProyecto] .  " [" . trim($reg00[codigo]) . "." . trim($reg00[cargo_defecto]) . "] ";
			$excel.="$elNomProyecto\t"; 
		?>
        </td>
        <td width="10%" align="right"> $
          <? 
			$vlrFacturado = 0;
			if (trim($reg00[vlrTotalFacturado]) != "") {
				$vlrFacturado = $reg00[vlrTotalFacturado] ;
				$vlrTotalFacturado = $vlrTotalFacturado + $vlrFacturado; 
				echo number_format($vlrFacturado, 2, ",", ".");
			} 
			else {
				echo "0";
			}
		?>
          <? $excel.="$vlrFacturado\n"; ?></td>
      </tr>
      <? } ?>
      <tr class="TituloTabla2">
        <td align="right">VALOR TOTAL FACTURADO</td>
        <td align="right">$ <? echo number_format($vlrTotalFacturado, 2, ",", "."); ?></td>
      </tr>
    </table>
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
	<form action="rptXlsFactura01.php" method="post">
      <tr>
        <td>
		<input type="hidden" name="export" value="<? echo $excel; ?>">
		<input type="submit" class="Boton" value="Generar XLS">		
		</td>
      </tr>
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
          <tr>
            <td class="TxtTabla"><input name="Submit" type="submit" class="Boton" onclick="MM_goToURL('parent','frm-GrabaTiempo.php');return document.MM_returnValue" value="Página principal Hoja de tiempo" /></td>
            <td align="right" class="TxtTabla">
			<input name="Submit3" type="submit" class="Boton" onclick="MM_goToURL('parent','ReportesHT.php');return document.MM_returnValue" value="Programaci&oacute;n personal" />
			<? if ($muestraDirProyecto > 0) { ?>
			<input name="Submit3" type="submit" class="Boton" onclick="MM_goToURL('parent','ReportesHT2.php');return document.MM_returnValue" value="Reporte Director de proyecto" />
			<? } ?>
			</td>
          </tr>
        </table></td>
      </tr>
    </table>
</body>
</html>
