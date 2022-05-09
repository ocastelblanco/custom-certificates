<?php
if (php_sapi_name() != 'cli') {
  throw new Exception('Esta aplicación solo puede ejecutarse desde CLI.');
}
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
$query .= "FROM " . $tablas['course'] . " AS c ";
$query .= "JOIN " . $tablas['ctx'] . " AS ctx ON c.id = ctx.instanceid ";
$query .= "JOIN " . $tablas['ra'] . " AS ra ON ra.contextid = ctx.id ";
$query .= "JOIN " . $tablas['user'] . " AS u ON u.id = ra.userid ";
$query .= "JOIN " . $tablas['gg'] . " AS gg ON gg.userid = u.id ";
$query .= "JOIN " . $tablas['gi'] . " AS gi ON gi.id = gg.itemid ";
$query .= "WHERE gi.courseid = c.id AND gi.itemtype = 'course' ";
$query .= "AND ROUND(gg.finalgrade,2) > 79.99 ";
$query .= "AND FROM_UNIXTIME(gg.timemodified) > DATE_ADD(NOW(), INTERVAL -1 MONTH)";
if (isset($_GET["id"])) $query .= "AND u.id = " . $_GET["id"];
// Se obtiene el listado completo de todos los participantes con calificación
$resultado = $database->query($query)->fetchAll();
// Se depura el listado de registros con campos vacíos o repetidos 
foreach ($resultado as $num => $res) {
  foreach ($res as $clave => $valor) {
    if (false === array_search($clave, $nombres) || $clave === 0) unset($resultado[$num][$clave]);
  }
  // Se añade el "nombre común" del curso al listado
  $resultado[$num]["coursename"] = $nombresCursos[$res["shortname"]];
}
// Se obtiene el listado de Certificados ya generados
$cert = $database->select($tablas["cert"], ["id", "userid", "courseid"]);
$salida = array();
// Se genera un listado final donde SOLO están los participantes sin certificado en un curso dado.
foreach ($resultado as $el) {
  $coinc = false;
  foreach ($cert as $ce) {
    if ($el["userid"] === $ce["userid"] && $el["courseid"] === $ce["courseid"]) $coinc = true;
  }
  !$coinc ? array_push($salida, $el) : null;
}
print_r(json_encode($salida));
