<?php
//$url = rtrim("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]","/");
//if(strstr($url,"?")){
//  $url = strstr($url,"?",true);
//}
//
//$valid_url = array(
//  'default' => '',
//  'en' => '',
//  'ge' => ''
//);
//
//$correct_url = false;
//$lang = "";
//$secondlang = "";
//foreach($valid_url as $key=>$val){
//  if($url == $val){
//    $correct_url = true;
//    $lang = $key;
//    break;
//  }
//}
//

//$dbCredentials = array(
//  'host' => 'localhost',
//  'username' => '',
//  'password' => '',
//  'tableName' => 'ipligence'
//);

//if(!$correct_url || $lang == 'default'){
//  $conn = mysqli_connect($dbCredentials['host'], $dbCredentials['username'], $dbCredentials['password'], $dbCredentials['tableName']);
//  if (!$conn) {
//    die("Connection to language detection database failed: " . mysqli_connect_error() . "<br/>");
//  }
//  mysqli_set_charset($conn,"utf8");
//
//  $ip = "'" . $_SERVER['REMOTE_ADDR'] . "'";
//  $sql = "SELECT country_code
//          FROM ipligence_geo
//          WHERE ip_from <= INET_ATON($ip) and ip_to >= INET_ATON($ip)
//          LIMIT 1;";
//  $result = $conn->query($sql);
//  $user_connected_from_georgia = false;
//  if($result && $result->num_rows > 0){
//    $user_connected_from_georgia = true;
//  }
//
//  if(!$correct_url){
//    $uri = $_SERVER['REQUEST_URI'];
//    $uri_arr = explode('/',$uri);
//    if(intval($uri_arr[1]) > 0){
//      header("Location: " . $valid_url['default'] .$uri_arr[1], true, 301);
//      exit;
//    }
//    else{
//      if($user_connected_from_georgia){
//        header("Location: " . $valid_url['ge'], true, 301);
//      }
//      else{
//        header("Location: " . $valid_url['en'], true, 301);
//      }
//      exit;
//    }
//  }
//  else{
//    if($user_connected_from_georgia){
//      header("Location: " . $valid_url['ge'], true, 301);
//    }
//    else{
//      header("Location: " . $valid_url['en'], true, 301);
//    }
//    exit;
//  }
//}
//if($lang == "ge"){
//  $secondlang = "en";
//}
//else{
//  $secondlang = "ge";
//}
//$tags = array(
//  'title' => array(
//    'en' => "",
//    'ge' => ""
//  ),
//  'description' => array(
//    'en' => "",
//    'ge' => ""
//  ),
//  'url' => array(
//    'en' => "",
//    'ge' => ""
//  ),
//  'fb-title' => array(
//    'en' => "",
//    'ge' => ""
//  ),
//  'fb-description' => array(
//    'en' => "",
//    'ge' => ""
//  )
//);
//$texts = json_decode(file_get_contents('texts.json'),true);
//?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico?v=1"/>
  <!--  for Android-->
  <meta name="theme-color" content="#CDA34C">
  <link rel="icon" sizes="192x192" href="img/favicon192.png">

  <link rel="stylesheet" href="css/style.css">
  <script src="js/script.js"></script>
  
  <title>My Boilerplate Changed</title>

<!--  <meta name="description" content="--><?//= $texts['tags']['description'][$lang]?><!--" />-->
<!--  <meta property="og:type" content="website">-->
<!--  <meta property="fb:app_id" content="--><?//= $texts['tags']['app-id'][$lang]?><!--" />-->
<!--  <meta property="og:image" content="--><?//= $valid_url['default'] . $texts['tags']['image'][$lang]?><!--">-->
<!--  <meta property="og:url" content="--><?//= $valid_url['default'] . $texts['tags']['url'][$lang]?><!--">-->
<!--  <meta property="og:title" content="--><?//= $texts['tags']['fb-title'][$lang]?><!--">-->
<!--  <meta property="og:description" content="--><?//= $texts['tags']['fb-description'][$lang]?><!--">-->

</head>
<body>
  <header></header>
  <nav></nav>
  <section></section>
  <footer></footer>
</body>