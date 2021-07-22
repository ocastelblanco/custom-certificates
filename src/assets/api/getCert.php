<?php
require("../config/config.php");
$where_id = array("userid[>]" => 0);
if (isset($_GET["id"])) $where_id = array("userid" => $_GET["id"]);
$resultado = $database->select(
  $tablas["cert"],
  [
    "[>]" . $tablas["user"] => ["userid" => "id"],
    "[>]" . $tablas["course"] => ["courseid" => "id"]
  ],
  [
    $tablas["cert"] . ".id",
    $tablas["cert"] . ".userid",
    $tablas["cert"] . ".courseid",
    $tablas["cert"] . ".intensidad",
    $tablas["cert"] . ".fecha",
    $tablas["cert"] . ".notificacion",
    $tablas["user"] . ".firstname",
    $tablas["user"] . ".lastname",
    $tablas["user"] . ".email",
    $tablas["user"] . ".idnumber",
    $tablas["user"] . ".institution",
    $tablas["user"] . ".city",
    $tablas["user"] . ".country",
    $tablas["course"] . ".fullname",
    $tablas["course"] . ".shortname"
  ],
  $where_id
);
foreach ($resultado as $num => $res) {
  $resultado[$num]["coursename"] = $nombresCursos[$res["shortname"]];
}
print_r(json_encode($resultado));
