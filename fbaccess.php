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
// Facebook, but we don’t know if the access token is valid. An access
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
	
//========= Batch requests over the Facebook Graph API using the PHP-SDK ========
	// Save your method calls into an array
	$queries = array(
		array('method' => 'GET', 'relative_url' => '/'.$user),
		array('method' => 'GET', 'relative_url' => '/'.$user.'/home?limit=50'),
		array('method' => 'GET', 'relative_url' => '/'.$user.'/friends'),
		array('method' => 'GET', 'relative_url' => '/'.$user.'/photos?limit=6'),
		);

	// POST your queries to the batch endpoint on the graph.
	try{
		$batchResponse = $facebook->api('?batch='.json_encode($queries), 'POST');
	}catch(Exception $o){
		error_log($o);
	}

	//Return values are indexed in order of the original array, content is in ['body'] as a JSON
	//string. Decode for use as a PHP array.
	$user_info		= json_decode($batchResponse[0]['body'], TRUE);
	$feed			= json_decode($batchResponse[1]['body'], TRUE);
	$friends_list	= json_decode($batchResponse[2]['body'], TRUE);
	$photos			= json_decode($batchResponse[3]['body'], TRUE);
//========= Batch requests over the Facebook Graph API using the PHP-SDK ends =====

	// Update user's status using graph api
	if(isset($_POST['pub'])){
		try{
			$statusUpdate = $facebook->api("/$user/feed", 'post', array(
				'message'		=> 'Check out 25 labs',
				'link'			=> 'http://25labs.com',
				'picture'		=> 'http://25labs.com/images/25-labs-160-160.jpg',
				'name'			=> '25 labs | A Technology Laboratory',
				'caption'		=> '25labs.com',
				'description'	=> '25 labs is a Technology blog that covers the tech stuffs happening around the globe. 25 labs publishes various tutorials and articles on web designing, Facebook API, Google API etc.',
				));
		}catch(FacebookApiException $e){
			error_log($e);
		}
	}

	////// Rahul Code .....
	
	$token= $facebook->getAccessToken();
    $facebook->setAccessToken($token);

    $albums = $facebook->api('/me/albums?fields=id'); 
	$pictures = array();

    foreach ($albums['data'] as $album) {
      $pics = $facebook->api('/'.$album['id'].'/photos');
      $pictures[$album['id']] = $pics['data']; // This gives the set of all photos in an album ....
		
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
  
    $imagecounter=1;
	$albumcounter=1;
  
	// Downloading Album Images ..
  
  
	 $pictureDataTree = new DOMDocument('1.0', 'UTF-8');
	 $pictureXML=$pictureDataTree->createElement("xml");
 	 $pictureXML=$pictureDataTree->appendChild($pictureXML);
  
	 $mainElement=$pictureDataTree->createElement("ImageList");
	 $mainElement=$pictureXML->appendChild($mainElement);
	 
	 
	 
	// 			    $fid = $rootXml->appendChild($fid);
	// 				
	// 				$name=$fndetails['name'];
	// 				
	// 				//echo $name;
	// 				$fid->appendChild($domTree->createElement('uID',$friendId));
	// 				$fid->appendChild($domTree->createElement('Name',$name));
	// 			
  
    //display the pictures url
    foreach ($pictures as $album) {    // from each pictures data structure ,extract album
      
	
	$albumXML=$pictureDataTree->createElement("Album");
	$albumXML=$mainElement->appendChild($albumXML);
	
	$names=array();
		
	  //Inside each album
      foreach ($album as $image) {  // take the image from each ....
      
  		$tags=0;
	  
	  	$imageTag= $pictureDataTree->createElement("Image"); 
	  	$imageTag=$albumXML->appendChild($imageTag);
	  
		//Get the URL and  Save the image ...
      $output = $image['source'];
  	  $string="$user/{$imagecounter}.jpg";
  	  save_image($output,$string);
  	  
	 
	  $imagecounter++;
	  
	  //// GETTING THE likes ...
      $likes=0; 
	  $image_id=$image['id'];
	  $likes_data = $facebook->api('/'.$image_id.'/likes?limit=10000000');
	  
	  
	   
	  
	  foreach($likes_data['data'] as $actuallikedata)
	  {
		  $likes++;
		  
	  }
	  
	  echo "<br />";
//	  echo " <br / > $likes";
	  echo "Number of Likes : $likes \n";
	  
	  $imageTag->appendChild($pictureDataTree->createElement('Likes',$likes));
	  
	  ///// Getting the Comments ....
	  
	  //var_dump($image['tags']);
	  
  	  //echo $output;
  	  // 
  	  
  	  // Storing the number of tags for that image ....
	  

	  
	  foreach($image['tags']['data'] as $taggedImage)
	  {

		  $names[$tags]=$taggedImage['name'];
		  $tags++;	
		 
		  //echo "<br /> $names[$tags]";	
		  echo $taggedImage['name'];echo "\n\n\n";
		  echo "<br /";
		 
	  }
	  
	  echo "Number of Tags : $tags \n";
	   
	  $imageTag->appendChild($pictureDataTree->createElement('Tags',$tags));

	  $numcomments=0;
	  
	  foreach($image['comments']['data'] as $comments)
	  {
		  $numcomments++;
		 
	  }
	  
	  echo "Number of Comments :$numcomments \n";
	  $imageTag->appendChild($pictureDataTree->createElement('Comments',$numcomments));
	  
	  echo "Time of Creation :";echo $image['created_time'];echo "\n\n";
	  
	  $imageTag->appendChild($pictureDataTree->createElement('DateandTime',$image['created_time']));
	  
	  if(strlen($image['name']))
      $imageTag->appendChild($pictureDataTree->createElement('Description',$image['name']));
	  
	  // echo "Album id";
	
      }
	  
	  $albumcounter++;
	
  	}
	
	echo " Album Counter $albumcounter ";
	
	// Saving the pictures.xml ----- OLD one ..
	
	// $path=getcwd()."/$user/pictures.xml";
	// 
	// $pictureDataTree->save("$path");
	
	
	
	///// Downloading Friends Profile Pictures ..
	
	$friends_details=$facebook->api('me/friends?fields=name,picture.width(800).height(800)');
	
	$friends_names=array();
    $friendcounter=1;

	mkdir($user.'/friends',0700);
	
  // echo	getcwd();
   
   /* create a dom document with encoding utf8 */
      $domTree = new DOMDocument('1.0', 'UTF-8');

      /* create the root element of the xml tree */
      $rootXml = $domTree->createElement("xml");
      /* append it to the document created */
      $rootXml = $domTree->appendChild($rootXml);
	   
   
	foreach ($friends_details['data'] as $fndetails)
			{
				// $friends_names[$friendcounter]=$fndetails['name'];
// 				//echo $fndetails['name'];
// 				
// 				$friendpic=$fndetails['picture']['data']['url'];
// 				;//echo $friendpic;
// 				$path=getcwd()."/$user/friends/{$friendcounter}.jpg";
// 				save_image($friendpic,$path);
// 				
// 				// Get Friends Id ..
// 				
// 				$friendId=$fndetails['id'];
// 				
// 				// XML stuff ..
// 			
// 			    $fid = $domTree->createElement("Friend{$friendcounter}");
// 			    $fid = $rootXml->appendChild($fid);
// 				
// 				$name=$fndetails['name'];
// 				
// 				//echo $name;
// 				$fid->appendChild($domTree->createElement('uID',$friendId));
// 				$fid->appendChild($domTree->createElement('Name',$name));
// 			
// 				//echo $path;
// 				
// 				$friendcounter++;
			}
			
	/// Saving my Xml file ..
	
	
		
	// Downloading my Tagged Images ........................................................
	
	$tagged_photos=$facebook->api('me/photos');
	
	mkdir($user.'/tagged_photos',0700);
	
	// get the pic and its tags ..
	
	
	// Create the DOM file of our tagged Pics xml file ..
	
    /* create a dom document with encoding utf8 */
       $domtree = new DOMDocument('1.0', 'UTF-8');

       /* create the root element of the xml tree */
       $xmlRoot = $domtree->createElement("xml");
       /* append it to the document created */
       $xmlRoot = $domtree->appendChild($xmlRoot);
	   
	//var_dump($tagged_photos['data']);
	// 
	// 
	// 
	// 
	// 
	// 
	// 
	// 
	// 
	// 
	// 
	// 
	//--------------
			
	$tags=$facebook->api('me/photos?fields=tags');
	//var_dump($tags);
	
	
	$tagcounter=1;
	
	$albumTagXML=$pictureDataTree->createElement("Album");
	$albumTagXML=$mainElement->appendChild($albumTagXML);
	
	
	foreach ($tagged_photos['data'] as $taggedpics)
		{
			
		  	$imageTag= $pictureDataTree->createElement("Image"); 
		  	$imageTag=$albumTagXML->appendChild($imageTag);
			
			$friend_id=$taggedpics['id'];
		 	$pic_array=$taggedpics['images'];
			//var_dump($tagged_photos['data']);
			
	  	  
	  	  //// GETTING THE likes ...
	        $likes=0; 
	  	
	  	  $likes_data = $facebook->api('/'.$friend_id.'/likes?limit=10000000');
	  
	  	  foreach($likes_data['data'] as $actuallikedata)
	  	  {
	  		  $likes++;
		  
	  	  }
	  
	  	  echo "<br />";
	  //	  echo " <br / > $likes";
	  	  echo "Number of Likes : $likes \n";
	  
	  	  $imageTag->appendChild($pictureDataTree->createElement('Likes',$likes));
			
			$key=0;
			$firstPic=$pic_array[$key];
			
			$pic=$firstPic['source'];
		
			///var_dump($firstPic);
			//echo $firstPic;
			$path="$user/{$imagecounter}.jpg";
			save_image($pic,$path);
  	    	$imagecounter++;
	  
			
			$tags=0;
			
	
			// // XML stuff ..
	// 	    $pic = $domtree->createElement("Pic{$tagcounter}");
	// 	    $pic = $xmlRoot->appendChild($pic);
			
			// Save the Tagged ids into the xml file ...
			
			///var_dump($taggedpics['tags']);
			//echo($taggedpics['link']);
			
			foreach ($taggedpics['tags']['data'] as $taggedfriends)
			{
				
			//$pic->appendChild($domtree->createElement('id',$taggedfriends['id']));
			//echo $taggedfriends['id'];
			$tags++;
			
		    }	
			
			$imageTag->appendChild($pictureDataTree->createElement('Tags',$tags));
			$imageTag->appendChild($pictureDataTree->createElement('Description',"Tagged Pictures"));
			
	  	  $numcomments=0;
	  
	  	  foreach($taggedpics['comments']['data'] as $comments)
	  	  {
	  		  $numcomments++;
		 
	  	  }
	  
	  	  echo "Number of Comments :$numcomments \n";
	  	  $imageTag->appendChild($pictureDataTree->createElement('Comments',$numcomments));
		  			$imageTag->appendChild($pictureDataTree->createElement('DateandTime',$taggedpics['created_time']));	
		
		
		$tagcounter++; // Think its useless ..
		
		}
		
		
		$path=getcwd()."/$user/pictures.xml";
	
		$pictureDataTree->save("$path");
	
		
		
		
	
	// store a file with Pic and User id Tags ...
	
	// See the Tags -> id of the users ...and connect them with the images ...
	
	// U have to create another file with friends uID and profile images ..
	
	$path=getcwd()."/$user/tags.xml";
	
	$domtree->save("$path");

	// Update user's status using graph api
	if(isset($_POST['status'])){
		try{
			$statusUpdate = $facebook->api("/$user/feed", 'post', array('message'=> $_POST['status']));
		}catch(FacebookApiException $e){
			error_log($e);
		}
	}
}
?>