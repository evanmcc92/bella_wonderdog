<?php

require 'src/Instagram.php';

use MetzWeb\Instagram\Instagram;

// initialize class
$instagram = new Instagram(array(
	'apiKey' => 'eda53af4356b4d2d87f9179786ba63dd',
	'apiSecret' => 'f55117ae3ed24495a6ece2125b6de98f',
	'apiCallback' => 'https://pacific-journey-4584.herokuapp.com/edit.php' // must point to success.php
));

// create login URL
$loginUrl = $instagram->getLoginUrl(array('likes','basic'));

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Instagram - OAuth Login</title>
	<link rel="stylesheet" type="text/css" href="assets/style.css">
	<style>
		.login {
			display: block;
			font-size: 20px;
			font-weight: bold;
			margin-top: 50px;
		}
	</style>
</head>
<body>
<div class="container">
	<header class="clearfix">
		<h1>Instagram <span>display your photo stream</span></h1>
	</header>
	<div class="main">
		<ul class="grid">
			<li><img src="assets/instagram-big.png" alt="Instagram logo"></li>
			<li>
				<a class="login" href="<?php echo $loginUrl ?>">» Login with Instagram</a>
				<h4>Use your Instagram account to login.</h4>
			</li>
		</ul>
	</div>
</div>
</body>
</html>
