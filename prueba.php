<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<title>Untitled</title>
	
	<script>
	
	function validar(){
		var x1=document.miformulario.prueba.options.value.length;
		var xx=document.miformulario.x2.value.length;
		alert(xx);
	}
	</script>
</head>

<body>


<form name="miformulario" action="javascript:validar()";>
	<select name="prueba">
	 <option selected value="1">1
	 <option value="20000">20000
	 <option value="300">300
	 <option value="40">40
</select>
	
	<textarea name=x2>gonza</textarea>
<input type=submit name=entrar value="enviar">
</form>

<?

$mk=mktime(0,0,0,1,14,2004);

$dia = strftime ("%a",$mk);

echo $dia;
?>

</body>
</html>
