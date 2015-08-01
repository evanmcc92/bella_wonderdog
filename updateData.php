<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	require 'src/Instagram.php';
	use MetzWeb\Instagram\Instagram;

	$con = mysqli_connect('localhost', 'bella_wonderdog', 'emily1106', 'bella_wonderdog') or die("Cannot Connect to MySQL: ".mysqli_connect_error());
	if (isset($_POST['submit']) || isset($argv[1])) {
		$instagram = new Instagram('b398cceb326a496c971ff89709812fff');
		// $instagram = new Instagram(array(
		//     'apiKey'      => 'b398cceb326a496c971ff89709812fff',
		//     'apiSecret'   => '93a296de4bc04ebbbc00632227fb66e3',
		//     'apiCallback' => 'http://192.168.1.140/bella_wonderdog/'
		// ));
		$sqlUser = "SELECT IG_UserID FROM User";
		$select = mysqli_query($con, $sqlUser) or die("Error inserting user '$username': ".mysqli_error($con)."<br>$sqlUser");
		while ($row = mysqli_fetch_array($select)) {
			$user = $instagram->getUser($row['IG_UserID']);
			$id = $user->data->id;
			$username = $user->data->username;
			$bio = urlencode($user->data->bio);
			$profpic = $user->data->profile_picture;
			$fullname = $user->data->full_name;
			$posts = $user->data->counts->media;
			$followers = $user->data->counts->followed_by;
			$following = $user->data->counts->follows;

			$sql = "INSERT INTO `UserData`(`IG_UserID`, `NoFollowers`, `NoFollowing`, `NoPosts`, `Created_at`) VALUES ($id, $followers, $following, $posts, NOW())";
			mysqli_query($con, $sql) or die("Error inserting user '$username': ".mysqli_error($con)."<br>$sql");
			echo "$sql;<br>";

			$posts = $instagram->getUserMedia($id, 1000);
			foreach ($posts->data as $media) {
				$postid = $media->id;
				$TagCount = count($media->tags);
				if ($TagCount > 0) {
					$sqlTagCheck = "SELECT COUNT(ID) FROM Tags WHERE PostID = '$postid'";
					$tagcheck = mysqli_query($con, $sqlTagCheck) or die("Error checking for '$postid' tags: ".mysqli_error($con)."<br>$sqlTagCheck");
					$tagcheck = mysqli_fetch_array($tagcheck);
					$count = $tagcheck[0];
					if ($count == 0) {
						foreach ($media->tags as $tag) {
							$sqlTagInsert = "INSERT INTO `Tags`(`Tag`, `PostID`, `Created_at`) VALUES ('$tag', '$postid', NOW())";
							mysqli_query($con, $sqlTagInsert) or die("Error inserting tag '$tag': ".mysqli_error($con)."<br>$sqlTagInsert");
							// echo "$sqlTagInsert;<br>";
						}
					}
				}
				$CommentCount = $media->comments->count;
				$LikeCount = $media->likes->count;
				if ($LikeCount > 0) {
					$medialikes = $instagram->getMediaLikes($postid);
					foreach ($medialikes->data as $key => $value) {
						$sqlLikeCheck = "SELECT COUNT(ID) FROM LikeUsers WHERE PostID = '$postid' AND UserID = '".$value->id."'";
						$likecheck = mysqli_query($con, $sqlLikeCheck) or die("Error checking liked user '".$value->id."': ".mysqli_error($con)."<br>$sqlLikeCheck");
						$likecheck = mysqli_fetch_array($likecheck);
						$count = $likecheck[0];
						if ($count == 0) {
							$sqlInsertLikeUsers = "INSERT INTO `LikeUsers`(`PostID`, `UserID`, `UserName`, `FullName`, `Created_at`) VALUES ('$postid', '".$value->id."','".$value->username."','".urlencode($value->full_name)."', NOW())";
							mysqli_query($con, $sqlInsertLikeUsers) or die("Error inserting liked user '".$value->id."': ".mysqli_error($con)."<br>$sqlInsertLikeUsers");
							// echo "$sqlInsertLikeUsers;<br>";
						}
					}
				}
				$Link = $media->link;
				$ImageThumbLink = $media->images->thumbnail->url;
				$ImageStandardLink = $media->images->standard_resolution->url;
				$ImageLowResolution = $media->images->low_resolution->url;
				$VideoLowResolution = (isset($media->videos->low_resolution->url)) ? $media->videos->low_resolution->url : "";
				$VideoStandardResolution = (isset($media->videos->standard_resolution->url)) ? $media->videos->standard_resolution->url : "";
				$UsersInPhoto = count($media->users_in_photo);
				$CaptionText = urlencode($media->caption->text);
				$Filter = $media->filter;
				$Type = $media->type;
				$IG_Created_at = date("Y-m-d H:i:s", $media->created_time);
				$sqlPostInsert = "INSERT IGNORE INTO `Posts`(`IG_UserID`, `IG_PostID`, `Link`, `ImageThumbLink`, `ImageStandardLink`, `CaptionText`, `Filter`, `IG_Created_at`, `Type`, `ImageLowResolution`, `VideoLowResolution`, `VideoStandardResolution`, `Created_at`) VALUES ($id, '$postid', '$Link', '$ImageThumbLink', '$ImageStandardLink', '$CaptionText', '$Filter', '$IG_Created_at', '$Type', '$ImageLowResolution', '$VideoLowResolution', '$VideoStandardResolution', NOW())";
							// echo "$sqlPostInsert;<br>";
				mysqli_query($con, $sqlPostInsert) or die("Error inserting media '$postid': ".mysqli_error($con)."<br>$sqlPostInsert");
				$sqlPostInsert = "INSERT INTO `PostData`(`IG_UserID`, `IG_PostID`, `TagCount`, `CommentCount`, `LikeCount`, `Link`, `UsersInPhoto`, `IG_Created_at`, `Created_at`) VALUES ($id, '$postid', $TagCount, $CommentCount, $LikeCount, '$Link', $UsersInPhoto, '$IG_Created_at', NOW())";
				mysqli_query($con, $sqlPostInsert) or die("Error inserting media '$postid': ".mysqli_error($con)."<br>$sqlPostInsert");
							// echo "$sqlPostInsert;<br>";
			}
		}
	}
	mysqli_close($con);
	if (isset($_POST['submit'])) {
		$urlparse = parse_url($_SERVER['HTTP_REFERER']);
		print_r($urlparse);
		$location = (isset($urlparse['query'])) ? 'Location: '.$urlparse['path']."?".$urlparse['query'] : 'Location: '.$urlparse['path'];
		header($location);
	}

?>
