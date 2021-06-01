<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}

function ismaxlength(obj){
var mlength=obj.getAttribute? parseInt(obj.getAttribute("maxlength")) : ""
if (obj.getAttribute && obj.value.length>mlength)
obj.value=obj.value.substring(0,mlength)
}

function cerrar()
{
	window.close();
//MM_openBrWindow('htFacturacion.php?cualMes=<? //=$cualMes ?>','winFacturacionHT','toolbar=yes,scrollbars=yes,resizable=yes,width=960,height=700');
}


//-->
</script>
<?php
session_start();

//Establecer la conexión a la base de datos
//$conexion = conectar();
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";


//--Trae los proyectos en los que una persona tiene facturación agrupada así:
//--Proyecto, Actividad, Horario, clase de tiempo, localización, cargo
$sql02="SELECT DISTINCT A.id_proyecto, A.id_actividad, A.unidad, A.vigencia, A.mes, A.clase_tiempo, A.localizacion, A.cargo, A.IDhorario,  ";
$sql02=$sql02." B.nombre, B.codigo, B.cargo_defecto, C.nombre nomActividad, C.macroactividad, D.descripcion " ;
$sql02=$sql02." FROM FacturacionProyectos A, Proyectos B, Actividades C, Clase_Tiempo D " ;
$sql02=$sql02." WHERE A.id_proyecto = B.id_proyecto " ;
$sql02=$sql02." AND A.id_proyecto = C.id_proyecto " ;
$sql02=$sql02." AND A.id_actividad = C.id_actividad " ;
$sql02=$sql02." AND A.clase_tiempo = D.clase_tiempo " ;
$sql02=$sql02." AND A.unidad = " . $laUnidad ;
$sql02=$sql02." AND A.mes = " . $cualMes ;
$sql02=$sql02." AND A.vigencia = " . $cualVigencia." and esInterno='I' " ;
$sql02=$sql02." GROUP BY A.id_proyecto, A.id_actividad, A.unidad, A.vigencia, A.mes, A.clase_tiempo, A.localizacion, A.cargo, A.IDhorario,  " ;
$sql02=$sql02." B.nombre, B.codigo, B.cargo_defecto, C.nombre, C.macroactividad, D.descripcion " ;
$sql02=$sql02." ORDER BY B.nombre " ;
$cursor02 =	 mssql_query($sql02);

$totalDiasMes = 0;
if ($cualMes<10) {
	$cantElMes = "0" . $cualMes;
}
else {
	$cantElMes = "" . $cualMes;
}
$sql04="select  day(dateadd(d,-1,dateadd(m,1,convert(datetime, '".$cualVigencia."' + '".$cantElMes."' + '01')))) diasDelMes ";
$cursor04 =	 mssql_query($sql04);
if ($reg04 = mssql_fetch_array($cursor04)) {
	$totalDiasMes =  $reg04['diasDelMes'];
}

		if($recarga==2)
		{
			$error="no";
			mssql_query("BEGIN TRANSACTION");
		
					$sql_update="
						 delete FacturacionProyectos
						  WHERE FacturacionProyectos.unidad = ".$laUnidad." AND mes =  ".$cualMes." AND vigencia = ".$cualVigencia."  and year(fechaFacturacion)=".$cualVigencia."  and month(fechaFacturacion)=".$cualMes." and esInterno='I' ";
					$cur_update=mssql_query($sql_update);

//echo $sql_update."<br>".mssql_get_last_message()."<br><br>";

					if(trim($cur_update)=="")
					{
						$error="si";
						break;
					}
			

			if(trim($error)=="no")
			{
//				mssql_query(" ROLLBACK TRANSACTION");
				mssql_query(" COMMIT TRANSACTION");
				echo "<script>alert('La operaci\xf3n se realiz\xf3 con \xe9xito')</script>";
			}
			else
			{
				mssql_query(" ROLLBACK TRANSACTION");
				echo "<script>alert('Error en la operaci\xf3n')</script>";
			}
			echo "	<script>window.close();MM_openBrWindow('htFacturacion.php?pMes=".$cualMes."&pAno=".$cualVigencia."','winFacturacionHT','toolbar=yes,scrollbars=yes,resizable=yes,width=960,height=700');</script>";

		}

