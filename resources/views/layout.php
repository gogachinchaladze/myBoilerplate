<!DOCTYPE html>
<html class = "<?=$lang?> <?= $page_class?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="shortcut icon" type="image/x-icon" href="/img/favicon.ico?v=<?=$version_number?>"/>

  <!--  for Android-->
  <meta name="theme-color" content="#FFFFFF">
  <link rel="icon" sizes="192x192" href="/img/favicon192.png?v=<?=$version_number?>">

  <link rel="stylesheet" href="css/style.css?v=<?=$version_number?>">

  <title><?=$texts['pages'][$page]['tags']['title'][$lang]?></title>

  <meta name="description" content="<?= $texts['pages'][$page]['tags']['description'][$lang]?>" />

  <meta name="twitter:card" content="summary_large_image" />
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
    <?php echo file_get_contents('/img/svg-icons.svg');?>
  </div>
  <header>
    <?php @include('../resources/views/components/header.php')?>
  </header>
  <nav>
    <?php @include('../resources/views/components/nav.php')?>
  </nav>
  <main>
    <?php @include("../resources/views/pages/landing.php")?>
  </main>
  <footer>
    <?php @include('../resources/views/components/footer.php')?>
  </footer>
  <script>
    var isMobile = <?=$is_mobile ? 'true' : 'false'?>;
    var isTablet = <?=$is_tablet ? 'true' : 'false'?>;
  </script>
  <script src="/js/lib.min.js?v=<?=$version_number?>"></script>
  <script src="/js/script.min.js?v=<?=$version_number?>"></script>
</body>