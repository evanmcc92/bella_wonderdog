<?php

/**
 * Instagram PHP API
 *
 * @link https://github.com/cosenary/Instagram-PHP-API
 * @author Christian Metz
 * @since 01.10.2013
 */
require 'src/Instagram.php';
session_start();
use MetzWeb\Instagram\Instagram;

// initialize class
$instagram = new Instagram(array(
	'apiKey' => 'eda53af4356b4d2d87f9179786ba63dd',
	'apiSecret' => 'f55117ae3ed24495a6ece2125b6de98f',
	'apiCallback' => 'https://pacific-journey-4584.herokuapp.com/success.php' // must point to success.php
));
// receive OAuth code parameter
$code = $_SESSION['code'];

// check whether the user has granted access
if (isset($code)) {
	// receive OAuth token object
	$data = $_SESSION['data'];
	$username = $data->user->username;
	// store user access token
	$instagram->setAccessToken($data);
	// now you have access to all authenticated user methods
} else {
	// check whether an error occurred
	if (isset($_SESSION['error'])) {
		echo 'An error occurred: ' . $_SESSION['error_description'];
	}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Instagram - photo stream</title>
	<link href="https://vjs.zencdn.net/4.2/video-js.css" rel="stylesheet">
	<link href="assets/style.css" rel="stylesheet">
	<script src="https://vjs.zencdn.net/4.2/video.js"></script>
</head>
<body>
<div class="container">
	<header class="clearfix">
		<img src="assets/instagram.png" alt="Instagram logo">

		<h1>Instagram photos <span>taken by <?php echo $data->user->username ?></span></h1>
	</header>
	<div class="main">
		<ul class="grid">
			<?php
				$result = $instagram->getUserMedia();
				foreach ($media as $media) {
					if ($media->id == $_GET['id']) {
						$content = '<li>';
						// output media
						if ($media->type === 'video') {
							// video
							$poster = $media->images->low_resolution->url;
							$source = $media->videos->standard_resolution->url;
							$content .= "<video class=\"media video-js vjs-default-skin\" width=\"250\" height=\"250\" poster=\"{$poster}\"
								   data-setup='{\"controls\":true, \"preload\": \"auto\"}'>
									 <source src=\"{$source}\" type=\"video/mp4\" />
								   </video>";
						} else {
							// image
							$image = $media->images->low_resolution->url;
							$content .= "<img class=\"media\" src=\"{$image}\"/>";
						}
						// create meta section
						$avatar = $media->user->profile_picture;
						$username = $media->user->username;
						$comment = $media->caption->text;
						$id = $media->id;
						// output media
						echo $content . '</li>';
						foreach ($media->tags as $tags) {
							$tagmedia = $instagram->getTagMedia($tag);
							echo "<pre>";
							print_r($tagmedia);
							exit();
						}
					}
				}
			?>
		</ul>
	</div>
</div>
</body>
</html>
