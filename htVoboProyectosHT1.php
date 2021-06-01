<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<LINK REL="stylesheet" HREF="css/estilo.css" TYPE="text/css">
<title>Documento sin título</title>
</head>

<body class="TxtTabla">
<table  width="100%"  border="0" cellpadding="0" cellspacing="1">
  <tr>
    <td height="2" align="center" class="TxtTabla"><table width="30%" border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
      <tr>
        <td class="TituloUsuario" height="2" colspan="2" >Criterios de consulta </td>
      </tr>
      <tr class="TxtTabla" >
        <td class="TituloTabla">Proyectos</td>
        <td  align="left"><select name="proyecto" class="CajaTexto" id="proyecto" >
          <option value="">::Seleccione Proyecto::</option>
          <?php
				$sql_proyecto="select * from Proyectos order by(nombre)";
				$cursor_proyecto=mssql_query($sql_proyecto);
				
				while($datos_proyecto=mssql_fetch_array($cursor_proyecto))
				{
							$select2="";
							if($proyecto== $datos_proyecto["id_proyecto"])
							{
								$select2="selected";
							}

?>
          <option value="<?php  echo  $datos_proyecto["id_proyecto"]; ?>" <?php echo $select2; ?>>
            <?php  echo "[".$datos_proyecto["codigo"].".".$datos_proyecto["cargo_defecto"]."] ". strtoupper($datos_proyecto["nombre"]); ?>
            </option>
          <?php
				}
?>
        </select></td>
      </tr>
      <tr class="TxtTabla" >
        <td width="15%" class="TituloTabla">Vigencia</td>
        <td align="left"><select name="lstVigencia" class="CajaTexto" id="lstVigencia" >
          <option value="">::Seleccione Vigencia::</option>
          <? 
								if(!isset($lstVigencia))
									$lstVigencia=$anos;

                                for ($k=$minVigenciaP; $k<=$maxVigenciaP; $k++) { 
                                    if ($lstVigencia == $k) {
                                        $selVig = "selected";
                                    }
                                    else {
                                        $selVig = "";
                                    }
                                ?>
          <option value="<? echo $k; ?>" <? echo $selVig; ?> ><? echo $k; ?></option>
          <? } ?>
        </select></td>
      </tr>
      <tr class="TxtTabla" >
        <td class="TituloTabla">Mes</td>
        <td align="left"><select name="mes" class="CajaTexto" id="mes" >
          <option value="">::Seleccione Mes::</option>
          <? 
						for($i=1;$i<=12;$i++)
						{						
                                    if ($mes== $i) {
                                        $selMes = "selected";
                                    }
                                    else {
                                        $selMes = "";
                                    }
                                ?>
          <option value="<? echo $i; ?>" <? echo $selMes; ?> ><? echo $meses[$i]; ?></option>
          <?	} ?>
        </select></td>
      </tr>
      <tr>
        <td width="15%" class="TituloTabla">Unidad</td>
        <td align="left" class="TxtTabla"><input name="unidad" type="text" class="CajaTexto" id="unidad" /></td>
      </tr>
      <tr>
        <td class="TituloTabla">Facturaci&oacute;n</td>
        <td align="left" class="TxtTabla"><select name="facturacion" class="CajaTexto" id="facturacion" >
          <option value="0">::Seleccione Facturación::</option>
          <option value="1">::Con Vobo::</option>
          <option value="2">::Sin Vobo::</option>
        </select></td>
      </tr>
      <tr class="TxtTabla" >
        <td colspan="2" align="right" class="TxtTabla"><input type="hidden" name="recarga" value="0" id="recarga" />
          <input type="hidden" name="ba" value="<?=$ba; ?>" id="ba" />
          <?php
