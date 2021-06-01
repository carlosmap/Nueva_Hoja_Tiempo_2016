<?php
session_start();
//include("../verificaRegistro2.php");
//include('../conectaBD.php');

//Establecer la conexión a la base de datos
//$conexion = conectar();
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";


//Cantidad de registros del formulario
if (trim($pCantReg) == "") {
	$pCantReg = 1;
}


if(trim($recarga) == "2"){
	#	#######################
	#	Realiza la grabación tiempo y recursos de la actividad.
	$msgGraba = "";
	$msgNOGraba = "";
	$s = $r = 1;
	$pCantReg = $registros - 1;
	while ($r <= $pCantReg) {
		########
		#	Asignar valores de las cajas de texto
		$fecha1 = "fechaI_".$r;
		$fecha2 = "fechaF_".$r;
		$idAct = "idAct".$r;
		$r++;
		#	COMPARAR FECHAS
		#	select datediff( day, '2012/12/01', '2011/12/10')
		$sql = "UPDATE hojadetiempo.dbo.TMPactividadesHT2 SET 
					 fecha_inicio = '".${$fecha1}."', 
					 fecha_fin = '".${$fecha2}."'
				WHERE id_actividad = ".${$idAct};
		#echo $sql."<br />";
		$qry = mssql_query( $sql );
		if( $qry ){			
			$sqlAct = "Select nombre from TMPactividadesHT2 WHERE id_actividad = ".${$idAct};	
			$qryAct = mssql_fetch_array( mssql_query( $sqlAct ) );
			$msgGraba .= $qryAct[nombre].". ";
		}

	}
	
	if( trim($msgGraba) != "" )
		echo ("<script>alert('Se grabaron las siguientes Actividades: ".$msgGraba." ');</script>"); 
	else 
		echo ("<script>alert('Error durante la grabación');</script>");

	echo ("<script>window.close();MM_openBrWindow('pnfProgProyectos01.php','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=950,height=600');</script>");
}

?>
 <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<script language="JavaScript" type="text/JavaScript">
function envia1(){ 
	document.Form1.recarga.value="1";
	document.Form1.submit();
}

function envia2(){ 
	var v1, v2, vd, vm, i, CantCampos, msg1, msg2, msg3, msg4, msg5, msg6, msg7, msg8, msg9, msg10, msg11, msg12, msg13, msg14, msg15, mensaje;
	var expresion, campo;
	var d, m;
	var fecha;// = new Array();
	
	//expresion =  /^\d[1-31]\/\d[1-12]\/\d{2,4}$/;
	//	correo electronico /[\w-\.]{3,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/
	v1 = v2 = d = m = vd = vm = 0;
	msg1 = msg2 = msg3 = msg4 = msg5 = '';
	mensaje = '';	
	CantCampos = document.getElementById('registros').value - 1;	
	
	//Valida que el campo Nombre no esté vacio
	for ( i = 1; i <= CantCampos; i++ ) {
		if( document.getElementById('fechaF_'+i).value == '' ){
			v1 = 1;
			msg1 = "Alguno de los campos esta vacío.\n";
		}
		//	VALIDA FECHA INICIAL
		fecha = document.getElementById('fechaI_'+i).value.split('/');
		d = fecha[0];
		m = fecha[1];
		// VALIDA EL DIAS
		if( d > 31 ){
			vd = 1;
			msg3 = 'El dia no esta dentro de los rangos';
		}
		//	VALIDA EL MES
		if( m > 12 ){
			vm = 1;
			msg4 = 'El mes no esta dentro de los rangos';
		}
		//	VALIDA FECHA FINAL
		var vf = 0;
		fecha = document.getElementById('fechaF_'+i).value.split('/');
		d = fecha[0];
		m = fecha[1];

		// VALIDA LOS DIAS
		if( d > 31 ){
			vd = 1;
			msg3 = 'El dia no esta dentro de los rangos.\n';
		}
		//	VALIDA LOS MESES
		if( m > 12 ){
			vm = 1;
			msg4 = 'El mes no esta dentro de los rangos.\n';
		}
	}	

//Si todas las validaciones fueron correctas, el formulario hace submit y permite grabar
	if ( v1 == 0  && v2 == 0 && vd == 0 && vm == 0 ){
		document.Form1.recarga.value="2";
		document.Form1.submit();
		//*
	}
	else {
		mensaje = msg1 + msg2 + msg3 + msg4 ;//+ msg5 + msg6 + msg7 + msg8 + msg9 + msg10 + msg11 + msg12 + msg13 + msg14 + msg15;
		alert ( mensaje );
	}
	//*/
}
</script>
<script language="JavaScript" src="calendar.js"></script>

