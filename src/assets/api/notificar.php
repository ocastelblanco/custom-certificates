<?php
require('../config/config.php');
require('../lib/vendor/autoload.php');

date_default_timezone_set('America/Bogota');

$client = getClient();
$service = new Google_Service_Gmail($client);

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

  //$email = "ocastelblanco@gmail.com";

  $mensaje = creaMensaje($email, $asunto, $contNotifHTML, $contNotifALT);
  $message = new Google_Service_Gmail_Message();
  $message->setRaw($mensaje);
  $userId = 'me';
  try {
    $envio = $service->users_messages->send($userId, $message);
    print_r(json_encode(array("error" => null, "message" => "Se ha enviado el correo con ID " . $message->getId())));
  } catch (Exception $e) {
    print_r(json_encode(array("error" => $e->getMessage())));
  }
}
