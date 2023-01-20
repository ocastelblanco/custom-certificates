<?php
require("../config/config.php");
$last_id = $database->query("SELECT max(id) FROM " . $tablas["cert"])->fetchAll()[0][0];
$certid = $last_id + 1;
if (isset($_POST) && isset($_POST["userid"]) && isset($_POST["courseid"])) {
  $intensidad = array_values(array_filter($listaCursos, function ($v) {
    return $v["id"] == $_POST["courseid"];
  }))[0]["intensidad"];
  $database->insert($tablas["cert"], [
    "id" => $certid,
    "userid" => $_POST["userid"],
    "courseid" => $_POST["courseid"],
    //"intensidad" => $intensidades[$_POST["courseid"]],
    "intensidad" => $intensidad,
    "fecha" => time(),
    "notificacion" => "0"
  ]);
}
print_r(json_encode(array("id" => $certid)));
