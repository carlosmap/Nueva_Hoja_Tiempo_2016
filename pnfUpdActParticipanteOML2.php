<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

//echo "Proy=".  $cualProyecto . "<br>";
//echo "Act=" . $cualActividad . "<br>";
//exit;

//22Enero2008
//Trae el nombre de los proyectos en los que el usuario activo aparece como director y/o coordinador
$sql="SELECT P.*, D.nombre nombreD, D.apellidos apellidosD, C.nombre nombreC, C.apellidos apellidosC  ";
$sql=$sql." FROM proyectos P, Usuarios D, Usuarios C " ;
$sql=$sql." WHERE P.id_director *= D.unidad " ;
$sql=$sql." AND P.id_coordinador *= C.unidad " ;
$sql=$sql." AND P.id_proyecto = " . $cualProyecto ;
$cursor = mssql_query($sql);

//10Jun2008
//Identificar si el usuario activo verá toda la información o sólo sus Actividades
$esDC = 0 ;
$esProgP = 0;
$esOrdG = 0 ;
$todo= 0 ;
$verProyecto="SI";

//El usuario es Director o Coordinador
$vSqlU="Select coalesce(count(*), 0) existeDir ";
$vSqlU=$vSqlU." from HojaDeTiempo.dbo.Proyectos ";
$vSqlU=$vSqlU." where (id_director = " . $laUnidad . " or id_coordinador = " . $laUnidad . " ) ";
$vSqlU=$vSqlU." and id_proyecto = " . $cualProyecto ;
$vCursorU = mssql_query($vSqlU);
if ($vRegU=mssql_fetch_array($vCursorU)) {
	$esDC =  $vRegU[existeDir] ;
}

//Si el usuarios es Programador del proyecto
$vSqlU="Select coalesce(count(*), 0) existeProg ";
$vSqlU=$vSqlU." from HojaDeTiempo.dbo.Programadores  ";
$vSqlU=$vSqlU." where unidad = " . $laUnidad ;
$vSqlU=$vSqlU." and id_proyecto = " . $cualProyecto ;
$vSqlU=$vSqlU." and progProyecto = 1 ";
$vCursorU = mssql_query($vSqlU);
if ($vRegU=mssql_fetch_array($vCursorU)) {
	$esProgP =  $vRegU[existeProg] ;
}

//Si el usuario es ordenador del gasto
$vSqlU="select coalesce(count(*), 0) existeOrd ";
$vSqlU=$vSqlU." from GestiondeInformacionDigital.dbo.OrdenadorGasto ";
$vSqlU=$vSqlU." where unidadOrdenador = ". $laUnidad ;
$vSqlU=$vSqlU." and id_proyecto =" . $cualProyecto ;
$vCursorU = mssql_query($vSqlU);
if ($vRegU=mssql_fetch_array($vCursorU)) {
	$esOrdG =  $vRegU[existeOrd] ;
}

//Si alguna de las variables es > 0 el usuario podrá ver todo
$todo= $esDC + $esProgP + $esOrdG ;
if ($todo > 0) {
	$verProyecto="SI";
}
else {
	$verProyecto="NO";
}


if (trim($pOrdenAct)=="") {
	$pOrdenAct=1;
}

@mssql_select_db("HojaDeTiempo",$conexion);
$fDivSql="Select * from divisiones ";
$fDivSql=$fDivSql." where (nombre <> '' and nombre <> 'sd') ";
$fDivSql=$fDivSql."and estadoDiv = 'A' ";
$fDivSql=$fDivSql." order by nombre ";
$fDivCursor = mssql_query($fDivSql);

