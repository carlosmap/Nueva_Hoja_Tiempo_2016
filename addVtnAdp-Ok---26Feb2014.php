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
//MM_openBrWindow('htFacturacion.php?pMes=<? //=$cualMes ?>','winFacturacionHT','toolbar=yes,scrollbars=yes,resizable=yes,width=960,height=700');
}


//-->
</script>
<?php
session_start();
//include("../verificaRegistro2.php");
//include('../conectaBD.php');

//Establecer la conexi?n a la base de datos
//$conexion = conectar();
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

		if($recarga==2)
		{
			mssql_query("BEGIN TRANSACTION");
			#$fch1 = '2014-02-15';
			#$fch2 = '2014-02-25';
			$fInicia = date( 'Y-m-d', strtotime( $fchInicial ) );
			$fFinal = date( 'Y-m-d', strtotime( $fchFinal ) );
			$error=0;

			$sqlFchIni='SELECT MAX(fechafinal) fch FROM AdpHT ';
			$sqlFchIni=$sqlFchIni.'WHERE id_proyecto = '.$cualProyecto;
			$sqlFchIni=$sqlFchIni.' AND unidad = '.$laUnidad;
			$sqlFchIni=$sqlFchIni.' AND vigencia = '.$cualVigencia;
			$sqlFchIni=$sqlFchIni.' AND mes = '.$cualMes;
			#$sqlFchIni=$sqlFchIni." AND adp = '".$adp."'";
			#echo $sqlFchIni;
			$qryFch = mssql_query($sqlFchIni);
			if(mssql_num_rows($qryFch)>0)
			{
				$info=mssql_fetch_array($qryFch);
				$fch1=date('Y-m-d', strtotime($info[fch]));
				#echo 'Fecha ultimo registro : '.$fch1;
				if(strcmp($fch1, $fInicia )>=0)
				{
					$error=1;
					$msg = 'La fecha inicial es mejor a la fecha final de la ADP anterior que registro en ese mes.\n';
				}
			}

			/*
			if(strcmp($fch1, $fInicia )>0)
			{
				$error=1;
				$msg = 'La fecha ingresada es menor a la fecha inicial de la ADP.\n';
			}
			
			if(strcmp($fch2, $fFinal )<0)
			{
				$error=1;
				$msg .= 'La fecha ingresada es mayor a la fecha final de la ADP.';
			}
			#*/
			if($error==0)
			{
				$vigencia = date( 'Y', strtotime($fInicia) );
				$mes = date( 'm', strtotime($fInicia) );

				#	Verificar que no este registrada una ADP con la misma fecha.
				$sqlVerifica = 
				$sqlVerifica = "SELECT COUNT(*) hayAdp FROM adpht ";
				$sqlVerifica = $sqlVerifica ." Where id_proyecto = ".$cualProyecto;
				$sqlVerifica = $sqlVerifica ." AND unidad = ".$laUnidad;
				$sqlVerifica = $sqlVerifica ." AND vigencia = ".$vigencia;
				$sqlVerifica = $sqlVerifica ." AND mes = ".$mes;
				$sqlVerifica = $sqlVerifica ." AND adp = '".$adp."'";
				$sqlVerifica = $sqlVerifica ." AND YEAR(fechaInicio) = ".date( 'Y', strtotime($fInicia));
				$sqlVerifica = $sqlVerifica ." AND MONTH(fechaInicio) = ".date( 'm', strtotime($fInicia));
				$sqlVerifica = $sqlVerifica ." AND DAY(fechaInicio) = ".date( 'd', strtotime($fInicia));
				$sqlVerifica = $sqlVerifica ." AND YEAR(fechafinal) = ".date( 'Y', strtotime($fFinal));
				$sqlVerifica = $sqlVerifica ." AND MONTH(fechafinal) = ".date( 'm', strtotime($fFinal));
				$sqlVerifica = $sqlVerifica ." AND DAY(fechafinal) = ".date( 'd', strtotime($fFinal));
				
				
				#echo 'Verifica la vigencia : '.$sqlVerifica.'<br />';
				$hayAdp = mssql_fetch_array(mssql_query($sqlVerifica));
				if($hayAdp[hayAdp]==0)
				{
					#	Llave primaria
					$id = 1;
					$sqlPkAdp = "SELECT MAX(idAdp) idAdp FROM adpht ";
					$sqlPkAdp = $sqlPkAdp ." Where id_proyecto = ".$cualProyecto;
					$sqlPkAdp = $sqlPkAdp ." AND unidad = ".$laUnidad;
					$sqlPkAdp = $sqlPkAdp ." AND vigencia = ".$vigencia;
					$sqlPkAdp = $sqlPkAdp ." AND mes = ".$mes;
					#echo 'PK : '.$sqlVerifica.'<br />';
					
					$idAdp = mssql_fetch_array(mssql_query($sqlPkAdp));
					
					$id += $idAdp[idAdp];
					
					$sqlInsertAdp = "Insert Into adpht ( id_proyecto, unidad, vigencia, mes, idAdp, adp, fechaInicio, fechafinal, usuarioCrea, fechaCrea ) ";
					$sqlInsertAdp = $sqlInsertAdp . "Values ( ";
					$sqlInsertAdp = $sqlInsertAdp . $cualProyecto.", ";
					$sqlInsertAdp = $sqlInsertAdp . $laUnidad.", ";
					$sqlInsertAdp = $sqlInsertAdp . $vigencia.", ";
					$sqlInsertAdp = $sqlInsertAdp . $mes.", ";
					$sqlInsertAdp = $sqlInsertAdp . $id.", ";
					$sqlInsertAdp = $sqlInsertAdp . "'".$adp."', ";
					$sqlInsertAdp = $sqlInsertAdp . "'".$fInicia."', ";
					$sqlInsertAdp = $sqlInsertAdp . "'".$fFinal."', ";
					$sqlInsertAdp = $sqlInsertAdp . $_SESSION['sesUnidadUsuario'].", ";
					$sqlInsertAdp = $sqlInsertAdp . "'".date('Y-m-d')."' )";
					
					#echo 'Insertar : '.$sqlInsertAdp.'<br />';
					$qryInsertAdp=mssql_query($sqlInsertAdp);
					if(!$qryInsertAdp)
					{
						$error=1;
						$msg = 'No se puede registrar la ADP.';
						#echo 'Error Insert : '.mssql_get_last_message().'<br />';
					}
				}
				else
				{
					$error=1;
					$msg = 'Ya hay una ADP registrada con ese rango de fechas.';
					#echo 'Error Registrada : '.mssql_get_last_message().'<br />';
				}
				#$sqlInfo = "select id_proyecto, unidad, vigencia, mes, idAdp, fechaInicio, fechafinal, usuarioCrea, fechaCrea from adpht";
			}
			if(trim($error)==0)
			{
/*
        <input type="hidden" name="cualActiv" id="cualActiv" value="<?= $cualActiv ?>" />
        <input type="hidden" name="cualHorario" id="cualHorario" value="<?= $cualHorario ?>" />
        <input type="hidden" name="cualLocaliza" id="cualLocaliza" value="<?= $cualLocaliza ?>" />
        <input type="hidden" name="cualClaseT" id="cualClaseT" value="<?= $cualClaseT ?>" />
        <input type="hidden" name="cualCargo" id="cualCargo" value="<?= $cualCargo ?>" />
        <input type="hidden" name="cualVigencia" id="cualVigencia" value="<?= $cualVigencia ?>" />
        <input type="hidden" name="cualMes" id="cualMes" value="<?= $cualMes ?>" />

*/
				mssql_query(" COMMIT TRANSACTION");
				echo "<script>
						alert('La operaci?n se realiz? con ?xito.');
						window.close();
						MM_openBrWindow('htVtnAdp.php?cualProyecto=".$cualProyecto."&cualActiv=".$cualActiv."&cualHorario=".$cualHorario."&cualLocaliza=".$cualLocaliza."&cualClaseT=".$cualClaseT."&cualCargo=".$cualCargo."&cualVigencia=".$cualVigencia."&cualMes=".$cualMes."', 'winADP', 'toolbar=yes, scrollbars=yes, resizable=yes, width=960, height=700');
					  </script>";
			}
			else if(trim($error)==1)
			{
				mssql_query(" ROLLBACK TRANSACTION");
				echo "<script>alert('".$msg."')</script>";
			}
		}
