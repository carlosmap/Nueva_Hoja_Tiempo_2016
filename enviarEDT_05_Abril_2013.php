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
//-->
</script>
<?php
session_start();
//include("../verificaRegistro2.php");
//include('../conectaBD.php');

//Establecer la conexión a la base de datos
//$conexion = conectar();
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";



if(trim($recarga) == "2")
{
/*			echo "<script>alert('Procesando la solicitud, por favor espere');</script>";
*/
//echo "entro <br>";

			//consulta el nombre del usuario que genera la solicitud
			$sql_usuario="select nombre,apellidos from HojaDeTiempo.dbo.Usuarios where unidad=".$_SESSION["sesUnidadUsuario"];
			$cur_usuario=mssql_query($sql_usuario);
			if  (trim($cur_usuario)=="")  
			{
				$error="si";
			}
			if($datos_usuario=mssql_fetch_array($cur_usuario))
			{
				$nombre_usuario=$datos_usuario["nombre"];
				$apellido_usuario=$datos_usuario["apellidos"];
			}	

			$error="no";
			$cursorTran1 = mssql_query(" BEGIN TRANSACTION ");
			$pAsunto = "Solicitud de aprobacion EDT";
//echo $inf_proy."<br>";
			include("fncEnviaMailPEAR.php");
			$pTema = "<table width='100%'  border='0' cellspacing='1' cellpadding='0'>";
			$pTema = $pTema . "	<tr class='Estilo2'>";
			$pTema = $pTema . "		<td width='30%'>Asunto:</td>";
			$pTema = $pTema . "		<td  width='70%'>Solicitud de aprobaci&oacute;n </td>";
			$pTema = $pTema . "	</tr>";
			$pTema = $pTema . "	<tr class='Estilo2'>";
			$pTema = $pTema . "		<td>&nbsp;</td>";
			$pTema = $pTema . "		<td>&nbsp;</td>";
			$pTema = $pTema . "	</tr>";
			$pTema = $pTema . "	<tr class='Estilo2'>";
			$pTema = $pTema . "		<td width='30%'>Proyecto:</td>";
			$pTema = $pTema . "		<td width='70%'>".strtoupper($inf_proy)."</td>";
			$pTema = $pTema . "	</tr>";
			$pTema = $pTema . "	<tr class='Estilo2'>";
			$pTema = $pTema . "		<td>&nbsp;</td>";
			$pTema = $pTema . "		<td>&nbsp;</td>";
			$pTema = $pTema . "	</tr>";
			$pTema = $pTema . "	<tr class='Estilo2'>";
			$pTema = $pTema . "		<td width='30%'>Quien solicita:</td>";
			$pTema = $pTema . "		<td width='70%'>".$nombre_usuario." ".$apellido_usuario."</td>";
			$pTema = $pTema . "	</tr>";
			$pTema = $pTema . "	<tr class='Estilo2'>";
			$pTema = $pTema . "		<td>&nbsp;</td>";
			$pTema = $pTema . "		<td>&nbsp;</td>";
			$pTema = $pTema . "	</tr>";
			$pTema = $pTema . "	<tr class='Estilo2'>";
			$pTema = $pTema . "		<td width='40%'>Fecha prevista para el inicio del proyecto (mes/dia/a&ntilde;o):</td>";
			$pTema = $pTema . "		<td width='60%'>".$fechaPrevista."</td>";
			$pTema = $pTema . "	</tr>";
			$pTema = $pTema . "	<tr class='Estilo2'>";
			$pTema = $pTema . "		<td>&nbsp;</td>";
			$pTema = $pTema . "		<td>&nbsp;</td>";
			$pTema = $pTema . "	</tr>";
			$pTema = $pTema . "	<tr class='Estilo2'>";
			$pTema = $pTema . "		<td width='40%'>Fecha de solicitud (mes/dia/a&ntilde;o):</td>";
			$pTema = $pTema . "		<td width='60%'>".$fecha."</td>";
			$pTema = $pTema . "	</tr>";
			$pTema = $pTema . "	<tr class='Estilo2'>";
			$pTema = $pTema . "		<td>&nbsp;</td>";
			$pTema = $pTema . "		<td>&nbsp;</td>";
			$pTema = $pTema . "	</tr>";
			$pTema = $pTema . "	<tr class='Estilo2'>";
			$pTema = $pTema . "		<td colspan='2'><br>Ya se encuentra  finalizada la EDT del proyecto. ".strtoupper($inf_proy).". Se requiere su VoBo para proceder a la planeaci&oacute;n del recurso. </td>";
			$pTema = $pTema . "	</tr>";

			$pTema = $pTema . "	<tr>";
			$pTema = $pTema . "		<td width='25%' class='Estilo2' colspan='2'>";					
			$pTema = $pTema . "	</tr>";
			if(trim($observacion)!="")
			{
				$pTema = $pTema . "	<tr class='Estilo2'>";
				$pTema = $pTema . "		<td colspan='2'><br>Observaciones</td>";
				$pTema = $pTema . "	</tr>";
				$pTema = $pTema . "	<tr class='Estilo2'>";
				$pTema = $pTema . "		<td colspan='2'>".$observacion."</td>";
				$pTema = $pTema . "	</tr>";
			}
			$pTema = $pTema . "</table>";
		
			$pFirma = ""; // Geotecnia - Investigaciones ";
//echo $inf_proy."<br>";
//echo $usu_firma;


//$usu_firma=18121;
//20400
			//consulta el correo de la persona a quien se le realiza la solicitud
			$sql_usu_aprueba="select * from HojaDeTiempo.dbo.Usuarios where unidad in (18121) and retirado is null";
			$eCursorMsql=mssql_query($sql_usu_aprueba);
			if  (trim($eCursorMsql)=="")  
			{
				$error="si";
			}
//echo " **** ".$error." - $sql_usu_aprueba - ".mssql_get_last_message()."<br><br>";	

			//consulta la hora en la que se realiza la solicitud
//			$sql_hora="select  datepart(hour,getdate()),datepart(minute ,getdate()),datepart(second ,getdate()) ";
//			$cur_hora=mssql_query($sql_hora);
//			while($datos_hora=mssql_fetch_array($cur_hora))



			///consulta si  existe una solicitud de aprobacion anterior sin confirmar
			$sql_sol_edt="select * from HojaDeTiempo.dbo.AutorizaEDT where id_proyecto=". $cualProyecto." and validaVoBo=0";
			$cur_sol_edt=mssql_query($sql_sol_edt);
			if (trim($cur_sol_edt)=="")  
			{
				$error="si";
			}

			$regis=mssql_num_rows($cur_sol_edt);

			//si no se encontraron solicitudes
			if($regis==0)
			{
			
				//consulta la secuencia maxima de solicitudes generadas por los proyectos
				$secuencia=1;
				$sql_secuen="select MAX(secuencia)as secuencia from HojaDeTiempo.dbo.AutorizaEDT";
				$cur_secuen=mssql_query($sql_secuen);
			//	$cur_secuen=mssql_query(" select  MAX(secuencia)as secuencias from HojaDeTiempo.dbo.AutorizaEDT ");
//	echo $error."<br><br>";
				if (trim($cur_secuen)=="")  
				{
					$error="si";
				}
	
//	echo $error." - $sql_secuen - ".mssql_get_last_message()."<br><br>";			
			if($datos_secuen=mssql_fetch_array($cur_secuen))
					$secuencia=$datos_secuen["secuencia"]+1;
		
		
				//almacena la informacion de la solicitud
				$sql_ins_solicitud="insert into HojaDeTiempo.dbo.AutorizaEDT (id_proyecto,secuencia,fechaIniProy,usuElabora,fechaElabora";
				if(trim($observacion)!="")
					$sql_ins_solicitud=$sql_ins_solicitud.",comentaElabora";
	
				$sql_ins_solicitud=$sql_ins_solicitud.",enviaAFirma,validaElabora,usuarioCrea,fechaCrea,unidadVoBo)";
	
				$sql_ins_solicitud=$sql_ins_solicitud."values(". $cualProyecto.",".$secuencia.",'".$fechaPrevista."',".$_SESSION["sesUnidadUsuario"].",getdate()";
				if(trim($observacion)!="")
							$sql_ins_solicitud=$sql_ins_solicitud.",'".$observacion."'";
	
				$sql_ins_solicitud=$sql_ins_solicitud.", 1,0,".$_SESSION["sesUnidadUsuario"].",getdate(),".$usu_firma." ) ";
	
				$cur_ins_solicitud=mssql_query($sql_ins_solicitud);
			
				if  (trim($cur_ins_solicitud)=="")  
				{
					$error="si";
				}
	
//	echo $error." *****- $sql_ins_solicitud - ".mssql_get_last_message()."<br><br>";	
			}
			else
			{
				//si se encontraron solicitudes 
	
				//consulta la secuencia de la ultima solicitud generada, para la edt del proyecto
				$sql_secuen2="select MAX(secuencia)as secuencia from HojaDeTiempo.dbo.AutorizaEDT where id_proyecto = ".$cualProyecto." and validaVoBo=0 ";
				$cur_secuen2=mssql_query($sql_secuen2);
				if  (trim($cur_secuen2)=="")  
				{
					$error="si";
				}
//	echo $error." - $sql_secuen - ".mssql_get_last_message()."<br>";			
				if($datos_secuen2=mssql_fetch_array($cur_secuen2))
					$secuencia2=$datos_secuen2["secuencia"];

		
				//consulta la unidad del usuario que contesto la solicitud
				$sql_usu_sol="select unidadVoBo from AutorizaEDT where id_proyecto=".$cualProyecto." and secuencia=".$secuencia2;
				$cur_usu_sol=mssql_query($sql_usu_sol);
				if($datos_usu_sol=mssql_fetch_array($cur_usu_sol))
						$usu_firma=$datos_usu_sol["unidadVoBo"];	
				if  (trim($cur_usu_sol)=="")  
				{
					$error="si";
				}						

				//actualiza  la informacion de la solicitud de la EDT
				$sql_ins_solicitud="update  HojaDeTiempo.dbo.AutorizaEDT set enviaAFirma=1, fechaIniProy='".$fechaPrevista."' , usuElabora=".$_SESSION["sesUnidadUsuario"].",comentaElabora='".$observacion."',fechaElabora=getdate(),usuarioMod=".$_SESSION["sesUnidadUsuario"].",fechaMod=getdate() ";
				$sql_ins_solicitud=$sql_ins_solicitud." where id_proyecto = ".$cualProyecto." and secuencia=".$secuencia2;
				$cur_ins_solicitud=mssql_query($sql_ins_solicitud);

//	echo $error." - $sql_ins_solicitud - ".mssql_get_last_message()."<br>";	
				if  (trim($cur_ins_solicitud)=="")  
				{
					$error="si";
				}
			}

//echo $error." - $sql_usu_aprueba - ".mssql_get_last_message();			
//		echo "<br>".$pTema."<br>".$pFirma;
	if  (trim($error)=="no")  
	{
//		$cursorTran2 = mssql_query(" ROLLBACK TRANSACTION ");

			
		while($eRegMsql = mssql_fetch_array($eCursorMsql))
		{		
		   $miMailUsuarioEM = $eRegMsql[email] ;
	
		   //***EnviarMailPEAR	
		   $pPara= trim($miMailUsuarioEM) . "@ingetec.com.co";
	
		   enviarCorreo($pPara, $pAsunto, $pTema, $pFirma);
	
		   //***FIN EnviarMailPEAR
		   $miMailUsuarioEM = "";
	
		}

//echo " <br><br>comit";
		$cursorTran2 = mssql_query(" COMMIT TRANSACTION ");		
		echo ("<script>alert('Operaci\xf3n realizada satisfactoriamente.');</script>"); 
	} 
	else 
	{
//echo " <br><br>rollback";
		$cursorTran2 = mssql_query(" ROLLBACK TRANSACTION ");
		echo ("<script>alert('Error durante la grabaci\xf3n');</script>");
	}

	echo ("<script>window.close();MM_openBrWindow('htPlanProyectos01.php?cualProyecto=".$cualProyecto."','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");

//		$cursorTran2 = mssql_query(" ROLLBACK TRANSACTION ");
}

