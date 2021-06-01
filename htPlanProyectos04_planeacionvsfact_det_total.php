<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";


//SI EL PARAMETRO T (LOTE DE TRABAJO), ESTA VACIO, ES POR QUE SE HA CONSULTADO TODAS LAS ACTIVIDADES  EN LA PAGINA ANTERIOR, Y SE HA SELECCIONADO EL DETALLE DE UNA ACTIVIDAD (NIVEL 4)
//ENTONCES SE CONSULTA EL ID DEL LOTE DE TRABAJO DE LA DIVISON A LA CUAL PERTENECE LA ACTIVIDAD (NIVEL 4)
if(trim($T)=="")
{
	$sql_lt_act="select dependeDe from Actividades where id_actividad=".$DIV."  and id_proyecto=".$cualProyecto."  and actPrincipal=".$LC;
	$cur_lt_act=mssql_query($sql_lt_act);
	if($datos_lt_act=mssql_fetch_array($cur_lt_act))
	{
		$T=$datos_lt_act["dependeDe"];
	}
}


$LT=$T;



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

/*

//01Feb2013
//Trae los RESPONSABLES de actividades, ResponsablesActividades (ingresados por los responsables delegando el tema
//y los ParticipantesActividades que se asignan como participantes en el tema 
$sql01="SELECT A.unidad, B.nombre nombreUsu, B.apellidos apellidosUsu, C.nombre nomCategoria, D.nombre nomDepartamento, E.nombre nomDivision ";
$sql01=$sql01." FROM ";
$sql01=$sql01." 	( ";
$sql01=$sql01." 	SELECT DISTINCT id_encargado as unidad ";
$sql01=$sql01." 	FROM Actividades ";
$sql01=$sql01." 	WHERE id_proyecto = " . $cualProyecto ;
$sql01=$sql01." 	and id_encargado is not null ";
$sql01=$sql01." 	UNION ";
$sql01=$sql01." 	SELECT DISTINCT unidad ";
$sql01=$sql01." 	FROM ResponsablesActividad ";
$sql01=$sql01." 	WHERE id_proyecto =  " . $cualProyecto ;
$sql01=$sql01." 	UNION ";
$sql01=$sql01." 	SELECT DISTINCT unidad ";
$sql01=$sql01." 	FROM ParticipantesActividad ";
$sql01=$sql01." 	WHERE id_proyecto =  " . $cualProyecto ;
$sql01=$sql01." 	) A, Usuarios B, Categorias C, Departamentos D, Divisiones E ";
$sql01=$sql01." where A.unidad = B.unidad ";
$sql01=$sql01." AND B.id_categoria = C.id_categoria ";
$sql01=$sql01." AND B.id_departamento = D.id_departamento ";
$sql01=$sql01." AND D.id_division = E.id_division ";
$cursor01 = mssql_query($sql01);


*/





//--------HASTA AQUI

//10Jun2008
//Identificar si el usuario activo verá toda la información o sólo sus actividades
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

//Cierra 10Jun2008

if (trim($pOrdenAct)=="") {
	$pOrdenAct=1;
}


$primerActiv = 1;

if (trim($pOrdena)=="") {
	$pOrdena=1;
}

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

