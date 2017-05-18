<?php
//TODO BEAUTIFY STRING
//TODO PHP CRON RUN ONCE A DAY

require_once '../php/cfg.php';
require_once '../php/helpers.php';

$texts = json_decode(file_get_contents("../php/texts.json"), true);
$sitemap = fopen("sitemap.xml", "w") or die("Unable to open file!");
$website_url = MAIN_URL;
$forbidden_pages = ['404'];

if(isset($_SERVER['HTTP_HOST'])){
  $website_url = "http://$_SERVER[HTTP_HOST]/";
}
$sitemap_header = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";

fwrite($sitemap, $sitemap_header);
$sitemap_body = "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:xhtml=\"http://www.w3.org/1999/xhtml\">";

foreach($texts['pages'] as $page_id=> $page_arr){
  for($i=0,$length=count($forbidden_pages);$i<$length;$i++){
    if($page_id == $forbidden_pages[$i]){
      continue 2;
    }
  }
  foreach($page_arr['url'] as $lang=>$url){
    $url_xml = "<url>";

    $full_url = getFullUrl($lang,$url,$website_url);
    $url_xml .= "<loc>$full_url</loc>";
    foreach($page_arr['url'] as $other_lang=>$alt_url){
      if($other_lang == $lang){
        continue;
      }
      $hreflang = $texts['languages'][$other_lang]['hrefLang'];
      $full_alt_url = getFullUrl($other_lang,$alt_url,$website_url);
      $url_xml .= "<xhtml:link rel=\"alternate\" hreflang=\"$hreflang\" href=\"$full_alt_url\" />";
    }
    $url_xml .= "<changefreq>daily</changefreq>";
    $url_xml .= '</url>';
    $sitemap_body .= $url_xml;
  }
}
$sitemap_body .= '</urlset>';
fwrite($sitemap, $sitemap_body);
fclose($sitemap);
echo $sitemap_body;
?>