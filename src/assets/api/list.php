<?php
require("../config/config.php");
$id = $_GET["id"];
$salida = $database->select(
  $tablas["cert"],
  [
    "[>]" . $tablas["user"] => ["userid" => "id"],
    "[>]" . $tablas["course"] => ["courseid" => "id"]
  ],
  [
    $tablas["cert"] . ".id",
    $tablas["cert"] . ".intensidad",
    $tablas["cert"] . ".fecha",
    $tablas["user"] . ".firstname",
    $tablas["user"] . ".lastname",
    $tablas["user"] . ".idnumber",
    $tablas["user"] . ".institution",
    $tablas["user"] . ".city",
    $tablas["user"] . ".country",
    $tablas["course"] . ".fullname"
  ],
  [
    $tablas["cert"] . ".id" => $id
  ]
);
print_r(json_encode($salida));