$primerActiv = 1;
if (($verProyecto=="SI") OR ($_SESSION["sesPerfilUsuario"] == 1 )  ) { 
	$sql2="Select A.* , U.nombre nomUsu, U.apellidos apeUsu ";
	$sql2=$sql2." from Actividades A, Usuarios U" ;
	$sql2=$sql2." where A.id_encargado *= U.unidad " ;
	$sql2=$sql2." and A.id_proyecto = " . $cualProyecto ;
	
	//para que en Porce muestre ordenado por ID
	if ($cualProyecto == 697) {
		$sql2=$sql2." order by A.id_actividad " ;
	}
	else {
		//$sql2=$sql2." order by A.nivelesActiv " ;
		
		if ($pOrdenAct==1) {
			$sql2=$sql2." order by A.actPrincipal , nivelesActiv " ;
		}
		else {
			$sql2=$sql2." order by A.macroactividad, A.actPrincipal , nivelesActiv " ;
		}
	}
	$cursor2 = mssql_query($sql2);
	if ($reg2=mssql_fetch_array($cursor2)) {
		$primerActiv =  $reg2[id_actividad] ;

	}
}
//Sino, se trata de responsable de actividad o programadores de actividad y ven sus Actividades
else {
	$sql2="Select A.*, U.nombre nomUsu, U.apellidos apeUsu  ";
	$sql2=$sql2." from ( " ;
	$sql2=$sql2." Select id_actividad " ;
	$sql2=$sql2." from Actividades " ;
	$sql2=$sql2." where id_proyecto = " . $cualProyecto ;
	$sql2=$sql2." and id_encargado =" . $laUnidad ;
	$sql2=$sql2." UNION " ;
	$sql2=$sql2." select id_actividad " ;
	$sql2=$sql2." from ParticipantesActividad " ;
	$sql2=$sql2." where id_proyecto =" . $cualProyecto ;
	$sql2=$sql2." and unidad = " . $laUnidad ;
	$sql2=$sql2." UNION " ;
	$sql2=$sql2." select id_actividad " ;
	$sql2=$sql2." from Programadores " ;
	$sql2=$sql2." where id_proyecto =" . $cualProyecto ;
	$sql2=$sql2." and unidad = " . $laUnidad ;
	$sql2=$sql2." UNION " ;
	$sql2=$sql2." select id_actividad " ;
	$sql2=$sql2." from Actividades" ;
	$sql2=$sql2." where id_proyecto = " . $cualProyecto ;
	$sql2=$sql2." and dependeDe In " ;
	$sql2=$sql2."  (" ;
	$sql2=$sql2." select id_actividad" ;
	$sql2=$sql2." from Programadores " ;
	$sql2=$sql2." where id_proyecto =" . $cualProyecto ;
	$sql2=$sql2." and unidad = " . $laUnidad ;
	$sql2=$sql2." )" ;
	$sql2=$sql2." ) R , Actividades A, Usuarios U " ;
	$sql2=$sql2." where R.id_actividad = A.id_actividad " ;
	$sql2=$sql2." AND A.id_encargado *= U.unidad " ;
	$sql2=$sql2." and A.id_proyecto = " . $cualProyecto ;
	if ($pOrdenAct==1) {
		$sql2=$sql2." order by A.actPrincipal , nivelesActiv " ;
	}
	else {
		$sql2=$sql2." order by A.macroactividad, A.actPrincipal , nivelesActiv " ;
	}

	$cursor2 = mssql_query($sql2);
	if ($reg2=mssql_fetch_array($cursor2)) {
		$primerActiv =  $reg2[id_actividad] ;
	}
}
$cursor2 = mssql_query($sql2);
//echo $sql2 ;

if (trim($cualActividad) == "" ) {
	//$cualActividad = 1;
	$cualActividad = $primerActiv;
}

//10Sep2008
//Para incluir el filtro del orden de las registros que aparecen en recursos
if (trim($pOrdena)=="") {
	$pOrdena=1;
}


//09Oct2008
//Para sacar el listado de usuarios asociados a una actividad 
$sql3u="select distinct A.unidad, U.nombre, U.apellidos, C.nombre nomCat   ";
$sql3u=$sql3u." from asignaciones A, Usuarios U, Horarios H, Categorias C , Departamentos D ";
$sql3u=$sql3u." where A.unidad = U.unidad  ";
$sql3u=$sql3u." and A.IDhorario = H.IDhorario  ";
$sql3u=$sql3u." And U.id_categoria = C.id_categoria ";
$sql3u=$sql3u." And U.id_departamento = D.id_departamento ";
$sql3u=$sql3u." and A.id_proyecto = " . $cualProyecto ;
$sql3u=$sql3u." and A.id_actividad = " . $cualActividad ;
if(trim($pFiltro)!="") {
	$sql3u=$sql3u." and U.id_categoria = " . $pFiltro ;
}
if(trim($pfDivision)!="") {
	if ($pfDivision == "888") { 
		$sql3u=$sql3u." and D.id_division > 25" ;
	}
	else {
		$sql3u=$sql3u." and D.id_division = " . $pfDivision ;
		if(trim($miDpto)!="") {
			$sql3u=$sql3u." and D.id_departamento = " . $miDpto ;	
		}
	}
}
if ($pOrdena == 1) {
$sql3u=$sql3u." ORDER BY U.apellidos " ;
}
if ($pOrdena == 2) {
$sql3u=$sql3u." ORDER BY C.nombre  " ;
}
$cursor3u = mssql_query($sql3u);



//1Jul2008
//Trae la información de los costos directos asociados a una actividad
$CDsql="select C.* , U.nombre, U.apellidos ";
$CDsql=$CDsql." from HojaDeTiempo.dbo.ActividadesCostosD C, HojaDeTiempo.dbo.Usuarios U ";
$CDsql=$CDsql." where C.unidad = U.unidad ";
$CDsql=$CDsql." and C.id_proyecto =" . $cualProyecto ;
$CDsql=$CDsql." and C.id_actividad =" . $cualActividad ;
$CDcursor = mssql_query($CDsql);

