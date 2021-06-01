<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
function cerrar()
{
	window.close();MM_openBrWindow('htPlanProyectos02.php?cualProyecto=<?=$cualProyecto; ?>&participante=1&aa=<?=$aa; ?>','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=960,height=700');
}
//-->
</script>
<?php
session_start();
//Despliega las unidades y los nombres de las personas que el usuario actual tiene que revisar
include "funciones.php";
include "validacion.php";
include "validaUsrBd.php";


//echo "Consecutiv: ".$id_consecutivo."<br>";
if(trim($recarga) == "2")
{
	mssql_query("BEGIN TRANSACTION");

		if(trim($DI)!="")
		{
			$id_ac=$DI;
			if(trim($AC)!="")
			{
				$id_ac=$AC;
			}
		}
//	$consecutivo=$usuario;
//echo "Consecutiv 2: ".$id_consecutivo." Tipos ".$tipos."<br>";
	//SE CONSULTA SI EL PARTICIPANTE YA FUE ASIGNADO EN LA ACTIVIDAD DE LA EDT
	$sql_verif="select * from ParticipantesExternos where id_proyecto=".$cualProyecto." and id_actividad=".$id_ac." and consecutivo=".$conse;
	$cur_veri=mssql_query($sql_verif);	
//echo $sql_verif." <br> ".mssql_get_last_message()." ***** ".mssql_num_rows($cur_veri)."<br><br>";
	if( (0< (int) mssql_num_rows($cur_veri)) )
	{
		$sql_act="select * from Actividades where id_proyecto=".$cualProyecto." and id_actividad=".$id_ac;
		$cur_act=mssql_query($sql_act);	
		if($datos_Ac=mssql_fetch_array($cur_act))
		{
			$acttivi=$datos_Ac["nombre"];	$macro=$datos_Ac["macroactividad"];
		}	

		echo "<script type='text/javascript' > alert('El usuario ya se encuentra asignado como participante en la actividad ".$macro." ".$acttivi."'); </script> ";
	}
	else
	{
				$sql_in_part_ext="insert into ParticipantesExternos (id_proyecto,id_actividad,consecutivo,estado,id_categoria,salario,usuarioCrea,fechaCrea) values(";		
				$sql_in_part_ext=$sql_in_part_ext." ".$cualProyecto." ,".$id_ac.", ".$conse." ,'A' ,".$categoria.",".$salario." , ".$_SESSION["sesUnidadUsuario"].",getdate()";
				$sql_in_part_ext=$sql_in_part_ext."	)";
				$cur_in_par=mssql_query($sql_in_part_ext);
		
//				echo "<br><br>".$sql_in_part_ext." --** ".mssql_get_last_message();

			if(trim($cur_in_par)!="" )
			{
				mssql_query("COMMIT TRANSACTION");
				echo "<script>alert('La operación se realizó con exito')</script>";
			}
			else
			{
				mssql_query(" ROLLBACK TRANSACTION ");
				echo "<script>alert('Error en la operación')</script>";	
			}


	}
}

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>.:: Planeación de Proyectos</title>

<script language="JavaScript" type="text/JavaScript">

function envia2()
{
    var msg="";

	if(document.Form1.LC.value=="")
	{
		msg = msg+'Seleccione un lote de control \n';
		v2='n';
	}
	if(document.Form1.LT.value=="")
	{
		msg =msg+ 'Seleccione un lote de trabajo \n';
		v2='n';
	}
	if(document.Form1.DI.value=="")
	{
		msg =msg+ 'Seleccione una divisi\xf3n \n';
		v2='n';
	}	

	if(msg!="")	
	{
    	alert(msg);
	}
	else
	{
		document.Form1.recarga.value=2;
		document.Form1.submit();
	}
}

</script>
<title>.:: Planeaci&oacute;n de Proyectos</title>
</head>

