<?php
    include_once "fbaccess.php";
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <title>Facebook Connect Demo | 25 labs</title>    
		<link rel="stylesheet" href="style.css" type="text/css" media="screen" />
    </head>
<body>
<?php
function time_elapsed($time) {
	sscanf($time,"%u-%u-%uT%u:%u:%u+0000",$year,$month,$day,$hour,$min,$sec);
    $time_seconds = time() - ((int)substr(date('O'),0,3)*60*60) - mktime($hour,$min,$sec,$month,$day,$year);
    
    if($time_seconds < 1) return '0 seconds';
    
    $arr = array(12*30*24*60*60	=> 'year',
                30*24*60*60		=> 'month',
                24*60*60		=> 'day',
                60*60			=> 'hour',
                60				=> 'minute',
                1				=> 'second'
                );
    
    foreach($arr as $secs => $str){
        $d = $time_seconds / $secs;
        if($d >= 1){
            $r = floor($d);
            return $r . ' ' . $str . ($r > 1 ? 's' : '');
        }
    }
}


function newsitem($profile_pic,$from,$to,$message,$picture,$name,$link,$caption,$description,$icon,$time,$comments,$likes)
	{
		if($to) $to_section = '<div class="to" >to</div><div class="news-friend-name"><strong><a>' . $to . '</a></strong></div><div class="clear"></div>';
		else $to_section = '';
				
		if($message) $message_section = '<div class="message">' . $message . '</div>';
		else $message_section = '';
			
		if($picture) $picture_section = '<div class="external-image"><img src="' . $picture . '"/></div><div class="news-external">';
		else $picture_section = '<div class="news-external" style="width: 410px;">';
		
		if(!$link) $link='#';
		
		if($name) $name_section = '<div class="news-title"><h3><a href="' . $link . '" target="_blank">' . $name . '</a></h3></div>';
		else $name_section = '';
		
		if($caption) $caption_section = '<div class="news-caption"><i>' . $caption . '</i></div>';
		else $caption_section = '';
		
		if($description) $description_section = '<div class="news-desc">' . $description . '</div>';
		else $description_section = '';
		
		if($icon) $icon_section = '<div class="news-icon" ><img src="' . $icon . '" /></div>';
		else $icon_section = '';
		
		$time_converted = time_elapsed($time);
		
		$news = '<div class="news">
						<div class="news-friend-thumb"><img src="' . $profile_pic . '"/></div>
						<div class="news-content">
							<div class="news-friend-name"><strong><a>'. $from . '</a></strong></div>'.$to_section.							
							'<div class="clear"></div>' . $message_section . $picture_section . $name_section . $caption_section . $description_section .
							'</div>
							<div class="clear"></div>
							<div class="comment-like">' . $icon_section . $time_converted . ' ago  ·  ' . $comments . ' comments  ·  ' . $likes . ' likes</div>
						</div>
					</div>';
			return $news;
	}
?>

<div id="bar">
	<div class="top-area">
		<div class="logo" ><a href="index.php" ><img src="images/facebook-logo.jpg" /></a></div>
		<div class="side-box" >
			<div class="link"><a href="http://25labs.com/tutorial-integrate-facebook-connect-to-your-website-using-php-sdk-v-3-x-x-which-uses-graph-api/" " target="_blank" ><h5>Click here to read the tutorial on 25labs.com</h5></a></div>			
			<div class="logout" >
				<?php				
				if ($user) echo '<a href="logout.php" rel="nofollow"><h5>Logout</h5></a>';
				else echo '<a href="' . $loginUrl . '" rel="nofollow"><h5>Login</h5></a>';
				?>
			</div>
		</div>
	</div>
</div>

