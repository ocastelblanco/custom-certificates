<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
header("Access-Control-Allow-Methods: HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS");

require('../config/config.php');
require('../lib/vendor/autoload.php');

date_default_timezone_set('America/Bogota');

chdir('../config');
$configDir = getcwd();
$client = new Google\Client();
$client->setApplicationName('ACG Certificates');
$client->setScopes(Google\Service\Gmail::MAIL_GOOGLE_COM);
$client->setAuthConfig("$configDir/credentials.json");
$client->setAccessType('offline');
$client->setPrompt('consent');
$tokenPath = "$configDir/token.json";

if (file_exists($tokenPath)) {
	$accessToken = json_decode(file_get_contents($tokenPath), true);
	if ($accessToken) {
		$client->setAccessToken($accessToken);
	}
}

if ($client->isAccessTokenExpired()) {
	if (file_exists($tokenPath)) unlink($tokenPath);
	if ($client->getRefreshToken()) {
		$client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
	} else {
		$authUrl = $client->createAuthUrl();
		if (isset($_GET['accion'])) {
			print_r(json_encode(array("token" => false)));
			exit();
		} else {
			echo "<script>window.location.href = '$authUrl';</script>";
		}
		if (isset($_GET['code'])) {
			$authCode = $_GET['code'];
			$accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
			$client->setAccessToken($accessToken);
		}
		if (isset($accessToken) && array_key_exists('error', $accessToken)) {
			if (isset($_GET['accion'])) {
				print_r(json_encode(array("token" => false)));
				exit();
			} else {
				throw new Exception(join(', ', $accessToken));
			}
		}
	}
	file_put_contents($tokenPath, json_encode($client->getAccessToken()));
}

if (isset($_GET['accion'])) {
	print_r(json_encode(array("token" => $client->getAccessToken())));
} else {
	echo "Token:<br> ";
	print_r(json_encode($client->getAccessToken()));
	echo "<br>Cierre esta pestaña y vuelva al anterior proceso.";
}
