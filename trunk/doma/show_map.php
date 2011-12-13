<?php
  include_once(dirname(__FILE__) ."/show_map.controller.php");
  include_once("./include/quickroute_jpeg_extension_data.php");
  
  $controller = new ShowMapController();
  $vd = $controller->Execute();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title><?php print __("PAGE_TITLE")?> :: <?php print strip_tags($vd["Name"])?></title>
  <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
  <link rel="stylesheet" href="style.css" type="text/css" />
  <link rel="icon" type="image/png" href="gfx/favicon.png" />
  <link rel="alternate" type="application/rss+xml" title="RSS" href="rss.php?<?php print Helper::CreateQuerystring(getUser())?>" />
  <script type="text/javascript" src="js/jquery/jquery-1.7.1.min.js"></script>  
  <script type="text/javascript" src="js/show_map.js.php?<?php print Helper::CreateQuerystring(getUser())?>"></script>
  <script type="text/javascript" src="js/jquery/jquery.livequery.js"></script>
  <script type="text/javascript" src="js/jquery/jquery.elastic.js" charset="utf-8"></script>
  <script type="text/javascript" src="js/jquery/jquery.watermarkinput.js"></script>
  <script type="text/javascript" src="js/jquery/jquery.timeago.js"></script>
  <?php 
  $lang = Session::GetLanguageFileShort();
  $langcode = substr($lang,0,strlen($lang)-4);
  if(($langcode != "")&&($langcode != "en"))
    {
      ?>
      <script type="text/javascript" src="js/jquery/jquery.timeago.<?php print $langcode ?>.js"></script>
      <?php
    }
  ?>

</head>
<body id="showMapBody">
<center>
<div id="top_menu">
<div id="wrapper" <?php if($vd["Map"]->IsGeocoded) {print 'style="float:left"';} else {print 'style="float:center"';}?>>
<?php Helper::CreateTopbar() ?>
<div id="content">
<form id="frm" method="post" action="<?php print $PHP_SELF?>">

<div id="name"><?php print $vd["Name"]?></div>

<div id="zoomButtonDiv">
  <div id="zoomIn" class="zoomButton"></div>
  <div id="zoomOut" class="zoomButton"></div>
</div>

<div id="previousAndNext">
  <?php if($vd["Previous"]) { ?><?php print __("PREVIOUS")?>: <a href="show_map.php?<?php print Helper::CreateQuerystring(getUser(), $vd["Previous"]->ID)?>"><?php print $vd["PreviousName"]?></a><?php } ?>
  <br />
  <?php if($vd["Next"]) { ?><?php print __("NEXT")?>: <a href="show_map.php?<?php print Helper::CreateQuerystring(getUser(), $vd["Next"]->ID)?>"><?php print $vd["NextName"]?></a><?php } ?>
  <br />
  <a href="<?php print $vd["BackUrl"]?>"><?php print __("BACK")?></a>
	<?php if($vd["SecondMapImageName"]) {?>
	<br />
	<a href="javascript:ToggleImage();" title="<?php print __("TOGGLE_IMAGE_TOOLTIP")?>"><?php print __("TOGGLE_IMAGE")?></a>
	<?php }?>
</div>

<div id="propertyContainer">
<?php
  $map = $vd["Map"];
  print '<div class="property">'. __("CATEGORY") .": ". $map->GetCategory()->Name .'</div>'; 
  if(__("SHOW_MAP_AREA_NAME") && $map->MapName != "") print '<div class="property">'. __("MAP_AREA_NAME") .': '. $map->MapName .'</div>';
  if(__("SHOW_ORGANISER") && $map->Organiser != "") print '<div class="property">'. __("ORGANISER") .': '. $map->Organiser .'</div>';
  if(__("SHOW_COUNTRY") && $map->Country != "") print '<div class="property">'. __("COUNTRY") .': '. $map->Country .'</div>';
  if(__("SHOW_DISCIPLINE") && $map->Discipline != "") print '<div class="property">'. __("DISCIPLINE") .': '. $map->Discipline .'</div>';
  if(__("SHOW_RELAY_LEG") && $map->RelayLeg != "") print '<div class="property">'. __("RELAY_LEG") .': '. $map->RelayLeg .'</div>';
  if(__("SHOW_RESULT_LIST_URL") && $map->ResultListUrl != "") print '<div class="property"><a href="'. hsc($map->CreateResultListUrl()) .'" target="_blank">'. __("RESULTS") .'</a></div>';

?>
</div>

<?php
$QR = $map->GetQuickRouteJpegExtensionData();

