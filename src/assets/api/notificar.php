<?php
require('../config/config.php');
require('../lib/vendor/autoload.php');

use PHPMailer\PHPMailer\PHPMailer;

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

  $client = getClient();
  $service = new Google_Service_Gmail($client);
  $correoFuente = 'cursosvirtualesacg@gmail.com';
  $email = "ocastelblanco@gmail.com";

  $mensaje = createMessage($correoFuente, $email, $asunto, $contNotifHTML, $contNotifALT, "Grupo Capacitacion ACG");
  $userId = 'me';
  try {
    $message = $service->users_messages->send($userId, $mensaje);
    print_r(json_encode(array("error" => null, "message" => "Se ha enviado el correo con ID " . $message->getId())));
  } catch (Exception $e) {
    print_r(json_encode(array("error" => $e->getMessage())));
  }
}

function getClient()
{
  chdir('../config');
  $configDir = getcwd();
  $client = new Google_Client();
  $client->setApplicationName('ACG Certificates');
  $client->setScopes(Google_Service_Gmail::MAIL_GOOGLE_COM);
  $client->setAuthConfig("$configDir/credentials.json");
  $client->setAccessType('offline');
  $client->setPrompt('select_account consent');
  $tokenPath = "$configDir/token.json";
  if (file_exists($tokenPath)) {
    $accessToken = json_decode(file_get_contents($tokenPath), true);
    $client->setAccessToken($accessToken);
  }
  if ($client->isAccessTokenExpired()) {
    if ($client->getRefreshToken()) {
      $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
    } else {
      $authUrl = $client->createAuthUrl();
      printf("Open the following link in your browser:\n%s\n", $authUrl);
      print 'Enter verification code: ';
      $authCode = trim(fgets(STDIN));
      $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
      $client->setAccessToken($accessToken);
      if (array_key_exists('error', $accessToken)) {
        throw new Exception(join(', ', $accessToken));
      }
    }
    if (!file_exists(dirname($tokenPath))) {
      mkdir(dirname($tokenPath), 0700, true);
    }
    file_put_contents($tokenPath, json_encode($client->getAccessToken()));
  }
  return $client;
}
function createMessage($sender, $to, $subject, $messageText, $altText, $alias)
{
  $message = new Google_Service_Gmail_Message();
  /*
  $rawMessageString = "From: \"Grupo Capacitacion ACG\" <{$sender}>\r\n";
  $rawMessageString .= "To: <{$to}>\r\n";
  $rawMessageString .= 'Subject: =?utf-8?B?' . base64_encode($subject) . "?=\r\n";
  $rawMessageString .= "MIME-Version: 1.0\r\n";
  $rawMessageString .= "Content-Type: multipart/mixed;\r\n";
  $boundary = uniqid("_Part_".time(), true); 
  $boundary2 = uniqid("_Part2_".time(), true);
  $rawMessageString .= " boundary=\"$boundary\"\n";
  $rawMessageString .= "\n";
  $rawMessageString .= "--$boundary\n";
  $rawMessageString .= "Content-Type: multipart/alternative;\n";
  $rawMessageString .= " boundary=\"$boundary2\"\n";
  $rawMessageString .= "\n";
  $rawMessageString .= "--$boundary2\n";
  $rawMessageString .= "Content-Type: text/plain; charset=utf-8\n";
  $rawMessageString .= "Content-Transfer-Encoding: 7bit\n";
  $rawMessageString .= "\n";
  $rawMessageString .= $altText;
  $rawMessageString .= "\n";
  $rawMessageString .= "--$boundary2\n";
  $rawMessageString .= "Content-Type: text/html; charset=utf-8\n";
  $rawMessageString .= "Content-Transfer-Encoding: 7bit\n";
  $rawMessageString .= "\n";
  $rawMessageString .= $messageText;
  $rawMessageString .= "\n";
  $rawMessageString .= "--$boundary2--\n";
  $rawMessageString .= "\n";
  $rawMessageString .= "--$boundary\n";
  $rawMessageString .= "Content-Transfer-Encoding: base64\n";
  $rawMessageString .= "Content-Type: {application/pdf}; name=plantilla.pdf;\n";
  $rawMessageString .= "Content-Disposition: attachment; filename=plantilla.pdf;\n";
  $rawMessageString .= "\n";
  $rawMessageString .= base64_encode(file_get_contents('plantilla.pdf'));
  $rawMessageString .= "\n--$boundary";
  $rawMessageString .= "--\n";
  //$rawMessageString .= "Content-Type: text/html; charset=utf-8\r\n";
  //$rawMessageString .= 'Content-Transfer-Encoding: quoted-printable' . "\r\n\r\n";
  //$rawMessageString .= "{$messageText}\r\n";
  //$rawMessage = strtr(base64_encode($rawMessageString), array('+' => '-', '/' => '_'));
  $rawMessage = rtrim(strtr(base64_encode($rawMessageString), '+/', '-_'), '=');
  $rawMessage = rtrim(strtr(base64_encode($msg), '+/', '-_'), '=');
  $message->setRaw($rawMessage);
  */
  $mail = new PHPMailer();
  $mail->CharSet = PHPMailer::CHARSET_UTF8;
  $mail->From = $sender;
  $mail->FromName = $alias;
  $mail->AddAddress($to);
  $mail->AddReplyTo($sender, $alias);
  $mail->Subject = $subject;
  $mail->Body = $messageText;
  $mail->AltBody = $altText;
  $mail->addAttachment('plantilla.pdf', 'adjunto.pdf', 'base64', 'application/pdf');

  $mail->preSend();
  $mime = $mail->getSentMIMEMessage();
  $mime = rtrim(strtr(base64_encode($mime), '+/', '-_'), '=');
  $message->setRaw($mime);

  return $message;
}
