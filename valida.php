<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
  <meta http-equiv="content-type" content="text/html; charset=windows-1250">
  <meta name="generator" content="PSPad editor, www.pspad.com">
  <title></title>
  </head>
  <body>
    <form method="get" action="valida.php">
        <? if ($Enviar=="OK") {
            $ldaphost = "192.168.1.5";
            $ldapport = 389;
            $ds=ldap_connect($ldaphost,$ldapport) or die("Sin conexion");
            if ($ds) {
              	$binddn = "uid=$nombre,ou=Users,ou=OxObjects,dc=ingetec,dc=com,dc=co";
              	$ldapbind = ldap_bind($ds, $binddn, $clave);
              	if ($ldapbind) {
              	   echo "Validado";
                } else {
                    echo "No Valido";
                }
            }
        } else { ?>            
            Nombre:<input type="text" accept="text/html" name="nombre"><br>
            Clave:<input type="password" accept="text/html" name="clave"><br>
            <input type="submit" name="Enviar" value="OK">
        <? } ?>
    </form>
  </body>
</html>
