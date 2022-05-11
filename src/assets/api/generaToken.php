<?php

namespace PHPMailer\PHPMailer;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
header("Access-Control-Allow-Methods: HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS");

require('../config/config.php');

use League\OAuth2\Client\Provider\Google;

session_start();

$providerName = 'Google';

if (array_key_exists('provider', $_SESSION)) {
	$providerName = $_SESSION['provider'];
} else {
	$_SESSION['provider'] = $providerName;
}

$archivo = '../config/token.txt';
//If this automatic URL doesn't work, set it yourself manually to the URL of this script
$redirectUri = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
//$redirectUri = 'http://http://aulavirtual.acgcalidad.co/certificados/assets/api/generaToken.php';

$params = [
	'clientId' => $clientId,
	'clientSecret' => $clientSecret,
	'redirectUri' => $redirectUri,
	'accessType' => 'offline'
];
$provider = new Google($params);
$options = [
	'scope' => [
		'https://mail.google.com/'
	]
];

if (!isset($_GET['code'])) {
	//If we don't have an authorization code then get one
	$authUrl = $provider->getAuthorizationUrl($options);
	$_SESSION['oauth2state'] = $provider->getState();
	header('Location: ' . $authUrl);
	exit;
	//Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
	unset($_SESSION['oauth2state']);
	unset($_SESSION['provider']);
	exit('Invalid state');
} else {
	unset($_SESSION['provider']);
	//Try to get an access token (using the authorization code grant)
	$token = $provider->getAccessToken(
		'authorization_code',
		[
			'code' => $_GET['code']
		]
	);
	//Use this to interact with an API on the users behalf
	//Use this to get a new access token if the old one expires
	$nuevoToken = $token->getRefreshToken() ?? $token->getToken();
	file_put_contents($archivo, $nuevoToken);
	print_r(json_encode(array("token" => $nuevoToken)));
}
