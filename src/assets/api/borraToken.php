<?php
chdir('../config');
$configDir = getcwd();
$tokenPath = "$configDir/token.json";
if (file_exists($tokenPath)) {
  unlink($tokenPath);
}
