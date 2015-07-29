<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
	require 'src/Instagram.php';
	use MetzWeb\Instagram\Instagram;
	$con = mysqli_connect('localhost', 'root', 'evan6992', 'bella_wonderdog') or die("Cannot Connect to MySQL: ".mysqli_connect_error());
	if (isset($_POST['submit'])) {
		$instagram = new Instagram('b398cceb326a496c971ff89709812fff');
		// $instagram = new Instagram(array(
		//     'apiKey'      => 'b398cceb326a496c971ff89709812fff',
		//     'apiSecret'   => '93a296de4bc04ebbbc00632227fb66e3',
		//     'apiCallback' => 'http://192.168.1.140/bella_wonderdog/'
		// ));
		$bella_wonderdog = $instagram->getUser("1634434248");
		$id = $bella_wonderdog->data->id;
		$username = $bella_wonderdog->data->username;
		$bio = urlencode($bella_wonderdog->data->bio);
		$profpic = $bella_wonderdog->data->profile_picture;
		$fullname = $bella_wonderdog->data->full_name;
		$posts = $bella_wonderdog->data->counts->media;
		$followers = $bella_wonderdog->data->counts->followed_by;
		$following = $bella_wonderdog->data->counts->follows;

		$sql = "INSERT INTO `UserData`(`IG_UserID`, `Username`, `Bio`, `NoFollowers`, `NoFollowing`, `NoPosts`, `ProfilePictureURL`, `FullName`, `Created_at`) VALUES ($id, '$username','$bio', $followers, $following, $posts, '$profpic', '$fullname', NOW())";
		mysqli_query($con, $sql) or die("Error inserting user '$username': ".mysqli_error($con)."<br>$sql");

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
			$sqlPostInsert = "INSERT INTO `Posts`(`IG_UserID`, `IG_PostID`, `TagCount`, `CommentCount`, `LikeCount`, `Link`, `ImageThumbLink`, `ImageStandardLink`, `UsersInPhoto`, `CaptionText`, `Filter`, `IG_Created_at`, `Type`, `ImageLowResolution`, `VideoLowResolution`, `VideoStandardResolution`, `Created_at`) VALUES ($id, '$postid', $TagCount, $CommentCount, $LikeCount, '$Link', '$ImageThumbLink', '$ImageStandardLink', $UsersInPhoto, '$CaptionText', '$Filter', '$IG_Created_at', '$Type', '$ImageLowResolution', '$VideoLowResolution', '$VideoStandardResolution', NOW())";
			mysqli_query($con, $sqlPostInsert) or die("Error inserting media '$postid': ".mysqli_error($con)."<br>$sqlPostInsert");
		}
	}
	header('Location: index.php');
	mysqli_close($con);

?>