<title>Investigaciones Geot&eacute;cnicas</title>
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">
<form action="" method="post"  name="Form1">

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td class="TituloUsuario">Divisi&oacute;n / Actividad</td>
  </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><img src="../images/Pixel.gif" width="4" height="2"></td>
        </tr>
      </table>  
      <?
	  	$sql = "select * from HojaDeTiempo.dbo.TMPactividadesHT2 act Where id_proyecto = ".$proy;
		$qry = mssql_query( $sql );
		$r = 1;
	  ?>    
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr class="TituloTabla2">
            <td width="3%">ID</td>
            <td width="5%">Macroactividad</td>
            <td width="25%">Lote de control / Lote de trabajo / Actividad Vs Divisi&oacute;n </td>
            <td width="15%">Responsable</td>
            <td width="7%">Valor Presupuestado </td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            <td width="5%">Fecha  Inicio </td>
            <td width="5%">Fecha  Fin </td>
            <td width="5%">Valor del recurso </td>
          </tr>
          <?	while( $row = mssql_fetch_array( $qry ) ){	?>
          <tr class="TxtTabla">
            <td width="3%"><?=	$row[id_actividad]	?><input type="hidden" name="idAct<?= $r ?>" id="idAct<?= $r ?>" value="<?= $row[id_actividad]	?>" /></td>
            <td width="5%"><?=	$row[macroactividad]	?></td>
            <td width="25%" class="TxtTabla">
				<?php	
                    switch( $row[nivel] ){
                        case 1:
                            $tabla = "<tr class='TxtTabla'> <td>  <b>".$row[nombre]."</b> </td></tr>";
                        break;	
                        
                        case 2:
                            $tabla = "<tr class='TxtTabla'> <td width='25px'>&nbsp;</td> <td>".$row[nombre]."</td></tr>";
                        break;	
                        
                        case 3:
                            $tabla = "<tr class='TxtTabla'> <td width='25px'>&nbsp;</td> <td width='25px'>&nbsp;</td> <td>".$row[nombre]."</td> </tr>";
                        break;	
                        
                        case 4:
                            $tabla = "<tr class='TxtTabla'> <td width='25px'>&nbsp;</td> <td width='25px'>&nbsp;</td> <td width='25px'>&nbsp;</td> <td>".$row[nombre]."</td> </tr>";
                        break;						
                    }
                ?>
                <table width="100%">
                	<?= $tabla ?>
                    <!--	<tr><td><?= $row[nombre]	?></td></tr>	-->
                </table>
            </td>
            <td width="15%">
            <!-- <strong>[2964] Alberto Marulanda </strong>            -->
            <?
				$sqlUsuario = "select ( nombre + ' ' + apellidos ) Nombres from hojadetiempo.dbo.usuarios where unidad = ".$row[id_encargado];
				$qryUsuario = mssql_fetch_array( mssql_query( $sqlUsuario ) );
				#echo $sqlUsuario;				
			?>
            	
            	<strong><?= "[ ".$row[id_encargado]." ] " ?></strong><?= ucwords( strtolower( $qryUsuario[Nombres] ) ) ?>
            </td>
            <td width="7%"><strong>$ 250.000.000 </strong></td>
            <td width="1%" class="TituloTabla2">&nbsp;</td>
            
            
            <td width="5%" align="center" class="TxtTabla">
            <!--	echo date("d-M-Y ", strtotime( $row['fechaAsignaUsuLab'] ) )	-->
            <? 
				if( $row[fecha_inicio] != "" )
					$fecha1 = date("d/m/Y ", strtotime( $row[fecha_inicio] ) );
				else
					$fecha1 = "";
				#$fecha2 = date( 'd/m/Y', $row[fecha_fin] )   fecha_inicio, fecha_fin, 
			?>
            <input name="fechaI_<?= $r ?>" type="text" class="CajaTexto" id="fechaI_<?= $r ?>" value="<?= $fecha1 ?>" size="15">
           	dd/mm/yyyy	
           	 <!--<a href="javascript:cal1.popup();"><img src="../portal/images/cal.gif" alt="" width="16" height="16" border="0"></a>	-->
            </td>
            <td width="5%" align="center" class="TxtTabla">
            <? 
				if( $row[fecha_fin] != "" )
					$fecha2 = date("d/m/Y ", strtotime( $row[fecha_fin] ) );
				else
					$fecha2 = "";
			?>
            <input name="fechaF_<?= $r ?>" type="text" class="CajaTexto" id="fechaF_<?= $r ?>" value="<?= $fecha2 ?>" size="15">
            dd/mm/yyyy
            <!--<a href="javascript:cal1.popup();"><img src="../portal/images/cal.gif" alt="" width="16" height="16" border="0"></a>	--></td>
            <td width="5%" valign="top" class="TxtTabla"><input name="textfield3" type="text" class="CajaTexto"></td>
          </tr>
<script language="JavaScript" type="text/JavaScript">
	 //var cal1 = new calendar2(document.forms['Form1'].elements['fechaI_'+<?= $r ?>]);
	 var cal1 = new calendar2( document.getElementById('fechaI_'+<?= $r ?>) ); // document.forms['Form1'].elements['fechaI_'+<?= $r ?>]);
	 cal1.year_scroll = true;
	 cal1.time_comp = false;
	 
	 var cal2 = new calendar2( document.getElementById('fechaF_'+<?= $r ?>) ); // document.forms['Form1'].elements['fechaF_'+<?= $r ?>]);
	 cal2.year_scroll = true;
	 cal2.time_comp = false;
</script>

          <?
		  		$r++;
		  	}
		  ?>
        </table>
        <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td align="right" class="TxtTabla">
  		    <input name="recarga" type="hidden" id="recarga" value="1">
            <input name="registros" type="hidden" id="registros" value="<?= $r ?>">
  		    <input name="Submit" type="button" class="Boton" value="Guardar" onClick="envia2()" ></td>
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
