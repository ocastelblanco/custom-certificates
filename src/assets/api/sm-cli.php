<?php
if (php_sapi_name() != 'cli') {
  throw new Exception('Esta aplicación solo puede ejecutarse desde CLI.');
}
require('../lib/vendor/autoload.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\OAuth;
use League\OAuth2\Client\Provider\Google;

$email = 'cursosvirtualesacg@gmail.com';
$clientId = '759013937852-tfpc65ko2rrctvd44askilbeqfkv0mek.apps.googleusercontent.com';
$clientSecret = 'GBgynlAoamzC6HSde7ZZB9br';

//Obtained by configuring and running get_oauth_token.php
//after setting up an app in Google Developer Console.
$refreshToken = '1//05YaBzQ54KhnCCgYIARAAGAUSNwF-L9IrJFsB91XXoLzRCWNXiV545xkC_sy_a3BOFgXrdaRRgzS8YgEe8Hkr3146sFzfbWz7LZA';

date_default_timezone_set('America/Bogota');

$contHTML = '
  <!DOCTYPE html>
    <html>
      <head>
        <meta charset="utf-8">
      </head>
      <body>
        <p>Estimado(a) Doctor(a) <strong>{{nombre}}</strong>:</p>
        <p>De parte del Grupo de Capacitación de ACG Calidad le queremos agradecer su participación en nuestro curso virtual <strong>{{curso}}</strong>.</p>
        <p>Ingrese a <a href="http://www.acgcalidadeducacion.com/" target="_blank">nuestro Campus Virtual</a> (con el mismo usuario y contraseña de siempre) y haga clic sobre el vínculo <strong>OBTENER CERTIFICADO</strong> que se encuentra al inicio de la página principal.</p>
        <p>Si tiene alguna duda, puede consultar <a href="https://youtu.be/Gq-UkAvsFOo" target="_blank">nuestro instructivo en video</a>.</p>
        <p>Atentamente,</p>
        <br>
        <p>
          --
          <br>
          <strong>Grupo Capacitación ACG</strong>
        </p>
      </body>
    </html>
';
$contAlt = '
  Estimado(a) Doctor(a) {{nombre}}:

  De parte del Grupo de Capacitación de ACG Calidad le queremos agradecer su participación en nuestro curso virtual {{curso}}.

  Ingrese a nuestro Campus Virtual con el mismo usuario y contraseña de siempre, http://www.acgcalidadeducacion.com/, y haga clic sobre el vínculo OBTENER CERTIFICADO que se encuentra al inicio de la página principal.

  Si tiene alguna duda, puede consultar nuestro instructivo en video https://youtu.be/Gq-UkAvsFOo.

  Atentamente,

  --
  Grupo Capacitación ACG
';
$asunto = "Certificado de curso virtual en ACG Calidad";
if ($gestor = fopen("enviarEmails.csv", "r")) {
  $numLinea = 0;
  while (($linea = fgetcsv($gestor)) != FALSE) {
    $numLinea++;
    $correo = $linea[0];
    $nombre = $linea[1];
    $curso = $linea[2];
    $contenido = str_replace('{{nombre}}', $nombre, $contHTML);
    $contenido = str_replace('{{curso}}', $curso, $contenido);
    $altbody = str_replace('{{nombre}}', $nombre, $contAlt);
    $altbody = str_replace('{{curso}}', $curso, $altbody);
    $mail = new PHPMailer();
    $mail->isSMTP();

    //Enable SMTP debugging
    //SMTP::DEBUG_OFF = off (for production use)
    //SMTP::DEBUG_CLIENT = client messages
    //SMTP::DEBUG_SERVER = client and server messages
    //$mail->SMTPDebug = SMTP::DEBUG_OFF;
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->SMTPAuth = true;
    $mail->AuthType = 'XOAUTH2';
    $provider = new Google(['clientId' => $clientId, 'clientSecret' => $clientSecret]);
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
    $mail->setFrom($email, 'Grupo Capacitación ACG');

    //$mail->addAddress($correo, $nombre);
    $mail->addAddress('ocastelblanco@gmail.com', 'Oliver Castelblanco');
    $mail->Subject = $asunto;
    $mail->CharSet = PHPMailer::CHARSET_UTF8;
    $mail->msgHTML($contenido);
    $mail->AltBody = $altbody;
    echo "********* Enviando correo $numLinea desde $email *********\n";
    echo "Nombre: $nombre | Correo: $correo\n";
    echo "Asunto: $asunto\n";
    //*
    if (!$mail->send()) {
      echo "Error al enviar el correo a $nombre: " . $mail->ErrorInfo . "\n";
    } else {
      echo "Correo enviado correctamente a $nombre\n";
    }
    //*/
    echo "*******************************************************\n";
  }
}