<?php if(!$user) { ?>
<div class="login-box">
<div id="f-connect-button"><a href="<?=$loginUrl?>"><img src="images/f-connect.png" alt="Connect to your Facebook Account"/></a><div>
This website will <b>NOT</b> post anything to your wall or like any page automatically.
</div>
<?php } else { ?>
<div class="big-container" >
		<div class="left-sidebar">		
			<div class="profile-pic"><img src="https://graph.facebook.com/<?php echo($user); ?>/picture?type=large"/></div>
			<div class="sidebar-title">
				<h3><a>Friends (<?php echo(count($friends_list['data'])); ?>)</a></h3>
			<?php
			$i=1;
			foreach($friends_list['data'] as $frnd)	{
					echo '<div class="friend-list" ><div class="friend-thumb"><img src="https://graph.facebook.com/'. $frnd['id'] .'/picture"/></div><div class="friend-name" ><strong><a>' . $frnd['name'] . '</a></strong></div></div>';
					if(++$i > 10) break;
			}
			?>
			</div>				
		</div>
	<div class="temp">
		<div class="container" >
			<div class="main" >
				<div class="user-info">
				<h1><?php echo($user_info['name']);?></h1>
				<div class="profile-info">
					<?php
				if(isset($user_info['gender'])) echo "<b>   GENDER : </b>" . $user_info['gender'];
				if(isset($user_info['birthday'])) echo "<b>   BIRTHDAY : </b>" . $user_info['birthday'];
				if(isset($user_info['education']))
					{
						echo "<b>   EDUCATION : </b>";
						foreach($user_info['education'] as $school)
							{
								echo $school['school']['name'];								
								break;									
							}
					}
				if(isset($user_info['hometown'])) echo "<b>   HOMETOWN : </b>" . $user_info['hometown']['name'];
					?>				
				</div>
				</div>
				<div class="like">
				
<div id="fb-root"></div>
<script>(function(d){
  var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
  js = d.createElement('script'); js.id = id; js.async = true;
  js.src = "//connect.facebook.net/en_US/all.js#appId=276860898991504&xfbml=1";
  d.getElementsByTagName('head')[0].appendChild(js);
}(document));</script>
<div class="fb-like" data-href="www.facebook.com/25labs" data-send="false" data-layout="box_count" data-width="55" data-show-faces="false"></div>

</div>
				<div class="clear"></div>
				<div class="status-update">
					<h3>Update Status</h3>					
					<form name="" action="<?=$site_url?>" method="post">
					<textarea id="status" name="status"></textarea>
					<?php if(isset($statusUpdate)) echo'<div class="status-success-message" ><h4 style="padding-left:20px;padding-top:4px;">Status Updated Successfully.</h4></div>'; ?>
					<input id="post-button" type="image" name="submit" src="images/post.jpg" />
					</form>
					<form name="pub" id="pub" action="<?=$site_url?>" method="post">
					<input type="hidden" name="pub" value="25 labs">					
					<input id="publish-button" type="image" name="publish" src="images/post-25-labs.jpg" />
					</form>
				</div>
				<div class="clear"></div>
				<div class="news-feed">
					<h3>News Feed</h3>				
					<?php
					foreach($feed['data'] as $news)
					{
					$pro_pic = 'https://graph.facebook.com/'.$news['from']['id'].'/picture';				
					$from = $news['from']['name'];
					$time = $news['created_time'];
				
					if(isset($news['to']['data']['0']['name'])) $to = $news['to']['data']['0']['name'];
					else $to = NULL;
					
					if(isset($news['message'])) $message = $news['message'];
					else $message = NULL;
				
					if(isset($news['picture'])) $picture = $news['picture'];
					else $picture = NULL;
				
					if(isset($news['name'])) $name = $news['name'];
					else $name = NULL;
				
					if(isset($news['link'])) $link = $news['link'];
					else $link = NULL;
				
					if(isset($news['caption'])) $caption = $news['caption'];
					else $caption = NULL;
				
					if(isset($news['description'])) $description = $news['description'];
					else $description = NULL;
				
					if(isset($news['icon'])) $icon = $news['icon'];
					else $icon = NULL;
				
					if(isset($news['comments']['count'])) $comment_count = $news['comments']['count'];
					else $comment_count = 0;
				
					if(isset($news['likes']['count'])) $likes_count = $news['likes']['count'];
					else $likes_count = 0;
				
					if(($news['type']=='status' && isset($news['message'])) || 
					($news['type']=='link' && isset($news['link']) && isset($news['name'])) || 
					($news['type']=='photo' && isset($news['link']) && isset($news['name'])) || 
					($news['type']=='video' && isset($news['link']) && isset($news['name']))){
						echo(newsitem($pro_pic,$from,$to,$message,$picture,$name,$link,$caption,$description,$icon,$time,$comment_count,$likes_count));
					}
			
					}
					?>
				</div>
			</div>			
		</div>		
		<div class="right-sidebar">
		<?php if(isset($photos['data']['0'])) { ?>
				<h3>Tagged Photos</h3>
				<div class="tagged-photos">
				<?php
				foreach($photos['data'] as $photo){
						echo '<div class="single-photo"><a href="' . $photo['source'] . '" ><img src="' . $photo['picture'] . '" /></a></div>';						
				}
				?>
				</div>
		<?php } ?>
<div class="sponsor" style="height:292px;">	
	<div id="fb-root"></div>
<script>(function(d){
  var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
  js = d.createElement('script'); js.id = id; js.async = true;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
  d.getElementsByTagName('head')[0].appendChild(js);
}(document));</script>
<div class="fb-like-box" data-href="http://www.facebook.com/25labs" data-width="250" data-show-faces="true" data-stream="false" data-header="true"></div>
	</div>
		</div>		
	</div>
	<div class="clear"></div>
	<div class="footer"> 25 labs 2011 · This page is only for demonstration purpose. If you feel that it violates any copyrighted stuff, please use <a href="http://25labs.com/contact-us/" >contact form</a>.</div>
</div>

<?php } ?>


</body>
</html>