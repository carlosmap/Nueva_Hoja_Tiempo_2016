<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
//-->
</script>
<?
session_start();

//Establecer la conexión a la base de datos
include "funciones.php";
include "validaUsrBd.php";

//Traer la informaciónde la actividad para mostrarlo en el encabezado
$sql="select * , DATEDIFF(month, fecha_inicio, fecha_fin) AS NumMeses from actividades " ;
$sql=$sql." where id_proyecto = " . $cualProyecto ;
$sql=$sql." and id_actividad = " . $cualActividad ;
$cursor = mssql_query($sql);
if ($reg=mssql_fetch_array($cursor)) { 
	$pActividad=ucwords(strtolower($reg[nombre]));
	$pFechaI= $reg[fecha_inicio] ;
	$pFechaF=date("M d Y ", strtotime($reg[fecha_fin])); 
}


//Si se presionó el botón Grabar
if ($valor != "") {
	//Valida que no ingrese 0 en Valor recurso
	if ($valor == 0) {
		echo ("<script>alert('No puede asignar 0 al valor del recurso. Por favor corrija la información.');</script>");
	}
	else {
		//Direcciona a la BD a donde va a grabar
		@mssql_select_db("HojaDeTiempo");
		
		//valida que el valor ingresado para la actividad no sea menor a la sumatoria de valores programados 
		//en las asignaciones para el proyecto y la actividad.
		$pValorAsignaciones = 0 ;
		$aSql="SELECT COALESCE(SUM(valorProgramado), 0) valorAsigna FROM Asignaciones  ";
		$aSql=$aSql." WHERE id_proyecto =" . $miProyecto  ;
		$aSql=$aSql." AND id_actividad = " . $miActividad ;
		$aCursor = mssql_query($aSql);
		if ($aReg=mssql_fetch_array($aCursor)) {
			$pValorAsignaciones = $aReg[valorAsigna];
		}
		
		if ($valor < $pValorAsignaciones) { 
			echo ("<script>alert('La actividad no puede tener un valor inferior al valor " . number_format($pValorAsignaciones, 0, ',', '.') . " ya programado');</script>");
		}
		else {
			//Encuentra la siguiente secuencia para el recurso en la tabla ActividadesRecursos
			$vSql="SELECT COALESCE(MAX(secuencia), 0) hayRecurso ";
			$vSql=$vSql." FROM ActividadesRecursos " ;
			$vSql=$vSql." WHERE id_proyecto = " . $miProyecto  ;
			$vSql=$vSql." AND id_actividad =" . $miActividad ;
			$vCursor = mssql_query($vSql);
			if ($vReg=mssql_fetch_array($vCursor)) {
				$pSecRec = $vReg[hayRecurso] + 1;
				}
			else {
				$pSecRec = 1;
			}
		
			//Realiza la inserción  del recurso de la actividad en la tabla dbo.ActividadesRecursos
			//id_proyecto, id_actividad, secuencia, valorActiv, unidad, fecha
			$query = "INSERT INTO ActividadesRecursos(id_proyecto, id_actividad, secuencia, valorActiv, unidad, fecha)  " ;
			$query = $query . " VALUES( " . $miProyecto . ", " ;
			$query = $query . $miActividad . ", ";
			$query = $query . $pSecRec . ",  ";	
			$query = $query . $valor . ", ";	
			$query = $query . $laUnidad  . ", ";	
			$query = $query . " '" . gmdate ("n/d/y") . "' ";	
			$query = $query . " ) ";	
			$cursor = mssql_query($query);
		
			//Si los cursores no presentaron problema
			if  (trim($cursor) != "") {
				echo ("<script>alert('La Grabación se realizó con éxito.');</script>");
				/*
				//****Envio de mails
				//Se envia correo electrónico a Director, Coordinador y Ordenadores del gasto sobre el cambio en la asignación del recurso
				//Enviar el correo electrónico al director / coordinador del proyecto
				$dcSql="SELECT P.nombre, P.id_director, D.email mailDirector , P.id_coordinador, C.email mailCoordinador ";
				$dcSql=$dcSql." FROM proyectos P, Usuarios D, Usuarios C ";
				$dcSql=$dcSql." where P.id_director *= D.unidad ";
				$dcSql=$dcSql." and P.id_coordinador *= C.unidad ";
				$dcSql=$dcSql." and id_proyecto = " . $miProyecto ;
				$dcCursor = mssql_query($dcSql);
				if ($dcReg=mssql_fetch_array($dcCursor)) {
					$mailDir = $dcReg[mailDirector] ;
					$mailCoord = $dcReg[mailCoordinador];
					$nombreProyecto = $dcReg[nombre];
				}


				//***Arma el mensaje
				$msgM="<html>";
				$msgM=$msgM." <head>";
				$msgM=$msgM." <title></title>";
				$msgM=$msgM." <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>";
				$msgM=$msgM." <style type='text/css'> ";
				$msgM=$msgM." <!-- ";
				$msgM=$msgM." .Estilo1 { ";
				$msgM=$msgM." 	font-family: Verdana, Arial, Helvetica, sans-serif; ";
				$msgM=$msgM." 	font-weight: bold; ";
				$msgM=$msgM." 	font-size: 12px; ";
				$msgM=$msgM." 	color: #FFFFFF; ";
				$msgM=$msgM." } ";
				$msgM=$msgM." .Estilo2 { ";
				$msgM=$msgM." 	font-family: Verdana, Arial, Helvetica, sans-serif; ";
				$msgM=$msgM." 	color: #666666; ";
				$msgM=$msgM." 	font-size: 12px; ";
				$msgM=$msgM." } ";
				$msgM=$msgM." --> ";
				$msgM=$msgM." </style>";
				$msgM=$msgM." </head>";
				$msgM=$msgM." <body> ";
				$msgM=$msgM." <table width='100%'  border='0' cellspacing='0' cellpadding='0'>";
				$msgM=$msgM."   <tr> ";
				$msgM=$msgM."     <td height='10' bgcolor='#999999'></td>";
				$msgM=$msgM."   </tr>";
				$msgM=$msgM."   <tr>";
				$msgM=$msgM."     <td>&nbsp;</td>";
				$msgM=$msgM."   </tr>";
				$msgM=$msgM."   <tr>";
				$msgM=$msgM."     <td class='Estilo2'>Estimado usuario: </td> \n\n";
				$msgM=$msgM."   </tr>";
				$msgM=$msgM."   <tr>";
				$msgM=$msgM."     <td>&nbsp;</td>";
				$msgM=$msgM."   </tr>";
				$msgM=$msgM."   <tr>";
				$msgM=$msgM."     <td><span class='Estilo2'>Se informa que se realizó la actualización del recurso para: </span></td> \n";
				$msgM=$msgM."   </tr>";
				$msgM=$msgM."   <tr>";
				$msgM=$msgM."     <td><span class='Estilo2'>Proyecto: <b>" . $nombreProyecto . "</b> </span></td> \n";
				$msgM=$msgM."   </tr>";
				$msgM=$msgM."   <tr>";
				$msgM=$msgM."     <td><span class='Estilo2'>Actividad: <b>" . $nombreActiv . " </b> </span></td> \n";
				$msgM=$msgM."   </tr>";
				$msgM=$msgM."   <tr>";
				$msgM=$msgM."     <td><span class='Estilo2'>Valor: <b> $ " . number_format($valor, 0, ',', '.') . " </b> </span></td> \n";
				$msgM=$msgM."   </tr>";
				$msgM=$msgM."   <tr>";
				$msgM=$msgM."     <td>&nbsp;</td>";
				$msgM=$msgM."   </tr>";
				$msgM=$msgM."   <tr>";
				$msgM=$msgM."     <td>&nbsp;</td>";
				$msgM=$msgM."   </tr>";
				$msgM=$msgM."   <tr>";
				$msgM=$msgM."     <td>&nbsp;</td>";
				$msgM=$msgM."   </tr>";
				$msgM=$msgM."   <tr>";
				$msgM=$msgM."     <td class='Estilo2'>Sistema Portal Ingetec S.A.</td>";
				$msgM=$msgM."   </tr>";
				$msgM=$msgM."   <tr>";
				$msgM=$msgM."     <td class='Estilo2'>Mensaje generado automáticamente por el sistema de programación</td> \n";
				$msgM=$msgM."   </tr>";
				$msgM=$msgM."   <tr>";
				$msgM=$msgM."     <td>&nbsp;</td>";
				$msgM=$msgM."   </tr>";
				$msgM=$msgM."   <tr >";
				$msgM=$msgM."     <td bgcolor='#999999' ><span class='Estilo1'>Ingetec S.A. </span></td>";
				$msgM=$msgM."   </tr>";
				$msgM=$msgM." </table>";
				$msgM=$msgM." </body>";
				$msgM=$msgM." </html>";

				$Asunto = "Programación de proyectos - Actualización de recursos";
				$Descripcion = $msgM;
				//cABECERAS
				$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
				$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				$cabeceras .= "From: Portal Ingetec S.A. <grodrig@ingetec.com.co>" . "\r\n";

				//Envio al Director
				if ($mailDir != "") {
					$cualMail= trim($mailDir) . "@ingetec.com.co";
					mail($cualMail,$Asunto,$Descripcion,$cabeceras); 
				}
//				echo "Director-->" . $cualMail ;
				//Envio al Coordinador
				if ($mailCoord != "") {
					$cualMail= trim($mailCoord) . "@ingetec.com.co";
					mail($cualMail,$Asunto,$Descripcion,$cabeceras); 
				}
//				echo "Coordinador-->" . $cualMail ;

				//Lista de ordenadores del gasto para envio de mail
				$oSql="SELECT O.unidadOrdenador , U.email ";
				$oSql=$oSql." FROM GestiondeInformacionDigital.dbo.OrdenadorGasto O, ";
				$oSql=$oSql." HojaDeTiempo.dbo.Usuarios U ";
				$oSql=$oSql." WHERE O.unidadOrdenador = U.unidad ";
				$oSql=$oSql." AND O.id_proyecto = " . $miProyecto ;
				$oCursor = mssql_query($oSql);
				if ($oReg=mssql_fetch_array($oCursor)) {
					$cualMail = trim($oReg[email]) . "@ingetec.com.co";
					@mail($cualMail,$Asunto,$Descripcion, $cabeceras); 
				}

				//***
				
				//***Envio de mails		
				*/		
			} 
			else {
				echo ("<script>alert('Error durante la grabación');</script>");
			};
			echo ("<script>window.close();MM_openBrWindow('ProgProyectosActiv.php?cualProyecto=$miProyecto&cualActividad=$miActividad','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");	
		}
	}
}


