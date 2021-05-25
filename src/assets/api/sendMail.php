<?php

require('../lib/vendor/autoload.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\OAuth;
use League\OAuth2\Client\Provider\Google;

date_default_timezone_set('America/Bogota');
//Create a new PHPMailer instance
$mail = new PHPMailer();

//Tell PHPMailer to use SMTP
$mail->isSMTP();

//Enable SMTP debugging
//SMTP::DEBUG_OFF = off (for production use)
//SMTP::DEBUG_CLIENT = client messages
//SMTP::DEBUG_SERVER = client and server messages
$mail->SMTPDebug = SMTP::DEBUG_SERVER;

//Set the hostname of the mail server
$mail->Host = 'smtp.gmail.com';

//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
$mail->Port = 587;

//Set the encryption mechanism to use - STARTTLS or SMTPS
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

//Whether to use SMTP authentication
$mail->SMTPAuth = true;

//Set AuthType to use XOAUTH2
$mail->AuthType = 'XOAUTH2';

//Create a new OAuth2 provider instance
$provider = new Google(
  [
    'clientId' => $clientId,
    'clientSecret' => $clientSecret,
  ]
);

//Pass the OAuth provider instance to PHPMailer
$mail->setOAuth(
  new OAuth(
    [
      'provider' => $provider,
      'clientId' => $clientId,
      'clientSecret' => $clientSecret,
      'refreshToken' => $refreshToken,
      'userName' => $email,
    ]
  )
);

//Set who the message is to be sent from
//For gmail, this generally needs to be the same as the user you logged in as
$mail->setFrom($email, 'Grupo Capacitación ACG');

//Set who the message is to be sent to
$mail->addAddress('ocastelblanco@gmail.com', 'Oliver Castelblanco');

//Set the subject line
$mail->Subject = 'PHPMailer GMail XOAUTH2 SMTP test';

//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
$mail->CharSet = PHPMailer::CHARSET_UTF8;
//$mail->msgHTML(file_get_contents('contentsutf8.html'), __DIR__);
$contenido = '
<html>
<head></head>
<body>
<strong>Este es</strong> un mensaje con <em>estilos</em> de texto de prueba y con tíldes.
<table style="border: 1px solid #333">
<tr>
<td>Un nombre</td>
<td>Una clave</td>
</tr>
</table>
</body>
</html>
';
$mail->msgHTML($contenido);

//Replace the plain text body with one created manually
$mail->AltBody = 'This is a plain-text message body';

//Attach an image file
//$mail->addAttachment('images/phpmailer_mini.png');

//send the message, check for errors
if (!$mail->send()) {
  echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
  echo 'Message sent!';
}
