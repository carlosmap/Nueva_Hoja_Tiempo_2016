<script language="JavaScript" type="text/JavaScript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
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


//Cantidad de registros del formulario
if (trim($pCantReg) == "") {
	$pCantReg = 1;
}


if(trim($recarga) == "2")
{
	mssql_query("BEGIN TRANSACTION");


	$consecutivo=$usuario;
//echo "tipos " .$tipos."<br><br>";
	if($tipos==0)
	{
//echo "tipos " .$tipos."<br><br>";

		$sql_max="select MAX(consecutivo) as con from TrabajadoresExternos";
		$cur_max=mssql_query($sql_max);
		if($dato_max=mssql_fetch_array($cur_max))
			$consecutivo=$dato_max["con"]+1;

		$sql_inser_participante="insert into TrabajadoresExternos (consecutivo,nombre,apellidos,codTipoDoc,numDocumento,sexo,esNN,usuarioCrea,fechaCrea) values(";
		$sql_inser_participante=$sql_inser_participante." ".$consecutivo.", '".$nombre."' ,'".$apellido."' ,".$tipo_doc." ,".$num_doc." ,'".$sexo."' ,".$tipos." ,".$_SESSION["sesUnidadUsuario"].",getdate()";
		$sql_inser_participante=$sql_inser_participante."	)";
	}
/*
	if($tipos==1) //nn
	{
		$sql_inser_participante=$sql_inser_participante." ".$consecutivo.", NULL ,NULL ,0 ,NULL ,NULL ,".$tipos." ,".$_SESSION["sesUnidadUsuario"].",getdate()";
	}
*/


	$cur_partici=mssql_query($sql_inser_participante);
//	echo "<br><br>".$sql_inser_participante." --** ".mssql_get_last_message();

	if(trim($cur_partici)!="")
	{
		$sql_in_part_ext="insert into ParticipantesExternos (id_proyecto,id_actividad,consecutivo,estado,id_categoria,salario,usuarioCrea,fechaCrea) values(";
		if(trim($DI)!="")
		{
			$id_ac=$DI;
			if(trim($AC)!="")
			{
				$id_ac=$AC;
			}
		}
	
		$sql_in_part_ext=$sql_in_part_ext." ".$cualProyecto." ,".$id_ac.", ".$consecutivo." ,'A' ,".$categoria.",".$salario." , ".$_SESSION["sesUnidadUsuario"].",getdate()";
		$sql_in_part_ext=$sql_in_part_ext."	)";
		$cur_in_par=mssql_query($sql_in_part_ext);

//		echo "<br><br>".$sql_in_part_ext." --** ".mssql_get_last_message();
	}
	if(trim($cur_in_par)!="" )
	{
		mssql_query("COMMIT TRANSACTION");
		echo "<script>alert('La operaci\xf3n se realiz\xf3 con exito')</script>";
	}
	else
	{
		mssql_query(" ROLLBACK TRANSACTION ");
		echo "<script>alert('Error en la operaci\xf3n')</script>";	
	}

	echo ("<script>window.close();MM_openBrWindow('htPlanProyectos02.php?cualProyecto=".$cualProyecto."&participante=1','winHojaTiempo','toolbar=yes,scrollbars=yes,resizable=yes,width=960,height=700');</script>");
}

if(!isset($tipos))
{
	$tipos=1;
}
?>

<html>
<head>
<script language="JavaScript" type="text/JavaScript">



var nav4 = window.Event ? true : false;
function acceptNum(evt)
{   
	var key = nav4 ? evt.which : evt.keyCode;   
	return (key <= 13 || (key>= 48 && key <= 57));
}

