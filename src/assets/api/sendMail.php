<?php
require('../config/config.php');
require('../lib/vendor/autoload.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\OAuth;
use League\OAuth2\Client\Provider\Google;

date_default_timezone_set('America/Bogota');

if (
  isset($_POST["asunto"]) &&
  isset($_POST["correo"]) &&
  isset($_POST["nombre"]) &&
  isset($_POST["contenido"]) &&
  isset($_POST["altbody"])
) {
  $asunto = $_POST["asunto"];
  $correo = $_POST["correo"];
  $nombre = $_POST["nombre"];
  $contenido = $_POST["contenido"];
  $altbody = $_POST["altbody"];

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


  /* GoDaddy no permite conectarse con GMail, por lo tanto, se elimina la opción de enviar correo */
  //print_r(json_encode(array("error" => null)));
  /* Fin de GoDaddy no permite conectarse con GMail, por lo tanto, se elimina la opción de enviar correo */

  //* Cuando se pueda conectar con GMail (cuando esté montado en AWS) *
  if (!$mail->send()) {
    print_r(json_encode(array("error" => $mail->ErrorInfo)));
  } else {
    print_r(json_encode(array("error" => null)));
  }
  //*/
  //print_r(json_encode(array("error" => null)));
  //print_r(json_encode(array("error" => "Correo enviado correctamente a $nombre al $correo")));
  /*
  if (rand(0, 2) > 1) {
    print_r(json_encode(array("error" => null)));
  } else {
    print_r(json_encode(array("error" => "Error oprobioso y vergonzante")));
  }
  //*/
}
