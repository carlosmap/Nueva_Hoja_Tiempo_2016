<?
	// direccion del servidor de correo
	define('MSERVER','192.168.1.2'); //'200.74.147.132');
	//define('MSERVER','mserver.ingetec.com.co');
	
	// constantes para mktime
	define('DOMINGO','0');
	define('LUNES','1');
	define('MARTES','2');
	define('MIERCOLES','3');
	define('JUEVES','4');
	define('VIERNES','5');
	define('SABADO','6');
	
	/********** FUNCIONES DE BASE DE DATOS **************/
	function conexion ($nombrecomputador,$usuario,$password){
		if (!($CONECTADO=@mssql_connect($nombrecomputador,$usuario,$password)))	{
			return 0;
			exit();
		}
		if (!@mssql_select_db("MANEhojadetiempo",$CONECTADO)) {
			exit();
		}
		return $CONECTADO;
	}
	
	//verifica si existen campos en blanco
	function CamposVacios($campo){
		if(empty($campo) or $campo==""){
			echo "<SCRIPT LANGUAGE='JAVASCRIPT'>window.alert('Verifique sus Datos. Algunos datos vacios!')</SCRIPT>";
			exit();
			return 0;
		}else{
			return 1;
		}
	}
	
	/************** FUNCIONES DE PRESENTACION ***************/
	function alert($msg) {
		echo "<script>alert('".$msg."')</script>";
	}
	
	function LlenarCombo ($usuarios,$contrasena,$tabla,$campo) {
		include "validaUsrBd.php";
		$sql="SELECT * FROM $tabla";
		$resultado=mssql_query($sql);
		if ($filas = mssql_fetch_array($resultado)) {
			do {
				echo '<OPTION>'.$filas[$campo].'</OPTION>';
			} while ($filas=mssql_fetch_array($resultado));
		} else {
			echo "<SCRIPT LANGUAGE='JAVASCRIPT'>window.alert('No Existen Registros!' )</SCRIPT>";
		}
	}
	
	/********* FUNCIONES DE MANEJO DE FECHAS **********/
	function validafechas($fecha) {
		if (isset($fecha)) {
			$lafecha=explode("-",$fecha);
			if (checkdate($lafecha[0],$lafecha[1],$lafecha[2])) return 1; else return 0;
		} else {
			return 0;
		}
	}
	
	function nombremes($mesnum) {
		switch ($mesnum) {
			case "01":$n="Ene";break;
			case "02":$n="Feb";break;
			case "03":$n="Mar";break;
			case "04":$n="Abr";break;
			case "05":$n="May";break;
			case "06":$n="Jun";break;
			case "07":$n="Jul";break;
			case "08":$n="Ago";break;
			case "09":$n="Sep";break;
			case "10":$n="Oct";break;
			case "11":$n="Nov";break;
			case "12":$n="Dic";break;
		}
		return $n;
	}
	
	function nombremes_completo($mesnum) {
		switch($mesnum){
			case "01":
				$TmpFlmes='enero';
				Break;
			case "02":
				$TmpFlmes='febrero';
				Break;
			case "03":
				$TmpFlmes='marzo';
				Break;
			case "04":
				$TmpFlmes='abril';
				Break;
			case "05":
				$TmpFlmes='mayo';
				Break;
			case "06":
				$TmpFlmes='junio';
				Break;
			case "07":
				$TmpFlmes='julio';
				Break;
			case "08":
				$TmpFlmes='agosto';
				Break;
			case "09":
				$TmpFlmes='septiembre';
				Break;
			case "10":
				$TmpFlmes='octubre';
				Break;
			case "11":
				$TmpFlmes='noviembre';
				Break;
			case "12":
				$TmpFlmes='diciembre';
		}
		return $TmpFlmes;
	}
	
	function NombreFecha($fechaM) {
		$fcad=date("m d Y",$fechaM);
		$fped=explode(" ",$fcad);
		$fmes=nombremes($fped[0]);
		return $fmes." ".$fped[1]." ".$fped[2];
	}
	
	/*
	FUNCION ADAPTADA DE PHP.NET
	http://www.php.net/manual/en/ref.mail.php
	function SendMail($From, $FromName, $To, $ToName, $Subject, $Text, $Html, $AttmFiles)
		$From      ... sender mail address like "my@address.com"
		$FromName  ... sender name like "My Name"
		$To        ... recipient mail address like "your@address.com"
		$ToName    ... recipients name like "Your Name"
		$Subject   ... subject of the mail like "This is my first testmail"
		$Text      ... text version of the mail
		$Html      ... html version of the mail
		$AttmFiles ... array containing the filenames to attach like array("file1","file2")
						si el archivo contiene rutas absolutas utilizar / en lugar de \
	*/
	function SendMail($From,$FromName,$To,$ToName,$Subject,$Text,$Html,$AttmFiles) {
		$OB="----=_OuterBoundary_000";
		$IB="----=_InnerBoundery_001";
		$Html=$Html?$Html:preg_replace("/\n/","<br>",$Text) or die("neither text nor html part present.");
		$Text=$Text?$Text:"Sorry, but you need an html mailer to read this mail.";
		$From or die("sender address missing");
		$To or die("recipient address missing");
		
		$headers ="MIME-Version: 1.0\r\n"; 
		$headers.="From: ".$FromName." <".$From.">\r\n"; 
		$headers.="To: ".$ToName." <".$To.">\r\n"; 
		$headers.="Reply-To: ".$FromName." <".$From.">\r\n"; 
		$headers.="X-Priority: 3\r\n"; 
		$headers.="X-MSMail-Priority: High\r\n"; 
		$headers.="X-Mailer: My PHP Mailer\r\n"; 
		$headers.="Content-Type: multipart/mixed;\r\n\tboundary=\"".$OB."\"\r\n";
		
		//Messages start with text/html alternatives in OB
		$Msg ="This is a multi-part message in MIME format.\r\n";
		$Msg.="\r\n--".$OB."\r\n";
		$Msg.="Content-Type: multipart/alternative;\r\n\tboundary=\"".$IB."\"\r\n\r\n";
		
		//plaintext section 
		$Msg.="\r\n--".$IB."\r\n";
		$Msg.="Content-Type: text/plain;\r\n\tcharset=\"iso-8859-1\"\r\n";
		$Msg.="Content-Transfer-Encoding: quoted-printable\r\n\r\n";
		// plaintext goes here
		$Msg.=$Text."\r\n\r\n";
		
		// html section 
		$Msg.="\r\n--".$IB."\r\n";
		$Msg.="Content-Type: text/html;\r\n\tcharset=\"iso-8859-1\"\r\n";
		$Msg.="Content-Transfer-Encoding: base64\r\n\r\n";
		// html goes here 
		$Msg.=chunk_split(base64_encode($Html))."\r\n\r\n";
		
		// end of IB
		$Msg.="\r\n--".$IB."--\r\n";
		
		// attachments
		if($AttmFiles){
			foreach($AttmFiles as $AttmFile){
				$patharray = explode ("/", $AttmFile); 
				$FileName=$patharray[count($patharray)-1];
				$Msg.= "\r\n--".$OB."\r\n";
				$Msg.="Content-Type: application/octetstream;\r\n\tname=\"".$FileName."\"\r\n";
				$Msg.="Content-Transfer-Encoding: base64\r\n";
				$Msg.="Content-Disposition: attachment;\r\n\tfilename=\"".$FileName."\"\r\n\r\n";
				
				//file goes here
				$fd=fopen ($AttmFile, "r");
				$FileContent=fread($fd,filesize($AttmFile));
				fclose ($fd);
				$FileContent=chunk_split(base64_encode($FileContent));
				$Msg.=$FileContent;
				$Msg.="\r\n\r\n";
			}
		}
		
		//message ends
		$Msg.="\r\n--".$OB."--\r\n";
		return mail($To,"[HOJATIEMPO]".$Subject,$Msg,$headers); 
	}
	
	function separa_cargo($CodOrig) {
		// contiene la posicion del punto (si lo hay)
		$poscodigo=strpos($CodOrig,".");
		if ($poscodigo===false) {
			$codigo=$CodOrig;
			$cargo="";
		} else {
			$codigo=substr($CodOrig,0,$poscodigo);
			$cargo=substr($CodOrig,$poscodigo+1);
		}
		return array("codigo"=>$codigo,"cargo"=>$cargo);
	}
?>