?>

<?php 
				//Trae el nombre de los proyectos en los que el usuario activo aparece como director y/o coordinador
				$sql="SELECT P.*, D.nombre nombreD, D.apellidos apellidosD, C.nombre nombreC, C.apellidos apellidosC  ";
				$sql=$sql." FROM proyectos P, Usuarios D, Usuarios C " ;
				$sql=$sql." WHERE P.id_director *= D.unidad " ;
				$sql=$sql." AND P.id_coordinador *= C.unidad " ;
				$sql=$sql." AND P.id_proyecto = " . $cualProyecto." " ;
				$cursor = mssql_query($sql);

				$datos_inf_proy=mssql_fetch_array($cur=mssql_query("select fechaInicio from Proyectos where id_proyecto=" . $cualProyecto ));


			//consulta la informacion de las solicitudes anteriores
			$sql_historico_solicitud_edt="select * from HojaDeTiempo.dbo.AutorizaEDT where id_proyecto=". $cualProyecto." and validaVoBo=0";
			$cur_historico=mssql_query($sql_historico_solicitud_edt);
			$datos_historico=mssql_fetch_array($cur_historico)

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>.:: Planeaci&oacute;n de Proyectos</title>

<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">

<script language="JavaScript" src="calendar.js"></script>


</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<form action="" type="post"   name="Form1">

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Enviar a Director <?php //echo $_SESSION["sesUnidadUsuario"]; ?></td>

  </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><img src="../images/Pixel.gif" width="4" height="2"></td>
        </tr>
      </table>      
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td class="TituloTabla">Proyecto</td>
          <td class="TxtTabla">

