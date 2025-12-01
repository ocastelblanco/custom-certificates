<?php
require("../config/config.php");
if (isset($_GET["id"])) {
  $database->update($tablas["cert"], [
    "notificacion" => time()
  ], [
    "id" => $_GET["id"]
  ]);
  header('Content-Type: application/json');
  print_r(json_encode(array("id" => $_GET["id"])));
}
