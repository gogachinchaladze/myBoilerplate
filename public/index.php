<?php

//TODO LATER move texts.json to db (mongo?!)
//TODO LATER admin panel to edit texts.json
//TODO LATER OOP version with classes and stuff

require_once('../php/country_detect.php');
require_once('../php/cfg.php');
require_once('../php/helpers.php');



$texts = json_decode(file_get_contents('../php/texts.json'),true);

$parsed_uri = parse_url($_SERVER['REQUEST_URI']);

$req_uri = $parsed_uri['path'];

$is_bot = preg_match('/bot|spider|crawl|curl|slurp|^$/i', strtolower($_SERVER['HTTP_USER_AGENT']));

$default_lang = DEFAULT_LANG;
$default_page = DEFAULT_PAGE_ID;
$language_cookie_name = 'preferred_lang';
$language_cookie_time = time() + (86400 * 14); //Two weeks
$page_params = getPageParamsByUri($req_uri); //detecting correct url

$website_url = "http://$_SERVER[HTTP_HOST]/";

$ip = '127.0.0.1';

if(isset($_SERVER["HTTP_CF_CONNECTING_IP"])){
  $ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
}
elseif(isset($_SERVER['REMOTE_ADDR'])){
  $ip = $_SERVER['REMOTE_ADDR'];
}
if (strpos($ip, ':') !== false) {
  $ip = '127.0.0.1';
}
//die($ip);
//$ip = "'95.104.69.164'"; //Debugging Georgia
//$ip = "'46.101.81.156'"; //Debugging Netherlands

if($page_params){ // That means that URL is correct
  $page = $page_params['page'];
  $lang = $page_params['lang'];
  $full_url = getFullUrl($lang, $texts['pages'][$page]['url'][$lang], $website_url);

  if($req_uri == '/' && !$is_bot){
    if(isset($_GET['lang']) && isset($texts['languages'][$_GET['lang']])){ //Checking if user is changing language manually.(with/?lang='ka')
      setcookie($language_cookie_name, $_GET['lang'], $language_cookie_time, "/");
      redirectTo($default_page, $_GET['lang']);
    }
    else{
      $pref_lang = getPreferredLanguageAndSetIfNotAlreadySet();
      if($pref_lang != $default_lang){ //Redirecting to /lang or staying at default language /
        redirectTo($default_page, $pref_lang);
      }
    }
  }
  else if($req_uri != '/'){ //Sets cookie of preferred language when user directly goes to
    setcookie($language_cookie_name, $lang, $language_cookie_time, "/");
  }
}
else{ //Redirect to 404 with correct language
  $page = $default_page;
  $lang = getPreferredLanguageAndSetIfNotAlreadySet();
  $full_url = getFullUrl($lang, $texts['pages'][$page]['url'][$lang], $website_url);

  pageNotFound();
  $lang = getPreferredLanguageAndSetIfNotAlreadySet();
  pageNotFound();
}

function getPageParamsByUri($req_url){
  global $texts;

  $req_url = trim($req_url,'/');

  foreach($texts['pages'] as $page_id =>$page){
    if($page_id == '404') continue;
    foreach($page['url'] as $lang=>$url){
      $url = getFullUrl($lang,$url);
      if($req_url == trim($url,'/')){
        return array('lang' => $lang,
                     'page' => $page_id);
      }
    }
  }
  return false;
}

function redirectTo($page, $lang=null, $redirect_code=307){
  global $texts;
  global $website_url;

  if(is_null($lang)) $lang = getPreferredLanguageAndSetIfNotAlreadySet();

  if(isset($texts['pages'][$page]) && isset($texts['pages'][$page]['url'][$lang])){
    $full_url = getFullUrl($lang, $texts['pages'][$page]['url'][$lang], $website_url);
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Cache-Control: no-cache");
    header("Location: $full_url", true, $redirect_code);
    exit();
  }
  else{
    redirectTo(DEFAULT_PAGE_ID, $lang);
  }
}

function pageNotFound($lang_of_404 = null){
  global $page;
  global $lang;
  global $texts;


  if(!is_null($lang_of_404) && isset($texts['languages'][$lang_of_404])) $lang = $lang_of_404;
  else $lang = (isset($lang)) ? $lang : DEFAULT_LANG;

  $page = '404';

  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
  header("Cache-Control: no-cache");
  header("HTTP/1.0 404 Not Found");

}