<?php

				 while ($reg=mssql_fetch_array($cursor)) 
				{
					 echo  "[".$reg[codigo].".".$reg[cargo_defecto]."]  -  ".  ucwords(strtolower($reg[nombre])) ;
					 $inf_proy="[".$reg[codigo].".".$reg[cargo_defecto]."]  -  ".  ucwords(strtolower($reg[nombre])) ;
					 $uni_dir=$reg[id_director];
					 $uni_cor=$reg[id_coordinador];
//echo $inf_proy;
				}

 ?>

		  </td>
        </tr>
        <tr>
          <td class="TituloTabla">Fecha (mes/dia/a&ntilde;o)</td>
<?php

//		$mes= array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
//$mes[$datos_fech[mes]]
		$sql_fech="select day(GETDATE()) as dia ,MONTH(GETDATE()) as mes,YEAR(GETDATE())as ano";
		$cur_fech=mssql_query($sql_fech);
		$datos_fech=mssql_fetch_array($cur_fech);
if ((1<=$datos_fech["mes"]) or ($datos_fech["mes"]<=9))
{
	$mess="0".$datos_fech["mes"];
}
if ((1<=$datos_fech["dia"]) or ($datos_fech["dia"]<=9))
{
	$dias="0".$datos_fech["dia"];
}
?>
          <td class="TxtTabla"><input name="fecha" type="text" class="CajaTexto" id="fecha" value="<? echo  $mess."/".$dias."/".$datos_fech["ano"]; ?>" size="17" readonly ></td>
        </tr>
        <tr>
          <td  class="TituloTabla">Fecha de Inicio (mes/dia/a&ntilde;o)</td>
          <td class="TxtTabla"><input name="fechaPrevista" type="text" class="CajaTexto" id="fechaPrevista" readonly value="<?  if($datos_inf_proy["fechaInicio"]!=""){  echo date("m/d/Y", strtotime( $datos_inf_proy["fechaInicio"])); }  ?>" />
