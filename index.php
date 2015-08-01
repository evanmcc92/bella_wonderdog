<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
	require_once 'header.php';

	$selectPost = "SELECT a.IG_PostID, b.Created_at, b.CommentCount, b.LikeCount, a.ImageLowResolution, a.VideoStandardResolution, a.Type
		FROM `Posts` as a, PostData as b 
		WHERE a.IG_PostID = b.IG_PostID 
		ORDER BY `a`.`Created_at` DESC
	";
	$postResult = mysqli_query($con, $selectPost) or die("Error gettings post data: ".mysqli_error($con)."<br>$selectPost");
	while($row = mysqli_fetch_array($postResult)){
		$post[$row['IG_PostID']] = array(
			"ID" => $row['IG_PostID'],
			"Created_at" => $row['Created_at'],
			"CommentCount" => $row['CommentCount'],
			"LikeCount" => $row['LikeCount'],
			"ImageLowResolution" => $row['ImageLowResolution'],
			"VideoStandardResolution" => $row['VideoStandardResolution'],
			"Type" => $row['Type']
		);
	}
	function sortByLike($a, $b) {
	    return $b['LikeCount'] - $a['LikeCount'];
	}
	function sortByComment($a, $b) {
	    return $b['CommentCount'] - $a['CommentCount'];
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>@<?php echo $user[0]['Username'];?> Statistics</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="https://vjs.zencdn.net/4.2/video-js.css" rel="stylesheet">
	<link href="assets/style.css" rel="stylesheet">
	<link href="assets/main.css" rel="stylesheet">
	<script src="https://vjs.zencdn.net/4.2/video.js"></script>
</head>
<body>
	<div class="container">
		<header class="clearfix">
			<span id="userimage"><img src="<?php echo $user[0]['ProfilePictureURL'];?>"></span>
			<h1><a href="index.php">@<?php echo $user[0]['Username'];?></a></h1>
			<nav>
				<ul>
					<li><a href="allPosts.php">All Posts</a></li>
					<li><a href="user.php">User Data</a></li>
					<li><a href="fans.php">Biggest Fans</a></li>
					<li><a href="tags.php">Most Popular Tags Used</a></li>
				</ul>
			</nav>
			<article id="userdata" style="text-align:center">
				<table>
					<tr>
						<th>Followers</th>
						<td><?php echo $user[0]['NoFollowers'];?></td>
						<td>Follower Change <span class="<?php echo $followerclass;?>"><?php echo $followerchange;?></span> since <?php echo date("m/d/Y H:i:s" ,strtotime($user[1]['Created_at']));?></td>
					</tr>
					<tr>
						<th>Following</th>
						<td><?php echo $user[0]['NoFollowing'];?></td>
						<td>Follow Change <span class="<?php echo $followingclass;?>"><?php echo $followingchange;?></span> since <?php echo date("m/d/Y H:i:s" ,strtotime($user[1]['Created_at']));?></td>
					</tr>
					<tr>
						<th>Number of Posts</th>
						<td><?php echo $user[0]['NoPosts'];?></td>
					</tr>
					<tr>
						<th>Last Updated</th>
						<td><?php echo date("m/d/Y H:i:s", strtotime($user[0]['Created_at']));?></td>
					</tr>
					<tr>
						<!-- <td colspan="2" style="text-align:center"><form action="updateData.php" method="post"><input type="submit" name="submit" value="Get Fresh Data"></form></td> -->
						<td colspan="2" style="text-align:center"><form action="updateData.php" method="post"><input type="submit" name="submit" value="Get Fresh Data"></form></td>
					</tr>
				</table>
			</article>
		</header>
		<article id="postdata">
			<section id="likes">
				<h1>Top 5 Posts with the most Likes</h1>
				<div class="main">
					<ul class="grid">
						<?php
							usort($post, 'sortByLike');
							$i = 0;
							foreach ($post as $id => $value) {
								$content = "<li>";
								if ($value['Type'] === 'video') {
									$poster = $value['ImageLowResolution'];
									$source = $value['VideoStandardResolution'];
									$content .= "
									<video class=\"media video-js vjs-default-skin\" width=\"250\" height=\"250\" poster=\"{$poster}\" data-setup='{\"controls\":true, \"preload\": \"auto\"}'>
										<source src=\"{$source}\" type=\"video/mp4\" />
									</video>";
								} else {
									$image = $value['ImageLowResolution'];
									$content .= "<img class=\"media\" src=\"{$image}\"/>";
								}
								// create meta section
								$avatar = $user[0]['ProfilePictureURL'];
								$username = $user[0]['Username'];
								$likecount = $value['LikeCount'];
								$content .= "
								<div class=\"content\">
									<div class='comments'><p>{$likecount} Likes</p></div>
									<div class='comments'><p><a href='post.php?id=".$value['ID']."'>See Full Image Here</a></p></div>
								</div>";
								echo "$content</li>";
								$i++;
								if ($i == 5) {
									break;
								}
							}
						?>
					</ul>
				</div>
			</section>
			<section id="comments">
				<h1>Top 5 Posts with the most Comments</h1>
				<div class="main">
					<ul class="grid">
						<?php
							usort($post, 'sortByComment');
							$i = 0;
							foreach ($post as $id => $value) {
								$content = "<li>";
								if ($value['Type'] === 'video') {
									$poster = $value['ImageLowResolution'];
									$source = $value['VideoStandardResolution'];
									$content .= "
									<video class=\"media video-js vjs-default-skin\" width=\"250\" height=\"250\" poster=\"{$poster}\" data-setup='{\"controls\":true, \"preload\": \"auto\"}'>
										<source src=\"{$source}\" type=\"video/mp4\" />
									</video>";
								} else {
									$image = $value['ImageLowResolution'];
									$content .= "<img class=\"media\" src=\"{$image}\"/>";
								}
								// create meta section
								$avatar = $user[0]['ProfilePictureURL'];
								$username = $user[0]['Username'];
								$commentcount = $value['CommentCount'];
								$content .= "
								<div class=\"content\">
									<div class='comments'><p>{$commentcount} Comments</p></div>
									<div class='comments'><p><a href='post.php?id=$id'>See Full Post Here</a></p></div>
								</div>";
								echo "$content</li>";
								$i++;
								if ($i == 5) {
									break;
								}
							}
						?>
					</ul>
				</div>
			</section>
		</article>
	</div>

<!-- javascript -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script>
	if ($('.good').length) {
		$('.good').prepend("\+");
	};
	$(document).ready(function () {
		// rollover effect
		$('li').hover(
			function () {
				var $image = $(this).find('.media');
				var height = $image.height();
				$image.stop().animate({marginTop: -(height - 82)}, 1000);
			}, function () {
				var $image = $(this).find('.media');
				var height = $image.height();
				$image.stop().animate({marginTop: '0px'}, 1000);
			}
		);
	});
</script>
</body>
</html>
<?php
	mysqli_close($con);
?>
