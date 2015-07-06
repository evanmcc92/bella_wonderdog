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
	'apiCallback' => 'https://pacific-journey-4584.herokuapp.com/edit.php' // must point to success.php
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
	$result = $instagram->getUserMedia();
	// now you have access to all authenticated user methods
} else {
	// check whether an error occurred
	if (isset($_SESSION['error'])) {
		die('An error occurred: ' . $_SESSION['error_description']);
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
		<a href="index.php">
			<img src="assets/instagram.png" alt="Instagram logo">

			<h1>Instagram photos <span>taken by <?php echo $data->user->username ?></span></h1>
		</a>
	</header>
	<div class="main">
		<ul class="grid">
			<?php
				foreach ($result->data as $media) {
					$id = $media->id;
					if ($id == $_GET['id']) {
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
						// output media
						echo $content . '</li></ul>';
						echo "<ol>";
						foreach ($media->tags as $tag) {
							echo "<li>$tag<ul class=\"grid\">";
							$tagnewmedia = $instagram->getTagMedia($tag);
							$x = 0;
							foreach ($tagnewmedia->data as $tagmedia) {
								$x++;
								if ($x == 25) {
									break;
								} else {
									$mediaid = $tagmedia->id;
									// $instagram->likeMedia($mediaid);
									$content = '<li class="li" id="'.$mediaid.'">';
									// output media
									if ($tagmedia->type === 'video') {
										// video
										$poster = $tagmedia->images->low_resolution->url;
										$source = $tagmedia->videos->standard_resolution->url;
										$content .= "<video class=\"media video-js vjs-default-skin\" width=\"250\" height=\"250\" poster=\"{$poster}\"
												data-setup='{\"controls\":true, \"preload\": \"auto\"}'>
													<source src=\"{$source}\" type=\"video/mp4\" />
												</video>";
									} else {
										// image
										$image = $tagmedia->images->low_resolution->url;
										$content .= "<img class=\"media\" src=\"{$image}\"/>";
									}
									// create meta section
									$avatar = $tagmedia->user->profile_picture;
									$username = $tagmedia->user->username;
									$comment = $tagmedia->caption->text;
									$instagramlink = $tagmedia->link;
									$content .= "<div class=\"content\">
												<div class=\"avatar\" style=\"background-image: url({$avatar})\"></div>
												<p>{$username}</p>
												<div class=\"comment\">{$comment}</div>
												<div><a href='$instagramlink' target='_blank'>Instagram Link</a></div>
											</div>";
									// output media
									echo $content . '</li>';
									// sleep(rand(15,30));
									break;
								}
							}
							// sleep(10);
							echo "</ul></li>";
							break;
						}
						echo "</ol>";
					}
				}
			?>
	</div>
</div>
<!-- javascript -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script>
	$(document).ready(function () {
		// rollover effect
		$('.li').hover(
			function () {
				var $media = $(this).find('.media');
				var height = $media.height();
				$media.stop().animate({marginTop: -(height - 82)}, 1000);
			}, function () {
				var $media = $(this).find('.media');
				$media.stop().animate({marginTop: '0px'}, 1000);
			}
		);
	});
</script>
</body>
</html>
