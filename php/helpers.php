<?php

function getFullUrl($lang, $url, $main_url = ''){
  $url = trim($url,'/');
  $url = ($lang != DEFAULT_LANG) ? "$lang/$url" : $url; //For $url can be /ka/kompaniis-shesakheb
  $full_url = trim($main_url,'/') . '/' . trim($url,'/');
  return $full_url;
}
?>