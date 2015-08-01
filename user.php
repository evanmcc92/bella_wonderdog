<?php
	require_once 'header.php';
	$jsonOut = getJSONOutput($con, $userid);
	function getJSONOutput($con, $id){
		$selectUser = "SELECT * FROM UserData
			WHERE IG_UserID = $id
			ORDER BY Created_at DESC
		";
		$userResult = mysqli_query($con, $selectUser) or die("Error gettings user data: ".mysqli_error($con)."<br>$selectUser");
		$out['cols'] = array(
			array(
				"id"=>"a",
				"label"=>"Date",
				"type"=>"datetime",
			),
			array(
				"id"=>"b",
				"label"=>"Followers",
				"type"=>"number",
			),
			array(
				"id"=>"c",
				"label"=>"Following",
				"type"=>"number",
			)
		);
		$x = 0;
		while($row = mysqli_fetch_array($userResult)){
			$year = date("Y", strtotime($row['Created_at']));
			$month = date("m", strtotime($row['Created_at'])) - 1;
			$day = date("d", strtotime($row['Created_at']));
			$hour = date("H", strtotime($row['Created_at']));
			$minutes = date("i", strtotime($row['Created_at']));
			$seconds = date("s", strtotime($row['Created_at']));
			
			$user = array();
			$user[] = array("v"=>"Date($year, $month, $day, $hour, $minutes, $seconds)");
			$user[] = array("v"=>intval($row['NoFollowers']));
			$user[] = array("v"=>floatval($row['NoFollowing']));
			$rows[$x] = array("c"=>$user);
			$x++;
		}
		$out['rows'] = $rows;
		return json_encode($out);
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

	<!--Load the AJAX API-->
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script type="text/javascript">
		// Load the Visualization API and the piechart package.
		google.load('visualization', '1', {'packages':['corechart']});

		// Set a callback to run when the Google Visualization API is loaded.
		google.setOnLoadCallback(drawChart);

		function drawChart() {
			var options = {
				title: 'Change Over Time',
				width: 1000,
				height: 600
			};
			// Create our data table out of JSON data loaded from server.
			var data = new google.visualization.DataTable(<?php echo $jsonOut; ?>);

			// Instantiate and draw our chart, passing in some options.
			var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
			chart.draw(data, options);
		}

	</script>



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
						<!-- <td colspan="2" style="text-align:center"><form action="updateData.php" method="post"><input type="submit" name="submit" value="Get Fresh Data"></form></td> -->
					</tr>
				</table>
			</article>
		</header>
		<article id="postdata">
			<table class="specificData">
				<colgroup>
					<col style="width:20%">
					<col style="width:80%">
				</colgroup>
				<tr>
					<th>Username</th>
					<td><?php echo $user[0]['Username']; ?></td>
				</tr>
				<tr>
					<th>Name</th>
					<td><?php echo $user[0]['FullName']; ?></td>
				</tr>
				<tr>
					<th>Bio</th>
					<td><?php echo urldecode($user[0]['Bio']); ?></td>
				</tr>
			</table>
			<!--Div that will hold the pie chart-->
			<div id="chart_div"></div>

		</article>
	</div>
	<script type="text/javascript">
		if ($('.good').length) {
			$('.good').prepend("\+");
		};
	</script>
</body>
</html>