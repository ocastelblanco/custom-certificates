<?php
require("../config/config.php");
$last_id = $database->query("SELECT max(id) FROM " . $tablas["cert"])->fetchAll()[0][0];
$certid = $last_id + 1;
if (isset($_POST) && isset($_POST["userid"]) && isset($_POST["courseid"])) {
  $database->insert($tablas["cert"], [
    "id" => $certid,
    "userid" => $_POST["userid"],
    "courseid" => $_POST["courseid"],
    "intensidad" => "40",
    "fecha" => time(),
    "notificacion" => "0"
  ]);
}
print_r(json_encode(array("id" => $certid)));
