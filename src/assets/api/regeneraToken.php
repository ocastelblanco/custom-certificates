<?php

$configDir = "/var/www/html/aulavirtual/certificados/assets/config";
require("$configDir/config.php");
$tokenPath = "$configDir/token.json";
$client = new Google\Client();
$client->setAuthConfig("$configDir/credentials.json");
$client->setScopes(Google\Service\Gmail::MAIL_GOOGLE_COM);
$client->setAccessType('offline');

if (file_exists($tokenPath)) {
  $accessToken = json_decode(file_get_contents($tokenPath), true);
  if ($accessToken) {
    $creado = $accessToken["created"];
    $accessToken = $client->setAccessToken($accessToken);
    $lapso = time() - $creado;
    $maxLapso = 12 * 60 * 60;
    if ($lapso > $maxLapso) {
      unlink($tokenPath);
      throw new Exception('El token se ha borrado porque está muy desactualizado.');
    }
  }
  if ($client->isAccessTokenExpired()) {
    print "El token expiró.\n";
    $refreshToken = $client->getRefreshToken();
    print "Se reintenta actualizar un nuevo token.\n";
    if ($refreshToken) {
      $tokenRecargado = $client->fetchAccessTokenWithRefreshToken($refreshToken);
      $getAccessToken = $client->getAccessToken();
      if ($getAccessToken && array_key_exists('error', $getAccessToken)) {
        throw new Exception(join(', ', $getAccessToken));
      }
      file_put_contents($tokenPath, json_encode($getAccessToken));
      print "Se creó un nuevo token recargado.\n";
    }
  } else {
    print "El token no ha expirado aún.\n";
  }
}
