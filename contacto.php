<?
	header ("Cache-Control: no-cache, must-revalidate");
	header ("Pragma: no-cache");
	include "funciones.php";
	session_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<SCRIPT LANGUAGE="JavaScript">

function ShowButton(objName, ImageName) {
	objName.src=ImageName
}

function PreloadImages() {
  if(document.images)
    { if (!document.tmpImages)
         document.tmpImages=new Array();
      with(document) {
       var
          i,j=tmpImages.length,
          a=PreloadImages.arguments;

       for(i=0; i<a.length; i++)
          if (a[i].indexOf("#")!=0) {
             tmpImages[j]=new Image;
             tmpImages[j++].src=a[i];
          }
      }
    }
}

</SCRIPT>
	<title>Sus comentarios</title>
</head>
<BODY bgcolor="#FFFFFF" text="#000000" link="#0000FF" vlink="#800080" alink="#FF0000">

<div id="Layer7" style="position:absolute; left:586px; top:484px; width:99px; height:62px; z-index:9">
<img src="pics/Image71557437_0.gif" width="99" height="62" border="0" name="Image_Layer7"></div>
<div id="Layer3" style="position:absolute; left:7px; top:156px; width:160px; height:352px; background-color:#FFFFFF; z-index:8">


<FONT size=4 color=#0000FF face="Arial"><B>&nbsp;</B></FONT></div>
<div id="Layer5" style="position:absolute; left:291px; top:63px; width:357px; height:29px; z-index:6">
<img src="pics/Image22977796.gif" width="357" height="29" border="0" name="Image_Layer5"></div>
<div id="logoingetec" style="position:absolute; left:12px; top:7px; width:154px; height:43px; z-index:5">
<img src="pics/Image20783687.gif" width="154" height="43" border="0" name="Image_logoingetec"></div>
<div id="Copyright" style="position:absolute; left:55px; top:530px; width:641px; height:64px; z-index:4">
<FONT size=1 color=#000000 face="Arial">&nbsp;</FONT><HR size=2><div align="CENTER"><FONT size=1 color=#000000 face="Arial">Copyright© 2003, Ingetec S.A. All rights reserved.</FONT></div>
<div align="CENTER"><A href="http://www.pysoft.com/"><FONT size=1 color=#008000 face="Arial">www.ingetec.com.co</FONT></A></div>
</div>
<div id="Layer4" style="position:absolute; left:-3px; top:63px; width:650px; height:91px; z-index:3">
<img src="pics/GreenRoundedImage4_0.gif" width="650" height="91" border="0" name="Image_Layer4"></div>
<div id="Layer2" style="position:absolute; left:647px; top:63px; width:81px; height:54px; z-index:2">
<img src="pics/GreenRoundedImage2_0.gif" width="81" height="54" border="0" name="Image_Layer2"></div>
<div id="Layer12" style="position:absolute; left:404px; top:-2px; width:295px; height:62px; z-index:1">
<img src="pics/GreenRoundedImage12_0.gif" width="295" height="62" border="0" name="Image_Layer12"></div>

<!--menu de botones-->
<div id="Layer3" style="position:absolute; left:441px; top:510px; width:154px; height:41px; z-index:3">
<A href="Inicio.php" onMouseOver="ShowButton(document.images['Image_Layer3'],'pics/Image177477343_1.gif')"
 onMouseOut="ShowButton(document.images['Image_Layer3'],'pics/Image177477343_0.gif')"
 onMouseDown="ShowButton(document.images['Image_Layer3'],'pics/Image177477343_2.gif')"
 onMouseUp="ShowButton(document.images['Image_Layer3'],'pics/Image177477343_1.gif')"
><img src="pics/Image177477343_0.gif" width="154" height="41" border="0" name="Image_Layer3"></A></div>
<div id="Layer2" style="position:absolute; left:287px; top:510px; width:154px; height:41px; z-index:2">
<A href="hdetiempo.php" onMouseOver="ShowButton(document.images['Image_Layer2'],'pics/Image177468546_1.gif')"
 onMouseOut="ShowButton(document.images['Image_Layer2'],'pics/Image177468546_0.gif')"
 onMouseDown="ShowButton(document.images['Image_Layer2'],'pics/Image177468546_2.gif')"
 onMouseUp="ShowButton(document.images['Image_Layer2'],'pics/Image177468546_1.gif')"
><img src="pics/Image177468546_0.gif" width="154" height="41" border="0" name="Image_Layer2"></A></div>
<div id="Layer1" style="position:absolute; left:133px; top:510px; width:154px; height:41px; z-index:1">
<A href="frm-GrabaTiempo.php" onMouseOver="ShowButton(document.images['Image_Layer1'],'pics/Image177461625_1.gif')"
 onMouseOut="ShowButton(document.images['Image_Layer1'],'pics/Image177461625_0.gif')"
 onMouseDown="ShowButton(document.images['Image_Layer1'],'pics/Image177461625_2.gif')"
 onMouseUp="ShowButton(document.images['Image_Layer1'],'pics/Image177461625_1.gif')"
><img src="pics/Image177461625_0.gif" width="154" height="41" border="0" name="Image_Layer1"></A></div>


<div id="Layer7" style="position:absolute; left:200px; top:200px; width:99px; height:62px; z-index:9">
<form name="formulario" action="contacto.php" method=post>
<table>
<tr><td colspan=2><strong><font face="Arial" size="3" color="#ff0000">Comentarios y sugerencias</font></strong></td></tr>
<tr><td colspan=2> </td></tr>
<tr>
	<td><strong><font face="Arial" size="3" color="#0000FF">Para</font></strong></td>
	<td><input type="text" name="para" value="hojatiempo@ingetec.com.co" size="40"></td>
</tr>
<tr>
	<td><strong><font face="Arial" size="3" color="#0000FF">Asunto</font></strong></td>
	<td><input type="text" name="asunto" size="40"></td>
</tr>
<tr>
	<td><strong><font face="Arial" size="3" color="#0000FF">Mensaje</font></strong></td>
	<td><textarea cols="30" rows="4" name="mensaje"></textarea></td>
</tr>

<tr><td colspan=2 align=center><input type="submit" name="EnviaMail" value="Enviar"></td></tr>
</table></div>

</form>

</BODY>
</HTML>

<?
	if ($EnviaMail=="Enviar") {
		if (strlen($asunto)==0)
			alert("Debe especificar un asunto");
		else {
			/*
			$de="userhoja@ingetec.com.co";
			$body="Mensaje de $login\n\n$mensaje";
			// necesario para poder enviar mails en windows
			$body=str_replace("\n","\r\n",$body);
			
			$HTML=str_replace("\r\n","<br>",$body);
			
			//ini_set("SMTP",MSERVER);
			*/
			
			//***********ENVIA CORREO***********
			include("fncEnviaMailPEAR.php");
			
			$pPara= "grodrig@ingetec.com.co, pbaron@ingetec.com.co";
			$pAsunto= "Nuevo contacto ";
			$pTema = "Mensaje de $login\n\n$mensaje";
			$pFirma = "Contacto página web";
			
		
			if (enviarCorreo($pPara, $pAsunto, $pTema, $pFirma)) {
				alert('Su mensaje ha sido enviado!');
			}
			
		}
	} 
?>