<body bgcolor="#EAEAEA" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<?
	#	Trae la información del coordinador y el director del proyecto
	$sqlDrCr = "select cr.nombre nCr, cr.apellidos aCr, dr.nombre nDr, dr.apellidos aDr from Proyectos pr, Usuarios cr, Usuarios dr
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
    <td width="25%" valign="top"><?= $qryDrCr[nDr]." ".$qryDrCr[aDr] ?></td>
    <td width="25%" valign="top" ><?= $qryDrCr[nCr]." ".$qryDrCr[aCr] ?></td>
    <td width="25%" >
    <?
		$sqlPr = "select u.nombre, u.apellidos, u.unidad from Programadores pr, Usuarios u
				  where id_proyecto = ".$cualProyecto." and pr.unidad = u.unidad";
		$qryPr = mssql_query( $sqlPr );
		while( $rw = mssql_fetch_array( $qryPr ) ){
			echo $rw[nombre]." ".$rw[apellidos]."<br />";
		}
	?>
    </td>
    <td width="25%" valign="top" >
    <?
		$sqlPr = "select u.nombre, u.apellidos, u.unidad from GestiondeInformacionDigital.dbo.OrdenadorGasto pr, Usuarios u
				  where id_proyecto = ".$cualProyecto." and pr.unidadOrdenador = u.unidad";
		$qryPr = mssql_query( $sqlPr );
		while( $rwg = mssql_fetch_array( $qryPr ) ){
			echo $rwg[nombre]." ".$rw[apellidos]."<br />";
		}
	?>
    </td>
  </tr>
</table>

    <table width="100%"  border="0" cellspacing="0" cellpadding="0">
	<form name="Form1" id="Form1" method="post" action="">
      <tr>
        <td bgcolor="#FFFFFF"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
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


		//	$sqlActResponsable = "select *, ( nombre+' '+ apellidos) as NombresApellidos  from TrabajadoresExternos  where consecutivo=".$conse;
			$sqlActResponsable = "select top(1)  *, ( TrabajadoresExternos.nombre+' '+ TrabajadoresExternos.apellidos) as NombresApellidos, Categorias.nombre as categoria
				,TrabajadoresExternos.consecutivo, ParticipantesExternos.salario
				 from TrabajadoresExternos 
				inner join ParticipantesExternos on TrabajadoresExternos.consecutivo=ParticipantesExternos.consecutivo
				inner join Categorias on ParticipantesExternos.id_categoria=Categorias.id_categoria
				  where TrabajadoresExternos.consecutivo=".$conse;

			
			$qryActResponsable = mssql_query( $sqlActResponsable );
#echo $sqlActResponsable."<br />".mssql_get_last_message();
		?>
        <table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr class="TituloTabla2">
            <td width="8%">Consecutivo</td>
            <td width="20%">Nombre</td>
            <td width="20%">Categoria</td>
            <td width="20%">Salario</td>

            <!-- <td width="1%">&nbsp;</td> -->
          </tr>
          <?	$row = mssql_fetch_array( $qryActResponsable ); ?>
          <tr class="TxtTabla">
            <td valign="top"><?= $row[consecutivo] ?></td>
            <td valign="top">
			<?= strtoupper($row[NombresApellidos]) ?>
            <input type="hidden" name="categoria" id="categoria" value="<?= $row[id_categoria] ?>" />
            <input type="hidden" name="salario" id="salario" value="<?=$row[salario] ?>" />

			</td>
            <td valign="top"><?= $row[categoria] ?></td>
            <td valign="top"><? echo "$ ".number_format( $row[salario] , "2", ",", "." ) ?></td>
          </table>
                  <table width="100%"  border="0" cellspacing="1" cellpadding="0">

          <tr class="TxtTabla">
            <td valign="top" colspan="2" >&nbsp;</td>
            </tr>
	</table>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
         <tr class="TxtTabla">
                <td valign="top" colspan="1" >
    
                <table width="100%" border="0">
                  <tr>
                    <td class="FichaInAct"><a href="upparticipante_ex.php?cualProyecto=683&conse=<?=$conse; ?>&aa=<?=$aa; ?>" class="FichaInAct1" >Activar-Desactivar Actividades</a></td>
                    <td class="FichaAct">Asociar Actividades</td>
                    <td width="60%" ></td>
                  </tr>
                </table></td>
                </tr>
		</table>
        <table width="100%"  border="0" cellspacing="1" cellpadding="0"  bgcolor="#FFFFFF">
        <tr>
            <td class="TituloUsuario" colspan="2" >Actividad</td>
        </tr>
<!--
              <tr>
                <td colspan="2" class="TxtTabla">Seleccione la actividad, en la que desea asociara al participante.</td>
                </tr>
-->
              <tr>
          <td class="TituloTabla">Lote de control </td>

