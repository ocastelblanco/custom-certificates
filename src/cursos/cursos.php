<?php

$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
if ($contentType === "application/json") {
  $crudo = trim(file_get_contents("php://input"));
  $cursos = json_decode($crudo, true);
  if ($cursos) {
    $salida = [
      "error" => null,
      "cursos" => $cursos
    ];
    print json_encode($salida);
  } else {
    print '{"error": "No se pudo decodificar el JSON"}';
  }
}
