<?php
/* ********************************************************

Por ahora, no debería usarse Gmail para enviar correos desde CRON, ya que es necesario autenticación regular del usuario.

Deberá implementarse cuando se haya creado un sistema funcional de envío de correos desde @acgcalidad.co por AWS SES.

******************************************************** */
// Se verifica que sea CLI
if (php_sapi_name() != 'cli') {
  throw new Exception('Esta aplicación solo puede ejecutarse desde CLI.');
}
// Se autentica con GMail
exec('php generaToken.php', $token);

// Se obtiene la lista de usuarios que faltan por certificar
exec('php cronListUsers.php', $output);
$usuarios = json_decode($output[0], true);

// Se inicia el ciclo por cada usuario
foreach ($usuarios as $usuario) {
  $nombre = $usuario['nombres'] . ' ' . $usuario['apellidos'];
  $vars = '--nombre="' . $nombre . '" ';
  $vars .= '--identificacion="' . $usuario['identificacion'] . '" ';
  $vars .= '--cursolargo="' . $usuario['fullname'] . '" ';
  $vars .= '--cursocorto="' . $usuario['shortname'] . '" ';
  $vars .= '--intensidad="40" ';
  $vars .= '--fecha="666" ';
  $vars .= '--num="666" ';

  // Se crea el certificado PDF
  exec('php cronCreaCert.php $vars', $creaCert);

  $pdf = $creaCert[0];

  if (!$pdf) {
    echo "No se pudo crear el certificado para $nombre\n";
    continue;
  }
  $asunto = 'Certificado de curso virtual en ACG Calidad';
  $correo = $usuario['email'];
  $contenido = preg_replace("/\{\{nombre\}\}/", $nombre, $contenidoHTML);
  $contenido = preg_replace("/\{\{curso\}\}/", $usuario['fullname'], $contenido);
  $altbody = preg_replace("/\{\{nombre\}\}/", $nombre, $contenidoALT);
  $altbody = preg_replace("/\{\{curso\}\}/", $usuario['fullname'], $altbody);
  $vars = '--asunto="' . $asunto . '" ';
  $vars .= '--correo="' . $correo . '" ';
  $vars .= '--nombre="' . $nombre . '" ';
  $vars .= '--contenido="' . $contenido . '" ';
  $vars .= '--altbody="' . $altbody . '" ';
  $vars .= '--pdf="' . $pdf . '" ';

  // Se envía el correo electrónico
  exec('php cronSendMail.php $vars', $enviaCorreo);

  $error = json_decode($enviaCorreo[0], true);
  if ($error['error']) {
    echo $error['error'] . "\n";
    continue;
  }

  $datoscli = '--userid=' . $usuario['userid'] . ' ';
  $datoscli .= '--courseid=' . $usuario['courseid'] . ' ';
  // Se guarda en la base de datos el usuario certificado
  exec('php cronPutCert.php $datoscli', $putCert);
}