function array_url($arrai)
{
		$tmp=serialize($arrai);  //Serializar el arreglo.
		$url=urlencode($tmp);  //Codificar URL. 
		return($url);
}

	if(trim($ACT==""))
	{
		if(trim($DIV!=""))
		{
			$act=$DIV;
		}
	}
	else
		$act=$ACT;

		//CONSULTA LAS ACTIVIDADES, QUE TIENE FACTURACION EN EL PROYECTO
		$sql_act_pla="SELECT id_actividad,macroactividad,nivel,nombre,fecha_inicio,fecha_fin FROM ( Select 
			CAST(REPLACE((REPLACE((SUBSTRING(A.macroactividad, 3, LEN(A.macroactividad))),'.','')),'A','') AS int) valMacro, factor = 
				case nivel when 1 then 100000 		when 2 then 10000 		when 3 then 1000 		when 4 then 100 
				end, A.*, U.nombre nomUsu, U.apellidos apeUsu 	
					from Actividades A, Usuarios U 	
				where A.id_encargado *= U.unidad and A.id_proyecto =".$cualProyecto."  	
				and A.id_actividad in( ";


				//CONSULTA LAS ACTIVIDADES QUE TIENEN PARTICIPANTES CON  C=(PLANEACION Y FACTURACION) Ó S=(CON FACTURACION SIN PLANEACION)
				if(($personal=='C')||($personal=='S'))
				{
					if($personal=='C')
						$opci=" IN ";
					if($personal=='S')
						$opci=" NOT IN ";

					$sql_act_pla=$sql_act_pla."	select distinct(id_actividad) from FacturacionProyectos where id_proyecto=".$cualProyecto." and vigencia=".$V." and 
					unidad ".$opci." (select unidad from PlaneacionProyectos where id_proyecto=".$cualProyecto." and vigencia=".$V." and id_actividad= FacturacionProyectos.id_actividad ";
				}

				//CONSULTA TODOS LAS ACTYIVIDADES QUE TIENE FACTURACION
				if($personal=='T')
				{
					$sql_act_pla=$sql_act_pla."select distinct(id_actividad) from FacturacionProyectos where id_proyecto=".$cualProyecto." and vigencia=".$V;
				}

		$sql_act_pla=$sql_act_pla.")

			)  Z 
			order by (valMacro *  factor)";

		$cur_act_pla=mssql_query($sql_act_pla);
//echo $sql_act_pla." ".mssql_get_last_message();
				
				if($personal=='T')
				{
						$sql_parti=" ( ";
						$sql_parti=$sql_parti.' '.conPlaneacion_sinPlaneacion ($cualProyecto,$V,$datos_act_pla["id_actividad"],' NOT IN ');		
						$sql_parti=$sql_parti.') UNION (';
						$sql_parti=$sql_parti.' '.conPlaneacion_sinPlaneacion ($cualProyecto,$V,$datos_act_pla["id_actividad"],' IN ');			
						$sql_parti=$sql_parti.' ) ';
				}	


		//FUNCION, QUE FORMA LA CONSULTA A EJECUTAR, DEPENDIENDO DE LO SELECCIONADO EN LA LISTA  "Tipo de  Personal"
		//SI SON LOS PARTICIPANTES, CON PLANEACION O SIN PLANEACION, ESTO SE DETERMINA, CON EL PARAMETRO $con, QUE PUEDE CONTIENERE EL VALOR ' IN' O 'NOT IN'
		// 'IN' CON PLANEACION Y FACTURACION RELACIONADA
		// 'NOT IN ' CON FACTURACION, PERO SIN PLANEACION 
		function conPlaneacion_sinPlaneacion($cualProyecto,$lstVigencia,$act,$con)
		{
				//CONSULTA DE USUARIOS CON PLANEACION Y FACTURACION RELACIONADA	
				$sql_parti=" select distinct( FacturacionProyectos.unidad),FacturacionProyectos.id_proyecto,FacturacionProyectos.id_actividad, case esInterno when 'E' then (select UPPER(nombre+' '+apellidos)  from TrabajadoresExternos where consecutivo=FacturacionProyectos.unidad )  
				 when 'I' then (select UPPER(nombre+' '+apellidos)  from Usuarios where unidad=FacturacionProyectos.unidad ) end nombre_apellido, 
				 case  esInterno when 'I' then (select fechaRetiro from Usuarios where unidad=FacturacionProyectos.unidad) else  NULL   end  fechaRetiro , vigencia , esInterno
				   from FacturacionProyectos	
				inner join Actividades on FacturacionProyectos.id_actividad=Actividades.id_actividad and FacturacionProyectos.id_proyecto=Actividades.id_proyecto 		
				where FacturacionProyectos.unidad ".$con." (			
				(
				select distinct(PlaneacionProyectos.unidad) from PlaneacionProyectos
							 inner join Actividades on PlaneacionProyectos.id_actividad=Actividades.id_actividad and PlaneacionProyectos.id_proyecto=Actividades.id_proyecto 
							inner join Usuarios on PlaneacionProyectos.unidad=Usuarios.unidad
							left join ParticipantesActividad on PlaneacionProyectos.id_proyecto=ParticipantesActividad.id_proyecto and PlaneacionProyectos.id_actividad=ParticipantesActividad.id_actividad
								and ParticipantesActividad.unidad=PlaneacionProyectos.unidad  						
							where  PlaneacionProyectos.id_actividad=".$act." and PlaneacionProyectos.id_proyecto=".$cualProyecto." and PlaneacionProyectos.vigencia=".$lstVigencia."	
				)
				union
				(
				SELECT distinct(PlaneacionProyectos.unidad)
				FROM PlaneacionProyectos 
				inner join ParticipantesExternos on PlaneacionProyectos.id_proyecto=ParticipantesExternos.id_proyecto
					 and PlaneacionProyectos.id_actividad=ParticipantesExternos.id_actividad and PlaneacionProyectos.unidad=ParticipantesExternos.consecutivo
				inner join TrabajadoresExternos on 	 ParticipantesExternos.consecutivo=TrabajadoresExternos.consecutivo
				 where PlaneacionProyectos.esInterno='E' and PlaneacionProyectos.id_proyecto=".$cualProyecto." and PlaneacionProyectos.id_actividad=".$act."  and PlaneacionProyectos.vigencia=".$lstVigencia."
				 )
				 ) and  FacturacionProyectos.id_actividad=".$act." and FacturacionProyectos.id_proyecto=".$cualProyecto." and FacturacionProyectos.vigencia=".$lstVigencia." ";

				return $sql_parti;
		}

//	global $array= array();

//echo mssql_get_last_message()."  ddddd <br>".$sql_parti;
function fecha_ini_finaliza_activi($cualProyecto,$act)
{
	
	//CONSULTA LA FECHA DE INICIO Y FINALIZACION DE LA ACTIVIDAD
	$sql_act1="select fecha_inicio,fecha_fin, year(fecha_inicio) as y_i ,month(fecha_inicio) as m_i, day(fecha_inicio) as d_i  ,year(fecha_fin) as y_f ,month(fecha_fin) as m_f,day(fecha_fin) as d_f  from Actividades where id_proyecto=".$cualProyecto." and id_actividad=".$act." ";
	$cur_act1=mssql_query($sql_act1);
	$datos_act1=mssql_fetch_array($cur_act1);

	$ano_i=$datos_act1["y_i"];
	$mes_i=$datos_act1["m_i"];
	$dia_i=$datos_act1["d_i"];
	$fecha_i=$datos_act1["fecha_inicio"];

	$ano_f=$datos_act1["y_f"];
	$mes_f=$datos_act1["m_f"];
	$dia_f=$datos_act1["d_f"];
	$fecha_f=$datos_act1["fecha_fin"];


	$array[0]=$ano_i;
	$array[1]=$mes_i;
	$array[2]=$dia_i;
	$array[3]=$fecha_i;
	$array[4]=$ano_f;
	$array[5]=$mes_f;
	$array[6]=$dia_f;
	$array[7]=$fecha_f;
//	return   $array[7]; // $array;
//echo " **".$ano_f."********* <br>";
	return array($ano_i,$mes_i,$dia_i,$fecha_i,$ano_f,$mes_f,$dia_f,$fecha_f); //,,,,$dia_f,$fecha_f);


}
function imprime_planea_factura($hombresMes,$hombresMesF,$caso)
{ 


							if(trim($hombresMes)=='&nbsp;')
							{
									$total_perso=0;

							}
							if(trim($hombresMesF)=='&nbsp;')
							{
									$total_perso_fact=0;			
							}
							$total_perso_fact=$hombresMesF;
							//IMAGEN DE LA INFROMACION DE LA FACTURACION
							if($total_perso_fact<=1)		//	SI Z ES MENOR O IGUAL A 1
							{
								if($total_perso_fact==1) //si EL VALOR ES 1
								{
									$total_perso_fact-=1;
									$ima2=" imagenes/ico1.gif";
								}

								else if ( ($total_perso_fact<1) && (0.75<=$total_perso_fact) ) // si el valor esta entre 0.99 y 0.75
								{
//									$total_perso-=0.75;
									$total_perso_fact=0;
									$ima2="imagenes/ico2.gif";
								}

								else if ( ($total_perso_fact<0.75) && (0.5<=$total_perso_fact) ) // si el valor esta entre 0.75 y 0.5
								{
//									$total_perso-=0.5;
									$total_perso_fact=0;
									$ima2="imagenes/ico3.gif";
								}
								else if ( ($total_perso_fact<0.5) && (0.25<=$total_perso_fact) ) // si el valor esta entre 0.5 y 0.25
								{
//									$total_perso-=0.25;
									$total_perso_fact=0;
									$ima2="imagenes/ico4.gif";
								}
								else if ( ($total_perso_fact<0.25) && (0<$total_perso_fact) ) // si el valor esta entre 0.25 y 0.01
								{
//									$total_perso-=0.25;
									$total_perso_fact=0;
									$ima2="imagenes/ico6.gif";
								}
								else
								{ $ima2="imagenes/ico5.gif"; } 
								

							}
							else //si el valor es mayor a 1
							{
								$ima2="../portal/imagenes/eje1.gif";
								$total_perso_fact-=1;
							}

									//IMAGENES QUE MUESTRAN LAS IMAGENES DE LA PLANEACION
									$total_perso=$hombresMes;				
									if($total_perso<=1)		//	SI Z ES MENOR O IGUAL A 1
									{
										if($total_perso==1) //si EL VALOR ES 1
										{
											$total_perso-=1;
											$ima=" imagenes/ico11.gif";
										}
		
										else if ( ($total_perso<1) && (0.75<=$total_perso) ) // si el valor esta entre 0.99 y 0.75
										{
		//									$total_perso-=0.75;
											$total_perso=0;
											$ima="imagenes/ico22.gif";
										}
		
										else if ( ($total_perso<0.75) && (0.5<=$total_perso) ) // si el valor esta entre 0.75 y 0.5
										{
		//									$total_perso-=0.5;
											$total_perso=0;
											$ima="imagenes/ico33.gif";
										}
										else if ( ($total_perso<0.5) && (0.25<=$total_perso) ) // si el valor esta entre 0.5 y 0.25
										{
		//									$total_perso-=0.25;
											$total_perso=0;
											$ima="imagenes/ico44.gif";
										}
										else if ( ($total_perso<0.25) && (0<$total_perso) ) // si el valor esta entre 0.25 y 0.01
										{
		//									$total_perso-=0.25;
											$total_perso=0;
											$ima="imagenes/ico66.gif";
										}
										else
										{ $ima="imagenes/ico5.gif"; } 
										
		
									}
									else //si el valor es mayor a 1
									{
										$ima="../portal/imagenes/ico1.gif";
										$total_perso-=1;
									}
//											caso('.$caso.') '.$datos_planeacion[mes].'
//											caso('.$caso.') '.$datos_facturacion[mes].'
	echo '
                                <td width="4%" align="center" class="TxtTabla" >
                                  <table width="100%" >
                                        <tr> 
                                            <td width="100%" background="'.$ima.'" class="TxtTabla" >
                                            '.$hombresMes.'
                                            </td>
                                        </tr>							
        
                                    </table>
                                  <table width="100%" >
                                        <tr>
                                            <td width="100%" background="'.$ima2.'" class="TxtTabla" >

                                            '.$hombresMesF.'
                                            </td>
                                        </tr>
                                    </table>
        
                                </td>    
		';
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript">
<!--


window.name="winHojaTiempo";


function envia0()
{
	var error = 'n';
	var mensaje="";

	if(document.Form1.Lote_control.value == '')
	{
		error = 's';
		mensaje = mensaje + 'Seleccione un lote de control. \n';
	}
	if(document.Form1.Lote_trabajo.value == '')
	{
		error = 's';
		mensaje = mensaje + 'Seleccione un lote de trabajo. \n';
	}
	if(document.Form1.Division.value == '')
	{
		error = 's';
		mensaje = mensaje + 'Seleccione una división. \n';
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

//-->
</script>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>.:: Planeaci&oacute;n de Proyectos</title>
</head>


<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
	<? include("bannerArriba.php") ; ?>
	<div class="TxtNota2" style="position:absolute; left:3px; top:55px; width: 639px; height: 30px;">
Planeaci&oacute;n de proyectos - Participantes</div>
	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table><table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>

    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;</td>
      </tr>
    </table>
    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario"> .: PROYECTO</td>
  </tr>
</table>

    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
	<form name="Form1" id="Form1" method="post" action="">
      <tr>
        <td bgcolor="#FFFFFF">
		<table width="100%"  border="0" cellspacing="1" cellpadding="1">
      <tr class="TituloTabla2">
        <td>Proyecto</td>
        <td width="20%">C&oacute;digo</td>
        <td width="20%">Encargados</td>
        <td width="20%">Programadores</td>
      </tr>
       <?
	  while ($reg=mssql_fetch_array($cursor)) {
	  ?>

	  <tr valign="top" class="TxtTabla">
	    <td><? echo  ucwords(strtolower($reg[nombre])) ; ?></td>
        <td width="20%"><?
		//27Ene2009
		//Traer los cargos adicionales del proyecto
		$sqlCargos="SELECT * FROM HojaDeTiempo.dbo.Cargos ";
		$sqlCargos=$sqlCargos." where id_proyecto = " . trim($reg[id_proyecto]) ;
		$cursorCargos = mssql_query($sqlCargos);
		
		?>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="3%"><strong><? echo  trim($reg[codigo])  ; ?></strong></td>
            <td width="1%"><strong>.</strong></td>
            <td width="5%"><strong><? echo  $reg[cargo_defecto] ; ?></strong></td>
            <td>[<? echo  $reg[descCargoDefecto] ; ?>]</td>
            </tr>
		<? while ($regCargos=mssql_fetch_array($cursorCargos)) { ?>
          <tr>
            <td width="3%">&nbsp;</td>
            <td width="1%">.</td>
            <td width="5%"><? echo $regCargos[cargos_adicionales]; ?></td>
            <td>[<? echo $regCargos[descripcion]; ?>]</td>
            </tr>
		<? } ?>
        </table>
		</td>
        <td width="20%">
		<? 
		echo "<B>Director: </B><br>" . ucwords(strtolower($reg[nombreD])) . " " . ucwords(strtolower($reg[apellidosD])) . "<br>" ;
		echo "<B>Coordinador: </B><br>" . ucwords(strtolower($reg[nombreC])) . " " . ucwords(strtolower($reg[apellidosC])) . "<br>"; 
		$DirectorNombre =  ucwords(strtolower($reg[nombreD])) . " " . ucwords(strtolower($reg[apellidosD]));
		$DirectorUnidad = $reg[id_director];
		?>
		<? 
		$oSql="select O.*, U.nombre, U.apellidos ";
		$oSql=$oSql." from GestiondeInformacionDigital.dbo.OrdenadorGasto O, HojaDeTiempo.dbo.Usuarios U  ";
		$oSql=$oSql." where O.id_proyecto =" . $reg[id_proyecto] ;
		$oSql=$oSql." and O.unidadOrdenador = U.unidad ";
		$oCursor = mssql_query($oSql);
		echo "<br><strong>Ordenadores</strong><br>" ;
		while ($oReg=mssql_fetch_array($oCursor)) {
			echo  ucwords(strtolower($oReg[nombre])) . " " . ucwords(strtolower($oReg[apellidos])) . "<br>";
		}
		?>		</td>
        <td width="20%" align="right">
		<?
		//Lista los programadores del proyecto
		$pSql="Select P.* , U.nombre, U.apellidos ";
		$pSql=$pSql." from programadores P, Usuarios U ";
		$pSql=$pSql." where P.unidad = U.unidad ";
		$pSql=$pSql." and P.id_proyecto = " . $reg[id_proyecto] ;
		$pSql=$pSql." and P.progProyecto = 1 ";
		$pCursor = mssql_query($pSql);
		?>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
		<? while ($pReg=mssql_fetch_array($pCursor)) { ?>
          <tr>
            <td align="left"><? echo ucwords(strtolower($pReg[apellidos])). ", " . ucwords(strtolower($pReg[nombre]))   ; ?></td>
            </tr>
		<? } ?>
        </table>				</td>
	  </tr>
	  <? } ?>
    </table>
		
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla">&nbsp;</td>
          </tr>
        </table>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="TxtTabla"><a href="htPlanProyectos.php" class="menu">&lt;&lt; Regresar a la Lista de Proyectos </a></td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
      <tr >
        <td width="15%" height="20" class="FichaInAct"><a href="htPlanProyectos01.php?cualProyecto=<? echo $cualProyecto; ?>" class="FichaInAct1" >EDT</a></td>
        <td width="15%" height="20" class="FichaInAct"><a href="htPlanProyectos02.php?cualProyecto=<? echo $cualProyecto; ?>" class="FichaInAct1" >Participantes</a></td>
        <td width="15%" height="20" class="FichaInAct"><a href="htPlanProyectos03.php?cualProyecto=<? echo $cualProyecto; ?>" class="FichaInAct1" >Planeaci&oacute;n</a></td>
        <td width="15%" height="20" class="FichaAct">Resumen</td>
        <td height="20" class="TxtTabla">&nbsp;</td>
      </tr>
      <tr>
        <td height="2" colspan="5" class="TituloUsuario"> </td>
        </tr>
    </table>
        <table  width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td class="TxtTabla" height="2" colspan="3"></td>
          </tr>
          <tr>
	        <td width="15%" height="20" class="FichaInAct"><a href="htPlanProyectos04.php?cualProyecto=<? echo $cualProyecto; ?>" class="FichaInAct1" >Valores</a>
    	    <td width="15%" height="20" class="FichaInAct" > <a href="htPlanProyectos04_planeacion.php?cualProyecto=<? echo $cualProyecto; ?>" class="FichaInAct1" >Planeaci&oacute;n </a></td>
	        <td width="15%" height="20" class="FichaAct">Planeaci&oacute;n vs Facturaci&oacute;n</td>
			 <td width="70%" height="20" class="TxtTabla">	</td>
          </tr>
          <tr>
            <td class="TxtTabla" height="2" colspan="3"></td>
          </tr>
		
		</table>
        <table  width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td  class="TituloUsuario" colspan="3" >.: TOTALES DEL PROYECTO</td>
          </tr>
        </table>
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td >
			<table width="100%"  border="0" cellspacing="0" cellpadding="0">
        
          <tr class="TxtTabla" >
            <td width="30%"  align="center"></td>
            <td width="20%"  align="center">&nbsp;</td>
            <td width="20%"  align="center">&nbsp;</td>
            <td width="30%"  align="center">&nbsp;</td>
          </tr>
          <tr class="TxtTabla" >
            <td width="30%"  align="center"></td>
            <td colspan="2" rowspan="2"  align="center"  bgcolor="#FFFFFF"  ><table width="100%" border="0" cellpadding="0" cellspacing="1">
                <tr>
                <td colspan="2" class="TituloUsuario" height="2" align="left" >Criterios de consulta </td>
                </tr>
     
<tr class="TxtTabla" >
            <td align="left" class="TituloTabla2">Tipo de Personal</td>
            <td align="left">

					<input type="text" class="CajaTexto" value="<? if($personal=='T'){ echo "Todos"; } 
					if($personal=='C'){ echo "Con planeación"; } 
					if($personal=='S'){ echo "Sin planeación"; } ?> " readonly="readonly" >
				</select>
			</td>
          </tr>
          <tr class="TxtTabla" >
            <td width="20%" align="right" class="TituloTabla2">Vigencia</td>
            <td align="left"><label for="vigencia"></label>
              
              
              <input type="text" class="CajaTexto"  value="<? echo $V; ?>" size="7" readonly="readonly" >
              
              </td>
          </tr>




            </table></td>
            <td width="30%" rowspan="3"  align="center">&nbsp;</td>
          </tr>
          <tr class="TxtTabla" >
            <td width="30%" rowspan="2"  align="center"></td>
          </tr>
          <tr class="TxtTabla" >
            <td colspan="2"  align="center" class="TxtTabla">&nbsp;</td>
          </tr>
          <tr class="TxtTabla" >


        	    <td colspan="4"  align="left"><a href="htPlanProyectos04_planeacionvsfact.php?cualProyecto=<?=$cualProyecto; ?>&Lote_control=<?=$LC ?>&Lote_trabajo=<?=$LT ?>&Division=<?=$DIV; ?>&Actividad=<?=$ACT ?>&lstVigencia=<?=$V; ?>&personal=<?=$personal; ?>" class="menu">&lt;&lt; Regresar a la planeaci&oacute;n</a></td>
        	    </tr>

  	      </table>
			</td>

          </tr>
        </table>
        <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="80%" colspan="8" align="right" class="TxtTabla">&nbsp;</td>
          </tr>
          <tr class="TxtTabla">
<td align="right"><img src="imagenes/ico11.gif" width="77" height="16">Planeado</td>
    <td align="right">&nbsp;</td>
    <td align="right"><img src="../portal/imagenes/ico1.gif" width="77" height="16">Excede Hombre/Mes la planeaci&oacute;n</td>
    <td align="right">&nbsp;</td>

    <td align="right"><img src="../portal/imagenes/eje100.gif" width="77" height="16">Facturado</td>
    <td align="right">&nbsp;</td>
    <td align="right"><img src="../portal/imagenes/eje1.gif" width="77" height="16">Excede Hombre/Mes la facturaci&oacute;n</td>
            <td>&nbsp;</td>
          </tr>
          
        </table>			
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">

          <tr>
            <td  align="left" class="TituloUsuario">.: Planeaci&oacute;n Actividad</td>
          </tr>


<?php
	$array_fecha= array();
	while($datos_act_pla=mssql_fetch_array($cur_act_pla))
	{

				//USUARIOS CON PLANEACION
				if($personal=='C')
				{
						$sql_parti=conPlaneacion_sinPlaneacion ($cualProyecto,$V,$datos_act_pla["id_actividad"],' IN ');			
				}
				
				//USUARIOS SIN PLANEACION, PERO CON FACTURACION 
				if($personal=='S')
				{
						$sql_parti=conPlaneacion_sinPlaneacion ($cualProyecto,$V,$datos_act_pla["id_actividad"],' NOT IN ');			
				}	
				//CONSULTA LA PERSONAS QUE TIENESN FACTURACION CON Y SIN PLANEACION
				if($personal=='T')
				{
						$sql_parti=" ( ";
						$sql_parti=$sql_parti.' '.conPlaneacion_sinPlaneacion ($cualProyecto,$V,$datos_act_pla["id_actividad"],' NOT IN ');		
						$sql_parti=$sql_parti.') UNION (';
						$sql_parti=$sql_parti.' '.conPlaneacion_sinPlaneacion ($cualProyecto,$V,$datos_act_pla["id_actividad"],' IN ');			
						$sql_parti=$sql_parti.' ) ';
				}					
//echo $sql_parti."<br><br>".mssql_get_last_message();
				$cur_parti=mssql_query($sql_parti);
							$ban=1;  //permite saber que mes del año se esta dibujando(1,2,3....,12)

?>
                      <tr class="TituloTabla2">
                        <td colspan="18" align="left" >
							<table width="100%">
								<tr>
									<td width="60%" class="TituloTabla">
								<? $ll=0; while( $ll<((int)$datos_act_pla["nivel"]) ){ echo " &nbsp;"; $ll++; }  echo $datos_act_pla[macroactividad]." ".$datos_act_pla[nombre]; // echo "(".$datos_act_pla["id_actividad"]." )"; ?>
									</td>

									<td width="10%" class="TituloTabla">Fecha Inicio</td> 
									<td width="10%" class="TxtTabla"><? echo date("M d Y ", strtotime ($datos_act_pla["fecha_inicio"])); ?></td>

									<td width="10%" class="TituloTabla">Fecha Finalización</td> <td width="10%" class="TxtTabla"><? echo date("M d Y ", strtotime ($datos_act_pla["fecha_fin"])); ?></td>
							  </tr>
							</table>
						</td>
                      </tr>

          <tr>
            <td colspan="4" align="right" class="TxtTabla"><table width="100%" border="0">
              <tr class="TituloTabla2">
                <td width="3%" rowspan="2">&nbsp;</td>
                <td width="5%" rowspan="2">Unidad</td>
                <td width="30%" rowspan="2">Participantes</td>
                <td width="5%" rowspan="2">&nbsp;</td>
                <td width="5%" rowspan="2">Total</td>
                <td colspan="13"><? echo $V; ?></td>


              </tr>
              <tr class="TituloTabla2">
                <td width="4%">Enero</td>
                <td width="4%"  >Febrero</td>
                <td width="4%"  >Marzo</td>
                <td width="4%" >Abril</td>
                <td width="4%"  >Mayo</td>
                <td width="4%"  >Junio</td>
                <td width="4%"  >Julio</td>
                <td width="4%"  >Agosto</td>
                <td width="4%"  >Septiembre</td>
                <td width="4%"  >Octubre</td>
                <td  >Noviembre</td>
                <td  >Diciembre</td>
                <td >Estado</td>
                </tr>
<?php
				while($datos_parti=mssql_fetch_array($cur_parti))
				{
					$ban=1;  //permite saber que mes del año se esta dibujando(1,2,3....,12)
?>
                      <tr class="TxtTabla">
                        <td width="3%"  align="center" ><? if(trim($datos_parti["fechaRetiro"])!="") { ?> <img src="imagenes/Inactivo.gif" title="Retirado de la compañia" /> <? } ?></td>
                        <td width="5%"  align="left"><?php echo $datos_parti["unidad"]; ?></td>
                        <td width="30%"  align="left"><? echo $datos_parti["nombre_apellido"]; ?></td>
  <td width="5%"  ><table width="100%" height="100%" border="0" >
                          <tr>
                            <td class="TituloTabla2"  height="100%">Planeaci&oacute;n</td>
                          </tr>

                          <tr>
                            <td class="TituloTabla2"  height="100%">Facturaci&oacute;n</td>
                          </tr>
                        </table></td>
						<td width="5%" ><table width="100%" border="0">
						  <tr>
						    <td class="TxtTabla"><?
						//SOLO SE EJECUTA LA CONSULTA DE PLANEACION, CUANDO SE HA SELECCIONADO EN "Tipo persona" C='con planeacion' T='Todo'
						if(($personal=='C')	|| ($personal=='T')	)
						{
							//TOTAL PLANEADO 

							$sql_total="select  SUM(hombresMes) as total_H_M_P from PlaneacionProyectos where id_proyecto=".$datos_parti["id_proyecto"]." and id_actividad=".$datos_parti["id_actividad"]." and vigencia=".$datos_parti["vigencia"]." and  unidad=".$datos_parti["unidad"]." and esInterno='".$datos_parti["esInterno"]."' ";
							$cur_total=mssql_query($sql_total);
							if($datos_total=mssql_fetch_array($cur_total))
								$total=$datos_total["total_H_M_P"];		
	
							echo $total;
							$total_perso=$total;
						}

?>
                        &nbsp;
                            </td>
					      </tr>

						  <tr>
						    <td class="TxtTabla">
<?
						//TOTAL FACTURADO
//						$total=0;
						$sql_total="select  SUM(hombresMesF) as total_H_M_F from FacturacionProyectos where id_proyecto=".$datos_parti["id_proyecto"]." and id_actividad=".$datos_parti["id_actividad"]." and vigencia=".$datos_parti["vigencia"]." and  unidad=".$datos_parti["unidad"]. " and esInterno='".$datos_parti["esInterno"]."' ";
						$cur_total=mssql_query($sql_total);
						if($datos_total=mssql_fetch_array($cur_total))
							$total=$datos_total["total_H_M_F"];		

						echo $total;
						$total_perso_fact=$total;


?>
								&nbsp;
							</td>
					      </tr>
					    </table></td>
<?

						$estado='';
						//CONSULTA LA INFORMACION DE LA PLANEACIÓN PARA LA PERSONAS INTERNAS
						if($datos_parti["esInterno"]=='I')
						{

							//CONSULTA DE LA FACTURACION, PARA EL PARTICIPANTE
							$sql_facturacion="select (select estado from ParticipantesActividad where id_proyecto=".$datos_parti["id_proyecto"]." and id_actividad=".$datos_parti["id_actividad"]." and unidad=".$datos_parti["unidad"]. ") estado
							, mes , esInterno, sum(hombresMesF)hombresMesF, sum(hombresMesF)total_H_M_F ,vigencia  from FacturacionProyectos
							 where id_proyecto=".$datos_parti["id_proyecto"]." and id_actividad=".$datos_parti["id_actividad"]." and vigencia=".$datos_parti["vigencia"]." and unidad=".$datos_parti["unidad"]. " and esInterno='I' group by mes, esInterno,vigencia ORDER BY(mes)";


/*
							//CONSULTA DE LA FACTURACION, PARA EL PARTICIPANTE
							$sql_facturacion="select (select estado from ParticipantesActividad where id_proyecto=".$datos_parti["id_proyecto"]." and id_actividad=".$datos_parti["id_actividad"]." and unidad=".$datos_parti["unidad"].") estado 
								,  SUM(hombresMesF) total_H_M_F , mes , esInterno, hombresMesF from FacturacionProyectos where id_proyecto=".$datos_parti["id_proyecto"]." and id_actividad=".$datos_parti["id_actividad"]." and vigencia=".$datos_parti["vigencia"]." and unidad=".$datos_parti["unidad"]." 
								and esInterno='I'
								group by mes, esInterno,hombresMesF
								ORDER BY(mes)";
*/
							//CONSULTA DE LA PLANEACION, PARA EL PARTICIPANTE

							$sql_planeacion="select
								(select estado from ParticipantesActividad where id_proyecto=".$datos_parti["id_proyecto"]." and id_actividad=".$datos_parti["id_actividad"]." and unidad=".$datos_parti["unidad"].") estado ,
							 (select  SUM(hombresMes) as total_H_M from PlaneacionProyectos where id_proyecto=".$datos_parti["id_proyecto"]." and id_actividad=".$datos_parti["id_actividad"]." and vigencia=".$datos_parti["vigencia"]." and  unidad=".$datos_parti["unidad"]." and esInterno='I') as total_H_M ,* from PlaneacionProyectos where id_proyecto=".$datos_parti["id_proyecto"]." and id_actividad=".$datos_parti["id_actividad"]." and vigencia=".$datos_parti["vigencia"]." and  unidad=".$datos_parti["unidad"]." and esInterno='I' ORDER BY(mes)";
//echo $sql_planeacion."<br>";

						}

						//CONSULTA LA INFORMACION DE LA PLANEACIÓN PARA LA PERSONAS EXTERNAS
						if($datos_parti["esInterno"]=='E')
						{
							//CONSULTA DE LA FACTURACION, PARA EL PARTICIPANTE
/*
							$sql_facturacion="select (select estado from ParticipantesExternos where id_proyecto=".$datos_parti["id_proyecto"]." and id_actividad=".$datos_parti["id_actividad"]." and consecutivo=".$datos_parti["unidad"].") estado 
								,  SUM(hombresMesF) total_H_M_F , mes , esInterno, hombresMesF from FacturacionProyectos where id_proyecto=".$datos_parti["id_proyecto"]." and id_actividad=".$datos_parti["id_actividad"]." and vigencia=".$datos_parti["vigencia"]." and unidad=".$datos_parti["unidad"]." 
								and esInterno='E'
								group by mes, esInterno,hombresMesF
								ORDER BY(mes)";
*/
							$sql_facturacion="select estado,mes,esInterno,sum(total_H_M_F)total_H_M_F, sum( hombresMesF ) hombresMesF from (
								select 
								(select estado from ParticipantesExternos where id_proyecto=".$datos_parti["id_proyecto"]." and id_actividad=".$datos_parti["id_actividad"]." and consecutivo=1) estado , SUM(hombresMesF) total_H_M_F , mes ,
								 esInterno, hombresMesF from
								  FacturacionProyectos where id_proyecto=".$datos_parti["id_proyecto"]." and id_actividad=".$datos_parti["id_actividad"]." and vigencia=".$datos_parti["vigencia"]." and unidad=".$datos_parti["unidad"]." and esInterno='E' 
								  group by mes, esInterno,hombresMesF 	  
								) facturado_esterno_X_mes
								group by mes, esInterno,estado
								ORDER BY(mes)";


							//CONSULTA DE LA PLANEACION, PARA EL PARTICIPANTE
							$sql_planeacion="select
								(select estado from ParticipantesExternos where id_proyecto=".$datos_parti["id_proyecto"]." and id_actividad=".$datos_parti["id_actividad"]." and consecutivo=".$datos_parti["unidad"].") estado ,
							 (select  SUM(hombresMes) as total_H_M from PlaneacionProyectos where id_proyecto=".$datos_parti["id_proyecto"]." and id_actividad=".$datos_parti["id_actividad"]." and vigencia=".$datos_parti["vigencia"]." and  unidad=".$datos_parti["unidad"]." and esInterno='E') as total_H_M ,* from PlaneacionProyectos where id_proyecto=".$datos_parti["id_proyecto"]." and id_actividad=".$datos_parti["id_actividad"]." and vigencia=".$datos_parti["vigencia"]." and  unidad=".$datos_parti["unidad"]." and esInterno='E' ORDER BY(mes)";
/*
							$sql_planeacion="select
								(select estado from ParticipantesExternos where id_proyecto=".$datos_parti["id_proyecto"]." and id_actividad=".$datos_parti["id_actividad"]." and consecutivo=".$datos_parti["unidad"].") estado ,
							 (select  SUM(hombresMes) as total_H_M from PlaneacionProyectos where id_proyecto=".$datos_parti["id_proyecto"]." and id_actividad=".$datos_parti["id_actividad"]." and vigencia=".$datos_parti["vigencia"]." and  unidad=".$datos_parti["unidad"]." and esInterno='E') as total_H_M ,* from PlaneacionProyectos where id_proyecto=".$datos_parti["id_proyecto"]." and id_actividad=".$datos_parti["id_actividad"]." and vigencia=".$datos_parti["vigencia"]." and  unidad=".$datos_parti["unidad"]." and esInterno='E' ORDER BY(mes)";
*/
						}
//echo $sql_planeacion."<br>";
//echo $sql_facturacion."<br><br>";
						$cur_facturacion=mssql_query($sql_facturacion);
						$cur_planeacion=mssql_query($sql_planeacion);
//echo $sql_planeacion."<br>";
		
						//VERIFICA, QUE CURSOR, TRAJO MAS REGISTROS. cON EL FIN DE DEFINIR, MAX CUANTOS CILOS SE REALIZARAN
						if(mssql_num_rows($cur_facturacion)< mssql_num_rows($cur_planeacion) )
								$can_cilos=mssql_num_rows($cur_planeacion);

						if(mssql_num_rows($cur_planeacion)< mssql_num_rows($cur_facturacion) )
								$can_cilos=mssql_num_rows($cur_facturacion);
//echo $sql_facturacion." ... ".mssql_get_last_message()."<br>";

						//////	VALIDA SI LOS MESES DE LA VIGENCIA, ESTAN, ENTRE LA FECHA DE INICIO O FINALIZACION DE LA ACTIVIADAD
						//SI LA VIGENCIA ES IGUAL AL AÑO DE INICIO DE LA ACTIVIDAD

	
						list ($ano_i,$mes_i,$dia_i,$fecha_i,$ano_f,$mes_f,$dia_f,$fecha_f)=fecha_ini_finaliza_activi($cualProyecto,$datos_parti["id_actividad"]);
//echo " 22 --" . $mes_i ." -- 2222 ".$dia_f." <br>";

//	return($array($ano_i,$mes_i,$dia_i,$fecha_i,$ano_f,$mes_f,$dia_f,$fecha_f));

//	return($array($ano_i,$,$,$,$,$mes_f,$dia_f,$fecha_f));

						if($V==$ano_i)
						{
							//SI EL MES  ($ban) ES INFERIOR A LA EL MES DE INICIO DE LA ACTIVIDAD
							while($ban<$mes_i)
							{
								$aplica='class="TituloTabla2"';		
								echo '<td   >
										  <table width="100%"     >
											<tr    > 
												<td width="100%"   '.$aplica.' >													
													&nbsp;
												</td>
											</tr>
											<tr>
												<td width="100%"   '.$aplica.' >
														&nbsp;
												</td>
											</tr>
										</table>


								</td>';
								$ban++;

							}
						}
						//SI LA VIGENCIA ES IGUAL AL AÑO DE FINALIZACION DE LA ACTIVIDAD
						if($V==$ano_f)
						{
							//SI EL MES banES SUPERIOR A LA EL MES DE FINALIZACION DE LA ACTIVIDAD
							while($mes_f<$ban)
							{
								$aplica='class="TituloTabla2"';		
								echo '<td >
										  <table width="100%"   >
											<tr    > 
												<td width="100%" '.$aplica.' >	1												
													&nbsp;
												</td>
											</tr>

										</table>
										  <table width="100%"   >
											<tr>
												<td width="100%"  '.$aplica.' >
														&nbsp;
												</td>
											</tr>
											</table>

								</td>';
								$ban++;
							}
						}

						$datos_planeacion=mssql_fetch_array($cur_planeacion);
						$datos_facturacion=mssql_fetch_array($cur_facturacion);

						if($datos_facturacion[estado]!='')
							$estado=$datos_facturacion[estado];
						else
							$estado=$datos_planeacion[estado];

						if(trim($datos_planeacion[mes])=="")
							$datos_planeacion[mes]=$datos_facturacion[mes];

						//CONSULTAMOS, CUAL DE LOS MESES ES EL MENOR ENTRE LA (FACTURACION Y LA PLANEACION)
						if($datos_planeacion[mes]<$datos_facturacion[mes])
							$min_mes_plane_factu=$datos_planeacion[mes];
						else
							$min_mes_plane_factu=$datos_facturacion[mes];

						//IGUALAMOS $ban AL MINIMO MES DE LA PLANEACION O FACTURACION
						//Y SE IMPRIMEN LOS ESPACIOS EN VACIO
						while($ban<$min_mes_plane_factu)
						{
								echo'	<td >	  <table width="100%"   >
											<tr>
												<td width="100%" >
														&nbsp;
												</td>
											</tr>
											<tr>
												<td width="100%" >
														&nbsp;
												</td>
											</tr>
											</table> </td >';
							$ban++;
						}

						mssql_data_seek($cur_planeacion,0);
						mssql_data_seek($cur_facturacion,0);
//echo mssql_num_rows($cur_planeacion)." --- can reg fac ".mssql_num_rows($cur_facturacion)." <br>";


							$datos_planeacion[mes]==0;
							$datos_facturacion[mes]==0;
						$imprimio_factu='si';
						$imprimio_planea='si';
			
						while($ban<=12)
						{
							if($imprimio_planea=='si')
							{
								if(mssql_num_rows($cur_planeacion)!=0)
								{
									$datos_planeacion=mssql_fetch_array($cur_planeacion);
//	echo " <br> ban: ".$ban." unidad: ".$datos_planeacion[unidad]."planeacion mes: ". $datos_planeacion[mes]." <br>";
									$imprimio_planea='no';
								}
								else
									$datos_planeacion[mes]=13;
							}

							if($imprimio_factu=='si')
							{
								if(mssql_num_rows($cur_facturacion)!=0)
								{
									$datos_facturacion=mssql_fetch_array($cur_facturacion);
//	echo "ban: ".$ban."unidad: ".$datos_facturacion[unidad]." facturacion mes: ". $datos_facturacion[mes]." <br>";
									$imprimio_factu='no';
								}
								else
									$datos_facturacion[mes]=13;
							}

//echo " <BR><BR> mMESSSS PLANEADO ".$datos_planeacion[mes]." ---- MES FACTURADO  ".$datos_facturacion[mes]." <BR><BR>";

							if (($ban==$datos_planeacion[mes]) and ($ban==$datos_facturacion[mes]))
							{
								$imprimio_factu='si';
								$imprimio_planea='si';

								imprime_planea_factura($datos_planeacion["hombresMes"],$datos_facturacion["total_H_M_F"],1);
							}
							else if (($ban<$datos_planeacion[mes]) and ($ban==$datos_facturacion[mes]))
							{
								$imprimio_factu='si';
								$imprimio_planea='no';


								imprime_planea_factura('&nbsp;',$datos_facturacion["total_H_M_F"],2);
							}
							else if (($ban==$datos_planeacion[mes]) and ($ban<$datos_facturacion[mes]))
							{
								$imprimio_factu='no';
								$imprimio_planea='si';

								imprime_planea_factura($datos_planeacion["hombresMes"],'&nbsp;',3);
							}


							else if (($ban==$datos_planeacion[mes]) and ($datos_facturacion[mes]<$ban))
							{
								$imprimio_factu='no';
								$imprimio_planea='si';
//echo "***** 4444 <br>";
								imprime_planea_factura($datos_planeacion["hombresMes"],'&nbsp;',3);
							}

							else if (($datos_planeacion[mes]<$ban) and ($ban==$datos_facturacion[mes]))
							{
								$imprimio_factu='si';
								$imprimio_planea='no';
//echo "***** 2 <br>";

								imprime_planea_factura('&nbsp;',$datos_facturacion["total_H_M_F"],2);
							}


							else
							{
									if (($ban<$datos_planeacion[mes]) and ($ban<$datos_facturacion[mes]))
									{
										$imprimio_factu='no';
										$imprimio_planea='no';
										imprime_planea_factura('&nbsp;','&nbsp;',4);			
									}
									else
									{

											//SI LA VIGENCIA ES IGUAL AL AÑO DE FINALIZACION DE LA ACTIVIDAD
											if($V==$ano_f)
											{
												$aplica='';
												//SI EL MES $ban ES SUPERIOR A LA EL MES DE FINALIZACION DE LA ACTIVIDAD
												if($mes_f<$ban)
												{
													$aplica='class="TituloTabla2"';		
													echo '<td >
															  <table width="100%"   >
																<tr    > 
																	<td width="100%" '.$aplica.' >													
																		&nbsp;
																	</td>
																</tr>
					
															</table>
															  <table width="100%"   >
																<tr>
																	<td width="100%"  '.$aplica.' >
																			&nbsp;
																	</td>
																</tr>
																</table>
					
													</td>';
												}
												else
													echo "<td></td>";
											}
											else
												echo "<td></td>";
									}
							}
							$ban++;
						}

//echo $ban." ***<br><br>";
/*
						while($ban<13)
						{
							echo "<td>1</td>";
							$ban++;
						}						
*/
?>	
		<!--	PARA EL ESTADO DEL USUARIO, EN LAS ACTIVIDADES -->
                <td align="center" >
<?
				if (trim($estado) == "A") { ?>
				<img title="Activo" src="img/images/alertaAzul.gif"  width="15" height="13" />
				<?
				}

				if (trim($estado) == "I") {
				?>
				<img src="img/images/alertaRojo.gif" title="Inactivo" width="15" height="13" />
				<? }



?>
				 </td>
           	    </tr>

                <tr>

                </tr>
                <tr>
                    <td colspan="17">
						<table width="100%" border="0" cellpadding="0" cellspacing="0" class="TituloTabla2">
                      <tr>
                        <td></td>
                        </tr>
                      </table></td>
                </tr>

<?
				}
?>
                      <tr class="TituloUsuario">
                        <td colspan="18" align="left" class="TituloUsuario"> </td>
                      </tr>
                      <tr class="TxtTabla">
                        <td colspan="18" align="left" class="TxtTabla">&nbsp; </td>
                      </tr>

            </table></td>
          </tr>
<?
	}
?>
          <tr>
            <td colspan="4" align="right" class="TxtTabla">&nbsp; <input type="hidden" name="recarga" value="0" id="recarga" /> </td>
          </tr>
        </table>
        </td>
      </tr>
	  </form >
    </table>
	<table>
		<tr>
            <td align="right" class="TxtTabla">&nbsp; </td>
          </tr>
		<tr>
            <td align="right" class="TxtTabla">&nbsp; </td>
          </tr>
	</table>


</body>
</html>
