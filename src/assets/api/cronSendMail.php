<?php
// Se verifica que sea CLI
if (php_sapi_name() != 'cli') {
  throw new Exception('Esta aplicación solo puede ejecutarse desde CLI.');
}
// Almacena las variables enviadas por CLI en $_VAR
$_VAR = [];
foreach ($argv as $pos => $arg) {
  if ($pos == 0) continue;
  if (substr($arg, 0, 2) == '--') {
    $key = explode("=", substr($arg, 2))[0];
    $val = explode("=", substr($arg, 2))[1];
    $_VAR[$key] = $val;
  } elseif (substr($arg, 0, 1) == '-') {
    $key = substr($arg, 1);
    $_VAR[$key] = true;
  }
}
require('../config/config.php');
require('../lib/vendor/autoload.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\OAuth;
use League\OAuth2\Client\Provider\Google;

date_default_timezone_set('America/Bogota');

if (
  isset($_VAR["asunto"]) &&
  isset($_VAR["correo"]) &&
  isset($_VAR["nombre"]) &&
  isset($_VAR["contenido"]) &&
  isset($_VAR["altbody"]) &&
  isset($_VAR["pdf"])
) {
  $asunto = $_VAR["asunto"];
  $correo = $_VAR["correo"];
  $nombre = $_VAR["nombre"];
  $contenido = $_VAR["contenido"];
  $altbody = $_VAR["altbody"];
  $pdf = $_VAR["pdf"];

  $mail = new PHPMailer();
  $mail->isSMTP();

  //SMTP::DEBUG_OFF = off (for production use)
  //SMTP::DEBUG_CLIENT = client messages
  //SMTP::DEBUG_SERVER = client and server messages

  //Enable SMTP debugging
  $mail->SMTPDebug = SMTP::DEBUG_OFF;
  //$mail->SMTPDebug = SMTP::DEBUG_SERVER;


  /** Autenticación con GMail */
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
  /** Fin de autenciación con GMail */

  //$mail->addAddress($correo, $nombre);
  $mail->addAddress('ocastelblanco@gmail.com', 'Oliver Castelblanco');
  $mail->Subject = $asunto;
  $mail->CharSet = PHPMailer::CHARSET_UTF8;
  $mail->msgHTML($contenido);
  $mail->AltBody = $altbody;
  $mail->addAttachment("certificados/$pdf");

  if (!$mail->send()) {
    print_r(json_encode(array("error" => $mail->ErrorInfo)));
  } else {
    unlink("certificados/$pdf");
    print_r(json_encode(array("error" => null)));
  }
}