?>


<html>
<head>

<title>.:: Hoja de tiempo - Facturaci&oacute;n de Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" /> 
<script language="javascript" src="calendar.js"></script>
<script language="javascript" src="val_fecha.js"></script>

<script language="javascript" type="text/javascript">
	//window.name = 'winADP';
	function trim(str) {
	  return str.replace(/^\s+|\s+$/g,"");
	}

	var nav4 = window.Event ? true : false;
	function acceptNum(evt){   
		var key = nav4 ? evt.which : evt.keyCode;   
		return (key == 45 || (key>= 48 && key <= 57) || (key >=65 && key <=90) || (key >=97 && key <=122) );
	}

	function valida()
	{

		var cont=1,ban=0;
		var cont_regis=parseInt(document.Form.cont.value);
		var fch, msg, vigencia, vigMes;
		
		var expresion = /[0-9a-zA-Z]/;
				
		msg = fch = vigencia = vigMes = '';
		
		fch = compare_fecha( document.getElementById('fchInicial').value, document.getElementById('fchFinal').value );

		if(document.getElementById('adp').value == '')
		{
			msg = 'El ADP es obligatorio.\n';
		}

		vigencia = vigenciaMes( document.getElementById('fchInicial').value, document.getElementById('fchFinal').value );
		
		if(document.getElementById('fchInicial').value==''||document.getElementById('fchFinal').value=='')
		{
			msg = msg +'Las fechas son obligatorias.\n';
		}
		 
		if((trim(document.getElementById('fchInicial').value)!=''&&trim(document.getElementById('fchFinal').value)!='')&&fch==true)
		{
			msg = msg + 'Las fecha inicial no puede ser mayor a la final.\n';
		}
		if((trim(document.getElementById('fchInicial').value)!=''&&trim(document.getElementById('fchFinal').value)!='')&&vigencia==false)
		{
			msg = msg + 'No es posible registrar una ADP que comprende 2 meses. Por favor relacione unicamente las fechas incluidas en el mes y vigencia seleccionados.\n';
		}
		vigMes = vigenciaAdp(document.getElementById('fchInicial').value, document.getElementById('fchFinal').value, document.getElementById('cualMes').value);
		if(vigMes==false)
		{
			msg = msg + 'La fecha inicial y/o final no corresponde al mes y vigencia seleccionados.\n';
		}

		if(msg!='')
		{
			alert(msg);
		}
		else
		{
			document.Form.recarga.value=2;
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
      <table width="100%"  border="0" cellspacing="1" cellpadding="0" bgcolor="#FFFFFF">
        <tr>
<?
	$cur_usu=mssql_query("select unidad,UPPER( nombre) nombre,UPPER( apellidos) apellidos from Usuarios where unidad=".$laUnidad);
	$datos_usu=mssql_fetch_array($cur_usu);
?>
          <td class="TituloTabla" >Unidad</td>
          <td class="TxtTabla" ><?=$datos_usu["unidad"]; ?></td>
        </tr>
        <tr>
          <td class="TituloTabla" >Nombre</td>
          <td  class="TxtTabla" ><?=$datos_usu["nombre"]." ".$datos_usu["apellidos"]; ?></td>
        </tr>
        <tr>
          <td class="TituloTabla" >Vigencia</td>
          <td  class="TxtTabla" ><?=$cualVigencia ?></td>
          </tr>
        <tr>
          <td class="TituloTabla" width="7%" >Mes</td>
          <td  class="TxtTabla" >
			<?
				$mes = array( '', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre' );
				echo $mes[$cualMes];
            ?>
            </td>
          </tr>
        <tr>
          <td colspan="5" class="TxtTabla">&nbsp;</td>
        </tr>
        <tr>
        <td colspan="4" class="TituloUsuario">
        .::Proyecto - 
                <?
					$sqlProyecto = "select nombre from Proyectos where id_proyecto = ".$cualProyecto;
					$nomProyecto = mssql_fetch_array( mssql_query( $sqlProyecto ) );
					echo $nomProyecto[nombre];
				?>                
        </td>
      </tr>
      <tr>
      	<td colspan="2">
	<table width="100%" border="0" cellpadding="0" cellspacing="1"><!-- bgcolor="#FFFFFF" -->
              <tr>
                <td class="TituloTabla2">ADP</td>
                <td align="left" class="TxtTabla">
                <!--<input type="text" class="CajaTexto" name="adp" id="adp" value="<?= $adp ?>" onBlur="document.Form.submit();" onKeyPress="return acceptNum(event);" />-->
                <input type="text" class="CajaTexto" name="adp" id="adp" value="<?= $adp ?>" onKeyPress="return acceptNum(event);" />
                </td>
                </tr>
              <tr class="TituloTabla2">
                <td width="25%">Fecha Inicial (m/d/Y)</td>
                <td width="25%" align="left" class="TxtTabla" >
                <?
					#	Esta consulta lo que hace es buscar si ya hay un ADP ingresado en la base de datos y carga el dia que sigue. , , 
					$sqlFchIni='SELECT MAX(fechafinal) fch FROM AdpHT ';
					$sqlFchIni=$sqlFchIni.'WHERE id_proyecto = '.$cualProyecto;
					$sqlFchIni=$sqlFchIni.' AND unidad = '.$laUnidad;
					$sqlFchIni=$sqlFchIni." AND adp = '".$adp."'";
					#/*
					$sqlFchIni=$sqlFchIni.'UNION SELECT MAX(fechafinal) fch FROM AdpHT ';
					$sqlFchIni=$sqlFchIni.'WHERE id_proyecto = '.$cualProyecto;
					$sqlFchIni=$sqlFchIni.' AND unidad = '.$laUnidad;
					$sqlFchIni=$sqlFchIni.' AND vigencia = '.$cualVigencia;
					$sqlFchIni=$sqlFchIni.' AND mes = '.$cualMes;
					$sqlFchIni=$sqlFchIni." AND adp = '".$adp."'";
					#*/
					#echo '<p>'.$sqlFchIni.'</p>';
					#$qryFchIni=mssql_fetch_array(mssql_query($sqlFchIni));
					$qryFchIni=mssql_query($sqlFchIni);
					
					$m = $fchAnt ='x';
					if($adp!='')
					{
						while($rwFchIni=mssql_fetch_array($qryFchIni))
						{
							#echo $sqlFchIni;
							$m = date( 'm', strtotime($rwFchIni['fch']));
							if($m==$cualMes)
							{
								#echo 'ingreso al segundo<br />';
								$fch =date( 'm/d/Y', strtotime($rwFchIni['fch']));
								$fchInicial=date( 'm/d/Y', strtotime($fch.'+1day'));
							}
							if($fchAnt=='')
							{
								$fch =date( 'm/d/Y', strtotime($rwFchIni['fch']));
								#$fchInicial=date( 'm/d/Y', strtotime($fch.'+1month'));
								$fchInicial=date( 'm/01/Y', strtotime($fch.'+1month'));
							}
							$fchAnt = $rwFchIni['fch'];
						}
					}
				?>
                <input type="text" class="CajaTexto" name="fchInicial" id="fchInicial" value="<?= $fchInicial ?>" />
                <a href="javascript:cal.popup();"><img src="imagenes/cal.gif" alt="" width="16" height="16" border="0" /></a></td>
                </tr>
              <tr>
                <td class="TituloTabla2">Fecha Final (m/d/Y)</td>
                <td align="left" class="TxtTabla">
                <input type="text" name="fchFinal" id="fchFinal" class="CajaTexto" value="<?= $fchFinal ?>" />
                <a href="javascript:cal2.popup();"><img src="imagenes/cal.gif" alt="" width="16" height="16" border="0" /></a></td>
                </tr>
              <tr>
              	<td colspan="2">
    <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr>
            <td align="right" class="TxtTabla">
                <input name="guardar" type="button" class="Boton" id="guardar" value="Guardar" onClick="valida()" >
                <input type="hidden" name="cualProyecto" id="cualProyecto" value="<?= $cualProyecto ?>" />
                <input type="hidden" name="cualActiv" id="cualActiv" value="<?= $cualActiv ?>" />
                <input type="hidden" name="cualHorario" id="cualHorario" value="<?= $cualHorario ?>" />
                <input type="hidden" name="cualLocaliza" id="cualLocaliza" value="<?= $cualLocaliza ?>" />
                <input type="hidden" name="cualClaseT" id="cualClaseT" value="<?= $cualClaseT ?>" />
                <input type="hidden" name="cualCargo" id="cualCargo" value="<?= $cualCargo ?>" />
                <input type="hidden" name="cualVigencia" id="cualVigencia" value="<?= $cualVigencia ?>" />
                <input type="hidden" name="cualMes" id="cualMes" value="<?= $cualMes ?>" />
                <input type="hidden" name="unidad" id="unidad" value="<?= $laUnidad ?>" />
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
  
    </td>
    </tr>
</table>


	     </td>
         </tr>
         </table>
</form>
<script language="JavaScript">
		 var cal = new calendar2(document.forms['Form'].elements['fchInicial']);
		 var cal2 = new calendar2(document.forms['Form'].elements['fchFinal']);
		 cal.year_scroll = true;
		 cal.time_comp = false;
</script>
</body>
</html>



<? mssql_close ($conexion); ?>	
