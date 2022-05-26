<?php
require('../config/config.php');
require('../lib/vendor/autoload.php');

date_default_timezone_set('America/Bogota');

$client = getClient();
$service = new Google_Service_Gmail($client);

if (
  isset($_POST["asunto"]) &&
  isset($_POST["correo"]) &&
  isset($_POST["nombre"]) &&
  isset($_POST["contenido"]) &&
  isset($_POST["altbody"]) /*&&
  isset($_POST["adjunto"])*/
) {
  $asunto = $_POST["asunto"];
  $correo = $_POST["correo"];
  $nombre = $_POST["nombre"];
  $contenido = $_POST["contenido"];
  $altbody = $_POST["altbody"];
  //$adjunto = $_POST["adjunto"];

  $dirAdjunto = __DIR__ . "/certificados";

  $email = "ocastelblanco@gmail.com";

  $mensaje = creaMensaje($email, $asunto, $contNotifHTML, $contNotifALT); //, $adjunto, $dirAdjunto);
  $userId = 'me';
  try {
    $message = $service->users_messages->send($userId, $mensaje);
    print_r(json_encode(array("error" => null, "message" => "Se ha enviado el correo con ID " . $message->getId())));
  } catch (Exception $e) {
    print_r(json_encode(array("error" => $e->getMessage())));
  }
}
