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