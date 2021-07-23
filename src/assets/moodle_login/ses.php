<?php
require('../config.php');
require_once('lib.php');
header('Access-Control-Allow-Origin: *');
$salida = false;
if (isset($_GET["logout"])) {
  require_logout();
} else {
  if (isloggedin()) {
    $salida = array(
      "id" => $USER->id,
      "idnumber" => $USER->idnumber,
      "firstname" => $USER->firstname,
      "lastname" => $USER->lastname,
      "email" => $USER->email,
      "city" => $USER->city,
      "country" => $USER->country,
      "institution" => $USER->institution,
      "admin" => is_siteadmin(),
    );
  }
}
print_r(json_encode($salida));