<!--
            <a href="javascript:cal.popup();"><img src="imagenes/cal.gif" width="16" height="16" border="0" /></a></td>
-->
        </tr>
        <tr>
          <td class="TituloTabla">&iquest;Enviar a VoBo?</td>
          <td class="TxtTabla"><input type="radio" name="enviar" id="enviar" value="1" checked>No <input type="radio" name="enviar" id="enviar" value="2">Si
          </td>
        </tr>
        <tr>
          <td width="20%" class="TituloTabla">&iquest;Quien Firma?</td>

          <td class="TxtTabla">
            <select name="usu_firma" id="usu_firma" class="CajaTexto">
              <option value=""></option>
<?php
/*
				 $reg2=mssql_fetch_array($cursor2);

					echo "<option value='".$reg2[id_director]."'>[".$reg2[id_director]."] ".ucwords(strtolower($reg2[nombreD])) . " " . ucwords(strtolower($reg2[apellidosD]))."</option>";
					echo "<option value='".$reg2[id_coordinador]."'>[".$reg2[id_coordinador]."] ".ucwords(strtolower($reg2[nombreC])) . " " . ucwords(strtolower($reg2[apellidosC]))."</option>";
*/
				//consulta el correo del director, coordinador del poryecto
				$sql_ord_dir="select * from Usuarios where unidad in(".$uni_dir.", ".$uni_cor.") and retirado is null";
				$cur_ord_dir=mssql_query($sql_ord_dir);
				while($datos_ord_dir=mssql_fetch_array($cur_ord_dir))
				{
					echo "<option value='".$datos_ord_dir[unidad]."'>[".$datos_ord_dir[unidad]."] ".ucwords(strtolower($datos_ord_dir[nombre])) . " " . ucwords(strtolower($datos_ord_dir[apellidos]))."</option>";
				}

				//consulta los ordenadores de gasto del proyecto
				$sql_ordenadores="select u.unidad,u.nombre,u.apellidos from GestiondeInformacionDigital.dbo.OrdenadorGasto as o
									inner join HojaDeTiempo.dbo.Usuarios as u on o.unidadOrdenador=u.unidad
									where id_proyecto=".$cualProyecto." and u.retirado is null";
				$cur_ordenadores=mssql_query($sql_ordenadores);
				while($datos_ordena=mssql_fetch_array($cur_ordenadores))
				{
					echo "<option value='".$datos_ordena[unidad]."'>[".$datos_ordena[unidad]."] ".ucwords(strtolower($datos_ordena[nombre])) . " " . ucwords(strtolower($datos_ordena[apellidos]))."</option>";
				}