if($QR->IsValid)
{
	$f = $QR->Sessions["0"]->Route->Segments["0"]->Waypoints;
	$c1 = 0;
	$c2 = 0;
	$max1 = 0;
	$val = count($f);
	for ($i = 0; $i < $val; $i++) {
		$c1 += $QR->Sessions["0"]->Route->Segments["0"]->Waypoints[$i]->HeartRate;
		$c2 += 1;
		if ($QR->Sessions["0"]->Route->Segments["0"]->Waypoints[$i]->HeartRate > $max1) 
		{
			$max1 = $QR->Sessions["0"]->Route->Segments["0"]->Waypoints[$i]->HeartRate;
		}
	}
	if((__("SHOW_DISTANCE"))||(__("SHOW_ELAPSEDTIME"))) 
	{
		print '<div id="propertyContainer">';
		if(__("SHOW_DISTANCE") && $map->Distance != "") print '<div class="property">'. __("DISTANCE") .': '. round(($map->Distance)/1000,2) .' km</div>';
		if(__("SHOW_ELAPSEDTIME") && $map->ElapsedTime != "") print '<div class="property">'. __("ELAPSEDTIME") .': '. Helper::ConvertToTime($map->ElapsedTime,"MM:SS").'</div>';
		print '</div>';
	}
	if (($c1 != 0)&&((__("SHOW_MAXHR"))||(__("SHOW_AVGHR")))) {
		print '<div id="propertyContainer">';
		if(__("SHOW_AVGHR")) print '<div class="property">'. __("AVGHR") .': '. round($c1/$c2,0).'</div>';
		if(__("SHOW_MAXHR")) print '<div class="property">'. __("MAXHR") .': '. round($max1,0).'</div>';
		print '</div>';
	}
}
?>
<?php
  if(__("SHOW_COMMENT") && $map->Comment != "") print '<div id="comment">'. nl2br($map->Comment) .'</div>';

  if(__("SHOW_VISITOR_COMMENTS"))
  {
?>
<div class="clear"></div>
<?php print __("COMMENTS") . ": " ?> <span id="comments_count"></span> ( <a href="javascript: void(0)" class="showCommentPosted"><?php print __("SHOW") ?></a> / <a href="javascript: void(0)" class="hideCommentPosted"><?php print __("HIDE") ?></a> ) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript: void(0)" class="showCommentBox"><?php print __("POST_COMMENTS") ?></a>

<div id="CommentPosted" style="display:none">
  <?php if(count($vd["Comments"])>0) 
    { 
    foreach($vd["Comments"] as $comment)
      //print_r($comment);
      {
      ?>
      <div class="commentPanel" id="commentPanel-<?php print $comment->ID ?>" align="left" >
        <label class="postedComments">
        <span class="commentName">
          <?php if($comment->Email != "") 
            {
            ?><a href="mailto:<?php print $comment->Email ?>"><?php print $comment->Name ?></a>
            <?php
            } else {
            print $comment->Name;
            }
            ?>
        </span>
        <span>
          <?php print Helper::ClickableLink($comment->Comment) ?> 
        </span>
        </label>
        <br clear="all" />
        <label class="postedTime">
        <abbr class="timeago" title="<?php print $comment->DateCreated?>"></abbr>
        </label>
        <?php
        $userip = $_SERVER['REMOTE_ADDR'];
        if(($comment->UserIP == $userip)||($map->UserID == Helper::GetLoggedInUser()->ID))
          {?>
          &nbsp;&nbsp;<a href="#" id="CID-<?php  echo $comment->ID;?>" class="c_delete"><?php print __("DELETE") ?></a>
          <?php
          }?>
      </div>

    <?php
      }
    } 
    ?>
</div>
  
<div class="commentBox" id="commentBox" <?php echo (($comment_num_row) ? '' :'style="display:none"')?>>
    <input type="hidden" id="map_id" value="<?php print $map->ID ?>"><input type="hidden" id="map_user" value="<?php print getUser()->Username ?>">
    <?php print __("NAME") ?>: <input type="text" id="user_name" <?php if(Helper::IsLoggedInUser()) print "value='".Helper::GetLoggedInUser()->FirstName. " " .Helper::GetLoggedInUser()->LastName ."'" ?> /> <?php print __("EMAIL") ?>: <input type="text" id="user_email" <?php if(Helper::IsLoggedInUser()) print "value='".Helper::GetLoggedInUser()->Email."'" ?> /><br/>
    <label id="record">
      <textarea class="commentMark" id="commentMark" name="commentMark" cols="59" rows="3"></textarea>
    </label>
    <br clear="all" />
     <a id="SubmitComment" class="small button comment"> <?php print __("SAVE") ?></a>
</div>

<?php 
}
?>



<div class="clear"></div>

</form>
</div>
</div>
<?php
if($map->IsGeocoded)
{
	print '<div id="gmap">';
	?>
	<script type="text/javascript">
	var divh = document.getElementById('wrapper').offsetHeight;
	document.write('<a href="http://maps.google.com/maps?q=<?php print($map->MapCenterLatitude)?>,<?php print($map->MapCenterLongitude)?>" target="_blank"><img src="http://maps.google.com/staticmap?center=<?php print($map->MapCenterLatitude)?>,<?php print($map->MapCenterLongitude)?>&amp;zoom=6&amp;size=170x'+divh+'&amp;maptype=terrain&amp;markers=<?php print($map->MapCenterLatitude)?>,<?php print($map->MapCenterLongitude)?>,red&amp;key=<?php print GOOGLE_MAPS_API_KEY; ?>&amp;sensor=false"></a>');
	</script>
	<?php
	print '</div>';
}
?>
</div>
<div class="clear">&nbsp;</div>

<div>
  <img id="mapImage" src="<?php print $vd["FirstMapImageName"]; ?>" alt="<?php print hsc(strip_tags($vd["Name"]))?>" <?php if($vd["SecondMapImageName"]) {?>title="<?php print __("TOGGLE_IMAGE_CLICK")?>"<?php }?>/>
  <?php if($vd["SecondMapImageName"]) { ?>
  <img id="hiddenMapImage" src="<?php print $vd["SecondMapImageName"]; ?>" alt="<?php print hsc(strip_tags($vd["Name"]))?>" <?php if($vd["SecondMapImageName"]) {?>title="<?php print __("TOGGLE_IMAGE_CLICK")?>"<?php }?>/>
  <?php } ?>
  <input type="hidden" id="imageWidth" value="<?php print $vd["ImageWidth"] ?>" />
  <input type="hidden" id="imageHeight" value="<?php print $vd["ImageHeight"] ?>" />
</div>
</center>
</body>
</html>