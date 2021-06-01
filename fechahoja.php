Mes:<select name="Flmes" OnClick="actualizarhiddenfin()">
<? 
	for ($i=1;$i<=12;$i++) {
		echo "<option value='".$i."'";
		if ($i==$MiMes) echo " selected";
		echo ">".nombremes($i)."</option>"; 
	}
?>
</select>

Año:<select name="Flano" OnClick="actualizarhiddenfin()">
<? 
	for($i=2001;$i<=2015;$i++) {
		echo "<option value='".$i."'";
		if ($i==$MiAnno) echo " selected";
		echo ">".$i."</option>";
	}
?>
</select>
<input type=hidden name="fechafinal" size=11 value="<? echo $fechainirep; ?>">

