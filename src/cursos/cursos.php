<?php
$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
if ($contentType === "application/json") {
  if (isset($_GET) && isset($_GET["url"])) {
    $url = $_GET["url"];
    $url = "../assets/data/cursos.json";
    $crudo = trim(file_get_contents("php://input"));
    $cursos = json_decode($crudo, true);
    if (
      $cursos &&
      count($cursos) > 0 &&
      $cursos[0] &&
      $cursos[0]["id"]
    ) {
      if (file_put_contents($url, $crudo)) {
        $salida = [
          "error" => null,
          "cursos" => $cursos,
          "url" => $url
        ];
        print json_encode($salida);
      } else {
        print '{"error": "No se pudo escribir en el JSON"}';
      }
    } else {
      print '{"error": "No se pudo decodificar el JSON"}';
    }
  } else {
    print '{"error": "No se envió la URL del JSON destino"}';
  }
} else {
  print '{"error": "La información enviada no tiene el formato correcto"}';
}
