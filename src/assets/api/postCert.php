<?php
require("../config/config.php");
$intensidades = array(
  2 => "40",
  3 => "40",
  4 => "40",
  5 => "40",
  6 => "40",
  7 => "40",
  8 => "40",
  9 => "40",
  10 => "40",
  11 => "40",
  12 => "40",
  13 => "40",
  14 => "40",
  15 => "40",
  16 => "40",
  17 => "40",
  18 => "40"
);
$last_id = $database->query("SELECT max(id) FROM " . $tablas["cert"])->fetchAll()[0][0];
$certid = $last_id + 1;
if (isset($_POST) && isset($_POST["userid"]) && isset($_POST["courseid"])) {
  $database->insert($tablas["cert"], [
    "id" => $certid,
    "userid" => $_POST["userid"],
    "courseid" => $_POST["courseid"],
    "intensidad" => $intensidades[$_POST["courseid"]],
    "fecha" => time(),
    "notificacion" => "0"
  ]);
}
print_r(json_encode(array("id" => $certid)));
/*
1	ACG Calidad - Campus Virtual
2	EM_seis_sigma
3	EM_gestion_riesgo
5	EM_paciente_seguro
6	EM_calidad_analitica
7	bioseguridad-covid19
8	SEM_Diplom_Calidad
9	EMI_diplo_calidad
10	EM_bioseguridad-covid19
11	EMI_diplo_calidad_2020-11-05
14	EMI_diplo_calidad_2021-08
16	EM_seis_sigma_julio-2021
17	EMI_poct_no_bact
18	EM_salud_trabajo
*/