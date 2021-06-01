<?php
session_id('12947');
session_start();
session_register('uusuario');
session_register('upasswd');
$ldaphost = "192.168.1.5";
$ldapport = 389;
$uusuario="gbazurto";
$upasswd="sgpdbgabg9";
$ds = ldap_connect($ldaphost, $ldapport) or die("No se pudo conectar al Servidor $ldaphost");
if ($ds) {
//	$uusuario = $usuario;
//	$upasswd = $password;
	$binddn = "uid=$uusuario,ou=Users,ou=OxObjects,dc=ingetec,dc=com,dc=co";
	$ldapbind = ldap_bind($ds, $binddn, $upasswd);
	if ($ldapbind) {
		  echo "! $uusuario autenticado. <br>";
				
            //echo "Searching for cn=*) ...";
		   // Search surname entry
		   $sr=ldap_search($ds, "ou=Users,ou=OxObjects,dc=ingetec,dc=com,dc=co","uid=*");  
		   // echo "Search result is " . $sr . "<br />";
		
		   // echo "Number of entires returned is " . ldap_count_entries($ds, $sr) . "<br />";
		
		   echo "Getting entries ...<p>";
		   $info = ldap_get_entries($ds, $sr);
		   echo "Data for " . $info["count"] . " items returned:<p>";
		
		   for ($i=0; $i<$info["count"]; $i++) {
			   // echo "dn is: " . $info[$i]["dn"] . "<br />";
			   echo $info[$i]["cn"][0]." -   ".$info[$i]["mail"][0] . "<br />";
			//   echo "       Tipo de usuario : ". $info[$i]["oxuserposition"][0] . "<br> <hr />"; 
		 }
	} else {
	 	 	   echo "No se valido correctamente $uusuario <br>";
	}
} else {
   echo "<h4>Unable to connect to LDAP server</h4>";
}
 ldap_close($ds);
?>