?>
<html>
<head>
<title>Programaci&oacute;n de Proyectos</title>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" type="text/JavaScript">
<!--

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_validateForm() { //v4.0
  var i,p,q,nm,test,num,min,max,errors='',args=MM_validateForm.arguments;
  for (i=0; i<(args.length-2); i+=3) { test=args[i+2]; val=MM_findObj(args[i]);
    if (val) { nm=val.name; if ((val=val.value)!="") {
      if (test.indexOf('isEmail')!=-1) { p=val.indexOf('@');
        if (p<1 || p==(val.length-1)) errors+='- '+nm+' must contain an e-mail address.\n';
      } else if (test!='R') { num = parseFloat(val);
        if (isNaN(val)) errors+='- '+nm+' debe ser numérico.\n';
        if (test.indexOf('inRange') != -1) { p=test.indexOf(':');
          min=test.substring(8,p); max=test.substring(p+1);
          if (num<min || max<num) errors+='- '+nm+' debe ser un número entre '+min+' y '+max+'.\n';
    } } } else if (test.charAt(0) == 'R') errors += '- '+nm+' es obligatorio.\n'; }
  } if (errors) alert('Validación:\n'+errors);
  document.MM_returnValue = (errors == '');
}
//-->
</script>
<script language="JavaScript" type="text/JavaScript">
function compareFechas() { 
//alert(document.Form1.lFechaInicio.value);
//alert(document.Form1.lFechaFin.value);
	fecha1=new Date(document.Form1.lFechaInicio.value); 
	fecha2=new Date(document.Form1.lFechaFin.value); 

	diferencia = fecha1 - fecha2; 
//  	alert(diferencia);
   	if (diferencia > 0) {
   		alert ("La fecha inicial es MAYOR que la fecha de finalización, por favor realice la corrección.");
		document.Form1.lFechaFin.value = "";
		}
//      return 1; 
//   else if (diferencia < 0) 
//   		alert ("La fecha inicial es MENOR que la fecha de finalización ");
//      return -1; 
//   else 
//   	alert ("La fecha inicial es IGUAL que la fecha de finalización ");
//      return 0; 
}
</script>
</head>

