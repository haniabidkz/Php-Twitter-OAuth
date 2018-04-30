<?php
	session_start();
	include('config.php');
	require "../autoload.php";
	use Abraham\TwitterOAuth\TwitterOAuth;

	//If Session With Token is set that means user is logged in on twter and has authorized app.
	if(isset($_SESSION['access_token'])){

		$user_id=$_SESSION['access_token']['user_id'];
		$screen_name=$_SESSION['access_token']['screen_name'];
		
		//Logout Feature
		echo "<br><a href='process.php?logout'>Logout</a><br><br>";

		//Access Token From Session
		$access_token=$_SESSION['access_token']['oauth_token'];

		//Access Token Secret From Session
		$access_token_secret=$_SESSION['access_token']['oauth_token_secret'];
		
		// Passing the Access Token and Secret Now to Post/Get Resources on the behalf of user.
		$con=new TwitterOAuth(CONSUMER_API_KEY,CONSUMER_API_SECRET,$access_token,$access_token_secret);
		
		//1-Getting User Details:(Name, Followers, Following, Profile Pic, Banner)
		$info=$con->get('users/show',['screen_name'=>$screen_name]);

		//Endocding To Decode in Array
		$info=json_encode($info);

		//Decodeing To Array
		$info=json_decode($info,true);
		
		//Information i.e Name, Des Profile Image ETC
		$name=$info['name'];
		$country=$info['location'];
		$text=$info['description'];
		$profile_image_url=$info['profile_image_url'];
		
		//Displaying User Info
		echo "Welcome Mr: ".$name."<br><hr>";
		echo"<h1>Personal Info</h1>";
		echo "<img src=$profile_image_url width='100' height='100'></img><br><br>";
		echo "Full Name:".$name."<br>";
		echo "Description: ".$text."<br>";
		echo "Location: ".$country."<br><hr><br>";
		echo"<form action='' method='post'><textarea cols='20' rows='10' name='status'></textarea><br><input type='submit' value='Tweet'></form>";


		//2-Post Box for Posting a Info Above Then If Posted then Post a Tweet
		if(isset($_POST['status'])){

			$status=$_POST['status'];
			$status_post=$con->post('statuses/update',['status'=>$status]);

			if($con->getLastHttpCode()==200){
				echo "Tweet Has Been Posted !";
			}
			else{
				echo "Please Try Again";
			}

		}

		//3-User Tweets
		$tweets=$con->get("statuses/user_timeline", ["screen_name"=>$screen_name]);
		$tweets=json_encode($tweets);
		$tweets=json_decode($tweets,true);

		echo "<hr><h1>My Tweets</h1><br>";
		foreach ($tweets as $tweet) {

			echo "- ".$tweet['text']."<br><br>";
		}

		//4- User Followers List
		$followers=$con->get("followers/list", ["screen_name"=>$screen_name]);
		$followers=json_encode($followers);
		$followers=json_decode($followers,true);
		
		echo "<hr><h1>Followers</h2><br>";
		
		//Nested Loop For Retireving Followers List
		foreach ($followers as $follower) {
			if(is_array($follower)){
				foreach ($follower as $follow) {
					echo $follow['name']."<br><br>";
				}
			}
		}

	}
	else{
		echo "<a href='process.php'><img width='150' height='50' src='login.png'></a></img>";
	}

?>