?>


<html>
<head>

<title>.:: Hoja de tiempo - Facturaci&oacute;n de Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">

<script language="javascript" type="text/javascript">

	function valida(mes,ano)
	{
		if(confirm('Desea eliminar toda la facturaci\xf3n para el mes de '+mes+' del '+ano+'?'))
		{
			document.Form.recarga.value = 2;
			document.Form.submit();
		}
	}
</script>

</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">

<form name="Form" id="Form" action="" method="post" >
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">.:: Informaci&oacute;n del usuario</td>

  </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0" >
  <tr>
    <td>  
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr>
<?
	$cur_usu=mssql_query("select unidad,UPPER( nombre) nombre,UPPER( apellidos) apellidos from Usuarios where unidad=".$laUnidad);
	$datos_usu=mssql_fetch_array($cur_usu);
?>
          <td class="TituloTabla" >Unidad</td>
          <td class="TxtTabla" ><?=$datos_usu["unidad"]; ?></td>
        </tr>
        <tr>
          <td class="TituloTabla" width="7%" >Nombre</td>
          <td  class="TxtTabla" ><?=$datos_usu["nombre"]." ".$datos_usu["apellidos"]; ?>
		  </td>
        </tr>
        <tr>
          <td colspan="5" class="TxtTabla">&nbsp;</td>
        </tr>
        <tr>
        <td colspan="4" class="TituloUsuario">.:: Informaci&oacute;n de la facturaci&oacute;n</td>

    
      </tr>
        <tr>

			<tr>
              <td class="TituloTabla2">Vigencia</td>
              <td align="left" class="TxtTabla"><?=$cualVigencia ?></td>
            </tr>

			<tr>
              <td class="TituloTabla2">Mes</td>
