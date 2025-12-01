<?php
header('Content-Type: application/json');
require("../config/config.php");
$salida = $database->select($tablas["config"], "value", ["name" => "siteadmins"]);
print_r(json_encode(explode(",", $salida[0])));