function mostrar()
{
//alert(document.Form1.tipos.value);
    //if (eval("document.form1."+aplica1+"["+0+"].checked"))     
    if ((document.Form1.tipo[0].checked))
    {

		document.Form1.tipos.value="1";
        document.getElementById('tabla').getElementsByTagName('tr')[0].style.display='table-row';
        document.getElementById('tabla').getElementsByTagName('tr')[1].style.display='none';
        document.getElementById('tabla').getElementsByTagName('tr')[2].style.display='none';
        document.getElementById('tabla').getElementsByTagName('tr')[3].style.display='none';
        document.getElementById('tabla').getElementsByTagName('tr')[4].style.display='none';
        document.getElementById('tabla').getElementsByTagName('tr')[5].style.display='none';
        document.getElementById('tabla').getElementsByTagName('tr')[6].style.display='none';

        
    }    
    if ((document.Form1.tipo[1].checked))
    {
		document.Form1.tipos.value="0";
        document.getElementById('tabla').getElementsByTagName('tr')[0].style.display='none';
        document.getElementById('tabla').getElementsByTagName('tr')[1].style.display='table-row';
        document.getElementById('tabla').getElementsByTagName('tr')[2].style.display='table-row';
        document.getElementById('tabla').getElementsByTagName('tr')[3].style.display='table-row';
        document.getElementById('tabla').getElementsByTagName('tr')[4].style.display='table-row';
        document.getElementById('tabla').getElementsByTagName('tr')[5].style.display='table-row';
        document.getElementById('tabla').getElementsByTagName('tr')[6].style.display='table-row';

            //alert("ingreso 2");
    }    

}

