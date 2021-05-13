<?php
require("config.php");
$nombres = array("id", "nombres", "apellidos", "email", "identificacion", "curso", "notaFinal", "fecha");
$query = "SELECT u.id " . $nombres[0] . ", ";
$query .= "u.firstname " . $nombres[1] . ", ";
$query .= "u.lastname " . $nombres[2] . ", ";
$query .= "u.email " . $nombres[3] . ", ";
$query .= "u.idnumber " . $nombres[4] . ", ";
$query .= "c.fullname " . $nombres[5] . ", ";
$query .= "ROUND(gg.finalgrade,2) " . $nombres[6] . ", ";
$query .= "FROM_UNIXTIME(gg.timemodified) " . $nombres[7] . " ";
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
}
print_r(json_encode($resultado));
