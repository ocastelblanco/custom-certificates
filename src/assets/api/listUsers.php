<?php
require("../config/config.php");
$nombres = array("userid", "nombres", "apellidos", "email", "identificacion", "fullname", "shortname", "courseid", "notaFinal", "fecha");
$query = "SELECT u.id " . $nombres[0] . ", ";
$query .= "u.firstname " . $nombres[1] . ", ";
$query .= "u.lastname " . $nombres[2] . ", ";
$query .= "u.email " . $nombres[3] . ", ";
$query .= "u.idnumber " . $nombres[4] . ", ";
$query .= "c.fullname " . $nombres[5] . ", ";
$query .= "c.shortname " . $nombres[6] . ", ";
$query .= "c.id " . $nombres[7] . ", ";
$query .= "ROUND(gg.finalgrade,2) " . $nombres[8] . ", ";
$query .= "FROM_UNIXTIME(gg.timemodified) " . $nombres[9] . " ";
$query .= "FROM mo_course AS c ";
$query .= "JOIN mo_context AS ctx ON c.id = ctx.instanceid ";
$query .= "JOIN mo_role_assignments AS ra ON ra.contextid = ctx.id ";
$query .= "JOIN mo_user AS u ON u.id = ra.userid ";
$query .= "JOIN mo_grade_grades AS gg ON gg.userid = u.id ";
$query .= "JOIN mo_grade_items AS gi ON gi.id = gg.itemid ";
$query .= "WHERE gi.courseid = c.id AND gi.itemtype = 'course' ";
if (isset($_GET["id"])) $query .= "AND u.id = " . $_GET["id"];
$resultado = $database->query($query)->fetchAll();
foreach ($resultado as $num => $res) {
  foreach ($res as $clave => $valor) {
    if (false === array_search($clave, $nombres) || $clave === 0) unset($resultado[$num][$clave]);
  }
  $resultado[$num]["coursename"] = $nombresCursos[$res["shortname"]];
}
$cert = $database->select($tablas["cert"], ["id", "userid", "courseid"]);
$salida = array();
foreach ($resultado as $el) {
  $coinc = false;
  foreach ($cert as $ce) {
    if ($el["userid"] === $ce["userid"] && $el["courseid"] === $ce["courseid"]) $coinc = true;
  }
  !$coinc ? array_push($salida, $el) : null;
}
print_r(json_encode($salida));
//print_r(json_encode($resultado));
