<?php
// Envía notificaciones sobre GENERACIÓN DE CERTIFICADOS
require('../config/config.php');
require('../lib/vendor/autoload.php');

date_default_timezone_set('America/Bogota');

$client = getClient();
$service = new Google_Service_Gmail($client);

if (
  isset($_POST["asunto"])
  && isset($_POST["correo"])
  && isset($_POST["nombre"])
  && isset($_POST["curso"])
  //&& isset($_POST["adjunto"])*/
) {
  $asunto = $_POST["asunto"];
  $email = $_POST["correo"];
  $nombre = $_POST["nombre"];
  $curso = $_POST["curso"];
  //$adjunto = $_POST["adjunto"];

  $contenido = preg_replace("/{{nombre}}/", $nombre, $contenidoHTML);
  $contenido = preg_replace("/{{curso}}/", $curso, $contenido);
  $altbody = preg_replace("/{{nombre}}/", $nombre, $contenidoALT);
  $altbody = preg_replace("/{{curso}}/", $curso, $altbody);

  $dirAdjunto = __DIR__ . "/certificados";

  //$email = "ocastelblanco@gmail.com";

  $mensaje = creaMensaje($email, $asunto, $contenido, $altbody); //, $adjunto, $dirAdjunto);
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