?>

            </select></td>
        </tr>

        <tr>
          <td class="TituloTabla" colspan="2">Observaciones </td>
        </tr>
        <tr>
          <td class="TituloTabla" colspan="2" align="center">
            <textarea maxlength="999" onKeyUp="return ismaxlength(this)" class="CajaTexto"   name="observacion" id="observacion" cols="100%" rows="5"   onKeyPress=" return acceptComilla(event)"  ><?php echo $datos_historico["comentaElabora"];  ?> </textarea></td>
        </tr>
		
      </table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td height="5" class="TituloTabla"> </td>
        </tr>
      </table>
<table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
          <td align="right" class="TxtTabla">
  		    <input name="recarga" type="hidden" id="recarga" value="1">
  		    <input name="cualProyecto" type="hidden" id="cualProyecto" value="<?php echo $cualProyecto; ?>">
  		    <input name="inf_proy" type="hidden" id="inf_proy" value="<?php echo $inf_proy; ?>">

  		    <input name="Submit" type="button" class="Boton" value="Grabar" onClick="envia2()" ></td>
        </tr>
  </table>
      </td>
  </tr>
</table>

	     </td>
         </tr>
         </table>
</form> 
<script language="JavaScript" type="text/JavaScript">

function envia2()
{ 


		//se se ha seleccionado si en la seccion "enviar a VoBo"
		if(document.Form1.enviar[1].checked)
		{

			if(document.Form1.usu_firma.value=="")
			{
				alert("Por favor seleccione, la persona que firma.");
			}
			else if(document.Form1.fechaPrevista.value=="")
			{
				alert("Por favor indique la fecha prevista de inicio del proyecto.");
			}
			else
			{
				var selec = document.getElementById("usu_firma");
				var usuario_seleccionado = selec.options[selec.selectedIndex].text;
				if(confirm("Desea enviar la EDT a "+usuario_seleccionado+" para su aprobaci\xf3n. "))
				{
//				alert ("si");
					document.Form1.recarga.value="2";
//alert('Procesando la solicitud, por favor espere');
					document.Form1.submit();
				}
				
			}
		}
		else
		{
			if(confirm("No se enviar\xe1 la EDT para su aprobaci\xf3n. Desea cancelar la solicitud?"))	
				window.close();
		}
	
}

var nav4 = window.Event ? true : false;

function acceptComilla(evt){   
var key = nav4 ? evt.which : evt.keyCode;   

return (key != 39);
}

</script>
<script language="JavaScript">
		 var cal = new calendar2(document.forms['Form1'].elements['fechaPrevista']);
		 cal.year_scroll = true;
		 cal.time_comp = false;
</script>
</body>
</html>

<? mssql_close ($conexion); ?>	