function getPreferredLanguageAndSetIfNotAlreadySet(){
  global $language_cookie_name;
  global $texts;
  global $language_cookie_time;
  global $ip;

  // if session is set return it
  if(isset($_COOKIE[$language_cookie_name]) && isset($texts['languages'][$_COOKIE[$language_cookie_name]])){
    return $_COOKIE[$language_cookie_name];
  }
  else{
    $country_code = (isset($_SERVER["HTTP_CF_IPCOUNTRY"])) ? $_SERVER["HTTP_CF_IPCOUNTRY"] : getCountryCodeByIP($ip); //Checking cloudflare if geolocation is present
    $country_code = strtolower($country_code);
    if($country_code == 'ge'){
      $pref_lang = 'ka';
    }
    else if(isset($texts['languages']['ru']) && ($country_code == 'am' || $country_code == 'az' || $country_code == 'ru')){
      $pref_lang = 'ru';
    }
    else if(isset($texts['languages']['en'])){
      $pref_lang = 'en';
    }
    else{
      $pref_lang = DEFAULT_LANG;
    }
    setcookie($language_cookie_name, $pref_lang, $language_cookie_time, "/");

    return $pref_lang;
//    if(isUserFromGeorgia()){
//      return 'ka';
//    }
//    else{
//      return 'en';
//    }
  }
}

//$second_lang = ($lang == "ka") ? "en" : "ka";

//Mobile Detection
require_once('../php/mobile_detect.php');
$detect = new Mobile_Detect;
$is_mobile = $detect->isMobile();
$is_tablet = $detect->isTablet();

//Just a random number
$random_number_for_disabling_cache = time();
$page_class= (isset($texts['pages'][$page]['className'])) ? $texts['pages'][$page]['className'] : $page;

?>

<!DOCTYPE html>
<html class = "<?=$lang?> <?= $page_class?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico?v=1"/>

  <!--  for Android-->
  <meta name="theme-color" content="#FFFFFF">
  <link rel="icon" sizes="192x192" href="/img/favicon192.png">

  <link rel="stylesheet" href="/css/style.css">
<!--  <link rel="stylesheet" href="css/style.css?v=--><?//=$random_number_for_disabling_cache?><!--">-->

  <title><?=$texts['pages'][$page]['tags']['title'][$lang]?></title>

  <meta name="description" content="<?= $texts['pages'][$page]['tags']['description'][$lang]?>" />
  <meta property="fb:app_id" content="<?= $texts['contact']['social']['fbAppId']?>" />
  <meta property="og:type" content="website">
  <meta property="og:image" content="<?= getFullUrl($lang,$texts['pages'][$page]['tags']['fbImage'], $website_url)?>">
  <meta property="og:url" content="<?= $full_url?>">
  <meta property="og:title" content="<?= $texts['pages'][$page]['tags']['fbTitle'][$lang]?>">
  <meta property="og:description" content="<?= $texts['pages'][$page]['tags']['fbDescription'][$lang]?>">

  <?php
  foreach($texts['pages'][$page]['url'] as $curr_lang => $url){
    if($curr_lang != $lang){
      $full_url = getFullUrl($curr_lang,$url,$website_url);
      $full_url = (!$is_bot && $curr_lang==$default_lang && $full_url=="/" && $lang != $default_lang) ? $full_url."?lang=$curr_lang" : $full_url;
      $hreflang = $texts['languages'][$curr_lang]['hrefLang'];
      echo "<link rel=\"alternate\" href=\"$full_url\" hreflang=\"$hreflang\" /> ";
    }
  }
  ?>

</head>
<body>

  <div id="svg-container">
    <?php //include 'img/svg-icons.svg'; ?>
  </div>
  <header>
    <a href="<?=getFullUrl($lang,$texts['pages']['landing'][$lang]['url'])?>">
      LOGO
      <!--svg viewBox="0 0 141 35" class="main-logo">
        <use xlink:href="#logo"></use>
      </svg-->
    </a>
  </header>
  <nav>
    <ul id="language-switcher-container">
      <?php
        foreach($texts['pages'][$page]['url'] as $curr_lang => $url){
          //CHECK IF LANGUAGE IS SAME AS NOW if($curr_lang != $lang){
          $lang_title = $texts['languages'][$curr_lang]['title'];
          $abbr = $texts['languages'][$curr_lang]['abbr'];
          $full_url = getFullUrl($curr_lang,$url);
          $full_url = (!$is_bot && trim($full_url,'/')=="" && $lang != $default_lang) ? $full_url."?lang=$curr_lang" : $full_url;
          $active = ($curr_lang == $lang) ? "active" : "";

          echo "<li><a href='$full_url' hreflang='$curr_lang' class='$active'>
                 <abbr lang='$curr_lang' title='$lang_title'>$abbr</abbr>
                </a></li>";
        }
      ?>
    </ul>
  </nav>
  <section><?php include_once "../templates/$page.php"?></section>
  <footer>footer</footer>

  <script>
    <?= "var isMobile=" . (($is_mobile) ? 'true' : 'false')?>;
    <?= "var isTablet=" . (($is_tablet) ? 'true' : 'false')?>;
  </script>
<!--  <script src="js/script.js?v=--><?//=$random_number_for_disabling_cache?><!--"></script>-->
  <script src="/js/lib.js"></script>
  <script src="/js/script.js"></script>
  <script src="//localhost:35729/livereload.js"></script>
</body>