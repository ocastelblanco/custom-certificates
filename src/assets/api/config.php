<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
header("Access-Control-Allow-Methods: HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS");
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

require('Medoo.php');

use Medoo\Medoo;

$database = new Medoo([
    'database_type' => 'mysql',
    'database_name' => 'custom_certificates',
    'server' => 'localhost',
    'username' => 'root',
    'password' => 'millonarios'
]);
$database->query("SET NAMES 'utf8'");
$tablas = array(
    "cert" => "cc_certificados",
    "user" => "mo_user",
    "course" => "mo_course",
    "config" => "mo_config",
    "ctx" => "mo_context",
    "ra" => "mo_role_assignments",
    "gg" => "mo_grade_grades",
    "gi" => "mo_grade_items"
);
