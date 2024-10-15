<?php
// Simple page redirect
function redirect($page)
{
  header('location: ' . URLROOT . '/' . $page);
}


function val_entry($input)
{
  $input = trim($input);
  $input = stripslashes($input);
  $input = htmlspecialchars($input);
  return $input;
}


function fn_resize($image_resource_id, $width, $height)
{

  $target_width = 392;
  $target_height = 270;
  $target_layer = imagecreatetruecolor($target_width, $target_height);
  imagecopyresampled($target_layer, $image_resource_id, 0, 0, 0, 0, $target_width, $target_height, $width, $height);
  return $target_layer;
}
