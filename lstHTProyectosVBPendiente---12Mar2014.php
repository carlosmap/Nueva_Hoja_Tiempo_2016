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

//Establecer la conexión a la base de datos
//$conexion = conectar();
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";

	$sqlLstProyectos = 'SELECT DISTINCT 
						B.nombre, B.codigo, B.cargo_defecto FROM VoBoFactuacionProyHT A, Proyectos B
						WHERE A.id_proyecto = B.id_proyecto 
						AND A.vigencia = '.$cualVigencia.' 
						AND A.mes = '.$cualMes.' 
						AND A.validaEncargado = 0'; 
	$qryLstProyectos = mssql_query($sqlLstProyectos);
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

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">.:: Proyectos pendientes por V.B.</td>

  </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0" >
  <tr>
    <td>  
      <table width="100%"  border="0" cellspacing="1" cellpadding="0" bgcolor="#FFFFFF">
        <tr class="TituloTabla">
          <td width="20%" >C&oacute;digo</td>
          <td >Nombre</td>
        </tr>
        <?
			while($rw=mssql_fetch_array($qryLstProyectos))
			{
		?>
        <tr class="TxtTabla">
          <td width="20%"><?= '['.trim($rw[codigo]).'.'.trim($rw[cargo_defecto]).']' ?></td>
          <td><?= strtolower(trim($rw[nombre])) ?></td>
        </tr>
        <?
			}
		?>
        <tr  class="TxtTabla">
          <td colspan="2" align="right" >
          	<input type="button" value="Cerrar" class="Boton" onClick="window.close();" />
          </td>
        </tr>
      </table>
  
    </td>
  </tr>
</table>


	     </td>
         </tr>
         </table>

</body>
</html>



<? mssql_close ($conexion); ?>	
