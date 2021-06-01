<?php
include("Mail.php");
include("Mailpear/mime.php");

function armarCuerpo($elTema, $laFirma){
	$msgM="<html>";
	$msgM=$msgM." <head>";
	$msgM=$msgM." <title></title>";
	$msgM=$msgM." <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>";
	$msgM=$msgM." <style type='text/css'> ";
	$msgM=$msgM." <!-- ";
	$msgM=$msgM." .Estilo1 { ";
	$msgM=$msgM." 	font-family: Verdana, Arial, Helvetica, sans-serif; ";
	$msgM=$msgM." 	font-weight: bold; ";
	$msgM=$msgM." 	font-size: 12px; ";
	$msgM=$msgM." 	color: #FFFFFF; ";
	$msgM=$msgM." } ";
	$msgM=$msgM." .Estilo2 { ";
	$msgM=$msgM." 	font-family: Verdana, Arial, Helvetica, sans-serif; ";
	$msgM=$msgM." 	color: #666666; ";
	$msgM=$msgM." 	font-size: 12px; ";
	$msgM=$msgM." } ";
	$msgM=$msgM." --> ";
	$msgM=$msgM." </style>";
	$msgM=$msgM." </head>";
	$msgM=$msgM." <body> ";
	$msgM=$msgM." <table width='100%'  border='0' cellspacing='0' cellpadding='0'>";
	$msgM=$msgM."   <tr> ";
	$msgM=$msgM."     <td height='10' bgcolor='#999999'></td>";
	$msgM=$msgM."   </tr>";
	$msgM=$msgM."   <tr>";
	$msgM=$msgM."     <td>&nbsp;</td>";
	$msgM=$msgM."   </tr>";
	$msgM=$msgM."   <tr>";
	$msgM=$msgM."     <td class='Estilo2'>Estimado usuario: </td> \n\n";
	$msgM=$msgM."   </tr>";
	$msgM=$msgM."   <tr>";
	$msgM=$msgM."     <td>&nbsp;</td>";
	$msgM=$msgM."   </tr>";
	$msgM=$msgM."   <tr>";
	$msgM=$msgM."     <td><span class='Estilo2'>".$elTema." </span></td> \n";
	$msgM=$msgM."   </tr>";
	$msgM=$msgM."   <tr>";
	$msgM=$msgM."     <td>&nbsp;</td>";
	$msgM=$msgM."   </tr>";
	$msgM=$msgM."   <tr>";
	$msgM=$msgM."     <td class='Estilo2'>Por favor consultar el Portal <a href='http://www.ingetec.com.co/portal' target='_blank'>www.ingetec.com.co/portal</a></td> \n";
	$msgM=$msgM."   </tr>";
	$msgM=$msgM."   <tr>";
	$msgM=$msgM."     <td>&nbsp;</td>";
	$msgM=$msgM."   </tr>";
	$msgM=$msgM."   <tr>";
	$msgM=$msgM."     <td class='Estilo2'>Atentamente,</td> \n\n";
	$msgM=$msgM."   </tr>";
	$msgM=$msgM."   <tr>";
	$msgM=$msgM."     <td>&nbsp;</td>";
	$msgM=$msgM."   </tr>";
	$msgM=$msgM."   <tr>";
	$msgM=$msgM."     <td class='Estilo2'>".$laFirma." </td>";
	$msgM=$msgM."   </tr>";
	$msgM=$msgM."   <tr>";
	$msgM=$msgM."     <td>&nbsp;</td>";
	$msgM=$msgM."   </tr>";
	$msgM=$msgM."   <tr >";
	$msgM=$msgM."     <td bgcolor='#999999' ><span class='Estilo1'>Ingetec S.A. </span></td>";
	$msgM=$msgM."   </tr>";
	$msgM=$msgM." </table>";
	$msgM=$msgM." </body>";
	$msgM=$msgM." </html>";
	
	return $msgM;
}

function enviarCorreo($Para, $Asunto, $elTema, $laFirma){

	$mensaje = armarCuerpo($elTema, $laFirma)."\n";

	$mime = new Mail_mime("\n");
	$mime->setTXTBody(strip_tags($mensaje));
	$mime->setHTMLBody($mensaje);

	$body = $mime->get();

	$hdrs["From"] = "portal@ingetec.com.co";
	$hdrs["To"] =  $Para ;
	$hdrs["Subject"] = $Asunto ;
	$hdrs = $mime->headers($hdrs);
	
	/* SMTP server name, port, user/passwd */
	$smtpinfo["host"] = "200.26.137.33";
	$smtpinfo["port"] = "25";
	$smtpinfo["auth"] = true;
	$smtpinfo["username"] = "maildeveloper@ingetec.com.co";
	$smtpinfo["password"] = "123";
	
	/* Create the mail object using the Mail::factory method */
	$mail_object =& Mail::factory("smtp", $smtpinfo);

	$mail_object->send($Para, $hdrs, $body);
}

function enviarCorreoSinHtml($Para, $Asunto, $elTema, $laFirma){

	$recipients = $Para ;
	$headers["To"]= $Para;
	$headers["Subject"]= $Asunto;
	$mailmsg= $elTema;


	$headers["From"] = "portalingetec@ingetec.com.co";
	$smtpinfo["host"] = "200.26.137.33";
	$smtpinfo["port"] = "25";
	$smtpinfo["auth"] = true;
	$smtpinfo["username"] = "maildeveloper@ingetec.com.co";
	$smtpinfo["password"] = "123";
	
	/* Create the mail object using the Mail::factory method */
	$mail_object =& Mail::factory("smtp", $smtpinfo);
	/* Ok send mail */
	$mail_object->send($recipients, $headers, $mailmsg);

}



?>