//8Ago2008
//Trae la información del personal externos 
$PEsql="SELECT P.*, E.nombre , E.apellidos , U.nombre nomUsu, U.apellidos apeUsu ";
$PEsql=$PEsql." FROM HojaDeTiempo.dbo.ActividadesPersonalExt P,  ";
$PEsql=$PEsql." HojaDeTiempo.dbo.PersonalExterno E, HojaDeTiempo.dbo.Usuarios U  ";
$PEsql=$PEsql." WHERE P.identificacion = E.identificacion ";
$PEsql=$PEsql." AND P.unidad = U.unidad ";
$PEsql=$PEsql." AND P.id_proyecto = " . $cualProyecto ;
$PEsql=$PEsql." AND P.id_actividad = " . $cualActividad ;
$PEcursor = mssql_query($PEsql);

if( $recarga == 2 ){
	$opc = "aplicaUsuario".$reg;
	$act = "idAct".$reg;
	$sqlUpd = " Update ".$tabla." set 
					estado = ".$estado."
				Where id_actividad = ".$idAct." AND unidad = ".$und." AND id_proyecto = ".$proyecto;
	$qryUpd = mssql_query( $sqlUpd );
	if( $qryUpd ){
		echo "<script type='text/javascript' language='javascript'>
				alert( 'Se cambio el estado de la actividad para el usuario.' );
				window.open( 'htProgProyectos02OML2.php?cualProyecto=".$proyecto."', 'winHojaTiempo' );
				window.close();
			  </script>";
	}
	else{
		echo "<script type='text/javascript' language='javascript'>
				alert( 'Se presento un error al tratar de cambiar el estado.' );
			  </script>";
	}
	echo $sqlDel;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
<!--

window.name="winHojaTiempo";

function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}

<!--		->
	function envia2(){
		document.form1.recarga.value = 2;
		document.form1.submit();
	}
//-->
</script>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Programaci&oacute;n de Proyectos</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<?
	#	Trae la información del coordinador y el director del proyecto
	$sqlDrCr = "select dr.unidad undD, cr.unidad undC, cr.nombre nCr, cr.apellidos aCr, dr.nombre nDr, dr.apellidos aDr from Proyectos pr, Usuarios cr, Usuarios dr
				where pr.id_proyecto = ".$cualProyecto." AND cr.unidad = pr.id_coordinador AND dr.unidad = pr.id_director";
	$qryDrCr = mssql_fetch_array( mssql_query( $sqlDrCr ) );
