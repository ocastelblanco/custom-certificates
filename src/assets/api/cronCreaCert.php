<?php
// Se verifica que sea CLI
if (php_sapi_name() != 'cli') {
  throw new Exception('Esta aplicación solo puede ejecutarse desde CLI.');
}
// Almacena las variables enviadas por CLI en $_VAR
$_VAR = [];
foreach ($argv as $pos => $arg) {
  if ($pos == 0) continue;
  if (substr($arg, 0, 2) == '--') {
    $key = explode("=", substr($arg, 2))[0];
    $val = explode("=", substr($arg, 2))[1];
    $_VAR[$key] = $val;
  } elseif (substr($arg, 0, 1) == '-') {
    $key = substr($arg, 1);
    $_VAR[$key] = true;
  }
}

require('../config/config.php');

use setasign\Fpdi\Fpdi;

$nombre = $_VAR['nombre'];
$identificacion = $_VAR['identificacion'];
$cursolargo = $_VAR['cursolargo'];
$cursocorto = $_VAR['cursocorto'];
$intensidad = $_VAR['intensidad'];
$fecha = $_VAR['fecha'];
$num = $_VAR['num'];

$archivo = "$num-$cursocorto-$identificacion-$fecha.pdf";
$pdf = new Fpdi();
$pdf->AddPage();
$pdf->setSourceFile('plantilla.pdf');
$tplIdx = $pdf->importPage(1);
$dim = $pdf->getTemplateSize($tplIdx);
$ancho = $dim["width"];
$alto = $dim["height"];
$pdf->useTemplate($tplIdx, 0, 0, $ancho, $alto, true);
$pdf->SetAutoPageBreak(false);
$pdf->SetMargins(0, 0);
$pdf->SetTextColor(0, 0, 0);

escribe($pdf, $nombre, "BI", 18, 67);
escribe($pdf, $identificacion, "BI", 14, 75);
escribe($pdf, $cursolargo, "I", 14, 97);
escribe($pdf, "INTENSIDAD $intensidad HORAS", "I", 12, 115);
escribe($pdf, "BOGOTÁ, COLOMBIA, $fecha", "I", 12, 120);

$pdf->SetFont('Helvetica', "", 12);
$pdf->SetXY(264, 202.6);
$pdf->Cell(0, 0, $num);

$pdf->Output('F', "certificados/$archivo");
echo $archivo;

function escribe($pdf, $texto, $estilo, $size, $pos)
{
  $salida = trim($texto);
  $salida = utf8_decode($salida);
  if (strlen($salida) > 60) $salida = preg_replace("/(?<=,)\s/", "|", $salida);
  $lineas = explode("|", $salida);
  $pdf->SetFont('Helvetica', $estilo, $size);
  $y = $pos;
  foreach ($lineas as $linea) {
    $pdf->SetXY(0, $y);
    $pdf->Cell(0, 0, $linea, 0, 0, 'C');
    $y += ($size / 3) + 3;
  }
}