function envia2()
{
    var msg="";
    if (document.Form1.tipo[0].checked)
    {
        if (document.Form1.usuario.value=="")
        {
            msg="Seleccione un usuario \n";
        }     
    }
    
    if (document.Form1.tipo[1].checked)
    {
        if (document.Form1.tipo_doc.value=="")
        {
            msg="Seleccione el tipo de documento \n";
        }
        if (document.Form1.num_doc.value=="")
        {
            msg=msg+"Ingrese el numero de documento \n";
        }        
        
        if (document.Form1.nombre.value=="")
        {
            msg=msg+"Ingrese el nombre \n";
        }
        if (document.Form1.apellido.value=="")
        {
            msg=msg+"Ingrese el apellido \n";
        }        
        if (document.Form1.sexo.value=="")
        {
            msg=msg+"Seleccione el sexo \n";
        }                
            
    }
    if (document.Form1.categoria.value=="")
    {
            msg=msg+"seleccione una categoria \n";
    }
    if (document.Form1.salario.value=="")
    {
            msg=msg+"Especifique el salario \n";
    }       

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
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" bgcolor="E6E6E6">    

<table width="100%" border="0" cellpadding="0" cellspacing="0">
<form action="" method="post"  name="Form1">
  <tr>
    <td class="TituloUsuario">Participantes Externos </td>
  </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td>     
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">        
        <tr>
          <td class="TituloTabla" width="20%" >Tipo de paricipante</td>
          <td class="TxtTabla" width="15%" ><input type="radio" name="tipo" id="tipo" checked value="1" onClick="mostrar()" <? if($tipos==1) { echo "checked"; } ?> > NN </td>

          <td class="TxtTabla">
            <input type="radio" name="tipo" id="tipo" value="0" onClick="mostrar()" <? if($tipos==0) { echo "checked"; } ?> > Por contratar            
          </td>          
        </tr>
      </table>
	</td>
	</tr>
	<tr>
	<td>
      <table  width="100%"  border="0" cellspacing="1" cellpadding="0" id="tabla">

        <tr>
          <td class="TituloTabla">Usuario</td>
          <td class="TxtTabla"><select name="usuario" id="usuario"   class="CajaTexto"  >
            <option value=""> </option>
<?php
			$sql_usu_nn="select * from TrabajadoresExternos where esNN=1";

			$cur_nn=mssql_query($sql_usu_nn);
			while($datos_nn=mssql_fetch_array($cur_nn))
			{
				$sel="";
				if($usuario==$datos_nn["consecutivo"])
					$sel="selected";
				echo "<option value='".$datos_nn["consecutivo"]."' ".$sel." >".$datos_nn["nombre"]."".$datos_nn["apellidos"]."</option>";
			}

?>			            
          </select></td>
        </tr>
        <tr>
            <td class="TxtTabla" colspan="2" ></td>            
        </tr>
        <tr>
            <td class="TituloTabla">Tipo de documento</td>
            <td class="TxtTabla"  ><select name="tipo_doc" id="tipo_doc" class="CajaTexto" >
                <option value="">Seleccione tipo</option>
<?php
					$sql_doc="select * from TipoDocumento where codTipoDoc <> 0";
					$cur_doc=mssql_query($sql_doc);
					while($datos_dco=mssql_fetch_array($cur_doc))
					{
						$sel="";
						if($tipo_doc==$datos_dco["codTipoDoc"])
							$sel="selected";
						echo "<option value='".$datos_dco["codTipoDoc"]."' ".$sel." >".$datos_dco["tipoDoc"]."</option>";
					}
?>
            </select></td>
        </tr>
        
        <tr>
            <td class="TituloTabla">Numero de documento</td>
            <td class="TxtTabla" >
                <input type="text" name="num_doc" id="num_doc"  class="CajaTexto"  onkeyPress="return acceptNum(event)" value="<?php echo $num_doc; ?>" >
            </td>
        </tr>
        <tr>
            <td class="TituloTabla" >Nombres</td>
            <td class="TxtTabla" >
                <input type="text" name="nombre" id="nombre" class="CajaTexto" value="<?php echo $nombre; ?>"  >
            </td>
        </tr>
        <tr>
            <td class="TituloTabla"> Apellidos</td>
            <td class="TxtTabla" >
                <input type="text" name="apellido" id="apellido" class="CajaTexto" value="<?php echo $apellido; ?>"  >
            </td>
        </tr>
        
        <tr>
            <td class="TituloTabla" >Sexo</td>
            <td class="TxtTabla" >
                <select name="sexo" id="sexo" class="CajaTexto">
                        <option value="" >Seleccione sexo</option>
                        <option  value="M" <?php if($sexo=="M") { echo "selected"; } ?> >Masculino</option>
                        <option  value="F" <?php if($sexo=="F") { echo "selected"; } ?> >Femenino</option>
                </select>
            </td>
        </tr>   
        
        <tr>
          <td width="20%" class="TituloTabla">Categoria</td>
          <td class="TxtTabla">
            <?php
				$sql_cat="select * from Categorias order by nombre";                
				$cur_cat=mssql_query($sql_cat);
				
            ?>
            <select name="categoria" id="categoria" class="CajaTexto">
                <option value="">Seleccione categoria</option>    
			<?php
				while($datos_cat=mssql_fetch_array($cur_cat))
				{
						$sel="";
						if($categoria==$datos_cat["id_categoria"])
							$sel="selected";

					echo "<option value='".$datos_cat["id_categoria"]."' ".$sel." >".$datos_cat["nombre"]."</option>";
				}
			?>
            
          </select> </td>
        </tr>
        <tr>
            <td class="TituloTabla">Salario</td>
            <td class="TxtTabla" ><input type="text" name="salario" id="salario" class="CajaTexto" onKeyPress="return acceptNum(event)" value="<?php echo $salario; ?>"  ></td>
        </tr>
        <tr>
            <td class="TituloTabla" colspan="2" ></td>
        </tr>
                
        <tr>
            <td class="TituloUsuario" colspan="2" >Actividad</td>
        </tr>
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
                            if($cualACtividad==$datos_sql_AC["id_actividad"])
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
            
            <td colspan="2" class="TxtTabla" align="right" > <input type="button" name="Grabar" value="Grabar" class="Boton" onClick="envia2()" >
				<input type="hidden" name="recarga" id="recarga" value=1 >
				<input type="hidden" name="tipos" id="tipos" value="<?=$tipos; ?>" >
			</td>
        </tr>

<?php 
	
//	if(!isset($recarga))
	{
		echo "<script language='javascript'> mostrar(); </script>";
	}
?>
<?php mssql_close ($conexion); ?>	
</form> 
      </table>



</body>
</html>