?>
    <table width="100%"  border="0" cellspacing="1" cellpadding="1" bgcolor="#FFFFFF">
    <tr class="TituloTabla2">
    <td colspan="4" align="left" class="TituloUsuario"> Encargados del proyecto </td>
    </tr>
    <tr class="TituloTabla2">
    <td width="25%">Director</td>
    <td width="25%" >Coordinador</td>
    <td width="25%" >Programadores</td>
    <td width="25%" >Ordenadores de Gasto</td>
    </tr>
    <tr class="TxtTabla">
    <td width="25%" valign="top"><?= $qryDrCr[nDr]." ".$qryDrCr[aDr]." [".$qryDrCr[undD]."]" ?></td>
    <td width="25%" valign="top" ><?= $qryDrCr[nCr]." ".$qryDrCr[aCr]." [".$qryDrCr[undC]."]" ?></td>
    <td width="25%" >
    <?
        $sqlPr = "select u.nombre, u.apellidos, u.unidad from Programadores pr, Usuarios u
                  where id_proyecto = ".$cualProyecto." and pr.unidad = u.unidad";
        $qryPr = mssql_query( $sqlPr );
        while( $rw = mssql_fetch_array( $qryPr ) ){
            echo $rw[apellidos]." ".$rw[nombre]." [".$rw[unidad]."]<br />";
        }
    ?>
    </td>
    <td width="25%" valign="top" >
    <?
        $sqlPr = "select u.nombre, u.apellidos, u.unidad from GestiondeInformacionDigital.dbo.OrdenadorGasto pr, Usuarios u
                  where id_proyecto = ".$cualProyecto." and pr.unidadOrdenador = u.unidad";
        $qryPr = mssql_query( $sqlPr );
        while( $rwg = mssql_fetch_array( $qryPr ) ){
            echo $rwg[apellidos]." ".$rwg[nombre]." [".$rwg[unidad]."]<br />";
        }
    ?>
    </td>
    </tr>
    </table>
	<form name="form1" id="form1" method="post" action="">
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
        <td bgcolor="#FFFFFF">
            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td class="TxtTabla">&nbsp;</td>
            </tr>
            </table>
            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="TituloUsuario">Persona involucrada en el proyecto</td>
              </tr>
            </table>
            <?
                $sqlActResponsable = "SELECT distinct
                                        usu.unidad, ( usu.apellidos + ' ' + usu.nombre ) NombresApellidos, dep.nombre nDepartamento, div.nombre nDivision, cat.nombre Categoria
                                      FROM
                                        HojaDeTiempo.dbo.".$tabla." res, HojaDeTiempo.dbo.Actividades act, HojaDeTiempo.dbo.Usuarios usu,
                                        HojaDeTiempo.dbo.Categorias cat, HojaDeTiempo.dbo.Departamentos dep, HojaDeTiempo.dbo.Divisiones div, HojaDeTiempo.dbo.Programadores prog									
                                      WHERE
                                        usu.unidad = res.unidad AND cat.id_categoria = usu.id_categoria AND usu.id_departamento = dep.id_departamento
                                        AND dep.id_division = div.id_division AND act.id_actividad = res.id_actividad 
                                        AND	res.id_proyecto =".$cualProyecto." AND res.unidad = ".$idAct;
                $qryActResponsable = mssql_query( $sqlActResponsable );
            ?>
            <table width="100%"  border="0" cellspacing="1" cellpadding="0">
              <tr class="TituloTabla2">
                <td width="8%">Unidad</td>
                <td width="20%">Nombre</td>
                <td width="5%">Categor&iacute;a</td>
                <td width="10%">Divisi&oacute;n</td>
                <td width="10%">Departamento</td>
                <!-- <td width="1%">&nbsp;</td> -->
              </tr>
              <?	$row = mssql_fetch_array( $qryActResponsable ); ?>
              <tr class="TxtTabla">
                <td valign="top"><?= $row[unidad] ?></td>
                <td valign="top">
                <?= $row[NombresApellidos] ?>
                <input type="hidden" name="und" value="<?= $row[unidad] ?>" />
                </td>
                <td valign="top"><?= $row[Categoria] ?></td>
                <td valign="top"><?= $row[nDivision] ?></td>
                <td valign="top"><?= $row[nDepartamento] ?></td>
              </tr>
              </table>
            <table width="100%"  border="0" cellspacing="1" cellpadding="0">
                <tr class="TxtTabla">
                    <td valign="top">&nbsp;</td>
                </tr>
            </table>
            <table width="100%"  border="0" cellspacing="1" cellpadding="0">
                <tr class="TituloUsuario">
                    <td colspan="4" valign="top">Actividades en las que se encuentra </td>
                </tr>
                <tr class="TituloTabla2">
                  <td colspan="4" valign="top">Desea cambiar el estado del usuario en esta actividad</td>
                </tr>
                <? #&tabla=<?= $tabla 
                    $sqlAct = "SELECT act.id_actividad idAct, act.nombre nActividad, act.macroactividad, res.estado	
                               FROM HojaDeTiempo.dbo.".$tabla." res, HojaDeTiempo.dbo.Actividades act, HojaDeTiempo.dbo.Usuarios usu	
                               WHERE 
                                usu.unidad = res.unidad	AND act.id_actividad = res.id_actividad AND act.id_proyecto = res.id_proyecto 
                                AND	res.id_proyecto = ".$cualProyecto." AND usu.unidad = ".$row[unidad]." AND res.id_actividad = ".$idActi; 
                    $qryAct = mssql_query( $sqlAct );
                    $r = 1;
                    $rw = mssql_fetch_array( $qryAct );
					 if( $rw[estado] == 1 ){
						$texto = "Inactivar";
						$estado = 0;
					 }
					else{
						$texto = "Activar";
						$estado = 1;
					}
				?>
                <tr class="TxtTabla">
                    <td colspan="4" valign="top">
                        <?= "<b>[ ".$rw[macroactividad]." ]</b> ".$rw[nActividad] ?>
                      <input type="hidden" name="idAct" value="<?= $rw[idAct] ?>" />
						<input type="hidden" name="estado" value="<?= $estado ?>" />
                    <input type="hidden" name="proyecto" value="<?= $cualProyecto ?>" /></td>
                </tr>
            </table>
            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td align="right" class="TxtTabla">
                <input name="cantUsuarios" type="hidden" value="<?= $r ?>" />
                <input name="recarga" id="recarga" type="hidden" value="1" />
                <input name="Submit2" type="submit" class="Boton" onclick="window.close();" value="Cancelar" />
<input name="Submit" type="submit" class="Boton" onclick="envia2()" value="<?= $texto ?>" /></td>
              </tr>
            </table>
            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                <tr>
                <td align="right" class="TxtTabla">&nbsp;
                </td>
                </tr>
            </table>
        </td>
        </tr>
		</table>
    </form>
</body>
</html>
