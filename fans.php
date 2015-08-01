<?php
	require_once 'header.php';

	$selectPost = "SELECT UserID, UserName, FullName, COUNT(ID) as `Count` FROM `LikeUsers` GROUP BY UserID ORDER BY `Count` DESC LIMIT 100";
	$postResult = mysqli_query($con, $selectPost) or die("Error gettings post data: ".mysqli_error($con)."<br>$selectPost");
	while($row = mysqli_fetch_array($postResult)){
		$fans[] = $row;
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
			<h2>Top 100 Biggest Fans</h2>
			<table class="specificData">
			<tr>
				<th>Username</th>
				<th>Full Name</th>
				<th>Number of Times Used</th>
			</tr>
			<?php
				foreach ($fans as $key => $value) {
					echo "
					<tr id='".$value['UserID']."'>
						<td><a href='https://instagram.com/".$value['UserName']."' target='_blank'>@".$value['UserName']."</a></td>
						<td>".urldecode($value['FullName'])."</td>
						<td>".$value['Count']."</td>
					</tr>";
				}
			?>
			</table>

		</article>
	</div>
	<script type="text/javascript">
		if ($('.good').length) {
			$('.good').prepend("\+");
		};
	</script>
</body>
</html>