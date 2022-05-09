<?php
// Se verifica que sea CLI
if (php_sapi_name() != 'cli') {
  throw new Exception('Esta aplicación solo puede ejecutarse desde CLI.');
}
// Almacena las variables enviadas por CLI en $_VAR
$_VAR = [];
foreach ($argv as $pos => $arg) {
  if ($pos == 0) continue;
  if (substr($arg, 0, 2) == '--') {
    $key = explode("=", substr($arg, 2))[0];
    $val = explode("=", substr($arg, 2))[1];
    $_VAR[$key] = $val;
  } elseif (substr($arg, 0, 1) == '-') {
    $key = substr($arg, 1);
    $_VAR[$key] = true;
  }
}
require("../config/config.php");
$last_id = $database->query("SELECT max(id) FROM " . $tablas["cert"])->fetchAll()[0][0];
$certid = $last_id + 1;
if (isset($_VAR) && isset($_VAR["userid"]) && isset($_VAR["courseid"])) {
  $database->insert($tablas["cert"], [
    "id" => $certid,
    "userid" => $_VAR["userid"],
    "courseid" => $_VAR["courseid"],
    "intensidad" => $intensidades[$_VAR["courseid"]],
    "fecha" => time(),
    "notificacion" => "0"
  ]);
}
print_r(json_encode(array("id" => $certid)));
