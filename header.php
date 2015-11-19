<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

$url = parse_url(getenv("CLEARDB_DATABASE_URL"));
print_r($url);

$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = substr($url["path"], 1);

$conn = new mysqli($server, $username, $password, $db);
	$con = mysqli_connect('localhost', 'bella_wonderdog', 'emily1106', 'bella_wonderdog') or die("Cannot Connect to MySQL: ".mysqli_connect_error());
	$userid = 1634434248;
	$selectUser = "SELECT a.IG_UserID, a.Username, a.Bio, a.ProfilePictureURL, a.FullName, b.NoFollowers, b.NoFollowing, b.NoPosts, b.Created_at FROM User as a, UserData as b WHERE a.IG_UserID = b.IG_UserID ORDER BY b.Created_at DESC LIMIT 2";
	$userResult = mysqli_query($con, $selectUser) or die("Error gettings user data: ".mysqli_error($con)."<br>$selectUser");
	while($row = mysqli_fetch_array($userResult)){
		$user[] = $row;
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
	$followerchange = (isset($user[1])) ? ($user[0]['NoFollowers'] - $user[1]['NoFollowers']) : 0;
	$followerclass = className($followerchange);
	$followingchange = (isset($user[1])) ? ($user[0]['NoFollowing'] - $user[1]['NoFollowing']) : 0;
	$followingclass = className($followingchange);
	$pagename = $_SERVER['PHP_SELF'];
?>
