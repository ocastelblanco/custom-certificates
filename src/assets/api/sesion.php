<?php
if (isset($_GET["logout"])) {
  if (session_id()) {
    session_destroy();
  }
} elseif (isset($_POST["id"])) {
  session_start(['cookie_lifetime' => 3600]);
  foreach ($_POST as $key => $val) {
    $_SESSION[$key] = $val;
  }
}
$sesionid = session_id();
if (isset($sesionid) && isset($_SESSION)) {
  $salida = array_merge($_SESSION, array("sesionid" => $sesionid));
} else {
  if (isset($_SESSION)) {
    foreach ($_SESSION as $key => $value) {
      unset($_SESSION[$key]);
    }
  }
  $salida = false;
}
if ($salida) {
  $salida["admin"] === "true" ? $salida["admin"] = true : $salida["admin"] = false;
}
print_r(json_encode($salida));