<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="#EAEAEA">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TituloUsuario">Programación de Proyectos - Asignaci&oacute;n del recurso </td>
  </tr>
</table>


<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td bgcolor="#FFFFFF">
	  <form action="" method="post" name="Form1" onSubmit="MM_validateForm('valor','','RisNum');return document.MM_returnValue"  >
	  <table width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="25%" class="TituloTabla">Actividad</td>
    <td class="TxtTabla">
	<?
	echo $pActividad;
	?>
    <input name="miProyecto" type="hidden"  value="<? echo $cualProyecto; ?>">	<input name="miActividad" type="hidden" id="miActividad" value="<? echo $cualActividad; ?>"></td>
  </tr>
  <tr>
    <td class="TituloTabla">Fecha Inicial </td>
    <td class="TxtTabla">	
	<? echo date("M d Y ", strtotime($pFechaI)); 	?>
</td>
  </tr>
  <tr>
    <td class="TituloTabla">Fecha Final </td>
    <td class="TxtTabla">	
	<?
	echo $pFechaF; 
	?>
</td>
  </tr>
  <tr>
    <td colspan="2" class="TituloTabla"><img src="img/images/Pixel.gif" width="4" height="4"></td>
    </tr>
  <tr>
    <td class="TituloTabla">Valor Recurso </td>
    <td class="TxtTabla"><input name="valor" type="text" class="CajaTexto" id="valor" value="0" size="20">
      <input name="nombreActiv" type="hidden" id="nombreActiv" value="<? echo $pActividad; ?>"></td>
  </tr>
  <tr>
    <td colspan="2" align="right" class="TxtTabla"><input name="Submit" type="submit" class="Boton" value="Grabar"></td>
  </tr>
</table>
      </form>
  	</td>
  </tr>
</table>

</body>
</html>
