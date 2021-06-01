<?php
function conectar(){
//		if (!($CONECTADO=@mssql_connect("sqlservidor","12974","1373")))	{
		if (!($CONECTADO=@mssql_connect("sqlservidor","12974","1373")))	{
			return 0;
			exit();
		}
		if (!@mssql_select_db("GestiondeInformacionDigital",$CONECTADO)) {
			exit();
		}
		return $CONECTADO;
	}
?>