<?php

					//si la variable no esta definida, es por que es la primera vez que se carga la pagina, y asi, el selecct quedara por defecto, con el LC al cual pertenece el LT seleccionado por el usuario, en la
					//ventana anterior, la cual trahe como parametro el valor del LC, perteneciente al LT
					if(!isset($cualLC2))
					{
							$cualLC2=$cualLC;
					}
					//Cargamos el valor del LT, que se trahe como parametro, esta sentencia es verdadera, cuando se accede a la pagina por primera vez
					if(!isset($cualLT2))
					{
							$cualLT2=$cualLT;
					}
					//validamos si la variable si la variable que se forma del  select, esta definida, si no lo esta, es por que es la primera vez que se accede a la pagina
					//entonces  cargamos la variable para cargar el select, del los lotes de trabajo 
					if(!isset($LC))
					{
							$LC=$cualLC;
					}
					if(!isset($LT))
					{
							$LT=$cualLT;
					}

?>
          <td class="TxtTabla"><select name="LC" id="LC"   class="CajaTexto"  onChange="document.Form1.submit();">
            <option value="">Seleccione Lote de Control </option>
            <?php
					//consultamos los lotes de control asociados a la EDT del proyecto
					$sql_LC="SELECT  id_actividad,nombre,macroactividad FROM Actividades WHERE id_proyecto = ".$cualProyecto." and nivel = 1";
					$sql_LC=$sql_LC." order by cast(reverse(substring(reverse(macroactividad),1,charindex('C', reverse(macroactividad))-1)) as int)";
					$cursor_sql_LC=mssql_query($sql_LC);
					while($datos_sql_LC=mssql_fetch_array($cursor_sql_LC))
					{
						//pertmite determinar el LC del LT, seleccionado  por el usuario en la pagina, y seleccionarlo en la lista de forma automatica, esto en el momento de abrir la pagina
						//y despues, se seleccionara el que el usuario escoga en el select
						if($cualLC2==$datos_sql_LC["id_actividad"])
						{
							$cualLC2=-1;  //modifiacmos el valor, para que al momento de seleccionar otro elemento de la lista, este me lo deje  seleccionado
							$select="selected";
							$LC_selec=$datos_sql_LC["macroactividad"];
						}
						else
						{
							if($LC==$datos_sql_LC["id_actividad"])
							{
								$select="selected";
								$LC_selec=$datos_sql_LC["macroactividad"];
							}
						}
						echo "<option value=".$datos_sql_LC["id_actividad"]." $select >".$datos_sql_LC["macroactividad"]." - ".$datos_sql_LC["nombre"]."</option>";
						$select="";
					}
 ?>
          </select></td>
        </tr>
        <tr>
          <td class="TituloTabla">Lote de trabajo </td>
          <td class="TxtTabla"><select name="LT" id="LT"   class="CajaTexto"  onChange="document.Form1.submit();">
            <option value="">Seleccione Lote de Trabajo</option>
            <?php
					//consultamos los lotes de control asociados a el lote de control seleccionado
					$sql_LT="SELECT  id_actividad,nombre,macroactividad FROM Actividades WHERE id_proyecto =".$cualProyecto." and dependeDe=".$LC." and nivel = 2";
					$sql_LT=$sql_LT." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
					$cursor_sql_LT=mssql_query($sql_LT);
					while($datos_sql_LT=mssql_fetch_array($cursor_sql_LT))
					{

						//pertmite determinar el LT seleccionado por el usuario en la pagina, y seleccionarlo en la lista de forma automatica, esto en el momento de abrir la pagina
						//y despues, se seleccionara el que el usuario escoga en el select
						if($cualLT2==$datos_sql_LT["id_actividad"])
						{
							$cualLT2=-1;  //modifiacmos el valor, para que al momento de seleccionar otro elemento de la lista, este me lo deje  seleccionado
							$select2="selected";
							$LT_selec=$datos_sql_LT["macroactividad"];
						}
						else
						{
							if($LT==$datos_sql_LT["id_actividad"])
							{
								$select2="selected";
								$LT_selec=$datos_sql_LT["macroactividad"];
							}
						}
						echo "<option value=".$datos_sql_LT["id_actividad"]." $select2>".$datos_sql_LT["macroactividad"]." - ".$datos_sql_LT["nombre"]."</option>";
						$select2="";
					}
 ?>
          </select></td>
        </tr>
        <tr>
            <td class="TituloTabla">Divisi&oacute;n</td>
            <td class="TxtTabla"><select name="DI" id="DI"   class="CajaTexto"  onChange="document.Form1.submit();">
              <option value="">Seleccione Divisi&oacute;n</option>
              <?php
					$divi_selec=""; //almacenamos la division (Hoja de tiempo) correspondiente a la Division (EDT) asociada al lote de trabajo, para almacenarla en el campo id_division, y asi referenciar las actividades por division
					//consultamos las divisiones  asociados al lote de trabajo
					$sql_DI="SELECT  id_actividad,upper(nombre) as nombre,macroactividad,id_division FROM Actividades WHERE id_proyecto = ".$cualProyecto."  and dependeDe=".$LT." and actPrincipal=".$LC." and nivel = 3";
					$sql_DI=$sql_DI." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int)";
					$cursor_sql_DI=mssql_query($sql_DI);
					while($datos_sql_DI=mssql_fetch_array($cursor_sql_DI))
					{
						//pertmite determinar la division, seleccionada  por el usuario en la pagina, y seleccionarlo en la lista de forma automatica, esto en el momento de abrir la pagina
						//y despues, se seleccionara el que el usuario escoga en el select
						if($cualLDI==$datos_sql_DI["id_actividad"])
						{
							$cualLDI=-1;  //modifiacmos el valor, para que al momento de seleccionar otro elemento de la lista, este me lo deje  seleccionado
							$select="selected";
							$DI_selec=$datos_sql_DI["macroactividad"];

							$divi_selec=$datos_sql_DI["id_division"]; //almacenamos el nombre de la division, para utilizarlo en el momento de traher lo responsalbes, ya que solo se mostraran los poertenecientes a la division seleccionada
						}
						else
						{
							if($DI==$datos_sql_DI["id_actividad"])
							{
								$select="selected";
								$DI_selec=$datos_sql_DI["macroactividad"];
								$divi_selec=$datos_sql_DI["id_division"];//almacenamos el id de la division, para utilizarlo en el momento de traher lo responsalbes, ya que solo se mostraran los poertenecientes a la division seleccionada
							}
						}
						echo "<option value=".$datos_sql_DI["id_actividad"]." $select >".$datos_sql_DI["macroactividad"]." - ".$datos_sql_DI["nombre"]."</option>";
						$select="";
					}
 ?>
            </select></td>
        </tr>
        <tr>
            <td class="TituloTabla">Actividad</td>
            <td class="TxtTabla"><select name="AC" id="AC"   class="CajaTexto" >
   					<option value="" >Seleccione Actividad</option>
              <?php
					if((trim($DI)!="")and(trim($LC)!=""))
					{
                        $AC_selec=""; 
						//consultamos las actividades asociadas a la division seleccionada
                        $sql_AC="SELECT  id_actividad,nombre,macroactividad,id_division FROM Actividades WHERE id_proyecto = ".$cualProyecto."  and dependeDe=".$DI." and actPrincipal=".$LC." and nivel = 4  ";
						$sql_AC=$sql_AC." order by cast(reverse(substring(reverse(macroactividad),1,charindex('.', reverse(macroactividad))-1)) as int) ";
                        $cursor_sql_AC=mssql_query($sql_AC);
                        while($datos_sql_AC=mssql_fetch_array($cursor_sql_AC))
                        {
                            //pertmite determinar la actividad, seleccionada  por el usuario en la pagina, y seleccionarlo en la lista de forma automatica, esto en el momento de abrir la pagina
                            //y despues, se seleccionara el que el usuario escoga en el select
                            if($AC==$datos_sql_AC["id_actividad"])
                            {
	                         	$select="selected";
                            }

                            echo "<option value=".$datos_sql_AC["id_actividad"]." $select >".$datos_sql_AC["macroactividad"]." - ".$datos_sql_AC["nombre"]."</option>";
                            $select="";
                        }
					}
     ?>
            </select></td>
        </tr>

        <tr>
            
            <td colspan="2" class="TxtTabla" align="right" >
									<input type="button" name="Cerrar" value="Cerrar Ventana" class="Boton" onClick=" return cerrar();" >
									 <input type="button" name="Grabar" value="Grabar" class="Boton" onClick="envia2()" >

				<input type="hidden" name="recarga" id="recarga" value=1 >
			</td>
        </tr>

          </table>
</body>
</html>