//			if((trim($division)!="") and (trim($lstVigencia)!="") and (trim($proyecto)!="") and ($recarga==1) )
			{
/*
		            <input onclick="MM_openBrWindow('consolidado_vigencia_div_xls.php?division=<?=$division;?>&amp;departamento=<?=$departamento;?>&amp;categoria=<?=$categoria;?>&proyecto=<?=$proyecto; ?>&lstVigencia=<?=$lstVigencia; ?>&empleado=<?=$empleado; ?>','wRPT1','scrollbars=yes,resizable=yes,width=500,height=400')" type="button" class="Boton" value="Descargar en XLS" />

*/
			}
		?>
          <input name="Consultar" onclick="envia0();" type="button" class="Boton" id="Consultar" value="Consultar" /></td>
      </tr>
      <tr>
        <td  colspan="2"><table cellspacing="0" cellpadding="0" border="0" width="100%">
          <tbody>
            <tr>
              <td class="TituloUsuario" height="2"></td>
            </tr>
          </tbody>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<table width="100%" border="0">
  <tr>
    <td class="TxtTabla">&nbsp;</td>
  </tr>
  <tr>
    <td class="TituloUsuario">Usuarios que deben aprobar facturación</td>
  </tr>
</table>
<table width="100%" border="0">
  <tr>
    <td class="TituloTabla">Cantidad de Usuarios</td>
    <td class="TxtTabla">3</td>
    <td class="TituloTabla">Cant. Usuarios que han aprobado</td>
    <td class="TxtTabla">3</td>
    <td class="TituloTabla">Cant. Usuarios que no han aprobado</td>
    <td class="TxtTabla">1</td>
  </tr>
</table>
<table width="100%" border="0" bgcolor="#FFFFFF" >
  <tr class="TituloTabla">
    <td width="5%">&nbsp;</td>
    <td width="15%">Unidad </td>
    <td>Nombre</td>
    <td width="5%" rowspan="1">Actividades a cargo</td>
    <td rowspan="1">Vobo</td>
    <td rowspan="1">Facturación</td>
    <td rowspan="1">&nbsp;</td>
  </tr>
  <tr>
    <td class="TxtTabla"><img src="imagenes/Inactivo.gif" alt=" " title="Retirado de la compañia" /></td>
    <td class="TxtTabla">1111</td>
    <td class="TxtTabla">Juan Losada</td>
	<td width="5%" class="TxtTabla">1</td>
	<td class="TxtTabla"><img src="img/images/Si.gif" width="16" height="14" /></td>
	<td><table width="100%" border="0">
	  <tr>
	    <td width="69%" class="TituloTabla">Con Vobo</td>
	    <td width="31%" class="TxtTabla">5</td>
	    </tr>
	  <tr>
	    <td class="TituloTabla">Sin Vobo</td>
	    <td class="TxtTabla">5</td>
	    </tr>
    </table></td>
	<td class="TxtTabla">
	  <input name="Detalle2" type="submit" class="Boton" id="Detalle2" value="Detalle" />
</td>
  </tr>
  <tr>
    <td class="TxtTabla">&nbsp;</td>
    <td class="TxtTabla">5552</td>
    <td class="TxtTabla">Carlos Pumarejo</td>
    <td width="5%" class="TxtTabla">1</td>
    <td class="TxtTabla"><img src="img/images/No.gif" width="16" height="16" /></td>
    <td><table width="100%" border="0">
      <tr>
        <td width="69%" class="TituloTabla">Con Vobo</td>
        <td width="31%" class="TxtTabla">5</td>
      </tr>
      <tr>
        <td class="TituloTabla">Sin Vobo</td>
        <td class="TxtTabla">3</td>
      </tr>
    </table></td>
    <td class="TxtTabla"><input name="Detalle2" type="submit" class="Boton" id="Detalle2" value="Detalle" /></td>
  </tr>
  <tr>
    <td class="TxtTabla">&nbsp;</td>
    <td class="TxtTabla">18121</td>
    <td class="TxtTabla">Carlos Slim</td>
    <td width="5%" class="TxtTabla">&nbsp;</td>
    <td class="TxtTabla"><img src="img/images/No.gif" width="16" height="16" /></td>
    <td><table width="100%" border="0">
      <tr>
        <td width="69%" class="TituloTabla">Con Vobo</td>
        <td width="31%" class="TxtTabla">5</td>
      </tr>
      <tr>
        <td class="TituloTabla">Sin Vobo</td>
        <td class="TxtTabla">3</td>
      </tr>
    </table></td>
    <td class="TxtTabla"><input name="Detalle2" type="submit" class="Boton" id="Detalle2" value="Detalle" /></td>
  </tr>
</table>
</body>
</html>