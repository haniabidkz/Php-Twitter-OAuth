<?php
	session_start();
	include('config.php');
	require "../autoload.php";
	use Abraham\TwitterOAuth\TwitterOAuth;

	//If Session Token and Oauth Token Not Same It Means Session Oauth Token Expired.
	if(isset($_GET['oauth_token']) AND $_SESSION['oauth_token']!==$_GET['oauth_token']){
		
		//So We Need to Destry Session
		session_destroy();

		//Unsetting Session Variables
		unset($_SESSION);

		//Redirecting so user can sign in again.
		header('location:index.php');
	}
	
	//if Session and Recived Oauth Token are same it means verified, Now we need to get access token
	elseif(isset($_GET['oauth_token']) AND $_SESSION['oauth_token']==$_GET['oauth_token']){
		
		//Passing Oauth Token which will be converted in Access Token..
		$con=new TwitterOAuth(CONSUMER_API_KEY,CONSUMER_API_SECRET,$_SESSION['oauth_token'],$_SESSION['oauth_token_secret']);

		//Getting Oauth Verfier
		$oauth_verifier=$_GET['oauth_verifier'];
		
		//Getting an array which will contain access token, secret , user id and screen name
		$access_token=$con->oauth('oauth/access_token',['oauth_verifier'=>$oauth_verifier]);

		//Now Unset Oauth Token since we have got access token.
		unset($_SESSION['oauth_token']);

		//Now Unset Oauth Token Secret since we have got access token Secret.
		unset($_SESSION['oauth_token_secret']);

		//Stroing the resultant access token  in session, Now this will be like a email in normal sign in code.
		$_SESSION['access_token']=$access_token;

		//Now Passing control to index.php, There third party application can do anythin on the behalf of user !
		header('location:index.php');
	}

	//For Logout
	elseif(isset($_GET['logout'])){

		session_destroy();
		unset($_SESSION);
		header('location:index.php');
	}

	else{
		$con=new TwitterOAuth(CONSUMER_API_KEY,CONSUMER_API_SECRET);

		// Getting Oauth Token and Secret
		$oauth=$con->oauth('oauth/request_token',['oauth_callback'=>CALL_BACK]);

		//Getting Oauth Token
		$oauth_token=$oauth['oauth_token'];

		//Getting Oauth Token Secret
		$oauth_token_secret=$oauth['oauth_token_secret'];

		//Storing Oauth Token in Session Because They Will Be Different for Each user and we need them in many requests
		$_SESSION['oauth_token']=$oauth_token;

		//Storing Oauth Secret in Session
		$_SESSION['oauth_token_secret']=$oauth_token_secret;

		//Getting Link with the oauth token to redirect user to Twiter for Login and Permission
		$oauth_url=$con->url('oauth/authenticate',['oauth_token'=>$oauth_token]);

		//Redirectng user
		header('location:'.$oauth_url);

		//Make Sure Script Die
		die();
	}

?>