<?php
if (php_sapi_name() != 'cli') {
  throw new Exception('Esta aplicación solo puede ejecutarse desde CLI.');
}
require('../lib/vendor/autoload.php');
function getClient()
{
  $client = new Google_Client();
  $client->setApplicationName('ACG Certificates');
  $client->setScopes(Google_Service_Gmail::MAIL_GOOGLE_COM);
  $client->setAuthConfig('credentials.json');
  $client->setAccessType('offline');
  $client->setPrompt('select_account consent');

  // Load previously authorized token from a file, if it exists.
  // The file token.json stores the user's access and refresh tokens, and is
  // created automatically when the authorization flow completes for the first
  // time.
  $tokenPath = 'token.json';
  if (file_exists($tokenPath)) {
    $accessToken = json_decode(file_get_contents($tokenPath), true);
    $client->setAccessToken($accessToken);
  }
  // If there is no previous token or it's expired.
  if ($client->isAccessTokenExpired()) {
    // Refresh the token if possible, else fetch a new one.
    if ($client->getRefreshToken()) {
      $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
    } else {
      // Request authorization from the user.
      $authUrl = $client->createAuthUrl();
      printf("Open the following link in your browser:\n%s\n", $authUrl);
      print 'Enter verification code: ';
      $authCode = trim(fgets(STDIN));

      // Exchange authorization code for an access token.
      $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
      $client->setAccessToken($accessToken);

      // Check to see if there was an error.
      if (array_key_exists('error', $accessToken)) {
        throw new Exception(join(', ', $accessToken));
      }
    }
    // Save the token to a file.
    if (!file_exists(dirname($tokenPath))) {
      mkdir(dirname($tokenPath), 0700, true);
    }
    file_put_contents($tokenPath, json_encode($client->getAccessToken()));
  }
  return $client;
}
// Get the API client and construct the service object.
$client = getClient();
$service = new Google_Service_Gmail($client);

$email = 'cursosvirtualesacg@gmail.com';
$contHTML = '
  <!DOCTYPE html>
    <html>
      <head>
        <meta charset="utf-8">
      </head>
      <body>
        <p>Estimado(a) Doctor(a) <strong>{{nombre}}</strong>:</p>
        <p>De parte del Grupo de Capacitación de ACG Calidad le queremos agradecer su participación en nuestro curso virtual <strong>{{curso}}</strong>.</p>
        <p>Para descargar el Certificado, ingrese a nuestro Campus Virtual http://www.acgcalidadeducacion.com/ (con el mismo usuario y contraseña de siempre) y haga clic sobre el vínculo <strong>OBTENER CERTIFICADO</strong> que se encuentra al inicio de la página principal.</p>
        <p>Si tiene alguna duda, puede consultar el instructivo en video: https://youtu.be/Gq-UkAvsFOo</p>
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

  Para descargar el certificado, ingrese a nuestro Campus Virtual con el mismo usuario y contraseña de siempre, http://www.acgcalidadeducacion.com/, y haga clic sobre el vínculo OBTENER CERTIFICADO que se encuentra al inicio de la página principal.

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

    //$correo = "ocastelblanco@gmail.com";

    $mensaje = createMessage($email, $correo, $asunto, $contenido);

    echo "********* Enviando correo $numLinea desde $email *********\n";
    echo "Nombre: $nombre | Correo: $correo\n";
    //*
    $userId = 'me';
    try {
      $message = $service->users_messages->send($userId, $mensaje);
      echo 'Mensaje con ID: ' . $message->getId() . ' enviado correctamente.';
    } catch (Exception $e) {
      echo 'Ocurrió un error: ' . $e->getMessage();
    }
    //*/
    echo "\n*******************************************************\n";
  }
}

function createMessage($sender, $to, $subject, $messageText)
{
  $message = new Google_Service_Gmail_Message();

  $rawMessageString = "From: \"Grupo Capacitacion ACG\" <{$sender}>\r\n";
  $rawMessageString .= "To: <{$to}>\r\n";
  $rawMessageString .= 'Subject: =?utf-8?B?' . base64_encode($subject) . "?=\r\n";
  $rawMessageString .= "MIME-Version: 1.0\r\n";
  $rawMessageString .= "Content-Type: text/html; charset=utf-8\r\n";
  $rawMessageString .= 'Content-Transfer-Encoding: quoted-printable' . "\r\n\r\n";
  $rawMessageString .= "{$messageText}\r\n";

  $rawMessage = strtr(base64_encode($rawMessageString), array('+' => '-', '/' => '_'));
  $message->setRaw($rawMessage);
  return $message;
}
/*
// Print the labels in the user's account.
$user = 'me';
$results = $service->users_labels->listUsersLabels($user);

if (count($results->getLabels()) == 0) {
  print "No labels found.\n";
} else {
  print "Labels:\n";
  foreach ($results->getLabels() as $label) {
    printf("- %s\n", $label->getName());
  }
}
*/