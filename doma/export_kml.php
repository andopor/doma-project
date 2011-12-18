<?php
  include_once(dirname(__FILE__) ."/config.php");
  include_once(dirname(__FILE__) ."/include/definitions.php");
  
  $format = $_GET["format"];
  $id = $_GET["id"];
  
  $map = new Map();
  $map->Load($id);

  $data = $map->CreateKmlString(Helper::GlobalPath(MAP_IMAGE_PATH));

  header("Content-Type: application/vnd.google-earth.kml+xml");
  header('Content-Disposition: attachment; filename="map.kml";');
  
  print $data;
?>
