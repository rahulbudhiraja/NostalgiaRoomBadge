<?php
//Application Configurations
$app_id		= "534257256586307";
$app_secret	= "ce9efd859fe0ae3b2f4c9537d23a3422";
$site_url	= "http://localhost:8888/25labs/index.php";

try{
	include_once "src/facebook.php";
}catch(Exception $e){
	error_log($e);
}
// Create our application instance
$facebook = new Facebook(array(
	'appId'		=> $app_id,
	'secret'	=> $app_secret,
	));

// Get User ID
$user = $facebook->getUser();
// We may or may not have this data based 
// on whether the user is logged in.
// If we have a $user id here, it means we know 
// the user is logged into
// Facebook, but we donít know if the access token is valid. An access
// token is invalid if the user logged out of Facebook.


if($user){
//==================== Single query method ======================================
	try{
		// Proceed knowing you have a logged in user who's authenticated.
		$user_profile = $facebook->api('/me');
	}catch(FacebookApiException $e){
		error_log($e);
		$user = NULL;
	}
//==================== Single query method ends =================================
}

if($user){
	// Get logout URL
	$logoutUrl = $facebook->getLogoutUrl();
}else{
	// Get login URL
	$loginUrl = $facebook->getLoginUrl(array(
		'scope'			=> 'read_stream, publish_stream, user_birthday, user_location, user_work_history, user_hometown, user_photos,friends_photos,user_about_me,user_videos,friends_actions.video,friends_online_presence,friends_photo_video_tags,friends_videos,user_actions.video,user_photo_video_tags',
		'redirect_uri'	=> $site_url,
		));
}

if($user){
	// Proceed knowing you have a logged in user who has a valid session.
	$token= $facebook->getAccessToken();
    $facebook->setAccessToken($token);
	
    $albums = $facebook->api('/me/albums?fields=id'); 

    $pictures = array();
  
    foreach ($albums['data'] as $album) {
      $pics = $facebook->api('/'.$album['id'].'/photos?fields=source,picture');
      $pictures[$album['id']] = $pics['data'];
		
    }

    mkdir($user,0700);
    //Store in the filesystem.
    $fp = fopen("image.jpg", "w");
  
    function save_image($img,$fullpath){
        $ch = curl_init ($img);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
  	  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
  	  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	  
	  
        $rawdata=curl_exec($ch);
        curl_close ($ch);

        $fp = fopen($fullpath,'w');
        fwrite($fp, $rawdata);
        fclose($fp);
    }
  
  

	echo "Great Success !";

}