<?php
require('../config/config.php');
require('../lib/vendor/autoload.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\OAuth;
use League\OAuth2\Client\Provider\Google;

date_default_timezone_set('America/Bogota');

if (
  isset($_POST["nombre"]) &&
  isset($_POST["email"]) &&
  isset($_POST["curso"]) &&
  isset($_POST["inicio"]) &&
  isset($_POST["fin"]) &&
  isset($_POST["username"]) &&
  isset($_POST["password"])
) {
  $nombre = $_POST["nombre"];
  $email = $_POST["email"];
  $curso = $_POST["curso"];
  $inicio = $_POST["inicio"];
  $fin = $_POST["fin"];
  $username = $_POST["username"];
  $password = $_POST["password"];

  $asunto = "ACG Calidad le da la bienvenida al $curso";
  $contNotifHTML = preg_replace("/{{nombre}}/", $nombre, $contNotifHTML);
  $contNotifHTML = preg_replace("/{{curso}}/", $curso, $contNotifHTML);
  $contNotifHTML = preg_replace("/{{inicio}}/", $inicio, $contNotifHTML);
  $contNotifHTML = preg_replace("/{{fin}}/", $fin, $contNotifHTML);
  $contNotifHTML = preg_replace("/{{username}}/", $username, $contNotifHTML);
  $contNotifHTML = preg_replace("/{{password}}/", $password, $contNotifHTML);
  $contNotifALT = preg_replace("/{{nombre}}/", $nombre, $contNotifALT);
  $contNotifALT = preg_replace("/{{curso}}/", $curso, $contNotifALT);
  $contNotifALT = preg_replace("/{{inicio}}/", $inicio, $contNotifALT);
  $contNotifALT = preg_replace("/{{fin}}/", $fin, $contNotifALT);
  $contNotifALT = preg_replace("/{{username}}/", $username, $contNotifALT);
  $contNotifALT = preg_replace("/{{password}}/", $password, $contNotifALT);

  $mail = new PHPMailer();
  $mail->isSMTP();
  $mail->SMTPDebug = SMTP::DEBUG_OFF;
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
  $mail->msgHTML($contNotifHTML);
  $mail->AltBody = $contNotifALT;
  if (!$mail->send()) {
    print_r(json_encode(array("error" => $mail->ErrorInfo)));
  } else {
    print_r(json_encode(array("error" => null)));
  }
}
