<?php
require("config.php");
$nombresCursos = array(
  "ACG Calidad - Campus Virtual" => "ACG CALIDAD",
  "EM_calidad_analitica" => "GESTIÓN DE LA CALIDAD",
  "EM_paciente_seguro" => "PACIENTE SEGURO",
  "EM_gestion_riesgo" => "GESTIÓN DEL RIESGO",
  "EM_seis_sigma" => "SEIS SIGMA",
  "EM_bioseguridad-covid19" => "BIOSEGURIDAD COVID-19",
  "bioseguridad-covid19" => "BIOSEGURIDAD COVID-19",
  "EMI_diplo_calidad" => "DIPLOMADO DE CALIDAD",
  "EMI_diplo_calidad_2020-11-05" => "DIPLOMADO DE CALIDAD NOV 2020",
  "EMI_diplo_calidad_2021-02-22" => "DIPLOMADO DE CALIDAD FEB 2021",
  "SEM_Diplom_Calidad" => "Semilla DIPLOMADO DE CALIDAD"
);
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
