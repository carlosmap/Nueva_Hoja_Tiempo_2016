<?php
echo " Prueba de correo <br> Enviando correo a gbazurto@ingetec.com.co <br> Mi asunto <br>";
$bueno= mail("gbazurto@ingetec.com.co", "Mi Asunto", "Linea 1\nLinea 2\nLinea 3");
echo "<br>";
echo $bueno;
echo "<br>";
?>  
