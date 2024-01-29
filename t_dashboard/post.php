<?php
	require_once "C:/xampp/htdocs/ams/config.php";
    require_once "C:/xampp/htdocs/ams/t_dashboard/header.php";
	require "includes/Classes/User.class.php";
	
	if (session_status() == PHP_SESSION_NONE)
		session_start();
	$user=NULL;
	if (isset($_SESSION["current_user"]))
		$user = new User($_SESSION["current_user"]);

	try
	{
		$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$sql = "SELECT * FROM posts WHERE postID=?";
		$stmt = $conn->prepare($sql);
		$stmt->execute([$_GET['postID']]);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
	}
	catch(PDOException $e)
	{
		header("Location: index.php?error=PDOerror");
	}

	$redir = $_SERVER['REQUEST_URI'];
?>

<html>
	<head>
		<link rel="stylesheet" href="styles/w3.css">
		<link rel="stylesheet" href="styles/index.css">
		<link href="https://fonts.googleapis.com/css?family=Titillium+Web&display=swap" rel="stylesheet">
		<meta name="viewport" content="width=device-width, initial-scale=1">
	</head>
	<body>
		<div class="w3-container w3-main" style="margin-top:13vh;" id="main">
			<div class="w3-container w3-content" style="max-width:1200px;">

				<?php

					$image = $result['postImage'];
					$likes = strval($result['postLikes']);
					$id = $result['postID'];

					try
					{
						$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
						$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
						
						$comments = strval($conn->query("select count(*) from comments where postID=$id")->fetchColumn());

					}
					catch(PDOException $e)
					{
						header("Location: index.php?error=PDOerror");
					}

					$sql = "SELECT * FROM users WHERE userID = {$result['userID']};";
					$stmt = $conn->prepare($sql);
					$stmt->execute();
					$result2 = $stmt->fetch();
					$username = $result2['userLogin'];
				?>
				<div class="w3-card-4 w3-display-container" id="<?=$id?>" style="color: black;">
					<?php
						if (($user !== null) &&($user->userID == $result['userID'] || $user->userPermissions == 7))
							echo '<button class="w3-large w3-button-cam w3-hover-red w3-display-topright" type="submit" id="delete-btn" onclick="delPost(\''.$id.'\',\''.$redir.'\')" title="Delete Post"><i class="fa fa-trash-o w3-text-black"></i></button> ';
					?>
					<img src="<?=$image?>" style="width: 100%;" id="postImg">
					<div class="w3-container w3-center" id="detailsWrap">
						<div class="w3-col w3-container" style="width:10%">
							<p class="w3-text-black" style="float: left;" id="likes">
								<button class="w3-button-nopad" onclick="likePost('<?=$id?>', '<?=$redir?>')"type="submit" id="like-btn"> <i class="fa fa-thumbs-o-up w3-text-black"></i> </button>
								<?=$likes?>
							</p>
						</div>

						<div class="w3-col w3-container" style="width:10%">
							<p class="w3-text-black" style="float: left;"><i class="fa fa-comment w3-text-black"></i><?=$comments?></p>
						</div>

						<div class="w3-col w3-container w3-right" style="width:50%">
							<p class="w3-text-black" id="postAuth" style="float: right;">Posted by: <a href="profile.php?user=<?=$username?>"><?=$username?></a></p>
						</div>
					</div>
				</div>

				<?php
					if (isset($_SESSION["current_user"])){
						$pid = $_GET['postID'];
						echo"
						<div class='w3-container cam-dark-grey' id='comment-box' style='display:block;'>
							<form class='w3-container form-container' style='color:black;' name='update' id='comment-form' method='POST' action='includes/commentHandler.php?action=create'>
								<h3 class='w3-text-white'>Add A Comment:</h3>
								<textarea rows='4' placeholder='Type a comment' name='comment' id='new-comment' form='comment-form' required></textarea>
								<input type='hidden' name='postID' value='$pid'>
								<input type='hidden' name='redir' value='$redir'>
								<button type='submit' name='submit-comment' id='comment-btn' class='btn' value='COMMENT'>ADD COMMENT</button>
							</form>
						</div>";
					}
				?>

				<?php
					try
					{
						$servername = "127.0.0.1";
						$username = "root";
						$password = "";
						$dbname = "ams_db";
						$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
						$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

						$sql = "SELECT * FROM comments WHERE postID=?";
						$stmt = $conn->prepare($sql);
						$stmt->execute([$_GET['postID']]);
						$result3 = $stmt->fetchAll(PDO::FETCH_ASSOC);
					}
					catch(PDOException $e)
					{
						// header("Location:index.php?error=PDOerror");
						echo "Connection failed: " . $e->getMessage();
					}
				?>
				<?php foreach($result3 as $comment): ?>
				<?php
					$current_comment = $comment['comment'];
					$comment_id = $comment['commentID'];
					$uid = $comment['userID'];
					
					$sql = "SELECT * FROM users WHERE userID={$comment['userID']}";
					$stmt = $conn->prepare($sql);
					$stmt->execute();
					$userF = $stmt->fetch();
					$userID = $userF['userID'];
					$userName = $userF['userLogin'];
					$userImage = $userF['userImage'];
				?>
				<div class="w3-container w3-padding-16 w3-border-bottom w3-border-black w3-display-container cam-blue" id="<?=$comment_id?>">
					<div class="w3-row">
						<div class="w3-col m1 l1">
							<img src="<?php echo('data:image/png;base64,'.base64_encode($userImage)) ?>" alt="User Avatar" class="w3-circle w3-image" style="width:50px;height:50px;background-color:#c9d6df;">
							<div>
								<p style="margin-top:0px;margin-bottom:0px;margin-left:4px" class="w3-text-black"><?=$userName?></p>
							</div>
						</div>
					</div>
					<div class="w3-col w3-auto w3-medium" style="width:85%;margin-left:3vw">
						<p class="w3-text-black"><?=$current_comment?></p>
					</div>
					<?php
						if ($user->userID == $uid || $user->userPermissions == 7)
							echo '<button class="w3-button w3-large w3-hover-red w3-display-right cam-white" id="delete-btn" style="color:black;" onclick="delComment(\''.$comment_id.'\',\''.$redir.'\')" title="Delete Comment"><i class="fa fa-trash-o"></i></button>';
					?>
				</div>			

				<?php endforeach; ?>
			</div>
		</div>

		<br>
	
		<div class="w3-container w3-right-align cam-dark-grey w3-padding-4">
			<!-- <p>© 2019 Camagru</p> -->
		</div>

        <script type="text/javascript" src="includes/scripts/like.js"></script>

        <script type="text/javascript" src="includes/scripts/delete.js"></script>

	</body>
</html>