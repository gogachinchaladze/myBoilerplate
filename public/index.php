<?php

require_once('../php/country_detect.php');
require_once('../php/cfg.php');
require_once('../php/helpers.php');

$texts = json_decode(file_get_contents('../php/texts.json'),true);

$parsed_uri = parse_url($_SERVER['REQUEST_URI']);

$req_uri = $parsed_uri['path'];

$is_bot = preg_match('/bot|spider|crawl|curl|slurp|facebookexternalhit|facebot^$/i', strtolower($_SERVER['HTTP_USER_AGENT']));

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
//    else if(isset($texts['languages']['ru']) && ($country_code == 'am' || $country_code == 'az' || $country_code == 'ru')){
//      $pref_lang = 'ru';
//    }
    else if(isset($texts['languages']['en'])){
      $pref_lang = 'en';
    }
    else{
      $pref_lang = DEFAULT_LANG;
    }
    setcookie($language_cookie_name, $pref_lang, $language_cookie_time, "/");

    return $pref_lang;
  }
}

//Mobile Detection
require_once('../php/mobile_detect.php');
$detect = new Mobile_Detect;
$is_mobile = $detect->isMobile();
$is_tablet = $detect->isTablet();

//Just a random number
$version_number = 1;
$page_class= (isset($texts['pages'][$page]['className'])) ? $texts['pages'][$page]['className'] : $page;

?>

<?php
  @include('../resources/views/layout.php');
?>
