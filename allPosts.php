<?php
	$con = mysqli_connect('localhost', 'root', 'evan6992', 'bella_wonderdog') or die("Cannot Connect to MySQL: ".mysqli_connect_error());
	$selectUser = "SELECT * FROM UserData ORDER BY Created_at DESC LIMIT 2";
	$userResult = mysqli_query($con, $selectUser) or die("Error gettings user data: ".mysqli_error($con)."<br>$selectUser");
	while($row = mysqli_fetch_array($userResult)){
		$user[] = $row;
	}

	$selectPost = "SELECT * FROM `Posts` WHERE Created_at in (SELECT MAX(Created_at) from Posts GROUP BY IG_PostID);";
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
	$followerchange = (isset($user[1])) ? ($user[0]['NoFollowers'] - $user[1]['NoFollowers']) : 0;
	$followerclass = className($followerchange);
	$followingchange = (isset($user[1])) ? ($user[0]['NoFollowing'] - $user[1]['NoFollowing']) : 0;
	$followingclass = className($followingchange);
	function sortByID($a, $b) {
	    return $b['ID'] - $a['ID'];
	}
	function className($value) {
		if ($value > 0) {
			$return = "good";
		} elseif ($value < 0) {
			$return = "bad";
		} else {
			$return = "";
		}
		return $return;
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
	<script src="https://vjs.zencdn.net/4.2/video.js"></script>
	<link href="assets/main.css" rel="stylesheet">
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
			<article id="userdata">
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
						<td colspan="2" style="text-align:center"><form action="updateData.php" method="post"><input type="submit" name="submit" value="Get Fresh Data"></form></td>
					</tr>
				</table>
			</article>
		</header>
		<article id="postdata">
			<section id="likes">
				<h1>All Posts</h1>
				<div class="main">
					<ul class="grid">
						<?php
							usort($post, 'sortByID');
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
								$commentcount = $value['CommentCount'];
								$content .= "
								<div class=\"content\">
									<div class='comments'><p>{$likecount} Likes</p></div>
									<div class='comments'><p>{$commentcount} Comments</p></div>
									<div class='comments'><p><a href='post.php?id=".$value['ID']."'>See Full Image Here</a></p></div>
								</div>";
								echo "$content</li>";
								$i++;
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