<?
$mes = array( '', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre' );
?>
              <td align="left" class="TxtTabla"><?=$mes[$cualMes] ?></td>
            </tr>
          
          </table></td>

    </tr>
        <tr>
          <td colspan="5"  class="TxtTabla">&nbsp;</td>
        </tr>

        <tr>
          <td colspan="5"  class="TxtTabla"><!-- readonly-->
<?
//********************************************
?>
          <table width="100%"  border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td class="TituloUsuario">.:: Facturaci&oacute;n reportada </td>
            </tr>
          </table>
            <table width="100%"  border="0" cellspacing="1" cellpadding="0">
                        <tr class="TituloTabla2">
                          <td width="10%">Proyecto</td>
                          <td width="10%">Actividad</td>
                          <td width="8%">Horario</td>
                          <td width="1%">Loc.</td>
                          <td width="1%">CT</td>
                          <td width="1%">Cargo</td>
                          <?
                          //25Jul2013
                          //PBM
                          //Genera los dís del mes
                          for ($d=1; $d<=$totalDiasMes; $d++) {
                          ?>
                          <td width="1%"><? echo $d; ?></td>
                          <?
                          } //for d
                          ?>
                          <td>Total</td>
                          <td>VoBo</td>
                          <td>Resumen</td>

              </tr>
                      <?
                      while ($reg02 = mssql_fetch_array($cursor02)) {
                      ?>
                        <tr >
                          <td width="10%" class="TxtTabla" ><? echo "<B>[" . $reg02['codigo'] . "." . $reg02['cargo_defecto'] . "]</B> " . strtoupper($reg02['nombre']) ; ?></td>
                          <td width="10%" class="TxtTabla" ><? echo "<B>[" . $reg02['macroactividad'] . "]</B> " . strtoupper($reg02['nomActividad'])  ; ?></td>
                          <td width="8%" class="TxtTabla" >
                          <? 
                          //Trae el Horario de lines a domingo
                          $cpHorario="";
                          $sql03="SELECT * FROM Horarios ";
                          $sql03=$sql03." WHERE IDhorario = " .$reg02['IDhorario'];
                          $cursor03 =	 mssql_query($sql03);
                          if ($reg03 = mssql_fetch_array($cursor03)) {
                            $cpHorario="[". $reg03['Lunes'] . "-" . $reg03['Martes'] . "-" . $reg03['Miercoles'] . "-" . $reg03['Jueves'] . "-" . $reg03['Viernes'] . "-" . $reg03['Sabado'] . "-" . $reg03['Domingo'] . "] " ;
                          }
                          echo $cpHorario; 
                          ?>			  </td>
                          <td width="1%" align="center" class="TxtTabla"><? echo $reg02['localizacion']; ?></td>
                          <td width="1%" align="center" class="TxtTabla"><? echo trim(substr($reg02['descripcion'], 0, 2));  ?></td>
                          <td width="1%" align="center" class="TxtTabla"><? echo $reg02['cargo']; ?></td>
                          <?
                          //25Jul2013
                          //PBM
                          //Genera los dís del mes 
                          $totalHorasRegistro = 0; //Para calcular la cantidad de horas totales por registro
                          $totalResumenRegistro = ""; //Para relacionar el resumen de todos los días con facturación
                          for ($d2=1; $d2<=$totalDiasMes; $d2++) {
                          
                            //--Determina si el día es sábado, domingo, festivo o dia normal
                            //--Domingo=1, Lunes = 2..., Sabado=7
                            $fechaAconsultar=$cualVigencia."-".$cualMes."-".$d2;
                            $esFestivo=0;
                            $esDia=0;
                            $usarClase="";
                            $sql05 = "SELECT COUNT(*) as hayFestivo , DATEPART ( dw , '".$fechaAconsultar."' ) diaSemana";
                            $sql05 = $sql05 . " FROM Festivos ";
                            $sql05 = $sql05 . " where fecha = '". $fechaAconsultar ."' ";
                            $cursor05 =	 mssql_query($sql05);
                            if ($reg05 = mssql_fetch_array($cursor05)) {
                                $esFestivo=$reg05['hayFestivo'];
                                $esDia=$reg05['diaSemana'];
                            }
                            
                            //Es festivo
                            if ( (($esDia >=2) AND ($esDia <=6)) AND ($esFestivo > 0) ) {
                                $usarClase="tdFestivo";
                            }
                            
                            //Es dia Normal
                            if ( (($esDia >=2) AND ($esDia <=6)) AND ($esFestivo == 0) ) {
                                $usarClase="TxtTabla";
                            }
                            
                            //Es sábado o domingo
                            if ( ($esDia == 1) OR ($esDia ==7) ) {
                                $usarClase="tdFinSemana";
                            }
                            
                            //Trae la cantidad de horas para el día en el proyecto, actividad, Horario, localización, clase de tiempo, cargo y dia definido.
                            //--Trae la facturación de una persona para un mes y año específicos
                            //--id_proyecto, id_actividad, unidad, vigencia, mes, esInterno, fechaFacturacion, IDhorario, clase_tiempo, localizacion, cargo, hombresMesF, horasMesF, 
                            //--resumen, id_categoria, valorFacturado, salarioBase, tipoContrato, usuarioCrea, fechaCrea, usuarioMod, fechaMod
                            $horasDia=0;
                            $sql06="SELECT *  ";
                            $sql06=$sql06." FROM FacturacionProyectos ";
                            $sql06=$sql06." WHERE unidad = " . $laUnidad ;
                            $sql06=$sql06." AND mes = " . $cualMes ;
                            $sql06=$sql06." AND vigencia = " . $cualVigencia ;
                            $sql06=$sql06." AND id_proyecto = " . $reg02['id_proyecto'] ;
                            $sql06=$sql06." AND id_actividad = " . $reg02['id_actividad'] ;
                            $sql06=$sql06." AND DAY(fechaFacturacion) = " . $d2 ;
                            $sql06=$sql06." AND IDhorario = " . $reg02['IDhorario'] ;
                            $sql06=$sql06." AND clase_tiempo = " . $reg02['clase_tiempo'] ;
                            $sql06=$sql06." AND localizacion = " . $reg02['localizacion'] ;
                            $sql06=$sql06." AND cargo = '" . $reg02['cargo'] . "' and esInterno='I' ";
                            $cursor06 =	 mssql_query($sql06);
                            if ($reg06 = mssql_fetch_array($cursor06)) {
                                $horasDia=$reg06['horasMesF'];
            
                                //Totaliza por registro
                                $totalHorasRegistro = $totalHorasRegistro + $horasDia ;
                                
                                //Resumen total por registro
                                $totalResumenRegistro = $totalResumenRegistro . "<br>". $reg06['resumen'] ; 
                            }
                          ?>
                          <td width="1%" align="right" class="<? echo $usarClase; ?>">
                          <?
                            if ($horasDia > 0) {
                                echo number_format($horasDia, 0, ",", ".");
                            }
                          ?>
                          </td>
                          <?
                          } //cierra for $d2
                          ?>
                          <td align="right" class="TxtTabla">
                          <?
                            if ($totalHorasRegistro > 0) {
                                echo number_format($totalHorasRegistro, 0, ",", ".");
                            }
                          ?>
                          </td>
                          <td class="TxtTabla">y</td>
                          <td class="TxtTabla">
                          <?
            //  			if (trim($totalResumenRegistro) != "") {
            //					echo $totalResumenRegistro;
            //				}
            
                          //--Trae el resumen de un proyecto SIN REPETIR una persona para una actividad, un mes y año específicos			  
                          $sql08="SELECT DISTINCT resumen  ";
                          $sql08=$sql08." FROM FacturacionProyectos ";
                          $sql08=$sql08." WHERE unidad = " . $laUnidad ;
                          $sql08=$sql08." AND mes = " . $cualMes ;
                          $sql08=$sql08." AND vigencia =" . $cualVigencia ;
                          $sql08=$sql08." AND id_proyecto = " . $reg02['id_proyecto'] ;
                          $sql08=$sql08." AND id_actividad = " . $reg02['id_actividad'] ;
                          $sql08=$sql08." AND IDhorario = " . $reg02['IDhorario'] ;
                          $sql08=$sql08." AND clase_tiempo = " . $reg02['clase_tiempo'] ;
                          $sql08=$sql08." AND localizacion = " . $reg02['localizacion'] ;
                          $sql08=$sql08." AND cargo = '". $reg02['cargo'] ."' and esInterno='I' ";
                          $cursor08 =	 mssql_query($sql08);
                          while ($reg08 = mssql_fetch_array($cursor08)) {
                                echo $reg08['resumen'] . "<br>";
                            }
                          
                           
                          
                          ?>
                          </td>
                        
                        </tr>
                        <? 
                        } //while $reg02
                        ?>
                        <tr class="TituloTabla2" >
                          <td colspan="6" class="TituloTabla2" >TOTAL CLASES DE TIEMPO 1 - 2 - 3 Y 11 </td>
                          <?
                          $totalHorasMensual=0;
                          for ($d3=1; $d3<=$totalDiasMes; $d3++) {
                          
                            //--Determina si el día es sábado, domingo, festivo o dia normal
                            //--Domingo=1, Lunes = 2..., Sabado=7
                            $fechaAconsultar=$cualVigencia."-".$cualMes."-".$d3;
                            $esFestivo=0;
                            $esDia=0;
                            $usarClase="";
                            $sql05 = "SELECT COUNT(*) as hayFestivo , DATEPART ( dw , '".$fechaAconsultar."' ) diaSemana";
                            $sql05 = $sql05 . " FROM Festivos ";
                            $sql05 = $sql05 . " where fecha = '". $fechaAconsultar ."' ";
                            $cursor05 =	 mssql_query($sql05);
                            if ($reg05 = mssql_fetch_array($cursor05)) {
                                $esFestivo=$reg05['hayFestivo'];
                                $esDia=$reg05['diaSemana'];
                            }
                            
                            //Es festivo
                            if ( (($esDia >=2) AND ($esDia <=6)) AND ($esFestivo > 0) ) {
                                $usarClase="tdFestivo";
                            }
                            
                            //Es dia Normal
                            if ( (($esDia >=2) AND ($esDia <=6)) AND ($esFestivo == 0) ) {
                                $usarClase="TituloTabla2";
                            }
                            
                            //Es sábado o domingo
                            if ( ($esDia == 1) OR ($esDia ==7) ) {
                                $usarClase="tdFinSemana";
                            }
            
                            //--Totaliza por día para clases de tiempo 1, 2, 3 y 11
                            $totalDiario=0;
                            $sql07="SELECT SUM(horasMesF) totDia  ";
                            $sql07=$sql07." FROM FacturacionProyectos ";
                            $sql07=$sql07." WHERE unidad =" . $laUnidad ;
                            $sql07=$sql07." AND mes = " . $cualMes ;
                            $sql07=$sql07." AND vigencia = " . $cualVigencia ;
                            $sql07=$sql07." AND DAY(fechaFacturacion) = " . $d3 ;
                            $sql07=$sql07." AND clase_tiempo IN (1, 2, 3, 11) and esInterno='I' ";
                            $cursor07 =	 mssql_query($sql07);
                            if ($reg07 = mssql_fetch_array($cursor07)) {
                                $totalDiario=$reg07['totDia'];
                                
                                //Totaliza la sumatoria de todos los resultados para clase de tiempo 1, 2, 3 y 11
                                $totalHorasMensual=$totalHorasMensual+$totalDiario;
                            }
            
                          ?>
                          <td align="right" class="<? echo $usarClase; ?>">
                          <?
                            if ($totalDiario > 0) {
                                echo number_format($totalDiario, 0, ",", ".");
                            }
                          ?>
                          </td>
                          <?
                          } //Cierra el for d3
                          ?>
                          <td align="right" class="TituloTabla2">
                          <?
                            if ($totalHorasMensual > 0) {
                                echo number_format($totalHorasMensual, 0, ",", ".");
                            }
                          ?>
                          </td>
                          <td class="TituloTabla2">&nbsp;</td>
                          <td class="TituloTabla2">&nbsp;</td>
                        </tr>
                      </table>
<?
//*********************************************
?>
		  </td>
    </tr>

	

      </table>



<table width="100%"  border="0" cellspacing="1" cellpadding="0">


  <tr>
    <td align="right" class="TxtTabla">&nbsp;</td>
  </tr>
  <tr>
    <td align="center" class="TxtTabla"><strong>Atenci&oacute;n: Esta operaci&oacute;n es irreversible. No podra recuperar la informaci&oacute;n de la vigencia y mes seleccionado.</strong></td>
  </tr>
  <tr>
    <td align="center" class="TxtTabla"><strong>&#191;Esta seguro de eliminar toda la informaci&oacute;n asociada 
      en la hoja de tiempo para el mes de <?=$mes[$cualMes] ?>?</strong></td>
  </tr>
  <tr>
          <td align="center" class="TxtTabla"><input type="button" class="Boton" value="Cancelar" onClick="cerrar()" > &nbsp;
<?
			//PENDIENTE
			///****** INCLUIR VALIDACIONES DEL BOTON, RELACIONADOS CON EL VOBO DEL JEFE INMEDIATO, CONTRATOS, Y PROYECTO
			//CUANDO TENGA ALUNA DE ESTAS FIRMAS, NO DEBE MOSTRAR EL BOTON, Y MANTENDRA INABILITADOS LAS AREAS DE TEXTO
?>
          <input name="guardar" type="button" class="Boton" id="guardar" value="Eliminar" onClick="valida('<?=$mes[$cualMes] ?>','<?=$cualVigencia ?>')" >
<?
			//PENDIENTE
?>
			<input type="hidden" name="cont" id="cont" value="<?=$cont; ?>" >
			<input type="hidden" name="recarga" id="recarga" value="1" >
			</td>
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
